<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request;
use App\View;
use App\Exception\ConfigurationException;
use App\Exception\NotFoundException;
use App\Exception\StorageException;
use App\Model\NoteModel;

abstract class AbstractController //tutatj mamy obsługę kontolerów i żądań od uzytkownika
{
  protected const DEFAULT_ACTION = 'list'; //jest protected to klasy potomne maja dostep do prywatnych wlasciwosci
  
  private static array $configuration = []; //inicjalizacja statycznej tablicy (do której przypiszemy dane z konfiguracją bazy danych) 
  //tu nie zmieniam na protected bo uzywam tylko w tej klasie. (Nie ma dostępu z klas potomnych)

  protected NoteModel $noteModel; //inicjalizacja nowego okietu klasy NoteModel (Widoczne dla klas potomnych) 
  protected Request $request; //inicjalizacja nowego okietu klasy Request (Widoczne dla klas potomnych) 
  protected View $view; //inicjalizacja nowego okietu klasy View (Widoczne dla klas potomnych) 

  public static function initConfiguration(array $configuration): void //metoda do krótej przekazane są dane z konfiguracją bazy danych.
  // Nie przekazuję ich bezpośrednio do konstuktora bo to metoda statyczna. Wykonywana jest tylko raz. Metoda styczna powiazana jest z klasa a nie obiektem.
  {
    self::$configuration = $configuration; //przypisanie zmiennej otrzymanej $configuration do zainicjalizowanej wyżej statycznej tablicy $configuration
  }

  public function __construct(Request $request) //obiekt $request to dane z żądania GET, POST, SERVER. Przekazana z pliku index.php
  {
    if (empty(self::$configuration['db'])) {  //jeśli zmienna (tablica) z konfiguracją bazy danych jest pusta lub jeśli nazwą nie jest 'db' to wyrzuca wyjątek.
      throw new ConfigurationException('Configuration error');
    }
    $this->noteModel = new NoteModel(self::$configuration['db']); 
    //przypisanie zainicjalizowanego wyżej obiektu $noteModel do obiektu $noteModel w konstruktorze(metodzie __contruct).
    //przekazanie tablicy $configuration z danymi konfiguracyjnymi bazy danych do obiektu noteModel. | '::' bo zmienna(tutaj tablica) jest statyczna.
    //(kontruktor NoteModel jest w AbstractModel).
    //Obiekt $noteModel Musi być zainicjalizowany wyżej żeby było go widać spoza metody i w klasie potomnej.

    $this->request = $request; //przypisanie otrzymanego w konstruktorze obiektu $request do obiektu $request(klasy Request) który znajduje się w klasie AbstractController.
    $this->view = new View(); //przypisanie zainicjalizowanego wyżej obiektu $view(klasy View) do obiektu $view w konstruktorze(metodzie __contruct).
    //Obiekt $view Musi być zainicjalizowany wyżej żeby było go widać spoza metody i w klasie potomnej.
  }

  final public function run(): void
  {
    try {
      $action = $this->action() . 'Action'; 
      //inicjalizacja zmiennej $action. Wywołanie metody action z tej klasy + konkatenacja 'Action'.
      //Wynikiem jest string np. 'createAction'.

      if (!method_exists($this, $action)) {  
        //Sprawdza czy nie istnieje (bo !) metoda o nazwie jak zmienna $action w tej klasie(lub klasie potomnej).
        //Jeśli istnieje to false, a jeśli nie istnieje to true
        $action = self::DEFAULT_ACTION . 'Action'; 
        //jeśli nie istenije przekazana nazwa metody to do zmiennej $action przypisuje wartość domyślną 'list' + string 'Action' ('listAction')
      }

      $this->$action(); //Wywołanie wybranej metody(metody znajdują się w NoteControlerze)
    } catch (StorageException $e) {
      // Log::error($e->getPrevios());
      $this->view->render('error', ['message' => $e->getMessage()]);
    } catch (NotFoundException $e) {
      $this->redirect('/', ['error' => 'noteNotFound']);
    }
  }

  final protected function redirect(string $to, array $params): void
  {
    $location = $to;

    if (count($params)) {
      $queryParams = [];
      foreach ($params as $key => $value) {
        $queryParams[] = urlencode($key) . '=' . urlencode($value);
      }
      $queryParams = implode('&', $queryParams);
      $location .= '?' . $queryParams;
    }

    header("Location: $location");
    exit;
  }

   private function action(): string
  {
    return $this->request->getParam('action', self::DEFAULT_ACTION); 
    // dla obiektu $request(klasy Request)->wywoluje metodę getParam i przekazuje do niej stringa 'action' i 'list'(DEFAUT_ACTION)
    //'list' zawsze będzie przekazane bo to stała
    //metoda action() zwraca stringa
  }

}
