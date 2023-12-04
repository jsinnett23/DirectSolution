<?php session_start();

	
//----------------------------------------------
require_once("includes/error.php");
require_once("dbclass/Dbpdo.class.php");
require_once("functions/misc.php");
require_once("functions/cart.php");
require_once("classes/clsinput.php");
//----------------------------------------------
	
$submitAction	= input::get('action');
$submitRecid	= input::get('recid');

if ( isset( $_SESSION['from'] ) ) {
	$submitFromURL = $_SESSION['from'];
}
else {
	$submitFromURL	= input::get('from');
}

switch($submitAction) {

	case "add":
		if ( checkrecid($submitRecid) != 0 ) {
			$physical = getisphysicalrecid($submitRecid);
			add($submitRecid, $submitFromURL, $physical);
		}				
		display($submitFromURL, "");
		break;

	case "remove":
		remove($submitRecid);
		display($submitFromURL, "");
		break;

	case "increase";

		increase($submitRecid);
		display($submitFromURL, "");				
		break;

	case "decrease";
		decrease($submitRecid);
		display($submitFromURL, "");				
		break;

	default:
		display($submitFromURL, "");			
		break;

}

//-------------------------------------------------
function display($from, $mess) {

require_once("includes/header.php");
require_once("includes/menu.php");

?>
    
	<div class="container">    
		  
		<div class="shoppingproducts">
			
			       	
			
			<div class="row">
				<div class="col-sm-2">&nbsp;</div>
            	<div class="col-md-8"><hr></div>
				<div class="col-sm-4">&nbsp;</div>
			</div>			
			
			<div class="row">
				
				<div class="col-md-2 col-lg-2">&nbsp;</div>  					
   				<div class="col-md-8 col-lg-8">
				
					<table border="0" align="center" style="width:100%;">
					<tr>						
						<td colspan="13">
							<h4>Your Shopping Cart...</h4>
							<p><strong><?php if ($mess != "") { echo($mess); }?></strong></p>
						</td>
					</tr>

					<?php
					if (isset($_SESSION['session_cart'])) {
					?>
						
					<tr bgcolor="#DCDBDB">
						<td width="10">&nbsp;</td>
						<td align="center"><strong>Item</strong></td>
						<td width="10">&nbsp;</td>
						<td align="center"><strong>Qty</strong></td>		
						<td width="10">&nbsp;</td>

						<td align="center" colspan="3"></td>

						<td width="10">&nbsp;</td>

						<td align="center"><strong>Price</strong></td>
						<td width="10">&nbsp;</td>	
						<td align="center"><strong>Remove</strong></td>
						<td width="10">&nbsp;</td>		
					</tr>
					<?php

							$total_price = 0;

							//-----------------------------------------------
							//foreach ( $_SESSION['session_cart'] as $value ) {			
							for ($i=0; $i<count($_SESSION['session_cart']); $i++ ) {

								$value 		= $_SESSION['session_cart'][$i][0];		//the product record id
								$quantity 	= $_SESSION['session_cart'][$i][1];		//the product item quantity

								$items = getproductdetails($value);				
								$item_amount = $items[0]['mc_gross']*$_SESSION['session_cart'][$i][1];		//cost of item with quantity
								$total_price  += $item_amount;			

								//echo("total price : $total_price");
								//echo(" record id : $value");
								//echo(" quantity  : $quantity");
								//exit;

								?>
									<tr bgcolor= "#EFEFEF">
										<td width="10">&nbsp;</td>
										<td><?php echo($items[0]['item_name']); ?></td>
										<td width="10">&nbsp;</td>
										<td align="center"><?php echo($quantity); ?></td>						
										<td width="10">&nbsp;</td>

										<td align="center"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=increase&amp;recid=<?php echo($value); ?>"><img src="assets/cart_add.gif" width="10" height="10" border="0" alt="increase by 1" /></a></td>
										<td width="10">&nbsp;</td>

										<td align="center"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=decrease&amp;recid=<?php echo($value); ?>"><img src="assets/cart_subtract.gif" width="10" height="10" border="0" alt="decrease by 1" /></a></td>
										<td width="10">&nbsp;</td>

										<td align="right"><?php echo($items[0]['currency']); ?>&nbsp;<?php echo( sprintf("%01.2f", $items[0]['mc_gross'] * $quantity ) ); ?></td>
										<td width="10">&nbsp;</td>
										<td align="center"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=remove&amp;recid=<?php echo($value); ?>"><img src="assets/trash_can.gif" width="23" height="23" border="0" alt="remove item from cart"></a></td>
										<td width="10">&nbsp;</td>						
									</tr>
								<?php

							}
							//-----------------------------------------------

							$_SESSION['session_total_price'] = $total_price;

							?>
							<tr>
								<td width="10">&nbsp;</td>
								<td>&nbsp;</td>

								<td width="10">&nbsp;</td>
								<td>&nbsp;</td>

								<td width="10">&nbsp;</td>
								<td>&nbsp;</td>

								<td width="10">&nbsp;</td>
								<td>&nbsp;</td>

								<td width="10">&nbsp;</td>
								<td align="right" bgcolor="#DCDBDB"><strong>Total <?php echo($items[0]['currency']); ?>&nbsp;<?php echo(sprintf("%01.2f", $total_price)); ?></strong></td>

								<td width="10">&nbsp;</td>	

								<td>&nbsp;</td>
								<td width="10">&nbsp;</td>	
							</tr>

							<tr>
								<td width="10">&nbsp;</td>
								<td>&nbsp;</td>

								<td width="10">&nbsp;</td>
								<td>&nbsp;</td>

								<td width="10">&nbsp;</td>
								<td>&nbsp;</td>

								<td width="10">&nbsp;</td>
								<td>&nbsp;</td>

								<td width="10">&nbsp;</td>
								<td align="right">Any shipping will be<br/>added at checkout</td>

								<td width="10">&nbsp;</td>	

								<td>&nbsp;</td>
								<td width="10">&nbsp;</td>	
							</tr>                        
                        
                        
                        
							<tr>
								<td colspan="13"></td>	
							</tr>

					<?php
					} else {
					?>						
						<tr>
							<td colspan="13"><p>The cart is empty</p></td>	
						</tr>
					<?php
					}
					?>

						<tr>
							<td colspan="13"><< <a href="<?php echo($from) ?>">Continue shopping</a></td>			
						</tr>
						<?php

						//--------------------------------------------------
						//Display checkout button for PayPal
						if (isset($_SESSION['session_cart'])) {
						?>
							<form action="ipn/process.php" method="post">	

								<tr>
									<td colspan="13">&nbsp;</td>	
								</tr>	

								<input type="hidden" name="cmd" value="_cart">
								<tr>
									<td colspan="3">&nbsp;</td>
									<td width="10">&nbsp;</td>
									<td>&nbsp;</td>

									<td width="10">&nbsp;</td>
									<td>&nbsp;</td>

									<td width="10">&nbsp;</td>
									<td>&nbsp;</td>

									<td colspan="4">
										<input type="image" src="assets/secure_checkout_paypal.gif" name="submit" value="Secure Checkout"  alt="Secure Checkout PayPal" />
									</td>
								</tr>


								

							</form>	
						<?php
						}
						//--------------------------------------------------	
						?>	

					</table>
				
				</div>
				<div class="col-md-2 col-lg-2">&nbsp;</div>					
         
			</div>                    
        
			<div class="row">
				&nbsp;
			</div>
		
		</div> <!-- shoppingproducts -->

		

	</div> <!-- End Container -->


<?php
}

?>
