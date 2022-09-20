<?php
$branch_session = $this->session->userdata('branch_id');

$branch_add = '';
$visit_branch_add = '';
if($branch_session > 0)
{
	$branch_add = ' AND branch_id = '.$branch_session;
	$visit_branch_add = ' AND visit.branch_id = '.$branch_session;
}
$i =0;
$where = 'patients.patient_delete = 0 AND category_id = 2 '.$branch_add;
$table = 'patients';
$uhdc_patient = $this->reception_model->count_items($table, $where);


$where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND DATE(patients.patient_date)'.$visit_branch_add;
$table = 'patients,visit,appointments';
$new_bookings = $this->reception_model->count_items($table, $where);




$where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0  AND appointments.appointment_date <= "'.date('Y-m-d').'"  '.$visit_branch_add;
$table = 'patients,visit,appointments';
$total_appointments = $this->reception_model->count_items($table, $where);



$where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND appointments.appointment_rescheduled = 0 AND (appointments.appointment_status = 4 ) AND appointments.appointment_date <= "'.date('Y-m-d').'" '.$visit_branch_add;
$table = 'patients,visit,appointments';
$honoured_appointments = $this->reception_model->count_items($table, $where);



$where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND appointments.appointment_rescheduled = 0 AND (appointments.appointment_status = 1 OR appointments.appointment_status = 5 OR appointments.appointment_status = 6 OR appointments.appointment_status = 2 OR appointments.appointment_status = 7) AND appointments.appointment_date <= "'.date('Y-m-d').'" '.$visit_branch_add;
$table = 'patients,visit,appointments';
$noshow_appointments = $this->reception_model->count_items($table, $where);


$where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND appointments.appointment_rescheduled = 0 AND (appointments.appointment_status = 3 ) AND appointments.appointment_date <= "'.date('Y-m-d').'" '.$visit_branch_add;
$table = 'patients,visit,appointments';
$cancelled_appointments = $this->reception_model->count_items($table, $where);


$where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND appointments.appointment_rescheduled = 1 AND appointments.appointment_date <= "'.date('Y-m-d').'" '.$visit_branch_add;
$table = 'patients,visit,appointments';
$rescheduled_appointments = $this->reception_model->count_items($table, $where);


$where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND appointments.appointment_rescheduled = 0 AND (appointments.appointment_status <> 3) AND appointments.appointment_date > "'.date('Y-m-d').'" '.$visit_branch_add;
$table = 'patients,visit,appointments';
$coming_appointments = $this->reception_model->count_items($table, $where);






// echo $month_patients.' '.$honoured_appointments;

?>
<div class="row">
	<!-- <div class="col-md-12"> -->
		<div class="col-md-2">
			<section class="card mb-4">
				<div class="card-body bg-primary">
					<div class="widget-summary">
						<!-- <div class="widget-summary-col widget-summary-col-icon">
							<div class="summary-icon">
								<i class="fa fa-calendar"></i>
							</div>
						</div> -->
						<div class="widget-summary-col">
							<div class="summary">
								<h4 class="title">Appointments</h4>
								<div class="info">
									<strong class="amount"><?php echo $total_appointments?></strong>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>

		<div class="col-md-2">
			<section class="card mb-4">
				<div class="card-body bg-success">
					<div class="widget-summary">
						<!-- <div class="widget-summary-col widget-summary-col-icon">
							<div class="summary-icon">
								<i class="fa fa-calendar"></i>
							</div>
						</div> -->
						<div class="widget-summary-col">
							<div class="summary">
								<h4 class="title">Honoured</h4>
								<div class="info">
									<strong class="amount"><?php echo $honoured_appointments?></strong>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div class="col-md-2">
			<section class="card mb-4">
				<div class="card-body bg-info">
					<div class="widget-summary">
						<!-- <div class="widget-summary-col widget-summary-col-icon">
							<div class="summary-icon">
								<i class="fa fa-calendar"></i>
							</div>
						</div> -->
						<div class="widget-summary-col">
							<div class="summary">
								<h4 class="title">No Show</h4>
								<div class="info">
									<strong class="amount"><?php echo $noshow_appointments?></strong>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</section>
		</div>

		<div class="col-md-2">
			<section class="card mb-4">
				<div class="card-body bg-warning">
					<div class="widget-summary">
						<!-- <div class="widget-summary-col widget-summary-col-icon">
							<div class="summary-icon">
								<i class="fa fa-calendar"></i>
							</div>
						</div> -->
						<div class="widget-summary-col">
							<div class="summary">
								<h4 class="title">Rescheduled</h4>
								<div class="info">
									<strong class="amount"><?php echo $rescheduled_appointments?></strong>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div class="col-md-2">
			<section class="card mb-4">
				<div class="card-body bg-danger">
					<div class="widget-summary">
						<!-- <div class="widget-summary-col widget-summary-col-icon">
							<div class="summary-icon">
								<i class="fa fa-calendar"></i>
							</div>
						</div> -->
						<div class="widget-summary-col">
							<div class="summary">
								<h4 class="title">Cancelled</h4>
								<div class="info">
									<strong class="amount"><?php echo $cancelled_appointments?></strong>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</section>
		</div>
		<div class="col-md-2">

			<section class="card mb-4">
				<div class="card-body bg-default">
					<div class="widget-summary">
						
						<div class="widget-summary-col">
							<div class="summary">
								<h4 class="title" style="color: #fff">Coming Appts</h4>
								<div class="info">
									<strong class="amount" style="color: #fff"><?php echo $coming_appointments;?></strong>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</section>
			
		</div>
		


	<!-- </div> -->
