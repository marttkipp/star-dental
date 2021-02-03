<input type="hidden" name="budget_year" id="budget_year" value="<?php echo $budget_year;?>">
<div class="row" style="margin-top: 10px;">
	<div class="col-md-6">
		<?php echo form_open("financials/budget/search_budget_analysis", array("class" => "form-horizontal"));?>
		<div class="col-md-6">
			<div class="form-group">
	            <label class="col-md-4 control-label">Year: </label>
	            
	            <div class="col-md-8">
	               <select id="budget_year" name="budget_year" class="form-control">	
		                <?php

		                $start_year = 2019;
				        $end_year  = date('Y') + 1;
				        // $selected = $this->session->session->userdata('budget_year');
				       
				   		for ($i=$start_year; $i <= $end_year; $i++) { 
				   			# code...

							if($i == date("Y"))
							{
	                                echo "<option value=".$i." selected>".$i."</option>";
	                        }
	                        else{
	                            echo "<option value=".$i.">".$i."</option>";
	                        }
				   		}          
		                
	                    ?>
	                 </select> 
	            </div>
	        </div>
		</div>
		<div class="col-md-2">
			<button type="submit" class="btn btn-sm btn-success"> SEARCH</button>
		</div>
		<div class="col-md-2">
			<?php
			$budget_year_searched = $this->session->userdata('analysis_budget_year');

			if(!empty($budget_year_searched))
			{
				?>
				<a href="<?php echo site_url().'financials/budget/close_budget_analysis_search'?>" class="btn btn-sm btn-warning"> CLOSE</a>
				<?php
			}

			?>
			
		</div>
		<?php echo form_close();?>
	</div>
	<div class="col-md-3">
		<div id="title-div"><?php echo $title?></div>
	</div>
	<div class="col-md-3">

		<a href="<?php echo site_url().'company-financials/budget'?>" class="btn btn-sm btn-success"> Budget</a>
		<a href="<?php echo site_url().'company-financials/budget-actual'?>" class="btn btn-sm btn-primary"> Actual</a>		
		<!-- <a onclick="javascript:xport.toCSV('testTable');" class="btn btn-sm btn-success"> Export</a> -->
		<a href="<?php echo site_url().'company-financials'?>" class="btn btn-sm btn-danger"> <i class="fa fa-arrow-left"></i></a>
	</div>
</div>

<?php


$where = 'account_type_id = 2 AND parent_account = 0';
$table = 'account';
$total_gender = '';
$visit_types = $this->dashboard_model->get_content($table, $where,'*',$group_by=NULL,$limit=NULL);
$total_gender2 = '';
$table_result = '';
$grand_month_value = 0;
$grand_month_actual_value = 0;
$grand_month_variance_value = 0;
$grand_variance = 0;
$count = 0;
if($visit_types->num_rows() > 0)
{
	foreach ($visit_types->result() as $key => $value) {
		# code...
		$account_name = $value->account_name;
		$account_id = $value->account_id;

		$month_value = $this->budget_model->get_total_amount_sum_parent($budget_year,$account_id);

		$month_value_actual = $this->budget_model->get_total_amount_sum_actual_parent($budget_year,$account_id);

		$count++;
		$color = $this->reception_model->random_color();
		$total_gender .= '{
		                        label: "'.$account_name.'",
		                        data: [
		                            ['.$count.', '.$month_value.']
		                        ],
		                        color: "'.$color.'",
		                    },';
		$color = $this->reception_model->random_color();

		$total_gender2 .= '{
		                        label: "'.$account_name.'",
		                        data: [
		                            ['.$count.', '.$month_value_actual.']
		                        ],
		                        color: "'.$color.'",
		                    },';


		if($account_name == "EMPLOYMENT")
		{
			$month_value_actual = $this->budget_model->get_actual_total_salary_expenses($budget_year);
		}
		$variance = $month_value - $month_value_actual;

		$grand_month_value += $month_value;
		$grand_month_actual_value += $month_value_actual;
		$grand_month_variance_value += $variance;
		if($month_value > 0)
		{
			$percentage = $variance/$month_value;
		}
		else
		{
			$percentage = 0;
		}
		

		$grand_variance += $percentage;



		$table_result .= '<tr>
		 					<td>'.strtoupper($account_name).'</td>
		 					<td>'.number_format($month_value,2).'</td>
		 					<td>'.number_format($month_value_actual,2).'</td>
		 					<td>'.number_format($variance,2).'</td>
		 					<td>'.number_format($percentage,2).' %</td>
		 				  </tr>';

	}

	$grand_percentage = $grand_variance/$count;
	$table_result .= '<tr>
		 					<th>TOTAL</th>
		 					<th>'.number_format($grand_month_value,2).'</th>
		 					<th>'.number_format($grand_month_actual_value,2).'</th>
		 					<th>'.number_format($grand_month_variance_value,2).'</th>
		 					<th>'.number_format($grand_percentage,2).' %</th>
		 				  </tr>';
}

