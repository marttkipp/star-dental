<?php
$result = '';
$total_interest = 0;
$actual_first_date = date('M Y', strtotime($first_date));

if(empty($error))
{
	$result .= '
	<table class="table table-condensed table-striped table-hover table-bordered">
		<tr>
			<th>#</th>
			<th>Application Date</th>
			<th>Start bal.</th>
			<th>Amount</th>
			<th>Interest</th>
			<th>Principal payment</th>
			<th>End bal.</th>
			
		</tr>
		<tr>
			
			<th>0</th>
			<th>'.$actual_first_date.'.</th>
			<th>0</th>
			<th>0</th>
			<th>0</th>
			<th>0</th>
			<th>'.$loan_amount.'</th>
			
		</tr>
	';
	$cummulative_interest = 0;
	$cummulative_principal = 0;
	$start_balance = $loan_amount;
	$total_days = 0;
	$count = 0;
	
	$interest_per_year = $interest_rate/1200;
	$calculation_top_one = 1 + $interest_per_year;
	$calculation_top_two = pow($calculation_top_one, $no_of_repayments);
	$calculation_top_three = $calculation_top_two * $interest_per_year;


	$calculation_bottom_one = $calculation_top_two - 1;

	$calculation_one = $calculation_top_three/$calculation_bottom_one;
	$total_amount = $calculation_one * $loan_amount;
	$main_amount = $total_amount;
	$installment_type_duration = 1;

	// var_dump($main_amount);die();
	//display all payment dates
	for($r = 0; $r < $no_of_repayments; $r++)
	{
		
		$total_days += $installment_type_duration;
		$payment_date = date('M Y', strtotime($first_date. ' + '.$total_days.' months'));
		//Amount Calculation




		$interest_per_year = $interest_rate/1200;
		$calculation_top_one = 1 + $interest_per_year;
		$calculation_top_two = pow($calculation_top_one, $no_of_repayments);
		$calculation_top_three = $calculation_top_two * $interest_per_year;


		$calculation_bottom_one = $calculation_top_two - 1;

		$calculation_one = $calculation_top_three/$calculation_bottom_one;
		$total_amount = $calculation_one * $loan_amount;
		$total_amount = number_format($total_amount, 2);

		// interest Calculation
		$interest_cal = $interest_per_year * $loan_amount;

		//Principal Calculation
		$principal = $main_amount - $interest_cal;

		// balance
		$balance = $loan_amount - $principal;
		$loan_amount = $balance;
		$count++;
		$total_interest = $total_interest + $interest_cal;

		
		//for each month, insert the principal and interest expected for that loan;
		//$this->payments_model->update_amortization_table($count,$interest_payment,$principal_payment,$individual_loan_id);
		
		$result .= '
		<tr>
			<td>'.$count.'</td>
			<td>'.$payment_date.'</td>
			<td>'.number_format($loan_amount, 2).'</td>
			<td>'.number_format($main_amount, 2).'</td>
			<td>'.number_format($interest_cal, 2).'</td>
			<td>'.number_format($principal, 2).'</td>
			<td>'.number_format($balance, 2).'</td>
			
		</tr>';
		
	}
	

}	
	
?>
<section class="panel">
    <header class="panel-heading">
        <div class="alert alert-success"> <strong>Depreciation Table</strong></div>
    </header>
    <div class="panel-body">
    	<?php
        if(!empty($error))
			{
				echo '<div class="alert alert-danger"> <strong>Oh snap!</strong> '.$error.' </div>';
				//$this->session->unset_userdata('error_message');
			}
		?>
       <div class="table-responsive">	
			<?php echo $result;?>
	   </div>
     </div>
</section>


						
				