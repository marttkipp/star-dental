<?php
$rs_pa = $this->dental_model->get_payment_info($visit_id);
$sick_leave_days = 0;
$sick_leave_start_date = date('Y-m-d');
$sick_leave_note ='';
if(count($rs_pa) >0){
	foreach ($rs_pa as $r2):
		# code...
		$sick_leave_note = $r2->sick_leave_note;
		$sick_leave_start_date = $r2->sick_leave_start_date;
		$sick_leave_days = $r2->sick_leave_days;

		// get the visit charge

	endforeach;


	


}

echo '<div class="row">
      	<div class="col-md-12">
  			<div class="form-group">
   				<label class="col-lg-4 control-label">Sick Leave Note:  </label>
	    		<div class="col-lg-8">
	      				<textarea id="deductions_and_other" rows="5" cols="50" class="form-control col-md-12" > '.$sick_leave_note.' </textarea>
	      		</div>
	      	</div>
	    </div>

	  </div>';

  echo '
  <br>
	<div class="row">
        <div class="form-group">
            <div class="col-lg-12">
                <div class="center-align">
                      <a hred="#" class="btn btn-sm btn-info" onclick="save_other_sick_off('.$visit_id.')">Save Sick Leave Note</a>
                      <a href="'.site_url().'print-sick-off/'.$visit_id.'" target="_blank" class="btn btn-sm btn-warning" >Print Note</a>
                  </div>
            </div>
        </div>
    </div>';
?>