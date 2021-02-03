<?php
if($month < 10)
{
	$month = '0'.$month;
}

$budget_list_rs = $this->budget_model->get_budget_list($budget_year,$month,$account_id);

$result = '';
$count = 0;
if($budget_list_rs->num_rows() > 0)
{
	foreach ($budget_list_rs->result() as $key => $value) {
		# code...
		$department_name = $value->department_name;
		$account_name = $value->account_name;
		$budget_item_amount = $value->budget_item_amount;
		$budget_month = $value->budget_month;
		$budget_year = $value->budget_year;
		$created = $value->created;
		$personnel_fname = $value->personnel_fname;
		$budget_item_id = $value->budget_item_id;


		if (substr($budget_month, 0, 1) === '0') 
		{
			$budget_month = ltrim($budget_month, '0');
		}

		$count++;
		$result .= '<tr>
						<td>'.$count.'</td>
						<td>'.strtoupper($account_name).'</td>
						<td>'.number_format($budget_item_amount,2).'</td>
						<td>'.$budget_year.'</td>
						<td>'.$budget_month.'</td>
						<td>'.$created.'</td>
						<td>'.strtoupper($department_name).'</td>
						<td>'.$personnel_fname.'</td>
						<td><a onclick="delete_budget_item('.$budget_item_id.','.$budget_year.','.$budget_month.','.$account_id.')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a></td>
					</tr>

				  ';
	}
}
?>

<table class="table table-condensed table-bordered">
	<thead>
		<th>#</th>
		<th>Account</th>
		<th>Amount</th>
		<th>Year</th>
		<th>Month</th>
		<th>Created</th>
		<th>Department</th>
		<th>Created By</th>
	</thead>
	<tbody>
		<?php echo $result;?>
		
	</tbody>
</table>