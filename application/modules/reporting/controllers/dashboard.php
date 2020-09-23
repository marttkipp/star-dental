<?php

$where = 'patients.patient_delete = 0 ';
$table = 'patients';
$total_patient = $this->reception_model->count_items($table, $where);


$where = 'patients.patient_delete = 0 AND category_id = 1';
$table = 'patients';
$new_patient = $this->reception_model->count_items($table, $where);

$where = 'patients.patient_delete = 0 AND category_id = 3';
$table = 'patients';
$uncategorized_patient = $this->reception_model->count_items($table, $where);

$where = 'patients.patient_delete = 0 AND category_id = 2';
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
    $where = 'patients.patient_delete = 0 AND category_id = 2 AND YEAR(patient_date) = '.$year.' AND MONTH(patient_date) = "'.$month.'" ';
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
						<div class="chart-data-selector" id="salesSelectorWrapper">
							<h2>
								Patients Turnover:
							
							</h2>

							<div id="salesSelectorItems" class="chart-data-selector-items mt-sm">
								<!-- Flot: Sales Porto Admin -->
								<div class="chart chart-sm" data-sales-rel="Porto Admin" id="flotDashSales1" class="chart-active"></div>
								<script>

									var flotDashSales1Data = [{
									    data: [
									        
									        	<?php echo $total_charts?>
									    ],
									    color: "#CCCCCC"
									}];

									// See: assets/javascripts/dashboard/examples.dashboard.js for more settings.

								</script>

								
							</div>

						</div>
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


<?php


$six_month = date("Y-m-01");
$today_month = date("Y-m-d");
?>


<!-- start: page -->
<h4 class="mt-none">Patient Attendance Report</h4>

<div class="row">
	<div class="col-md-6">
		<p class="mb-lg pull-left" >This is the population of patients attendance for <?php echo date('M Y');?> .</p>
	</div>
	<div class="col-md-6 pull-right">
		<a href="<?php echo site_url().'print-summary/1/'.$six_month.'/'.$today_month.''?>" target="_blank" class="btn btn-sm btn-warning"><i class="fa fa-print"></i> Print <?php echo date('M Y');?> Summary Report</a>
		<a target="_blank" href="<?php echo site_url().'export-bookings/1/'.$six_month.'/'.$today_month.''?>" class="btn btn-sm btn-success"><i class="fa fa-print"></i> Export <?php echo date('M Y');?> Bookings Report</a>
	</div>
</div>


<?php

$where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND DATE(patients.patient_date) BETWEEN "'.$six_month.'" AND "'.$today_month.'" ';
$table = 'patients,visit,appointments';
$new_bookings = $this->reception_model->count_items($table, $where);




$where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND (appointments.appointment_status = 4 OR appointments.appointment_status = 7 OR appointments.appointment_status = 1) AND visit.visit_date BETWEEN "'.$six_month.'" AND "'.$today_month.'" ';
$table = 'patients,visit,appointments';
$total_appointments = $this->reception_model->count_items($table, $where);



$where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND appointments.appointment_rescheduled = 0 AND (appointments.appointment_status = 4 OR appointments.appointment_status = 7) AND visit.visit_date BETWEEN "'.$six_month.'" AND "'.$today_month.'" ';
$table = 'patients,visit,appointments';
$honoured_appointments = $this->reception_model->count_items($table, $where);


$where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND appointments.appointment_rescheduled = 1 AND visit.visit_date BETWEEN "'.$six_month.'" AND "'.$today_month.'" ';
$table = 'patients,visit,appointments';
$rescheduled_appointments = $this->reception_model->count_items($table, $where);




$where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND appointments.appointment_rescheduled = 0 AND (appointments.appointment_status = 4 OR appointments.appointment_status = 7) AND visit.visit_date BETWEEN "'.$six_month.'" AND "'.$today_month.'" ';
$table = 'patients,visit,appointments';
$month_patients = $this->reception_model->count_items($table, $where);
$showed_charts = '['.$i.', '.$month_patients.'],';


 $reschedule_where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND appointments.appointment_rescheduled = 1  AND visit.visit_date BETWEEN "'.$six_month.'" AND "'.$today_month.'" ';
$reschedule_table = 'patients,visit,appointments';
$reschedule_month_patients = $this->reception_model->count_items($reschedule_table, $reschedule_where);
$rescheduled_charts = '['.$i.', '.$reschedule_month_patients.'],';


