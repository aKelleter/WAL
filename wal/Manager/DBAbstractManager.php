<?php
namespace Wal\Manager;

use PDOStatement;
use PDOException;

require dirname(__DIR__, 2) . '/config/database.php';

abstract class DBAbstractManager {

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
     * $class (string) : le namespace d’une entité.
     * Par exemple, le namespace "App\Entity\Article" sera converti en "article".
     * 
     * @param string $class 
     * @return string 
     */
    private function classToTable(string $class): string {
		$tmp = explode('\\', $class);
		return strtolower(end($tmp));
	}

    // I'm here...
    // https://laconsole.dev/formations/framework-php/modeles#m%C3%A9thodes-g%C3%A9n%C3%A9riques-crud
    
}
