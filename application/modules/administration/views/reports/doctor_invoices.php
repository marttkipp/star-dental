<!-- search -->
<?php echo $this->load->view('search/search_doctor_invoices', '', TRUE);?>
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
<?php
		$result = '';
		if(!empty($search))
		{
			echo '<a href="'.site_url().'administration/reports/close_doctor_invoice_search" class="btn btn-sm btn-warning">Close Search</a>';
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
						  <th>Charge Detail</th>
						  <th>Iris Charge</th>
						  <th>Amount Charge</th>
						</tr>
					  </thead>
					  <tbody>
			';
			
			$personnel_query = $this->personnel_model->get_all_personnel();
			// var_dump($query); die();
			$total_invoiced = 0;
			$total_charged = 0;
			foreach ($query->result() as $row)
			{
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
				 $visit_type_id = $row->visit_type_idd;
				$visit_type = $row->visit_type;
				$rejected_amount = $row->rejected_amount;
				$visit_table_visit_type = $visit_type;
				$invoice_number = $visit_id;//$row->invoice_number;
				$patient_table_visit_type = $visit_type_id;
				// $coming_from = $this->reception_model->coming_from($visit_id);
				// $sent_to = $this->reception_model->going_to($visit_id);
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_phone1 = $row->patient_phone1;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$close_card = $row->close_card;
				$hold_card = $row->hold_card;
				$doctor_invoice_status = $row->doctor_invoice_status;
				
				$doctor = $row->personnel_onames.' '.$row->personnel_fname;
				// this is to check for any credit note or debit notes
				$payments_value = $this->accounts_model->total_payments($visit_id);

				$invoice_total = $this->accounts_model->total_invoice($visit_id);

				$balance = $this->accounts_model->balance($payments_value,$invoice_total);
				$visit_waiver = $this->reports_model->get_visit_waiver($visit_id);
				// end of the debit and credit notes
				$doctor_rs = $this->reports_model->get_patient_invoiced_items($visit_id);


				$payments_rs = $this->accounts_model->payments($visit_id);
                $total_payments = 0;
                $payments_made = '';
                if(count($payments_rs) > 0)
                {
                    foreach ($payments_rs as $key_items):
             
                        $payment_type = $key_items->payment_type;
                         $payment_status = $key_items->payment_status;
                        if($payment_type == 1 && $payment_status == 1)
                        {
                            $payment_method = $key_items->payment_method;
                            $amount_paid = $key_items->amount_paid;
                            $payment_created = $key_items->payment_created;
                            

                            $payments_made .='<tr>
												<td>'.$payment_created.'</td>
												<td>'.number_format($amount_paid).'</td>
											</tr>';
                        }


                    endforeach;
                    
                }
                else
                {
                	 $payments_made .='<tr>
											<td colspan=2>No Payments Done</td>
										</tr>';
                }


				$billed_charges = '<table class="table">
									  <thead>
										<tr>
										  <th >Name</th>
										  <th >Amount</th>										
										</tr>
										</thead>
									  <tbody>';
				if($doctor_rs->num_rows() > 0){
					$total_billed = 0;
					foreach ($doctor_rs->result() as $key_items_row =>$value):
						$invoiced_amount = $value->invoiced_amount;
						$approved_by = $value->approved_by;
						$type = $value->type;
						$total_billed += $invoiced_amount;
						if($type == 1)
						{
							// cash
							$transaction = 'Cash';
						}
						else
						{
							// insurance
							$transaction = 'Insurance';
						}
						$billed_charges .='<tr>
												<td>'.$transaction.'</td>
												<td>'.$invoiced_amount.'</td>
											</tr>';


					endforeach;
					$billed_charges .='<tr>
												<td>TOTAL</td>
												<td>'.$total_billed.'</td>
											</tr>';
					
					$total_charged += $total_billed;
				}
				$billed_charges .= '</tbody>
									</table>';
				$item_invoiced_rs = $this->accounts_model->get_patient_visit_charge_items($visit_id);
				$charged_services = '<table class="table">
									  <thead>
										<tr>
										  <th >Name</th>
										  <th >Units</th>
										  <th >Charge</th>
										  <th >Total</th>										
										</tr>
										</thead>
									  <tbody>';

				if(count($item_invoiced_rs) > 0){
					$s=0;
					$total_nhif_days = 0;
					$total = 0;
					
					foreach ($item_invoiced_rs as $key_items):
						$service_charge_id = $key_items->service_charge_id;
						$service_charge_name = $key_items->service_charge_name;
						$visit_charge_amount = $key_items->visit_charge_amount;
						$service_name = $key_items->service_name;
						$units = $key_items->visit_charge_units;
						$service_id = $key_items->service_id;
						$personnel_id = $key_items->personnel_id;
						$total += $units*$visit_charge_amount;

						$charged_services .=  '<tr>
													<td>'.$service_charge_name.'</td>
													<td>'.$units.'</td>
													<td>'.$visit_charge_amount.'</td>
													<td> '.number_format($units*$visit_charge_amount,2).'</td>
												</tr>';
						
					endforeach;
					
					
					if($visit_waiver > 0)
					{
						$charged_services .=  '<tr>
												<td colspan=3>WAIVER</td>
												<td><strong> ('.number_format($visit_waiver,2).') </strong></td>
											</tr>';
					}
					$charged_services .=  '<tr>
													<td colspan=3>TOTAL BILL</td>
													<td><strong> '.number_format($invoice_total,2).' </strong></td>
												</tr>';

					if($visit_type_id != 1 AND $rejected_amount > 0)
					{
					
						$charged_services .=  '<tr>
													<td colspan=3>'.$visit_type_name.' BILL</td>
													<td><strong> '.number_format($invoice_total-$rejected_amount-$payments_value,2).' </strong></td>
												</tr>';
					}
					else
					{
						$charged_services .=  '<tr>
													<td colspan=3>'.$visit_type_name.' BILL</td>
													<td><strong> '.number_format($invoice_total,2).' </strong></td>
												</tr>';
					}
					if($visit_type_id != 1 AND $rejected_amount > 0)
					{
						$charged_services .=  '<tr>
												<td colspan=3>CASH BALANCE</td>
												<td><strong> '.number_format($rejected_amount,2).' </strong></td>
											</tr>';
					}
					$charged_services .=  '<tr>
												<td colspan=3>PAYMENT</td>
												<td><strong> '.number_format($payments_value,2).' </strong></td>
											</tr>';
					$charged_services .=  '<tr>
												<td colspan=3>BALANCE</td>
												<td><strong> '.number_format($balance,2).' </strong></td>
											</tr>';
					
				}
				$charged_services .= '</tbody>
									</table>

									<p><strong>PAYMENTS</strong><p>';

				$charged_services .= '<table class="table">
									  <thead>
										<tr>
										  <th >Date</th>
										  <th >Amount</th>										
										</tr>
										</thead>
									  <tbody>
									  	'.$payments_made.'
										</tbody>
									</table>

									<p><strong>DR '.strtoupper($doctor).'</strong><p>
									<p><strong> '.strtoupper($visit_type_name).'</strong><p>';

				$total_invoiced += $invoice_total;
				

				
				$count++;
				
				//payment data
			
				if($hold_card == 1)
				{
					$button ='<td><a href="'.site_url().'reception/unhold_card/'.$visit_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to unhold this card?\');">Unhold Card</a></td>';
				}
				else
				{
					if($close_card == 1)
					{
						$button ='<td><a href="'.site_url().'accounts/print_invoice_new/'.$visit_id.'" class="btn btn-sm btn-success" target="_blank">Invoice</a></td>
								 <td><a href="'.site_url().'administration/reports/open_visit_current/'.$visit_id.'"  onclick="return confirm(\'Do you want to open card ?\');" class="btn btn-sm btn-info" >Open Card</a></td>';
					}
					else
					{
						$button ='<td><a href="'.site_url().'administration/reports/end_visit_current/'.$visit_id.'"  onclick="return confirm(\'Do you want to close visit ?\');" class="btn btn-sm btn-danger" >Close Card</a></td>';
					}
				}


				if($doctor_invoice_status == 1)
				{
					$buttons = '';
				}
				else
				{

					// if($visit_type_id != 1 AND $rejected_amount > 0)
					// {
						$result.= form_open("administration/reports/invoice_hospital/".$visit_id.'/2', array("class" => "form-horizontal"));
						$buttons = '<td><input type="text" name="amount'.$visit_id.'" class="form-control" value="" placeholder="insurance charge"/> <br>
								
							
								<input type="text" name="cash_amount'.$visit_id.'" class="form-control" value="" placeholder="cash charge"/> <br>
								<button type="submit" class="btn btn-sm btn-success" onclick="return confirm(\'Do you want to update the charge ?\');" >UPDATE CHARGE </button></td>
								';
					// }
					// else if($visit_type_id != 1 AND empty($rejected_amount))
					// {
					// 	$result.= form_open("administration/reports/invoice_hospital/".$visit_id.'/0', array("class" => "form-horizontal"));
					// 	$buttons = '<td><input type="text" name="amount'.$visit_id.'" class="form-control" value=""/> <br>
					// 			<button type="submit" class="btn btn-sm btn-warning" onclick="return confirm(\'Do you want to update the charge ?\');" >Insurance Charge </button></td>';
					// } 
					// else
					// {
					// 	$result.= form_open("administration/reports/invoice_hospital/".$visit_id.'/1', array("class" => "form-horizontal"));
					// 	$buttons = '<td><input type="text" name="amount'.$visit_id.'" class="form-control" value=""/> <br>
					// 			<button type="submit" class="btn btn-sm btn-info" onclick="return confirm(\'Do you want to update the charge ?\');" >Cash Charge </button></td>';
					 
					// }

				}

				$personnel_id = $this->session->userdata('personnel_id');
				$is_cashier = $this->reception_model->check_if_admin($personnel_id,5);
				
				if(($is_cashier OR $personnel_id == 0) AND $doctor_invoice_status == 0)
				{
					$buttons .='<td><a href="'.site_url().'administration/reports/approve_payment/'.$visit_id.'" class="btn btn-sm btn-danger"  onclick="return confirm(\'You are about to approve this charge. Continue?\');">Approve </a></td>';
				}
				// payment value ///
				if(empty($rejected_amount))
				{
					$rejected_amount = 0;
				}				
				
					$result .= 
						'
							<tr>
								<td>'.$count.'</td>
								<td>'.$visit_date.'</td>
								<td>'.$patient_surname.' '.$patient_othernames.'</td>
								<td>'.$charged_services.'</td>
								<td>'.$billed_charges.'</td>					
								'.$buttons.'
								
								
							</tr> 
					';

				if($doctor_invoice_status == 1)
				{
					$result .='';
				}
				else
				{
					 $result .= form_close();

				}
				
			}
			
			$result .= 
						'
							<tr>
								<td colspan=3>TOTAL KES</td>
								<td>KES. '.number_format($total_invoiced,2).'</td>								
								<td>KES. '.number_format($total_charged,2).'</td>
								
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