</div>

<?php
$result = '';
		
//if users exist display them
if ($todays_visit->num_rows() > 0)
{
	$count = $page;
	
	$result .= 
		'
			<table class="table table-hover table-bordered table-condensed ">
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
				<td>'.ucfirst(strtoupper($patient_surname)).'</td>
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
			<table class="table table-hover table-bordered table-condensed">
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
		$time_start = $row->appointment_start_time;
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
				<td>'.ucfirst(strtoupper($patient_surname)).'</td>
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
			<table class="table table-hover table-bordered table-condensed">
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
		$time_start = $row->appointment_start_time;
		$time_end = $row->time_end;
		$procedure_done = $row->procedure_done;
		$schedule_id = $row->schedule_id;
		if($schedule_id == 0)
		{
			$color_code = 'warning';
		}
		else
		{
			$color_code = 'default';
		}
		
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
				<td class="'.$color_code.'">'.ucfirst(strtoupper($patient_surname)).'</td>
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

$date_tomorrow = date("Y-m-d",strtotime("tomorrow"));
$dt= $date_tomorrow;
$dt1 = strtotime($dt);
$dt2 = date("l", $dt1);
$dt3 = strtolower($dt2);
if(($dt3 == "sunday"))
{
    // echo $dt3.' is weekend'."\n";

    $date_tomorrow = strtotime('+1 day', strtotime($dt));
    $date_tomorrow = date("Y-m-d",$date_tomorrow);
    $date_to_send = 'Monday';
} 
else
{
    // echo $dt3.' is not weekend'."\n";
     $date_tomorrow = $dt;
     $date_to_send = 'Tomorrow';
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
		          <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $date_to_send;?>'s Appointments <?php echo $date_tomorrow;?></h4>
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

<?php

// $where = 'patients.patient_delete = 0 ';
// $table = 'patients';
// $total_patient = $this->reception_model->count_items($table, $where);


// $where = 'patients.patient_delete = 0 AND category_id = 1';
// $table = 'patients';
// $new_patient = $this->reception_model->count_items($table, $where);

// $where = 'patients.patient_delete = 0 AND category_id = 3';
// $table = 'patients';
// $uncategorized_patient = $this->reception_model->count_items($table, $where);

// $where = 'patients.patient_delete = 0 AND category_id = 2';
// $table = 'patients';
// $uhdc_patient = $this->reception_model->count_items($table, $where);




$total_charts = '';
for ($i = 6; $i >= 0; $i--) {
    $months = date("Y-m", strtotime( date( 'Y-m-d' )." -$i months"));
    $months_explode = explode('-', $months);
    $year = $months_explode[0];
    $month = $months_explode[1];
    $last_visit = date('M Y',strtotime($months));
    $where = 'patients.patient_delete = 0 AND YEAR(patient_date) = '.$year.' AND MONTH(patient_date) = "'.$month.'" AND patients.patient_id IN (SELECT visit.patient_id FROM visit)'.$branch_add;
	$table = 'patients';
	$month_patients = $this->reception_model->count_items($table, $where);
	$total_charts .= '["'.$last_visit.'", '.$month_patients.'],';

}



// $where = 'patients.patient_delete = 0 AND gender_id = 1  AND patients.patient_id IN (SELECT visit.patient_id FROM visit)';
// 	$table = 'patients';
// 	$male_patients = $this->reception_model->count_items($table, $where);

// $where = 'patients.patient_delete = 0 AND gender_id = 2  AND patients.patient_id IN (SELECT visit.patient_id FROM visit)';

// $table = 'patients';

$where = 'visit_type_id > 0';
$table = 'visit_type';
$total_gender = '';
$visit_types = $this->dashboard_model->get_content($table, $where,'*',$group_by=NULL,$limit=NULL);

$count = 0;
if($visit_types->num_rows() > 0)
{
	foreach ($visit_types->result() as $key => $value) {
		# code...
		$visit_type_name = $value->visit_type_name;
		$visit_type_id = $value->visit_type_id;

		$where2 = 'visit.visit_type = '.$visit_type_id.' AND (parent_visit = 0 OR parent_visit IS NULL) and visit.visit_delete = 0 AND visit.visit_date > "2018-03-01" AND MONTH(visit.visit_date) = "'.date('m').'" AND YEAR(visit.visit_date) = "'.date('Y').'" '.$visit_branch_add;
		$table2 = 'visit';
		$total_patients = $this->reception_model->count_items($table2, $where2);
		$count++;
		$color = $this->reception_model->random_color();
		$total_gender .= '{
		                        label: "'.$visit_type_name.'",
		                        data: [
		                            ['.$count.', '.$total_patients.']
		                        ],
		                        color: "'.$color.'",
		                    },';
	}
}



// var_dump($total_gender); die();


// $total_visits = '';
// for ($k = 6; $k >= 0; $k--) {
//     $months = date("Y-m", strtotime( date( 'Y-m-d' )." -$k months"));
//     $months_explode = explode('-', $months);
//     $year = $months_explode[0];
//     $month = $months_explode[1];
//     $last_visit = date('M Y',strtotime($months));
   
//     $community_where ='patients.patient_id = visit.patient_id AND visit.personnel_id > 0 AND visit.revisit = 0 AND patients.patient_type = 0 AND  YEAR(visit.visit_date) = '.$year.' AND MONTH(visit.visit_date) = "'.$month.'"';
//     $community_table = 'visit,patients';
//     $total_number_new = $this->reception_model->count_items($community_table, $community_where);

//     $community_where ='patients.patient_id = visit.patient_id AND visit.personnel_id > 0 AND visit.revisit = 1 AND patients.patient_type = 0 AND  YEAR(visit.visit_date) = '.$year.' AND MONTH(visit.visit_date) = "'.$month.'"';
//     $community_table = 'visit,patients';
//     $total_number_old = $this->reception_model->count_items($community_table, $community_where);

// 	$total_visits .= '{
// 	                        y: "'.$last_visit.'",
// 	                        a: '.$total_number_new.',
// 	                        b: '.$total_number_old.'
// 	                    },';

// }


$total_visit_report='';
$year = date('Y');
$month = date('m');

$community_where ='patients.patient_id = visit.patient_id AND visit.personnel_id > 0 AND visit.revisit = 0 AND patients.patient_type = 0 AND  YEAR(visit.visit_date) = '.$year.' AND MONTH(visit.visit_date) = "'.$month.'"'.$visit_branch_add;
$community_table = 'visit,patients';
$total_number_new = $this->reception_model->count_items($community_table, $community_where);

$community_where ='patients.patient_id = visit.patient_id AND visit.personnel_id > 0 AND visit.revisit = 1 AND patients.patient_type = 0 AND  YEAR(visit.visit_date) = '.$year.' AND MONTH(visit.visit_date) = "'.$month.'"'.$visit_branch_add;
$community_table = 'visit,patients';
$total_number_old = $this->reception_model->count_items($community_table, $community_where);
$color = $this->reception_model->random_color();

$total_visit_report .= '{
		                        label: "New Visits ('.$total_number_new.')",
		                        data: [
		                            [1, '.$total_number_new.']
		                        ],
		                        color: "purple",
		                    },';
