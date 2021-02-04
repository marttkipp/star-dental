<!-- search -->
<?php echo $this->load->view('search/creditor_search', '', TRUE);?>

<!-- end search -->
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?> </h2>
          <a href="<?php echo site_url();?>accounting/creditors/export_creditors" target="_blank" class="btn btn-sm btn-success pull-right" style="margin-top: -25px;margin-left: 5px;"><i class="fa fa-download"></i> Export Aging Report</a>
        <a href="<?php echo site_url();?>accounting/creditors/add_creditor" class="btn btn-sm btn-primary pull-right" style="margin-top: -25px;"><i class="fa fa-plus"></i> Add creditors</a>
                	
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
						echo '<a href="'.site_url().'accounting/creditors/close_search_hospital_creditors" class="btn btn-warning btn-sm">Close Search</a>';
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
						  <th>30 days</th>
						  <th>60 Days</th>
						  <th>90 Days</th>
						  <th>> 90 Days</th>
						  <th>Total payments</th>
						  <th>Total invoice</th>
						  <th>Account Balance</th>
						  <th colspan="4">Actions</th>
						</tr>
					  </thead>
					  <tbody>
				';
				$total_this_month = 0;
				$total_three_months = 0;
				$total_six_months = 0;
				$total_nine_months = 0;
				$total_payments = 0;
				$total_invoices =0;
				$total_balance = 0;
			
			foreach ($query->result() as $row)
			{
				$count++;
				$creditor_id = $row->creditor_id;
				$creditor_name = $row->creditor_name;
				$opening_balance = $row->opening_balance;
				$debit_id = $row->debit_id;

				// $invoice_total = $this->creditors_model->get_invoice_total($creditor_id);
				// $payments_total = $this->creditors_model->get_payments_total($creditor_id);
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
				// if($debit_id == 2)
				// {
				// 	$payments_total = $payments_total +$opening_balance;	
				// }
				// else
				// {
				// 	$invoice_total = $invoice_total + $opening_balance;
				// }

				$creditor_result = $this->creditors_model->get_creditor_statement($creditor_id);

				$invoice_total = $creditor_result['total_invoice_balance'];
				$payments_total = $creditor_result['total_payment_amount'];


				$date = date('Y-m-d');
	            $this_month = $this->creditors_model->get_statement_value($creditor_id,$date,1);
	            $three_months = $this->creditors_model->get_statement_value($creditor_id,$date,2);
	            $six_months = $this->creditors_model->get_statement_value($creditor_id,$date,3);
	            $nine_months = $this->creditors_model->get_statement_value($creditor_id,$date,4);

	            $total_this_month +=$this_month;
	            $total_three_months +=$three_months;
	            $total_six_months +=$six_months;
	            $total_nine_months +=$nine_months;
	            $total_payments += $payments_total;
	            $total_invoices += $invoice_total;

	            $total_balance += $invoice_total-$payments_total;


				$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$creditor_name.'</td>
							<td>'.number_format($opening_balance, 2).'</td>
							<td>'.number_format($this_month, 2).'</td>
							<td>'.number_format($three_months, 2).'</td>
							<td>'.number_format($six_months, 2).'</td>
							<td>'.number_format($nine_months, 2).'</td>
							<td>'.number_format($payments_total, 2).'</td>
							<td>'.number_format($invoice_total, 2).'</td>
							<td>'.number_format($invoice_total-$payments_total, 2).'</td>
							<td><a href="'.site_url().'creditor-statement/'.$creditor_id.'" class="btn btn-sm btn-info" >Account</a></td>
							<td><a href="'.base_url().'accounting/creditors/print_creditor_account/'.$creditor_id.'" class="btn btn-sm btn-warning"  target="_blank"><i class="fa fa-print"></i> Print</a></td>
							<td><a href="'.site_url().'accounting/creditors/edit_creditor/'.$creditor_id.'" class="btn btn-sm btn-success">Edit</a></td>
							<td><a href="'.site_url().'accounting/delete-creditor/'.$creditor_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you want to remove this account ?\')"><i class="fa fa-trash"></i></a></td>
							</tr>
							';
				
			}
			
			$result .= '<tr>
							<td colspan=3></td>
							<td>'.number_format($total_this_month, 2).'</td>
							<td>'.number_format($total_three_months, 2).'</td>
							<td>'.number_format($total_six_months, 2).'</td>
							<td>'.number_format($total_nine_months, 2).'</td>
							<td>'.number_format($total_payments, 2).'</td>
							<td>'.number_format($total_invoices, 2).'</td>
							<td>'.number_format($total_balance, 2).'</td>
						</tr>
						';
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