<!-- search -->
<?php echo $this->load->view('search_general_report', '', TRUE);?>
<!-- end search -->
<?php //echo $this->load->view('transaction_statistics', '', TRUE);?>
 
<div class="row">
    <div class="col-md-12">

        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	 <h2 class="panel-title"><?php echo $title;?></h2>
            </header>             

          <!-- Widget content -->
                <div class="panel-body">
          <h5 class="center-align"><?php echo $this->session->userdata('general_search_title');?></h5>
<?php
		$result = '<a href="'.site_url().'accounting/reports/export_general_report" target="_blank" class="btn btn-sm btn-success pull-right">Export</a>';
		$search = $this->session->userdata('general_report_search');
		if(!empty($search))
		{
			echo '<a href="'.site_url().'accounting/reports/close_general_reports_search" class="btn btn-sm btn-warning">Close Search</a>';
		}
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			$result .= 
				'
					<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>TYPE</th>
						  <th>DATE</th>
						  <th>INVOICE/RECEIPT NO.</th>
						  <th>PATIENT\'S NAME</th>
						  <th>VISIT TYPE</th>
						  <th>PROCEDURES</th>
						  <th>AMOUNT</th>
						  <th>PAID BY</th>
						  <th>TRANSACTION NO</th>
						  <th>BRANCH</th>
						</tr>
					  </thead>
					  <tbody>
			';
			
			// $personnel_query = $this->accounting_model->get_all_personnel();
			$total_waiver = 0;
			$total_payments = 0;
			$total_invoice = 0;
			$total_balance = 0;
			$total_rejected_amount = 0;
			$total_cash_balance = 0;
			$total_insurance_payments =0;
			$total_insurance_invoice =0;
			$total_payable_by_patient = 0;
			$total_payable_by_insurance = 0;
			$total_debit_notes = 0;
			$total_credit_notes= 0;
			foreach ($query->result() as $row)
			{
				$total_invoiced = 0;
				
				
				
				
				$parent_visit = $row->parent_visit;
				$branch_code = $row->branch_code;
				$transaction_id = $row->transaction_id;
				$reference_id = $row->reference_id;
				$visit_type_name = $row->payment_type_name;
				$transactionCategory = $row->transactionCategory;
				$transaction_date = $row->transaction_date;
				$dr_amount = $row->dr_amount;
				$cr_amount = $row->cr_amount;
				$payment_method_name = $row->payment_method_name;
				$payment_type_name = $row->payment_type_name;
				$patient_surname = $row->patient_surname;
				$reference_code = $row->reference_code;
				$transaction_code = $row->transactionCode;
				$payment_method_name = $row->payment_method_name;

				$branch_code = $row->branch_code;

				$visit_date = date('jS M Y',strtotime($row->transaction_date));

				$doctor = '';//$row->personnel_fname;
				$count++;
				// $invoice_total = $row->dr_amount;
				$payments_value = 0;//$this->accounts_model->get_visit_invoice_payments($visit_invoice_id);
				// $balance  = $this->accounts_model->balance($payments_value,$invoice_total);

				$total_payable_by_patient += $invoice_total;
				$total_payments += $payments_value;
				$total_balance += 0;

				if($transactionCategory == "Revenue")
				{
					$amount = $dr_amount;
					$transactionCategory = "Invoice";
					$button = '<td><a href="'.site_url().'print-invoice/'.$transaction_id.'/'.$reference_id.'" target="_blank" class="btn btn-sm btn-warning"><i class="fa fa-print"></i> Print Invoice</a></td>';
				}
				else if($transactionCategory == "Revenue Payment")
				{
					$amount = $cr_amount;
					$transactionCategory = "Receipt";
					$button = '<td><a href="'.site_url().'print-receipt/'.$transaction_id.'/'.$reference_id.'" target="_blank" class="btn btn-sm btn-warning"><i class="fa fa-print"></i> Print Receipt</a></td>';
				}

				
				$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$transactionCategory.'</td>
							<td>'.$visit_date.'</td>
							<td>'.$reference_code.'</td>
							<td>'.ucwords(strtolower($patient_surname)).'</td>
							<td>'.$visit_type_name.'</td>
							<td>-</td>
							<td>'.number_format($amount,2).'</td>
							<td>'.ucwords(strtolower($payment_method_name)).'</td>
							<td>'.ucwords(strtolower($transaction_code)).'</td>
							<td>'.$branch_code.'</td>
							'.$button.'
						</tr> 
				';
				
			}

			// $result .= 
			// 		'
			// 			<tr>
			// 				<td colspan=8> Totals</td>
			// 				<td><strong>'.number_format($total_payable_by_patient,2).'</strong></td>
			// 				<td><strong>'.number_format($total_payments,2).'</strong></td>
			// 				<td><strong>'.number_format($total_balance,2).'</strong></td>
			// 			</tr> 
			// 	';
			
			$result .= 
			'
						  </tbody>
						</table>
			';
		}
		
		else
		{
			$result .= "There are no information";
		}
		
		echo $result;
?>
          </div>
          
          <div class="widget-foot">
                                
				<?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        
		</section>
    </div>
  </div>