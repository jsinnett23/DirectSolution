<?php

$recid = 3;
$publish = true;
$item_number = "Bottle";

require_once("config.php");
require_once("Dbpdo.class.php");

// Creates the instance
$db = new Db();
echo("<br/>--------------------------------<br/>");

//Set the debug and logging flag
$db->setdebug(true);
$db->setlogging(true);

//Simple select on a table
$sql = "SELECT * FROM tblitems";
$table = $db->query($sql);
print_r($table);
echo("<br/>--------------------------------<br/>");

//Simple select on a view
$sql = "SELECT * FROM view_publisheditems";
$table = $db->query($sql);
print_r($table);
echo("<br/>--------------------------------<br/>");

//Select all values with a particular string item_number using a length
$sql = "SELECT * FROM tblitems WHERE item_number = :item_number";
$params = array( 
	'0' => array ("item_number" => $item_number, "type" => PDO::PARAM_STR, "length" => 10)
	);
$table = $db->query($sql, $params);
print_r($table);
echo("<br/>--------------------------------<br/>");

//Select all values with a particular string item_number without using a length
$sql = "SELECT * FROM tblitems WHERE item_number = :item_number";
$params = array( 
	'0' => array ("item_number" => $item_number, "type" => PDO::PARAM_STR)
	);
$table = $db->query($sql, $params);
print_r($table);
echo("<br/>--------------------------------<br/>");

//Select all values with two parameters
$sql = "SELECT * FROM tblitems WHERE recid = :recid AND publish = :publish";
$params = array( 
	'0' => array ("recid" => $recid, "type" => PDO::PARAM_INT), 
	'1' => array ("publish" => $publish, "type" => PDO::PARAM_BOOL)
	);
$table = $db->query($sql, $params);
print_r($table);
echo("<br/>--------------------------------<br/>");

//Select a single value
$sql = "SELECT item_name FROM tblitems WHERE recid= :recid";
$params = array( 
	'0' => array ("recid" => $recid, "type" => PDO::PARAM_INT)
	);
$item_name = $db->single($sql, $params);
echo($item_name);
echo("<br/>--------------------------------<br/>");

//Select a row
$sql = "SELECT item_name, item_number FROM tblitems WHERE recid = :recid";
$params = array( 
	'0' => array ("recid" => $recid, "type" => PDO::PARAM_INT)
	);
$items = $db->row( $sql, $params );
print_r($items);
echo("<br/>--------------------------------<br/>");

//Fetch a column, numeric index
$sql = "SELECT item_name FROM tblitems";
$items = $db->column($sql);
print_r($items);
echo("<br/>--------------------------------<br/>");

//Insert Statement
$item_number = "Fruit";
$mc_gross = "2.27";
$sql = "INSERT INTO tblitems ( item_number, mc_gross) VALUES ( :item_number, :mc_gross )";
$params = array( 
	'0' => array ("item_number" => $item_number, "type" => PDO::PARAM_STR), 
	'1' => array ("mc_gross" => $mc_gross, "type" => PDO::PARAM_STR)
	);
$insert	= $db->query( $sql, $params );	//the number of rows affected
$lastinsertedid = $db->lastInsertId();	//returns last inserted id	
echo("Last inserted id: " . $lastinsertedid);
echo("<br/>--------------------------------<br/>");

## Update Statement
$item_name = "Apples";
$sql = "UPDATE tblitems SET item_name = :item_name WHERE recid = :recid";
$params = array( 
	'0' => array ("item_name" => $item_name, "type" => PDO::PARAM_STR), 
	'1' => array ("recid" => $lastinsertedid, "type" => PDO::PARAM_INT)
	);
$update	= $db->query( $sql, $params );
echo($update);	//the number of rows affected
echo("<br/>--------------------------------<br/>");

## Delete statement
$sql = " DELETE FROM tblitems WHERE recid = :recid ";
$params = array( 
	'0' => array ("recid" => $lastinsertedid, "type" => PDO::PARAM_INT), 
	);
$delete	 =  $db->query( $sql, $params ); 
echo($delete);	//the number of rows affected
echo("<br/>--------------------------------<br/>");

/*
foreach($params as $key => $value) {
	//echo("The array value at " . $key . " is $value<br/>.");
	foreach ( $value as $key => $value) {
		echo("The array value at " . $key . " is $value<br/>.");
	}
}
*/

?>