$no_show_where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND (appointments.appointment_status = 1) AND appointments.appointment_rescheduled = 0 AND visit.visit_date BETWEEN "'.$six_month.'" AND "'.$today_month.'" ';
$no_show_table = 'patients,visit,appointments';
$no_show_month_patients = $this->reception_model->count_items($no_show_table, $no_show_where);
$no_show_charts = '['.$i.', '.$no_show_month_patients.'],';

$total_appointments = $no_show_month_patients+$reschedule_month_patients+$month_patients;




if(empty($honoured_appointments))
{
	$percentage_honored =0;
}
else
{
	$percentage_honored = round(($honoured_appointments *100) /$total_appointments) ;
}


// echo $month_patients.' '.$honoured_appointments;

?>

<div class="row">
	<div class="col-sm-3">
		<section class="card card-featured-left card-featured-primary mb-3">
			<div class="card-body">
				<div class="widget-summary">
					<div class="widget-summary-col widget-summary-col-icon">
						<div class="summary-icon bg-primary">
							<i class="fa fa-user"></i>
						</div>
					</div>
					<div class="widget-summary-col">
						<div class="summary">
							<h4 class="title">New Patients</h4>
							<div class="info">
								<strong class="amount"><?php echo $new_bookings;?></strong>
								<!-- <span class="text-primary">(14 unread)</span> -->
							</div>
						</div>
						<div class="summary-footer">
							<a class="text-muted text-uppercase" target="_blank" href="<?php echo site_url().'export-patients/1/'.$six_month.'/'.$today_month.''?>">(export)</a>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	<div class="col-sm-3">
		<section class="card card-featured-left card-featured-secondary">
			<div class="card-body">
				<div class="widget-summary">
					<div class="widget-summary-col widget-summary-col-icon">
						<div class="summary-icon bg-secondary">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
					<div class="widget-summary-col">
						<div class="summary">
							<h4 class="title">Total Bookings</h4>
							<div class="info">
								<strong class="amount"><?php echo $total_appointments?></strong>
							</div>
						</div>
						<div class="summary-footer">
							<a class="text-muted text-uppercase" target="_blank" href="<?php echo site_url().'export-bookings/1/'.$six_month.'/'.$today_month.''?>">(export)</a>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	<div class="col-sm-3">
		<section class="card card-featured-left card-featured-tertiary mb-3">
			<div class="card-body">
				<div class="widget-summary">
					<div class="widget-summary-col widget-summary-col-icon">
						<div class="summary-icon bg-tertiary">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
					<div class="widget-summary-col">
						<div class="summary">
							<h4 class="title">Honoured</h4>
							<div class="info">
								<strong class="amount"><?php echo $honoured_appointments;?></strong>
							</div>
						</div>
						<div class="summary-footer">
							<a class="text-muted text-uppercase" target="_blank" href="<?php echo site_url().'export-bookings/2/'.$six_month.'/'.$today_month.''?>">(export)</a>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	<div class="col-sm-3">
		<section class="card card-featured-left card-featured-quaternary">
			<div class="card-body">
				<div class="widget-summary">
					<div class="widget-summary-col widget-summary-col-icon">
						<div class="summary-icon bg-success">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
					<div class="widget-summary-col">
						<div class="summary">
							<h4 class="title">Rescheduled</h4>
							<div class="info">
								<strong class="amount"><?php echo $rescheduled_appointments?></strong>
							</div>
						</div>
						<div class="summary-footer">
							<a class="text-muted text-uppercase" target="_blank" href="<?php echo site_url().'export-bookings/3/'.$six_month.'/'.$today_month.''?>" >(export)</a>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
</div>

