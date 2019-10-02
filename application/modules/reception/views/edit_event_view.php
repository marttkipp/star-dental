<?php 
$appointment_details = $this->reception_model->get_appointment_details($appointment_id);
if($appointment_details->num_rows() >0)
{
	foreach ($appointment_details->result() as $key => $value) {
		# code...
		$event_name = $value->event_name;
		$event_description = $value->event_description;
		$duration = $value->duration;

	}
}
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
	<div class="col-md-12">
		<div class="col-md-5">
			<?php echo $event_items;?>
		</div>
		<div class="col-md-7">
			<form id="edit_event" method="post">
				<div class="form-group">
					<label class="col-lg-4 control-label">Title: </label>
					<div class="col-lg-8">
					<input type="text" name="appointment_title" id="appointment_title" class="form-control" value="<?php echo $event_name?>">
					</div>
				 </div>
				 <div class="form-group">
					<label class="col-lg-4 control-label"> Duration: </label>
					<div class="col-lg-8">
						<select name="event_duration" id="event_duration" class="form-control">
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

				 <div class="form-group">
					<label class="col-lg-4 control-label">Description: </label>
					<div class="col-lg-8">
						<textarea id="procedure_done" class="form-control" name="procedure_done" ><?php echo $event_description?></textarea>
					</div>
				 </div>
				 <input type="hidden" name="appointment_id" id="appointment_id" value="<?php echo $appointment_id;?>">
				 <input type="hidden" name="appointment_type" id="appointment_type" value="2">
				 <br/>
				<div class="row">
			        <div class="col-md-12">
			        	<div class=" center-align">
			        		<button type="submit" class="btn btn-sm btn-success ">EDIT EVENT DETAIL</button>
			        	</div>
			               
			        </div>
			    </div>
			</form>
		</div>
	</div>

</div>
