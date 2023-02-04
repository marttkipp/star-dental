<?php
$patient_items = '';
if($query->num_rows() > 0)
{
	foreach ($query->result() as $key => $res) 
	{
		# code...
		$v_data['appointment_query'] = $query->result();

		$visit_date = date('D M d Y',strtotime($res->appointment_date)); 
		$date_created = date('D M d Y',strtotime($res->date_created)); 
		$appointment_start_time = $res->appointment_start_time; 
		$personnel_fname = $res->personnel_fname; 
		$personnel_onames = $res->personnel_onames; 
		$appointment_end_time = $res->appointment_end_time; 
		$time_start = $res->appointment_date_time_start; 
		$time_end = $res->appointment_date_time_end;
		$patient_id = $res->patient_id;
		$patient_othernames = $res->patient_othernames;
		$patient_surname = $res->patient_surname;				
		$visit_id = $res->visit_id;
		$appointment_id = $res->appointment_id;
		$resource_id = $res->resource_id;
		$event_name = $res->event_name;
		$event_description = $res->event_description;
		$appointment_status = $res->appointment_status;
		$appointment_type = $res->appointment_type;
		$procedure_done = '';//$res->procedure_done;
		$resource_id = $res->resource_id;
		$patient_data = $patient_surname;
		$patient_phone1 = $res->patient_phone1;
		$patient_email = $res->patient_email;
		$category_id = $res->category_id;
		$patient_number = $res->patient_number;
		if($appointment_status == 0)
		{
			$color = 'blue';
			$status_name = 'unassigned';
		}
		else if($appointment_status == 1)
		{
			$color = '';
			$status_name = 'unassigned';
		}
		else if($appointment_status == 2)
		{
			$color = 'green';
			$status_name = 'Confirmed';
		}
		else if($appointment_status == 3)
		{
			$color = 'red';
			$status_name = 'Cancelled';
		}
		else if($appointment_status == 4)
		{
			$color = 'purple';
			$status_name = 'Showed';
		}
		else if($appointment_status == 5)
		{
			$color = 'black';
			$status_name = 'No Showed';
		}
		else if($appointment_status == 6)
		{
			$color = 'DarkGoldenRod';
			$status_name = 'Notified';
		}
		else if($appointment_status == 7)
		{
			$color = '';
			$status_name = 'Not Notified';
		}
		else
		{
			$color = 'orange';
			$status_name = '';
		}
		if(empty($patient_data))
		{
			$patient_data = '';
		}
		if(empty($procedure_done))
		{
			$procedure_done = '';
		}

		$data['status'] = $appointment_status;
		$data['appointment_id'] = $appointment_id;
		$data['appointment_type'] = $appointment_type;
		$v_data['doctors'] = $this->reception_model->get_doctor();


			$list_order = 'list_name';		    
			$list_where = 'list_id > 0';
			$list_table = 'schedule_list';

			$list_query = $this->reception_model->get_all_visit_type_details($list_table, $list_where,$list_order);

			$rs14 = $list_query->result();
			$list = '';
			foreach ($rs14 as $list_rs) :


			  $list_id = $list_rs->list_id;
			  $list_name = $list_rs->list_name;

			  $list .="<option value='".$list_id."'>".$list_name."</option>";

			endforeach;
			$v_data['list'] = $list;
			$v_data['appointment_id'] = $appointment_id;
			$v_data['visit_id'] = $visit_id;
			$v_data['patient_id'] = $patient_id;

			$data['visit_id'] = $visit_id;
			$data['patient_id'] = $patient_id;

			if($appointment_status == 4)
			{
				$marked = 'display:none;';
				
			}
			else if($appointment_status == 7)
			{
				$marked = 'display:none;';
				
			}
			else
			{
				$marked = 'display:block;';
				
			}


			$patient_items .= '
			        	';

			if($appointment_status == 4)
			{
				$in_clinic = 'display:none;';
				$out_clinic = 'display:block;';
				$rescheduled = 'display:none;';
			}
			else if($appointment_status == 7)
			{
				$in_clinic = 'display:none;';
				$out_clinic = 'display:none;';
				$rescheduled = 'display:none;';
			}
			else
			{
				$in_clinic = 'display:block;';
				$out_clinic = 'display:block;';
				$rescheduled = 'display:block;';
			}

			$data['buttons'] = '
				            	<button type="button" class="btn btn-primary pull-left" data-dismiss="modal" onclick="update_event_status('.$appointment_id.',4)" style="'.$in_clinic.'">IN CLINIC </button>
				            	
				            	<button type="button" class="btn btn-success pull-left" data-dismiss="modal" onclick="update_event_status('.$appointment_id.',7)"  style="'.$out_clinic.'"> Out of Clinic </button>
				            	<button type="button" class="btn btn-primary pull-left" data-dismiss="modal" onclick="resheduled_appointments('.$appointment_id.',1)" style="'.$rescheduled.'"> Mark as rescheduled appointment </button>';

			

		
		

	}

}