<div class="row">
	
	<div class="col-md-6">
		<section class="panel">
			<header class="panel-heading">
				<!-- <div class="panel-actions">
					<a href="ui-elements-charts.html#" class="panel-action panel-action-toggle" data-panel-toggle></a>
					<a href="ui-elements-charts.html#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
				</div> -->

				<h2 class="panel-title">Appointments Report</h2>
				<p class="panel-subtitle">Representation of patients appointments for the past six months in %.</p>
			</header>
			<div class="panel-body">
				
				<!-- Flot: Pie -->
				<div class="chart chart-md" id="flotPie"></div>
				<script type="text/javascript">

					var flotPieData = [{
						label: "Honoured",
						data: [
							<?php echo $showed_charts?>
						],
						color: '#228B22'
					}, {
						label: "Rescheduled",
						data: [
							<?php echo $rescheduled_charts?>
						],
						color: '#B22222'
					}, {
						label: "No Show",
						data: [
							<?php echo $no_show_charts?>
						],
						color: '#FF4500'
					}];

					// See: assets/javascripts/ui-elements/examples.charts.js for more settings.

				</script>

			</div>
		</section>
	</div>
	<div class="col-md-6">
		<section class="panel">
			<header class="panel-heading">
				<div class="panel-actions">
					<a href="ui-elements-charts.html#" class="panel-action panel-action-toggle" data-panel-toggle></a>
					<a href="ui-elements-charts.html#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
				</div>

				<h2 class="panel-title">Honoured Appointments</h2>
				<p class="panel-subtitle">Percentage of honoured bookings out of the total bookings for <?php echo date('M Y')?></p>
			</header>
			<div class="panel-body">
				<div class="row">
					
					<div class="col-md-12">
						<meter min="0" max="100" value="<?php echo $percentage_honored;?>" id="meterDark"></meter>
					</div>
				</div>
			</div>
		</section>
		
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<section class="panel">
			<header class="panel-heading">
				<!-- <div class="panel-actions">
					<a href="ui-elements-charts.html#" class="panel-action panel-action-toggle" data-panel-toggle></a>
					<a href="ui-elements-charts.html#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
				</div> -->

				<h2 class="panel-title">Doctors Appointments Record</h2>
				<p class="panel-subtitle">Display of patients appointment based bookings done for <?php echo date('M Y')?> .</p>
			</header>
			<div class="panel-body">
<?php
				$doctors = $this->reception_model->get_all_doctors();
				$doctor_view = '';

				$total_honoured = 0;
				$total_old_showed_charts = '';
				if($doctors->num_rows() > 0)
				{
					foreach ($doctors->result() as $key => $value) {
						# code...
						$personnel_fname = $value->personnel_fname;
						$personnel_onames = $value->personnel_onames;
						$personnel_id = $value->personnel_id;


						$six_month = date("Y-m-01");
					    $today_month = date("Y-m-d");

					    $where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND appointments.appointment_rescheduled = 0 AND (appointments.appointment_status = 4 OR appointments.appointment_status = 7) AND visit.visit_date BETWEEN "'.$six_month.'" AND "'.$today_month.'" AND visit.personnel_id = '.$personnel_id;
						$table = 'patients,visit,appointments';
						$month_patients = $this->reception_model->count_items($table, $where);

						 $reschedule_where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND appointments.appointment_rescheduled = 1  AND visit.visit_date BETWEEN "'.$six_month.'" AND "'.$today_month.'" AND visit.personnel_id = '.$personnel_id;
						$reschedule_table = 'patients,visit,appointments';
						$reschedule_month_patients = $this->reception_model->count_items($reschedule_table, $reschedule_where);


						$no_show_where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND appointments.appointment_status = 1 AND appointments.appointment_rescheduled = 0  AND visit.visit_date BETWEEN "'.$six_month.'" AND "'.$today_month.'" AND visit.personnel_id = '.$personnel_id;
						$no_show_table = 'patients,visit,appointments';
						$no_show_month_patients = $this->reception_model->count_items($no_show_table, $no_show_where);

						$total_honoured += $month_patients;


						$total_old_showed_charts .= '{
													y: "'.$personnel_onames.'",
													a: '.$month_patients.',
													b: '.$reschedule_month_patients.',
													c: '.$no_show_month_patients.'
												},';


					}
				}



				?>
				<!-- Morris: Bar -->
				<div class="chart chart-md" id="morrisStackedPatients"></div>
				<script type="text/javascript">

					var morrisStackedData = [<?php echo $total_old_showed_charts;?>];

					// See: assets/javascripts/ui-elements/examples.charts.js for more settings.

				</script>
				<div class="col-md-12 text-center">
					 <span style="padding-left:15px;background-color: #228B22;margin-right: 2px;"></span> Honoured
					 <span style="padding-left:15px;background-color: #B22222;margin-right: 2px;"></span> Rescheduled
					 <span style="padding-left:15px;background-color: #FF4500;margin-right: 2px;"></span> No Show 
				</div>

			</div>
		</section>
	
	

	</div>
	<div class="col-md-6">
		<section class="panel">
			<header class="panel-heading">
				<!-- <div class="panel-actions">
					<a href="ui-elements-charts.html#" class="panel-action panel-action-toggle" data-panel-toggle></a>
					<a href="ui-elements-charts.html#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
				</div> -->

				<h2 class="panel-title">Doctors General Workload Report</h2>
				<p class="panel-subtitle">Workload report for doctors based on honoured appointments for <?php echo date('M Y')?>.</p>
			</header>
			<div class="panel-body">
				<?php
				$doctors_percentage = '';
				if($doctors->num_rows() > 0)
				{
					foreach ($doctors->result() as $key => $value) {
						# code...
						$personnel_fname = $value->personnel_fname;
						$personnel_onames = $value->personnel_onames;
						$personnel_id = $value->personnel_id;


						$six_month = date("Y-m-01");
					    $today_month = date("Y-m-d");

					    $where = 'patients.patient_delete = 0 AND visit.visit_id = appointments.visit_id AND patients.patient_id = visit.patient_id AND visit.visit_delete = 0 AND (appointments.appointment_status = 4 OR appointments.appointment_status = 7)  AND appointments.appointment_rescheduled = 0 AND visit.visit_date BETWEEN "'.$six_month.'" AND "'.$today_month.'" AND visit.personnel_id = '.$personnel_id;
						$table = 'patients,visit,appointments';
						$doctor_patients = $this->reception_model->count_items($table, $where);

						if(empty($doctor_patients))
						{
							$percentage = 0;
						}
						else
						{
							$percentage = ($doctor_patients *100)/$total_honoured;
						}
						

						$percentage = number_format($percentage,0);

						$doctors_percentage .= '{
													label: "'.$personnel_onames.'",
													value: '.$percentage.'
												},';

					}
				}
				?>
				<!-- Morris: Donut -->
				<div class="chart chart-md" id="morrisDonut"></div>
				<script type="text/javascript">

					var morrisDonutData = [<?php echo $doctors_percentage;?>];

					// See: assets/javascripts/ui-elements/examples.charts.js for more settings.

				</script>
			</div>
		</section>
	</div>
