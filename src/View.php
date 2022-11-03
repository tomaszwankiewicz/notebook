<?php

declare(strict_types=1);

namespace App;

class View
{
  public function render(string $page, array $params = []): void    //$params = [] - domyslna wartosc to pusta talica
  {
    require_once("templates/layout.php");
  }
}
