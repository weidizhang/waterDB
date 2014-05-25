<?php
namespace waterDB;

class QueryHandler
{
	private $DB;

	public function __construct($obj) {
		$this->SetDatabase($obj);
	}
	
	public function SetDatabase($obj) {
		$this->DB = $obj;
	}

	public function Insert($table, $data = array()) {
		if ($this->DB->TableExists($table)) {
			if (count($data) > $this->DB->ColumnCount($table)) {
				return false;
			}
			else {
				if ($this->IsAssocArray($data)) {
					$newRow = array_fill_keys(array_values($this->DB->dbData[$table]["Columns"]), null);
					foreach ($data as $dataKey => $dataValue) {
						if (array_key_exists($dataKey, $newRow) && !is_object($dataValue) && !is_array($dataValue)) {
							$newRow[$dataKey] = $dataValue;
						}
					}
					
					if (count(array_filter($newRow)) > 0) {
						$this->DB->dbData[$table]["Rows"][] = $newRow;
						$this->DB->Save();
						return true;
					}
					return false;
				}
				elseif (count($data) == $this->DB->ColumnCount($table)) {
					$this->DB->dbData[$table]["Rows"][] = array_combine(array_values($this->DB->dbData[$table]["Columns"]), $data);
					$this->DB->Save();
					return true;
				}
				
				return false;
			}			
		}
		return false;
	}
	
	public function Select($table, $where = array()) {
		if ($this->DB->TableExists($table)) {
			$rowResults = array();
			$getColumns = array_values($this->DB->dbData[$table]["Columns"]);
			
			if (count($where) > 0) {
				foreach ($this->DB->dbData[$table]["Rows"] as $rowData) {
					$rowOK = true;
					foreach ($where as $searchKey => $valueReq) {
						if (array_key_exists($searchKey, $rowData)) {
							$checkField = $rowData[$searchKey];
							if (is_string($valueReq) && is_string($checkField)) {
								if (strtolower($valueReq) !== strtolower($checkField)) {
									$rowOK = false;
									break;
								}
							}
							else {
								if ($valueReq != $checkField) {
									$rowOK = false;
									break;
								}
							}
						}
						else {
							$rowOK = false;
							break;
						}
					}
					
					if ($rowOK) {
						$rowResults[] = $rowData;
					}
				}
			}
			else {
				$rowResults = $this->DB->dbData[$table]["Rows"];
			}
			return $rowResults;
		}
		return false;
	}
	
	public function Delete($table, $where = array()) {
		if ($this->DB->TableExists($table)) {
			if (count($where) > 0) {
				$rowsToDelete = $this->Select($table, $where);
				foreach ($this->DB->dbData[$table]["Rows"] as $rowIndex => $row) {
					foreach ($rowsToDelete as $removeRow) {
						if ($row === $removeRow) {
							unset($this->DB->dbData[$table]["Rows"][$rowIndex]);
						}
					}
				}
				$this->DB->Save();
				return true;
			}
			else {
				$this->DB->dbData[$table]["Rows"] = array();
				$this->DB->Save();
				return true;
			}
		}
		return false;
	}
	
	private function IsAssocArray($arr) {
		return array_keys($arr) !== range(0, count($arr) - 1);
	}
}
?>