$total_visits = '';
$month = $this->budget_model->get_month();
if($month->num_rows() > 0){
    foreach ($month->result() as $row):
        $mth = $row->month_name;
        $mth_id = $month_id = $row->month_id;
        if($mth_id < 10)
        {
            $mth_id = '0'.$mth_id;
        }

		$month_value = $this->budget_model->get_total_amount_sum_parent($budget_year,NULL,$mth_id);

		$month_value_actual = $this->budget_model->get_total_amount_sum_actual_parent($budget_year,NULL,$mth_id);

		$total_visits .= '{
		                        y: "'.$mth.'",
		                        a: '.$month_value.',
		                        b: '.$month_value_actual.'
		                    },';
	endforeach;

}


?>
<div class="row">
	<div class="panel-body">
		<div class="row">
			<div class="col-md-6">
				<section class="card">
		            <header class="card-header">				               

		                <h4 class="card-title">ANALYSIS</h4>
		                <p class="card-subtitle">ANALYSIS OF BUDGET AND ACTUAL </p>
		            </header>
		            <div class="card-body">
						<table class="table table-condensed table-bordered" id="testTable">
							<thead>
								<th>CATEGORY</th>
								<th>BUDGET</th>
								<th>ACTUAL</th>
								<th>VARIANCE</th>
								<th>%</th>
							</thead>
							<tbody>
								<?php echo $table_result;?>
								
							</tbody>
						</table>
					</div>
				</section>
			</div>
			<div class="col-md-3">
				<section class="card">
		            <header class="card-header">				               

		                <h4 class="card-title">Budget</h4>
		                <p class="card-subtitle">Comparison of acconts budgeted </p>
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
			<div class="col-md-3">
				<section class="card">
		            <header class="card-header">				               

		                <h4 class="card-title">Actual</h4>
		                <p class="card-subtitle">Comparison of acconts budgeted </p>
		            </header>
		            <div class="card-body">

		                <!-- Flot: Pie -->
		                <div class="chart chart-md" id="flotPie2"></div>
		                <script type="text/javascript">
		                    var flotPieData2 = [<?php echo $total_gender2?>];

		                    // See: js/examples/examples.charts.js for more settings.
		                </script>

		            </div>
		   		</section>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">

				<section class="card">
		            <header class="card-header">
		                <h4 class="card-title">BUDGET VS ACTUAL Comparisons</h4>
		                <p class="card-subtitle">Comparison between BUDGET and ACTUAL for <?php echo $budget_year?></p>
		            </header>
		            <div class="card-body">

		                <!-- Morris: Bar -->
		                <div class="chart chart-md" id="morrisBar2"></div>
		                <script type="text/javascript">
		                    var morrisBarData2 = [<?php echo $total_visits;?>];

		                    // See: js/examples/examples.charts.js for more settings.
		                </script>

		            </div>
		        </section>
				
			</div>
		</div>
		
		
	</div>
	
</div>