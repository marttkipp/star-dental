<?php
$patient = $this->reception_model->patient_names2(NULL, $visit_id);
$account_balance = $patient['account_balance'];


$rs_rejection = $this->dental_model->get_rejection_info($visit_id);
$rejected_amount = 0;
$rejected_reason ='';
$close_card = 0;
$payment_info = '';
if(count($rs_rejection) >0){
  foreach ($rs_rejection as $r2):
    # code...
    $rejected_amount = $r2->rejected_amount;
    $rejected_date = $r2->rejected_date;
    $rejected_reason = $r2->rejected_reason;
    $visit_type = $r2->visit_type;
    $close_card = $r2->close_card;
    $invoice_number = $r2->invoice_number;
    $parent_visit = $r2->parent_visit;
    $payment_info = $r2->payment_info;

    // get the visit charge

  endforeach;
}
// echo $parent_visit; die();

// var_dump($visit_id); die();

$rs_rejection_rs = $this->dental_model->get_visit_rejected_updates($visit_id);

$rejection = '<table class="table table-hover table-bordered col-md-12">
				<thead>
					<tr>
						<th>Visit Type</th>
						<th>Amount</th>
						<th colspan="1"></th>
					</tr>
				</thead>
				<tbody>';
$total_rejected = 0;
if(count($rs_rejection_rs) >0){
  foreach ($rs_rejection_rs as $r3):
    # code...
    $visit_type_name2 = $r3->visit_type_name;
    $visit_id_other = $r3->visit_id;
    $invoice_number = $r3->invoice_number;
    $visit_bill_amount = $r3->visit_bill_amount;
    $total_rejected += $visit_bill_amount;

    // get the visit charge
    	$rejection .= '<tr>
    						<td>'.$visit_type_name2.'</td>
    						<td>'.number_format($visit_bill_amount,2).'</td>
    					
    						<td><a class="btn btn-danger btn-sm fa fa-trash" href="'.site_url().'accounts/remove_invoice/'.$visit_id.'/'.$invoice_number.'" onclick="return confirm(\' Do you want to remove this invoice\')"> </a></td>
    					</tr>';

  endforeach;
}

$rejection .='</tbody>
			</table>';

$rejected_amount += $total_rejected;



