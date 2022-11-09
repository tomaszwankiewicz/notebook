<?php

declare(strict_types=1);

namespace App;

class Request
{
    private array $get = [];  //tworzę właściwość (pustą tablicę)
    private array $post = []; //tworzę właściwość (pustą tablicę)
    private array $server = [];

    public function __construct(array $get, array $post, array $server)
    {
        $this->get = $get;   //przypisane stworzonej w klasie właściwości do otrzymanej w kontruktoze
        $this->post = $post;
        $this->server = $server;
    }

    public function isPost(): bool        //metoda która sprawdza czy jest postrequest - to jest do edycji notatki
    {
        return $this->server['REQUEST_METHOD'] === 'POST';   //jesli pod kluczem REQUEST_METHOD ze zmiennej globalnej $_SERVER jest POST to metoda da true
    }        

    public function isGet(): bool        //metoda która sprawdza czy jest postrequest - to jest do edycji notatki
    {
        return $this->server['REQUEST_METHOD'] === 'GET';   //jesli pod kluczem REQUEST_METHOD ze zmiennej globalnej $_SERVER jest GET to metoda da true
    }      

    public function hasPost(): bool
    {
        return !empty($this->post);
    }

    //metoda która zwraca dane z geta
    public function getParam(string $name, $default = null) //jeśli wartość $name jest nie podana, podaje wartość domyślną null
    {
        return $this->get[$name] ?? $default;
    }

    //metoda która zwraca dane z posta
    public function postParam(string $name, $default = null)
    {
        return $this->post[$name] ?? $default;
    }
}