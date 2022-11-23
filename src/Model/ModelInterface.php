<?php

declare(strict_types=1);

namespace App\Model;

interface ModelInterface
//Interfejsy są opisami tego, co dana klasa powinna posiadać. 
//Można ich użyć, aby upewnić się, że dowolna klasa implementująca interfejs będzie zawierała każdą metodę publiczną, która jest w nim zdefiniowana.

//Interfejsy mogą być:
//-używane do definiowania metod publicznych dla danej klasy.
//-używane do definiowania stałych dla klasy.

//Interfejsy nie mogą:
//-Być samodzielnie instancjowane.
//-Być używane do definiowania prywatnych lub chronionych metod klasy.
//-Definiować właściwości klasy.

{
  public function list(
    int $pageNumber,
    int $pageSize,
    string $sortBy,
    string $sortOrder
  ): array;

  public function search( 
    string $phrase,
    int $pageNumber,
    int $pageSize,
    string $sortBy,
    string $sortOrder
  ): array;

  public function count(): int;

  public function searchCount(string $phrase): int; 

  public function get(int $id): array;

  public function create(array $data): void;

  public function edit(int $id, array $data): void;

  public function delete(int $id): void;
}
