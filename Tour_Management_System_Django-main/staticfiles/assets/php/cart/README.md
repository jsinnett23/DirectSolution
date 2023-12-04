# PHP-Cart application

This is a PHP Shopping Cart using sessions and includes a Bootstrap menu.

The cart creates a PayPal buy now button which takes the customer to PayPal where they can log in and purchase the items.

https://github.com/paulv200/php-cart

Version 1.0.0

2020-04- 13

## Features

* Displays with Bootstrap so that they look good on any device, PC, tablet or smartphone

* A MySQL database script for creating the required tables with test data.

* An index page that lists all the products using a paging system.

* A detail page to show the product in more detail.

* A view cart page with the cart summary and a PayPal button to take the customer to PayPal.

## Installation

* Create a new MySQL database.  Create a user and password and set privilages for the user against the new database.

* In your MySQL database, run the sqlscript.sql located in the db folder to create the tables.

* In the folder dbclass, there is a file config.php.  Enter in the database details for the database that you have created.

* Upload all the files to a WebServer and display the index.php page in a browser.

* In ipn_tblsetup update the field paypaladdress to your PayPal email address.

## The error.php include file

The include file error.php is included in all scripts and defines the error_reporting level.  Set the error_reporting level for develpment or destribution.

