
<?php
    require('db.php');
    // When form submitted, insert values into the database.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // removes backslashes
        $firstname = stripslashes($_REQUEST['firstname']);
        
        $lastname = stripslashes($_REQUEST['lastname']);
        //escapes special characters in a string
        #$lastname = mysqli_real_escape_string($con, $username);
        $email    = stripslashes($_REQUEST['email']);
        $email    = mysqli_real_escape_string($con, $email);
        $telephone = stripslashes($_REQUEST['telephone']);
        // $password = mysqli_real_escape_string($con, $password);
        $create_datetime = date("Y-m-d H:i:s");
        $query    = "INSERT into `users` (firstname, lastname, email,telephone, create_datetime)
                     VALUES ('$firstname', '$lastname', '$email','$telephone' ,'$create_datetime')
                     ";
        $result   = mysqli_query($con, $query);
        
        if ($result) {
            // Set a 200 (okay) response code.
            http_response_code(200);
            echo "Thank You! account information changed";
        } else {
            // Set a 500 (internal server error) response code.
            http_response_code(500);
            echo "Oops! Something went wrong and we couldn't register you . Please try again .";
        }
    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }
?>
    


