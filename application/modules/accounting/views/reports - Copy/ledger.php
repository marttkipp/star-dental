<!-- search -->
<?php echo $this->load->view('search/search_petty_cash', '', TRUE);

?>
<!-- end search -->
<!--begin the reports section-->
<?php
//unset the sessions set\
$search = $this->session->userdata('accounts_search');
$search_title = $this->session->userdata('accounts_search_title');//echo $account;die();

$ledger_search = $this->session->userdata('ledger_search');
$search_title  = '';
if($ledger_search == 1)
{
	$account = $this->session->userdata('account_id');
	$search_title = $this->session->userdata('search_title');
	$opening_bal = $this->petty_cash_model->get_account_opening_bal($account);
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
			if(!empty($ledger_search))
			{
				?>
                <a href="<?php echo base_url().'accounting/petty_cash/close_search_ledger/';?>" class="btn btn-sm btn-danger"><i class="fa fa-cancel"></i> Close Search</a>
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
						  <th>Account</th>
						  <th>Description</th>
						  <th>Debit</th>
						  <th>Credit</th>						
						</tr>
					 </thead>
				  	<tbody>
				  		<?php
				  			$ledger_search = $this->session->userdata('ledger_search');
							if($ledger_search == 1)
							{
								$account = $this->session->userdata('account_id');
								$account_name = $this->session->userdata('account_name');
								
								$statement_result = $this->petty_cash_model->get_ledger_statement($account,$account_name);
								echo $statement_result['result'];
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