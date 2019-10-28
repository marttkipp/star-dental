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
	$approval_id = 2;//$this->inventory_management_model->get_approval_id($personnel_id);
// var_dump($approval_id); die();
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
                            <!-- <a href="<?php echo base_url();?>inventory/manage-orders" class="btn btn-info btn-sm fa fa-plus"> Manage Orders</a> -->
                            <?php
						}
					?>
                 	<!-- <a href="<?php echo base_url();?>inventory/manage-store" class="btn btn-default btn-sm fa fa-plus"> Manage Store</a> -->
			        <?php
			         if(($type == 1) || ($type == 3))
			         {
			         	?>

			         	<?php
			         }
			         $personnel_id = $this->session->userdata('personnel_id');
			         $department_id = $this->reception_model->get_personnel_department($personnel_id);
			         // if(($type == 2) || ($type == 3) || $personnel_id == 0 || $department_id == 1)
			         // {
			         	?>
			         	<!--<a href="<?php echo base_url();?>inventory/import-products" class="btn btn-success btn-sm" style="margin-left:10px;">Import Product</a>
						<a href="<?php echo base_url();?>inventory/export-products" class="btn btn-info btn-sm" style="margin-left:10px;">Export Product</a>
                        <a href="<?php echo base_url();?>inventory/import-balances" class="btn btn-info btn-sm" style="margin-left:10px;">Import Opening Balances</a>
						<!-- <a href="<?php echo base_url();?>inventory/product-deductions" class="btn btn-warning btn-sm fa fa-minus"> Manage Requests</a> -->

						<a href="<?php echo base_url();?>inventory/add-product" class="btn btn-success btn-sm fa fa-plus"> Add New Product</a>

                        <!-- <a href="<?php echo base_url();?>inventory_management/update_initial_product_balance" class="btn btn-default btn-sm fa fa-plus"> Update Stock Balances</a> -->

                        <!-- <a href="<?php echo base_url();?>inventory/print-product-out-stock" class="btn btn-danger btn-sm fa fa-print"> Out Of Stock</a> -->

			         	<?php
			         // }
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
		                                <th>O</th>
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
		                                <th>33% MU</th>
		                                <th>O</th>-->
		                                <th>P</th>
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
							// $personnel_query = $this->personnel_model->get_all_personnel();

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
								$owning_store_id = $row->owning_store_id;
								$regenerate_id = $row->regenerate_id;
								$stock_take = $row->stock_take;
								if($parent_store == 0)
								{
									$quantity = $row->quantity;
								}
								else
								{
									$quantity = 0;
								}

								$quantity = $row->store_quantity;
								// var_dump($quantity); die();
								$product_unitprice = $row->product_unitprice;
		                        $product_deleted = $row->product_deleted;
		                        $in_service_charge_status = $row->in_service_charge_status;
		                        // var_dump($owning_store_id);

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

								if($stock_take == 0 OR empty($stock_take))
								{
									$regenerate = 'info';
									$closed = 'readonly';
								}
								else
								{
									$regenerate = 'primary';
									$closed = '';
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



									$store_id = $owning_store_id;


                                    $purchases = 0;
                                    $sales = 0;
                                    $purchases = 0;
                                    $deductions = 0;
                                    $in_stock = 0;
                                    $pending_procurement = 0;
                                    $opening_quantity = 0;
                                    $total_store_deductions = 0;
                                    $child_stock = 0;

                                    $stock_level = $this->inventory_management_model->get_stock_purchases_new($product_id,$store_id);

                                    $purchases = $stock_level['dr_quantity'];
                                    $deductions = $stock_level['cr_quantity'];
                                   
                                    $in_stock = $purchases - $deductions;

                                    
			                        	// var_dump($store_requests); die();
									if($in_stock<=$reorder_level)
									{
			                        	$class = "class = 'danger'";
			                        }
									else{
			                        	$class = "";

			                        }
									$other_items =
										'
		                                <td>'.$purchases.'</td>
		                                <td>'.$deductions.'</td>

										';



										if($parent_store == 0 )
										{
											
												
													// <td><a href="'.site_url().'search-store-ded/'.$product_id.'" class="btn btn-sm btn-info fa fa-database"></a></td>
												$button_two_sub ='

													<td><a href="'.site_url().'inventory/drug-trail/'.$product_id.'" class="btn btn-sm btn-primary fa fa-file"></a></td>
													 <td><a href="'.site_url().'inventory/product-purchases/'.$product_id.'" class="btn btn-sm btn-success fa fa-plus"></a></td>
													 
													';
												// $personnel_role = $this->session->userdata('personnel_role');

												$personnel_role = $this->session->userdata('personnel_role');
												$regenerate_button = '';

												// var_dump($personnel_role);die();
												if($personnel_role == 'Admin' OR $personnel_role == 'Accounts' OR $personnel_role == 'CEO')
												{
													$regenerate_button = '<td><a href="'.site_url().'regenerate-product/'.$product_id.'" class="btn btn-sm btn-danger fa fa-recycle" onclick="return confirm(\'Are you sure you want to regenerate this product?\');"></a></td>
													<td><a href="'.site_url().'delete-product/'.$product_id.'" class="btn btn-sm btn-danger fa fa-trash" tooltip="Delete" onclick="return confirm(\'Are you sure you want to delete this record ? \')"></a></td>';
												}
												else
												{
													$regenerate_button = '';
												}

											$delete_button = '<td><a href="'.site_url().'inventory/deduction-product/'.$product_id.'/'.$store_id.'" class="btn btn-sm btn-danger fa fa-minus" tooltip="Reduce Stock"></a></td>
													';	
											
											$button_two = ' <td><a href="'.site_url().'inventory/edit-product/'.$product_id.'" class="btn btn-sm btn-primary fa fa-pencil"> </a></td>
					                           	'.$button_two_sub.'
					                           	'.$delete_button.'
					                            

					                              ';

										}
										else
										{
											
											$personnel_role = $this->session->userdata('personnel_role');
											$delete_button = '';
											if($personnel_role == 'Admin' OR $personnel_role == 'Accounts' OR $personnel_role == 'CEO')
											{
												$delete_button = '<td><a href="'.site_url().'inventory/deduction-product/'.$product_id.'/'.$store_id.'" class="btn btn-sm btn-danger fa fa-minus" tooltip="Reduce Stock"></a></td>
													';
											}
											else
											{
												$delete_button = '';
											}
											$button_two = ' <td><a class="btn btn-default btn-sm fa fa-money" href="'.site_url().'inventory/product-sales/'.$product_id.'" ></a></td>
													 <td><a href="'.site_url().'search-s11/'.$product_id.'" class="btn btn-sm btn-success fa fa-search"></a></td>
													  '.$delete_button.'';
											
										}


								// }
							  	// var_dump($department_id); die();
										if( ($personnel_id_main==0))
										{

											$button_update = '<td><input type="text" name="amount" class="form-control" value="'.$quantity.'" '.$closed.'/></td>
												 <td><button type="submit" class="btn btn-sm btn-warning" >Update </button></td>';
										}
										else
										{
											if(($department_id == 1 AND ($is_admin OR $personnel_id_main == 0) AND $store_id > 5))
											{
												$button_update = ' <td><a href="'.site_url().'inventory/return-product/'.$product_id.'/'.$store_id.'" class="btn btn-sm btn-warning fa fa-arrow-left"></a></td>';
											}
											else
											{
												$button_update = '';
											}

										}


								// $child_store_stock = $this->inventory_management_model->child_store_stock($inventory_start_date, $product_id,6);
								$count++;
								$result.= form_open("update-current-stock/".$product_id."/".$owning_store_id, array("class" => "form-horizontal"));
								$result .=
								'
									<tr >
										<td>'.$count.'</td>
										<td class="'.$regenerate.'">'.$product_code.'</td>
										<td>'.$store_name.'</td>
										<td '.$class.'>'.$product_name.'</td>
										<td>'.$category_name.'</td>
										<td>'.$vatable_status.'</td>
		                                '.$other_items.'
		                                <td '.$class.'>'.$in_stock.'</td>
		                                <td>'.$status.'</td>
		                                '.$button_update.'
		                                '.$button_two.'




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
