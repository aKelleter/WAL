<?php
namespace Wal\Manager;

use PDOStatement;
use PDOException;

require dirname(__DIR__, 2) . '/config/database.php';

abstract class ModelAbstractManager {

  private function connect(): \PDO {
		$db = new \PDO(
			"mysql:host=" . DB_INFOS['host'] . ";port=" . DB_INFOS['port'] . ";dbname=" . DB_INFOS['dbname'],
			DB_INFOS['username'],
			DB_INFOS['password']
		);
		$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$db->exec("SET NAMES utf8");
		return $db;
	}

  /**
   * La fonction executeQuery() permet d'exécuter les requêtes SQL
   * 
   * Elle comporte deux paramètres : la requête SQL et un tableau de paramètres
   * $query (string) : la requête SQL à exécuter (SELECT, INSERT, UPDATE, DELETE…)
   * $params (array) : un tableau de paramètres à binder si la requête contient des marqueurs ':' (ex : WHERE id = :id)

   * @param string $query 
   * @param array $params 
   * @return PDOStatement 
   * @throws PDOException 
   */
  private function executeQuery(string $query, array $params = []): \PDOStatement {

		$db = $this->connect();

		$stmt = $db->prepare($query);

		foreach ($params as $key => $param) $stmt->bindValue($key, $param);

		$stmt->execute();

		return $stmt;
	}

  /**
   * La fonction classToTable() converti le namespace d’une entité en nom de table en DB
   * 
   * Elle comporte 1 paramètre :
   * $class (string) : le namespace d’un model.
   * Par exemple, le namespace "App\Entity\Article" sera converti en "article".
   * 
   * @param string $class 
   * @return string 
   */
  private function classToTable(string $class): string {
		$tmp = explode('\\', $class);
		return strtolower(end($tmp));
	}

  /**
   * La fonction readOne() permet de récupérer un seul enregistrement d'une table (classe)
   * 
   * setFetchMode(\PDO::FETCH_CLASS, $class) permet de spécifier que nous souhaitons mapper les données 
   * récupérées au sein de l’entité spécifiée par le paramètre $class.
   * La méthode readOne() comporte 2 paramètres :
   *
   * $class (string) : le namespace d’un model.
   * $filters (array) : un tableau de critères de filtre de la ressource.
   * 
   * Cette méthode retournera :
   *
   * En cas de succès : un objet
   * En cas d’échec : false
   * 
   * @param string $class 
   * @param array $filters 
   * @return mixed 
   */
  protected function readOne(string $class, array $filters): mixed {

    $query = 'SELECT * FROM ' . $this->classToTable($class) . ' WHERE ';

    foreach (array_keys($filters) as $filter) {
      $query .= $filter . " = :" . $filter;
      if ($filter != array_key_last($filters)) $query .= ' AND ';
    }

    $stmt = $this->executeQuery($query, $filters);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, $class);

