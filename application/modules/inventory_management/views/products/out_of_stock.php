<?php 


$parent_store = 0;
//check for parent store
if($store_priviledges->num_rows() > 0)
{
	foreach($store_priviledges->result() as $res)
	{
		$store_parent = $res->store_parent;
		
		if($store_parent == 0)
		{
			$parent_store = 1;
			break;
		}
	}
}
if($branches->num_rows() > 0)
{
	$row = $branches->result();
	$branch_id = $row[0]->branch_id;
	$branch_name = $row[0]->branch_name;
	$branch_image_name = $row[0]->branch_image_name;
	$branch_address = $row[0]->branch_address;
	$branch_post_code = $row[0]->branch_post_code;
	$branch_city = $row[0]->branch_city;
	$branch_phone = $row[0]->branch_phone;
	$branch_email = $row[0]->branch_email;
	$branch_location = $row[0]->branch_location;

	$data['branch_name'] = $branch_name;
	$data['branch_image_name'] = $branch_image_name;
	$data['branch_id'] = $branch_id;
	$data['branch_address'] = $branch_address;
	$data['branch_post_code'] = $branch_post_code;
	$data['branch_city'] = $branch_city;
	$data['branch_phone'] = $branch_phone;
	$data['branch_email'] = $branch_email;
	$data['branch_location'] = $branch_location;
}

//echo $this->load->view('search_products', $v_data, TRUE); 

	//get personnel approval rightts
	$personnel_id = $this->session->userdata('personnel_id');
	$approval_id = $this->inventory_management_model->get_approval_id($personnel_id);

