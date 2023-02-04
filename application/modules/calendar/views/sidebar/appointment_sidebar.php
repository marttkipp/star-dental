<div class="col-md-12">
	<ul class="nav nav-tabs nav-justified">
	    <li class="active"><a href="#vitals-pane" data-toggle="tab">Patient Appointment</a></li>
	    <li><a href="#events-pane" data-toggle="tab">Event</a></li>
	   
	</ul>
	<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
	    <div class="tab-pane active" id="vitals-pane">
	    	<section class="panel">
	    		<div class="panel-body">
	    			
	    		
				<div class="col-md-6">
					<div id="top-div" style="margin-top: 0px;display: block;">
						<!-- <div class="padd"> -->		
						<form action="#" method="get" class="sidebar-form">
							<div class="input-group">
							<input type="text" name="q" id="q" class="form-control" onkeyup="search_laboratory_tests(<?php echo $appointment_id?>)" placeholder="Search Patients Database">
							<span class="input-group-btn">
							    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
							    </button>
							  </span>
							</div>
						</form>
						<ul  id="searched-lab-test">
					  
						</ul>
					</div>
				</div>
				<div class="col-md-6">
					<a  class="btn btn-sm btn-success" id="new-patient-button" onclick="get_new_patient_view(<?php echo $appointment_id;?>)" style="display:block"><i class="fa fa-arrow-right"></i> ADD NEW PATIENT</a>
				</div>
				

				<div id="new-patient-div" style="margin-top: 0px;display: none;">
					<!-- <div class="padd"> -->		
					<h6>New Patient Detail</h6>
					<br>
					<form id="add_appointment2" method="post">

						<div class="row">
							<div class="col-md-12">
								<div class="col-md-6">		
									<!-- <div class="form-group">
										<input id="first_name<?php echo $appointment_id?>" class="form-control" name="first_name<?php echo $appointment_id?>" placeholder="First Name" onkeyup="search_patients_list(<?php echo $appointment_id?>)">
									</div>-->				
									<div class="form-group">
										<input id="other_names<?php echo $appointment_id?>" class="form-control" name="other_names<?php echo $appointment_id?>" placeholder="Other Names" onkeyup="search_patients_list(<?php echo $appointment_id?>)">
									</div> 
									<div class="form-group">
										<input id="surname<?php echo $appointment_id?>" class="form-control" name="surname<?php echo $appointment_id?>" placeholder="Surname" onkeyup="search_patients_list(<?php echo $appointment_id?>)" autocomplete="off">
									</div>									
							
									
									<div class="form-group">
										<select name="visit_type_id<?php echo $appointment_id?>" id="visit_type_id<?php echo $appointment_id?>" class="form-control">
											<option value="">----Select an account----</option>
											<?php
																	
												echo $visit_type;
											?>
										</select>
									</div>

									<div class="form-group">
					                    <label class="col-lg-4 control-label">How did you know about us? </label>
					                    <div class="col-lg-8">
					                         <select class="form-control" name="about_us<?php echo $appointment_id?>" required="required">
											    <?php
											    	$places = $this->reception_model->get_places();
											        if($places->num_rows() > 0)
											        {
											            $places = $places->result();
											            
											            foreach($places as $res)
											            {
											                $place_id1 = $res->place_id;
											                $place_name = $res->place_name;
											                
											                if($place_id1 ==  set_value("place_id"))
											                {
											                    echo '<option value="'.$place_id1.'" selected>'.$place_name.'</option>';
											                }
											                
											                else
											                {
											                    echo '<option value="'.$place_id1.'">'.$place_name.'</option>';
											                }
											            }
											        }
											    ?>

											</select>
					                        
					                    </div>
					                   
					                </div>

				                    <div class="form-group">
				                        <div class="radio">
				                            <label class="col-lg-4 control-label">
				                                Specifiy
				                                
				                            </label>
				                              <div class="col-md-8">
				                                    <input type="text" class="form-control" name="about_us_view<?php echo $appointment_id?>" placeholder="Specify the person /location or how you knew about us">
				                                </div>
				                        </div>
				                    </div>

									
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<input id="phone_number<?php echo $appointment_id?>" class="form-control" name="phone_number<?php echo $appointment_id?>" placeholder="Phone Number" required onkeyup="search_patients_list(<?php echo $appointment_id?>)">	
									</div>
									<div class="form-group" style="display: none;">
										<label class="col-lg-4 control-label">Appointment Type: </label>
										<div class="col-lg-4">
							                <div class="radio">
							                    <label>
							                        <input id="optionsRadios2" type="radio" name="appointment_status" value="1" checked="checked" >
							                        Normal
							                    </label>
							                </div>
							            </div>
							            
							            <div class="col-lg-4">
							                <div class="radio">
							                    <label>
							                        <input id="optionsRadios2" type="radio" name="appointment_status" value="8" >
							                        Tentative
							                    </label>
							                </div>
							            </div>
									</div>

									<div class="form-group">
										<select name="visit_time_id<?php echo $appointment_id?>" id="visit_time_id<?php echo $appointment_id?>" class="form-control">
											<!-- <option value="">----Select a Visit Duration----</option> -->
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
									<div class="form-group">					
										<select name="dentist_id<?php echo $appointment_id?>" id="dentist_id<?php echo $appointment_id?>" class="form-control">
											<option value="">----Select a  Primary Dentist----</option>
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
														echo '<option value="'.$personnel_id.'" selected> '.$personnel_fname.' '.$personnel_onames.'</option>';
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

							</div>
						</div>
						<div class="row" style="margin-top: 10px">
							<div class="col-md-12">
								<div class="form-group">
									<textarea id="procedure_done<?php echo $appointment_id?>" class="form-control" name="procedure_done<?php echo $appointment_id?>" placeholder="Description..."></textarea>
								</div>
							</div>
						</div>
						<input type="hidden" name="appointment_id" id="appointment_id" value="<?php echo $appointment_id;?>">
						<input type="hidden" name="appointment_type" id="appointment_type" value="1">
						<input type="hidden" name="category" id="category" value="1">				
						
						<div class="row" style="margin-top: 10px">
					        <div class="col-md-12 center-align">
					        	<button type="submit" class="btn btn-sm btn-success ">ADD APPOINTMENT DETAIL</button>	
					        	<a  class="btn btn-sm btn-warning"  onclick="get_old_patient_view(<?php echo $appointment_id;?>)" ><i class="fa fa-arrow-left"></i> BACK TO SEARCH</a>
					        </div>
					    </div>
					</form>
					<ul  id="searched-patients-list">
				  
					</ul>
				</div>

				<div id="bottom-div" style="margin-top: 60px;display: none;">
					<form id="add_appointment" method="post" id="appointment-details">
						<h4>Appointment Details : <?php echo $appointment_date_time_start?> <?php echo $patient_name;?>  <a class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a></h4>
						<ul>
							
						
							 <input type="hidden" name="appointment_id" id="appointment_id" value="<?php echo $appointment_id;?>">
							 <input type="hidden" name="appointment_type" id="appointment_type" value="1">
							 <input type="hidden" name="patient_id<?php echo $appointment_id;?>" id="patient_id<?php echo $appointment_id;?>" value="<?php echo $patient_id;?>">
							  <input type="hidden" name="category" id="category" value="0">
							
						</ul>


						<div class="row">
							<div class="col-md-12">
								<div class="col-md-6">
								 	<div class="form-group">
								 		<input type="text" id="patient_phone1<?php echo $appointment_id?>" class="form-control" name="patient_phone1<?php echo $appointment_id?>" placeholder="Phone Number">
								 	</div>
								 	<div class="form-group">
								 		<select name="dentist_id<?php echo $appointment_id?>" id="dentist_id<?php echo $appointment_id?>" class="form-control">
											<option value="">----Select a  Primary Dentist----</option>
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
														echo '<option value="'.$personnel_id.'" selected> '.$personnel_fname.' '.$personnel_onames.'</option>';
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
								 	<div class="form-group">
								 		<label class="col-lg-4 control-label">Appointment Type: </label>
										<div class="col-lg-4">
							                <div class="radio">
							                    <label>
							                        <input id="optionsRadios2" type="radio" name="appointment_status" value="1" checked="checked" >
							                        Normal
							                    </label>
							                </div>
							            </div>
							            
							            <div class="col-lg-4">
							                <div class="radio">
							                    <label>
							                        <input id="optionsRadios2" type="radio" name="appointment_status" value="8" >
							                        Tentative
							                    </label>
							                </div>
							            </div>
								 	</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<select  id="visit_type_id<?php echo $appointment_id?>" class="form-control"  name="visit_type_id<?php echo $appointment_id?>">
											<option value="">----Select a account----</option>
											<?php
																	
												echo $visit_type;
											?>
										</select>
									</div>
									<div class="form-group">
											<select name="visit_time_id<?php echo $appointment_id?>" id="visit_time_id<?php echo $appointment_id?>" class="form-control">
												<!-- <option value="">----Select a Visit Duration----</option> -->
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
							</div>

						</div>
						<div class="row" style="margin-top: 10px">
							<div class="col-md-12">
								<div class="form-group">
									<textarea id="procedure_done<?php echo $appointment_id?>" class="form-control" name="procedure_done<?php echo $appointment_id?>" placeholder="Additional information "></textarea>
								</div>
							</div>
						</div>
						<div class="row" style="margin-top: 10px">
					        <div class="col-md-12">
					        	<div class=" center-align">
					        		<button type="submit" class="btn btn-sm btn-success ">ADD APPOINTMENT DETAIL</button>
					        	</div>  
					        </div>
					    </div>
					</form>
				</div>
				</div>
	    	</section>
		</div>
		<div class="tab-pane" id="events-pane">
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
									<!-- <option value="">----Select a  Duration----</option> -->
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
	</div>
	<div class="row" style="margin-top: 5px;">
		<ul>
			<li style="margin-bottom: 5px;">
				<div class="row">
			        <div class="col-md-12 center-align">
				        	<!-- <div id="old-patient-button" style="display:none">
				        				        		
				        		
				        	</div> -->
				        	<!-- <div> -->
				        		<a  class="btn btn-sm btn-info" onclick="close_side_bar()"><i class="fa fa-folder-closed"></i> CLOSE SIDEBAR</a>
				        	<!-- </div> -->
				        		
			               
			        </div>
			    </div>
				
			</li>
		</ul>
	</div>
</div>
