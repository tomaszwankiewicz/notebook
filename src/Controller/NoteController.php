<?php

declare(strict_types=1);

namespace App\Controller;

class NoteController extends AbstractController
{

  private const PAGE_SIZE = 10;

  public function createAction(): void
  {
    if ($this->request->hasPost()) { //jeśli dane z żądania 'post' nie są puste 
      $noteData = [ //inicjalizacja tablicy $noteData (widoczna tylko w metodzie)
        'title' => $this->request->postParam('title'), 
        //do klucza 'title' przypisuję to co otrzymano z żądania 'post' w tytule notatki
        'description' => $this->request->postParam('description')
        //do klucza 'description' przypisuję to co otrzymano z żądania 'post' w treści notatki
      ];
      $this->noteModel->create($noteData); //dla obiektu $noteModel wywołuję metodę create() i przekazuję tablicę $noteData 
      $this->redirect('/', ['before' => 'created']);
    }

    $this->view->render('create');
  }

  public function showAction(): void
  {
    $this->view->render( //inicjalizacja obiektu $view klasy View w AbstractControlerze
      //wywołanie metody render() i przekazanie stringa 'show' i tablicy 'note'
      'show',
      ['note' => $this->getNote()] //do klucza 'note' przypisujemy funkcję getNote()
    );
  }

  public function listAction(): void
  {
    $phrase = $this->request->getParam('phrase'); 
    // stworznie nowej zmiennej $phrese (tylko wewnątrz metody)
    // dla obiektu $request(klasy Request)->wywoluje metodę getParam i przekazuje do niej stringa 'phrase'. 
    // Obiekt request zainicjalizowany w klasie rodzica (AbstractController) dlatego mogę z niego korzystać
    $pageNumber = (int) $this->request->getParam('page', 1);
    $pageSize = (int) $this->request->getParam('pagesize', self::PAGE_SIZE);
    $sortBy = $this->request->getParam('sortby', 'title');
    $sortOrder = $this->request->getParam('sortorder', 'desc');

    if (!in_array($pageSize, [1, 5, 10, 25])) { //W przpadku gdy w url wpiszę inną liczbę dla pageSize niż 1,5,10,25 to domyślnie zmieni na 10
      $pageSize = self::PAGE_SIZE;
    }

    if ($phrase) {
      $noteList = $this->noteModel->search($phrase, $pageNumber, $pageSize, $sortBy, $sortOrder);
      //stworznie nowej zmiennej $noteList (tylko wewnątrz metody)-> przypisanie do obiektu noteModel z klasy NoteModel(inicjalizajca w klasie rodzica)
      //wywołanie metody search(). Metoda znaduje się w klasie ModelInterface, a klasa NoteModel implemetuje interfejs ModelInterface oraz dodaje linijkę "retun"
      //przekazanie do metody search() zmiennych $phrase, $pageNumber, $pageSize, $sortBy, $sortOrder
      $notes = $this->noteModel->searchCount($phrase);
      //przekazanie do metody searchCount() zmiennej $phrase

    } else {
      $noteList = $this->noteModel->list($pageNumber, $pageSize, $sortBy, $sortOrder);
      $notes = $this->noteModel->count();
    }

    $this->view->render(
      'list',
      [
        'page' => [
          'number' => $pageNumber,
          'size' => $pageSize,
          'pages' => (int) ceil($notes / $pageSize)
        ],
        'phrase' => $phrase,
        'sort' => ['by' => $sortBy, 'order' => $sortOrder],
        'notes' => $noteList,
        'before' => $this->request->getParam('before'),
        'error' => $this->request->getParam('error')
      ]
    );
  }

  public function editAction(): void
  {

    if ($this->request->isPost()) {
      $noteId = (int) $this->request->postParam('id');
      $noteData = [
        'title' => $this->request->postParam('title'),
        'description' => $this->request->postParam('description')
      ];
      $this->noteModel->edit($noteId, $noteData);
      $this->redirect('/', ['before' => 'edited']);
    }

    $this->view->render(
      'edit',
      ['note' => $this->getNote()]
    );
  }

  public function deleteAction(): void
  {
    if ($this->request->isPost()) {
      $id = (int) $this->request->postParam('id');
      $this->noteModel->delete($id);
      $this->redirect('/', ['before' => 'deleted']);
    }

    $this->view->render(
      'delete',
      ['note' => $this->getNote()]
    );
  }

  private function getNote(): array
  {
    $noteId = (int) $this->request->getParam('id'); //do zmiennej $noteId przypisany jest int z id notatki(pobrane z żadania get)
    if (!$noteId) { //jesli nie ma id notatki (jest null)
      $this->redirect('/', ['error' => 'missingNoteId']);
    }

    return $this->noteModel->get($noteId); //zwraca 
  }
}
