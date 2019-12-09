<!-- search -->

<div class="row">
    <div class="col-md-12">

        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	 <h2 class="panel-title"><?php echo $title;?></h2>
            	  <a href="<?php echo site_url();?>company-financials/profit-and-loss"  class="btn btn-sm btn-warning pull-right" style="margin-top:-25px;margin-left:5px" > Back to P & L </a>
            	 <a href="<?php echo site_url();?>accounting/company_financial/export_services_bills/<?php echo $service_id;?>"  class="btn btn-sm btn-success pull-right" style="margin-top:-25px;" download> Export Patients </a>
            </header>             

          <!-- Widget content -->
                <div class="panel-body">
          <h5 class="center-align"><?php echo $this->session->userdata('search_title');?></h5>
<?php
		$result = '';
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
						  <th>Charge Date</th>
						  <th>Patient</th>
						  <th>Category</th>
						  <th>Invoice Number</th>
						  <th>Units</th>
						  <th>Charge Amount</th>
						  <th>Total</th>
						  
				';
				
			$result .= '
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
			foreach ($query->result() as $row)
			{
				$total_invoiced = 0;
				$visit_date = date('jS M Y',strtotime($row->visit_date));
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
				$date = $row->date;
				$invoice_date = date('jS M Y',strtotime($row->date));
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
				$invoice_number = $row->invoice_number;
				$visit_charge_amount = $row->visit_charge_amount;
				$visit_charge_units = $row->visit_charge_units;

				if(empty($rejected_amount))
				{
					$rejected_amount = 0;
				}
				// $coming_from = $this->reception_model->coming_from($visit_id);
				// $sent_to = $this->reception_model->going_to($visit_id);
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;

				// $payments_value = $this->accounts_model->total_payments($visit_id);


				$doctor = $row->personnel_onames.' '.$row->personnel_fname;
				
				$count++;
				
				//payment data
				$charges = '';
				
			

				$total_invoice += $visit_charge_units;			
				$total_balance += $visit_charge_amount*$visit_charge_units;
				$total_payments += $visit_charge_amount;
				
				$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$invoice_date.'</td>
							<td>'.$patient_surname.' '.$patient_othernames.'</td>
							<td>'.$visit_type_name.'</td>
							<td>'.$invoice_number.'</td>
							<td>'.$visit_charge_units.'</td>
							<td>'.number_format($visit_charge_amount,2).'</td>
							<td>'.number_format($visit_charge_amount*$visit_charge_units,2).'</td>
							';
					
				$result .= '
							<td><a href="'.site_url().'accounts/print_invoice_new/'.$visit_id.'" class="btn btn-sm btn-success" target="_blank">Invoice</a></td>
						</tr> 
				';
				
			}

			$result .= 
					'
						<tr>
							<td colspan=5> Totals</td>
							<td><strong>'.number_format(round($total_invoice),2).'</strong></td>
							<td><strong>'.number_format(round($total_payments),2).'</strong></td>
							<td><strong>'.number_format(round($total_balance),2).'</strong></td>
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