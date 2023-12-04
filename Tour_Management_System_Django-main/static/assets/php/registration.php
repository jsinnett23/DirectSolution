
<?php
    
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "project";
   
    // Enter your host name, database username, password, and database name.
    // If you have not set database password on localhost then set empty.
    $con = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }


    // When form submitted, insert values into the database.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // removes backslashes
        $username = stripslashes($_REQUEST['user-name']);
        //escapes special characters in a string
        $username = mysqli_real_escape_string($con, $username);
        $email    = stripslashes($_REQUEST['user-email']);
        $email    = mysqli_real_escape_string($con, $email);
        $password = stripslashes($_REQUEST['user-password']);
        $password = mysqli_real_escape_string($con, $password);
        $create_datetime = date("Y-m-d H:i:s");
        $query    = "INSERT into 'users' (username, password, email, create_datetime)
                     VALUES ('$username', '" . md5($password) . "', '$email', '$create_datetime')";
        $result   = mysqli_query($con, $query);
        
        if ($result) {
            // Set a 200 (okay) response code.
            http_response_code(200);
            echo "Thank You! You have been registered. ";
        } else {
            // Set a 500 (internal server error) response code.
            http_response_code(500);
            echo "Oops! ";
        }
    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }
?>
    


