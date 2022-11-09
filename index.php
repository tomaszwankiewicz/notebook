<?php

declare(strict_types=1);

spl_autoload_register(function(string $classNamespace)
{
  $path = str_replace(['\\', 'App/'],['/',''], $classNamespace); //znak specjalny \(backslash) musimy poprzedzic eskejowaniem czyli dodac kolejny backslash
  $path ="src/$path.php";
  require_once($path);
});

require_once("src/Utils/debug.php");
$configuration = require_once("config/config.php"); //przypisuję do zmiennej $configuration tablicę z pliku config.php

//namespace App; - nie potrzebujemy namespace tutaj

use App\Controller\AbstractController;
use App\Controller\NoteController;
use App\Request;
use App\Exception\AppException;
use App\Exception\ConfigurationException;

$request = new Request($_GET, $_POST, $_SERVER); //tworzę nowy obiekt klasy Request


try {

//$controller = new Controller($request);
//$controller->run();

AbstractController::initConfiguration($configuration); //przekazuję do AbstractControllera tablicę z plikami konfiguracyjnymi bazy danych |wywowuje metode statyczna (powiazana z klasa a nie obiektem)
(new NoteController($request))->run(); //tworze nowy obiekt klasy NoteController, przekazuję do konstuktora(ktory jest w AbstractController) tablicę $request i wywołuję metodą run()


//Pzechwytuwanie wyjątków
} catch (ConfigurationException $e) {
  //mail('xxx@xxx.com', 'Error', $e->getMessage());
  echo '<h1>Wystąpił błąd w aplikacji - ConfigurationExcection</h1>';
  echo 'Problem z aplikacją, proszę spróbować za chwilę.';
  echo '<h3>' .  $e->getMessage() . "|  " .$e->getFile() . " " . $e->getLine() .'</h3>';
  //echo '<h3>' . $e->getTraceAsString() . '</h3>';

} catch (AppException $e) {
  echo '<h1>Wystąpił błąd w aplikacji - AppExcection</h1>';
  echo '<h3>' .  $e->getMessage() . "|  " .$e->getFile() . " " . $e->getLine() .'</h3>';
  //echo '<h3>' . $e->getTraceAsString() . '</h3>';

} catch (\Throwable $e) {                //throwable pochodzi z globalnego namespace
  echo '<h1>Wystąpił błąd w aplikacji - Throwable</h1>';
  echo '<h3>' .  $e->getMessage() . "|  " .$e->getFile() . " " . $e->getLine() .'</h3>';
  //echo '<h3>' . $e->getTraceAsString() . '</h3>';
  //dump( '<h3>' . $e->getTraceAsString() . '</h3>');
}

