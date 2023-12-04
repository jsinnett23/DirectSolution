<?php

	/***********************************************************
	*  Add record to session cart array
	*
	*  @param int $recid
	*  @param string $from
	*  @param int $physical (1 = physical always in this case)
	*  @return void
	*/
	function add($recid, $from, $physical) {
	
		//if no session has been started
		if ( !isset( $_SESSION['session_cart'] ) ) {

			//add recid to session
			//session_cart is an array of record ids
			//2d array consisting of item and quantity $_SESSION['session_cart'][$i][0]
			//where 
			//$_SESSION['session_cart'][$i][0] is the item
			//$_SESSION['session_cart'][$i][1] is the qty
		
			$_SESSION['session_cart']  = array(array());
			$_SESSION['session_cart'][0][0] = $recid;
			$_SESSION['session_cart'][0][1] = "1";		//initial quantity of 1
		
			//add physical to session
			$_SESSION['session_physical'] = $physical;

			//set price to 0.00
			$_SESSION['session_total_price'] = "0.00";
		
		} else {

			//Add value
			
			//Need to check if this value has already been entered before adding it
			$bolFound = false;
			for ( $i=0; $i < count($_SESSION['session_cart']); $i++) {
				if ($_SESSION['session_cart'][$i][0] == $recid) {
					$bolFound = true;				
				}
			}

			//now add the item to the cart
			if ($bolFound == false) {
				$j=count($_SESSION['session_cart']);
				$_SESSION['session_cart'][$j][0] = $recid;
				$_SESSION['session_cart'][$j][1] = 1;
			}

		}

	}
	/***********************************************************
	*  Check if the recid exists by getting record count
	*
	*  @return int
	*/
	function checkrecid($recid) {

		$table = getproductdetails($recid);

		$recordcount = count($table);

		return $recordcount;

	}
	/***********************************************************
	*  Returns the full url of the current page to use in the "from" variable
	*
	*  @return string
	*/
	function curPageURL() {
		$pageURL = 'http';
		if(isset($_SERVER["HTTPS"]))
		if ($_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	/***********************************************************
	*  Add 1 to quantity for this recid
	*
	*  @param int $recid
	*  @return integer
	*/
	function increase($recid) {

		$bolFound = false;
		for ( $i=0; $i < count($_SESSION['session_cart']); $i++) {
			if ($_SESSION['session_cart'][$i][0] == $recid) {
				$_SESSION['session_cart'][$i][1]++;
				return;
			}
		}

	}
	/***********************************************************
	*  Remove 1 from quantity for this recid
	*
	*  @param int $recid
	*  @return void
	*/
	function decrease($recid) {

		$bolFound = false;
		for ( $i=0; $i < count($_SESSION['session_cart']); $i++) {
			if ($_SESSION['session_cart'][$i][0] == $recid) {
				if ($_SESSION['session_cart'][$i][1] > 1) {
					$_SESSION['session_cart'][$i][1]--;				
				}
				return;
			}
		}

	}
	/***********************************************************
	*   Removes the recid from the session variable
	*
	*  @param int $recid
	*  @return void
	*/
	function remove($recid) {

			//Copy the contents of 'session_cart' array to a temporary array
			$cart = array();
			for ( $i=0; $i < count($_SESSION['session_cart']); $i++) {
				if ($_SESSION['session_cart'][$i][0] != $recid) {
					array_push($cart, $_SESSION['session_cart'][$i]);
				}
			}

			//Blank out existing 'session_cart' array

			unset( $_SESSION['session_cart'] );

			if ( count($cart) > 0 ) {
				$_SESSION['session_cart'] = array();
				//Copy the contents back to the 'session_cart' array
				for ( $i=0; $i < count($cart); $i++) {
					array_push($_SESSION['session_cart'], $cart[$i]);
				}
			} else {
				//if zero entries, then blank out session_physical
				unset( $_SESSION['session_physical'] );
			}

	}


?>
