<?php

declare(strict_types=1); 

spl_autoload_register(function (string $classNamespace) {
  $path = str_replace(['\\', 'App/'], ['/', ''], $classNamespace); //znak specjalny \(backslash) musimy poprzedzic eskejowaniem czyli dodac kolejny backslash
  $path = "src/$path.php";
  require_once($path);
});

require_once("src/Utils/debug.php");
$configuration = require_once("config/config.php"); //przypisuję do zmiennej $configuration tablicę z pliku config.php(plik zwraca tablicę)

use App\Controller\AbstractController;
use App\Controller\NoteController;
use App\Request;
use App\Exception\AppException;
use App\Exception\ConfigurationException;

$request = new Request($_GET, $_POST, $_SERVER); //tworze nowy obiekt klasy request i przypisuje go do zmiennej $request. 
//Pobiera i przekazuje dane do konstruktora z żądania GET, POST i SERVER(tablice)

try {
  AbstractController::initConfiguration($configuration); //wywoluje metodę initConfiguration (z klasy AbstractController) 
  //i przekazuję zmienną(w tym pzypadku tablica) z danymi konfiguracyjnymi bazy danych |wywowuje metode statyczną '::'
  (new NoteController($request))->run(); //tworze nowy obiekt klasy NoteController, 
  //przekazuję do konstuktora obiekt $request (konstruktor jest w AbstractController bo to rodzic NoteController) 
  //i wywołuję metodę run(). Metoda run jest dziedziczona z AbstractController

//Przechwytywanie wyjątków
} catch (ConfigurationException $e) {
  //mail('xxx@xxx.com', 'Errro', $e->getMessage());
  echo '<h1>Wystąpił błąd w aplikacji (ConnfigurationException)</h1>';
  echo 'Problem z applikacją, proszę spróbować za chwilę.';
  echo '<h3>' .  $e->getMessage() . "|  " .$e->getFile() . " " . $e->getLine() .'</h3>';
  //echo '<h3>' .  $e->getTraceAsString();
  //echo '<h3>' .  $e->__toString();
} catch (AppException $e) {
  echo '<h1>Wystąpił błąd w aplikacji (AppException)</h1>';
  echo '<h3>' .  $e->getMessage() . "|  " .$e->getFile() . " " . $e->getLine() .'</h3>';
  //echo '<h3>' .  $e->getTrace();
} catch (\Throwable $e) {
  echo '<h1>Wystąpił błąd w aplikacji (Throwable)</h1>';
  echo '<h3>' .  $e->getMessage() . "|  " .$e->getFile() . " " . $e->getLine() .'</h3>';
  //echo '<h3>' .  $e->getTrace();
  dump($e);
}
