<?php session_start();
//----------------------------------------------------
//Process the transaction
//
//This takes the record id from the paypal button and creates
//a set of variables to be posted for this record
//
//It uses Javascript on load to post, so includes  
//noscript incase the Javascript is not enabled.
//----------------------------------------------------

//----------------------------------------------------
require_once("../includes/error.php");
require_once('includes/ipnfunctions.php'); 
require_once('../functions/misc.php'); 
require_once("../dbclass/Dbpdo.class.php");
require_once("../classes/clsinput.php");
//----------------------------------------------------

$cmd = input::get('cmd');
if ($cmd == "_cart") {	//This is a cart selection

	//First check if there is anything in $_SESSION['session_cart']
	if ( count($_SESSION['session_cart']) == 0 ) {
		echo("Error with items - process.php ref 1 - usually caused by the session variables not working on the server");
		exit();
	}
	
}
else {
	//Not a cart purchase
	exit();
}

?><!DOCTYPE html>
<html lang="en">
  <head>
	<title>PHP PayPal Cart</title> 
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
      
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" 
          crossorigin="anonymous">      
      
    <style type="text/css">
       .body, td, p {
	   font-size: 110%;
	   font-family: Verdana, Arial, Helvetica, sans-serif; 
        }
        .tableblue {
        /*background-color: #0066ff;*/
        }
      </style>
  </head>
<body onLoad="document.paypal_form.submit();">
<!-- <body> -->
	
	<form method="post" name="paypal_form" action="https://www.paypal.com/cgi-bin/webscr">
		<?php
			showpaypalcart();
		?>
		<table border="0" width="100%">
		<tr height="100">
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="center" valign="center" height="120">
				
			<div id="dialog" title="Please Wait">
				<p>&nbsp;</p>
				<h2 style="text-align:center;">Please wait ...</h2>
				<h2 style="text-align:center;">Connecting to PayPal</h2>
				<p style="text-align:center;"><img src="../assets/pleasewait.gif" style="align:center" /></p>
			</div> 
				
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>	
		</table>
	</form>
	
	<br/><br/>
	<noscript>
		<table border="0" align="center">
		<tr>
			<td>
				<h2>This page requires Javascript</h2>
			</td>
		</tr>
		</table>
	</noscript>	
</body>   
</html>