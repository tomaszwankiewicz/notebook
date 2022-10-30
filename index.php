<?php

declare(strict_types=1);

namespace App;

use App\Exception\AppException;
use App\Exception\ConfigurationException;
use Throwable;

require_once("src/Utils/debug.php");
require_once("src/Controller.php");
require_once("src/Exception/AppException.php");

$configuration = require_once("config/config.php"); //przypisuję do zmiennej $configuration tablicę z pliku config.php

$request = [            //tworzę tablicę asocjacyjną $request
  'get' => $_GET,       //przypisuję do klucza 'get' wartość 
  'post' => $_POST      //
];

try {

//$controller = new Controller($request);
//$controller->run();

Controller::initConfiguration($configuration);
(new Controller($request))->run();

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

