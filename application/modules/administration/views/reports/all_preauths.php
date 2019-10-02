<?php
$all_wards = '';
if($rooms->num_rows() >  0){
	foreach($rooms->result() as $row):
		$room_name = $row->room_name;
		$room_id = $row->room_id;
		
		if($room_id == set_value('room_id'))
		{
			$all_wards .="<option value='".$room_id."' selected='selected'>".$room_name."</option>";
		}
		
		else
		{
			$all_wards .="<option value='".$room_id."'>".$room_name."</option>";
		}
	endforeach;
}

$all_doctors ='';
if($doctors->num_rows() >  0){
	foreach($doctors->result() as $row):
		$fname = $row->personnel_fname;
		$onames = $row->personnel_onames;
		$personnel_id = $row->personnel_id;
		
		if($personnel_id == set_value('personnel_id'))
		{
			$all_doctors .="<option value='".$personnel_id."' selected='selected'>".$onames." ".$fname."</option>";
		}
		
		else
		{
			$all_doctors .="<option value='".$personnel_id."'>".$onames." ".$fname."</option>";
		}
	endforeach;
}
?>
<!-- search -->
<?php echo $this->load->view('search/preauths', '', TRUE);?>
<!-- end search -->
<?php //echo $this->load->view('transaction_statistics', '', TRUE);?>

<div class="row">
    <div class="col-md-12">

        <section class="panel panel-warning">
            <header class="panel-heading">
            	 <h2 class="panel-title"><?php echo $title;?></h2>

            	 <a href="<?php echo site_url().'download-preauths'?>" target="_blank"  class="btn btn-sm btn-success pull-right" style="margin-top:-25px;" > Download preauths</a>
            </header>

          <!-- Widget content -->
                <div class="panel-body">
