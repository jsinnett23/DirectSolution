<?php
/**
 *  DB - A simple database class which also logs errors.
 *  You also need config.php file to define the database configuration settigs.
 *
 *	This class will do SELECT, UPDATE, INSERT and DELETE statements.
 *
 */
require("Log.class.php");
class DB
{

	private $pdo;					# @object, The PDO object.	
	private $sQuery;				# @object, PDO statement object.
	private $bConnected = false;	# @bool ,  Connected to the database.	
	private $log;					# @object, Object for logging exceptions.	
	private $parameters;			# @array, The parameters of the SQL query.
	private $debug = false;			# Set to true to show debug info.
	private $logging = false;		# Set to true to send log details to "logs" folder.
		
    /***********************************************************
	*   Default Constructor 
	*
	*	1. Instantiate Log class.
	*	2. Connect to database.
	*	3. Creates the parameter array.
	*/
		public function __construct()
		{			
			$this->log = new Log();	
			$this->Connect();
			$this->parameters = array();
		}
	
    /***********************************************************
	*	This method makes connection to the database.
	*	
	*	1. Reads the database settings from a config file. 
	*	2. Puts  the config contents into the settings array.
	*	3. Tries to connect to the database.
	*	4. If connection failed, exception is displayed and a log file gets created.
	*/
		private function Connect()
		{

			$config_file_path = "config.php";
			
			include( $config_file_path );
			
			//echo($dbname);
			//echo($dbhost);
			//echo($dbusername);
			//echo($dbpass);
			//exit();						
			
			$dsn = 'mysql:dbname='.$dbname.';host='.$dbhost.'';
			try 
			{
				# Read settings from config file, set UTF8
				$this->pdo = new PDO($dsn, $dbusername, $dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				
				# We can now log any exceptions on Fatal error. 
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				//# Disable emulation of prepared statements, use REAL prepared statements instead.
				//$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);																					
			
				# Connection succeeded, set the boolean to true.
				$this->bConnected = true;
			}
			catch (PDOException $e) 
			{
				# Write into log
				if ($this->logging) {
					echo $this->ExceptionLog($e->getMessage());
				}
				die();
			}
		}

     /***********************************************************
	 *
	 *   You can use this method if you want to close the PDO connection
	 *   If you don't do this explicitly, PHP will automatically close the 
	 *	 connection when your script ends.
	 *
	 */
	 	public function CloseConnection()
	 	{
	 		# Set the PDO object to null to close the connection
	 		# http://www.php.net/manual/en/pdo.connections.php
	 		$this->pdo = null;
	 	}
		
    /***********************************************************
	*	Every method which needs to execute a SQL query uses this method.
	*	
	*	1. If not connected, connect to the database.
	*	2. Prepare Query.
	*	3. Parameterize Query.
	*	4. Execute Query.	
	*	5. On exception : Write Exception into the log + SQL query.
	*	6. Reset the Parameters.
	*/	
		private function Init($query, $parameters = "")
		{
		# Connect to database
		if(!$this->bConnected) { $this->Connect(); }
		try {

				if ($this->debug) {
					echo($query . "<br/>");
				}

				# Prepare query
				$this->sQuery = $this->pdo->prepare($query);

				# Add parameters to the parameter array	
				$this->bindMore($parameters);

				//This is where we need to get the array and get the individual components.
				# Bind parameters
				if(!empty($this->parameters)) {
					foreach($this->parameters as $param)
					{
						$parameters = explode("\x7F", $param);
						$countparas = count($parameters);
						switch ($countparas) {
							case $countparas:								
								case 2:
									$this->sQuery->bindParam( $parameters[0], $parameters[1] );									//No optional parameters
								break;
								case 3:
									$this->sQuery->bindParam( $parameters[0], $parameters[1], $parameters[2] );					//Only the type for the optional paramenter
								break;
								case 4:
									$this->sQuery->bindParam( $parameters[0], $parameters[1], $parameters[2], $parameters[3] );	//Both type and length optional parameter
								break;
								default:
									# Write into log
									if ($this->logging) {
										echo $this->ExceptionLog("Error in parameters");
									}
									die();
								break;
						}	

						if ($this->debug) {
							echo("<strong>para0:</strong> " . $parameters[0] . " <strong>para1:</strong> " . $parameters[1] . " <strong>para2:</strong> " .$parameters[2] . " <strong>para3:</strong> " .$parameters[3] ."<br/>");
						}

					}		
				}

				# Execute SQL 
				$this->success 	= $this->sQuery->execute();

			}
			catch(PDOException $e)
			{
					# Write into log and display Exception
					if ($this->logging) {
						echo $this->ExceptionLog($e->getMessage(), $query );
					}
					die();
			}

			# Reset the parameters
			$this->parameters = array();
		}
		
    /***********************************************************
	*	@void 
	*
	*	Add the parameter to the parameter array
	*	@param string $para			- the name of the parameter
	*	@param string $value		- the value of the parameter
	*	@param string $type			- the type of data
	*/	
		public function bind($para, $value, $type, $length)
		{			

			if ($type == "" && $length == "") {					//No optional parameters
				if ($this->debug) {
					echo("No optional parameters<br/>");
				}
				$this->parameters[sizeof($this->parameters)] = ":" . $para . "\x7F" . $value;
			}
			elseif ($type != "" && $length == "") {				//Only the type for the optional paramenter
				if ($this->debug) {
					echo("Only the type for the optional paramenter<br/>");
				}
				$this->parameters[sizeof($this->parameters)] = ":" . $para . "\x7F" . $value .  "\x7F" . $type;
			}
			elseif ($type != "" && $length != "") {				//Both type and length optional parameter
				if ($this->debug) {
					echo("Both type and length optional parameter<br/>");
				}
				$this->parameters[sizeof($this->parameters)] = ":" . $para . "\x7F" . $value .  "\x7F" . $type .  "\x7F" . $length;
			}
			else {
				# Write into log
				if ($this->logging) {
					echo $this->ExceptionLog("Error with type length in the array");
				}
				die();
			}

			if ($this->debug) {
				echo("<strong>para:</strong> " . $para . " <strong>value:</strong> " . $value . " <strong>type:</strong> " . $type . " <strong>length</strong> " . $length . "<br/>");
			}

		}

    /***********************************************************
	*	@void
	*	
	*	Add more parameters to the parameter array
	*	@param array $parray
	*/	
		public function bindMore($parray)
		{

			if(empty($this->parameters) && is_array($parray)) {

				foreach($parray as $key => $value) {
					
					//echo("elements in the array: " . count($value) . "<br/>");
					$countvalue = count($value);

					$i = 0;
					$keyvalue = "";
					$paramvalue = "";
					$typevalue = "";
					$lengthvalue = "";
					foreach ( $value as $key => $param) {					
						
						switch ($i) {
							case 0:	
								$keyvalue = $key;
								$paramvalue = $param;
								break;
							case 1:
								$typevalue = $param;																
								break;
							case 2:
								$lengthvalue = $param;			
								break;
							default:
								# Write into log
								if ($this->logging) {
									echo $this->ExceptionLog("Error in parameters");
								}
								die();
								break;
						}	
						
						$i++;
						if ($i == 3) {$i = 0;}

					}

					$this->bind( $keyvalue, $paramvalue, $typevalue, $lengthvalue );

				}

			}

		}

    /***********************************************************
	*   If the SQL query contains a SELECT or SHOW statement it returns an array containing all of the result set row
	*	If the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows
	*
	*   @param  string $query
	*	@param  array  $params
	*	@param  int    $fetchmode
	*	@return mixed
	*/			
		public function query($query, $params = null, $fetchmode = PDO::FETCH_ASSOC)
		{
	
			$query = trim($query);

			$this->Init($query, $params);

			$rawStatement = explode(" ", $query);		


			# Which SQL statement is used 
			$statement = strtolower($rawStatement[0]);
			
			if ($statement === 'select' || $statement === 'show') {
				return $this->sQuery->fetchAll($fetchmode);
			}
			elseif ( $statement === 'insert' ||  $statement === 'update' || $statement === 'delete' ) {
				return $this->sQuery->rowCount();	
			}	
			else {
				return NULL;
			}
		}
		
	/***********************************************************
	*  Returns the last inserted id.
	*  @return string
	*/	
		public function lastInsertId() {
			return $this->pdo->lastInsertId();
		}	
		
    /***********************************************************
	*	Returns an array which represents a column from the result set 
	*
	*	@param  string $query
	*	@param  array  $params
	*	@return array
	*/	
		public function column($query, $params = null)
		{
			$this->Init($query,$params);
			$Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);		
			
			$column = null;

			foreach($Columns as $cells) {
				$column[] = $cells[0];
			}

			return $column;
			
		}	

