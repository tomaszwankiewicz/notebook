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
  
  private static array $configuration = []; //inicjalizacja statycznej tablicy (do której przypiszemy dane z konfiguracją bazy danych) //tu nie zmieniamy na protected bo uzywamy tylko w klasie rodzica

  protected NoteModel $noteModel; //inicjalizacja nowego okietu klasy NoteModel (Widoczne dla klas potomnych) 
  protected Request $request;
  protected View $view;

  public static function initConfiguration(array $configuration): void //metoda do krótej przekazane są dane z konfiguracją bazy danych. Dlaczego nie przekazujemy ich bezpośrednio do konstuktora?
  {
    self::$configuration = $configuration;
  }

  public function __construct(Request $request) //$request-dane z żądania GET, POST, SERVER. Przekazana z pliku index.php
  {
    if (empty(self::$configuration['db'])) {  //jeśli tablica z konfiguracją bazy danych jest pusta lub jeśli nazwą nie jest 'db' to wyrzuca wyjątek.
      throw new ConfigurationException('Configuration error');
    }
    $this->noteModel = new NoteModel(self::$configuration['db']); //przekazanie tablicy z danymi konfiguracyjnymi bazy danych do obiektu noteModel(kontruktor jest w AbstractModel)
    //czy potrzeba to wyżej inicjalizować skoro tutaj jest zainicjalizowane? 

    $this->request = $request;
    $this->view = new View();
  }

  final public function run(): void
  {
    try {
      $action = $this->action() . 'Action';
      if (!method_exists($this, $action)) {
        $action = self::DEFAULT_ACTION . 'Action';
      }
      $this->$action();
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
  }

}