</div>

<div class="row"> 
	<div class="col-md-12"> 
		<h4 class="mt-none">Clinic's Resources</h4>
	</div>
</div>
<div class="row"> 
	<div class="col-md-6">
		<section class="card">
			<header class="card-header card-header-transparent">
				<div class="card-actions">
					<a href="#" class="card-action card-action-toggle" data-card-toggle=""></a>
					<a href="#" class="card-action card-action-dismiss" data-card-dismiss=""></a>
				</div>

				<h3 class="card-title">Claim Forms</h3>
			</header>
			<div class="card-body">
				<table class="table table-responsive-md table-striped mb-0">
					<thead>
						<tr>
							<th>#</th>
							<th>Insurance Company</th>
							<th>Form Type</th>
							<th>Download Form</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>1</td>
							<td>Madison Insurance</td>
							<td>claim form</td>
							<td>
								<a class="btn btn-xs btn-success" href="<?php echo site_url().'download-form'?>"> download form</a>
							</td>
						</tr>
						
					</tbody>
				</table>
			</div>
		</section>
	</div>

	<div class="col-md-6">
		<section class="card">
			<header class="card-header card-header-transparent">
				<div class="card-actions">
					<a href="#" class="card-action card-action-toggle" data-card-toggle=""></a>
					<a href="#" class="card-action card-action-dismiss" data-card-dismiss=""></a>
				</div>

				<h3 class="card-title">Suppliers Contacts</h3>
			</header>
			<div class="card-body">
				<table class="table table-responsive-md table-striped mb-0">
					<thead>
						<tr>
							<th>#</th>
							<th>Supplier Name</th>
							<th>Contact Person</th>
							<th>Contact</th>
							<th>Download Form</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>1</td>
							<td>Eighteen O Nine Limited</td>
							<td>Hurlinghum road, 0704808007</td>
							<td>
								download form
							</td>
						</tr>
						
					</tbody>
				</table>
			</div>
		</section>
	</div>
</div>



					
					
					<!-- end: page -->
                     <?php //echo $this->load->view('administration/line_graph');?>
                     <?php //echo $this->load->view('administration/bar_graph');?>
                            

<!-- <script type="text/javascript" src="<?php echo base_url().'assets/themes/bluish/js/reports.js';?>"></script> -->