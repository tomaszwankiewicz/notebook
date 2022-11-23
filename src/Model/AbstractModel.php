<?php

declare(strict_types=1);

namespace App\Model;

use PDO;
use App\Exception\ConfigurationException;
use App\Exception\StorageException;
use PDOException;

abstract class AbstractModel
{
  protected PDO $conn; //inicjalizacja nowego okietu klasy PDO (Widoczne dla klas potomnych) //wbudowana klasa PDO ustanawia połączenie aplikacji z bazą danych

  public function __construct(array $config) //otrzymany do kontruktora tablica z danymi konfiguracyjnymi bazy danych 
  {
    try {
      $this->validateConfig($config); //walidacja
      $this->createConnection($config); //ustanawia połącznie z bazą danych
    } catch (PDOException $e) {
      throw new StorageException('Connection error');
    }
  }

  private function createConnection(array $config): void //ustanawia połącznie z bazą danych
  {
    $dsn = "mysql:dbname={$config['database']};host={$config['host']}"; //inicjalizacja zmiennej $dsn
    $this->conn = new PDO(
      $dsn,   //sprawdza watości przypisane do kluczy('databse' i 'host')
      $config['user'], //sprawdza watości przypisane do klucza 'user'
      $config['password'],//sprawdza wartości przypisane do klucza 'password'
      [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION //wyrzuca błąd jeśli nie udało się połączyć
      ]
    );
  }

  private function validateConfig(array $config): void //metoda sprawdzająca czy klucze w tablicy zawierają poprawne wartości(lub czy nie są puste). 
  {
    if (
      empty($config['database'])
      || empty($config['host'])
      || empty($config['user'])
      || empty($config['password'])
    ) {
      throw new ConfigurationException('Storage configuration error');
    }
  }
}
