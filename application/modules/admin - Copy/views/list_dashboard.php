<?php
$result = '';
		
//if users exist display them
if ($todays_visit->num_rows() > 0)
{
	$count = $page;
	
	$result .= 
		'
			<table class="table table-hover table-bordered ">
			  <thead>
				<tr>
				  <th>#</th>
				  <th>Patient</th>
				  <th>Phone</th>
				  <th>Time</th>
				  <th>Doctor</th>
				</tr>
			  </thead>
			  <tbody>
		';
	
	$personnel_query = $this->personnel_model->all_personnel();
	
	foreach ($todays_visit->result() as $row)
	{
		// $appointment_start = date('Y-m-d');
		// $visit_dates_query = $this->reception_model->get_all_appointments_dates($appointment_start);
		

		$visit_date = date('jS M Y',strtotime($row->visit_date));
		$visit_date_old = $row->visit_date;
		$visit_time = date('H:i a',strtotime($row->visit_time));
		if($row->visit_time_out != '0000-00-00 00:00:00')
		{
			$visit_time_out = date('H:i a',strtotime($row->visit_time_out));
		}
		else
		{
			$visit_time_out = '-';
		}
		$visit_id = $row->visit_id;
		$patient_id = $row->patient_id;
		$personnel_id3 = $row->personnel_id;
		$strath_no = $row->strath_no;
		$patient_number = $row->patient_number;
		$room_id2 = $row->room_id;
		$patient_year = $row->patient_year;
		$coming_from = $this->reception_model->coming_from($visit_id);
		$sent_to = $this->reception_model->going_to($visit_id);
		$patient_othernames = $row->patient_othernames;
		$patient_surname = $row->patient_surname;
		$patient_date_of_birth = $row->patient_date_of_birth;
		$patient_national_id = $row->patient_national_id;
		$patient_phone = $row->patient_phone1;
		$time_start = $row->time_start;
		$time_end = $row->time_end;
		$procedure_done = $row->procedure_done;
		
		//creators and editors
		if($personnel_query->num_rows() > 0)
		{
			$personnel_result = $personnel_query->result();
			
			foreach($personnel_result as $adm)
			{
				$personnel_id2 = $adm->personnel_id;
				
				if($personnel_id3 == $personnel_id2)
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
		
		$count++;
		
		$result .= 
		'
			<tr>
				<td>'.$count.'</td>
				<td>'.$patient_surname.' '.$patient_othernames.'</td>
				<td>'.$patient_phone.'</td>	
				<td>'.$time_start.'</td>	
				<td>'.$doctor.'</td>	
				
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
	$result .= "There are no queued patients";
}


$todays_appointments_list = '';
		
//if users exist display them
if ($appointment_list->num_rows() > 0)
{
	$count = $page;
	
	$todays_appointments_list .= 
		'
			<table class="table table-hover table-bordered ">
			  <thead>
				<tr>
				  <th>#</th>
				  <th>Patient</th>
				  <th>Phone</th>
				  <th>Time</th>
				  <th>Doctor</th>
				</tr>
			  </thead>
			  <tbody>
		';
	
	$personnel_query = $this->personnel_model->all_personnel();
	
	foreach ($appointment_list->result() as $row)
	{
		// $appointment_start = date('Y-m-d');
		// $visit_dates_query = $this->reception_model->get_all_appointments_dates($appointment_start);
		

		$visit_date = date('jS M Y',strtotime($row->visit_date));
		$visit_date_old = $row->visit_date;
		$visit_time = date('H:i a',strtotime($row->visit_time));
		if($row->visit_time_out != '0000-00-00 00:00:00')
		{
			$visit_time_out = date('H:i a',strtotime($row->visit_time_out));
		}
		else
		{
			$visit_time_out = '-';
		}
		$visit_id = $row->visit_id;
		$patient_id = $row->patient_id;
		$personnel_id3 = $row->personnel_id;
		$dependant_id = $row->dependant_id;
		$strath_no = $row->strath_no;
		$patient_number = $row->patient_number;
		$room_id2 = $row->room_id;
		$patient_year = $row->patient_year;
		$coming_from = $this->reception_model->coming_from($visit_id);
		$sent_to = $this->reception_model->going_to($visit_id);
		$patient_othernames = $row->patient_othernames;
		$patient_surname = $row->patient_surname;
		$patient_date_of_birth = $row->patient_date_of_birth;
		$patient_national_id = $row->patient_national_id;
		$patient_phone = $row->patient_phone1;
		$time_start = $row->time_start;
		$time_end = $row->time_end;
		$procedure_done = $row->procedure_done;
		
		//creators and editors
		if($personnel_query->num_rows() > 0)
		{
			$personnel_result = $personnel_query->result();
			
			foreach($personnel_result as $adm)
			{
				$personnel_id2 = $adm->personnel_id;
				
				if($personnel_id3 == $personnel_id2)
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
		
		$count++;
		
		$todays_appointments_list .= 
		'
			<tr>
				<td>'.$count.'</td>
				<td>'.$patient_surname.' '.$patient_othernames.'</td>
				<td>'.$patient_phone.'</td>	
				<td>'.$time_start.'</td>	
				<td>'.$doctor.'</td>	
				
			</tr> 
		';
	}
	

	$todays_appointments_list .= 
	'
				  </tbody>
				</table>
	';
}

else
{
	$todays_appointments_list .= "There are no appointents for this day";
}



$tomorrows_appointments_list = '';
		
//if users exist display them
if ($tomorrows_appointments->num_rows() > 0)
{
	$count = $page;
	
	$tomorrows_appointments_list .= 
		'
			<table class="table table-hover table-bordered ">
			  <thead>
				<tr>
				  <th>#</th>
				  <th>Patient</th>
				  <th>Phone</th>
				  <th>Time</th>
				  <th>Doctor</th>
				</tr>
			  </thead>
			  <tbody>
		';
	
	$personnel_query = $this->personnel_model->all_personnel();
	
	foreach ($tomorrows_appointments->result() as $row)
	{

		$visit_date = date('jS M Y',strtotime($row->visit_date));
		$visit_date_old = $row->visit_date;
		$visit_time = date('H:i a',strtotime($row->visit_time));
		if($row->visit_time_out != '0000-00-00 00:00:00')
		{
			$visit_time_out = date('H:i a',strtotime($row->visit_time_out));
		}
		else
		{
			$visit_time_out = '-';
		}
		$visit_id = $row->visit_id;
		$patient_id = $row->patient_id;
		$personnel_id3 = $row->personnel_id;
		$dependant_id = $row->dependant_id;
		$strath_no = $row->strath_no;
		$patient_number = $row->patient_number;
		$room_id2 = $row->room_id;
		$patient_year = $row->patient_year;
		$coming_from = $this->reception_model->coming_from($visit_id);
		$sent_to = $this->reception_model->going_to($visit_id);
		$patient_othernames = $row->patient_othernames;
		$patient_surname = $row->patient_surname;
		$patient_date_of_birth = $row->patient_date_of_birth;
		$patient_national_id = $row->patient_national_id;
		$patient_phone = $row->patient_phone1;
		$time_start = $row->time_start;
		$time_end = $row->time_end;
		$procedure_done = $row->procedure_done;
		
		//creators and editors
		if($personnel_query->num_rows() > 0)
		{
			$personnel_result = $personnel_query->result();
			
			foreach($personnel_result as $adm)
			{
				$personnel_id2 = $adm->personnel_id;
				
				if($personnel_id3 == $personnel_id2)
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
		
		$count++;
		
		$tomorrows_appointments_list .= 
		'
			<tr>
				<td>'.$count.'</td>
				<td>'.$patient_surname.' '.$patient_othernames.'</td>
				<td>'.$patient_phone.'</td>	
				<td>'.$time_start.'</td>	
				<td>'.$doctor.'</td>	
				
			</tr> 
		';
	}
	

	$tomorrows_appointments_list .= 
	'
				  </tbody>
				</table>
	';
}

else
{
	$tomorrows_appointments_list .= "There are no appointents for this day";
}

?>
<div class="row">
	<!-- <div class="col-md-12"> -->
		<div class="col-md-4">
			<section class="panel panel-primary">
			    <header class="panel-heading">
		          <h4 class="pull-left"><i class="icon-reorder"></i>Today's Visits <?php echo date('Y-m-d');?></h4>
		          <div class="widget-icons pull-right">
		            <a href="#" class="wminimize"><i class="icon-chevron-up"></i></a> 
		          </div>
		          <div class="clearfix"></div>
		        </header>
		      	<div class="panel-body">
					<!-- <div class="padd"> -->
						<?php echo $result?>
					<!-- </div> -->
		        </div>
			</section>
		</div>
		<div class="col-md-4">
			<section class="panel panel-info">
			    <header class="panel-heading">
		          <h4 class="pull-left"><i class="icon-reorder"></i>Today's Appointments <?php echo date('Y-m-d');?></h4>
		          <div class="widget-icons pull-right">
		            <a href="#" class="wminimize"><i class="icon-chevron-up"></i></a> 
		          </div>
		          <div class="clearfix"></div>
		        </header>
		      	<div class="panel-body">
					<div class="padd">
						<?php echo $todays_appointments_list;?>
					</div>
		        </div>
			</section>
		</div>
		<div class="col-md-4">
			<section class="panel panel-warning">
			    <header class="panel-heading">
		          <h4 class="pull-left"><i class="icon-reorder"></i>Tomorrow's Appointments <?php echo date("Y-m-d",strtotime("tomorrow"));;?></h4>
		          <div class="widget-icons pull-right">
		            <a href="#" class="wminimize"><i class="icon-chevron-up"></i></a> 
		          </div>
		          <div class="clearfix"></div>
		        </header>
		      	<div class="panel-body">
					<div class="padd">
						<?php echo $tomorrows_appointments_list;?>
					</div>
		        </div>
			</section>
		</div>
	<!-- </div> -->
</div>
