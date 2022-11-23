<?php

declare(strict_types=1);

namespace App;

class Request
{
  private array $get = []; //inicjalizacja tablicy $get
  private array $post = []; //inicjalizacja tablicy $post
  private array $server = []; //inicjalizacja tablicy $server

  public function __construct(array $get, array $post, array $server)  //Otrzymuje dane do konstruktora z żądania GET, POST i SERVER(tablice)
  {
    $this->get = $get; //przypisanie tablicy $get otrzymanej z kontruktora do zainicjalizowanej w klasie Request
    $this->post = $post; ////przypisanie tablicy $post otrzymanej z kontruktora do zainicjalizowanej w klasie Request
    $this->server = $server;
  }

  public function isPost(): bool
  {
    return $this->server['REQUEST_METHOD'] === 'POST';
  }

  public function isGet(): bool
  {
    return $this->server['REQUEST_METHOD'] === 'GET';
  }

  public function hasPost(): bool
  {
    return !empty($this->post); 
    //zwraca true jeśli tablica z żądania 'post' nie jest pusta
    //zwraca false jeśli tablica z żadania 'post' jest pusta
  }

  public function getParam(string $name, $default = null) 
    //W przypadku wywołania z metody action() przekazany jest string 'action' i przypisany jest do zmiennej $name oraz string 'list' i przypisany jest do zmiennej $default.
    //Jeśli string 'list' nie został przekazany to $default domyślnie jest null. W przypadku wywołania z metody action() - AbstractController on zawsze jest przekazany bo to stała.
  
    //W przypadku wywołania z metody listAction() (dla $phrase) - NoteController przekazujemy string 'phrase'.
    //W przypadku wywołania z metody listAction() (dla $pageNumber) - NoteController przekazujemy string 'page' oraz inta - 1.
    //W przypadku wywołania z metody listAction() (dla $pageSize) - NoteController przekazujemy string 'pagesize' oraz stałą PAGE_SIZE = 10.
    //W przypadku wywołania z metody listAction() (dla $sortBy) - NoteController przekazujemy string 'sortby' oraz 'title'.
    //W przypadku wywołania z metody listAction() (dla $sortOrder) - NoteController przekazujemy string 'sortorder' oraz 'desc'.
    
    //W przypadku wywołania z metody getNote() (dla $noteId) - NoteController przekazujemy string 'id'.
  {
    return $this->get[$name] ?? $default; 
    // zwraca ze zmiennej globalnej GET (a dokladniej z adresu URL) parametr który jest po znaku "=" pod odpowiednim kluczem(tutaj action)
    // np notebook.localhost/?action=create. Tutaj zwraca create. 
    // Jeśli coś jest po lewej to zwraca to co po lewej po "="

    // ?? - jesli to co po lewej stronie jest nullem to zwraca to co po prawej stronie czyli 'list' bo to zawsze przekazujemy (dla metody action). 
    // np notebook.localhost - tutaj zwraca 'list' bo po lewej jest null (w sensie nie ma np /?action=create)

    // metoda getParam() wywołana z metody action() zwraca stringa 'create'/'delete'/'edit'...  albo  'list'

    //W przypadku wywołania z metody listAction() (dla $phrase) - NoteController zwraca  null. LUB TO CO JEST WYSZUKIWANE?
    //W przypadku wywołania z metody listAction() (dla $pageNumber) - NoteController zwraca inta 1,2,3... , domyslnie 1
    //W przypadku wywołania z metody listAction() (dla $pageSize) - NoteController zwraca inta 1, 5, 10 lub 25. domyślnie PAGE_SIZE=10.
    //W przypadku wywołania z metody listAction() (dla $sortBy) - NoteController zwraca 'title' lub 'created'. Jeśli nie prekazaliśmy klucza to domyślnie 'title'.
    //W przypadku wywołania z metody listAction() (dla $sortOrder) - NoteController zwraca 'desc' lub 'asc'. Jeśli nie prekazaliśmy klucza to domyślnie 'desc'.
    //W przypadku wywołania z metody getNote() (dla $noteId) - NoteController zwraca numer id notatki. Jeśli nie przekazaliśmy klucza to domyślnie null.
  }

  public function postParam(string $name, $default = null)
  {
    //W przypadku wywołania z metody createAction() - dla 'title' (z NoteController) przekazujemy string 'title'.
    //W przypadku wywołania z metody createAction() - dla 'description' (z NoteController) przekazujemy string 'description'.

    return $this->post[$name] ?? $default;
    //W przypadku wywołania z metody createAction() - dla 'title' (z NoteController) zwraca treść z pola "Tytuł"
    //W przypadku wywołania z metody createAction() - dla 'description' (z NoteController) zwraca treść z pola "Treść"
  }
}
