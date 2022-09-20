<!-- search -->
<?php echo $this->load->view('search_all_debtors', '', TRUE);?>
<!-- end search -->
<?php //echo $this->load->view('debtors_statistics', '', TRUE);?>
 
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
		$result = '';
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
						  <th>Visit Date</th>
						  <th>Patient</th>
						  <th>Category</th>
						  <th>Phone Numer</th>
						  
				';
				
			$result .= '
			
						  <th>Invoice</th>
						  <th>Patient Bill</th>
						  <th>Insurance Bill</th>
						  <th>Payment</th>
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
				$patient_phone1 = $row->patient_phone1;
				$parent_visit = $row->parent_visit;

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

				$cash_payment = $this->accounts_model->get_cash_payments($visit_id);
				$insurance_payment = $this->accounts_model->get_insurance_payments($visit_id);
				$payments_value = $insurance_payment + $cash_payment;
                $invoice_amount = $this->accounts_model->get_visit_total_invoice($visit_id);

                $cummulative_invoice = $invoice_amount;

                $balance = $this->accounts_model->balance($payments_value,$invoice_total);

                $invoice_total = $invoice_amount - $payments_value ;

                $waiver_amount = $this->accounts_model->get_sum_debit_notes($visit_id);

                $cash_balance = 0;
                if(!empty($rejected_amount))
                {
                	$cash_invoice = $rejected_amount;
                }

               $rs_rejection = $this->dental_model->get_visit_rejected_updates_sum($visit_id,$visit_type);
				$total_rejected = 0;
				if(count($rs_rejection) >0){
				  foreach ($rs_rejection as $r2):
				    # code...
				    $total_rejected = $r2->total_rejected;

				  endforeach;
				}

				$rejected_amount += $total_rejected;
				

				$doctor = $row->personnel_onames.' '.$row->personnel_fname;
				
				
				//payment data
				$charges = '';
				
				

		
				
				$payments_value = $this->accounts_model->total_payments($visit_id);

				$invoice_total = $amount_payment = $this->accounts_model->total_invoice($visit_id);

				// var_dump($parent_visit); die();

				// if($parent_visit == 0 OR empty($parent_visit))
				// {
				// 	$invoice_total = $amount_payment - $rejected_amount;
				// }
				// else
				// {
				// 	$rejected_amount = $this->accounts_model->get_child_amount_payable($visit_id);
				// 	$invoice_total = $rejected_amount;
				// }


				// if($parent_visit == 0 OR empty($parent_visit))
				// {
				// 	$balance = $invoice_total - $payments_value;
				// }
				// else
				// {
				// 	$rejected_amount = $this->accounts_model->get_child_amount_payable($visit_id);
				// 	// echo $rejected_amount; die();
				// 	$balance = $rejected_amount - $payments_value;


				// }

				if($visit_type > 1 AND $total_rejected > 0)
				{
					$payable_by_patient = $rejected_amount;
					$payable_by_insurance = $invoice_total - $rejected_amount;
				}
				else
				{
					$payable_by_patient = $invoice_total;
					$payable_by_insurance = 0;
				}
				$balance  = $this->accounts_model->balance($payments_value,$invoice_total);
				
				if($balance > 0)
				{
					$total_insurance_payments += $payments_value;
					$total_balance += $balance;
					$total_rejected_amount += $billed_amount;				
					$total_invoice += $invoice_total;
					$total_payable_by_insurance += $payable_by_insurance;
					$total_payable_by_patient += $payable_by_patient;

					$count++;
					$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$visit_date.'</td>
							<td>'.$patient_surname.' '.$patient_othernames.'</td>
							<td>'.$visit_type_name.'</td>
							<td>'.$patient_phone1.'</td>
					'.$charges;
					
					$result .= '
								<td>'.$invoice_total.'</td>
								<td>'.$payable_by_patient.'</td>
								<td>'.$payable_by_insurance.'</td>
								<td>'.($payments_value).'</td>
								<td>'.($balance).'</td>
								<td><a href="'.site_url().'accounts/print_invoice_new/'.$visit_id.'" class="btn btn-sm btn-success" target="_blank">Invoice</a></td>
							</tr> 
					';
				}
				
				
				
			}

			$result .= 
					'
						<tr>
							<td colspan=5> Totals</td>
							<td><strong>'.number_format($total_invoice,2).'</strong></td>
							<td><strong>'.number_format($total_payable_by_patient,2).'</strong></td>
							<td><strong>'.number_format($total_payable_by_insurance,2).'</strong></td>
							<td><strong>'.number_format($total_insurance_payments,2).'</strong></td>
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