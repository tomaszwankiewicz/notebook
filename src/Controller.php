<?php

declare(strict_types=1);

namespace App;

require_once("src/Exception/ConfigurationException.php");
require_once("src/Database.php");
require_once("src/View.php");

use App\Request;
use App\Exception\ConfigurationException;
use App\Exception\NotFoundException;

class Controller
{
  private const DEFAULT_ACTION = 'list';

  private static array $configuration = []; //inicjalizacja statycznej tablicy (do której przypiszemy dane z konfiguracją bazy danych)

  private Database $database; //inicjalizacja nowego obiektu klasy Database
  private Request $request; //inicjalizuję(tworzę) nowy obiekt $request klasy Request
  private View $view;

  public static function initConfiguration(array $configuration): void //metoda która pobiera tablicę z konfguracją bazy danych
  {
    self::$configuration = $configuration;
  }

  public function __construct(Request $request) //konstruktor pobiera obiekt $request klasy Request
  {
    if(empty(self::$configuration['db'])) //jeśli tablica z konfiguracją bazy danych jest pusta (lub nie zgadza się nazwa 'db' lub któryś z parametrów to wyrzuca wyjątek
    {
      throw new ConfigurationException('Configuration error');
    }

    $this->database = new Database(self::$configuration['db']);
    
    $this->request = $request;//przypisuję zmieną otrzymaną z kontruktora do zmiennej $request 
    $this->view = new View(); //tworzę nowy obiekt klasy View i przypisuję go do zmiennej $view
  }

  public function createAction()
  {
    if ($this->request->hasPost()) {
      $noteData = [
        'title' => $this->request->postParam('title'),
        'description' => $this->request->postParam('description'),
      ];
      $this->database->createNote($noteData);
      header('Location: /?before=created');
      exit;
    }

    $this->view->render('create');    //Jeśli nie ma zmiennej $params(po przecinku) to przekazujemy pustą tablicę
  }

  public function showAction()
  {
    $noteId = (int) $this->request->getParam('id'); 

    if (!$noteId) {
      header('Location: /?error=missingNotId');
      exit;
    }

    try {
      $note = $this->database->getNote($noteId);
    } catch (NotFoundException $e){
      header('Location: /?error=noteNotFound');
      exit;
    }

    $this->view->render(
      'show',
      ['note' => $note]
    ); 
  }

  public function listAction()
  {
    $this->view->render(
      'list', 
      [
        'notes' => $this->database->getNotes(),
        'before' => $this->request->getParam('before'),
        'error' => $this->request->getParam('error')
      ]
    ); 
  }


  public function run(): void
  {

    $action = $this->action() . 'Action';         //php jako zmienną $action zapisuje otrzymaną z żądania akcje(typ string create/show/list) i dodaje końcówkę 'Action'
    if (!method_exists($this, $action)){          //spawdza czy metoda istnieje w obiekcie | $this wskazuje na obiekt którym operujemy a $action to nazwa metpdy | sprawdz https://www.php.net/manual/en/function.method-exists.php 
      $action = self::DEFAULT_ACTION . 'Action';                    
    }
    $this->$action();                              //php zamienia zmienną w postaci string na metodę(poprzez dodanie nawiasów)
    //Jeśli akcja nie istnieje to zostanie przekierowane do akcji domyslnej - czyli listy notatek


    //to jest to samo co wyżej

    // switch ($this->action()) {
    //   case 'create':
    //     $this->create();
    //     break;

    //   case 'show':
    //     $this->show();
    //     break;

    //   default:
    //     $this->list();
    //     break;
  }

  private function action(): string
  {
    return $this->request->getParam('action', self::DEFAULT_ACTION); //jeśli nie znajdzie parametru 'action' zwraca wartość domyślną
  }
}
