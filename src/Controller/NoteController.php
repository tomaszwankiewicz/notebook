<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\NotFoundException;

class NoteController extends AbstractController   //tutaj mamy obsłgę akcji
{
  public function createAction()
  {
    if ($this->request->hasPost()) {
      $noteData = [
        'title' => $this->request->postParam('title'),
        'description' => $this->request->postParam('description'),
      ];
      $this->database->createNote($noteData);
      $this->redirect('/', ['before' => 'created']);
    }

    $this->view->render('create');    //Jeśli nie ma zmiennej $params(po przecinku) to przekazujemy pustą tablicę
  }

  public function showAction()
  {
    $noteId = (int) $this->request->getParam('id'); 

    if (!$noteId) {
      $this->redirect('/', ['error' => 'missingNoteId']);
    }

    try {
      $note = $this->database->getNote($noteId);
    } catch (NotFoundException $e){
      $this->redirect('/', ['error' => 'noteNotFound']);
    }

    $this->view->render(
      'show',
      ['note' => $note]
    ); 
  }

  public function listAction()
  {
    $this->view->render(
      'list', 
      [
        'notes' => $this->database->getNotes(),
        'before' => $this->request->getParam('before'),
        'error' => $this->request->getParam('error'),
      ]
    ); 
  }

  public function editAction()
  {
    $noteId = (int) $this->request->getParam('id');
    if (!$noteId) {
      $this->redirect('/', ['error' => 'missingNoteId']);
    }

    $this->view->render(
      'edit'
    );   
  }

private function redirect(string $to, array $params): void
{
  $location = $to;

  if (count($params)) {
    $queryParams = [];
    foreach ($params as $key => $value) {
      $queryParams[] = urlencode($key) . '=' . urlencode($value);
    } 
    $queryParams = implode('&', $queryParams);     //metoda która łączy wszystkie parametry w tablic
    $location .= '?' . $queryParams;
  }
  
  header("Location: $location");
  exit;
}

}
