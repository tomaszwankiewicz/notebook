<?php

declare(strict_types=1);

namespace App;

class Request
{
    private array $get = [];  //tworzę właściwość (pustą tablicę)
    private array $post = []; //tworzę właściwość (pustą tablicę)

    public function __construct(array $get, array $post)
    {
        $this->get = $get;   //przypisane stworzonej w klasie właściwości do otrzymanej w kontruktoze
        $this->post = $post;
    }

    public function hasPost(): bool
    {
        return !empty($this->post);
    }

    //metoda która zwraca dane z geta
    public function getParam(string $name, $default = null) //jeśli wartość $name jest niepodana, podaje wartość domyślną null
    {
        return $this->get[$name] ?? $default;
    }

    //metoda która zwraca dane z posta
    public function postParam(string $name, $default = null)
    {
        return $this->post[$name] ?? $default;
    }
}