    /***********************************************************
	*	Returns an array which represents a row from the result set 
	*
	*	@param  string $query
	*	@param  array  $params
	*   @param  int    $fetchmode
	*	@return array
	*/	
		public function row($query, $params = null, $fetchmode = PDO::FETCH_ASSOC)
		{				
			$this->Init($query, $params);
			return $this->sQuery->fetch($fetchmode);			
		}

    /***********************************************************
	*	Returns the value of one single field/column
	*
	*	@param  string $query
	*	@param  array  $params
	*	@return string
	*/	
		public function single($query, $params = null)
		{
			$this->Init($query,$params);
			return $this->sQuery->fetchColumn();
		}
	

    /***********************************************************
	*	Sets the debug value to true or false - default is false
	*
	*	@param  boolean $debug
	*	@return void
	*/	
		public function setdebug($debug) {
			$this->debug = $debug;
		}


   /***********************************************************
	*	Sets the logging value to true or false - default is false
	*
	*	@param  boolean $logging
	*	@return void
	*/	
		public function setlogging($debug) {
			$this->logging = $logging;
		}

    /***********************************************************
	* Writes the log and returns the exception
	*
	* @param  string $message
	* @param  string $sql
	* @return string
	*/
	private function ExceptionLog($message, $sql = "")
	{
		$exception  = 'Unhandled Exception. <br />';
		$exception .= $message;
		$exception .= "<br /> You can find the error back in the log.";

		if(!empty($sql)) {
			# Add the Raw SQL to the Log
			$message .= "\r\nRaw SQL : "  . $sql;
		}
			# Write into log
			$this->log->write($message);

		return $exception;
	}			


}
?>
