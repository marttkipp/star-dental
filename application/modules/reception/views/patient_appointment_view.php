 <ul class="nav nav-tabs nav-justified">
    <li class="active"><a href="#vitals-pane" data-toggle="tab">Appointment Details</a></li>
    <li><a href="#lists-pane" data-toggle="tab" onclick="initialize_editor()">Recall and Pending List</a></li>
    <li><a href="#notes-pane" data-toggle="tab" onclick="initialize_editor()">Notes</a></li>
</ul>
<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
    <div class="tab-pane active" id="vitals-pane">
      	<?php echo $patient_items;?>
    </div>
    <div class="tab-pane " id="lists-pane">
      	<div class="row">
	      	<div class="col-md-12">	
	      		<div class="form-group">
					<label class="col-lg-2 control-label">Recall List: </label>
					<div class="col-lg-10">
						<select name="list_id<?php echo $appointment_id;?>" id="list_id<?php echo $appointment_id;?>" class="form-control">
							<option value="">----Select a List----</option>
							<?php echo $list;?>											
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label">Doctor: </label>
					<div class="col-lg-10">
						<select name="doctor_id<?php echo $appointment_id?>" class="form-control" id="doctor_id<?php echo $appointment_id?>" >
							<option value="">----Select a Doctor----</option>
							<?php
													
								if(count($doctor) > 0){
									foreach($doctor as $row):
										$fname = $row->personnel_fname;
										$onames = $row->personnel_onames;
										$personnel_id = $row->personnel_id;
										
										if($personnel_id == set_value('personnel_id'))
										{
											echo "<option value='".$personnel_id."' selected='selected'>Dr. ".$onames." ".$fname."</option>";
										}
										
										else
										{
											echo "<option value='".$personnel_id."'>Dr. ".$onames." ".$fname."</option>";
										}
									endforeach;
								}
							?>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-lg-2 control-label">Period: </label>
					<div class="col-lg-10">
						<select name="period_id<?php echo $appointment_id;?>" id="period_id<?php echo $appointment_id;?>" class="form-control">
							<option value="">----Select a period----</option>
							<option value="30">One month</option>
							<option value="90">Three month</option>
							<option value="180">Six month</option>
							<option value="365">One year</option>
																	
						</select>
					</div>
				</div>			      		
				<div class="form-group" style="display: none;">
					<label class="col-lg-2 control-label">Date: </label>
					
					<div class="col-lg-10">
		                <div class="input-group">
		                    <span class="input-group-addon">
		                        <i class="fa fa-calendar"></i>
		                    </span>
		                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="end_date" placeholder="End Date" value="<?php echo date('Y-m-d');?>">
		                </div>
					</div>
				</div>
				<input type="hidden" name="visit_id<?php echo $appointment_id?>" id="visit_id<?php echo $appointment_id?>" value="<?php echo $visit_id?>">
				<input type="hidden" name="patient_id<?php echo $appointment_id?>" id="patient_id<?php echo $appointment_id?>" value="<?php echo $patient_id?>">
				<div class="form-group">
	   				<label class="col-lg-2 control-label">Note :  </label>
		    		<div class="col-lg-10">
		      				<textarea id="summary_notes<?php echo $appointment_id;?>" rows="5" cols="50"  class="form-control col-md-12 cleditor" > </textarea>
		      		</div>
		      	</div>
	      	</div>
	    </div>
	    <br>
	    <div class="row">
	        <div class="form-group">
	            <div class="col-lg-12">
	                <div class="center-align">
	                      <a hred="#" class="btn btn-sm btn-info" onclick="submit_patient_recall(<?php echo $appointment_id;?>)">Submit to Recall</a>
	                     
	                  </div>
	            </div>
	        </div>
	    </div>
	    <br>
	    <div id="recall-view"></div>
	</div>		
    <div class="tab-pane " id="notes-pane">
      	<div class="row">
	      	<div class="col-md-12">
	      		<div class="form-group">
					<label class="col-lg-12 control-label">Email From: </label>
					<div class="col-lg-12">
						<select name="email<?php echo $appointment_id?>" id="email<?php echo $appointment_id?>" class="form-control">
							<option value="">----Select a email----</option>
							<option value="info@upperhilldentalcentre.com">info@upperhilldentalcentre.com</option>
							<option value="appointments@upperhilldentalcentre.com">appointments@upperhilldentalcentre.com</option>
							<option value="accounts">accounts@upperhilldentalcentre.com</option>
							<option value="preauths@upperhilldentalcentre.com">preauths@upperhilldentalcentre.com</option>							
						</select>
					</div>
				 </div>
	      		<div class="form-group">
	   				<label class="col-lg-12 control-label">Subject :  </label>
		    		<div class="col-lg-12">
		      			<input id="subject<?php echo $appointment_id;?>" class="form-control" />
		      		</div>
		      	</div>
	  			<div class="form-group">
	   				<label class="col-lg-12 control-label">Message :  </label>
		    		<div class="col-lg-12">
		      			<textarea id="message<?php echo $appointment_id;?>" rows="5" cols="50"  class="form-control col-md-12 cleditor" > </textarea>
		      		</div>
		      	</div>
		      	<div class="form-group">
					<label class="col-lg-3 control-label">Send Message to ? </label>
		            <div class="col-lg-3">
		                <div class="radio">
		                    <label>
		                        <input id="optionsRadios1<?php echo $appointment_id;?>" type="radio" name="featured" value="0" checked="checked">
		                        SMS
		                    </label>
		                </div>
		            </div>
		            <div class="col-lg-3">
		                <div class="radio">
		                    <label>
		                        <input id="optionsRadios1<?php echo $appointment_id;?>" type="radio" name="featured" value="1" >
		                        EMAIL
		                    </label>
		                </div>
		            </div>
		            <div class="col-lg-3">
		                <div class="radio">
		                    <label>
		                        <input id="optionsRadios1<?php echo $appointment_id;?>" type="radio" name="featured" value="3" >
		                        EMAIL AND SMS
		                    </label>
		                </div>
		            </div>
				</div>
		    </div>


		  </div>
		  <br>
		<div class="row">
	        <div class="form-group">
	            <div class="col-lg-12">
	                <div class="center-align">
	                      <a hred="#" class="btn btn-sm btn-info" onclick="send_patient_note(<?php echo $appointment_id;?>)">Send Note</a>
	                     
	                  </div>
	            </div>
	        </div>
	    </div>
    </div>
</div>