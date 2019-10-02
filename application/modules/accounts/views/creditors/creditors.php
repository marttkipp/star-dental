<?php echo $this->load->view('search/creditor_search', '', TRUE);?><!-- search -->

<!-- end search -->
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?> </h2>
        <a href="<?php echo site_url();?>accounts/creditors/add_creditor" class="btn btn-sm btn-primary pull-right" style="margin-top: -25px;"><i class="fa fa-plus"></i> Add creditors</a>
                	
    </header>

    <!-- Widget content -->
    <div class="panel-body">
    	<div class="padd">
          <?php
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
			?>
            
           	<div style="min-height:30px;">
            	<div class="pull-right">
                	<?php
					$search = $this->session->userdata('search_hospital_creditors');
		
					if(!empty($search))
					{
						echo '<a href="'.site_url().'accounts/creditors/close_search_hospital_creditors" class="btn btn-warning btn-sm">Close Search</a>';
					}
					?>
                </div>
            </div>
                
<?php
		
		$result = '';
		
				// var_dump($query->result()); die();
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			$result .= '
					<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Creditor name</th>
						  <th>Opening Balance</th>
						  <th>Total payments</th>
						  <th>Total invoice</th>
						  <th>Account Balance</th>
						  <th colspan="2">Actions</th>
						</tr>
					  </thead>
					  <tbody>
				';
			
			foreach ($query->result() as $row)
			{
				$count++;
				$creditor_id = $row->creditor_id;
				$creditor_name = $row->creditor_name;
				$opening_balance = $row->opening_balance;
				$debit_id = $row->debit_id;

				// $invoice_total = $this->creditors_model->get_invoice_total($creditor_id);
				// $payments_total = $this->creditors_model->get_payments_total($creditor_id);

				$response = $this->petty_cash_model->get_creditor_statement($creditor_id);	
				$invoice_total = $response['total_arrears'];
				$payments_total = $response['total_payment_amount'];
				//$payments_total = 0;
				$creditor_status = $row->creditor_status;
				
				if($creditor_status == 1)
				{
					$checked_active = 'checked';
					$checked_inactive = '';
				}
				else
				{
					$checked_active = '';
					$checked_inactive = 'checked';
				}
				// var_dump($invoice_total);
				if($debit_id == 2)
				{
					$payments_total = $payments_total +$opening_balance;	
				}
				else
				{
					$invoice_total = $invoice_total + $opening_balance;
				}
				$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$creditor_name.'</td>
							<td>'.number_format($opening_balance, 2).'</td>
							<td>'.number_format($payments_total, 2).'</td>
							<td>'.number_format($invoice_total, 2).'</td>
							<td>'.number_format($payments_total - $invoice_total, 2).'</td>
							<td><a href="'.site_url().'accounts/creditors/statement/'.$creditor_id.'" class="btn btn-sm btn-info" >Statement</a></td>
							<td><a href="'.site_url().'accounts/creditors/edit_creditor/'.$creditor_id.'" class="btn btn-sm btn-success">Edit</a></td>
							';
				
			}
			
			$result .= 
				'
							  </tbody>
							</table>
				';
		}
		
		else
		{
			$result .= "There are no creditors";
		}
		
		echo $result;
?>
          </div>
          
          <div class="widget-foot">
                                
				<?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        </div>
        <!-- Widget ends -->

      </div>
</section>