?>
<div class="row">
    <div class="col-md-12">
		<section class="panel panel-featured panel-featured-info">
		    <header class="panel-heading">
		        <h2 class="panel-title pull-left"><?php echo $title;?></h2>
		         <div class="widget-icons pull-right">
                 	<?php
						if($approval_id == 2)
						{
							?>
                            <a href="<?php echo base_url();?>inventory/manage-orders" class="btn btn-info btn-sm fa fa-plus"> Manage Orders</a>
                            <?php
						}
					?>
                   <?php 
			         if(($type == 1) || ($type == 3))
			         {
			         	?>
			         	 
			         	<?php
			         }
			         if(($type == 2) || ($type == 3))
			         {
			         	?>
			         	
			         	<?php
			         }
			         ?>
		          </div>
		          <div class="clearfix"></div>
		    </header>
		    <div class="panel-body">
				<?php

						$error = $this->session->userdata('error_message');
						$success = $this->session->userdata('success_message');
						$search_result ='';
						$search_result2  ='';
						if(!empty($error))
						{
							$search_result2 = '<div class="alert alert-danger">'.$error.'</div>';
							$this->session->unset_userdata('error_message');
						}
						
						if(!empty($success))
						{
							$search_result2 ='<div class="alert alert-success">'.$success.'</div>';
							$this->session->unset_userdata('success_message');
						}
								
						$search = $this->session->userdata('product_inventory_search');
						
						if(!empty($search))
						{
							$search_result = '<a href="'.site_url().'inventory/close-product-search" class="btn btn-success btn-sm">Close Search</a>';
						}

						$result = '';	
						$result .= ''.$search_result2.'';
						$result .= '
								';

						
						
						//if users exist display them
						if ($query->num_rows() > 0)
						{
							$count = 0;

							if($type == 1)
							{
								$cols_items ='';
								$colspan = 2;
							}
							else
							{
								$cols_items = 
										'
										<!--<th class="table-sortable:default table-sortable" title="Click to sort">Unit Price</th>
		                                <th class="table-sortable:default table-sortable" title="Click to sort">33% MU</th>-->
		                                <th class="table-sortable:default table-sortable" title="Click to sort">Opening</th>
		                                <th class="table-sortable:default table-sortable" title="Click to sort">P</th>
		                                <th class="table-sortable:default table-sortable" title="Click to sort">S</th>
		                                <th class="table-sortable:default table-sortable" title="Click to sort">D</th>
		                                ';
		                         $colspan = 6;
							}
							$result .= 
							'
							<div class="row">
							<div class="col-md-12">
								<table class="example table-autosort:0 table-stripeclass:alternate table table-hover table-bordered " id="TABLE_2">
								 
								  <thead> 
		                                <th>#</th>
		                                <th class="table-sortable:default table-sortable" title="Click to sort">Code</th>
		                                <th class="table-sortable:default table-sortable" title="Click to sort">Store</th>
		                                <th class="table-sortable:default table-sortable" title="Click to sort">Name</th>
		                                <th class="table-sortable:default table-sortable" title="Click to sort">Category</th>
		                                '.$cols_items.'
		                                <th class="table-sortable:default table-sortable" title="Click to sort">Stock</th>
		                                <th>Status</th>
		                                
		                            </thead>
								  <tbody>
							';
							
							//get all administrators
							$personnel_query = $this->personnel_model->get_all_personnel();
							
							foreach ($query->result() as $row)
							{
								$product_id = $row->product_id;

								$product_name = $row->product_name;
								$product_code = $row->product_code;
								$product_status = $row->product_status;
								$product_description = $row->product_description;
								$store_name = $row->store_name;
								$category_id = $row->category_id;
								$created = $row->created;
								$created_by = $row->created_by;
								$last_modified = $row->last_modified;
								$modified_by = $row->modified_by;
								$category_name = $row->category_name;
								$store_id = $row->store_id;
								$quantity = $row->quantity;
								$reorder_level = $row->reorder_level;

								$product_unitprice = $row->product_unitprice;
		                        
		                        $product_deleted = $row->product_deleted;
		                        $in_service_charge_status = $row->in_service_charge_status;

								
								//status
								if($product_status == 1)
								{
									$status = 'Active';
								}
								else
								{
									$status = 'Disabled';
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

								/*if($product_deleted == 0)
								{
									if($parent_store == 1)
									{
										$button .= '<a href="'.site_url().'/inventory/activation/deactivate/'.$page.'/'.$product_id.'" class="btn btn-sm btn-default" onclick="return confirm(\'Do you want to disable '.$product_name.'?\');">Disable</a>';
									}
								}
								
								else
								{
									if($parent_store == 1)
									{
										$button .= '<a href="'.site_url().'/inventory/activation/activate/'.$page.'/'.$product_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you want to enable '.$product_name.'?\');">Enable</a>';
									}
								}*/
					
								//creators & editors
								if($personnel_query->num_rows() > 0)
								{
									$personnel_result = $personnel_query->result();
									
									foreach($personnel_result as $adm)
									{
										$personnel_id2 = $adm->personnel_id;
										
										if($created_by == $personnel_id2)
										{
											$created_by = $adm->personnel_fname;
											break;
										}
										
										else
										{
											$created_by = '-';
										}
									}
								}
								
								else
								{
									$created_by = '-';
								}

								if($type == 1)
								{
									$in_stock = $quantity;
									$other_items ='';
									$button_two = '';

								}
								else
								{
									$markup = round(($product_unitprice * 1.33), 0);
									$markdown = $markup;//round(($markup * 0.9), 0);


									$purchases = $this->inventory_management_model->item_purchases($product_id,$store_id);
			                       
			                        $deductions = $this->inventory_management_model->item_deductions($product_id,$store_id);
			                   


			                        if($in_service_charge_status == 1)
			                        {
			                        	 $sales = $this->inventory_management_model->get_drug_units_sold('2017-10-01',$product_id,$type);
			                        }
			                        else
			                        {
			                        	$sales =0;
			                        }

								// var_dump($product_id); die();
									$sales = $this->inventory_management_model->get_drug_units_sold('2017-10-01',$product_id);
			                        $in_stock = ($quantity + $purchases) - $sales - $deductions;
			                        

									$other_items = 
										'
										<!--<td>'.number_format($product_unitprice, 2).'</td>
		                                <td>'.number_format($markup, 2).'</td>-->				         
		                                <td>'.$quantity.'</td>						         
		                                <td>'.$purchases.'</td>
		                                <td>'.$sales.'</td>						         
		                                <td>'.$deductions.'</td>

										';
										if($parent_store == 1)
										{
											$button_two = ' <td><a href="'.site_url().'inventory/edit-product/'.$product_id.'" class="btn btn-sm btn-primary">Edit</a></td>
					                                <td><a href="'.site_url().'inventory/product-purchases/'.$product_id.'" class="btn btn-sm btn-warning">Purchases</a></td>
					                              ';
										}
										else
										{
											$button_two = '';
										}
								}

								
								

								$count++;
								if($in_stock<=$reorder_level){
			                        	$result .= 
								'
									<tr>
										<td>'.$count.'</td>
										<td>'.$product_code.'</td>
										<td>'.$store_name.'</td>
										<td>'.$product_name.'</td>
										<td>'.$category_name.'</td>		         
		                                '.$other_items.'
		                                <td>'.$in_stock.'</td>
		                                <td>'.$status.'</td>
		                               
									</tr> 
								';
			                        }else{
			                        	$class = "";

			                        }
								
								
								
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
						//echo $result;
				?>
				<div class="widget-foot">
			    <?php
			    if(isset($links)){echo $links;}
			    ?>
			    </div>
			
		</section>
	</div>
</div>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Products Out Of Stock</title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/bootstrap/css/";?>bootstrap.css" />
        <script type="text/javascript" src="<?php echo base_url()."assets/themes/jquery/";?>jquery-2.1.1.min.js"></script>
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
			.align-right{margin:0 auto; text-align: right !important;}
        </style>
    </head>
    <body class="receipt_spacing" onLoad="window.print();return false;">
    	
    <!--<body class="receipt_spacing">
    	<div class="row" >
        	<img src="<?php echo base_url().'assets/logo/'.$branch_image_name;?>" alt="<?php echo $branch_name;?>" class="img-responsive logo"/>
        	<div class="col-md-12 center-align receipt_bottom_border">
            	<strong>
                	<?php echo $branch_name;?><br/>
                    <?php echo $branch_address;?> <?php echo $branch_post_code;?> <?php echo $branch_city;?><br/>
                    E-mail: <?php echo $branch_email;?>. Tel : <?php echo $branch_phone;?><br/>
                    <?php echo $branch_location;?><br/>
                </strong>
            </div>
        </div>
        
      	<div class="row receipt_bottom_border" >
        	<div class="col-md-12 center-align">
            	<h4><?php echo '<h3>Payroll for The month of '.date('M Y',strtotime($year.'-'.$month)).'</h3>';?></h4>
            </div>
        </div>-->
        
        <div class="row receipt_bottom_border" >
        	<div class="col-md-12">
            	<?php echo $result;?>
            </div>
            <div class="col-md-12">
            	<table class="table table-condensed">
                    <tr>
                        <th class="align-right">
                            <?php echo $branch_name;?> | <?php echo $branch_location;?> | <?php echo $branch_city;?><br/>
							Tel : <?php echo $branch_phone;?> <!--<?php echo $branch_address;?> <?php echo $branch_post_code;?> | E-mail: <?php echo $branch_email;?>-->
                        </th>
                    </tr>
                </table>
            </div>
        	<div class="col-md-12 center-align">
            	
            	  </div>
        </div>
		<a href="#" onClick ="$('#customers').tableExport({type:'excel',escape:'false'});">XLS</a>
<!--<a href="#" onClick ="$('#customers').tableExport({type:'csv',escape:'false'});">CSV</a>
<a href="#" onClick ="$('#customers').tableExport({type:'pdf',escape:'false'});">PDF</a>-->

    </body>
</html>