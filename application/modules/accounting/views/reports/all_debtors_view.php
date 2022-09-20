<!-- search -->
<?php echo $this->load->view('search_patients_debtors', '', TRUE);?>
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
          <h5 class="center-align"><?php echo $this->session->userdata('search_title');?></h5>
<?php
		$result = '<a href="'.site_url().'accounting/reports/export_debtors" target="_blank" class="btn btn-sm btn-success pull-right">Export</a>';
		$search = $this->session->userdata('all_debtors_search_query');
		if(!empty($search))
		{
			echo '<a href="'.site_url().'accounting/reports/close_all_reports_search" class="btn btn-sm btn-warning">Close Search</a>';
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
						   <th>Last Visit Date</th>
						  <th>Patient Number</th>
						  <th>Patient</th>
						  <th>Phone</th>
						  
				';
				
			$result .= '
			
						  <th>Balance</th>
						  <th></th>
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
			$total_waiver = 0;
			foreach ($query->result() as $row)
			{
				$total_invoiced = 0;
				
				$patient_id = $row->patient_id;
				$visit_type_id = $row->visit_type;
				$last_visit = $row->last_visit;
				$patient_number = $row->patient_number;
				$patient_surname = $row->patient_surname;
				$patient_othernames = $row->patient_othernames;
				$patient_phone1 = $row->patient_phone1;
				$balance = $row->balance;
				$invoices = $row->total_invoice_amount;
				$amount_paid = $row->total_paid_amount;
				$amount_waived = $row->total_waived_amount;
				
				$count++;
				$total_invoice += $invoices;
				$total_payments += $amount_paid;
				$total_balance +=$balance;
				$total_waiver +=$amount_waived;
				//payment data
				$charges = '';
				
				$last_visit = date('jS M Y',strtotime($last_visit));
				
				$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$last_visit.'</td>
							<td>'.$patient_number.'</td>
							<td>'.$patient_surname.' '.$patient_othernames.'</td>
							<td>'.$patient_phone1.'</td>
							<td>'.$balance.'</td>
							<td><a href="'.site_url().'administration/individual_statement/'.$patient_id.'/1" class="btn btn-sm btn-warning" target="_blank">Statement</a></td>
						</tr> 
				';
				
			}

			$result .= 
					'
						<tr>
							<td colspan=5> Totals</td>
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