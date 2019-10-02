<!-- search -->
<?php echo $this->load->view('patients/search_patient', '', TRUE);?>
<!-- end search -->

<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?></h2>
        <div class="pull-right">
        	<?php
        	echo '
			<a href="'.site_url().'reception/import-patients" class="btn btn-primary  btn-sm " style="margin-left:10px; margin-top:-40px;">Import Patients</a>
			
			<a href="'.site_url().'add-patient" class="btn btn-success btn-sm" style="margin-top:-40px;">Add Patient</a>
			';
        	?>
        	
        </div>
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
				
		$search = $this->session->userdata('patient_search');
		
		if(!empty($search))
		{
			echo '
			<a href="'.site_url().'reception/close_patient_search" class="btn btn-warning btn-sm ">Close Search</a>
			';
		}
		
		
		
		if($delete != 1)
		{
			$result = '
				';
		}
		
		else
		{
			$result = '';
		}
		
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			$result .= 
				'	
					<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Patient Number</th>
						  <th>Patient name</th>
						  <th>Contact details</th>
						  <th>Last Visit</th>
						  <th>Doctor</th>
						  <th>Cash Balance</th>
						  <th>Insurance Balance</th>
						  <th colspan="3">Actions</th>
						</tr>
					  </thead>
					  <tbody>
				';
			
			$personnel_query = $this->personnel_model->get_all_personnel();
			
			foreach ($query->result() as $row)
			{

				$patient_id = $row->patient_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$created_by = $row->created_by;
				$modified_by = $row->modified_by;
				$deleted_by = $row->deleted_by;
				$visit_type_id = $row->visit_type_id;
				$created = $row->patient_date;
				$last_modified = $row->last_modified;
				$patient_year = $row->patient_year;
				$last_visit = $row->last_visit;
				$patient_phone1 = $row->patient_phone1;
				$patient_number = $row->patient_number;
				$current_patient_number = $row->current_patient_number;
				$patient = $this->reception_model->patient_names2($patient_id);
				$patient_type = $patient['patient_type'];
				$patient_othernames = $patient['patient_othernames'];
				$patient_surname = $patient['patient_surname'];
				$patient_type_id = $patient['visit_type_id'];
				$account_balance = $patient['account_balance'];
				$last_visit = $row->last_visit;
				$last_visit_date = $row->last_visit;
				//$card_no = $row->card_no;
				$patient_phone1 = $row->patient_phone1;
				$patient_number = $row->patient_number;
				if($last_visit != NULL)
				{
					$last_visit = date('jS M Y',strtotime($last_visit));
				}
				
				else
				{
					$last_visit = '';
				}
				

				$patient_type = $patient['patient_type'];
				$patient_othernames = $patient['patient_othernames'];
				$patient_surname = $patient['patient_surname'];
				$patient_date_of_birth = $patient['patient_date_of_birth'];
				$gender = $patient['gender'];
				
				//creators and editors
				if($personnel_query->num_rows() > 0)
				{
					$personnel_result = $personnel_query->result();
					
					foreach($personnel_result as $adm)
					{
						$personnel_id = $adm->personnel_id;
						
						if($personnel_id == $created_by)
						{
							$created_by = $adm->personnel_fname;
						}
						
						if($personnel_id == $modified_by)
						{
							$modified_by = $adm->personnel_fname;
						}
						
						if($personnel_id == $modified_by)
						{
							$modified_by = $adm->personnel_fname;
						}
						
						if($personnel_id == $deleted_by)
						{
							$deleted_by = $adm->personnel_fname;
						}
					}
				}
				
				else
				{
					$created_by = '-';
					$modified_by = '-';
					$deleted_by = '-';
				}
				$insurance_company = $this->reception_model->get_patient_insurance_company($patient_id);

				$personnel_id = $this->reception_model->get_last_personnel_id($patient_id,$last_visit_date);
				if($personnel_query->num_rows() > 0)
				{
					$personnel_result = $personnel_query->result();
					
					foreach($personnel_result as $adm)
					{
						$personnel_id2 = $adm->personnel_id;
						
						if($personnel_id == $personnel_id2)
						{
							$doctor = $adm->personnel_fname;
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

				$cash_balance = $this->accounts_model->get_cash_balance($patient_id);
				$insurance_balance = $this->accounts_model->get_insurance_balance($patient_id);
				$count++;
				
				

					$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$patient_number.'</td>
							<td>'.$patient_surname.' '.$patient_othernames.'</td>
							<td>'.$patient_phone1.'</td>							
							<td>'.$last_visit.'</td>
							<td>'.$doctor.'</td>
							<td>'.number_format($cash_balance,2).'</td>
							<td>'.number_format($insurance_balance,2).'</td>
							<td><a href="'.site_url().'reception/set_visit/'.$patient_id.'" class="btn btn-sm btn-info">Queue </a></td>
							<td><a href="'.site_url().'reception/edit_patient/'.$patient_id.'" class="btn btn-sm btn-warning">Edit </a></td>
							<td><button type="button" class="btn btn-sm btn-success pull-right" data-toggle="modal" data-target="#book-appointment'.$patient_id.'"><i class="fa fa-plus"></i> Appointment </button>
								<div class="modal fade " id="book-appointment'.$patient_id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
								    <div class="modal-dialog modal-lg" role="document">
								        <div class="modal-content ">
								            <div class="modal-header">
								            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								            	<h4 class="modal-title" id="myModalLabel">Schedule Appointment for '.$patient_surname.'</h4>
								            </div>
								            '.form_open("reception/save_appointment_accounts/".$patient_id, array("class" => "form-horizontal")).'

								            <div class="modal-body">
								            	<div class="row">
								            		<input type="hidden" name="redirect_url" id="redirect_url'.$patient_id.'" value="'.$this->uri->uri_string().'">
								            		<input type="hidden" name="patient_id" id="patient_id'.$patient_id.'" value="'.$patient_id.'">
								            		<input type="hidden" name="current_date" id="current_date'.$patient_id.'" value="'.date('Y-m-d').'">
								            		<div class="col-md-12">
								            			<div class="col-md-6">
								            				<div class="form-group">
															<label class="col-lg-4 control-label">Visit date: </label>
															
															<div class="col-lg-8">
						                                        <div class="input-group">
						                                            <span class="input-group-addon">
						                                                <i class="fa fa-calendar"></i>
						                                            </span>
						                                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date" id="scheduledate'.$patient_id.'" placeholder="Visit Date" value="'.date('Y-m-d').'" required>
						                                        </div>
															</div>
														</div>
														
														<div class="form-group">
														  <label class="col-lg-4 control-label">Room: </label>	
															<div class="col-lg-8">
																	<select name="room_id" id="room_id'.$patient_id.'" class="form-control" >
																		<option value="">----Select Room----</option>';
																		 if($rooms->num_rows() >  0){
																			foreach($rooms->result() as $row):
																				$room_name = $row->room_name;
																				$room_id = $row->room_id;
																				
																				if($room_id == set_value('room_id'))
																				{
																					$result .="<option value='".$room_id."' selected='selected'>".$room_name."</option>";
																				}
																				
																				else
																				{
																					$result .="<option value='".$room_id."'>".$room_name."</option>";
																				}
																			endforeach;
																		}
																	$result .='</select>
															</div>
														</div>
														

														 <div class="form-group">
										                        <label class="col-lg-4 control-label">Procedure to be done</label>
										                        <div class="col-lg-8">
										                        	<textarea class="form-control" name="procedure_done" id="procedure_done'.$patient_id.'"></textarea>
										                           
										                       </div>
							                             </div>  
														
						                                	
								            			</div>
								            			<div class="col-md-6">
								            				<div class="form-group">
																<label class="col-lg-4 control-label">Doctor: </label>
																<div class="col-lg-8">
																	 <select name="doctor_id" id="doctor_id'.$patient_id.'" class="form-control">
																		<option value="">----Select a Doctor----</option>';
																		 if($doctors->num_rows() >  0){
																			foreach($doctors->result() as $row):
																				$fname = $row->personnel_fname;
																				$onames = $row->personnel_onames;
																				$personnel_id = $row->personnel_id;
																				
																				if($personnel_id == set_value('personnel_id'))
																				{
																					$result .="<option value='".$personnel_id."' selected='selected'>".$onames." ".$fname."</option>";
																				}
																				
																				else
																				{
																					$result .="<option value='".$personnel_id."'>".$onames." ".$fname."</option>";
																				}
																			endforeach;
																		}
																	$result .='
																	</select>
																</div>
															</div>
								            				<div id="appointment_details" >
							                                    <div class="form-group">
							                                        <label class="col-lg-4 control-label">Schedule: </label>
							                                        
							                                        <div class="col-lg-8">
							                                            <a onclick="check_date('.$patient_id.')" style="cursor:pointer;">[Show Doctors Schedule]</a><br>
							                                            <div id="show_doctor'.$patient_id.'" style="display:none;"> 
							                                                
							                                            </div>
							                                            <div  id="doctors_schedule'.$patient_id.'" style="margin-left: -94px;font-size: 10px;"> </div>
							                                        </div>
							                                    </div>
							                                    
							                                    <div class="form-group">
							                                        <label class="col-lg-4 control-label">Start time : </label>
							                                    
							                                        <div class="col-lg-8">
							                                            <div class="input-group">
							                                                <span class="input-group-addon">
							                                                    <i class="fa fa-clock-o"></i>
							                                                </span>
							                                                <input type="text" class="form-control" data-plugin-timepicker="" name="timepicker_start" id="timepicker_start'.$patient_id.'">
							                                            </div>
							                                        </div>
							                                    </div>
							                                        
							                                    <div class="form-group">
							                                        <label class="col-lg-4 control-label">End time : </label>
							                                        
							                                        <div class="col-lg-8">		
							                                            <div class="input-group">
							                                                <span class="input-group-addon">
							                                                    <i class="fa fa-clock-o"></i>
							                                                </span>
							                                                <input type="text" class="form-control" data-plugin-timepicker="" name="timepicker_end" id="timepicker_end'.$patient_id.'">
							                                            </div>
							                                        </div>
							                                    </div>
							                                </div>
								            			</div>
								            		</div>
								            	</div>
								            	
														
								              	
								            </div>
								            <div class="modal-footer">
								            	<a  class="btn btn-sm btn-success" onclick="submit_reception_appointment('.$patient_id.')">Schedule Appointment</a>
								                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
								            </div>

								               '.form_close().'
								        </div>
								    </div>
								</div>

							</td>
							<td><a href="'.site_url().'administration/individual_statement/'.$patient_id.'/1" class="btn btn-primary btn-sm " style="margin-top:0px"><i class="fa fa-print"></i> Statement </a></td>
							
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
			$result .= "There are no patients";
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

      </div>
    </section>

<script type="text/javascript">
	function get_visit_type(patient_id)
	{
		var visit_type_id = document.getElementById("visit_type_id2"+patient_id).value;

		
		if(visit_type_id != 1)
		{
			$('#insured_company2'+patient_id).css('display','block');
		}
		else
		{
			$('#insured_company2'+patient_id).css('display', 'none');
		}
		
		
	}

</script>



<script type="text/javascript">
	

	function check_date(patient_id){
	     var datess=document.getElementById("scheduledate"+patient_id).value;
	     var doctor_id=document.getElementById("doctor_id"+patient_id).value;

	    
	     if(datess && doctor_id){
	     	load_schedule(patient_id);
	     	load_patient_appointments_two(patient_id);
		  $('#show_doctor').fadeToggle(1000); return false;
		 }
		 else{
		  alert('Select Date and a Doctor First')
		 }
	}

	function load_schedule(patient_id){
		var config_url = $('#config_url').val();
		var datess=document.getElementById("scheduledate"+patient_id).value;
		var doctor= document.getElementById("doctor_id"+patient_id).value;

		var url= config_url+"reception/doc_schedule/"+doctor+"/"+datess;

		// alert(url);
		
		  $('#doctors_schedule'+patient_id).load(url);
		  $('#doctors_schedule'+patient_id).fadeIn(1000); return false;	
	}
	function load_patient_appointments(patient_id){
		var patient_id = $('#patient_id'+patient_id).val();
		var current_date = $('#current_date'+patient_id).val();

		var url= config_url+"reception/patient_schedule/"+patient_id+"/"+current_date;
		
		$('#patient_schedule'+patient_id).load(url);
		$('#patient_schedule'+patient_id).fadeIn(1000); return false;	

		$('#patient_schedule2'+patient_id).load(url);
		$('#patient_schedule2'+patient_id).fadeIn(1000); return false;	
	}
	function load_patient_appointments_two(patient_id){
		var patient_id = $('#patient_id'+patient_id).val();
		var current_date = $('#current_date'+patient_id).val();

		var url= config_url+"reception/patient_schedule/"+patient_id+"/"+current_date;
		
		$('#patient_schedule2'+patient_id).load(url);
		$('#patient_schedule2'+patient_id).fadeIn(1000); return false;	
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

	function submit_reception_appointment(patient_id)
	{
		var config_url = document.getElementById("config_url").value;

        var data_url = config_url+"reception/save_appointment_accounts/"+patient_id;

		var visit_date = $('#scheduledate'+patient_id).val();   
       	var doctor_id = $('#doctor_id'+patient_id).val(); 
       	var timepicker_start = $('#timepicker_start'+patient_id).val(); 
       	var timepicker_end = $('#timepicker_end'+patient_id).val();   
       	var procedure_done = $('#procedure_done'+patient_id).val(); 
       	var room_id = $('#room_id'+patient_id).val(); 
       	var url_redirect = $('#redirect_url'+patient_id).val(); 

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