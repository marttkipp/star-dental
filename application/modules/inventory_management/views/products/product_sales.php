
<div class="row">
    <div class="col-md-12">
		<section class="panel panel-featured panel-featured-info">
		    <header class="panel-heading">
		        <h2 class="panel-title pull-left"><?php echo $title;?></h2>
		         <div class="widget-icons pull-right">
		         	<a href="<?php echo base_url();?>inventory/products" class="btn btn-info btn-sm "> <i class="fa fa-arrow-left"></i> Back to products</a>
		          </div>
		          <div class="clearfix"></div>
		    </header>
		    <div class="panel-body">
				<?php

						
						$result = '';	

						//if users exist display them
						if ($query->num_rows() > 0)
						{
							$count = $page;

							
							$result .= 
							'
							<div class="row">
							<div class="col-md-12 table-responsive">
								<table class="table table-hover table-bordered">
								 
								  <thead> 
		                                <th>#</th>
		                                <th>Date</th>
		                                <th>Patient Name</th>
		                                <th>Quantity Sold</th>
		                                <th>Unit Price</th>
		                                <th>Total Amount</th>
		                                <th>Prescribed By</th>
		                                <th>Dispensed By</th>
		                            </thead>
								  <tbody>
							';
							
							//get all administrators
							$personnel_query = $this->personnel_model->get_all_personnel();
							
							foreach ($query->result() as $row)
							{//var_dump($query);die();
						
								$product_id = $row->product_id;
								$service_charge_name = $row->service_charge_name;
								$patient_surname = $row->patient_surname;
								$patient_othernames = $row->patient_othernames;
								$charge_created_by = $row->charge_created_by;
								$charge_modified_by = $row->charge_modified_by;
								$charge_date = $row->charge_date;
								$charge_time = $row->charge_time;
								
								$visit_charge_amount = $row->visit_charge_amount;
								$visit_charge_units = $row->visit_charge_units;
		                        
								$visit_date = date('jS M Y',strtotime($charge_date));
								$button = '';
								
					
								//creators & editors
								if($personnel_query->num_rows() > 0)
								{
									$personnel_result = $personnel_query->result();
									
									foreach($personnel_result as $adm)
									{
										$personnel_id2 = $adm->personnel_id;
										
										if($charge_created_by == $personnel_id2)
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

								if($personnel_query->num_rows() > 0)
								{
									$personnel_result = $personnel_query->result();
									
									foreach($personnel_result as $adm)
									{
										$personnel_id2 = $adm->personnel_id;
										
										if($charge_modified_by == $personnel_id2)
										{
											$dispensed_by = $adm->personnel_fname;
											break;
										}
										
										else
										{
											$dispensed_by = '-';
										}
									}
								}
								
								else
								{
									$dispensed_by = '-';
								}


								$count++;
								
								$result .= 
								'
									<tr>
										<td>'.$count.'</td>
										<td>'.$visit_date.'</td>
										<td>'.$patient_surname.' '.$patient_othernames.'</td>
										<td>'.$visit_charge_units.'</td>
										<td>'.$visit_charge_amount.'</td>
										<td>'.$visit_charge_amount*$visit_charge_units.'</td>		
		                                <td>'.$created_by.'</td>
		                                <td>'.$dispensed_by.'</td>
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