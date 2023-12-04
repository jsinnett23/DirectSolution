<?php

    /***********************************************************
	*   Return all published items as a table
    *
    *	@param  boolean $paging
	*	@param  int  $startrow
	*   @param  int  $pagesize
	*	@return array
	*/
    function publishedproducts($paging, $startrow, $pagesize) {
	
        //echo("paging: " . $paging . "<br/>");
        //echo("startrow: " . $startrow . "<br/>");
        //echo("pagesize: " . $pagesize . "<br/>");
        //exit;
    
        $intstartrow = (int)$startrow;
        $intpagesize = (int)$pagesize;

        if ( ! is_numeric( $intstartrow ) ) { exit(); }
        if ( ! is_numeric( $intpagesize ) ) { exit(); }

        $db = new Db();	
	    
            if ($paging == true)
            {

                $table = $db->query(
				   "SELECT * FROM ipn_view_publishedproducts ORDER BY sortorder, item_number 
                   LIMIT " . $intstartrow . "," . $intpagesize . ";"
				    );

				//$sql = " SELECT * FROM ipn_view_publishedproducts ORDER BY sortorder, item_number LIMIT :startrow, :pagesize ";
				//$params = array( 
				//	'0' => array ("startrow" => $intstartrow, "type" => PDO::PARAM_INT),
				//	'1' => array ("pagesize" => $intpagesize, "type" => PDO::PARAM_INT)
				//);
				//$table = $db->query($sql, $params);

            }
            else
            {

                $table = $db->query(
				    "SELECT * FROM ipn_view_publishedproducts ORDER BY sortorder, item_number;"
				    );

            }

         return $table;

    }
    /***********************************************************
	*   Return all currencies
    *
	*	@return void
	*/
    //----------------------------------------
    function currencyList() {

        $db = new Db();

        $table = "SELECT * FROM ipn_tblpaypal_currency";
    
        return $table;
    }
    /***********************************************************
	*   Return product details given the recid
    *
    *   @param int $recid
	*	@return array
	*/
    function getproductdetails($recid) {
        
        $db = new Db();

		$sql = " SELECT * FROM ipn_view_publishedproducts WHERE recid = :recid ";       
		$params = array( 
			'0' => array ("recid" => $recid, "type" => PDO::PARAM_INT)
		);
		$table = $db->query( $sql, $params );

        return $table;
	}
	/***********************************************************
	* Get tblsetup details
	* Returns some setup details used for ipn post and returns an array
	*
	* @return array
	*/
	function gettblsetuppost() {

		$db = new Db();

		$query  = " SELECT paypaladdress, sandbox_seller, cancel_url, return_url, notify_url, notify_sandbox_url, mc_currency ";
		$query .= " FROM ipn_tblsetup WHERE recid = 1" ;

		$table = $db->query($query);
	
		if (count($table) == 0)
		{	
		}
		else
		{
			$array_name['paypaladdress'] 		= $table[0]["paypaladdress"];
			$array_name['sandbox_seller']		= $table[0]["sandbox_seller"];		
			$array_name['cancel_url']			= $table[0]["cancel_url"];
			$array_name['return_url']			= $table[0]["return_url"];
			$array_name['notify_url']			= $table[0]["notify_url"];
			$array_name['notify_sandbox_url']	= $table[0]["notify_sandbox_url"];	
			$array_name['mc_currency']			= $table[0]["mc_currency"];				
		}

		return $array_name;
	}

?>