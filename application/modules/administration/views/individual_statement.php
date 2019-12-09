<!-- search -->
<?php //echo $this->load->view('search/search_patient', '', TRUE);

$res = $patient->row();
$patient_id = $res->patient_id;
$patient_surname = $res->patient_surname;
$patient_othernames = $res->patient_othernames;
$title_id = $res->title_id;
$patient_date_of_birth = $res->patient_date_of_birth;
$gender_id = $res->gender_id;
$religion_id = $res->religion_id;
$civil_status_id = $res->civil_status_id;
$patient_email = $res->patient_email;
$patient_address = $res->patient_address;
$patient_postalcode = $res->patient_postalcode;
$patient_town = $res->patient_town;
$patient_phone1 = $res->patient_phone1;
$patient_phone2 = $res->patient_phone2;
$patient_kin_sname = $res->patient_kin_sname;
$patient_kin_othernames = $res->patient_kin_othernames;
$relationship_id = $res->relationship_id;
$patient_national_id = $res->patient_national_id;
$insurance_company_id = $res->insurance_company_id;
$next_of_kin_contact = $res->patient_kin_phonenumber1;


?>
<!-- end search -->

<div class="row">
    <div class="col-md-12">

        <section class="panel">
            <header class="panel-heading">
                
                <h2 class="panel-title"><?php echo $title;?></h2>
                <a href="<?php echo site_url().'print-patient-statement/'.$patient_id;?>" class="btn btn-sm btn-warning pull-right" style="margin-top: -25px;" target="_blank"> Print Statement</a>
            </header>
            
            <div class="panel-body">
                <div class="well well-sm info">
                    <h5 style="margin:0;">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>First name:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $patient_surname;?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Other names:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $patient_othernames;?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </h5>
                </div>
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
				
		$result =  '';
		if($module > 0)
		{

		}
		else
		{
			$result = '<a href="'.site_url().'administration/patient_statement" class="btn btn-success">Back to Statements</a>';
	
		}
		
		
		
		$result .= '';
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			
			$result .= 
			'
				<table class="table table-hover table-bordered ">
				  <thead>
					<tr>
					  <th style="text-align:center" rowspan=2>Date</th>
					  <th rowspan=2>Document Number</th>
					  <th rowspan=2>RX</th>
					  <th colspan=2 style="text-align:center;">Amount</th>
					
					</tr>
					<tr>
					  
					  <th style="text-align:center">Invoice</th>
					  <th style="text-align:center">Payment</th>
					  <th style="text-align:center">Balance</th>
					</tr>
				  </thead>
				  <tbody>
			';
			
			
			$personnel_query = $this->personnel_model->get_all_personnel();
			$total_invoiced_amount = 0;
			$total_paid_amount = 0;
			$total_balance =0;
			foreach ($query->result() as $row)
			{
				$visit_id = $row->visit_id;
				$visit_date = $row->visit_date;
				$visit_date = $row->visit_date;
				$total_invoice = $this->accounts_model->total_invoice($visit_id);
				$total_payments = $this->accounts_model->total_payments($visit_id);

				$patient_data = $this->reception_model->patient_names2(NULL, $visit_id);
                $visit_type_preffix = $patient_data['visit_type_preffix'];

				$array_split = explode("-", $visit_date);

				$month = $array_split[1];
				$year = $array_split[0];

				// $invoice_number = 
				$invoice_number = $visit_id; //$visit_type_preffix.'-'.$month.'/'.$year.'-'.sprintf('%03d', $visit_id);
				$total_paid_amount = $total_paid_amount + $total_payments;
				$total_invoiced_amount = $total_invoiced_amount + $total_invoice;


				$payments_rs = $this->accounts_model->payments($visit_id);
               
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

				$item_invoiced_rs = $this->accounts_model->get_patient_visit_charge_items($visit_id);
				$charged_services = '<table class="table">
									  <thead>
										<tr>
										  <th >Name</th>
										  <th >Units</th>
										  <th >Charge</th>
										  <th >Total</th>										
										</tr>
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
					$charged_services .=  '<tr>
													<td colspan=3>TOTAL</td>
													<td> '.number_format($total,2).'</td>
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
									</table>';


				$count++;
				if($total_invoice > 0)
				{
					$balance = $total_invoice - $total_payments;
					$total_balance += $balance;


					$result .= 
					'
						<tr>
							<td style="text-align:center">'.$visit_date.'</td>
							<td>'.$invoice_number.'</td>
							<td>'.$charged_services.'</td>
							<td style="text-align:center">'.number_format($total_invoice,2).'</td>
							<td style="text-align:center">'.number_format($total_payments,2).'</td>
							<td style="text-align:center">'.number_format($total_balance,2).'</td>
						</tr> 
					';
				}

				
				
			}
				$result .= 
					'
						<tr>
							<td></td>
							<td></td>
							<td style="text-align:center">Totals</td>
							<td style="text-align:center; font-weight:bold;"> '.number_format($total_invoiced_amount,2).'</td>
							<td style="text-align:center; font-weight:bold;">'.number_format($total_paid_amount,2).'</td>
							<td style="text-align:center; font-weight:bold;">'.number_format($total_balance,2).'</td>
						</tr> 
					';
				$Balance =  $total_invoiced_amount -$total_paid_amount;
					$result .= 
					'
						<tr>
							<td></td>
							<td></td>
							<td style="text-align:center; font-weight:bold;">Balance</td>
							<td colspan="3" style="text-align:center; font-weight:bold;">'.number_format($Balance,2).'</td>
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
			$result .= "There are no items";
		}
		
		echo $result;
?>
          	</div>
          
          	<div class="widget-foot">
                                
				<?php if(isset($links)){echo $links;}?>
            
            </div>
		</section>
    </div>
  </div>