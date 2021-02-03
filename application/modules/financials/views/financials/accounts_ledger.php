<!-- search -->
<?php //echo $this->load->view('search/search_expense_ledger', '', TRUE);


$operation_rs = $this->company_financial_model->get_accounts_ledger($account_id);

$operation_result = '';
$total_operational_amount = 0;
$balance = 0;
// $balance = $this->company_financial_model->get_account_opening_balance($account_id);

// $operation_result .='<tr>

//                       <td class="text-right" colspan="3">Opening Balance</td>
//                       <td class="text-right">'.$balance.'</td>
//                       <td class="text-right">0.00</td>
//                       <td class="text-right">'.number_format($total_operational_amount,2).'</td>
//                       </tr>';
$total_operational_amount = $balance;
if($operation_rs->num_rows() > 0)
{
	foreach ($operation_rs->result() as $key => $value) {
		# code...
		$dr_amount = $value->dr_amount;
		$cr_amount = $value->cr_amount;
		$transactionName = $value->accountName;
		$account_id = $value->accountId;
		$transactionDescription = $value->transactionDescription;

		$transactionCategory = $value->transactionCategory;
		$transactionName = $value->transactionName;
		$transactionDate = $value->transactionDate;
		$transactionCode = $value->transactionCode;
		$total_operational_amount += $dr_amount;
		$total_operational_amount -= $cr_amount;

		$operation_result .='<tr>
                          <td class="text-left">'.strtoupper($transactionDate).'</td>
            							<td class="text-left">'.strtoupper($transactionDescription).'</td>

                          <td class="text-left">'.strtoupper($transactionCode).'</td>
            							<td class="text-right">'.number_format($dr_amount,2).'</td>
                          <td class="text-right">'.number_format($cr_amount,2).'</td>
                          <td class="text-right">'.number_format($total_operational_amount,2).'</td>
            							</tr>';
	}
	$operation_result .='<tr>
							<td class="text-left" colspan="3"><b>BALANCE</b></td>
							<td class="text-center" colspan="3"><b>'.number_format($total_operational_amount,2).'</b></td>
							</tr>';
}

?>

<!--end reports -->
<div class="row">
    <div class="col-md-12">

        <section class="panel ">
            <header class="panel-heading">

                <h2 class="panel-title"><?php echo strtoupper($title);?></h2>
                <a href="<?php echo site_url();?>company-financials/balance-sheet"  class="btn btn-sm btn-info pull-right" style="margin-top:-25px;margin-left:5px" > Back to Balance Sheet </a>
                <!-- <a href="<?php echo base_url().'accounting/print-expenses-ledger';?>" target="_blank" class="btn btn-sm btn-warning pull-right" style="margin-top: -25px;"><i class="fa fa-cancel" ></i> Print Ledger</a> -->
            </header>

            <div class="panel-body">


			<?php
			if(!empty($ledger_search))
			{
				?>
                <a href="<?php echo base_url().'accounting/petty_cash/close_expense_ledger';?>" class="btn btn-sm btn-danger"><i class="fa fa-cancel"></i> Close Search</a>
                <?php
			}
			$error = $this->session->userdata('error_message');
			$success = $this->session->userdata('success_message');

			if(!empty($error))
			{
				echo '<div class="alert alert-danger">'.$error.'</div>';
				$this->session->unset_userdata('error_message');
			}

			if(!empty($success))
			{
				echo '<div class="alert alert-success">'.$success.'</div>';
				$this->session->unset_userdata('success_message');
			}

			// echo $result;



?>			<table class="table table-hover table-bordered ">
				 	<thead>
						<tr>
						  <th>Transaction Date</th>
						  <th>Description</th>
						  <th>Voucher</th>
						  <th>Debit</th>
              <th>Credit</th>
              <th>Arrears</th>
						</tr>
					 </thead>
				  	<tbody>
              <?php echo $operation_result;?>
					</tbody>
				</table>

          	</div>
		</section>
    </div>
</div>

<script type="text/javascript">



	$(document).on("change","select#transaction_type_id",function(e)
	{
		var transaction_type_id = $(this).val();

		if(transaction_type_id == '1')
		{
			// deposit
			$('#from_account_div').css('display', 'block');
			$('#account_to_div').css('display', 'block');
			// $('#consultation').css('display', 'block');
		}
		else if(transaction_type_id == '2')
		{
			// expenditure
			$('#from_account_div').css('display', 'block');
			$('#account_to_div').css('display', 'none');
			// $('#consultation').css('display', 'block');
		}
		else
		{
			$('#from_account_div').css('display', 'none');
			$('#account_to_div').css('display', 'none');
		}


	});
</script>
