<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="form-group">
			<label class="col-lg-4 control-label"> </label>
            <div class="col-lg-4">
                <div class="radio">
                    <label>
                        <input id="optionsRadios1" type="radio" name="apppointment_type" value="2" onclick="get_appointment_view(2)">
                        Event
                    </label>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="radio">
                    <label>
                        <input id="optionsRadios1" type="radio" name="apppointment_type" value="1" onclick="get_appointment_view(1)">
                        Appointment
                    </label>
                </div>
            </div>
		</div>
	</div>
</div>
<div id="patient_appointment" style="display: none;">
	<div class="row" id="old-patient-view" style="display: block;">
			<form id="add_appointment" method="post">
				<div class="row">
					<div class="col-md-8 col-md-offset-2">
						<div class="form-group">
							<label class="col-lg-4 control-label">Patient: </label>
							<div class="col-lg-8">
								<select name="patient_id<?php echo $appointment_id?>" id="patient_id<?php echo $appointment_id?>" class="form-control custom-select">
									<option value="">----Select a Patient----</option>
									<?php
															
										echo $patients;
									?>
								</select>
							</div>
						 </div>

						 <div class="form-group">
							<label class="col-lg-4 control-label">Account: </label>
							<div class="col-lg-8">
								<select  id="visit_type_id<?php echo $appointment_id?>" class="form-control"  name="visit_type_id<?php echo $appointment_id?>">
									<option value="">----Select a account----</option>
									<?php
															
										echo $visit_type;
									?>
								</select>
							</div>
						 </div>


						 <div class="form-group">
							<label class="col-lg-4 control-label">Service: </label>
							<!-- <div class="col-lg-8"> -->
								<!-- <select  id="service_charge_id<?php echo $appointment_id?>" class="form-control"  name="service_charge_id<?php echo $appointment_id?>">
									<option value="">----Select a service----</option>
									<?php
															
										echo $service_charge;
									?>
								</select> -->
							<!-- </div> -->
							<div class="col-lg-4">
				                <div class="radio">
				                    <label>
				                        <input id="optionsRadios2" type="radio" name="appointment_id" value="0" checked="checked" >
				                        Normal
				                    </label>
				                </div>
				            </div>
				            
				            <div class="col-lg-4">
				                <div class="radio">
				                    <label>
				                        <input id="optionsRadios2" type="radio" name="appointment_id" value="1" >
				                        Tentative
				                    </label>
				                </div>
				            </div>
						 </div>

						 <div class="form-group" >
							<label class="col-lg-4 control-label">Description: </label>
							<div class="col-lg-8">
								<textarea id="procedure_done<?php echo $appointment_id?>" class="form-control" name="procedure_done<?php echo $appointment_id?>"></textarea>
							</div>
						 </div>

						 <div class="form-group">
							<label class="col-lg-4 control-label">Visit Duration: </label>
							<div class="col-lg-8">
								<select name="visit_time_id<?php echo $appointment_id?>" id="visit_time_id<?php echo $appointment_id?>" class="form-control">
									<option value="">----Select a Visit Duration----</option>
									<option value="15">15 Min</option>
									<option value="30">30 Min</option>
									<option value="45">45 Min</option>
									<option value="60">1 Hrs</option>
									<option value="90">1 Hrs 30 Min</option>
									<option value="120">2 Hrs</option>
									<option value="180">3 Hrs</option>
									<option value="240">4 Hrs</option>
									<option value="300">5 Hrs</option>
									<option value="360">6 Hrs</option>
									
								</select>
							</div>
						 </div>
						 <div class="form-group">
							<label class="col-lg-4 control-label"> Primary Dentist: </label>
							<div class="col-lg-8">
								<select name="dentist_id<?php echo $appointment_id?>" id="dentist_id<?php echo $appointment_id?>" class="form-control">
									<option value="">----Select a  Primary dentist----</option>
									<?php
									if($doctors->num_rows() > 0)
									{
										foreach ($doctors->result() as $key => $value) {
											# code...
											$personnel_id = $value->personnel_id;
											$personnel_fname = $value->personnel_fname;
											$personnel_onames = $value->personnel_onames;

											echo '<option value="'.$personnel_id.'">'.$personnel_onames.' '.$personnel_fname.'</option>';
										}
									}
									?>
								</select>
							</div>
						 </div>

						 <input type="hidden" name="appointment_id" id="appointment_id" value="<?php echo $appointment_id;?>">
						  <input type="hidden" name="appointment_type" id="appointment_type" value="1">
						  <input type="hidden" name="category" id="category" value="0">
						
					</div>
				</div>
				<br/>

					<div class="row">
			        <div class="col-md-12">
			        	<div class=" center-align">
			        		<button type="submit" class="btn btn-sm btn-success ">ADD APPOINTMENT DETAIL</button>
			        		<a  class="btn btn-sm btn-warning" id="new-patient-button" onclick="get_new_patient_view(<?php echo $appointment_id;?>)"><i class="fa fa-arrow-right"></i> ADD NEW PATIENT</a>		        		
			        		
			        	</div>
			               
			        </div>
			    </div>
			</form>
		
	</div>
	
	<div class="row" id="new-patient-view" style="display: none;">
		<form id="add_appointment2" method="post">
			<div class="col-md-12">
				<div class="col-md-6">
					<div class="form-group">
						<label class="col-lg-4 control-label">First Name: </label>
						<div class="col-lg-8">
							<input id="other_names<?php echo $appointment_id?>" class="form-control" name="other_names<?php echo $appointment_id?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-4 control-label">Middle Name: </label>
						<div class="col-lg-8">
							<input id="first_name<?php echo $appointment_id?>" class="form-control" name="first_name<?php echo $appointment_id?>">
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-lg-4 control-label">Surname: </label>
						<div class="col-lg-8">
							<input id="surname<?php echo $appointment_id?>" class="form-control" name="surname<?php echo $appointment_id?>">
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-lg-4 control-label">Default Phone: </label>
						<div class="col-lg-8">
							<input id="phone_number<?php echo $appointment_id?>" class="form-control" name="phone_number<?php echo $appointment_id?>">
						</div>
					</div>
					
				</div>
				<div class="col-md-6">
					<div class="form-group">
					<label class="col-lg-4 control-label">Visit Type: </label>
					<div class="col-lg-8">
						<select name="visit_type_id<?php echo $appointment_id?>" id="visit_type_id<?php echo $appointment_id?>" class="form-control">
							<option value="">----Select a Visit type----</option>
							<?php
													
								echo $visit_type;
							?>
						</select>
					</div>
				 </div>

				<div class="form-group">
					<label class="col-lg-4 control-label">Description: </label>
					<div class="col-lg-8">
						<textarea id="procedure_done<?php echo $appointment_id?>" class="form-control" name="procedure_done<?php echo $appointment_id?>"></textarea>
					</div>
				</div>

				<div class="form-group">
					<label class="col-lg-4 control-label">Visit Duration: </label>
					<div class="col-lg-8">
						<select name="visit_time_id<?php echo $appointment_id?>" id="visit_time_id<?php echo $appointment_id?>" class="form-control">
							<option value="">----Select a Visit Duration----</option>
							<option value="15">15 Min</option>
							<option value="30">30 Min</option>
							<option value="45">45 Min</option>
							<option value="60">1 Hrs</option>
							<option value="90">1 Hrs 30 Min</option>
							<option value="120">2 Hrs</option>
							<option value="180">3 Hrs</option>
							<option value="240">4 Hrs</option>
							<option value="300">5 Hrs</option>
							<option value="360">6 Hrs</option>
							
						</select>
					</div>
				 </div>
				 <div class="form-group">
							<label class="col-lg-4 control-label"> Primary Dentist: </label>
							<div class="col-lg-8">
								<select name="dentist_id<?php echo $appointment_id?>" id="dentist_id<?php echo $appointment_id?>" class="form-control">
									<option value="">----Select a  Primary dentist----</option>
									<?php
									if($doctors->num_rows() > 0)
									{
										foreach ($doctors->result() as $key => $value) {
											# code...
											$personnel_id = $value->personnel_id;
											$personnel_fname = $value->personnel_fname;
											$personnel_onames = $value->personnel_onames;
											if($personnel_id == $resource_id)
											{
												echo '<option value="'.$personnel_id.'" selected="selected"> '.$personnel_fname.' '.$personnel_onames.'</option>';
											}
											else
											{
												echo '<option value="'.$personnel_id.'">'.$personnel_fname.' '.$personnel_onames.'</option>';
											}
											
										}
									}
									?>
								</select>
							</div>
						 </div>
				 <input type="hidden" name="appointment_id" id="appointment_id" value="<?php echo $appointment_id;?>">
					  <input type="hidden" name="appointment_type" id="appointment_type" value="1">
					  <input type="hidden" name="category" id="category" value="1">
					
				</div>
			</div>
			<br/>
			<br/>
			<div class="row">
		        <div class="col-md-12">
		        	<div class=" center-align">
		        		<button type="submit" class="btn btn-sm btn-success ">ADD APPOINTMENT DETAIL</button>
		        		<a  class="btn btn-sm btn-warning" id="old-patient-button" onclick="get_old_patient_view(<?php echo $appointment_id;?>)"><i class="fa fa-arrow-left"></i> BACK TO EXISITING PATIENT</a>
		        	</div>
		               
		        </div>
		    </div>
		</form>
			
	</div>
