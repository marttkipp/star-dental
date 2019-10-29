<?php 


echo $this->load->view('search_product_prices', '', TRUE); 

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
                 
                     <a href="<?php echo base_url();?>inventory/download-all-stock" target="_blank" class="btn btn-default btn-sm fa fa-print"> Download All</a>
                    
                     
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
								
						 $search = $this->session->userdata('product_price_search');
        
                        if(!empty($search))
                        {
                            echo '
                            <a href="'.site_url().'inventory_management/close_request_prices_search" class="btn btn-warning btn-sm ">Close Search</a>
                            ';
                        }
						
						
						$result = '';
						$result = ''.$search_result2.''	;
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
		                                <th>Pharmacy</th>
		                                <th>Main Store</th>
		                                <th>Price</th>
		                                <th>Amount</th>
		                                <th>Vatable</th>
		                                <th>Status</th>
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
                               
                            	
                                // $child_stock = $this->inventory_management_model->total_child_store_stock($inventory_start_date, $product_id,6);
                                // $parent_stock = $this->inventory_management_model->total_parent_store_stock($inventory_start_date, $product_id,5);


                                
                               
								// $total_stock = $child_stock+$parent_stock;

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
										<td>'.number_format($product_unitprice,2).'</td>
										<td><input type="numeric"  class="form-control" name="product_unitprice" size="4" ></td>
										<td><div class="form-group">
				                                '.$status_value.'
				                            </div>
				                        </td>
				                         <td><button type="submit" class="btn btn-xs btn-warning" >Update </button></td>

		                               
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