<?php
		$result = '';
		if(!empty($search))
		{
			echo '<a href="'.site_url().'administration/reports/close_preauth_search" class="btn btn-sm btn-warning">Close Search</a>';
		}

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

		$method_rs = $this->accounts_model->get_payment_methods();

		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;

			$result .=
				'
					<table class="table  table-bordered table-striped table-responsive col-md-12">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Preauth Date</th>
						  <th>Invoice</th>
						  <th>Patient</th>
						  <th>Phone</th>
						  <th>Category</th>
						  <th>Doctor</th>
						  <th>Preauth AMT</th>
						  <th colspan="3"></th>
						</tr>
					  </thead>
					  <tbody>
			';

			$personnel_query = $this->personnel_model->get_all_personnel();

			foreach ($query->result() as $row)
			{
				$total_invoiced = 0;
				$visit_date = date('jS M Y',strtotime($row->visit_date));
				$visit_time = date('H:i a',strtotime($row->visit_time));
				if($row->visit_time_out != '0000-00-00 00:00:00')
				{
					$visit_time_out = date('H:i a',strtotime($row->visit_time_out));
				}
				else
				{
					$visit_time_out = '-';
				}
				$past_visit_date = $row->visit_date;
				$visit_id = $row->visit_id;
				$patient_id = $row->patient_id;
				$personnel_id = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				$visit_table_visit_type = $visit_type;
				$invoice_number = $visit_id;//$row->invoice_number;
				$patient_table_visit_type = $visit_type_id;
				$coming_from = $this->reception_model->coming_from($visit_id);
				$sent_to = $this->reception_model->going_to($visit_id);
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_phone1 = $row->patient_phone1;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$close_card = $row->close_card;
				$hold_card = $row->hold_card;
				$invoice_number = $row->invoice_number;
				$visit_type_id = $row->visit_type;
				$parent_visit = $row->parent_visit;
				$rejected_amount = $row->rejected_amount;
				$preauth = $row->preauth;

				// this is to check for any credit note or debit notes



				//creators and editors
				if($personnel_query->num_rows() > 0)
				{
					$personnel_result = $personnel_query->result();

					foreach($personnel_result as $adm)
					{
						$personnel_id2 = $adm->personnel_id;

						if($personnel_id == $personnel_id2)
						{
							$doctor = $adm->personnel_onames.' '.$adm->personnel_fname;
							break;
						}

						else
						{
							$doctor = '-';
						}
					}
				}

				else
				{
					$doctor = '-';
				}

				$count++;

				//payment data
				$cash = $this->reports_model->get_all_visit_payments($visit_id);
				$charges = '';

				foreach($services_query->result() as $service)
				{
					$service_id = $service->service_id;
					$visit_charge = $this->reports_model->get_all_visit_charges($visit_id, $service_id);
					$total_invoiced += $visit_charge;

					//$charges .= '<td>'.$visit_charge.'</td>';
				}
				if($hold_card == 1)
				{
					$button ='<td><a href="'.site_url().'reception/unhold_card/'.$visit_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to unhold this card?\');">Unhold Card</a></td>';
				}
				else
				{
					if($close_card == 1)
					{
						$button ='<td><a href="'.site_url().'accounts/print_invoice_new/'.$visit_id.'" class="btn btn-sm btn-success" target="_blank">Invoice</a></td>
								 <td><a href="'.site_url().'administration/reports/open_visit_current/'.$visit_id.'"  onclick="return confirm(\'Do you want to open card ?\');" class="btn btn-sm btn-info" >Open Card</a></td>';
					}
					else
					{
						$button ='<td><a href="'.site_url().'administration/reports/end_visit_current/'.$visit_id.'"  onclick="return confirm(\'Do you want to close visit ?\');" class="btn btn-sm btn-danger" >Close Card</a></td>';
					}
				}
				// payment value ///

				// var_dump($parent_visit); die();



				$payments_value = $this->accounts_model->total_payments($visit_id);
				$invoice_total = $amount_payment  = $this->accounts_model->total_invoice($visit_id);

				// end of the debit and credit notes

				$balance = $this->accounts_model->balance($payments_value,$invoice_total);

				$rs_rejection = $this->dental_model->get_visit_rejected_updates_sum($visit_id,$visit_type);
				$total_rejected = 0;
				if(count($rs_rejection) >0){
				  foreach ($rs_rejection as $r2):
				    # code...
				    $total_rejected = $r2->total_rejected;

				  endforeach;
				}

				$rejected_amount += $total_rejected;

				if($visit_type_id > 1 AND $rejected_amount > 0)
				{

				}



				// if($parent_visit == 0 OR empty($parent_visit))
				// {
				// 	$invoice_total = $amount_payment - $rejected_amount;
				// }
				// else
				// {
				// 	$rejected_amount = $this->accounts_model->get_child_amount_payable($visit_id);
				// 	$invoice_total = $rejected_amount;
				// }


				// if($parent_visit == 0 OR empty($parent_visit))
				// {
				// 	$balance = $invoice_total - $payments_value;
				// }
				// else
				// {
				// 	$rejected_amount = $this->accounts_model->get_child_amount_payable($visit_id);
				// 	// echo $rejected_amount; die();
				// 	$balance = $rejected_amount - $payments_value;



				// }

				// $invoice_total = $invoice_total - $rejected_amount;


					// echo $invoice_total; die();
				if($visit_type > 1 AND $total_rejected > 0)
				{
					$payable_by_patient = $rejected_amount;
					$payable_by_insurance = $invoice_total - $rejected_amount;
				}
				else if($visit_type > 1 AND $total_rejected == 0 OR empty($total_rejected))
				{
					$payable_by_patient = 0;
					$payable_by_insurance = $invoice_total;
				}
				else
				{
					$payable_by_patient = $invoice_total;
					$payable_by_insurance = 0;
				}

				if($preauth == 2)
				{
					$color = 'success';
				}
				else
				{
					$color = 'warning';
				}

				$is_dentist = $this->reception_model->check_if_admin($personnel_id,1);
				$is_assitant = $this->reception_model->check_if_admin($personnel_id,6);

				if($is_dentist OR $is_assitant)
				{
					$buttons = '<td><a href="'.site_url().'patient-card/'.$visit_id.'/2" class="btn btn-sm btn-success" \> Open Card </a></td>';
				}
				else
				{
					$buttons == '';
				}
					$result .=
						'
							<tr>
								<td class="'.$color.'">'.$count.'</td>
								<td class="'.$color.'">'.$visit_date.'</td>
								<td class="'.$color.'">'.$invoice_number.'</td>
								<td class="'.$color.'">'.$patient_surname.' '.$patient_othernames.'</td>
								<td>'.$patient_phone1.'</td>
								<td>'.$visit_type_name.'</td>
								<td>'.$doctor.'</td>
								<td>'.number_format($invoice_total,2).'</td>
								'.$buttons.'
								<td><button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#book-appointment'.$visit_id.'"><i class="fa fa-plus"></i> Appointment </button>
									<div class="modal fade " id="book-appointment'.$visit_id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									    <div class="modal-dialog modal-lg" role="document">
									        <div class="modal-content ">
									            <div class="modal-header">
									            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									            	<h4 class="modal-title" id="myModalLabel">Schedule Appointment for '.$patient_surname.'</h4>
									            </div>
									            '.form_open("reception/save_appointment_accounts/".$patient_id."/".$visit_id, array("class" => "form-horizontal")).'

									            <div class="modal-body">
									            	<div class="row">
									            		<input type="hidden" name="redirect_url" id="redirect_url'.$visit_id.'" value="'.$this->uri->uri_string().'">
									            		<input type="hidden" name="patient_id" id="patient_id'.$visit_id.'" value="'.$patient_id.'">
									            		<input type="hidden" name="current_date" id="current_date'.$visit_id.'" value="'.$past_visit_date.'">
									            		<div class="col-md-12">
									            			<div class="col-md-6">
									            				<div class="form-group">
																<label class="col-lg-4 control-label">Visit date: </label>
																
																<div class="col-lg-8">
							                                        <div class="input-group">
							                                            <span class="input-group-addon">
							                                                <i class="fa fa-calendar"></i>
							                                            </span>
							                                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date" id="scheduledate'.$visit_id.'" placeholder="Visit Date" value="'.date('Y-m-d').'" required>
							                                        </div>
																</div>
															</div>

															<div class="form-group" >
															  <label class="col-lg-4 control-label">Room: </label>	
																<div class="col-lg-8">
																		<select name="room_id" id="room_id'.$visit_id.'" class="form-control" >
																			<option value="">----Select Room----</option>
																			 '.$all_wards.'
																		</select>
																</div>
															</div>
															

															 <div class="form-group" >
											                        <label class="col-lg-4 control-label">Procedure to be done</label>
											                        <div class="col-lg-8">
											                        	<textarea class="form-control" name="procedure_done" id="procedure_done'.$visit_id.'"></textarea>
											                           
											                       </div>
								                             </div>  
															
							                                	
									            			</div>
									            			<div class="col-md-6">
									            				<div class="form-group">
																	<label class="col-lg-4 control-label">Doctor: </label>
																	<div class="col-lg-8">
																		 <select name="doctor_id" id="doctor_id'.$visit_id.'" class="form-control">
																			<option value="">----Select a Doctor----</option>
																			 '.$all_doctors.'
																		</select>
																	</div>
																</div>
									            				<div id="appointment_details" >
								                                    <div class="form-group">
								                                        <label class="col-lg-4 control-label">Schedule: </label>
								                                        
								                                        <div class="col-lg-8">
								                                            <a onclick="check_date('.$visit_id.')" style="cursor:pointer;">[Show Doctors Schedule]</a><br>
								                                            <div id="show_doctor'.$visit_id.'" style="display:none;"> 
								                                                
								                                            </div>
								                                            <div  id="doctors_schedule'.$visit_id.'" style="margin-left: -94px;font-size: 10px;"> </div>
								                                        </div>
								                                    </div>
								                                    
								                                    <div class="form-group" style="display:none;">
								                                        <label class="col-lg-4 control-label">Start time : </label>
								                                    
								                                        <div class="col-lg-8">
								                                            <div class="input-group">
								                                                <span class="input-group-addon">
								                                                    <i class="fa fa-clock-o"></i>
								                                                </span>
								                                                <input type="text" class="form-control" data-plugin-timepicker="" name="timepicker_start" id="timepicker_start'.$visit_id.'">
								                                            </div>
								                                        </div>
								                                    </div>
								                                        
								                                    <div class="form-group" style="display:none;">
								                                        <label class="col-lg-4 control-label">End time : </label>
								                                        
								                                        <div class="col-lg-8">		
								                                            <div class="input-group">
								                                                <span class="input-group-addon">
								                                                    <i class="fa fa-clock-o"></i>
								                                                </span>
								                                                <input type="text" class="form-control" data-plugin-timepicker="" name="timepicker_end" id="timepicker_end'.$visit_id.'">
								                                            </div>
								                                        </div>
								                                    </div>
								                                </div>
									            			</div>
									            		</div>
									            	</div>
									            	
															
									              	
									            </div>
									            <div class="modal-footer">
									            	<a  class="btn btn-sm btn-success" onclick="submit_appointment('.$visit_id.','.$patient_id.')">Schedule Appointment</a>
									                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
									            </div>

									               '.form_close().'
									        </div>
									    </div>
									</div>

								</td>

								<td><a href="'.site_url().'receipt-payment/'.$visit_id.'/2" class="btn btn-sm btn-warning" >Payments</a></td>


							</tr>
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
			$result .= "There are no visits";
		}

		echo $result;
?>
          </div>

          <div class="widget-foot">

				<?php if(isset($links)){echo $links;}?>

                <div class="clearfix"></div>

            </div>

		</section>
    </div>
  </div>
  <script type="text/javascript">

  	function check_payment_type(visit_id){

   		var payment_type_id = $('#payment_type_id'+visit_id).val();

   		// alert(payment_type_id);
	    var myTarget1 = document.getElementById("cheque_div"+visit_id);

	    var myTarget2 = document.getElementById("mpesa_div"+visit_id);

	    var myTarget3 = document.getElementById("insuarance_div"+visit_id);

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

  	WWW}
  </script>