$color = $this->reception_model->random_color();		                   
$total_visit_report .= '{
		                        label: "Re-visits ('.$total_number_old.')",
		                        data: [
		                            [2, '.$total_number_old.']
		                        ],
		                        color: "green",
		                    },';		    



$this->db->where('place.place_delete = 0');

$query = $this->db->get('place');


$patients_list = '';
$patient_count = 0;
$amount_total = 0;
if($query->num_rows() > 0)
{
	foreach ($query->result() as $key => $value) {
		// code...

		$place_name = $value->place_name;
		$place_id = $value->place_id;

		$community_where ='patients.patient_delete = 0 AND  YEAR(patients.patient_date) = '.$year.' AND patients.about_us = '.$place_id.'  AND MONTH(patients.patient_date) = "'.$month.'"';
		$community_table = 'patients';
		$patient_numbers = $this->reception_model->count_items($community_table, $community_where);

		(int)$month;

	

			$where ='patients.patient_delete = 0 AND  YEAR(patients.patient_date) = '.$year.' AND patients.about_us = '.$place_id.'  AND MONTH(patients.patient_date) = "'.$month.'"  AND YEAR(visit.visit_date) = "'.$year.'" AND MONTH(visit.visit_date) = "'.$month.'" AND visit.patient_id = patients.patient_id AND visit_charge.visit_id = visit.visit_id AND visit.visit_delete = 0 AND visit_charge.charged = 1 AND visit_charge.visit_charge_delete = 0';
			$table = 'patients,visit_charge,visit';
			$select = 'SUM(visit_charge.visit_charge_amount*visit_charge.visit_charge_units) AS number';
			$cpm_group = 'patients.about_us';
			$amount = $this->dashboard_model->count_items_group($table, $where, $select,$cpm_group);

			$patient_count += $patient_numbers;
			$amount_total += $amount;
			$patients_list .= '<tr>
									<th>'.$place_name.'</th>
									<td><a onclick="open_patients('.$place_id.','.$year.','.$month.')">'.$patient_numbers.'</a></td>
									<td><a onclick="open_patients('.$place_id.','.$year.','.$month.')">'.number_format($amount,2).'</a></td>

								</tr>';
	

	}
	$patients_list .= '<tr>
									<th>TOTALS</th>
									<td>'.$patient_count.'</td>
									<td>'.number_format($amount_total,2).'</td>

								</tr>';
}
// var_dump($total_visit_report); die();
?>
<!-- start: page -->
<div class="row" style="margin-top:20px;">
	<div class="col-md-6 col-lg-12 col-xl-12">
		<section class="panel">
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-4">						
						<section class="card">
					            <header class="card-header">				               

					                <h4 class="card-title">Patients visit</h4>
					                <p class="card-subtitle">Comparison of either New Visit or Re Visit for  <?php echo date('M')?> </p>
					            </header>
					            <div class="card-body">

					                <!-- Flot: Pie -->
					                <div class="chart chart-md" id="flotPie2"></div>
					                <script type="text/javascript">
					                    var flotPieData2 = [<?php echo $total_visit_report?>];

					                    // See: js/examples/examples.charts.js for more settings.
					                </script>

					            </div>
					    </section>
						
					</div>
					<div class="col-lg-4">
						<section class="card">
					            <header class="card-header">				               

					                <h4 class="card-title">Patients Visit Category</h4>
					                <p class="card-subtitle">Comparison of visit types for <?php echo date('M')?> </p>
					            </header>
					            <div class="card-body">

					                <!-- Flot: Pie -->
					                <div class="chart chart-md" id="flotPie"></div>
					                <script type="text/javascript">
					                    var flotPieData = [<?php echo $total_gender?>];

					                    // See: js/examples/examples.charts.js for more settings.
					                </script>

					            </div>
					    </section>
                   
					</div>

					<div class="col-lg-4">
						<section class="card">
					            <header class="card-header">				               

					                <h4 class="card-title">Patients</h4>
					                <p class="card-subtitle">Comparison of how patients came to know about the clinic in <?php echo date('M')?></p>
					            </header>
					            <div class="card-body">
					            	<table class="table table-condensed table-bordered">
					            		<thead>
					            			<thead>
					            				<th>Place</th>
					            				<th>Count</th>
					            				<th>Revenue</th>
					            			</thead>
					            		</thead>
					            		<tbody>
					            			<?php echo $patients_list;?>
					            		</tbody>
					            	</table>

					            </div>
					        </section>
                   
					</div>
				</div>
			</div>
		</section>
	</div>
	
