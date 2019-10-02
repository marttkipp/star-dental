<div class="row">
	<div class="col-md-12">
		<a href="" class="btn btn-sm btn-warning pull-right" style="margin-left:5px"> <i class="fa fa-calendar"></i> Online Diary <i class="fa fa-arrow-right"></i></a>
		<button type="button" class="btn btn-sm btn-success pull-right" data-toggle="modal" data-target="#book-appointment"><i class="fa fa-plus"></i> Schedule Appointment </button>


		<div class="modal fade " id="book-appointment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		    <div class="modal-dialog modal-lg" role="document">
		        <div class="modal-content ">
		            <div class="modal-header">
		            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		            	<h4 class="modal-title" id="myModalLabel">Schedule Appointment</h4>
		            </div>
		            <?php echo form_open("reception/save_appointment/".$patient_id."/".$visit_id, array("class" => "form-horizontal"));?>

		            <div class="modal-body">
		            	<div class="row">
		            		<input type="hidden" name="redirect_url" value="<?php echo $this->uri->uri_string();?>">
		            		<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id;?>">
		            		<input type="hidden" name="current_date" id="current_date" value="<?php echo $past_visit_date;?>">
		            		<div class="col-md-12">
		            			<div class="col-md-6">
		            				<div class="form-group">
									<label class="col-lg-4 control-label">Visit date: </label>
									
									<div class="col-lg-8">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date" id="scheduledate" placeholder="Visit Date" value="<?php echo date('Y-m-d');?>" required>
                                        </div>
									</div>
								</div>
								<div class="form-group">
								  <label class="col-lg-4 control-label">Room: </label>	
									<div class="col-lg-8">
											<select name="room_id" id="room_id" class="form-control" >
												<option value="">----Select Room----</option>
												<?php
													$all_rooms = $this->rooms_model->all_rooms();			
													if($all_rooms->num_rows() > 0){

														foreach($all_rooms->result() as $row):
															$room_name = $row->room_name;
															$room_id = $row->room_id;
															
															if($room_id == set_value('room_id'))
															{
																echo "<option value='".$room_id."' selected='selected'>".$room_name."</option>";
															}
															
															else
															{
																echo "<option value='".$room_id."'>".$room_name."</option>";
															}
														endforeach;
													}
												?>
											</select>
									</div>
								</div>
								
								<?php
								$get_tca_rs = $this->nurse_model->get_tca_notes($visit_id);
								$tca_num_rows = count($get_tca_rs);
								$tca_description ='';
								if($tca_num_rows > 0){
									foreach ($get_tca_rs as $key7):
										$tca_description = $key7->tca_description;
									endforeach;
								}

								?>

								 <div class="form-group">
				                        <label class="col-lg-4 control-label">Procedure to be done</label>
				                        <div class="col-lg-8">
				                        	<textarea class="form-control" name="procedure_done" id="procedure_done"><?php echo $tca_description;?></textarea>
				                           
				                       </div>
	                             </div>  
								
                                	
		            			</div>
		            			<div class="col-md-6">
		            				<div id="appointment_details" >
	                                    <div class="form-group">
	                                        <label class="col-lg-4 control-label">Schedule: </label>
	                                        
	                                        <div class="col-lg-8">
	                                            <a onclick="check_date()" style="cursor:pointer;">[Show Doctor's Schedule]</a><br>
	                                            <div id="show_doctor" style="display:none;"> 
	                                                
	                                            </div>
	                                            <div  id="doctors_schedule" style="margin-left: -94px;font-size: 10px;"> </div>
	                                        </div>
	                                    </div>
	                                    
	                                    <div class="form-group">
	                                        <label class="col-lg-4 control-label">Start time : </label>
	                                    
	                                        <div class="col-lg-8">
	                                            <div class="input-group">
	                                                <span class="input-group-addon">
	                                                    <i class="fa fa-clock-o"></i>
	                                                </span>
	                                                <input type="text" class="form-control" data-plugin-timepicker="" name="timepicker_start" id="timepicker_start">
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
	                                                <input type="text" class="form-control" data-plugin-timepicker="" name="timepicker_end" id="timepicker_end">
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
		            			</div>
		            		</div>
		            	</div>
		            	
								
		              	
		            </div>
		            <div class="modal-footer">
		            	<button type="submit" class="btn btn-sm btn-success">Schedule Appointment</button>
		                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
		            </div>

		               <?php echo form_close();?>
		        </div>
		    </div>
		</div>
	</div>
	
</div>
<br>



