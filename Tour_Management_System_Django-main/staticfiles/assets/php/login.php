<?php
    // A generic simple login system in PHP. WAMPServer is used for the MySQL backend.
	// Modified from: https://www.tutorialspoint.com/php/php_mysql_login.htm 
   session_start();
   // Establish database connection
   $servername = "localhost";
   $username = "root";
   $password = "root";
   $database = "deepakdb";

   // Create connection
   $conn = mysqli_connect($servername, $username, $password, $database);

   // Check connection
   if (!$conn) {
       die("Connection failed: " . mysqli_connect_error());
   }
   // Initialize the error variable
   $error = "";

   // Check if the user is logged in, if not then redirect him to login page
   //if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
   //    header("location: login.php");
   //    exit;
   //}
   
   
   // If the form has been posted
   if($_SERVER["REQUEST_METHOD"] == "POST") {
           
      // Get rid of speacial characters in the input values
      $myusername = mysqli_real_escape_string($conn,$_POST['username']);
      $mypassword = mysqli_real_escape_string($conn,$_POST['password']); 
      
      $sql = "SELECT username FROM userpass WHERE username = '$myusername' and password = '$mypassword'";
      $result = mysqli_query($conn,$sql);
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC); //The mysqli_fetch_array() function fetches a result row as an associative array (A["index"] = Value)

      $count = mysqli_num_rows($result);
      
      // If result matched $myusername and $mypassword, table row must be 1 row
		
      if($count == 1) {
         $_SESSION['login_user'] = $myusername;  // Save the username
         $_SESSION['loggedin'] = true;  // Someone is logged in!
         
         header("location: ../../index.html");
      } else {
         $error = "Your Login Name or Password is invalid";
      }
   }
?>
