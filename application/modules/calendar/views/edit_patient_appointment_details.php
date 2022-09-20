<?php
$appointment_details = $this->reception_model->get_patient_appointment_details($appointment_id);
$type_visit = null;
if($appointment_details->num_rows() >0)
{
	foreach ($appointment_details->result() as $key => $value) {
		# code...
		$event_name = $value->event_name;
		$event_description = $value->event_description;
		$duration = $value->duration;
		$appointment_date = $value->visit_date;
		$personnel_idd = $value->personnel_id;
		$appointment_start_time = $value->appointment_start_time;
		$type_visit = $value->visit_type;

	}
}

$time_in_12_hour_format  = date("g:i a", strtotime($appointment_start_time));
$one = '';
$two = '';
$three = '';
$four = '';
$five = '';
$six = '';
$seven = '';
$eight = '';
$nine = '';
$ten = '';
if($duration == 15)
{
	$one = 'selected';

}

if($duration == 30)
{
	$two = 'selected';

}
if($duration == 45)
{
	$three = 'selected';

}
if($duration == 60)
{
	$four = 'selected';
}
if($duration == 90)
{
	$five = 'selected';
}
if($duration == 120)
{
	$six = 'selected';
}
if($duration == 180)
{
	$seven = 'selected';
}
if($duration == 240)
{
	$eight = 'selected';
}
if($duration == 300)
{
	$nine = 'selected';
}
if($duration == 360)
{
	$ten = 'selected';
}



?>
<div class="row">
	
	<form id="edit_appointment" method="post">
		<div class="col-md-12">		

			<div class="form-group" >
				<label class="col-lg-4 control-label">Visit Type: </label>
				<div class="col-lg-8">
					<select name="visit_type_id" id="visit_type_id" class="form-control">
						<option value="">----Select a Visit type----</option>
						<?php		
							$visit_types = $this->reception_model->get_visit_types();
							$types = '';
							if($visit_types->num_rows() > 0)
							{
								foreach ($visit_types->result() as $key => $value2) {
									# code...
									$visit_type_name = $value2->visit_type_name;
									$visit_type_id = $value2->visit_type_id;

									if($type_visit ===  $visit_type_id)
									{
										echo  '<option value="'.$visit_type_id.'" selected="selected">'.$visit_type_name.'</option>';
									}
									else
									{
										echo  '<option value="'.$visit_type_id.'">'.$visit_type_name.'</option>';
									}
								}
							}
						?>
					</select>
				</div>
			 </div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Description: </label>
				<div class="col-lg-8">
					<textarea id="procedure_done" class="form-control" name="procedure_done"><?php echo $event_description;?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Appointment date:</label>
				
				<div class="col-lg-8">
	                <div class="input-group">
	                    <span class="input-group-addon">
	                        <i class="fa fa-calendar"></i>
	                    </span>
	                    <input data-format="YYYY-MM-DD" type="text" data-plugin-datepicker class="form-control " name="visit_date" placeholder="Visit Date" value="<?php echo $appointment_date;?>" id="datepicker" readonly>
	                </div>
				</div>
			</div>
			<div class="form-group">
	            <label class="col-lg-4 control-label">Start time : </label>
	        
	            <div class="col-lg-8">
	                <div class="input-group">
	                    <span class="input-group-addon">
	                        <i class="fa fa-clock-o"></i>
	                    </span>
	                    <input type="text" class="form-control timepicker" data-plugin-timepicker="" id="timepicker" name="time_start" value="<?php echo $time_in_12_hour_format?>">
	                </div>
	            </div>
	        </div>
	        <div class="form-group" >
				<label class="col-lg-4 control-label">Dentist: </label>
				<div class="col-lg-8">
					<select name="personnel_id" id="personnel_id" class="form-control">
						<option value="">----Select a Dentist----</option>
						<?php		
							$doctos = $this->reception_model->get_all_doctors();
							$types = '';
							// var_dump($doctos->num_rows());die();
							if($doctos->num_rows() > 0)
							{
								foreach ($doctos->result() as $key => $value4) {
									# code...
									$personnel_fname = $value4->personnel_onames;
									$personnel_id = $value4->personnel_id;

									if($personnel_idd ===  $personnel_id)
									{
										echo  '<option value="'.$personnel_id.'" selected="selected">Dr. '.$personnel_fname.'</option>';
									}
									else
									{
										echo  '<option value="'.$personnel_id.'">'.$personnel_fname.'</option>';
									}
								}
							}
						?>
					</select>
				</div>
			 </div>
			<div class="form-group">
				<label class="col-lg-4 control-label"> Duration: </label>
				<div class="col-lg-8">
					<select name="visit_time" id="visit_time" class="form-control">
						<option value="">----Select a  Duration----</option>

						<option value="15" <?php echo $one?>>15 Min</option>
						<option value="30" <?php echo $two?>>30 Min</option>
						<option value="45" <?php echo $three?>>45 Min</option>
						<option value="60" <?php echo $four?>>1 Hrs</option>
						<option value="90" <?php echo $five?>>1 Hrs 30 Min</option>
						<option value="120" <?php echo $six?>>2 Hrs</option>
						<option value="180" <?php echo $seven?>>3 Hrs</option>
						<option value="240" <?php echo $eight?>>4 Hrs</option>
						<option value="300" <?php echo $nine?>>5 Hrs</option>
						<option value="360" <?php echo $ten?>>6 Hrs</option>
						
					</select>
				</div>
			</div>
			
			<input type="hidden" name="reschedule_id" id="reschedule_id" value="0">

			
			 <input type="hidden" name="appointment_id" id="appointment_id" value="<?php echo $appointment_id;?>">
			<input type="hidden" name="appointment_type" id="appointment_type" value="1">
			<input type="hidden" name="category" id="category" value="1">
				
		</div>
		<br/>
		<br/>
		<div class="row" >
	        <div class="col-md-12">
	        	<div class=" center-align" style="margin-top: 10px;">
	        		<button type="submit" class="btn btn-sm btn-success ">EDIT APPOINTMENT DETAIL</button>
	        		
	        	</div>
	               
	        </div>
	    </div>
	</form>
</div>

