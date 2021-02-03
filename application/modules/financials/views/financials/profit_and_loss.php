<?php echo $this->load->view('search/search_profit_and_loss','', true);?>
<?php


$income_rs = $this->company_financial_model->get_income_value_new('Revenue');

// var_dump($income_rs); die();

$array_counter = count($income_rs);

$income_result = '';
$total_income = 0;
for ($i=0; $i < $array_counter ; $i++) { 

	$total_income += $income_rs[$i]['value'];
	$department_id = $income_rs[$i]['department_id'];
	$income_result .='<tr>
							<td class="text-left">'.strtoupper($income_rs[$i]['name']).'</td>
							<td class="text-right">
							<a href="'.site_url().'company-financials/services-bills/'.$income_rs[$i]['department_id'].'" >'.number_format($income_rs[$i]['value'],2).'</a></td>
							</tr>';

}

$income_result .='<tr>
							<td class="text-left"><b>Total Income</b></td>
							<td class="text-right"><b>'.number_format($total_income,2).'</b></td>
							</tr>';

// $income_result = '';
// $total_income = 0;
// if($income_rs->num_rows() > 0)
// {
// 	foreach ($income_rs->result() as $key => $value) {
// 		# code...
// 		$total_amount = $value->total_amount;
// 		$transactionName = $value->parent_service;
// 		$service_id = $value->service_id;
// 		$total_income += $total_amount;
// 		$income_result .='<tr>
// 							<td class="text-left">'.strtoupper($transactionName).'</td>
// 							<td class="text-right">
// 							<a href="'.site_url().'company-financials/services-bills/'.$service_id.'" >'.number_format($total_amount,2).'</a></td>
// 							</tr>';
// 	}
	
// }




// $operation_rs = $this->company_financial_model->get_cog_value('Expense');





$cog_result = '';
$total_cog = 0;
$start_date = $this->company_financial_model->get_inventory_start_date();

$closing_stock =  $this->company_financial_model->get_opening_stock_value();
$stock_list = $this->company_financial_model->get_product_purchases_new($start_date);
$array_count = count($stock_list);

// var_dump($array_count);die();
$total_other_purchases = 0;//$this->company_financial_model->get_product_other_purchases($start_date);
$total_return_outwards = 0;//$this->company_financial_model->get_product_return_outwards($start_date);
$total_sales = 0;//$this->company_financial_model->get_product_sales();
$total_other_deductions = 0;//$this->company_financial_model->get_total_other_deductions();
$total_purchases = 0;

for ($i=0; $i < $array_count ; $i++) { 
	# code...
	$name = $stock_list[$i]['name'];

	if($name === "Additions")
	{
		$total_other_purchases = $stock_list[$i]['value'];
	}
	else if($name === "Sales")
	{
		$total_sales = -$stock_list[$i]['value'];
	}
	else if($name === "Purchases")
	{
		$total_purchases = $stock_list[$i]['value'];
	}
	else if($name === "Deductions")
	{
		$total_other_deductions = -$stock_list[$i]['value'];
	}
	else if($name === "Return Outwards")
	{
		$total_return_outwards = -$stock_list[$i]['value'];
	}
}
// var_dump($stock_list);die();
$current_stock = (($total_purchases+$closing_stock+$total_other_purchases) - ($total_sales + $total_return_outwards + $total_other_deductions));
$total_cog = $total_purchases+$closing_stock-$current_stock;



$non_pharm_query = $this->company_financial_model->get_non_pharm_purchases();
$non_pharm_purchases = 0;
$non_pharm = '';

if($non_pharm_query->num_rows() > 0)
{
	foreach ($non_pharm_query->result() as $key => $value_category) {
		# code...

		$category_name = $value_category->transactionCategory;
		$category_id = $value_category->category_id;
		$category_value = $value_category->cr_amount;

		$non_pharm .='<tr>
							<td class="text-left">'.strtoupper($category_name).'</td>
							<td class="text-right"><a href="'.site_url().'view-non-pharm-purchases/'.$category_id.'" >'.number_format($category_value,2).'</a></td>
							</tr>';
		$non_pharm_purchases += $category_value;
	}
}
$current_stock -= $non_pharm_purchases;
// var_dump($non_pharm);die();

$direct_costs_rs = $this->company_financial_model->get_operational_cost_value_by_classification('Direct Costs');
// 
$direct_costs_result = '';
$total_direct_amount = '';
if($direct_costs_rs->num_rows() > 0)
{
	foreach ($direct_costs_rs->result() as $key => $value) {
		# code...
		$total_amount = $value->total_amount;
		$transactionName = $value->account_name;
		$account_id = $value->accountId;
		$account_id = $value->account_type_id;
		$total_direct_amount += $total_amount;
		$direct_costs_result .='<tr>
							<td class="text-left">'.strtoupper($transactionName).'</td>
							<td class="text-right"><a href="'.site_url().'accounting/expense-ledger/'.$account_id.'" >'.number_format($total_amount,2).'</a></td>
							</tr>';
	}
	
}



