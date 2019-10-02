<?php
	if($notes_detail->num_rows() > 0)
	{
		foreach ($notes_detail->result() as $key => $value) {
			# code...
			$resource_id = $value->resource_id;
			$note = $value->note;
			$end_date = $value->end_date;
			$calendar_note_id = $value->calendar_note_id;
		}
	}
?>
<form id="edit_note" method="post">
	<div class="row">
		<input type="hidden" name="calendar_note_id" value="<?php echo $calendar_note_id?>">
		<div class="col-md-8 col-md-offset-2">
			<div class="form-group">
				<label class="col-lg-4 control-label">Schedule: </label>
				<div class="col-lg-8">
					<select name="schedule" id="schedule" class="form-control">
						<?php 
							if($schedule_views->num_rows() > 0)
							{
								foreach ($schedule_views->result() as $key => $value) {
									# code...
									$schedule_name = $value->schedule_name;
									$schedule_value = $value->schedule_value;

									if($schedule_value == $resource_id)
									{
										echo '<option value="'.$schedule_value.'" selected>'.$schedule_name.'</option>';
									}
									else
									{
										echo '<option value="'.$schedule_value.'">'.$schedule_name.'</option>';
									}
								}
							}
						?>
						
					</select>
				</div>
			 </div>

			 <div class="form-group">
				<label class="col-lg-4 control-label">Note: </label>
				<div class="col-lg-8">
					<textarea id="schedule_note" class="form-control" name="schedule_note"><?php echo $note;?></textarea>
				</div>
			 </div>
			 <div class="form-group">
				<label class="col-lg-4 control-label">Type *: </label>
				<div class="col-lg-8">
					<select name="type" id="type" class="form-control">
						<option value="2">BOTTOM</option>
						
					</select>
				</div>
			 </div>
			
			<div class="form-group">
				<label class="col-lg-4 control-label">End date: </label>
				
				<div class="col-lg-8">
	                <div class="input-group">
	                    <span class="input-group-addon">
	                        <i class="fa fa-calendar"></i>
	                    </span>
	                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="end_date" placeholder="End Date" value="<?php echo $end_date;?>">
	                </div>
				</div>
			</div>
			
		</div>
	</div>

	<br/>
	<div class="row">
        <div class="col-md-12">
        	<div class=" center-align">
        		<button type="submit" class="btn btn-sm btn-success ">EDIT NOTE DETAIL</button>
        	</div>
               
        </div>
    </div>
</form>