</div>

<?php

$where = 'patients.patient_delete = 0 '.$branch_add;
$table = 'patients';
$total_patient = $this->reception_model->count_items($table, $where);


$where = 'patients.patient_delete = 0 AND category_id = 1'.$branch_add;
$table = 'patients';
$new_patient = $this->reception_model->count_items($table, $where);

$where = 'patients.patient_delete = 0 AND category_id = 3'.$branch_add;
$table = 'patients';
$uncategorized_patient = $this->reception_model->count_items($table, $where);

$where = 'patients.patient_delete = 0 AND category_id = 2'.$branch_add;
$table = 'patients';
$uhdc_patient = $this->reception_model->count_items($table, $where);





$chart_array = array();
$total_charts = '';
for ($i = 12; $i >= 0; $i--) {
    $months = date("Y-m", strtotime( date( 'Y-m-d' )." -$i months"));
    $months_explode = explode('-', $months);
    $year = $months_explode[0];
    $month = $months_explode[1];
    $last_visit = date('M Y',strtotime($months));
    $where = 'patients.patient_delete = 0 AND category_id = 2 AND YEAR(patient_date) = '.$year.' AND MONTH(patient_date) = "'.$month.'" '.$branch_add;
	$table = 'patients';
	$month_patients = $this->reception_model->count_items($table, $where);
	$total_charts .= '["'.$last_visit.'", '.$month_patients.'],';

}

