<?php

$result = '';
$result = '';
$result .= '
		';

//if users exist display them
if ($query->num_rows() > 0)
{
	$count = $page;
	
	$result .= 
	'
	<div class="row">
	<div class="col-md-12 table-responsive">
		<table class="table table-bordered">
		 
		  <thead> 
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Category</th>
                <th>VAT status</th>
                <th>Main Store</th>
                <th>Pharmacy</th>
                <th>Total</th>
                <th>Selling Price</th>
            </thead>
		  <tbody>
	';
	
	//get all administrators
	$personnel_query = $this->personnel_model->get_all_personnel();
	
	foreach ($query->result() as $row)
	{//var_dump($query);die();

		$product_id = $row->product_id;
		$product_name = $row->product_name;
		$product_code = $row->product_code;
		$product_status = $row->product_status;
		$product_description = $row->product_description;
		$category_id = $row->category_id;
		$created = $row->created;
		$created_by = $row->created_by;
		$last_modified = $row->last_modified;
		$modified_by = $row->modified_by;
		$category_name = $row->category_name;
		$store_id = $row->store_id;
		$reorder_level = $row->reorder_level;
		$parent_store = $row->store_id;
		$vatable = $row->vatable;
		

		

		$product_unitprice = $row->product_unitprice;
        
        $product_deleted = $row->product_deleted;

		
		//status
		if($product_status == 1)
		{
			$status = 'Active';
		}
		else
		{
			$status = 'Disabled';
		}


		if($vatable == 1)
		{
			$vatable_status = 'Yes';
			$status_value ='<div class="col-lg-3">
                                <div class="radio">
                                    <label>
                                        <input id="optionsRadios1" type="radio" checked value="1" name="vatable">
                                        Yes
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="radio">
                                    <label>
                                        <input id="optionsRadios2" type="radio" value="0" name="vatable">
                                        No
                                    </label>
                                </div>
                            </div>';
		}
		else
		{
			$vatable_status = 'No';
			$status_value ='<div class="col-lg-3">
                                <div class="radio">
                                    <label>
                                        <input id="optionsRadios1" type="radio"  value="1" name="vatable">
                                        Yes
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="radio">
                                    <label>
                                        <input id="optionsRadios2" type="radio" checked value="0" name="vatable">
                                        No
                                    </label>
                                </div>
                            </div>';
		}


		
		$button = '';
		//create deactivated status display
		if($product_status == 0)
		{
			$status = '<span class="label label-danger">Deactivated</span>';
			if($parent_store == 1)
			{
				$button .= '<a class="btn btn-info btn-sm" href="'.site_url().'inventory/activate-product/'.$product_id.'" onclick="return confirm(\'Do you want to activate '.$product_name.'?\');">Activate</a>';
			}
		}
		//create activated status display
		else if($product_status == 1)
		{
			$status = '<span class="label label-success">Active</span>';
			if($parent_store == 1)
			{
				$button .= '<a class="btn btn-default btn-sm" href="'.site_url().'inventory/deactivate-product/'.$product_id.'" onclick="return confirm(\'Do you want to deactivate '.$product_name.'?\');">Deactivate</a>';
			}
		}

		

		
				
            
        $child = 0;
        $parent = 0;
       
    	$store_id = 5;
    	$purchases = $this->inventory_management_model->product_purchases($inventory_start_date, $product_id,5);
    	$transfers = $this->inventory_management_model->product_transfers($inventory_start_date, $product_id,5); 
    	// deduction 1
    	$store_deductions = $this->inventory_management_model->product_deducted($inventory_start_date, $product_id,5); 
    	// deduction 2
    	$s11 = $this->inventory_management_model->product_disbersed($inventory_start_date, $product_id,null); 
    	//deduction 3
    	$total_store_deductions =  $transfers + $s11 + $store_deductions;
    	$sales = $transfers;
    	$deductions = $s11 + $store_deductions;
    	$parent_opening = $this->inventory_management_model->get_store_opening_quantity($product_id,5);
    	$parent_stock = ($parent_opening + $purchases) - $total_store_deductions;

    	// var_dump($quantity); die();

  
    	$child_s11 = $this->inventory_management_model->product_disbersed($inventory_start_date, $product_id,6); ;
    	$sales = $this->inventory_management_model->get_drug_units_sold($inventory_start_date, $product_id,null,null, $branch_code=NULL);
    	$store_requests = $purchases = $this->inventory_management_model->product_added($inventory_start_date, $product_id,6); 
    	// var_dump($sales); die();
    	$purchases = $store_requests + $child_s11;
    	$opening_stock = $this->inventory_management_model->get_store_opening_quantity($product_id,6);
    	$child_stock = ($opening_stock + $purchases) - $sales;
       
		$total_stock = $child_stock+$parent_stock;

		$count++;
		$result.= form_open("update-stock-pricing/".$product_id, array("class" => "form-horizontal"));
		$result .= 
		'
			<tr >
				<td>'.$count.'</td>
				<td>'.$product_code.'</td>
				<td>'.$product_name.'</td>
				<td>'.$category_name.'</td>											
				<td>'.$vatable_status.'</td>
				<td>'.$parent_stock.'</td>
				<td>'.$child_stock.'</td>
				<td>'.$total_stock.'</td>
				<td>'.number_format($product_unitprice,2).'</td>               
			</tr> 
		';
		 $result .= form_close();
		
	}
	
	$result .= 
	'
				  </tbody>
				</table>
				</div>
			</div>
	';
}

else
{
	$result .= '';
}

$result .= '</div>';
						
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $title;?></title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
		<link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" />
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
    <body class="receipt_spacing">
    	
       <div class="row receipt_bottom_border">
            <div class="col-md-12">
                <section class="panel panel-featured panel-featured-info">
                    <header class="panel-heading">
                         <h2 class="panel-title"><?php echo $title;?></h2>
                    </header>             
                    
                    <!-- Widget content -->
                    <div class="panel-body"  onLoad="window.print();return false;">
                        <?php echo $result;?>
                    </div>
					<a href="#" onClick ="$('#customers').tableExport({type:'excel',escape:'false'});"> EXCEL DOWNLOAD</a>

                    
                </section>
            </div>
        </div>

    </body>
</html>