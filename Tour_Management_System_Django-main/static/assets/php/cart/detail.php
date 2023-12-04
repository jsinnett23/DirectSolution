<?php session_start();

//----------------------------------------------
require_once("includes/header.php");
require_once("includes/error.php");
require_once("dbclass/Dbpdo.class.php");
require_once("functions/misc.php");
require_once("functions/cart.php");
require_once("classes/clsinput.php");
//----------------------------------------------

$submitRecid = input::get('recid');
if ( ! is_numeric( $submitRecid ) ) { exit(); }

$productdetails = getproductdetails($submitRecid);
if ( count($productdetails) == 0 ) { exit(); }

$recid = $submitRecid;

//----------------------------------------------
$submitAction	= input::get('action');
//$_SESSION['from'] = $_SERVER['PHP_SELF'];
//$submitFromURL	= input::get('from');

$submitFromURL = "";
if ($submitAction == "add") {
	if ( checkrecid($submitRecid) != 0 ) {
		$physical = 1;	//is always physical in this case
		add($submitRecid, $submitFromURL, $physical);
	}
}

//-----------------------------------------
//Find number of items in the cart
if ( isset($_SESSION['session_cart']) ){
	$numberincart = count($_SESSION['session_cart']);
}else{
	$numberincart = 0;
}

require_once("includes/menu.php");
?>

<div class="container">

      <div class="shoppingproducts">

            <div class="row">
            	<div class="col-md-12">
					<p>
						<span class="basket-setting"><?php echo($numberincart); ?><a href="viewcart.php">&nbsp;Basket</a>&nbsp;<i class="fa fa-shopping-basket fa-1x" aria-hidden="true"></i></span>
						<br/>
					</p>
				</div>
			</div>			

			<div class="row">
				<div class="col-md-2">&nbsp;</div>
            	<div class="col-md-6"><img src="assets/shopping_cart.png" alt="Shopping cart image" class="img-fluid" /></div>
				<div class="col-md-4">&nbsp;</div>
			</div>
			<div class="row">
				<div class="col-md-2">&nbsp;</div>
				<div class="col-md-10"><hr></div>	
			</div>

				<div class="row">
        	
					<?php
					foreach( $productdetails as $d_row ) {	
					?>
				      
						<div class="col-md-2">&nbsp;</div>
  					
   						<div class="col-md-6">
   					
   							<h2><?php echo( $d_row["item_title"] ); ?></h2>
   					
   							<div style="float:left;margin-right:10px;margin-bottom:10px;">
   							<?php
								if ($d_row["item_image"] != "") {
									?>
										<img src="<?php echo( $d_row["item_image"] ); ?>" 
												class="img-fluid img-thumbnail rounded" 
												style="max-width:100%; height:auto; padding-top:15px;border: none;"
												alt="<?php echo( $d_row["item_title"] ); ?>" />
									<?php
								}
							?>
							</div>
				
							<p><?php echo( $d_row["item_description"] ); ?></p>
						
							<?php
								if ($d_row["item_description_full"] != "") {
									?><p><?php echo( $d_row["item_description_full"] ); ?></p><?php
								}
							?>		
						
							<p class="cost"><?php echo( $d_row["currency"] ); ?>&nbsp;<?php echo( $d_row["mc_gross"] ); ?></p>
						
							<p>
								<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
									<input type="hidden" name="recid" value="<?php echo($recid); ?>" />
									<input type="hidden" name="action" value="add" />
									<input type="hidden" name="from" value="<?php echo ($_SERVER['PHP_SELF']); ?>" />
									<button type="submit" class="btn btn-info">Add to cart</button>
								</form>
							</p>
							
						</div>
					
    					<div class="col-md-4">
    					
							<h4>Most Popular</h4>
   					
    					</div>
    	
					<?php
					}
					?>         	
         
				</div>        


      </div> <!-- End shoppingproducts -->

      <?php
        require_once("includes/copyright.php");
      ?>

</div> <!-- End container -->

<?php
require_once("includes/footer.php");
?>
