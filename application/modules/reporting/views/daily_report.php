<?php

	$date_tomorrow = date('Y-m-d');
		$visit_date = date('jS M Y',strtotime($date_tomorrow));

		$date_tomorrow = date('Y-m-d');
		$date_tomorrow = date("Y-m-d", strtotime("-1 day", strtotime($date_tomorrow)));


		$branch = $this->config->item('branch_name');
		$message['subject'] =  $branch.' '.$visit_date.' report';

		$where = $where1 = $where6 = 'visit.patient_id = patients.patient_id AND visit.visit_delete = 0 AND visit.visit_date = "'.$date_tomorrow.'"';
		$payments_where = 'visit.patient_id = patients.patient_id AND visit.visit_delete = 0 ';
		$table = 'visit, patients';


		
			//cash payments todays visit
		$where2 = $payments_where.' AND payments.payment_method_id = 2 AND payments.payment_type = 1 AND payments.cancel = 0 AND visit.visit_date = "'.$date_tomorrow.'" AND payments.payment_created = "'.$date_tomorrow.'"';
		$total_cash_collection = $this->reports_model->get_total_cash_collection($where2, $table);

		// cash payments for debt payments
		$where2 = $payments_where.' AND payments.payment_method_id = 2 AND payments.payment_type = 1 AND payments.cancel = 0 AND visit.visit_date <> "'.$date_tomorrow.'" AND payments.payment_created = "'.$date_tomorrow.'"';
		$total_cash_debt = $this->reports_model->get_total_cash_collection($where2, $table);

        
		// mpesa today's visits
		$where2 = $payments_where.' AND payments.payment_method_id = 5 AND payments.payment_type = 1 AND payments.cancel = 0  AND visit.visit_date = "'.$date_tomorrow.'" AND payments.payment_created = "'.$date_tomorrow.'"';
		$total_mpesa_collection = $this->reports_model->get_total_cash_collection($where2, $table);

		// mpesa today's visits
		$where2 = $payments_where.' AND payments.payment_method_id = 5 AND payments.payment_type = 1 AND payments.cancel = 0  AND visit.visit_date <> "'.$date_tomorrow.'" AND payments.payment_created = "'.$date_tomorrow.'"';
		$total_mpesa_debt = $this->reports_model->get_total_cash_collection($where2, $table);



		$where2 = $payments_where.' AND (payments.payment_method_id = 1 OR  payments.payment_method_id = 6 OR  payments.payment_method_id = 7 OR  payments.payment_method_id = 8)  AND payments.payment_type = 1 AND payments.cancel = 0 AND visit.visit_date = "'.$date_tomorrow.'" AND payments.payment_created = "'.$date_tomorrow.'"';
		$total_other_collection = $this->reports_model->get_total_cash_collection($where2, $table);

		$where2 = $payments_where.' AND (payments.payment_method_id = 1 OR  payments.payment_method_id = 6 OR  payments.payment_method_id = 7 OR  payments.payment_method_id = 8)  AND payments.payment_type = 1 AND payments.cancel = 0 AND visit.visit_date <> "'.$date_tomorrow.'" AND payments.payment_created = "'.$date_tomorrow.'"';
		$total_other_debt = $this->reports_model->get_total_cash_collection($where2, $table);


		$where4 = 'payments.payment_method_id = payment_method.payment_method_id AND payments.visit_id = visit.visit_id  AND visit.visit_delete = 0  AND visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND payments.cancel = 0 AND payments.payment_type = 2 AND visit.visit_date = "'.$date_tomorrow.'" AND payments.payment_created = "'.$date_tomorrow.'"';
		$total_waiver = $this->reports_model->get_total_cash_collection($where4, 'payments, visit, patients, visit_type, payment_method', 'cash');

		$where4 = 'payments.payment_method_id = payment_method.payment_method_id AND payments.visit_id = visit.visit_id  AND visit.visit_delete = 0  AND visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND payments.cancel = 0 AND payments.payment_type = 2 AND visit.visit_date <> "'.$date_tomorrow.'" AND payments.payment_created = "'.$date_tomorrow.'"';
		$total_waiver_debt = $this->reports_model->get_total_cash_collection($where4, 'payments, visit, patients, visit_type, payment_method', 'cash');






		 // var_dump($total_other_collection+$total_mpesa_collection+$total_cash_collection); die();
		
		//count outpatient visits
		$where2 = $where1.' AND visit.inpatient = 0';

		(int)$outpatients = $this->reception_model->count_items($table, $where2);
		// var_dump($outpatients); die();
		//count inpatient visits
		$where2 = $where6.' AND visit.inpatient = 1';

		(int)$inpatients = $this->reception_model->count_items($table, $where2);


		$table1 = 'petty_cash,account';
		$where1 = 'petty_cash.account_id = account.account_id AND (account.account_name = "Cash Box" OR account.account_name = "Cash Collection") AND petty_cash.petty_cash_delete = 0';		
		
		$where1 .=' AND petty_cash.petty_cash_date = "'.$date_tomorrow.'"';
		$total_transfers = $this->reports_model->get_total_transfers($where1,$table1);



		$table1 = 'account_payments';
		$where1 = 'account_payments.account_to_id = (SELECT account_id FROM account WHERE account_name = "Equity Bank")AND account_payments.account_payment_deleted = 0 AND account_payments.account_from_id = (SELECT account_id FROM account WHERE account_name = "Cash Account")';			
		$where1 .=' AND account_payments.payment_date = "'.$date_tomorrow.'"';
		$select = 'SUM(account_payments.amount_paid) AS total_paid';
		$total_bank_transfers = $this->reports_model->get_total_account_transfers($where1,$table1,$select);


		$table5 = 'account_payments';
		$where5 = 'account_payments.account_to_id = (SELECT account_id FROM account WHERE account_name = "Petty Cash") AND account_payments.account_payment_deleted = 0 AND account_payments.account_from_id = (SELECT account_id FROM account WHERE account_name = "Cash Account")';			
		$where5.=' AND account_payments.payment_date = "'.$date_tomorrow.'"';
		$select5 = 'SUM(account_payments.amount_paid) AS total_paid';
		$total_petty_cash_transfers = $this->reports_model->get_total_account_transfers($where5,$table5,$select5);


		$table2 = 'visit';
		$where2 = 'visit.visit_delete = 0 AND rejected_amount > 0';			
		$where2 .=' AND visit.visit_date = "'.$date_tomorrow.'"';
		$select2 = 'SUM(visit.rejected_amount) AS total_paid';
		$total_rejected_amount = $this->reports_model->get_total_account_transfers($where2,$table2,$select2);

		$table3 = 'payments';
		$where3 = 'cancel = 1';			
		$where3 .=' AND cancelled_date = "'.$date_tomorrow.'"';
		$select3 = 'SUM(amount_paid) AS total_paid';
		$total_cancelled_amount = $this->reports_model->get_total_account_transfers($where3,$table3,$select3);


		$table4 = 'account_invoices';
		$where4 = 'account_invoices.account_from_id = (SELECT account_id FROM account WHERE account_name = "Petty Cash") AND account_invoices.account_invoice_deleted = 0';			
		$where4 .=' AND invoice_date = "'.$date_tomorrow.'"';
		$select4 = 'SUM(invoice_amount) AS total_paid';
		$total_petty_cash_usage = $this->reports_model->get_total_account_transfers($where4,$table4,$select4);




		$total_patients = $outpatients + $inpatients;

		$visit_types_rs = $this->reception_model->get_visit_types();
		$visit_results = '';
		$total_balance = 0;
		$total_invoices = 0;
		$total_payments = 0;
		$total_patients = 0;
		$total_cash_invoices = 0;
		$total_insurance_invoices = 0;

		$total_cash_payments = 0;
		$total_insurance_payments = 0;

		$total_cash_balance = 0;
		$total_insurance_balance = 0;

		if($visit_types_rs->num_rows() > 0)
		{
			foreach ($visit_types_rs->result() as $key => $value) {
				# code...

				$visit_type_name = $value->visit_type_name;
				$visit_type_id = $value->visit_type_id;


				$table = 'visit';
				$where = 'visit.visit_delete = 0 AND visit_type = '.$visit_type_id.' AND visit.visit_date = "'.$date_tomorrow.'"';
				$total_visit_type_patients = $this->reception_model->count_items($table,$where);

				// calculate invoiced amounts
				$report_response = $this->reports_model->get_visit_type_invoice_todays($visit_type_id,$date_tomorrow);

				$invoice_amount = $report_response['invoice_total'];
				$payments_value = $report_response['payments_value'];
				$balance = $report_response['balance'];

				if($visit_type_id == 1)
				{
					$total_cash_invoices += $invoice_amount;
					$total_cash_payments += $payments_value;
					$total_cash_balance += $balance;
				}
				else
				{
					$total_insurance_invoices += $invoice_amount;
					$total_insurance_payments += $payments_value;
					$total_insurance_balance += $balance;
				}	

				// calculate amounts paid
				if($total_visit_type_patients > 0)
				{
					$visit_results .='<tr>
								  		<td style="text-align:left;"> '.strtoupper($visit_type_name).'  </td>
								  		<td style="text-align:center;"> '.$total_visit_type_patients.'</td>
								  		<td style="text-align:center;"> '.number_format($invoice_amount,2).'</td>
								  		<td style="text-align:center;"> '.number_format($payments_value,2).'</td>
								  		<td style="text-align:center;"> '.number_format($balance,2).'</td>
								  	</tr>';
				}
				$total_patients = $total_patients + $total_visit_type_patients;
				$total_invoices = $total_invoices + $invoice_amount;
				$total_payments = $total_payments + $payments_value;
				$total_balance = $total_balance + $balance;


			}

			$visit_results .='<tr>
							  		<td style="text-align:left;" colspan="1"> TOTAL </td>
							  		<td style="text-align:center;border-top:2px solid #000;" > '.$total_patients.' </td>
							  		<td style="text-align:center;border-top:2px solid #000;">Ksh. '.number_format($total_invoices,2).'</td>
							  		<td style="text-align:center;border-top:2px solid #000;">Ksh. '.number_format($total_payments,2).'</td>
							  		<td style="text-align:center;border-top:2px solid #000;">Ksh.'.number_format($total_balance,2).'</td>
							  	</tr>';
		}


		$services_result = $this->reports_model->get_all_service_types();
		$service_result = '';
		$total_service_invoice = 0;
		$total_service_payment = 0;
		$total_service_balance = 0;
		if($services_result->num_rows() > 0)
		{
			$result = $services_result->result();
			$grand_total = 0;			
			foreach($result as $res)
			{
				$service_id = $res->service_id;
				$service_name = $res->service_name;
				$count++;
				
				//get service total
				$service_invoice = $this->reports_model->get_service_invoice_total($service_id,$date_tomorrow);
				$service_payment = $this->reports_model->get_service_payments_total($service_id,$date_tomorrow);
				$service_balance = abs($service_payment - $service_invoice);

				$total_service_invoice = $total_service_invoice + $service_invoice;
				$total_service_payment = $total_service_payment + $service_payment;
				$total_service_balance = $total_service_balance + $service_balance;
				
				$grand_total += $service_invoice;

				

				$service_result .='<tr>
							  		<td style="text-align:left;"> '.strtoupper($service_name).'  </td>
							  		<td style="text-align:center;"> '.number_format($service_invoice,2).'</td>
							  		<td style="text-align:center;"> '.number_format($service_payment,2).'</td>
							  		<td style="text-align:center;"> '.number_format($service_balance,2).'</td>
							  	</tr>';
				

			}

			$undefined_payment = $this->reports_model->get_payments_total(0,$date_tomorrow);
			$service_result .='<tr>
							  		<td style="text-align:left;" colspan="1"> WAIVER </td>
							  		<td style="text-align:center;"> ('.number_format($total_waiver,2).')</td>
							  		<td style="text-align:center;"></td>
							  		<td style="text-align:center;">('.number_format($total_waiver,2).')</td>
							  	</tr>';
			$service_result .='<tr>
							  		<td style="text-align:left;" colspan="2"> PAYMENTS</td>
							  		<td style="text-align:center;"> '.number_format($undefined_payment+$total_service_payment,2).'</td>
							  		<td style="text-align:center;"> ('.number_format($undefined_payment+$total_service_payment,2).')</td>
							  	</tr>';

			$service_result .='<tr>
							  		<td style="text-align:left;"> TOTAL </td>
							  		<td style="text-align:center;border-top:2px solid #000;">Ksh. '.number_format($total_service_invoice-$total_waiver,2).'</td>
							  		<td style="text-align:center;border-top:2px solid #000;">Ksh. '.number_format($total_service_payment+$undefined_payment,2).'</td>
							  		<td style="text-align:center;border-top:2px solid #000;">Ksh. '.number_format($total_service_balance - $undefined_payment - $total_service_payment -$total_waiver,2).'</td>
							  	</tr>';
		}
		$doctor_results = $this->reports_model->get_all_doctors();
		$counting =0;
		$date_from =$date_tomorrow;
		$date_to =$date_tomorrow;
		$results ='';
		if($doctor_results->num_rows() > 0)
		{
		$count = $full = $percentage = $daily = $hourly = 0;

			$results .=  
				'
					<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
					  <thead>
						<tr>
						  <th>#</th>
						  <th style="padding:5px;">DOCTOR</th>
						  <th style="padding:5px;">PATIENTS</th>
						  <th style="padding:5px;">INVOICE</th>
						  <th style="padding:5px;">WAIVERS </th>
						  <th style="padding:5px;">REVENUE </th>
						  <th style="padding:5px;">PAYMENTS </th>
						  <th style="padding:5px;">BALANCES </th>
						</tr>
					</thead>
					<tbody>
				';
			$result = $doctor_results->result();
			$grand_total = 0;
			$patients_total = 0;
			$total_charge_waivers = 0;
			$total_revenue = 0;
			$total_payments_made = 0;
			$total_balances = 0;
			foreach($result as $res)
			{
				$personnel_id = $res->personnel_id;
				$personnel_onames = $res->personnel_onames;
				$personnel_fname = $res->personnel_fname;
				$personnel_type_id = $res->personnel_type_id;
				
				
				//get service total
				$total = $this->reports_model->get_total_collected($personnel_id, $date_from, $date_to);
				$patients = $this->reports_model->get_total_patients($personnel_id, $date_from, $date_to);
				$waivers = $this->reports_model->get_total_waivers($personnel_id, $date_from, $date_to);
				$payments_made = $this->reports_model->get_total_payments_made($personnel_id, $date_from, $date_to);
				$revenue = $total - $waivers;
				$grand_total += $total;
				$patients_total += $patients;
				$total_revenue += $revenue;
				$total_charge_waivers += $waivers;
				$total_payments_made += $payments_made;

				$balance_charged = $revenue - $payments_made;

				$total_balances += $balance_charged;
				if($patients > 0)
				{
					$count++;
					$results.= '
						<tr>
							<td style="padding:5px;">'.$count.'</td>
							<td >DR. '.strtoupper($personnel_fname).' '.strtoupper($personnel_onames).'</td>
							<td style="text-align:center;padding:5px;">'.$patients.'</td>
							<td style="text-align:center;padding:5px;">'.number_format($total, 2).'</td>
							<td style="text-align:center;padding:5px;">'.number_format($waivers, 2).'</td>
							<td style="text-align:center;padding:5px;">'.number_format($revenue, 2).'</td>
							<td style="text-align:center;padding:5px;">('.number_format($payments_made, 2).')</td>
							<td style="text-align:center;padding:5px;">'.number_format($balance_charged, 2).'</td>
							
						</tr>
					';
				}
			}
			
			$results.= 
			'
				
					<tr>
						<td colspan="2">TOTAL</td>
						<td style="text-align:center;"><span class="bold" >'.$patients_total.'</span></td>
						<td  style="text-align:center;border-top:2px solid #000;"><span class="bold">'.number_format($grand_total, 2).'</span></td>
						<td  style="text-align:center;border-top:2px solid #000;"><span class="bold">'.number_format($total_charge_waivers, 2).'</span></td>
						<td  style="text-align:center;border-top:2px solid #000;"><span class="bold">'.number_format($total_revenue, 2).'</span></td>
						<td  style="text-align:center;border-top:2px solid #000;"><span class="bold">('.number_format($total_payments_made, 2).')</span></td>
						<td  style="text-align:center;border-top:2px solid #000;"><span class="bold">'.number_format($total_balances, 2).'</span></td>
					</tr>
				</tbody>
			</table>
			';
		}

