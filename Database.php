<?php
namespace waterDB;

class Database
{
	public $dbData;
	public $dbLocation;
	
	public function __construct($file) {
		$this->dbLocation = $file;
		if (!file_exists($this->dbLocation)) {
			$this->dbData = array();
			$this->Save();
		}
		$this->Reload();
	}
	
	private function Reload() {
		$this->dbData = unserialize(base64_decode(trim(file_get_contents($this->dbLocation))));
	}
	
	public function Save() {
		file_put_contents($this->dbLocation, base64_encode(serialize($this->dbData)), LOCK_EX);
		$this->Reload();
	}
	
	public function GetAllTables() {
		return array_keys($this->dbData);
	}
	
	public function TableExists($name) {
		return (isset($this->dbData[$name]));
	}
	
	public function CreateTable($name, $structure = array()) {
		if ($this->TableExists($name) || count($structure) == 0) {
			return false;
		}
		$dbRows = array_unique(array_values($structure));
		$this->dbData[$name] = array(
			"Columns" => $dbRows,
			"Rows" => array()
		);
		$this->Save();
		return true;
	}
	
	public function DeleteTable($name) {
		if (isset($this->dbData[$name])) {
			unset($this->dbData[$name]);
			$this->Save();
			return true;
		}
		return false;
	}
	
	public function ColumnCount($table) {
		if (isset($this->dbData[$table])) {
			return count($this->dbData[$table]["Columns"]);
		}
		return false;
	}
	
	public function RowCount($table) {
		if (isset($this->dbData[$table])) {
			return count($this->dbData[$table]["Rows"]);
		}
		return false;
	}
}
?>