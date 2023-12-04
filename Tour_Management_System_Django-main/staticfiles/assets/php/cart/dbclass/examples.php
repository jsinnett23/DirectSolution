<?php

//This uses the tables as created by the testing.sql script

$recid = 3;			//Example recid
$publish = true;		//Example value for publish field

//Set the debug and logging flag
$db->setdebug(true);
$db->setlogging(true);

require_once("config.php");
require_once("Dbpdo.class.php");

$db = new Db();
	
$sql = "SELECT * FROM tblitems WHERE recid = :recid AND publish = :publish";
$params = array( 
	'0' => array ("recid" => $recid, "type" => PDO::PARAM_INT), 
	'1' => array ("publish" => $publish, "type" => PDO::PARAM_BOOL)
	);

$productdetails = $db->query($sql, $params);
foreach( $productdetails as $d_row ) {
	echo( $d_row["item_name"] . "<br/>");
}

//The $params array consists of the parmater name and the data type for that parameter.  So in the above example we have recid which is an INT type and a publish flag which is a boolean type.

## Fetching data
$sql = "SELECT * FROM tblitems";
$productdetails =  $db->query($sql);
foreach( $productdetails as $d_row ) {
	echo( $d_row["item_name"] . "<br/>");
}

//By default, this returns an assoicate array using FETCH_ASSOC as the fetch mode.

//If you want to use another fetchmode just give it as a parameter.  So in the above example:

$productdetails =  $db->query($sql, null, PDO::FETCH_NUM);


## Fetch a value
$sql = "SELECT item_name FROM tblitems WHERE recid = :recid";
$params = array( 
	'0' => array ("recid" => $recid, "type" => PDO::PARAM_INT), 
	);
$products = $db->query($sql, $params);
echo( $products[0]["item_name"] );

## Fetching a single value
$sql = "SELECT item_name FROM tblitems WHERE recid= :recid";
$params = array( 
	'0' => array ("recid" => $recid, "type" => PDO::PARAM_INT)
	);
$item_name = $db->single($sql, $params);
echo($item_name);

## Fetching a Single Row
$sql = "SELECT item_name, item_number FROM tblitems WHERE recid = :recid";
$params = array( 
	'0' => array ("recid" => $recid, "type" => PDO::PARAM_INT)
	);
$items = $db->row( $sql, $params );
print_r($items);

## Fetching a Column, numeric index
$sql = "SELECT item_name FROM tblitems";
$items = $db->column($sql);
print_r($items);

## Insert Statement
$item_number = "Fruit";
$mc_gross = "2.27";
$sql = "INSERT INTO tblitems ( item_number, mc_gross) VALUES ( :item_number, :mc_gross )";
$params = array( 
	'0' => array ("item_number" => $item_number, "type" => PDO::PARAM_STR), 
	'1' => array ("mc_gross" => $mc_gross, "type" => PDO::PARAM_STR)
	);
$insert	= $db->query( $sql, $params );	//The number of rows affected
$lastinsertedid = $db->lastInsertId();	//Returns last inserted id	
echo("Last inserted id: " . $lastinsertedid);

## Update Statement
$recid = 4;
$item_name = "Apples";
$sql = "UPDATE tblitems SET item_name = :item_name WHERE recid = :recid";
$params = array( 
	'0' => array ("item_name" => $item_name, "type" => PDO::PARAM_STR), 
	'1' => array ("recid" => $recid, "type" => PDO::PARAM_INT)
	);
$update	= $db->query( $sql, $params );
echo($update);	//The number of rows affected

## Delete statement
$recid = 4;
$sql = " DELETE FROM tblitems WHERE recid = :recid ";
$params = array( 
	'0' => array ("recid" => $recid, "type" => PDO::PARAM_INT), 
	);
$delete	 =  $db->query( $sql, $params ); 
echo($delete);	//The number of rows affected

?>
