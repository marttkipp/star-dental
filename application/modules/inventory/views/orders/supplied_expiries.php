  <?php echo $this->load->view('search_supplied_products', '', TRUE);?>

<div class="row">
    <div class="col-md-12">
		<section class="panel panel-featured panel-featured-info">
		    <header class="panel-heading">
		        <h2 class="panel-title pull-left"><?php echo $title;?></h2>
		         <div class="widget-icons pull-right">
                 	
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
								
						$search = $this->session->userdata('product_purchased_search');
						
						if(!empty($search))
						{
							echo '<a href="'.site_url().'close-product-purchased-search" class="btn btn-success btn-sm">Close Search</a>';
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

							
							$result .= 
							'
							<div class="row">
							<div class="col-md-12 table-responsive">
								<table class="table table-bordered">								 
								  <thead> 
		                                <th>#</th>
		                                <th>Supplied Date</th>
		                                <th>Expiry Date</th>
		                                <th>Drug</th>
		                                <th>Units</th>
		                                <th>Pack Size</th>
		                                <th>Purchased Units</th>
		                                <th>Buying Unit Price</th
		                            </thead>
								  <tbody>
							';
							
							//get all administrators
							$personnel_query = $this->personnel_model->get_all_personnel();
							
							foreach ($query->result() as $row)
							{//var_dump($query);die();
						
								$product_id = $row->product_id;
								$product_name = $row->product_name;
								$product_status = $row->product_status;
								$category_name = $row->product_category_name;
								$reorder_level = $row->reorder_level;
								$store_id = $row->store_id;
								$opening_quantity = $row->opening_quantity;			
								$product_unitprice = $row->product_unitprice;
		                        $product_deleted = $row->product_deleted;
		                        $creditor_name = $row->creditor_name;
		                        $supplier_invoice_date = $row->supplier_invoice_date;
		                        $supplier_invoice_number = $row->supplier_invoice_number;
		                        $quantity_received = $row->quantity_received;
		                        $pack_size = $row->pack_size;
		                        $unit_price = $row->unit_price;
		                        $selling_unit_price = $row->selling_unit_price;
		                        $expiry_date = $row->expiry_date;
		                        $expiry_recorded = $row->expiry_recorded;
		                        $order_supplier_id = $row->order_supplier_id;
								

								$units_received = $quantity_received * $pack_size;
								 if ($units_received == 0)
								 {


								$bp_unit = 0;
								 }
								 else
								 {

								$bp_unit = $unit_price / $units_received;
								 }

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
								
								$search_end_date = $supplier_invoice_date;

								
									$markup = round(($product_unitprice * 1.33), 0);
									$markdown = $markup;//round(($markup * 0.9), 0);

								if($expiry_recorded == 1 AND $expiry_date <= date('Y-m-d'))
								{
									$checked = 'danger';
								}
								else
								{
									$checked = 'default';
								}
							

								
								

								$count++;
								
								$result .= 
								'
									<tr >
										<td>'.$count.'</td>
										<td class="'.$checked.'">'.date('jS M Y',strtotime($supplier_invoice_date)).'</td>
										<td class="'.$checked.'">'.date('jS M Y',strtotime($expiry_date)).'</td>
										<td class="'.$checked.'">'.$product_name.'</td>		
										<td class="'.$checked.'">'.$quantity_received.'</td>	
										<td class="'.$checked.'">'.$pack_size.'</td>	
										<td class="'.$checked.'">'.$quantity_received * $pack_size.'</td>
										<td class="'.$checked.'">'.number_format($bp_unit,2).'</td>	
										<td><a href="'.site_url().'inventory/deduction-product/'.$product_id.'/'.$order_supplier_id.'" class="btn btn-sm btn-danger fa fa-minus"></a></td>
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