<?php

//------------------------------------------
//MAIN PAYPAL SITE
//------------------------------------------


//-----------------------------------------
/**
*  Purpose : Displays the paypal hidden variables as applicable to the 
*		shopping cart then posts to the PayPal server

*  There are now two ways to integrate your third-party shopping cart with the PayPal
*  payment flow. The first is to pass in the aggregate amount of the Cart payment, rather
*  than of the individual items. The second is to pass details of the items that have been
*  selected to PayPal, instead of an aggregated amount for the entire Cart.  I use the 
*  second method because I need to know the individual items.

*  Passing Individual Items to PayPal
*  If your third-party shopping cart can be configured to pass individual items to PayPal,
*  information about the items will be included in the buyer’s and seller’s History logs
*  and notifications. To include information about the items, you will post HTML form
*  elements to a new version of PayPal’s Shopping Cart flow. This process is much like
*  the one described in Passing Aggregate Shopping Cart Amount to PayPal, with the
*  following exceptions:
*  1. Set the cmd variable to _cart.
*  2. Replace this required HTML line:
*  <input type="hidden" name="cmd" value="_xclick">
*  with
*  <input type="hidden" name="cmd" value="_cart">
*  3. Add a new variable called upload by adding the following line between the
*  <form> and </form> tags:
*  <input type="hidden" name="upload" value="1">
*  4. Define item details.
*  For each of the following item-specific parameters, define a new set of values
*  that correspond to each item that was purchased via your third-party cart.
*  Append _x to the variable name, where x is the item number, starting with 1
*  and increasing by one for each item that is added.
*  5. Repeat for each item included in cart.
*  Include a set of required variables and any optional variables from the table
*  above for each item included in your buyer’s cart. The first item included in
*  the cart should be defined with parameters ending in _1, such as item_name_
*  1, amount_1, and so on. Similarly, the second item should be denoted with
*  variables like item_name_2, amount_2, and so on.
*  Important: The _x values must increment by one continuously in order to
*  be recognized. If you skip from item #1 to item #3 without defining an item
*  #2, the third item will be ignored.
*  To specify currency: All monetary variables (amount_x, shipping_x, shipping2_x,
*  handling_x, tax_x) will be interpreted in the currency designated by the currency_
*  code variable that is posted with the payment. Since it is not item-specific, there is no
*  need to append a “_x” to the variable name. If no currency_code variable is posted,
*  we will assume that all monetary values are in U.S. Dollars.
*  For a complete list of variables, please refer to the Passing Individual Items to PayPal
*  section of Appendix A in this manual.
*/
function showpaypalcart() {
	
	//Look up post details from tblsetup
	$setup_details 	= gettblsetuppost();
	
		if (count($setup_details) == 0)
		{
			echo("Error in table - problem with tblsetup - SQL error - ipnfunctions.php ref 2");
			exit();
		}

		$paypaladdress 	= $setup_details['paypaladdress'];
		$cancel_url		= $setup_details['cancel_url'];
		$return_url		= $setup_details['return_url'];
		$notify_url		= "";
		$mc_currency 	= $setup_details['mc_currency'];
		
	?>
	<!-- PayPal Configuration --> 
	<input type="hidden" name="cmd" value="_cart" />
	<input type="hidden" name="business" value="<?php echo($paypaladdress); ?>" />
	<input type="hidden" name="currency_code" value="<?php echo($mc_currency); ?>" />
	<input type="hidden" name="no_note" value="1" />
	<input type="hidden" name="upload" value="1" />

	<!-- //language code, France(FR), Spain (ES), Italy (IT), Germany (DE), China (CN), English (US). -->
	<input type="hidden" name="lc" value="US" />	

	<!-- Product Information --> 
	<?php
	
		//record ids of items and quantities are stored in session array        
		for ($i=0; $i<count($_SESSION['session_cart']); $i++) {
		
			$item_id = $_SESSION['session_cart'][$i][0];		//the record of the item			
			$item_details = getproductdetails($item_id);
			?>
			<input type="hidden" name="item_name_<?php echo ($i+1) ;?>" value="<?php echo($item_details[0]['item_name']); ?>" />
			<input type="hidden" name="amount_<?php echo ($i+1) ;?>" value="<?php echo($item_details[0]['mc_gross']); ?>" />
			<input type="hidden" name="item_number_<?php echo ($i+1) ;?>" value="<?php echo($item_details[0]['item_number']); ?>" />
			<input type="hidden" name="quantity_<?php echo ($i+1) ;?>" value="<?php echo($_SESSION['session_cart'][$i][1]); ?>" />
			<?php
            
            //Note that name="no_shipping" defines if you want the shipping address to be prompted for				

        }
    
	?>
	<!-- URLs -->
	<?php
	if ($notify_url != "") {
	?>
		<input type="hidden" name="notify_url" value="<?php echo($notify_url); ?>" />
	<?php
	}
	?>

	<?php
	if ($cancel_url != "") {
	?>
		<input type="hidden" name="cancel_return" value="<?php echo($cancel_url); ?>" />
	<?php
	}
	?>
	
	<?php
	if ($return_url != "") {
	?>	
		<input type="hidden" name="return" value="<?php echo($return_url); ?>" />
	<?php	
	}

}

?>