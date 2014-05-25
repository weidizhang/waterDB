<?php
require "QueryHandler.php";
require "Database.php";

$waterDB = new \waterDB\Database("file.h2o-db");
$waterDB->CreateTable("Test_data", array(
	"Name",
	"Email",
	"Time"
));

$waterQuery = new \waterDB\QueryHandler($waterDB);
$findJohn = $waterQuery->Select("Test_data", array("Name" => "John Smith"));
if (count($findJohn) == 0) {
	$waterQuery->Insert("Test_data", array(
		"John Smith",
		"secret@nsa.gov",
		time()
	));
}

$allRows = $waterQuery->Select("Test_data");
print_r($allRows);

// $delete = $waterQuery->Delete("Test_data", array("Name" => "John Smith"));

$anotherDB = new \waterDB\Database("file2.h2o-db");
$waterQuery->SetDatabase($anotherDB);
$anotherDB->CreateTable("Links", array("URL", "OptionalTag"));
$waterQuery->Insert("Links", array(
	"URL" => file_get_contents("http://is.gd/create.php?format=simple&url=http://google.com/")
));
print_r($waterQuery->Select("Links"));
?>