echo '<p>Good morning to you,<br>
		Herein is a report of todays transactions. This is sent at '.date('H:i:s A').'
		</p>

		<h4 style="text-decoration:underline"><strong>CASH VS INSURANCE SUMMARY WORK DONE FOR TODAY </strong></h4>
		<table  class="table table-hover table-bordered ">
				<thead>
					<tr>
						<th style="padding:5px;">TYPE</th>
						<th style="padding:5px;">INVOICE AMOUNT (KES) </th>
						<th style="padding:5px;">PAYMENTS (KES) </th>
						<th style="padding:5px;">BALANCE (KES) </th>
					</tr>
				</thead>
				</tbody>
		  	<tr>
		  		<td>CASH  </td>
		  		<td style="text-align:center;"> '.number_format($total_cash_invoices,2).'</td>
		  		<td style="text-align:center;"> '.number_format($total_cash_payments,2).'</td>
		  		<td style="text-align:center;"> '.number_format($total_cash_balance,2).'</td>
		  	</tr>
		  	<tr>
		  		<td>INSURANCE </td>
		  		<td style="text-align:center;"> '.number_format($total_insurance_invoices,2).'</td>
		  		<td style="text-align:center;"> '.number_format($total_insurance_payments,2).'</td>
		  		<td style="text-align:center;"> '.number_format($total_insurance_balance,2).'</td>
		  	</tr>

		  	<tr>
		  		<td>TOTAL</td>
		  		<td style="text-align:center;border-top:2px solid #000;"> '.number_format($total_cash_invoices + $total_insurance_invoices,2).'</td>
		  		<td style="text-align:center;border-top:2px solid #000;"> '.number_format($total_cash_payments+$total_insurance_payments,2).'</td>
		  		<td style="text-align:center;border-top:2px solid #000;"> '.number_format($total_cash_balance+$total_insurance_balance,2).'</td>
		  	</tr>
		  	
		  	</tbody>

		</table>


		<h4 style="text-decoration:underline"><strong>COLLECTIONS SUMMARY TODAYS WORK VS DEBT REPAYMENT (PREVIOUS DAY\'S WORK) </strong></h4>
		<table  class="table table-hover table-bordered ">
				<thead>
					<tr>
						<th width="33%"></th>
						<th style="text-align:left;text-decoration:underline;">TODAYS PAYMENTS</th>
						<th style="text-align:left; text-decoration:underline;">DEBT REPAYMENT</th>
					</tr>
				</thead>
				</tbody>
		  	<tr>
		  		<td>CASH COLLECTED </td>
		  		<td  style="text-align:left;">KES. '.number_format($total_cash_collection,2).'</td>
		  		<td  style="text-align:left;">KES. '.number_format($total_cash_debt,2).'</td>
		  	</tr>
		  	<tr>
		  		<td>MPESA COLLECTED </td>
		  		<td  style="text-align:left;">KES. '.number_format($total_mpesa_collection,2).'</td>
		  		<td  style="text-align:left;">KES. '.number_format($total_mpesa_debt,2).'</td>
		  	</tr>
		  	<tr>
		  		<td>OTHER COLLECTION</td>
		  		<td  style="text-align:left;">KES. '.number_format($total_other_collection,2).'</td>
		  		<td  style="text-align:left;">KES. '.number_format($total_other_debt,2).'</td>
		  	</tr>
		  	
		  	<tr>
		  		<td>CASH - PETTY CASH TRANSFER </td>
		  		<td  style="text-align:left;"> ( KES. '.number_format($total_petty_cash_transfers,2).' )</td>
		  		<td  style="text-align:left;">KES. '.number_format(0,2).'</td>
		  	</tr>
		  	<tr>
		  		<td><strong>REVENUE</strong> </td>
		  		<td  style="text-align:left;"><strong> KES. '.number_format(($total_mpesa_collection + $total_cash_collection + $total_other_collection) - $total_transfers,2).' </strong></td>
		  		<td  style="text-align:left;"><strong> KES. '.number_format(($total_mpesa_debt + $total_cash_debt + $total_other_debt),2).' </strong> </td>
		  	</tr>
		  	<tr>
		  		<td><strong>WAIVERS</strong> </td>
		  		<td  style="text-align:left;"> KES. '.number_format($total_waiver,2).'</td>
		  		<td  style="text-align:left;">KES. '.number_format($total_waiver_debt,2).'</td>
		  	</tr>
		  	</tbody>

		</table>

		<h4 style="text-decoration:underline"><strong>ACCOUNTS UPDATES</strong></h4>
		
		<table  class="table table-hover table-bordered ">
				<thead>
					<tr>
						<th width="50%"></th>
						<th width="50%"></th>
					</tr>
				</thead>
				</tbody>
		  	<tr>
		  		<td>REJECTED INVOICES</td><td>KES. '.number_format($total_rejected_amount,2).'</td>
		  	</tr>
		  	<tr>
		  		<td>CANCELLED PAYMENTS</td><td> KES. '.number_format($total_cancelled_amount,2).'</td>
		  	</tr>
		  	<tr>
		  		<td>CASH - PETTY CASH TRANSFER</td><td> KES. '.number_format($total_petty_cash_transfers,2).'</td>
		  	</tr>
		  	<tr>
		  		<td>PETTY CASH USAGE</td><td> KES. '.number_format($total_petty_cash_usage,2).'</td>
		  	</tr>
		  	
		  	<tr>
		  		<td>CASH - BANK TRANSFER </td><td>  KES. '.number_format($total_bank_transfers,2).' </td>
		  	</tr>
		
		  	</tbody>

		</table>


		<h4 style="text-decoration:underline"><strong>VISIT SUMMARY</strong></h4>
		<table  class="table table-hover table-bordered ">
			<thead>
				<tr>
					<th style="padding:5px;">PATIENT TYPE</th>
					<th style="padding:5px;">NO</th>
					<th style="padding:5px;">AMOUNT INVOICED</th>
					<th style="padding:5px;">AMOUNT COLLECTED</th>
					<th style="padding:5px;">DEBT</th>
				</tr>
			</thead>
			</tbody> 
			  	'.$visit_results.'
		  	</tbody>
		</table>


		<h4 style="text-decoration:underline"><strong>DENTIST\'S SUMMARY</strong></h4>
		'.$results.'


		';
?>