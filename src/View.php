<?php

declare(strict_types=1);

namespace App;

class View
{
  public function render(string $page, array $params = []): void    //$params = [] - domyslna wartosc to pusta talica
  {
    $params = $this->escape($params);
    require_once("templates/layout.php");
  }

  private function escape(array $params): array
  {
    $clearParams = [];

    foreach ($params as $key => $param) {   //$key-nazwa parametru, $param -wartość parametru
      if (is_array($param)) {                        //jeśli to co znajduje sie pod kluczem $key(czyli $param) jest tablicą 
        $clearParams[$key] = $this->escape($param); //to wywołaj ponownie escape ale przekaż mu tą tablicę //wywołanie rekurencjne czyli wywołanie metody w tej samej metodzie
      } else if ($param) {
        $clearParams[$key] = htmlentities((string) $param); //zapisujemy pod tym samym kluczem
      } else {
        $clearParams[$key] = $param; //jak bedzie null
      }
    }
    return $clearParams;
  }
}