// var_dump($appointment_type);die();

if($appointment_type == 1)
{
?>


<ul class="nav nav-tabs nav-justified">
    <li class="active"><a href="#vitals-pane" data-toggle="tab" onclick="get_appointment_details(<?php echo $appointment_id;?>)">Appointment Info</a></li>
   <!--  <li><a href="#update-patient-procedures" data-toggle="tab" onclick="update_patient_procedures(<?php echo $appointment_id;?>)">Patient Procedures</a></li> -->
    <li><a href="#lists-pane" data-toggle="tab" onclick="reschedule_request(<?php echo $appointment_id;?>)">Reschedule Appt</a></li>
    <li><a href="#edit-patient-details" data-toggle="tab" onclick="edit_patients_div(<?php echo $appointment_id;?>)">Edit Patient</a></li>
    <!-- <li><a href="#schedule-allocation" data-toggle="tab" onclick="get_allocation_view(<?php echo $appointment_id;?>,<?php echo $visit_id;?>,<?php echo $patient_id;?>)">Allocate to schedule</a></li> -->
    <!-- <li><a href="#reminders" data-toggle="tab" onclick="get_reminders_div(<?php echo $appointment_id;?>,<?php echo $visit_id;?>,<?php echo $patient_id;?>)"> Reminders</a></li> -->

    <!-- <li><a href="#special-notes" data-toggle="tab" onclick="get_special_notes_div(<?php echo $appointment_id;?>,<?php echo $visit_id;?>,<?php echo $patient_id;?>)">Notes</a></li> -->
     
    
    <!-- <li><a href="#correspondence" data-toggle="tab" onclick="get_correspondence_div(<?php echo $appointment_id;?>,<?php echo $visit_id;?>,<?php echo $patient_id;?>)">Correspondence</a></li> -->

    <!-- <li><a href="#notes-pane" data-toggle="tab" onclick="add_appointment_note(<?php echo $appointment_id;?>)">Notes</a></li>  -->
</ul>
<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
    <div class="tab-pane active" id="vitals-pane">

    	<div id="appointment-details"></div>
      	
    </div>
     <div class="tab-pane " id="update-patient-procedures">
    	<div id="edit-procedures-div"></div>
      
	</div>	
    <div class="tab-pane " id="lists-pane">
    	<div id="reschedule-div"></div>
      
	</div>		
	<div class="tab-pane " id="schedule-allocation">
		<div id="allocation-div"></div>
    	
	    <div id="recall-view"></div>
      
	</div>
	<div class="tab-pane " id="reminders">
    	<div id="reminders-div"></div>
    	<div id="reminder-view"></div>
      
	</div>
	<div class="tab-pane " id="special-notes">
    	<div id="special-notes-div"></div>
    	<div id="special-notes-view"></div>
      
	</div>	
	<div class="tab-pane " id="edit-patient-details">
		<div id="edit-patient-div"></div>
      	
    </div>
    <div class="tab-pane" id="correspondence">
    	<div id="correspondence-div"></div>
    	<div id="correspondence-view"></div>
    	
    </div>
    <div class="tab-pane " id="notes-pane">
    	<div id="appointment-note-div"></div>
    	
      	
    </div>
</div>
<?php
}
else
{
	?>
		<ul class="nav nav-tabs nav-justified">
		    <li class="active"><a href="#vitals-pane" data-toggle="tab">Event Details</a></li>
		</ul>
		<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
		    <div class="tab-pane active" id="vitals-pane">
			<div class="row">
	      		<div class="col-md-5">
	      			

	        		<p><h4><strong>Appointment Details</strong> </h4></p>
	        		<p><strong>Title</strong> <?php echo $event_name;?>  <?php echo $event_description;?></p>
	        		<strong>Start date</strong> <?php echo $visit_date;?> <?php echo $appointment_start_time;?><br/>
	        		<strong>End Date</strong> <?php echo $visit_date;?> <?php echo $appointment_end_time;?><br/>
	        		<strong>Status</strong> <?php echo $status_name;?><br/>
	        		<strong>Created By</strong> <?php echo $personnel_fname;?> <?php echo $personnel_onames;?><br/>
	        		<strong>Created On</strong> <?php echo $date_created;?><br/>
	        		
	      		</div>
	      		<div class="col-md-7">
	      			<!-- edit appointment details -->
	      			<?php
	      			$v_data['appointment_id'] = $appointment_id;
	      			$this->load->view('edit_event_view', $v_data);
	      			?>
	      		</div>
	      		
	      	</div>
	      	
	     </div>
	</div>
	<?php
}
?>

<div class="row" style="margin-top: 5px;">
    <div class="col-md-12 center-align">
        <a  class="btn btn-sm btn-info" onclick="close_side_bar()"><i class="fa fa-folder-closed"></i> CLOSE SIDEBAR</a>
       	<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" onclick="delete_event_details(<?php echo $appointment_id;?>,1)">Delete </button>
        	
    </div>
</div>
