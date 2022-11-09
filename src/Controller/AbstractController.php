<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request;
use App\View;
use App\Database;
use App\Exception\ConfigurationException;

abstract class AbstractController  //tutatj mamy obsługę kontolerów i żądań od uzytkownika
{
    protected const DEFAULT_ACTION = 'list';    //klasy potomne nie maja dostepu do prywatnych wlasciwosci dlatego zmieniamy na protected

    private static array $configuration = []; //inicjalizacja statycznej tablicy (do której przypiszemy dane z konfiguracją bazy danych) //tu nie zmieniamy na protected bo uzywamy tylko w klasie rodzica
  
    protected Database $database; //inicjalizacja nowego obiektu klasy Database
    protected Request $request; //inicjalizuję(tworzę) nowy obiekt $request klasy Request
    protected View $view;
  
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

    final public function run(): void
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
  
    final protected function redirect(string $to, array $params): void
    {
      $location = $to;

      if (count($params)) {
        $queryParams = [];
        foreach ($params as $key => $value) {
          $queryParams[] = urlencode($key) . '=' . urlencode($value);
        } 
        $queryParams = implode('&', $queryParams);     //metoda która łączy wszystkie parametry w tablic
        $location .= '?' . $queryParams;
      }
      
      header("Location: $location");
      exit;
    }


    private function action(): string
    {
      return $this->request->getParam('action', self::DEFAULT_ACTION); //jeśli nie znajdzie parametru 'action' zwraca wartość domyślną
    }
}