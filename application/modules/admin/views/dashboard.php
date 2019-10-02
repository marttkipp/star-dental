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
												<div class="col-md-4">
													<h4 class="title">UHDC</h4>
													<div class="info">
														<strong class="amount"> <?php echo $uhdc_patient;?></strong>
													</div>
												</div>
												<div class="col-md-4">
													<h4 class="title">New</h4>
													<div class="info">
														<strong class="amount"> <?php echo $new_patient;?></strong>
													</div>
												</div>
												<div class="col-md-4">
													<h4 class="title">Uncategorized</h4>
													<div class="info">
														<strong class="amount"> <?php echo $uncategorized_patient;?></strong>
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

					
					
					<!-- end: page -->
                     <?php //echo $this->load->view('administration/line_graph');?>
                     <?php //echo $this->load->view('administration/bar_graph');?>
                            

<!-- <script type="text/javascript" src="<?php echo base_url().'assets/themes/bluish/js/reports.js';?>"></script> -->