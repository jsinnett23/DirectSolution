<?php

//Define error reporting level

//Report all errors
//error_reporting( E_ALL | E_STRICT | E_DEPRECATED ); 

//Distribution
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);	

?>