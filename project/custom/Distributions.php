<?php

/**
* Класс для работы с таблицей distributions
*/
class Distributions 
{
	private $tableName = "distributions";
	private $pdo;	
	private $fields;

	public function __construct() {
		require_once 'config.php';

        $dsn = "mysql:host={$config['host']};dbname={$config['db']};charset={$config['charset']}";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->pdo = new PDO($dsn, $config['user'], $config['pass'], $opt);
		
		$this->fields = $this->pdo->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$this->tableName}';")->fetchAll(PDO::FETCH_COLUMN, 0);
	}
	
	/**
	* Возвращает все пункты выдачи посылок.
	*
	* @param array $fields Поля вывода в SELECT
    * @return array
	*/
	public function getAll(array $fields = []): array {
		$fields = array_intersect($fields, $this->fields);
		
		// Проверка наличия искомых полей в таблице
        if (!$fields){
			$sql = "SELECT * FROM {$this->tableName}";
		} else {
			$sql = "SELECT " . implode(", " ,$fields) . " FROM {$this->tableName}";
		}
		$distributions = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			
		return $distributions;
	}
}