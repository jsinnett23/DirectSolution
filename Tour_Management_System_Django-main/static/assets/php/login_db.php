<?php
    

    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "deepakdb";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
    }


    // Only process POST reqeusts.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form fields and remove whitespace.
  //       $name = strip_tags(trim($_POST["name"]));
		// $name = str_replace(array("\r","\n"),array(" "," "),$name);
  //       $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
  //       $password = trim($_POST["password"]);



        $myusername = mysqli_real_escape_string($conn,$_POST["username"]);
        $mypassword = mysqli_real_escape_string($conn,$_POST["password"]); 
        // $message = trim($_POST["message"]);

        // // Check that data was sent to the mailer.
        // if ( empty($name) OR empty($password) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //     // Set a 400 (bad request) response code and exit.
        //     http_response_code(400);
        //     echo "Please complete the form and try again.";
        //     exit;
        // }

        // Set the recipient email address.
        // FIXME: Update this to your desired email address.
        // $recipient = "deepak_kasi_nathan@yahoo.com";

        //$recipient=  'deepak_kasi_nathan@yahoo.com';

        // Set the email subject.
        // $subject = "New contact from $name";

        // // Build the email content.
        // $email_content = "Name: $name\n";
        // $email_content .= "Email: $email\n\n";
        // $email_content .= "Subject: $subject\n\n";
        // $email_content .= "Message:\n$message\n";

        // Build the email headers.
        //$email_headers = "From: $name <$email>";

        // $sql = "INSERT INTO reg (name,password, email ) VALUES ('".$name."','".$password."','".$email."')";

        $sql = "SELECT username FROM userpass WHERE username = '$myusername' and password = '$mypassword'";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_array($result,MYSQLI_ASSOC);


        $count = mysqli_num_rows($result);
      
      // If result matched $myusername and $mypassword, table row must be 1 row
        
      if($count == 1) {
         $_SESSION['login_user'] = $myusername;  // Save the username
         $_SESSION['loggedin'] = true;  // Someone is logged in!
         
        //  <script type="text/javascript">
        // window.location.href = 'http://www.google.com.au/';
        // </script>
        //  header("location: ../../index.html");

         http_response_code(200);
         echo "login successful";
      } 
      else {
         
         http_response_code(500);
        echo "Oops! Something went wrong and we couldn't log you in ";
      }





    } 

    else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }
    

?>
