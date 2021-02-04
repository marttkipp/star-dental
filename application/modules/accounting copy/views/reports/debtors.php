<!-- search -->
<?php echo $this->load->view('search_debtors', '', TRUE);?>
<!-- end search -->
<?php echo $this->load->view('transaction_statistics', '', TRUE);?>
 
<div class="row">
    <div class="col-md-12">

        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	 <h2 class="panel-title"><?php echo $title;?></h2>
            </header>             

          <!-- Widget content -->
                <div class="panel-body">
          <h5 class="center-align"><?php echo $this->session->userdata('search_title');?></h5>
<?php
		$result = '<a href="'.site_url().'accounting/reports/export_debtors" target="_blank" class="btn btn-sm btn-success pull-right">Export</a>';
		$search = $this->session->userdata('debtors_search_query');
		if(!empty($search))
		{
			echo '<a href="'.site_url().'accounting/reports/close_reports_search" class="btn btn-sm btn-warning">Close Search</a>';
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
						  <th>Invoice Date</th>
						  <th>Patient No.</th>
						  <th>Patient</th>
						  <th>Category</th>
						  <th>Doctor</th>
						  <th>Invoice No.</th>

						  
				';
				
			$result .= '
							<th>Branch Code</th>
						  <th>Invoice Amount</th>
						   <th>Payments.</th>
						   <th>Balance.</th>
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
				$visit_date = date('jS M Y',strtotime($row->transaction_date));
				$visit_time = date('H:i a',strtotime($row->visit_time));
				if($row->visit_time_out != '0000-00-00 00:00:00')
				{
					$visit_time_out = date('H:i a',strtotime($row->visit_time_out));
				}
				else
				{
					$visit_time_out = '-';
				}
				
				$visit_id = $row->visit_id;
				$patient_id = $row->patient_id;
				$personnel_id = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$visit_type_id = $row->visit_type;
				$patient_number = $row->patient_number;
				$visit_type = $row->visit_type;
				$visit_table_visit_type = $visit_type;
				$patient_table_visit_type = $visit_type_id;
				$rejected_amount = $row->amount_rejected;
				$visit_invoice_number = $row->visit_invoice_number;
				$visit_invoice_id = $row->visit_invoice_id;
				$parent_visit = $row->parent_visit;
				$branch_code = $row->branch_code;

				if(empty($rejected_amount))
				{
					$rejected_amount = 0;
				}
				// $coming_from = $this->reception_model->coming_from($visit_id);
				// $sent_to = $this->reception_model->going_to($visit_id);
				$visit_type_name = $row->payment_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;

				$doctor = $row->personnel_fname;
				$count++;
				$invoice_total = $row->dr_amount;
				$payments_value = $this->accounts_model->get_visit_invoice_payments($visit_invoice_id);
				$balance  = $this->accounts_model->balance($payments_value,$invoice_total);

				$total_payable_by_patient += $invoice_total;
				$total_payments += $payments_value;
				$total_balance += $balance;
				$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$visit_date.'</td>
							<td>'.$patient_number.'</td>
							<td>'.ucwords(strtolower($patient_surname)).'</td>
							<td>'.$visit_type_name.'</td>
							<td>'.$doctor.'</td>
							<td>'.$visit_invoice_number.'</td>
							<td>'.$branch_code.'</td>
							<td>'.number_format($invoice_total,2).'</td>
							<td>'.(number_format($payments_value,2)).'</td>
							<td>'.(number_format($balance,2)).'</td>
							<td><a href="'.site_url().'print-invoice/'.$visit_invoice_id.'/'.$visit_id.'" target="_blank" class="btn btn-sm btn-warning"><i class="fa fa-print"></i> Print Invoice</a></td>
						</tr> 
				';
				
			}

			$result .= 
					'
						<tr>
							<td colspan=8> Totals</td>
							<td><strong>'.number_format($total_payable_by_patient,2).'</strong></td>
							<td><strong>'.number_format($total_payments,2).'</strong></td>
							<td><strong>'.number_format($total_balance,2).'</strong></td>
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
			$result .= "There are no visits";
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