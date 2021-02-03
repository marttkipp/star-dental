<!-- search -->
<?php echo $this->load->view('search_ledgers', '', TRUE);

?>
<!-- end search -->
<!--begin the reports section-->
<?php
//unset the sessions set\
$search = $this->session->userdata('accounts_search');
$search_title = $this->session->userdata('accounts_search_title');//echo $account;die();

$account_ledger_search = $this->session->userdata('account_ledger_search');
$search_title  = '';
if($account_ledger_search == 1)
{

	$account = $this->session->userdata('account_id');
	$search_title = $this->session->userdata('search_title');
	$account_date_from = $this->session->userdata('account_date_from');
    $account_date_to = $this->session->userdata('account_date_to');
	if(!empty($account_date_from) AND !empty($account_date_to))
	{
		$search_title .= ' FROM PERIOD BETWEEN '.$account_date_from.'  AND '.$account_date_to.'';
	}
	else if(!empty($account_date_from) AND empty($account_date_to))
	{
		$search_title .= ' FOR "'.$account_date_from.'"';
	}
	else if(empty($account_date_from) AND !empty($account_date_to))
	{
		$search_title .= ' FOR "'.$account_date_to.'"';
	}
	else
	{
		$search_title .= '';
	}
	// $opening_bal = $this->petty_cash_model->get_account_opening_bal($account);
}
// else
// {
// 	$opening_bal = $this->petty_cash_model->get_total_opening_bal();
// }

?>
<!--end reports -->
<div class="row">
    <div class="col-md-12">

        <section class="panel panel-primary">
            <header class="panel-heading">
                
                <h2 class="panel-title center-align"><?php echo strtoupper($search_title);?></h2>
            </header>
            
            <div class="panel-body">
               
              
			<?php
			if(!empty($account_ledger_search))
			{
				?>
				<div class="row">
					<div class="col-md-12">
						<div class="pull-left">
							 <a href="<?php echo base_url().'financials/ledgers/close_search_ledger';?>" class="btn btn-sm btn-danger"><i class="fa fa-cancel"></i> Close Search</a>
						</div>
						<div class="pull-right">
							<a href="<?php echo base_url().'export-account-ledger';?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-export"></i> Export Statement</a>
							<a href="<?php echo base_url().'print-account-ledger';?>" class="btn btn-sm btn-warning" target="_blank"><i class="fa fa-print"></i> Print Statement</a>
						</div>
					</div>
				</div>
				<br>
               

                
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


			
?>			<table class="table table-hover table-bordered table-condensed table-stripped ">
				 	<thead>
						<tr>
						  <th>Transaction Date</th>						  
						  <th>Type</th>
						  <th>Description</th>
						  <th>Debit</th>
						  <th>Credit</th>
						  <th>Balance</th>						
						</tr>
					 </thead>
				  	<tbody>
				  		<?php
				  			$account_ledger_search = $this->session->userdata('account_ledger_search');
							if($account_ledger_search == 1)
							{
								$account = $this->session->userdata('account_id');
								$account_name = $this->session->userdata('account_name');
								
								$statement_rs = $this->ledgers_model->get_account_ledger_statement($account);
								if($statement_rs->num_rows() > 0)
								{
									$x = 0;
									$balance = 0;
									$total_dr = 0;
									$total_cr = 0;
									foreach ($statement_rs->result() as $key => $value) {
										# code...

										$transactionId = $value->transactionId;
										$accountName = $value->accountName;
										$transactionDate = $value->transactionDate;
										$dr_amount = $value->dr_amount;
										$cr_amount = $value->cr_amount;
										$transactionDescription = $value->transactionDescription;
										$transactionName = $value->transactionClassification;
										$balance += $dr_amount - $cr_amount;
										$total_dr += $dr_amount;
										$total_cr += $cr_amount;
										$x++;
										echo '
											<tr>
												<td>'.$transactionDate.'</td>
												<td>'.$transactionName.'</td>
												<td>'.$transactionDescription.'</td>
												<td>'.number_format($cr_amount,2).'</td>
												<td>'.number_format($dr_amount,2).'</td>
												<td>'.number_format($balance,2).'</td>
											</tr>';


									}

									echo '
											<tr>
												<td colspan="3"><strong>Totals</strong></td>		
												<td><strong>'.number_format($total_cr,2).'</strong></td>									
												<td><strong>'.number_format($total_dr,2).'</strong></td>
												<td><strong>'.number_format($balance,2).'</strong></td>
											</tr>';
								}
								else
								{
									echo '<tr><td colspan="5">No transactions done on this account</td></tr>';
								}
							}
							else
							{
								echo '<tr><td colspan="5">Please select an account</td></tr>';
							}

				  		?>
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