</div>
<div id="event_appointment" style="display: none;">
	<form id="add_event" method="post">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="form-group">
					<label class="col-lg-4 control-label">Title: </label>
					<div class="col-lg-8">
					<input type="text" name="appointment_title" id="appointment_title" class="form-control" value="">
					</div>
				 </div>
				 <div class="form-group">
					<label class="col-lg-4 control-label"> Duration: </label>
					<div class="col-lg-8">
						<select name="event_duration" id="event_duration" class="form-control">
							<option value="">----Select a  Duration----</option>
							<option value="15">15 Min</option>
							<option value="30">30 Min</option>
							<option value="45">45 Min</option>
							<option value="60">1 Hrs</option>
							<option value="90">1 Hrs 30 Min</option>
							<option value="120">2 Hrs</option>
							<option value="180">3 Hrs</option>
							<option value="240">4 Hrs</option>
							<option value="300">5 Hrs</option>
							<option value="360">6 Hrs</option>
							
						</select>
					</div>
				 </div>

				 <div class="form-group">
					<label class="col-lg-4 control-label">Description: </label>
					<div class="col-lg-8">
						<textarea id="procedure_done" class="form-control" name="procedure_done"></textarea>
					</div>
				 </div>
				 <input type="hidden" name="appointment_id" id="appointment_id" value="<?php echo $appointment_id;?>">
				 <input type="hidden" name="appointment_type" id="appointment_type" value="2">
				
			</div>
		</div>
		<br/>
		<div class="row">
	        <div class="col-md-12">
	        	<div class=" center-align">
	        		<button type="submit" class="btn btn-sm btn-success ">ADD EVENT DETAIL</button>
	        	</div>
	               
	        </div>
	    </div>
	</form>



</div>

<button type="button" class="btn btn-danger btn-sm"  onclick="delete_event_details(<?php echo $appointment_id;?>,1)">Delete View</button>