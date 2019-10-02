<?php
	$count = 0;
	$rs9 = $query->result();
	//get personnel who made order
	
	$personnel_name = $this->inventory_management_model->get_order_creator($order_id);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Order Details</title>
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
                <th>Order Details for Order <?php echo $order_number;?><br/>
                	Order Made by <?php echo $personnel_name;?>
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
                <!--<th>Sub Store QTY</th>
                <th>In Store QTY</th><th>QTY Received</th>
                <th>Order Status</th>
                <th>QTY Given</th>
                <th></th>-->
            </thead>
                    
			<?php 
            //echo "current - ".$current_item."end - ".$end_item;
            foreach ($rs9 as $rs10) :
                $deduction_date = $last_visit = date('jS M Y H:i:s',strtotime($rs10->product_deductions_date));
                $product_deduction_id = $rs10->product_deductions_id;
                $category_name = $rs10->category_name;
                $product_deduction_pack_size = $rs10->product_deductions_pack_size;
                $product_deduction_quantity = $rs10->product_deductions_quantity;
                $product_id = $rs10->product_id;
                $parent_store_qty = $this->inventory_management_model->get_total_products($product_id);
                $quantity_requested = $rs10->quantity_requested;
                $quantity_received = $rs10->quantity_received;
                $quantity_given = $rs10->quantity_given;
                $store_name = $rs10->store_name;
                $product_name = $rs10->product_name;
                $product_deductions_status = $rs10->product_deductions_status;
                $store_id = $rs10->store_id;
				$unit_name = $rs10->unit_name;
				$product_code = $rs10->product_code;
                $sub_store_quantity = $this->inventory_management_model->get_store_inventory_quantity($store_id,$product_id);

					// calculate the current stoe
					if($product_deductions_status == 0)
					{
						$status = 'Not Awarded';
					}
					//create activated status display
					else if($product_deductions_status == 1)
					{
						$status = 'Awarded';
					}
					else if($product_deductions_status == 2)
					{
						$status = 'Received';
					}

					$count++;
				?>
			   <tr>
                <td><?php echo $count;?></td>
                <td><?php echo $category_name;?></td>
                <td><?php echo $product_code;?></td>
                <td><?php echo $product_name;?></td>
                <td><?php echo number_format($quantity_requested);?></td>
                <td><?php echo $unit_name;?></td>
                <!--<td><?php echo $parent_store_qty;?></td>
                <td><?php echo $quantity_requested;?></td>
                <td><?php echo $quantity_received;?></td>
                <td><?php echo $status;?></td>-->
            </tr>
            <?php endforeach;?>
        </table>
        </div>
		<a href="#" class="print-no-display" onClick ="$('#excel-export').tableExport({type:'excel',escape:'false'});">XLS</a>
     </body>
 </html>
