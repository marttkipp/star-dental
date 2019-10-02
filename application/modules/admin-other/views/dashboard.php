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
    $where = 'patients.patient_delete = 0 AND YEAR(patient_date) = '.$year.' AND MONTH(patient_date) = "'.$month.'" AND patients.patient_id IN (SELECT visit.patient_id FROM visit)';
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

		$where2 = 'visit.visit_type = '.$visit_type_id.' AND (parent_visit = 0 OR parent_visit IS NULL) and visit.visit_delete = 0 AND visit.visit_date > "2018-03-01"';
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


$total_visits = '';
for ($k = 6; $k >= 0; $k--) {
    $months = date("Y-m", strtotime( date( 'Y-m-d' )." -$k months"));
    $months_explode = explode('-', $months);
    $year = $months_explode[0];
    $month = $months_explode[1];
    $last_visit = date('M Y',strtotime($months));
   
    $community_where ='patients.patient_id = visit.patient_id AND visit.personnel_id > 0 AND visit.revisit = 0 AND patients.patient_type = 0 AND  YEAR(visit.visit_date) = '.$year.' AND MONTH(visit.visit_date) = "'.$month.'"';
    $community_table = 'visit,patients';
    $total_number_new = $this->reception_model->count_items($community_table, $community_where);

    $community_where ='patients.patient_id = visit.patient_id AND visit.personnel_id > 0 AND visit.revisit = 1 AND patients.patient_type = 0 AND  YEAR(visit.visit_date) = '.$year.' AND MONTH(visit.visit_date) = "'.$month.'"';
    $community_table = 'visit,patients';
    $total_number_old = $this->reception_model->count_items($community_table, $community_where);

	$total_visits .= '{
	                        y: "'.$last_visit.'",
	                        a: '.$total_number_new.',
	                        b: '.$total_number_old.'
	                    },';

}

// var_dump($total_visits); die();
?>
<!-- start: page -->
<div class="row" style="margin-top:20px;">
	<div class="col-md-6 col-lg-12 col-xl-12">
		<section class="panel">
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-6">						

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
					<div class="col-lg-6">
						<section class="card">
					            <header class="card-header">				               

					                <h4 class="card-title">Patients Visit Category</h4>
					                <p class="card-subtitle">Comparison of visit types </p>
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
				</div>
			</div>
		</section>
	</div>
	
</div>

<div class="row" >
	<div class="col-md-6 col-lg-12 col-xl-12">
		<section class="panel">
			<div class="panel-body">
				<div class="row">
					
					<div class="col-lg-12">
						<section class="card">
				            <header class="card-header">
				                <h4 class="card-title">New Visits VS Re-visits Comparisons</h4>
				                <p class="card-subtitle">Comparison between new visits and revisit for the past six months</p>
				            </header>
				            <div class="card-body">

				                <!-- Morris: Bar -->
				                <div class="chart chart-md" id="morrisBar"></div>
				                <script type="text/javascript">
				                    var morrisBarData = [<?php echo $total_visits;?>];

				                    // See: js/examples/examples.charts.js for more settings.
				                </script>

				            </div>
				        </section>
					</div>
				</div>
			</div>
		</section>
	</div>
	
</div>
<script type="text/javascript">
	
	
</script>
					
					
					