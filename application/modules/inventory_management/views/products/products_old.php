<?php 
$v_data['all_categories'] = $all_categories;
$v_data['all_brands'] = $all_brands;
$v_data['all_generics'] = $all_generics;
$v_data['store_priviledges'] = $store_priviledges;

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

echo $this->load->view('search_products', $v_data, TRUE); 

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
                 	<a href="<?php echo base_url();?>inventory/manage-store" class="btn btn-default btn-sm fa fa-plus"> Manage Store</a>
			        <?php 
			         if(($type == 1) || ($type == 3))
			         {
			         	?>
			         	 
			         	<?php
			         }
			         $personnel_id = $this->session->userdata('personnel_id');
			         $department_id = $this->reception_model->get_personnel_department($personnel_id);
			         if(($type == 2) || ($type == 3) || $personnel_id == 0 || $department_id == 1)
			         {
			         	?>
			         	<a href="<?php echo base_url();?>inventory/import-products" class="btn btn-success btn-sm" style="margin-left:10px;">Import Product</a>
						<!--<a href="<?php echo base_url();?>inventory/export-products" class="btn btn-info btn-sm" style="margin-left:10px;">Export Product</a>-->
                        <a href="<?php echo base_url();?>inventory/import-balances" class="btn btn-info btn-sm" style="margin-left:10px;">Import Opening Balances</a>
						<a href="<?php echo base_url();?>inventory/product-deductions" class="btn btn-warning btn-sm fa fa-minus"> Manage Requests</a>
						<a href="<?php echo base_url();?>inventory/add-product" class="btn btn-success btn-sm fa fa-plus"> Add New Product</a>
                        <a href="<?php echo base_url();?>inventory_management/update_initial_product_balance" class="btn btn-default btn-sm fa fa-plus"> Update Stock Balances</a>
                        <a href="<?php echo base_url();?>inventory/print-product-out-stock" class="btn btn-danger btn-sm fa fa-print"> Out Of Stock</a>
						
			         	<?php
			         }
					 ?>
                     <a href="<?php echo base_url();?>inventory/download-all-stock" target="_blank" class="btn btn-default btn-sm fa fa-print"> Download All</a>
                     
                     <!-- <?php
					 if($store_priviledges->num_rows() > 0)
					 {
						 foreach($store_priviledges->result() as $res)
						 {
							 $store_name = $res->store_name;
							 $store_id = $res->store_id;
							 ?> -->
                             <!-- <a href="<?php echo base_url();?>inventory/print-product-in-store/<?php echo $store_id;?>" class="btn btn-default btn-sm fa fa-print" target="_blank">Download <?php echo $store_name;?></a> -->
                           <!--   <?php
						 }
					 }
		
			         ?> -->
                     
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
						
						$inventory_search_start_date = $this->session->userdata('inventory_search_start_date');
						if(!empty($inventory_search_start_date))
						{
							$search_start_date = $inventory_search_start_date;
							if($search_start_date == '')
							{
								$search_start_date = NULL;
							}
						}
						else
						{
							$search_start_date = NULL;
						}
						
						$inventory_search_end_date = $this->session->userdata('inventory_search_end_date');
						if(!empty($inventory_search_start_date))
						{
							$search_end_date = $inventory_search_end_date;
							if($search_end_date == '')
							{
								$search_end_date = NULL;
							}
						}
						else
						{
							$search_end_date = NULL;
						}
						
						
						$result = '';	
						$result .= ''.$search_result2.'';
						$result .= '
								';

						//if users exist display them
						if ($query->num_rows() > 0)
						{
							$count = $page;

							if($type == 1)
							{
								$cols_items ='
		                                <th>Opening</th>
		                                <th>T</th>
		                                <th>S</th>
		                                <th>R</th>';
								$colspan = 2;
							}
							else
							{
								$cols_items = 
										'
										<!--<th>Unit Price</th>
		                                <th>33% MU</th>-->
		                                <th>Opening</th>
		                                <th>P</th>
		                                <th>S</th>
		                                <th>D</th>
		                                ';
		                         $colspan = 6;
							}
							$result .= 
							'
							<div class="row">
							<div class="col-md-12 table-responsive">
								<table class="table table-bordered">
								 
								  <thead> 
		                                <th>#</th>
		                                <th>Code</th>
		                                <th>Store</th>
		                                <th>Name</th>
		                                <th>Category</th>
		                                <th>Vatable</th>
		                                '.$cols_items.'
		                                <th>Stock</th>
		                                <th>Status</th>
		                                <th colspan="'.$colspan.'">Actions</th>
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
								$store_name = $row->store_name;
								$category_id = $row->category_id;
								$created = $row->created;
								$created_by = $row->created_by;
								$last_modified = $row->last_modified;
								$modified_by = $row->modified_by;
								$category_name = $row->category_name;
								$store_id = $row->store_id;
								$reorder_level = $row->reorder_level;
								$parent_store = $row->store_parent;
								$vatable = $row->vatable;
								if($parent_store == 0)
								{
									$quantity = $row->quantity;
								}
								else
								{
									$quantity = 0;
								}

								$quantity = $row->store_quantity;
								
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


								if($vatable == 1)
								{
									$vatable_status = 'Yes';
								}
								else
								{
									$vatable_status = 'No';
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

								// if($type == 1)
								// {
								// 	$in_stock = $quantity;
								// 	$other_items ='';
								// 	$button_two = '';

								// }
								// else
								// {
									$markup = round(($product_unitprice * 1.33), 0);
									$markdown = $markup;//round(($markup * 0.9), 0);

									if($parent_store == 0)
									{
										$purchases = $this->inventory_management_model->item_purchases($inventory_start_date, $product_id,$store_id,$search_start_date,$search_end_date);
			                       
			                        	$deductions = $this->inventory_management_model->parent_item_deductions($inventory_start_date, $product_id,$store_id,$search_start_date,$search_end_date);
			                        	$other_deductions = $this->inventory_management_model->stock_item_deductions($inventory_start_date, $product_id,$store_id,$search_start_date,$search_end_date);
				
									}
									else
									{
										$purchases = $this->inventory_management_model->child_item_purchases($inventory_start_date, $product_id,$store_id,$search_start_date,$search_end_date);
										$other_deductions = $this->inventory_management_model->stock_item_deductions($inventory_start_date, $product_id,$store_id,$search_start_date,$search_end_date);
										$deductions = 0;

									}
									// var_dump($store_name);die();
			                        if(($in_service_charge_status == 1)&&(($store_name == "Pharmacy") || ($store_name == "Lab Store")))
			                        {
			                        	 $sales = $this->inventory_management_model->get_drug_units_sold($inventory_start_date, $product_id,$search_start_date,$search_end_date);
			                        }

			                        elseif($store_name == "Pharmaceutical Store")
			                        {
										//get all children that belong to that store
										$children_stores = $this->inventory_management_model->get_all_child_stores($store_id);
										$sales = 0;
										//get all child store reuests
										if($children_stores->num_rows() > 0)
										{
											foreach($children_stores->result() as $child_stores)
											{
												$child_store_id = $child_stores->store_id;
												
												$child_requests = $this->inventory_management_model->get_requests($child_store_id);												//var_dump($child_requests);die();
												if($child_requests->num_rows() > 0)
												{
													foreach($child_requests->result() as $requests)
													{
														$orders_id = $requests->order_id;
														$child_requests_sales = $this->inventory_management_model->get_child_sales($inventory_start_date, $orders_id,$product_id,$search_start_date,$search_end_date);	
														//var_dump($child_requests_sales);die();
														$sales +=	$child_requests_sales;
													}
												}
											}
										}
			                        }
									else
									{
										$sales =0;
										
									}

									// var_dump($sales);die();
									if($store_name == "Pharmacy")
									{
										$branch_code = "OSE";
										$sales2 = $sales1 = 0; 
										//$sales = $this->inventory_management_model->get_drug_units_sold($inventory_start_date, $product_id,$search_start_date,$search_end_date, $branch_code);
										$sales1 = $this->inventory_management_model->get_pharmacy_drug_units_sold($inventory_start_date, $product_id,$search_start_date,$search_end_date, $branch_code);
										//for all stores whose parent is pharmacy
										$pharmacy_children = $this->inventory_management_model->get_all_child_stores($store_id);
										if($pharmacy_children->num_rows() > 0)
										{
											foreach($pharmacy_children->result() as $child_stores)
											{
												$pharmacy_child_store_id = $child_stores->store_id;
												
												$pharmacy_child_requests = $this->inventory_management_model->get_requests($pharmacy_child_store_id);												//var_dump($child_requests);die();
												if($pharmacy_child_requests->num_rows() > 0)
												{
													foreach($pharmacy_child_requests->result() as $pharm_requests)
													{
														$orders_id = $pharm_requests->order_id;
														$pharmacy_child_requests_sales = $this->inventory_management_model->get_child_sales($inventory_start_date, $orders_id,$product_id,$search_start_date,$search_end_date);	
														//var_dump($child_requests_sales);die();
														$sales2 +=	$pharmacy_child_requests_sales;
													}
												}
											}
										}
										else
										{
											$sales2 = 0;
										}
										$sales = $sales1 + $sales2;
									}
									elseif($store_name == "Oserengoni")
									{
										$branch_code = "OSEB";
										
										$sales = $this->inventory_management_model->get_pharmacy_drug_units_sold($inventory_start_date, $product_id,$search_start_date,$search_end_date, $branch_code);
									}

									if($parent_store > 0)
									{
										$sales = $this->inventory_management_model->get_drug_units_sold($inventory_start_date, $product_id,$search_start_date,$search_end_date, $branch_code=NULL);
										$procurred = $this->inventory_management_model->item_deductions($inventory_start_date, $product_id,$store_id);
										// var_dump($quantity);die();
									}
									else
									{
										$sales = 0;
										$procurred = $this->inventory_management_model->item_proccured($inventory_start_date, $product_id,$store_id,$search_start_date,$search_end_date);
									}
									
			                        $in_stock = ($quantity + $purchases + $procurred) - $sales - $deductions - $other_deductions;

			                        $purchases = $purchases + $procurred;

			                        
									if($in_stock<=$reorder_level)
									{
			                        	$class = "class = 'danger'";
			                        }
									else{
			                        	$class = "";

			                        }
									$other_items = 
										'
										<!--<td>'.number_format($product_unitprice, 2).'</td>
		                                <td>'.number_format($markup, 2).'</td>-->				         
		                                <td>'.$quantity.'</td>						         
		                                <td>'.$purchases.'</td>
		                                <td>'.$sales.'</td>						         
		                                <td>'.$deductions.'</td>

										';
										if($parent_store == 0)
										{
											$button_two = ' <td><a href="'.site_url().'inventory/edit-product/'.$product_id.'" class="btn btn-sm btn-primary">Edit</a></td>
					                                <td><a href="'.site_url().'inventory/product-purchases/'.$product_id.'" class="btn btn-sm btn-warning">Purchases</a></td>
													
													 <td><a href="'.site_url().'deductions/'.$product_id.'/'.$store_id.'" class="btn btn-sm btn-danger">Deductions</a></td>
					                              ';
										}
										else
										{
											$button_two = ' <td><a class="btn btn-default btn-sm" href="'.site_url().'inventory/product-sales/'.$product_id.'" >Sales</a></td>
												 <td><a href="'.site_url().'deductions/'.$product_id.'/'.$store_id.'" class="btn btn-sm btn-danger">Deductions</a></td>';
										}
								// }

								
								

								$count++;
								
								$result .= 
								'
									<tr '.$class.'>
										<td>'.$count.'</td>
										<td>'.$product_code.'</td>
										<td>'.$store_name.'</td>
										<td>'.$product_name.'</td>
										<td>'.$category_name.'</td>	
										<td>'.$vatable_status.'</td>	         
		                                '.$other_items.'
		                                <td>'.$in_stock.'</td>
		                                <td>'.$status.'</td>
		                                '.$button_two.'
		                               
									</tr> 
								';
								
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
						echo $result;
				?>
				<div class="widget-foot">
			    <?php
			    if(isset($links)){echo $links;}
			    ?>
			    </div>
			</div>
			
		</section>
	</div>
</div>