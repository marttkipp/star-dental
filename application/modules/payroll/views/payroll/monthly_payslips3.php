<?php
$personnel_id = $this->session->userdata('personnel_id');
$prepared_by = $this->session->userdata('first_name');
$roll = $payroll->row();
$year = $roll->payroll_year;
$month = $roll->month_id;
$totals = array();

$result = '';
//$total_rows = $query->num_rows();
if ($total_rows > 0)
{
	//echo $current_row; die();
	$count = $current_row;
	$result = '';
	
	$total_payments = 0;
	$total_savings = 0;
	$total_loans = 0;
	$total_net = 0;
	$payroll_check  = 0;
	$all_loans_total = array();
	$benefits_amount = $payroll_data->benefits;
	$total_benefits2 = $payroll_data->total_benefits;
	$payments_amount = $payroll_data->payments;
	$total_payments2 = $payroll_data->total_payments;
	$allowances_amount = $payroll_data->allowances;
	$total_allowances2 = $payroll_data->total_allowances;
	$deductions_amount = $payroll_data->deductions;
	$total_deductions2 = $payroll_data->total_deductions;
	$other_deductions_amount2 = $payroll_data->other_deductions;
	$total_other_deductions2 = $payroll_data->total_other_deductions;
	$nssf_amount = $payroll_data->nssf;
	$nhif_amount = $payroll_data->nhif;
	$life_ins_amount = $payroll_data->life_ins;
	$paye_amount = $payroll_data->paye;
	$monthly_relief_amount = $payroll_data->monthly_relief;
	$insurance_relief_amount = $payroll_data->insurance_relief;
	$insurance_amount_amount = $payroll_data->insurance;
	$scheme = $payroll_data->scheme;
	$scheme_borrowed = $payroll_data->scheme_borrowed;
	$scheme_payments = $payroll_data->scheme_payments;
	$remaining_balance = $payroll_data->remaining_balance;//var_dump($remaining_balance);die();
	$scheme_start_date = $payroll_data->scheme_start_date;
	$scheme_end_date = $payroll_data->scheme_end_date;
	$total_scheme = $payroll_data->total_scheme;
	$savings = $payroll_data->savings;
	$total_overtime2 = $payroll_data->total_overtime;
	$overtime_amount = $payroll_data->overtime;
	$overtime_rate = $payroll_data->overtime_rate;
	$overtime_type = $payroll_data->overtime_type;
	$overtime_hours = $payroll_data->overtime_hours;
	
	foreach ($query->result() as $row)
	{
		$personnel_id = $row->personnel_id;
		$personnel_number = $row->personnel_number;
		$personnel_fname = $row->personnel_fname;
		$personnel_onames = $row->personnel_onames;
		$nhif_number =$row->personnel_nhif_number;
		$nssf_number = $row->personnel_nssf_number;
		$kra_pin = $row->personnel_kra_pin;
		$gross = $payroll_check = 0;
		$page_break = '';
		
		//check if personnel has basic pay
		$payment_id = 1;
		if(isset($total_payments2->$payment_id))
		{
			$total_payment_amount[$payment_id] = $total_payments2->$payment_id;
		}
		if($total_payment_amount[$payment_id] != 0)
		{
			if(isset($payments_amount->$personnel_id->$payment_id))
			{
				$payroll_check = $payments_amount->$personnel_id->$payment_id;
			}
		}
		//echo $total_rows;die();
		$payroll_check = 1;
		//display only if personnel has basic pay
		
		if(($total_rows == 1))
		//if(($total_rows == 1) || (($total_rows%2) != 0))
		{
			$one_page = 'style="width:50%;"';
		}
		
		else
		{
			$one_page = '';
		}
		if($payroll_check > 0)
		{
			//echo $total_rows; die();
			if($total_rows != 1)
			{
				$count++;
			}
			$pb = '';
			if(($count > 1) && (($count % 3) == 0))
			{
				$pb = 'page-break';
			}
			$result .= '<div class="col-xs-6" '.$one_page.' id="'.$pb.'">';
			//for single payslips
			//if(($total_rows == 1) || ($count == $total_rows))
			if($total_rows == 1)
			{
				//echo $result; die();
				
			}
			
			else
			{
				//var_dump($result); die();
				$result .= '
					
					<table class="table" style="width:100%;">
						<tr>
							<td style="padding:0 10px 0 10px;">';
			}
				$result .= '
						<table class="table receipt_bottom_border" style = "center-align">
							<tr>
								<td>
									<table class="table table-condensed">
										<tr>
											<td><strong>'.strtoupper(strtolower($contacts['company_name'])).'.</strong></td>
										</tr>
										<tr>
											<td>'.$personnel_fname.' '.$personnel_onames.'</td>
										</tr>
										<tr>
											<td>STAFF NUMBER. '.$personnel_number.'</td>
										</tr>
										<tr>
											<td>NSSF NO. '.$nssf_number.'</td>
										</tr>
										<tr>
											<td>NHIF NO. '.$nhif_number.'</td>
										</tr>
										<tr>
											<td>KRA PIN: '.$kra_pin.'</td>
										</tr>
										<tr>
											<td>'.date('M Y',strtotime($year.'-'.$month)).'</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						';
						$result .= 
						'<table class="table table_condensed">
							<tr>
								<th>EARNINGS</th>
							</tr>
						';
						$total_payments = 0;
						$payment_amt = 0;					
						
						if($payments->num_rows() > 0)
						{
							foreach($payments->result() as $res)
							{
								$payment_abbr = $res->payment_name;
								$payment_id = $res->payment_id;
								$table_id = $payment_id;
								$total_payment_amount[$payment_id] = 0;

								if(isset($total_payments2->$payment_id))
								{
									$total_payment_amount[$payment_id] = $total_payments2->$payment_id;
								}
								if($total_payment_amount[$payment_id] != 0)
								{
									if(isset($payments_amount->$personnel_id->$table_id))
									{
										$payment_amt = $payments_amount->$personnel_id->$table_id;
										$gross += $payment_amt;
										$payroll_check = $payment_amt;
									}
									if(!isset($total_personnel_payments[$payment_id]))
									{
										$total_personnel_payments[$payment_id] = 0;
									}
								
									if($payroll_check != 0)
									{
									 $result.='
										<tr>
											<td class = "left-align">
												'.strtoupper ($payment_abbr).'
											</td>
											<td class="right-align">
												'.number_format($payment_amt, 2).'
											</td>
										</tr>';
									}
								}
							}
						}
						
						$total_allowances = 0;				
						$allowance_amt = 0;
						//allowances
						
						if($allowances->num_rows() > 0)
						{
							foreach($allowances->result() as $res)
							{
								$allowance_abbr = $res->allowance_name;
								$allowance_id = $res->allowance_id;
								$table_id = $allowance_id;
								$total_allowance_amount[$allowance_id] = 0;

								if(isset($total_allowances2->$allowance_id))
								{
									$total_allowance_amount[$allowance_id] = $total_allowances2->$allowance_id;
								}
								if($total_allowance_amount[$allowance_id] != 0)
								{
									if(isset($allowances_amount->$personnel_id->$table_id))
									{
										$allowance_amt = $allowances_amount->$personnel_id->$table_id;
										$gross += $allowance_amt;
									}
									if(!isset($total_personnel_allowances[$allowance_id]))
									{
										$total_personnel_allowances[$allowance_id] = 0;
									}
									if($allowance_amt != 0)
									{
										 $result.='
											<tr>
												<td class = "left-align">
													'.strtoupper ($allowance_abbr).'
												</td>
												<td class="right-align">
													'.number_format($allowance_amt, 2).'
												</td>
											</tr>';
									}
								}
							}
						}
						
						$total_overtime = 0;				
						$overtime_amt = $ot_rate = $ot_type = $ot_hours = 0;
						//overtime
						if($overtime->num_rows() > 0)
						{
							foreach($overtime->result() as $res)
							{
								$overtime_name = $res->overtime_name;
								$overtime_id = $res->overtime_type;
								$table_id = $overtime_id;
								$total_overtime_amount[$overtime_id] = 0;

								if(isset($total_overtime2->$overtime_id))
								{
									$total_overtime_amount[$overtime_id] = $total_overtime2->$overtime_id;
								}
								if($total_overtime_amount[$overtime_id] != 0)
								{
									//overtime amount
									if(isset($overtime_amount->$personnel_id->$table_id))
									{
										$overtime_amt = $overtime_amount->$personnel_id->$table_id;
										$gross += $overtime_amt;
										$payroll_check = $overtime_amt;
									}
									
									//overtime rate
									if(isset($overtime_rate->$personnel_id->$table_id))
									{
										$ot_rate = $overtime_rate->$personnel_id->$table_id;
									}
									
									//overtime type
									if(isset($overtime_type->$personnel_id->$table_id))
									{
										$ot_type = $overtime_type->$personnel_id->$table_id;
									}
									
									//overtime hours
									if(isset($overtime_hours->$personnel_id->$table_id))
									{
										$ot_hours = $overtime_hours->$personnel_id->$table_id;
									}
									
									if(!isset($total_personnel_overtime[$overtime_id]))
									{
										$total_personnel_overtime[$overtime_id] = 0;
									}
									if($overtime_amt > 0)
									{
										if($ot_hours > 0)
										{
											 $result.='
												<tr>
													<td class = "left-align">
														'.strtoupper ($overtime_name).' - '.$ot_hours.' hours
													</td>
													<td class="right-align">
														'.number_format($overtime_amt, 2).'
													</td>
												</tr>';
										}
										else
										{
											 $result.='
												<tr>
													<td class = "left-align">
														'.strtoupper ($overtime_name).'
													</td>
													<td class="right-align">
														'.number_format($overtime_amt, 2).'
													</td>
												</tr>';
										}
									}
								}
							}
						}
						$result .='</table>';
						$result .= ' <table class="table table-condensed">
						<tr>
							<td class="left-align">
								<th>TOTAL EARNINGS</th>
							</td>
							<td class="right-align">
								'.number_format(($gross), 2).'
							</td>
						</tr>';
						$result .='</table>';
						
						$total_benefits = 0;
						$benefit_amt = 0;
						if($benefits->num_rows() > 0)
						{
						
							$result .='<table class="table table-condensed">';
				
							$result .='
							<tr>
								<th>NON CASH BENEFITS</th>
							</tr>
							';
							foreach($benefits->result() as $res)
							{
								$benefit_id = $res->benefit_id;
								$benefit_name = $res->benefit_name;
								$table_id = $benefit_id;
								$total_benefit_amount[$benefit_id] = 0;

								if(isset($total_benefits2->$benefit_id))
								{
									$total_benefit_amount[$benefit_id] = $total_benefits2->$benefit_id;
								}
								if($total_benefit_amount[$benefit_id] != 0)
								{
																		
									$benefit_amt = 0;
									if(isset($benefits_amount->$personnel_id->$table_id))
									{
										$benefit_amt = $benefits_amount->$personnel_id->$table_id;
									
									}
									if(!isset($total_personnel_benefits[$benefit_id]))
									{
										$total_personnel_benefits[$benefit_id] = 0;
									}
									if($payroll_check != 0)
									{
										if($benefit_amt > 0)
										{
										$result.='
										<tr>
											<td class = "left-align">
												'.strtoupper ($benefit_name).'
											</td>
											<td class="right-align">
												'.number_format($benefit_amt, 2).'
											</td>
										</tr>';
										}
									}
								}
							}
							
							$result .='</table>';
						}
							
						/*********** Taxable ***********/
						$gross_taxable = $gross += $benefit_amt;
						$nssf = 0;
						$taxable = 0;
						$paye = 0;
						$paye_less_relief = 0;
						$monthly_relief = 0;
						$insurance_relief = 0;
						$insurance_amount = 0;
						$total_gross = 0;
						$total_paye = 0;
						$total_nssf = 0;
						$total_nhif = 0;
						$total_life_ins = 0;
						
						//nssf
						$nssf = $nssf_amount->$personnel_id;
						$total_nssf += $nssf;
						
						//nhif
						$nhif = $nhif_amount->$personnel_id;
						$total_nhif += $nhif;
						
						//paye
						$paye =$paye_amount->$personnel_id;
						
						//relief
						$relief = $monthly_relief_amount->$personnel_id;
						
						//insurance_relief
						$insurance_relief = $insurance_relief_amount->$personnel_id;
						
						//relief
						$insurance_amount = $insurance_amount_amount->$personnel_id;
						//echo $insurance_relief;
						$paye_less_relief -= ($relief + $insurance_relief);
										
						if($paye < 0)
						{
							$paye = 0;
						}
						if($gross <=0)
						{
							$relief = 0;
						}
					
						$total_paye += $paye;
						$total_life_ins += $insurance_amount;
						
						$result .= ' <table class="table table-condensed">
						<tr>
							<th>P.A.Y.E</th>
						</tr>';
						$result .='
						<tr>
							<td class="left-align">
								LIABLE PAY
							</td>
							<td class="right-align">
								'.number_format(($gross_taxable), 2).'
							</td>
						</tr>';
											
						$result .='
									<tr>
									<td class="left-align">
									LESS PENSIONS/NSSF
									</td>
									<td class="right-align">
										'.number_format($total_nssf, 2).'
									</td>
								</tr>';
						$taxable = $gross_taxable - $total_nssf;
						$result .= ' 
									<tr>
									<td class="left-align">
										CHARGEABLE AMT KSHS
									</td>
									<td class="right-align">
										'.number_format($taxable, 2).'
									</td>
								</tr>';
						$result .= ' 
							<tr>
							<td class="left-align">
								TAX CHARGED
							</td>
							<td class="right-align">
								'.number_format($paye, 2).'
							</td>
						</tr>';
						$result .= ' 
							<tr>
							<td class="left-align">
								PERSONAL RELIEF
							</td>
							<td class="right-align">
								'.number_format($relief, 2).'
							</td>
						</tr>';
						
						if($insurance_relief > 0){
							$result .='
								<tr>
									<td class="left-align">
										INSURANCE RELIEF
									</td>
									<td class="right-align">
										'.number_format($insurance_relief, 2).'
									</td>
								</tr>';
						 }
								
						$result .='</table>';
				
						$result .= ' <table class="table table-condensed">
						<tr>
							<th>DEDUCTIONS</th>
						</tr>';
						if($insurance_amount > 0){
							$result .='
										
								<!--<tr>
									<td class="left-align">
										Life Insurance
									</td>
									<td class="right-align">
										'.number_format($insurance_amount, 2).'
									</td>
								</tr>-->';
						}
						$paye_less_relief = $paye- ($relief + $insurance_relief);
						if($paye_less_relief < 0){
							$paye_less_relief = 0;
						}
						$result .= '
								<tr>
									<td class="left-align">
										PAYE
									</td>
									<td class="right-align">
										'.number_format($paye_less_relief, 2).'
									</td>
								</tr>';
					
						$result .=
						'
						<tr>
							<td class="left-align">
								NSSF
							</td>
							<td class="right-align">
								'.number_format($total_nssf, 2).'
							</td>
						</tr>';
		   
						$result .='
							<tr>
								<td class="left-align">
									NHIF
								</td>
								<td class="right-align">
									'.number_format($total_nhif, 2).'
								</td>
							</tr>';
							
						/*********** Deductions ***********/
						$total_deductions = 0;
						//deductions
						$total_deductions = 0;
						if($deductions->num_rows() > 0)
						{
							foreach($deductions->result() as $res)
							{
								$deduction_id = $res->deduction_id;
								$deduction_name = $res->deduction_name;
								
								$table_id = $deduction_id;
								$total_deduction_amount[$deduction_id] = 0;

								if(isset($total_deductions2->$deduction_id))
								{
									$deduction_amt = 0;
									if(isset($deductions_amount->$personnel_id->$table_id))
									{
										$deduction_amt = $deductions_amount->$personnel_id->$table_id;
									}
									$total_deductions += $deduction_amt;
									if(!isset($total_personnel_deductions[$deduction_id]))
									{
										$total_personnel_deductions[$deduction_id] = 0;
									}
									if($deduction_amt > 0)
									{
										$result .='
												 <tr>
												<td class="left-align">
													'.strtoupper ($deduction_name).'
												</td>
												<td class="right-align">
													'.number_format($deduction_amt, 2).'
												</td>
											</tr>';
									}
								}
							}
						}						
								
						/*********** Other deductions ***********/
						$total_other_deductions = 0;
						//other_deductions
						$total_other_deductions = 0;
						if($other_deductions->num_rows() > 0)
						{
							foreach($other_deductions->result() as $res)
							{
								$other_deduction_id = $res->other_deduction_id;
								$other_deduction_name = $res->other_deduction_name;
								
								$table_id = $other_deduction_id;
								
								$total_other_deduction_amount[$other_deduction_id] = 0;
								
								if(isset($total_other_deductions2->$other_deduction_id))
								{
									$other_deduction_amt = 0;
									if(isset($other_deductions_amount2->$personnel_id->$table_id))
									{
										$other_deduction_amt = $other_deductions_amount2->$personnel_id->$table_id;
									}
									$total_other_deductions += $other_deduction_amt;
									if(!isset($total_personnel_other_deductions[$other_deduction_id]))
									{
										$total_personnel_other_deductions[$other_deduction_id] = 0;
									}	
									if($other_deduction_amt > 0)
									{
									$result .='<tr>
												<td class="left-align">
													'.strtoupper ($other_deduction_name).'
												</td>
												<td class="right-align">
													'.number_format($other_deduction_amt, 2).'
												</td>
											</tr>';
									}
								}
							}
						}	
						$result .='</table>';

						$result .= ' <table class="table table-condensed">';
						/*********** Other deductions ***********/
					
						//other_deductions
						$total_savings = 0;
						if($savings_rs->num_rows() > 0)
						{
							$result .= '
							<tr>
								<th>SAVINGS</th>
							</tr>';
							foreach($savings_rs->result() as $res)
							{
								$savings_id = $res->savings_id;
								$savings_name = $res->savings_name;
								
								$table_id = $savings_id;
								
								$total_savings_amount[$savings_id] = 0;
								
								if(isset($savings->$savings_id))
								{
									$savings_amt = 0;
									if(isset($savings->$personnel_id->$table_id))
									{
										$savings_amt = $savings->$personnel_id->$table_id;
									}
									$total_savings += $savings_amt;
									if(!isset($total_personnel_savings[$savings_id]))
									{
										$total_personnel_savings[$savings_id] = 0;
									}	
									if($savings_amt > 0)
									{
									$result .='<tr>
												<td class="left-align">
													'.strtoupper ($savings_name).'
												</td>
												<td class="right-align">
													'.number_format($savings_amt, 2).'
												</td>
											</tr>';
									}
								}
							}
						}	
						$result .='</table>';


				
						$result .= ' <table class="table table-condensed">';
						
						$total_loan_schemes = 0;
						//get loan schemes
						$date = date("Y-m-d");
						$total_schemes = 0;
						$interest = 0;
						$monthly = 0;
						$interest = 0;
						$interest2 = 0;
						$sdate = '';
						$edate = '';
						$today = date("y-m-d");
						$prev_payments = "";
						$prev_interest = "";
						$loan_output = "";
					
						if(($rs_schemes->num_rows() > 0) && ($total_scheme > 0))
						{
							$result .= '
							<tr>
								<th>LOANS</th>
							</tr>';
							foreach($rs_schemes->result() as $res)
							{
								$loan_scheme_name = $res->loan_scheme_name;
								$loan_scheme_name = $res->loan_scheme_name;
								$loan_scheme_id = $res->loan_scheme_id;
								$table_id = $loan_scheme_id;
								if(isset($total_scheme->$loan_scheme_id))
								{
									$total_loan_scheme_amount[$loan_scheme_id] = $total_scheme->$loan_scheme_id;
								}
								
								if($total_loan_scheme_amount[$loan_scheme_id] != 0)
								{
									//repayment
									$loan_scheme_amt = 0;
									if(isset($scheme->$personnel_id->$table_id))
									{

										$loan_scheme_amt = $scheme->$personnel_id->$table_id;
									}
									$total_schemes += $loan_scheme_amt;
									//borrowed
									$loan_scheme_borrowed = $loan_scheme_remaining_balance = 0;
									if(isset($scheme_borrowed->$personnel_id->$table_id))
									{
										$loan_scheme_borrowed = $scheme_borrowed->$personnel_id->$table_id;
									}
									//remaining balance
									if(isset($remaining_balance->$personnel_id->$table_id))
									{
										$loan_scheme_remaining_balance = $remaining_balance->$personnel_id->$table_id;
									}
									//payments
									$loan_scheme_payments = 0;
									if(isset($scheme_payments->$personnel_id->$table_id))
									{
										$loan_scheme_payments = $scheme_payments->$personnel_id->$table_id;
									}
									//balance
									//$loan_scheme_balance = $loan_scheme_borrowed - $loan_scheme_amt - $loan_scheme_payments;
									$total_scheme_repaid = $this->payroll_model->get_total_loan_scheme_paid($personnel_id, $loan_scheme_id,$payroll_created_for);
									if($personnel_id == 8194)
									{
										//var_dump($total_scheme_repaid); die();
									}
									if($loan_scheme_borrowed > 0)
									{
										$loan_scheme_balance = $loan_scheme_borrowed - $total_scheme_repaid;
									}
									else
									{
										$loan_scheme_balance = $loan_scheme_remaining_balance - $total_scheme_repaid;
									}
									//echo $loan_scheme_remaining_balance.'-'.$loan_scheme_borrowed;die();
									if($loan_scheme_amt > 0)
									{
										$result .= '
										<!--<tr>
											<td>'.$loan_scheme_name.' - Borrowed</td>
											<td class="right-align">'.number_format($loan_scheme_borrowed, 2).'</td>
										</tr>
										<tr>
											<td>'.$loan_scheme_name.' - Monthly Payments</td>
											<td class="right-align">'.number_format($loan_scheme_amt, 2).'</td>
										</tr>
										<tr>
											<td>'.$loan_scheme_name.' - Total Payments</td>
											<td class="right-align">'.number_format($loan_scheme_payments, 2).'</td>
										</tr>
										<tr>
											<td>'.$loan_scheme_name.' - Balance</td>
											
											<td class="right-align">'.number_format($loan_scheme_balance, 2).'</td>
										</tr>-->
										<tr>
											<td>'.$loan_scheme_name.' (Bal '.number_format($loan_scheme_balance, 2).')</td>
											<td class="right-align">'.number_format($loan_scheme_amt, 2).'</td>
										</tr>
										';
									}
								}
							}
						}
						
						/*if($total_schemes > 0)
						{
							$result .= '
							<tr>
								<th>LOANS</th>
							</tr>
							'.$loan_output;
						}*/
						
						if($paye > $relief)
						{
							$paye = $paye - ($relief + $insurance_relief);
						}
						else
						{
							$paye = 0;
						}
						$all_deductions = $paye + $total_nssf + $total_nhif + $total_deductions + $total_other_deductions + $total_schemes + $total_savings;
						
						$net_pay = $gross - $all_deductions;											
						$result .='
							<tr>
								<td class="left-align">
									TOTAL DEDUCTIONS
								</td>
								<td class="right-align">
									'.number_format($all_deductions, 2).'
								</td>
							</tr>';											
					   
						$result .='</table>';
						
						$result .= ' <table class="table table-condensed">
							';
						 $result .= '
						<tr>
							<td class="left-align">
								<th>Net Pay</th>
							</td>
							<td class="right-align">
								'.number_format(($net_pay), 2).'
							</td>
						</tr>
						</table>';
						$result .= '
							<table class="table table-condensed">
								<tr>
									<th style = "font-size:16px;"><u>Memorandum Information</u></th>
								</tr>
								<tr>
									<td class="left-align" style="font-size:15px;">Signature:</td>
									<td></td>
								</tr>
								<tr>
									<td class="left-align" style="font-size:15px;">Date:</td>
									<td></td>
								</tr>
							</table>';	
			
			//for single payslips			
			if(($total_rows == 1) || ($count == $total_rows))
			{
				
			}
			
			else
			{
				$result .= '
							</td>
						</tr>
					</table>
					';
					//$result.='<div id="page-break">'.date('Y-m-d').'</div>';
			}
			$result .= '</div>';
		}
		
	}
}
echo $result;