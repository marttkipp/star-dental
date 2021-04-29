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
<?php echo $this->load->view('search/search_patients', '', TRUE);?>
<!-- end search -->
 
 <section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?> for <?php echo date('jS M Y',strtotime(date('Y-m-d')));?></h2>
        <?php

        $personnel_idd = $this->session->userdata('personnel_id');
        $is_receptionist = $this->reception_model->check_if_admin($personnel_idd,2);
		if($is_receptionist OR $personnel_idd == 0)
		{
	        ?>
	       	 <a href="<?php echo site_url();?>patients" class="btn btn-sm btn-warning pull-right" style="margin-top:-25px;"><i class="fa fa-plus"></i> QUEUE NEW PATIENT</a>
	        <?php
    	}
        ?>
    </header>
      <div class="panel-body">
          <div class="padd">
          
<?php
		$search = $this->session->userdata('general_queue_search');
		
		if(!empty($search))
		{
			echo '<a href="'.site_url().'reception/close_general_queue_search" class="btn btn-warning">Close Search</a>';
		}
		$result = '';
		$queue_one = '';
		$queue_two = '';
		// var_dump($query->num_rows()); die();
		//if users exist display them
		$count_one = 0;
		$count_two = 0;
		if ($query->num_rows() > 0)
		{
			$count = $page;
				
			
			
			$result .= 
				'
					<table class="table table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Patient No</th>
						  <th>Patient</th>
						  <th>Appointment Time</th>
						  <th>Time In Clinic</th>
						  <th>Insurance/Cash</th>
						  <th>Scheme</th>
						  <th>Doctor</th>
						  <th>Department</th>
						  <th colspan="6">Actions</th>
						</tr>
					  </thead>
					  <tbody>
				';
			
			$personnel_query = $this->personnel_model->get_all_personnel();
			
			foreach ($query->result() as $row)
			{
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
				$visit_created = date('H:i a',strtotime($row->visit_created));
				$visit_id = $row->visit_id;
				$patient_id = $row->patient_id;
				$past_visit_date = $row->visit_date;
				$personnel_id3 = $row->personnel_id;
				$insurance_description = $row->insurance_description;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				$accounts = $row->accounts;
				$visit_table_visit_type = $visit_type;
				$patient_table_visit_type = $visit_type_id;
				$coming_from = $this->reception_model->coming_from($visit_id);
				$sent_to = $this->reception_model->going_to($visit_id);
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$appointment_id = $row->appointment_id;
				$patient_number = $row->patient_number;
				$current_patient_number = $row->current_patient_number;
				$patient_surname = $row->patient_surname;
				$close_card = $row->close_card;
				$room_name = $row->room_name;
				$time_start = $row->time_start;

				$patient_year = $row->patient_year;
				$patient_date_of_birth = $row->patient_date_of_birth;

				if($appointment_id == 1)
				{
					$appointment_time = $time_start;
				}
				else
				{
					$appointment_time = '-';
				}



				//creators and editors
				if($personnel_query->num_rows() > 0)
				{
					$personnel_result = $personnel_query->result();
					
					foreach($personnel_result as $adm)
					{
						$personnel_id2 = $adm->personnel_id;
						
						if($personnel_id3 == $personnel_id2)
						{
							$doctor = 'Dr. '.$adm->personnel_onames;
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
				
				//cash paying patient sent to department but has to pass through the accounts

				if($close_card != 0)
				{
					$highlight = 'warning';
				}
				else
				{
					$highlight ='';
				}
				if($close_card == 0)
				{
					$color = 'danger';
				}
				else if($close_card == 4)
				{
					$color = 'default';
				}
				else
				{
					$color = 'success';
				}
				
				$v_data = array('visit_id'=>$visit_id);
				$count++;

				$personnel_id = $this->session->userdata('personnel_id');
				$is_dentist = $this->reception_model->check_if_admin($personnel_id,1);
				$is_assitant = $this->reception_model->check_if_admin($personnel_id,6);

				if($is_dentist OR $is_assitant)
				{
					$display = '<td><a href="'.site_url().'patient-card/'.$visit_id.'" class="btn btn-sm btn-success" \> Card </a></td>
							<td><button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#book-appointment'.$visit_id.'"><i class="fa fa-plus"></i> Appointment </button>
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

														<div class="form-group">
														  <label class="col-lg-4 control-label">Room: </label>	
															<div class="col-lg-8">
																	<select name="room_id" id="room_id'.$visit_id.'" class="form-control" >
																		<option value="">----Select Room----</option>
																		 '.$all_wards.'
																	</select>
															</div>
														</div>
														

														 <div class="form-group">
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
							                                    
							                                    <div class="form-group">
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
							                                        
							                                    <div class="form-group" >
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

							</td>';

				}

				// cashier
				$is_cashier = $this->reception_model->check_if_admin($personnel_id,5);

				// if($is_cashier)
				// {
				// 	$display = '<td><a href="'.site_url().'receipt-payment/'.$visit_id.'/0" class="btn btn-sm btn-warning"> Payments </a></td>
				// 				';

				// }

				if($sent_to == "Accounts" AND $is_cashier)
				{
					$button_accounts = '<td><a href="'.site_url().'receipt-payment/'.$visit_id.'/0" class="btn btn-sm btn-warning"> Payments </a></td>
										<td><a href="'.site_url().'reception/end_visit/'.$visit_id.'" class="btn btn-sm btn-info" onclick="return confirm(\'Do you really want to end this visit ?\');">End Visit</a></td>';
				}
				else
				{
					// $button_accounts = '';
					$button_accounts = '<td><a href="'.site_url().'receipt-payment/'.$visit_id.'/0" class="btn btn-sm btn-warning"> Payments </a></td>';
				}
				$is_receptionist = $this->reception_model->check_if_admin($personnel_id,5);


				if($is_receptionist)
				{
					$display = '
								<td>'.$button_accounts.'</td>
								<td><button type="button" class="btn btn-sm btn-success pull-right" data-toggle="modal" data-target="#book-appointment'.$visit_id.'"><i class="fa fa-plus"></i> Appointment </button>
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
							                                    
							                                    <div class="form-group">
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
							                                        
							                                    <div class="form-group" >
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
								<td><a href="'.site_url().'reception/edit_visit/'.$visit_id.'" class="btn btn-sm btn-primary"> Edit </a></td>
								<td><a href="'.site_url().'reception/delete_visit/'.$visit_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete this visit?\');">Delete</a></td>
								';

				}


				$is_admin = $this->reception_model->check_if_admin($personnel_id,3);

				if($is_admin OR $personnel_id == 0)
				{
					$display = '
								<td><a href="'.site_url().'patient-card/'.$visit_id.'" class="btn btn-sm btn-success" \> Card </a></td>
								<td><a href="'.site_url().'receipt-payment/'.$visit_id.'/0" class="btn btn-sm btn-warning"> Payments </a></td>
								<td><button type="button" class="btn btn-sm btn-success pull-right" data-toggle="modal" data-target="#book-appointment'.$visit_id.'"><i class="fa fa-plus"></i> Appointment </button>
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
							                                    
							                                    <div class="form-group">
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
							                                        
							                                    <div class="form-group" >
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
								<td><a href="'.site_url().'reception/end_visit/'.$visit_id.'" class="btn btn-sm btn-info" onclick="return confirm(\'Do you really want to end this visit ?\');">End Visit</a></td>
								<td><a href="'.site_url().'reception/edit_visit/'.$visit_id.'" class="btn btn-sm btn-primary"> Edit </a></td>';

				}


				
				$buttons = $display;
					
			
				if(empty($insurance_description))
				{
					$insurance_description = '-';
				}

				$count_two++;
				$queue_two .= '<tr >
									<td>'.$count.'</td>
									<td>'.$patient_number.'</td>
									<td class="'.$color.'">'.$patient_surname.' '.$patient_othernames.'</td>
									<td>'.$appointment_time.'</td>
									<td>'.$visit_time.'</td>
									<td>'.$visit_type_name.' </td>
									<td>'.$insurance_description.'</td>
									<td>'.$doctor.'</td>
									<td>'.$sent_to.'</td>
									'.$buttons.'
								</tr> ';
				
			
			}
			$result .= $queue_two;
			
			$result .= 
			'
						  </tbody>
						</table>
			';
		}
		
		else
		{
			$result .= "There are no patients";
		}
		
?>
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
		echo $result;
		?>
          </div>
          
          <div class="widget-foot">
                                
				<?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        </div>
        <!-- Widget ends -->
       

  </section>

<script type="text/javascript">
	

	function check_date(visit_id){
	     var datess=document.getElementById("scheduledate"+visit_id).value;
	     var doctor_id=document.getElementById("doctor_id"+visit_id).value;

	    
	     if(datess && doctor_id){
	     	load_schedule(visit_id);
	     	load_patient_appointments_two(visit_id);
		  $('#show_doctor').fadeToggle(1000); return false;
		 }
		 else{
		  alert('Select Date and a Doctor First')
		 }
	}

	function load_schedule(visit_id){
		var config_url = $('#config_url').val();
		var datess=document.getElementById("scheduledate"+visit_id).value;
		var doctor= document.getElementById("doctor_id"+visit_id).value;

		var url= config_url+"reception/doc_schedule/"+doctor+"/"+datess;
		
		  $('#doctors_schedule'+visit_id).load(url);
		  $('#doctors_schedule'+visit_id).fadeIn(1000); return false;	
	}
	function load_patient_appointments(visit_id){
		var patient_id = $('#patient_id'+visit_id).val();
		var current_date = $('#current_date'+visit_id).val();

		var url= config_url+"reception/patient_schedule/"+patient_id+"/"+current_date;
		
		$('#patient_schedule'+visit_id).load(url);
		$('#patient_schedule'+visit_id).fadeIn(1000); return false;	

		$('#patient_schedule2'+visit_id).load(url);
		$('#patient_schedule2'+visit_id).fadeIn(1000); return false;	
	}
	function load_patient_appointments_two(visit_id){
		var patient_id = $('#patient_id'+visit_id).val();
		var current_date = $('#current_date'+visit_id).val();

		var url= config_url+"reception/patient_schedule/"+patient_id+"/"+current_date;
		
		$('#patient_schedule2'+visit_id).load(url);
		$('#patient_schedule2'+visit_id).fadeIn(1000); return false;	
	}
	function schedule_appointment(appointment_id)
	{
		if(appointment_id == '1')
		{
			$('#appointment_details').css('display', 'block');
		}
		else
		{
			$('#appointment_details').css('display', 'none');
		}
	}

	function submit_appointment(visit_id,patient_id)
	{
		var config_url = document.getElementById("config_url").value;

        var data_url = config_url+"reception/save_appointment_accounts/"+patient_id+"/"+visit_id;


		var visit_date = $('#scheduledate'+visit_id).val();   
       	var doctor_id = $('#doctor_id'+visit_id).val(); 
       	var timepicker_start = $('#timepicker_start'+visit_id).val(); 
       	var timepicker_end = $('#timepicker_end'+visit_id).val();   
       	var procedure_done = $('#procedure_done'+visit_id).val(); 
       	var room_id = $('#room_id'+visit_id).val(); 
       	var url_redirect = $('#redirect_url'+visit_id).val(); 

		$.ajax({
	    type:'POST',
	    url: data_url,
	    data:{visit_date: visit_date,doctor_id: doctor_id, timepicker_start: timepicker_start, timepicker_end: timepicker_end, procedure_done: procedure_done, room_id: room_id},
	    dataType: 'text',
	    success:function(data){

	    	window.location = config_url+''+url_redirect;
	    },
	    error: function(xhr, status, error) {

	   		 alert(error);
	    }

	    });
	}

</script>