$operation_rs = $this->company_financial_model->get_operational_cost_value('Expense');
// 
$operation_result = $non_pharm;
$total_operational_amount = 0;
if($operation_rs->num_rows() > 0)
{
	foreach ($operation_rs->result() as $key => $value) {
		# code...
		$total_amount = $value->total_amount;
		$transactionName = $value->accountName;
		$account_id = $value->accountId;

		if($total_amount > 0)
		{
			$total_operational_amount += $total_amount;
		}
		
		$operation_result .='<tr>
							<td class="text-left">'.strtoupper($transactionName).'</td>
							<td class="text-right"><a href="'.site_url().'accounting/expense-ledger/'.$account_id.'" >'.number_format($total_amount,2).'</a></td>
							</tr>';
	}
	
}

$salary = $this->company_financial_model->get_salary_expenses();
// $nssf = $this->company_financial_model->get_statutories(1);
// $nhif = $this->company_financial_model->get_statutories(2);
// $paye_amount = $this->company_financial_model->get_statutories(3);
$relief =0;// $this->company_financial_model->get_statutories(4);
$loans = 0;//$this->company_financial_model->get_statutories(5);

// $paye = $paye_amount - $relief;

$salary -= $relief;
$other_deductions = $salary;// - ($nssf+$nhif+$paye_amount+$relief);

// $total_operational_amount += $salary+$nssf+$nhif+$paye_amount;
$total_operational_amount += $salary;
// $operation_result .= $non_pharm;
$operation_result .='<tr>
						<td class="text-left"><b>Total Operation Cost</b></td>
						<td class="text-right" style="border-top:#3c8dbc solid 2px;"><b>'.number_format($total_operational_amount,2).'</b></td>
					</tr>';


$statement = $this->session->userdata('income_statement_title_search');

// var_dump($statement);die();

if(!empty($statement))
{
	$checked = $statement;
}
else {
	$checked = 'Reporting period: '.date('M j, Y', strtotime(date('Y-01-01'))).' to ' .date('M j, Y', strtotime(date('Y-m-d')));
}
?>

<div class="text-center">
	<h3 class="box-title">Income Statement</h3>
	<h5 class="box-title"> <?php echo $checked?></h5>
	<h6 class="box-title">Created <?php echo date('M j, Y', strtotime(date('Y-m-d')));?></h6>
</div>

<section class="panel">
		<header class="panel-heading">
				<h5 class="pull-left"><i class="icon-reorder"></i>Revenue</h5>
				<div class="clearfix"></div>
		</header>
		<!-- /.box-header -->
		<div class="panel-body">
			<h5 class="box-title" style="background-color:#3c8dbc;color:#fff;padding:5px;">INCOME</h5>
    	<table class="table  table-striped table-condensed">
			<thead>
				<tr>
        			<th class="text-left">Account</th>
					<th class="text-right">Balance</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $income_result;?>
			</tbody>
		</table>


		<h5 class="box-title" style="background-color:#3c8dbc;color:#fff;padding:5px;">DIRECT COSTS</h5>
    	<table class="table  table-striped table-condensed">
			<thead>
				<tr>
        			<th class="text-left">Account</th>
					<th class="text-right">Balance</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $direct_costs_result?>
				

				<tr>
					<td ><b>TOTAL DIRECT COSTS<b></td>
					<td class="text-right" style="border-top:#3c8dbc solid 1px;">(<?php echo number_format($total_cog,2);?>)</td>
				</tr>
				<tr>
        			<td class="text-left"><strong>GROSS PROFIT</strong> (Total Income - Total Goods Sold)</td>
					<th class="text-right"  style="border-top:#3c8dbc solid 2px;"><?php echo number_format($total_income - $total_cog,2)?></th>
				</tr>
			</tbody>
		</table>

		<h5 class="box-title" style="background-color:#3c8dbc;color:#fff;padding:5px;">OPERATING EXPENSE</h5>
    	<table class="table  table-striped table-condensed">
			<thead>
				<tr>
        			<th class="text-left">Account</th>
					<th class="text-right">Balance</th>
				</tr>
			</thead>
			<tbody>
				
				<tr>
					<td class="text-left">SALARIES</td>
					<td class="text-right"><a href="<?php echo site_url().'company-financials/salary'?>"> <?php echo number_format($salary,2);?> </a> </td>
				</tr>
				<?php echo $operation_result;?>
				<tr>
        			<th class="text-left"><strong>Operating Profit (Loss)</strong></th>
					<th class="text-right" style="border-top:#3c8dbc solid 3px;"><?php echo number_format($total_income - $total_cog - $total_operational_amount,2)?></th>
				</tr>

			</tbody>
		</table>

		<!-- <h5 class="box-title">INTEREST (INCOME), EXPENSE & TAXES</h5> -->
    	<table class="table  table-striped table-condensed">
			<thead>
				<tr>
        			<th class="text-left"></th>
					<th class="text-right"></th>
				</tr>
			</thead>
			<tbody>

				<tr>
        			<th class="text-left"><strong>NET Profit</strong></th>
					<th class="text-right"><?php echo number_format($total_income - $total_cog - $total_operational_amount,2)?></th>
				</tr>
			</tbody>
		</table>
    </div>
</section>