    return $stmt->fetch();
  }

  /**
   * La méthode readMany() est dédiée à la récupération de plusieurs enregistrements d'une table (classe)
   * 
   * La méthode comporte 5 paramètres :
   * 
   * $class (string) : le namespace d’un model.
   * (optionnel) $filters (array) : un tableau de critères de filtrage des ressources. 
   *  Exemples : ['slug' => 'recette-gateau-chocolat'], ['draft' => true]…
   * (optionnel) $order (array) : un tableau de critères de tri des ressources. 
   *  Exemples : ['price' => 'ASC'], ['views' => 'DESC']…
   * (optionnel) $limit (int) : un nombre limitant la quantité de ressources à récupérer.
   * (optionnel) $offset (int) : un nombre spécifiant un décalage pour la récupération de ressources (“à partir de telle ligne”).
   *
   *  Cette méthode n’intègre pas de notion de comparaison poussée (<, >, <=, >=…). Ici, $filters ne traite que d’égalités.
   *  A faire évoluer ...
   * 
   * Cette méthode retournera :
   *
   * En cas de succès : un tableau d’objets
   * En cas d’échec : false
   *
   * @param string $class 
   * @param array $filters 
   * @param array $order 
   * @param int|null $limit 
   * @param int|null $offset 
   * @return mixed 
   */
  protected function readMany(string $class, array $filters = [], array $order = [], int $limit = null, int $offset = null): mixed {

		$query = 'SELECT * FROM ' . $this->classToTable($class);

		if (!empty($filters)) {
			$query .= ' WHERE ';
			foreach (array_keys($filters) as $filter) {
				$query .= $filter . " = :" . $filter;
				if ($filter != array_key_last($filters)) $query .= ' AND ';
			}
		}

		if (!empty($order)) {
			$query .= ' ORDER BY ';
			foreach ($order as $key => $val) {
				$query .= $key . ' ' . $val;
				if ($key != array_key_last($order)) $query .= ', ';
			}
		}

		if (isset($limit)) {
			$query .= ' LIMIT ' . $limit;
			if (isset($offset)) {
				$query .= ' OFFSET ' . $offset;
			}
		}

		$stmt = $this->executeQuery($query, $filters);
		$stmt->setFetchMode(\PDO::FETCH_CLASS, $class);

		return $stmt->fetchAll();
	}

  /**
   * La méthode create() enregistre une ressource dans une table (classe)
   * 
   * La méthode comporte 2 paramètres :
   *
   * $class (string) : le namespace d’un model.
   * $fields (array) : les champs à enregistrer en BD (clé-valeur). Le tableau associatif reçu dans cette variable va permettre 
   * de construire, à partir de ses clés, la requête préparée en y précisant tous les champs concernés par l’insertion.
   * 
   * Cette méthode retournera :
   * En cas de succès : une instance de PDOStatement
   * En cas d’échec : false
   * 
   * @param string $class 
   * @param array $fields 
   * @return PDOStatement    
   */
  protected function create(string $class, array $fields): \PDOStatement {

		$query = "INSERT INTO " . $this->classToTable($class) . " (";

		foreach (array_keys($fields) as $field) {
			$query .= $field;
			if ($field != array_key_last($fields)) $query .= ', ';
		}

		$query .= ') VALUES (';
		foreach (array_keys($fields) as $field) {
			$query .= ':' . $field;
			if ($field != array_key_last($fields)) $query .= ', ';
		}

		$query .= ')';
		return $this->executeQuery($query, $fields);
	}

  /**
   * La méthode update() met à jour une ressource au sein d’une table (classe)
   * 
   * La méthode update() comporte 3 paramètres :
   *
   * $class (string) : le namespace d’un model.
   * $fields (array) : les champs à modifier en BD (clé-valeur). Le tableau associatif reçu dans cette variable va 
   * permettre de construire, à partir de ses clés, la requête préparée en y précisant tous les champs concernés par l’édition.
   * $id (string) : l’identifiant de la ressource à éditer.
   *
   * Cette méthode retournera :
   *
   * En cas de succès : une instance de PDOStatement
   * En cas d’échec : false
   *
   * 
   * @param string $class 
   * @param array $fields 
   * @param int $id 
   * @return PDOStatement    
   */
  protected function update(string $class, array $fields, int $id): \PDOStatement {

		$query = "UPDATE " . $this->classToTable($class) . " SET ";

		foreach (array_keys($fields) as $field) {
			$query .= $field . " = :" . $field;
			if ($field != array_key_last($fields)) $query .= ', ';
		}

		$query .= ' WHERE id = :id';
		$fields['id'] = $id;

		return $this->executeQuery($query, $fields);
	}

  /**
   * La méthode remove() supprime une ressource d’une table (classe)
   * 
   * La méthode comporte 2 paramètres :
   *
   * $class (string) : le namespace d’un model
   * $id (int) : l’identifiant de la ressource à supprimer
   *
   * Cette méthode retournera :
   *
   * En cas de succès : une instance de PDOStatement
   * En cas d’échec : false
   *
   * @param string $class 
   * @param int $id 
   * @return PDOStatement    
   */
  protected function remove(string $class, int $id): \PDOStatement {

		$query = "DELETE FROM " . $this->classToTable($class) . " WHERE id = :id";

		return $this->executeQuery($query, [ 'id' => $id ]);
	}

    
}
