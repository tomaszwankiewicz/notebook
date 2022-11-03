<?php

declare(strict_types=1);

namespace App;

require_once("src/Utils/debug.php");
require_once("src/Controller.php");
require_once("src/Request.php");
require_once("src/Exception/AppException.php");
//require_once("src/Exception/NotFoundException.php");

use App\Request;
use App\Exception\AppException;
use App\Exception\ConfigurationException;
use Throwable;



$configuration = require_once("config/config.php"); //przypisuję do zmiennej $configuration tablicę z pliku config.php

$request = new Request($_GET, $_POST); //tworzę nowy obiekt klasy Request


try {

//$controller = new Controller($request);
//$controller->run();

Controller::initConfiguration($configuration); //przekazuję do Controllera tablicę z plikami konfiguracyjnymi bazy danych
(new Controller($request))->run(); //tworze nowy obiekt klasy Controller, przekazuję do konstuktora tablicę $request i wywołuję metodą run()


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

} catch (Throwable $e) {
  echo '<h1>Wystąpił błąd w aplikacji - Throwable</h1>';
  //echo '<h3>' .  $e->getMessage() . "|  " .$e->getFile() . " " . $e->getLine() .'</h3>';
  //echo '<h3>' . $e->getTraceAsString() . '</h3>';
}

