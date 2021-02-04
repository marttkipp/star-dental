<!-- search -->
<?php //echo $this->load->view('search/providers', '', TRUE);?>
<!-- end search -->
<div class="row" style="margin-top: 15px;">
	<div class="col-md-12">

		<section class="panel" >
		    <header class="panel-heading">
		        <h2 class="panel-title"><?php echo $title;?> </h2>                	
		    </header>

		    <!-- Widget content -->
		    <div class="panel-body">
		    	<div class="padd">
		          <?php
		            	$error = $this->session->userdata('error_message');
						$success = $this->session->userdata('success_message');
						
						if(!empty($error))
						{
							echo '<div class="alert alert-danger">'.$error.'</div>';
							$this->session->unset_userdata('error_message');
						}
						
						if(!empty($success))
						{
							echo '<div class="alert alert-success">'.$success.'</div>';
							$this->session->unset_userdata('success_message');
						}
					?>
		            
		           
		                
		<?php
				
				$result = '';
				
						// var_dump($query->result()); die();
				//if users exist display them
				if ($query->num_rows() > 0)
				{
					$count = $page;
					
					$result .= '
							<table class="table table-hover table-bordered ">
							  <thead>
								<tr>
								  <th>#</th>
								  <th>Doctor</th>
								  <th>Opening bAL</th>
								  <th>Cash</th>
								  <th>Insurance</th>
								  <th>Gross</th>
								  <th>Less Lab</th>	
								  <th>Net Payable</th>
								  <th>60%</th>
								  <th>40%</th>
								  <th>Payments</th>
								  <th>Balance</th>
								  <th colspan="2">Actions</th>
								</tr>
							  </thead>
							  <tbody>
						';
					
					foreach ($query->result() as $row)
					{
						$count++;
						$personnel_id = $row->personnel_id;
						$personnel_fname = $row->personnel_fname;
						$personnel_onames = $row->personnel_onames;
						$personnel_type_id = $row->personnel_type_id;
						$personnel_percentage = $row->personnel_type_payment_amount;
						
						$creditor_result = $this->creditors_model->get_provider_statement($personnel_id,$personnel_type_id,$personnel_percentage);

						
						// $response['total_gross_payable']

						$hosp_payable = $creditor_result['hosp_payable'];
						$doctors_total = $creditor_result['doctors_total'];
						$total_payments = $creditor_result['total_payments'];
						$total_balance = $creditor_result['total_balance'];
						$total_wht = $creditor_result['total_wht'];
						$total_hospital = $creditor_result['total_hospital'];
						$total_doctors = $creditor_result['total_doctors'];
						$total_gross_payable = $creditor_result['total_gross_payable'];
						$total_net_payable = $creditor_result['total_net_payable'];

						
						$result .= 
							'
								<tr>
									<td>'.$count.'</td>
									<td>Dr. '.$personnel_fname.' '.$personnel_onames.'</td>
									<td>'.number_format($opening_balance, 2).'</td>
									<td>'.number_format($hosp_payable, 2).'</td>
									<td>'.number_format($doctors_total, 2).'</td>
									<td>'.number_format($total_gross_payable, 2).'</td>
									<td>('.number_format($total_wht, 2).')</td>
									<td>'.number_format($total_net_payable, 2).'</td>
									<td>'.number_format($total_hospital, 2).'</td>
									<td>'.number_format($total_doctors, 2).'</td>
									<td>('.number_format($total_payments, 2).')</td>
									<td>'.number_format($total_balance, 2).'</td>
									<td><a href="'.site_url().'accounting/provider-statement/'.$personnel_id.'/'.$personnel_type_id.'" class="btn btn-xs btn-info" >Statement</a></td>
									<td><button type="button" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#book-appointment'.$personnel_id.'"><i class="fa fa-plus"></i> Balance </button>
										<div class="modal fade " id="book-appointment'.$personnel_id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
										    <div class="modal-dialog modal-lg" role="document">
										        <div class="modal-content ">
										            <div class="modal-header">
										            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										            	<h4 class="modal-title" id="myModalLabel">Update Balance '.$personnel_fname.' '.$personnel_onames.'</h4>
										            </div>
										            '.form_open("update-provider-balance/".$personnel_id, array("class" => "form-horizontal")).'

										            <div class="modal-body">
										            	<div class="row">
										            		<input type="hidden" name="redirect_url" id="redirect_url'.$personnel_id.'" value="'.$this->uri->uri_string().'">
										            		<div class="col-md-12">
										            			<div class="col-md-4">
										            				<div class="form-group">
																		<label class="col-lg-4 control-label">From: </label>
																		
																		<div class="col-lg-8">
									                                        <div class="input-group">
									                                            <span class="input-group-addon">
									                                                <i class="fa fa-calendar"></i>
									                                            </span>
									                                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="start_date" id="scheduledate" placeholder="Date" value="'.$opening_date.'" required>
									                                        </div>
																		</div>
																	</div>
																</div>
																<div class="col-md-4">
										            				<div class="form-group">
																		<label class="col-lg-4 control-label">Amount: </label>
																		
																		<div class="col-lg-8">
									                                        <input class="form-control" name="opening_balance" id="procedure_done" value="'.$opening_balance.'">
													                           
																		</div>
																	</div>
																</div>
																<div class="col-md-4">
										            				<div class="form-group">
																		<label class="col-lg-5 control-label">Prepayment ?</label>
																		<div class="col-lg-3">
																			<div class="radio">
																				<label>
																				<input id="optionsRadios5" type="radio" value="1" name="debit_id" checked="'.$payment.'">
																				Yes
																				</label>
																			</div>
																		</div>
																		<div class="col-lg-3">
																			<div class="radio">
																				<label>
																				<input id="optionsRadios6" type="radio" value="2" name="debit_id" checked="'.$invoice.'">
																				No
																				</label>
																			</div>
																		</div>
																	</div>
																</div> 
										            			
										            		</div>
										            	</div>
										            	
																
										              	
										            </div>
										            <div class="modal-footer">
										            	<button  class="btn btn-sm btn-success" type="submit">Update Opening Balance</a>
										                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
										            </div>

										               '.form_close().'
										        </div>
										    </div>
										</div>

									</td>
									';
						
					}
					
					$result .= 
						'
									  </tbody>
									</table>
						';
				}
				
				else
				{
					$result .= "There are no creditors";
				}
				
				echo $result;
		?>
		          
		          <div class="widget-foot">
		                                
						<?php if(isset($links)){echo $links;}?>
		            
		                <div class="clearfix"></div> 
		            
		            </div>
		        </div>
		        <!-- Widget ends -->

		      </div>
		</section>
		
	</div>
	
</div>
