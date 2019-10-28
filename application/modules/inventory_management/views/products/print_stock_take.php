<?php
 $stock_take_rs = $this->products_model->stock_take_drugs();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $contacts['company_name'];?> | Stock Take</title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" media="all"/>
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css" media="all"/>
        <style type="text/css">
			.receipt_spacing{letter-spacing:0px; font-size: 9px;}
			.center-align{margin:0 auto; text-align:center;}
			
			.receipt_bottom_border{border-bottom: #888888 medium solid;}
			.row .col-md-12 table {
				border:solid #000 !important;
				border-width:1px 0 0 1px !important;
				font-size:9px;
			}
			.row .col-md-12 th, .row .col-md-12 td {
				border:solid #000 !important;
				border-width:0 1px 1px 0 !important;
			}
			.table thead > tr > th, .table tbody > tr > th, .table tfoot > tr > th, .table thead > tr > td, .table tbody > tr > td, .table tfoot > tr > td
			{
				 padding: 3px;
			}
			
			.row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 10px;}
			.title-img{float:left; padding-left:30px;}
			img.logo{max-height:70px; margin:0 auto;}
		</style>
    </head>
    <body class="receipt_spacing">
    	<div class="row">
        	<div class="col-xs-12">
            	<img src="<?php echo base_url().'assets/logo/'.$contacts['logo'];?>" alt="<?php echo $contacts['company_name'];?>" class="img-responsive logo"/>
            </div>
        </div>
    	<div class="row">
        	<div class="col-md-12 center-align receipt_bottom_border">
            	<strong>
                	<?php echo $contacts['company_name'];?><br/>
                    P.O. Box <?php echo $contacts['address'];?> <?php echo $contacts['post_code'];?>, <?php echo $contacts['city'];?><br/>
                    E-mail: <?php echo $contacts['email'];?>. Tel : <?php echo $contacts['phone'];?><br/>
                    <?php echo $contacts['location'];?>, <?php echo $contacts['building'];?>, <?php echo $contacts['floor'];?><br/>
                </strong>
            </div>
        </div>
        
      <div class="row receipt_bottom_border" >
        	<div class="col-md-12 center-align">
            	<h4><strong> STOCK TAKE </strong></h4>
                <h5><strong>DOC NO#  DT<?php echo date('Ymd');?></strong></h5>
            </div>
					
        </div>
        
    	<div class="row">
        	<div class="col-md-12">

        		<table class="table table-hover table-bordered ">
				 	<thead>
						<tr>
						  <th>#</th>						  
						  <th>Store Name</th>
						  <th>Product</th>
						  <th>Category</th>
        				  <th>Closing Stock</th>
        				  <th>Closing Stock Value</th>
						  <th>Opening Stock</th>
						  <th>Opening Stock Value</th>
						  <th>Stock Variance</th>	
						  <th>Value Stock</th>				
						</tr>
					 </thead>
				  	<tbody>
				  		<?php 
				  		$result = '';
				  		// var_dump($stock_take_rs->result());die();

				  		$total_opening_stock = 0;
						$total_change = 0;
						$total_store_quantity = 0;
						$total_change_two = 0;
						$total_balance = 0;
						$total_value_variance = 0;
				  		if($stock_take_rs->num_rows() > 0)
				  		{
				  			$count = 0;
				  			foreach ($stock_take_rs->result() as $key => $value) {
				  				# code...
				  				$product_name = $value->product_name;
				  				$opening_stock = $value->store_quantity;
				  				$store_name = $value->store_name;
				  				$category_name = $value->category_name;
				  				$product_id = $value->product_id;
				  				$owning_store_id = $value->owning_store_id;
				  				$regenerate_id = $value->regenerate_id;
				  				$product_unitprice = $value->product_unitprice;

				  				$closing_stock = $this->products_model->get_opening_stock($owning_store_id,$regenerate_id);
				  				$balance = $opening_stock - $closing_stock;

				  				$change = $closing_stock*$product_unitprice;
				  				$change_two = $opening_stock*$product_unitprice;
				  				$value_variance = $change_two - $change;

				  				$total_opening_stock += $closing_stock;
				  				$total_change += $change;
				  				$total_store_quantity += $opening_stock;
				  				$total_change_two += $change_two;
				  				$total_balance += $balance;
				  				$total_value_variance += $value_variance;

				  				if($opening_stock < $closing_stock)
				  				{
				  					$highlight = 'danger';
				  				}
				  				else if($opening_stock > $closing_stock)
				  				{
				  					$highlight = 'primary';
				  				}
				  				else
				  				{
				  					$highlight = 'default';
				  				}
				  				$count++;


				  				$result .= '
				  							<tr class="'.$highlight.'">
				  								<td>'.$count.'</td>
				  								<td>'.$store_name.'</td>
				  								<td>'.$product_name.'</td>
				  								<td>'.$category_name.'</td>
				  								<td>'.$closing_stock.'</td>
				  								<td>'.$change.'</td>
				  								<td>'.$opening_stock.'</td>
				  								<td>'.$change_two.'</td>
				  								<td>'.$balance.'</td>
				  								<td>'.$value_variance.'</td>
				  							</tr>
				  						  ';
				  			}
				  			$result .= '
				  							<tr>
				  								
				  								<th colspan="4">Totals</th>
				  								<th>'.$total_opening_stock.'</th>
				  								<th>'.$total_change.'</th>
				  								<th>'.$total_store_quantity.'</th>
				  								<th>'.$total_change_two.'</th>
				  								<th>'.$total_balance.'</th>
				  								<th>'.$total_value_variance.'</th>
				  							</tr>
				  						  ';
				  		}
				  		echo $result;
				  		?>
					</tbody>
				</table>
            </div>

        </div>
     
    </body>
    
</html>