$rs_pa = $this->nurse_model->get_prescription_notes_visit($visit_id);
$visit_prescription = count($rs_pa);
// var_dump($visit_prescription);die();
?>
 <section class="panel ">
	<header class="panel-heading">
		<div class="panel-title">
		<strong>Name:</strong> <?php echo $patient_surname.' '.$patient_othernames;?>. <strong> Visit: </strong><?php echo $visit_type_name;?>.  Bal <?php echo $account_balance?> 

		<a href="<?php echo site_url();?>administration/individual_statement/<?php echo $patient_id?>/1" class="btn btn-warning btn-sm " target="_blank" style="margin-top:0px"><i class="fa fa-print"></i> Statement </a>
		

		</div>
		<div class="pull-right">
			
				<a href="<?php echo site_url();?>queue" class="btn btn-info btn-sm pull-right " style="margin-top:-25px"><i class="fa fa-arrow-left"></i> Back to Queue</a>
		</div>
	</header>
	
	<!-- Widget content -->
	
	<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
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
						
				$search = $this->session->userdata('patient_search');
				
				if(!empty($search))
				{
					echo '
					<a href="'.site_url().'reception/close_patient_search" class="btn btn-warning btn-sm ">Close Search</a>
					';
				}

				if($visit_prescription > 0)
				{
					echo '<div class="alert alert-danger">A prescription has been attached to this visit. <a href="'.site_url().'print-prescription/'.$visit_id.'" target="_blank" class="btn btn-warning btn-sm ">Print Prescription</a></div>';
				}

				if(!empty($payment_info))
				{
					echo '<div class="alert alert-danger">'.$payment_info.'</div>';
				}
		
			 ?>
			</div>
		</div>
		
		
        <!--<div class="row">
        	<div class="col-sm-3 col-sm-offset-3">
            	<a href="<?php echo site_url().'doctor/print_prescription'.$visit_id;?>" class="btn btn-warning">Print prescription</a>
            </div>
            
        	<div class="col-sm-3">
            	<a href="<?php echo site_url().'doctor/print_lab_tests'.$visit_id;?>" class="btn btn-danger">Print lab tests</a>
            </div>
        </div>-->

        
      
        
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-6">
					<section class="panel panel-featured panel-featured-info">
						<header class="panel-heading">
							
							<h2 class="panel-title"></h2>

							 
						</header>





						 <div class="row">
						 	<br>
						 	<div class="col-md-12">
						 	<a href="<?php echo site_url();?>accounts/print_invoice_new/<?php echo $visit_id?>" target="_blank" class="btn btn-sm btn-warning pull-right" > <i class="fa fa-print"></i> Print Invoice</a>
							 <!-- <a href="<?php echo site_url();?>accounts/print_self_invoice/<?php echo $visit_id?>" target="_blank" class="btn btn-sm btn-info pull-left" > <i class="fa fa-print"></i> Print Self</a> -->
						 	</div>
						<br>
					
                            	<?php echo form_open("accounts/bill_patient/".$visit_id, array("class" => "form-horizontal"));?>
                            	<br/>
                            	<!-- <div class="row">
					            	<div class="col-md-10 ">
					                    <div class="col-md-12" style="margin-bottom: 10px">
						                  <div class="form-group">
						                  <label class="col-md-2 control-label">Service: </label>
						                  	<div class="col-md-10">
							                    <select id='service_id_item' name='service_charge_id' class='form-control custom-select ' >
							                      <option value=''>None - Please Select a service</option>
							                       <?php echo $services_list;?>
							                    </select>

							                    <input type="hidden" name="visit_id_checked" id="visit_id_checked">
						                    </div>
						                  </div>
						                </div>
						                <br>
						                <input type="hidden" name="provider_id" value="0">
						               
						                <input data-format="yyyy-MM-dd" type="hidden" data-plugin-datepicker class="form-control" name="visit_date_date" id="visit_date_date" placeholder="Admission Date" value="<?php echo date('Y-m-d');?>">
						            </div>
						            <div class="col-md-10" >
						            	<div class="center-align">
											<button type="submit" class='btn btn-info btn-sm'  onclick="parse_procedures(<?php echo $visit_id;?>,1);" >Add to Bill</button>
										</div>
						            </div>
						           </div> -->
						         <?php echo form_close();?>
						    </div>
						<div class="panel-body">

							<div id="procedures"></div>
                            
                            
						
						</div>
					</section>

				    <section class="panel panel-featured panel-featured-info">
							<header class="panel-heading">
										
							<h2 class="panel-title">Rejected Invoice</h2>

							</header>
							<div class="panel-body">

								<?php 
								if($parent_visit == 0)
								{


									?>
									<?php echo form_open("accounts/update_rejected_reasons/".$visit_id.'/'.$close_page, array("class" => "form-horizontal"));?>	

									<div class="form-group">
										<label class="col-lg-4 control-label">Visit type: </label>
										
										<div class="col-lg-8">
											<select name="visit_type_id" id="visit_type_id" class="form-control">
												<option value="0">----Select a visit type----</option>
												<?php
																		
													if($visit_types_rs->num_rows() > 0){

														foreach($visit_types_rs->result() as $row):
															$visit_type_name = $row->visit_type_name;
															$visit_type_id = $row->visit_type_id;

															if($visit_type_id == $visit_type)
															{
																// echo "<option value='".$visit_type_id."' selected='selected'>".$visit_type_name."</option>";
															}
															
															else
															{
																echo "<option value='".$visit_type_id."'>".$visit_type_name."</option>";
															}
														endforeach;
													}
												?>
											</select>
										</div>
									 </div>

									 <div id="insured_company" style="display: none;">            
										<div class="form-group" style="margin-bottom: 15px;">
											<label class="col-lg-4 control-label">Insurance Number: </label>
											<div class="col-lg-8">
												<input type="text" name="insurance_number" class="form-control" value="<?php echo $insurance_number;?>">
											</div>
										</div>

							            
										<div class="form-group" style="margin-bottom: 15px;">
											<label class="col-lg-4 control-label">Insurance Scheme: </label>
											<div class="col-lg-8">
												<input type="text" name="insurance_description" class="form-control" value="<?php echo $scheme_name?>">
											</div>
										</div>
									</div>
										<div class="form-group">
										    <label class="col-lg-4 control-label">Amount</label>
										    <div class="col-lg-8">
										        <input type="text" class="form-control" name="rejected_amount" placeholder="Rejected Amount" value="<?php echo set_value('rejected_amount');?>" >
										    </div>
										</div> 
										 <input type="hidden" class="form-control" name="redirect_url" placeholder="" autocomplete="off" value="<?php echo $this->uri->uri_string()?>">
										<div class="form-group">
										    <label class="col-lg-4 control-label">reason</label>
										    <div class="col-lg-8">
										    	<textarea  class="form-control" name="rejected_reason"><?php echo set_value('rejected_reason');?></textarea>
										    </div>
										</div> 
				                         <div class="center-align">
											<button class="btn btn-info btn-sm" type="submit">Create a rejection </button>
										</div>
										<br>
			                      <?php echo form_close();?>
			                      <?php
			                      if(!empty($rejected_reason))
			                      {


			                      ?>

			                      <br>
			                      <div class="row">
			                      	<div class="alert alert-danger"> 
				                      	<p>Rejected Amount : Kes. <?php echo $rejected_amount;?></p>
				                      	<p>Rejected Reason : <?php echo $rejected_reason;?></p>
				                      </div>
			                      </div>
			                      <a href="<?php echo site_url().'accounts/remove_rejected_amount/'.$visit_id?>" class="btn- btn-sm btn-danger"> Remove allocation</a>
			                       <br>
			                      <?php

			                  		}
			                  		echo $rejection;
			                  	}
			                  	else
			                  	{
			                  		?>
			                  		 <div class="row">
				                      	<div class="alert alert-danger"> 
					                      	<p>This is a child invoice to invoice number <?php echo $invoice_number;?></p>
					                      </div>
				                      </div>
			                  		<?php


			                  	}
		                      ?>
		                    </div>
                     </section>      
		                 
				</div>
				
				<div class="col-md-6">							
					<section class="panel panel-featured panel-featured-info">
						<header class="panel-heading">
							
							<h2 class="panel-title">Add payment</h2>
						</header>
                        
						<div class="panel-body">
							<?php echo form_open("accounts/make_payment_charge/".$visit_id.'/'.$close_page, array("class" => "form-horizontal"));?>
								<div class="form-group">
									<div class="col-lg-4">
                                    	<div class="radio">
                                            <label>
                                                <input id="optionsRadios2" type="radio" name="type_payment" value="1" checked="checked" onclick="getservices(1)"> 
                                                Normal
                                            </label>
                                        </div>
									</div>
									<div class="col-lg-4">
                                    	<div class="radio">
                                            <label>
                                                <input id="optionsRadios2" type="radio" name="type_payment" value="2" onclick="getservices(2)"> 
                                                Waiver / Discount
                                            </label>
                                        </div>
									</div>
									<div class="col-lg-4">
                                    	<div class="radio">
                                            <label>
                                                <input id="optionsRadios2" type="radio" name="type_payment" value="3" onclick="getservices(3)"> 
                                                Debit Note
                                            </label>
                                        </div>
									</div> 
								</div>
                                <input type="hidden" name="service_id" value="0">
								<div id="service_div2" class="form-group" style="display:none;">
									<label class="col-lg-4 control-label">Service: </label>
								  
									<div class="col-lg-8">
										
                                    	<select name="service_id" class="form-control" >
                                        	<option value="">All services</option>
                                    	<?php
										if(count($item_invoiced_rs) > 0)
										{
											$s=0;
											foreach ($item_invoiced_rs as $key_items):
												$s++;
												$service_id = $key_items->service_id;
												$service_name = $key_items->service_name;
												?>
                                                <option value="<?php echo $service_id;?>"><?php echo $service_name;?></option>
												<?php
											endforeach;
										}
											
										//display DN & CN services
										if(count($payments_rs) > 0)
										{
											foreach ($payments_rs as $key_items):
												$payment_type = $key_items->payment_type;
												
												if(($payment_type == 2) || ($payment_type == 3))
												{
													$payment_service_id = $key_items->payment_service_id;
													
													if($payment_service_id > 0)
													{
														$service_associate = $this->accounts_model->get_service_detail($payment_service_id);
														?>
														<option value="<?php echo $payment_service_id;?>"><?php echo $service_associate;?></option>
														<?php
													}
												}
												
											endforeach;
										}
										?>
                                        </select>
									</div>
								</div>
                                
                                <div id="service_div" style="display:none;">
                                	<div  class="form-group" >
                                        <label class="col-lg-4 control-label"> Services: </label>
                                        
                                        <div class="col-lg-8">
                                            <select class="form-control" name="payment_service_id" >
                                            	<option value="">--Select a service--</option>
												<?php
                                                $service_rs = $this->accounts_model->get_all_service();
                                                $service_num_rows = count($service_rs);
                                                if($service_num_rows > 0)
                                                {
													foreach($service_rs as $service_res)
													{
														$service_id = $service_res->service_id;
														$service_name = $service_res->service_name;
														if($service_name="Cash")
														{
															echo '<option value="'.$service_id.'" selected>'.$service_name.'</option>';
														}
														else
														{

															echo '<option value="'.$service_id.'">'.$service_name.'</option>';
														}
														
													}
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
										<label class="col-lg-4 control-label">Amount: </label>
									  
										<div class="col-lg-8">
											<input type="text" class="form-control" name="waiver_amount" placeholder="" autocomplete="off">
										</div>
									</div>

									<div class="form-group">
										<label class="col-lg-4 control-label">Reason: </label>
									  
										<div class="col-lg-8">
											<textarea class="form-control" name="reason" placeholder="" autocomplete="off"></textarea>
										</div>
									</div>
                                </div>
                            	

								
									
								<div id="payment_method">
									<div class="form-group">
										<label class="col-lg-4 control-label">Amount: </label>
									  
										<div class="col-lg-8">
											<input type="text" class="form-control" name="amount_paid" placeholder="" autocomplete="off">
										</div>
									</div>
									<div class="form-group" >
										<label class="col-lg-4 control-label">Payment Method: </label>
										  
										<div class="col-lg-8">
											<select class="form-control" name="payment_method" onchange="check_payment_type(this.value)">
                                            	<?php
												  $method_rs = $this->accounts_model->get_payment_methods();
												  $num_rows = count($method_rs);
												 if($num_rows > 0)
												  {
													
													foreach($method_rs as $res)
													{
													  $payment_method_id = $res->payment_method_id;
													  $payment_method = $res->payment_method;
													  
														echo '<option value="'.$payment_method_id.'">'.$payment_method.'</option>';
													  
													}
												  }
											  ?>
											</select>
										  </div>
									</div>
								</div>
								
								<div id="mpesa_div" class="form-group" style="display:none;" >
									<label class="col-lg-4 control-label"> Mpesa TX Code: </label>

									<div class="col-lg-8">
										<input type="text" class="form-control" name="mpesa_code" placeholder="">
									</div>
								</div>
							  
								<div id="insuarance_div" class="form-group" style="display:none;" >
									<label class="col-lg-4 control-label"> Reference Number: </label>
									<div class="col-lg-8">
										<input type="text" class="form-control" name="debit_card_detail" placeholder="">
									</div>
								</div>
							  
								<div id="cheque_div" class="form-group" style="display:none;" >
									<label class="col-lg-4 control-label"> Cheque Number: </label>
								  
									<div class="col-lg-8">
										<input type="text" class="form-control" name="cheque_number" placeholder="">
									</div>
								</div>
							  
								<div id="username_div" class="form-group" style="display:none;" >
									<label class="col-lg-4 control-label"> Username: </label>
								  
									<div class="col-lg-8">
										<input type="text" class="form-control" name="username" placeholder="">
									</div>
								</div>
							  
								<div id="password_div" class="form-group" style="display:none;" >
									<label class="col-lg-4 control-label"> Password: </label>
								  
									<div class="col-lg-8">
										<input type="password" class="form-control" name="password" placeholder="">
									</div>
								</div>
								<br>
								<div class="center-align">
									<button class="btn btn-info btn-sm" type="submit">Add Payment Information</button>
								</div>
								<?php echo form_close();?>
						</div>
					</section>
					<section class="panel panel-featured panel-featured-info">
						<header class="panel-heading">
							<h2 class="panel-title">Receipts</h2>
						</header>
						
						<div class="panel-body">
                        	<div class="row">
                            	<div class="col-md-12">
                            		
                                	<!-- <a href="<?php echo site_url();?>accounts/print_receipt_new/<?php echo $visit_id;?>" target="_blank" class="btn btn-sm btn-primary pull-right" style="margin-bottom:10px;" >Print all Receipts</a> -->
                                </div>
                            </div>
							<table class="table table-hover table-bordered col-md-12">
								<thead>
									<tr>
										<th>#</th>
										<th>Time</th>
										<th>Method</th>
										<th>Amount</th>
										<th colspan="2"></th>
									</tr>
								</thead>
								<tbody>
									<?php
								
									$payments_rs = $this->accounts_model->payments($visit_id);
									$total_payments = 0;
									$total_amount = ($total + $debit_note_amount) - $credit_note_amount;
									$total_waiver = 0;
									if(count($payments_rs) > 0)
									{
										$x=0;

										foreach ($payments_rs as $key_items):
											$x++;
											$payment_method = $key_items->payment_method;

											$time = $key_items->time;
											$payment_type = $key_items->payment_type;
											$payment_id = $key_items->payment_id;
											$payment_status = $key_items->payment_status;
											$payment_service_id = $key_items->payment_service_id;
											$service_name = '';

											if($payment_type == 2 && $payment_status == 1)
											{
												$waiver_amount = $key_items->amount_paid;
												$total_waiver += $waiver_amount;
											}
											
											if($payment_type == 1 && $payment_status == 1)
											{
												$amount_paid = $key_items->amount_paid;
												$amount_paidd = number_format($amount_paid,2);
												
												if(count($item_invoiced_rs) > 0)
												{
													foreach ($item_invoiced_rs as $key_items):
													
														$service_id = $key_items->service_id;
														
														if($service_id == $payment_service_id)
														{
															$service_name = $key_items->service_name;
															break;
														}
													endforeach;
												}
											
												//display DN & CN services
												if((count($payments_rs) > 0) && ($service_name == ''))
												{
													foreach ($payments_rs as $key_items):
														$payment_type = $key_items->payment_type;
														
														if(($payment_type == 2) || ($payment_type == 3))
														{
															$payment_service_id2 = $key_items->payment_service_id;
															
															if($payment_service_id2 == $payment_service_id)
															{
																$service_name = $this->accounts_model->get_service_detail($payment_service_id);
																break;
															}
														}
														
													endforeach;
												}
												?>
												<tr>
													<td><?php echo $x;?></td>
													<td><?php echo $time;?></td>
													<td><?php echo $payment_method;?></td>
													<td><?php echo $amount_paidd;?></td>
													<td><a href="<?php echo site_url().'accounts/print_single_receipt/'.$payment_id;?>" class="btn btn-small btn-warning" target="_blank"><i class="fa fa-print"></i></a></td>

													<?php
													$authorize_invoice_changes = $this->session->userdata('authorize_invoice_changes');
													$buttons = "";
													if($authorize_invoice_changes)
													{
														?>
														<td>
                                                        	<button type="button" class="btn btn-small btn-default" data-toggle="modal" data-target="#refund_payment<?php echo $payment_id;?>"><i class="fa fa-times"></i></button>
															<!-- Modal -->
															<div class="modal fade" id="refund_payment<?php echo $payment_id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
															    <div class="modal-dialog" role="document">
															        <div class="modal-content">
															            <div class="modal-header">
															            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
															            	<h4 class="modal-title" id="myModalLabel">Cancel payment</h4>
															            </div>
															            <div class="modal-body">
															            	<?php echo form_open("accounts/cancel_payment/".$payment_id.'/'.$visit_id, array("class" => "form-horizontal"));?>
															                <div class="form-group">
															                    <label class="col-md-4 control-label">Action: </label>
															                    
															                    <div class="col-md-8">
															                        <select class="form-control" name="cancel_action_id">
															                        	<option value="">-- Select action --</option>
															                            <?php
															                                if($cancel_actions->num_rows() > 0)
															                                {
															                                    foreach($cancel_actions->result() as $res)
															                                    {
															                                        $cancel_action_id = $res->cancel_action_id;
															                                        $cancel_action_name = $res->cancel_action_name;
															                                        
															                                        echo '<option value="'.$cancel_action_id.'">'.$cancel_action_name.'</option>';
															                                    }
															                                }
															                            ?>
															                        </select>
															                    </div>
															                </div>
															                <input type="hidden" class="form-control" name="redirect_url" placeholder="" autocomplete="off" value="<?php echo $this->uri->uri_string()?>">
															                
															                <div class="form-group">
															                    <label class="col-md-4 control-label">Description: </label>
															                    
															                    <div class="col-md-8">
															                        <textarea class="form-control" name="cancel_description"></textarea>
															                    </div>
															                </div>
															                
															                <div class="row">
															                	<div class="col-md-8 col-md-offset-4">
															                    	<div class="center-align">
															                        	<button type="submit" class="btn btn-primary">Save action</button>
															                        </div>
															                    </div>
															                </div>
															                <?php echo form_close();?>
															            </div>
															            <div class="modal-footer">
															                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
															            </div>
															        </div>
															    </div>
															</div>

                                                        </td>
														<?php
													}

													?>
													

														
                                                  
												</tr>
												<?php
												$total_payments =  $total_payments + $amount_paid;
											}
										endforeach;

										?>
										<tr>
											<td colspan="3"><strong>Total : </strong></td>
											<td><strong> <?php echo number_format($total_payments,2);?></strong></td>
										</tr>
										<?php
									}
									
									else
									{
										?>
										<tr>
											<td colspan="4"> No payments made yet</td>
										</tr>
										<?php
									}
									?>
								</tbody>
							</table>
						</div>
					</section>
				</div>
				<!-- END OF THE SPAN 7 -->
			</div>

		</div>

		<?php
			$payments_value = $this->accounts_model->total_payments($visit_id);

			$invoice_total = $this->accounts_model->total_invoice($visit_id);

			$balance = $this->accounts_model->balance($payments_value,$invoice_total);

			$balance = $invoice_total - $payments_value;


			$total_waiver = $this->accounts_model->get_visit_waiver($visit_id);
		?>
		<div class="row">
	    	<div class="col-md-12 center-align">
	    		<h5><strong> INVOICE: <?php echo number_format($invoice_total+$total_waiver,2)?></strong></h5>
	    		<br>
	    		<h5><strong> WAIVER: <?php echo number_format($total_waiver,2)?></strong></h5>
	    		<br>
	    		<h5><strong> REJECTION: <?php echo number_format($rejected_amount,2)?></strong></h5>
	    		 <h2><strong>BAL: <?php echo number_format(($balance ),2)?></strong></h2>
	        </div>
	    </div>
		<div class="row">
	    	<div class="col-md-12 center-align">
	    	
	    		 <a href="<?php echo site_url();?>accounts/end_visit/<?php echo $visit_id?>" class="btn btn-danger btn-sm  " onclick="return confirm('Do you want to close this visit ?')" ><i class="fa fa-folder"></i> Close this visit </a>

	    		  <?php echo '<a href="'.site_url().'print-sick-off/'.$visit_id.'" target="_blank" class="btn btn-sm btn-warning" >Print Note</a> <a href="'.site_url().'print-prescription/'.$visit_id.'" target="_blank" class="btn btn-sm btn-warning" >Print Prescription</a>';?>


	        </di>
	    </div>
			
	
	</div>
</section>
  <!-- END OF ROW -->
<script type="text/javascript">

 
   $(function() {
       $("#service_id_item").customselect();
       $("#provider_id_item").customselect();
       $("#parent_service_id").customselect();

   });
   $(document).ready(function(){
   		display_patient_bill(<?php echo $visit_id;?>);
   		display_procedure(<?php echo $visit_id;?>);
   });

  
     
  function getservices(id){

        var myTarget1 = document.getElementById("service_div");
        var myTarget2 = document.getElementById("username_div");
        var myTarget3 = document.getElementById("password_div");
        var myTarget4 = document.getElementById("service_div2");
        var myTarget5 = document.getElementById("payment_method");
		
        if(id == 1)
        {
          myTarget1.style.display = 'none';
          myTarget2.style.display = 'none';
          myTarget3.style.display = 'none';
          myTarget4.style.display = 'block';
          myTarget5.style.display = 'block';
        }
        else
        {
          myTarget1.style.display = 'block';
          myTarget2.style.display = 'block';
          myTarget3.style.display = 'block';
          myTarget4.style.display = 'none';
          myTarget5.style.display = 'none';
        }
        
  }
  function check_payment_type(payment_type_id){
   
   
    var myTarget1 = document.getElementById("cheque_div");

    var myTarget2 = document.getElementById("mpesa_div");

    var myTarget3 = document.getElementById("insuarance_div");

    if(payment_type_id == 1)
    {
      // this is a check
     
      myTarget1.style.display = 'block';
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'none';
    }
    else if(payment_type_id == 2)
    {
      myTarget1.style.display = 'none';
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'none';
    }
    else if(payment_type_id == 3)
    {
      myTarget1.style.display = 'none';
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'block';
    }
    else if(payment_type_id == 4)
    {
      myTarget1.style.display = 'none';
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'none';
    }
    else if(payment_type_id == 5)
    {
      myTarget1.style.display = 'none';
      myTarget2.style.display = 'block';
      myTarget3.style.display = 'none';
    }
    else
    {
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'block';  
    }

  }

   function display_patient_bill(visit_id){

      var XMLHttpRequestObject = false;
          
      if (window.XMLHttpRequest) {
      
          XMLHttpRequestObject = new XMLHttpRequest();
      } 
          
      else if (window.ActiveXObject) {
          XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
      }
      
      var config_url = document.getElementById("config_url").value;
      var url = config_url+"accounts/view_patient_bill/"+visit_id;
      // alert(url);
      if(XMLHttpRequestObject) {
                  
          XMLHttpRequestObject.open("GET", url);
                  
          XMLHttpRequestObject.onreadystatechange = function(){
              
              if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                  document.getElementById("patient_bill").innerHTML=XMLHttpRequestObject.responseText;
              }
          }
                  
          XMLHttpRequestObject.send(null);
      }
  }

	//Calculate procedure total
	function calculatetotal(amount, id, procedure_id, v_id){
	       
	    var units = document.getElementById('units'+id).value;  
	    var billed_amount = document.getElementById('billed_amount'+id).value;  
	   // alert(billed_amount);
	    grand_total(id, units, billed_amount, v_id);

	}
	function grand_total(procedure_id, units, amount, v_id){
	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    var config_url = document.getElementById("config_url").value;

	    var url = config_url+"accounts/update_service_total/"+procedure_id+"/"+units+"/"+amount+"/"+v_id;
	    // alert(url);
	    if(XMLHttpRequestObject) {
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) 
				{
	    			// display_patient_bill(v_id);
	    			display_procedure(v_id);
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}
	function delete_service(id, visit_id){

		var res = confirm('Do you want to remove this charge ? ');

		if(res)
		{
			var XMLHttpRequestObject = false;
	        
		    if (window.XMLHttpRequest) {
		    
		        XMLHttpRequestObject = new XMLHttpRequest();
		    } 
		        
		    else if (window.ActiveXObject) {
		        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
		    }
		     var config_url = document.getElementById("config_url").value;
		    var url = config_url+"accounts/delete_service_billed/"+id;
		    
		    if(XMLHttpRequestObject) {
		                
		        XMLHttpRequestObject.open("GET", url);
		                
		        XMLHttpRequestObject.onreadystatechange = function(){
		            
		            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

		                // display_patient_bill(visit_id);
		                display_procedure(visit_id);
		            }
		        }
		                
		        XMLHttpRequestObject.send(null);
		    }
		}
	    
	}
	function save_service_items(visit_id)
	{
		var provider_id = $('#provider_id'+visit_id).val();
		var service_id = $('#service_id'+visit_id).val();
		var visit_date = $('#visit_date_date'+visit_id).val();
		var url = "<?php echo base_url();?>accounts/add_patient_bill/"+visit_id;
		
		$.ajax({
		type:'POST',
		url: url,
		data:{provider_id: provider_id, service_charge_id: service_id, visit_date: visit_date},
		dataType: 'text',
		success:function(data){
			alert("You have successfully billed");
			display_patient_bill(visit_id);
		},
		error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
			display_patient_bill(visit_id);
		}
		});
		return false;
	}



	function parse_procedures(visit_id,suck)
    {
      var procedure_id = document.getElementById("procedure_id").value;
       procedures(procedure_id, visit_id, suck);
      
    }

	function procedures(id, v_id, suck){
       
        var XMLHttpRequestObject = false;
            
        if (window.XMLHttpRequest) {
        
            XMLHttpRequestObject = new XMLHttpRequest();
        } 
            
        else if (window.ActiveXObject) {
            XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
        }
        
        var url = "<?php echo site_url();?>nurse/procedure/"+id+"/"+v_id+"/"+suck;
       
         if(XMLHttpRequestObject) {
                    
            XMLHttpRequestObject.open("GET", url);
                    
            XMLHttpRequestObject.onreadystatechange = function(){
                
                if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                    document.getElementById("procedures").innerHTML=XMLHttpRequestObject.responseText;
                }
            }
                    
            XMLHttpRequestObject.send(null);
        }

    }
    function display_procedure(visit_id){

	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    
	    var config_url = document.getElementById("config_url").value;
	    // var url = config_url+"nurse/view_procedure/"+visit_id;
	    var url = config_url+"accounts/view_procedure/"+visit_id;

	    if(XMLHttpRequestObject) {
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                document.getElementById("procedures").innerHTML=XMLHttpRequestObject.responseText;
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}
	function delete_procedure(id, visit_id){
	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	     var config_url = document.getElementById("config_url").value;
	    var url = config_url+"nurse/delete_procedure/"+id;
	    
	    if(XMLHttpRequestObject) {
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                display_procedure(visit_id);
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}

	$(document).on("change","select#visit_type_id",function(e)
	{
		var visit_type_id = $(this).val();
		
		if(visit_type_id != '1')
		{
			$('#insured_company').css('display', 'block');
			// $('#consultation').css('display', 'block');
		}
		else
		{
			$('#insured_company').css('display', 'none');
			// $('#consultation').css('display', 'block');
		}
		
		


	});

 
</script>