?>

<!-- start: page -->
<div class="row">
	<div class="col-md-6 col-lg-12 col-xl-12">
		<section class="panel">
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-7">
						<section class="card">
				            <header class="card-header">
				               	<h4 class="card-title">Patient Turnover</h4>
				                <p class="card-subtitle">Displays the patients turnover in the hospital for the past six months according to registrations</p>
				            </header>
				            <div class="card-body">

				                <!-- Flot: Bars -->
				                <div class="chart chart-md" id="flotBars"></div>
				                <script type="text/javascript">
				                    var flotBarsData = [<?php echo $total_charts?>
				                    ];

				                    // See: js/examples/examples.charts.js for more settings.
				                </script>

				            </div>
				        </section>
					</div>
					<div class="col-lg-5 text-center">
						<h2 class="panel-title mt-md">PATIENTS RECORDS</h2>
						<section class="panel">
							<div class="panel-body">
								<div class="widget-summary">	
									<div class="widget-summary-col">
										<div class="summary">
											<div class="row">
												<div class="col-md-12">
													<h4 class="title">Total Patients</h4>
													<div class="info">
														<strong class="amount"> <?php echo $total_patient;?></strong>
													</div>
												</div>
											</div>
											<br/>
											<div class="row">
												<div class="col-md-6">
													<h4 class="title">Patients With file no.</h4>
													<div class="info">
														<strong class="amount"> <?php echo $uhdc_patient;?></strong>
													</div>
												</div>
												<div class="col-md-6">
													<h4 class="title">Patients With no file no.</h4>
													<div class="info">
														<strong class="amount"> <?php echo $new_patient;?></strong>
													</div>
												</div>
												
											</div>
											
										</div>
									</div>
								</div>
							</div>
						</section>

						
					</div>
				</div>
				
			</div>
		</section>
	</div>
	
</div>
<script type="text/javascript">
	function open_patients(place_id,year,month)
	{
		open_sidebar();

		var config_url = $('#config_url').val();
        var data_url = config_url+"admin/get_all_patients/"+place_id+"/"+year+"/"+month;

    	// alert(data_url);
        
        $.ajax({
        type:'POST',
        url: data_url,
        data:{visit_id: 1},
        dataType: 'text',
		success:function(data)
		{
			$("#sidebar-div").html(data);		
		},
        error: function(xhr, status, error) {
	        //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	        alert(error);
        }

        });
	}
</script>
