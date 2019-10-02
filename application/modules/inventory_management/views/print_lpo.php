<?php
	$count = 0;
	$rs9 = $query->result();
	
	if($lpo_query->num_rows() > 0)
	{
		foreach($lpo_query->result() as $lpos_query)
		{
			$vendor_no = $lpos_query->No_;
			$personnel_first_name = $lpos_query->personnel_fname;
			$personnel_surname = $lpos_query->personnel_onames;
			$personnel_name = $personnel_first_name.' '.$personnel_surname;
			$lpo_id = $lpos_query->lpo_id;
			$lpo_number = $lpos_query->lpo_number;
			$nav_supplier_id = $lpos_query->nav_supplier_id;
			$nav_supplier = $lpos_query->Search_Name;
			$vendor_address = $lpos_query->Address;
			$vendor_pin = $lpos_query->Pin_No_;
			$lpo_date = $lpos_query->lpo_date;
			$lpo_status = $lpos_query->lpo_status_id;
			$status = $this->inventory_management_model->get_lpo_status($lpo_status);
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>LPO Details</title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css">
		<script type="text/javascript" src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery/jquery.js"></script>
		<script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>tableExport.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>jquery.base64.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>html2canvas.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>libs/sprintf.js"></script>
		<script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>jspdf.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>libs/base64.js"></script>
		<style type="text/css">
            .receipt_spacing{letter-spacing:0px; font-size: 12px;}
            .center-align{margin:0 auto; text-align:center;}
            
            .receipt_bottom_border{border-bottom: #888888 medium solid;}
            .row .col-md-12 table {
                border:solid #000 !important;
                border-width:1px 0 0 1px !important;
                font-size:10px;
            }
            .row .col-md-12 th, .row .col-md-12 td {
                border:solid #000 !important;
                border-width:0 1px 1px 0 !important;
            }
            
            .row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 20px;}
            .title-img{float:left; padding-left:30px;}
            img.logo{max-height:70px; margin:0 auto;}
            .table {margin-bottom: 0;}
			@media print
			{
				#page-break
				{
					page-break-after: always;
					page-break-inside : avoid;
				}
				.print-no-display
				{
					display: none !important;
				}
			}
        </style>
    </head>
    <body class="receipt_spacing" onLoad="window.print();return false;">        
        <!-- Widget content -->
        <div id="excel-export">
        <table class="table table-condensed">
            <tr>
                <th>Local Purchase Order <?php echo $lpo_number;?><br/>
                	Order Made by <?php echo $personnel_name;?><br/><br/><br/>
                	Buy-from Vendor No <?php echo $vendor_no;?><br/>
                	<?php echo $nav_supplier;?><br/>
                	<?php echo $vendor_address;?><br/>
                	<?php echo $vendor_pin;?>
                </th>
                <th class="align-right">
                    <?php echo $branch_name;?><br/>
                    <?php echo $branch_address;?> <?php echo $branch_post_code;?> <?php echo $branch_city;?><br/>
                    E-mail: <?php echo $branch_email;?><br/>
                    Tel : <?php echo $branch_phone;?><br/>
                    <?php echo $branch_location;?>
                </th>
                <th>
                    <img src="<?php echo base_url().'assets/logo/'.$branch_image_name;?>" alt="<?php echo $branch_name;?>" class="img-responsive logo"/>
                </th>
            </tr>
        </table>
        <table border="0" class="table table-hover table-condensed">
            <thead> 
                <th>#</th>
                <th>Category</th>
                <th>Product Code</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Units</th>
                <th>Price</th>
                <th>Total</th>
                <!--<th>Sub Store QTY</th>
                <th>In Store QTY</th><th>QTY Received</th>
                <th>Order Status</th>
                <th>QTY Given</th>
                <th></th>-->
            </thead>
                    
			<?php 
			$total_price = 0;
            foreach ($rs9 as $rs10) :
                $product_deduction_id = $rs10->lpo_item_id;
                $category_name = $rs10->category_name;
                $product_id = $rs10->product_id;
                $quantity_requested = $rs10->quantity_requested;
                $product_name = $rs10->product_name;
				$unit_name = $rs10->unit_name;
				$product_code = $rs10->product_code;
				$lpo_item_price = $rs10->lpo_item_price;
				$total = $lpo_item_price * $quantity_requested;
				$total_price += $total;

				$count++;
				?>
			   <tr>
                <td><?php echo $count;?></td>
                <td><?php echo $category_name;?></td>
                <td><?php echo $product_code;?></td>
                <td><?php echo $product_name;?></td>
                <td><?php echo number_format($quantity_requested);?></td>
                <td><?php echo $unit_name;?></td>
                <td><?php echo number_format($lpo_item_price);?></td>
                <td><?php echo number_format($total);?></td>
            </tr>
            <?php endforeach;?>
			<tr>
				<th colspan="7">Total</th>
				<th><?php echo number_format($total_price);?></th>
			</tr>
        </table>
        </div>
		<a href="#" class="print-no-display" onClick ="$('#excel-export').tableExport({type:'excel',escape:'false'});">XLS</a>
     </body>
 </html>
