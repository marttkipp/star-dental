<?php
require_once "./application/libraries/mpdf/Mpdf.php";
ini_set('MAX_EXECUTION_TIME', -1);
ini_set('max_execution_time',0);
set_time_limit(0);

class Payroll_model extends CI_Model 
{	
	public function payments_view($personnel_id)
	{
		$result = $this->payroll_model->get_personnel_payments($personnel_id);
		
		$total = 0;
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $row2)
			{
				$total = $total + $row2->amount;
			}
		}
		
		return $total;
	}
	
	public function benefits_view($personnel_id)
	{
		$result = $this->payroll_model->get_personnel_benefits($personnel_id);
		
		$total = 0;
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $row2)
			{
				$taxable = $row2->taxable;
				
				if($taxable == 1)
				{
					$total = $total + $row2->amount;
				}
			}
		}
		
		return $total;
	}
	
	public function allowances_view($personnel_id)
	{
		$result = $this->payroll_model->get_personnel_allowances($personnel_id);
		
		$total = 0;
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $row2)
			{
				$taxable = $row2->taxable;
				
				if($taxable == 1)
				{
					$total = $total + $row2->amount;
				}
			}
		}
		
		return $total;
	}
	
	public function deductions_view($personnel_id)
	{
		$result = $this->payroll_model->get_personnel_deductions($personnel_id);
		
		$total = 0;
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $row2)
			{
				$taxable = $row2->taxable;
				
				if($taxable == 1)
				{
					$total = $total + $row2->amount;
				}
			}
		}
		
		return $total;
	}
	
	public function other_deductions_view($personnel_id)
	{
		$result = $this->payroll_model->get_personnel_other_deductions($personnel_id);
		
		$total = 0;
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $row2)
			{
				$taxable = $row2->taxable;
				
				if($taxable == 1)
				{
					$total = $total + $row2->amount;
				}
			}
		}
		
		return $total;
	}
	
	public function savings_view($personnel_id)
	{
		$result = $this->payroll_model->get_personnel_savings($personnel_id);
		
		$total_savings = 0;
											
		if($result->num_rows() > 0)
		{
			foreach($result->result() as $allow)
			{
				$amount = $allow->amount;
				$total_savings += $amount;
			}
		}
		
		return $total_savings;
	}
	
	public function scheme_view($personnel_id)
	{
		$result = $this->payroll_model->get_personnel_scheme($personnel_id);
		
		$total_loan_schemes = 0;
                                
		if($result->num_rows() > 0)
		{
			foreach($result->result() as $open)
			{
				$amount = $open->amount;
					
				if($amount > 0)
				{
					$monthly = $open->monthly;
					$total_loan_schemes += $monthly;
				}
			}
		}
		
		
		return $total_loan_schemes;
	}
	
	public function nssf_view($gross)
	{
		$nssf = 0;
		if($gross > 0)
		{
			$nssf_query = $this->payroll_model->get_nssf();
			
			if($nssf_query->num_rows() > 0)
			{
				foreach ($nssf_query->result() as $row2)
				{
					$nssf_id = $row2->nssf_id;
					$nssf = $row2->amount;
						
					$nssf_percentage = $row2->percentage;
					
					if($nssf_percentage == 1)
					{
						$nssf_deduction_amount = $gross;
						
						if($nssf_deduction_amount > 18000)
						{
							$nssf_deduction_amount = 18000;
						}
						$nssf = $nssf_deduction_amount * ($nssf/100);
					}
				}
			}
		}
		
		return $nssf;
	}
	
	public function nhif_view($gross)
	{
		$nhif = 0;
		if($gross > 0)
		{
			$nhif_query = $this->payroll_model->calculate_nhif($gross);
			
			if($nhif_query->num_rows() > 0)
			{
				foreach ($nhif_query->result() as $row2)
				{
					$nhif = $row2->amount;
				}
			}
		}
		
		return $nhif;
	}
	
	function calculate_paye($taxable)
	{
		$tax = 0;
		$total_tax = 0;
		
		if($taxable > 0)
		{	
			//get tax rates
			$paye_query = $this->payroll_model->get_paye();
			$count = 0;
			$total_tax = 0;
			$current_amount = $taxable;
			
			if($paye_query->num_rows() > 0)
			{
				foreach ($paye_query->result() as $row2)
				{
					$count++;
					$paye_id = $row2->paye_id;
					$paye_from = $row2->paye_from;
					$paye_to = $row2->paye_to;
					$paye_amount = $row2->paye_amount;
					
					//for people earning more than $paye_from
					//if(($current_amount > $paye_to) && ($paye_to > 0))
					if($paye_to != 0)
					{
						$section_difference = ($paye_to - $paye_from);
						if($current_amount >= $section_difference)
						{
							$tax = (($paye_amount / 100) * ($section_difference));
							//echo $paye_amount.' - '.$tax.' - '.$section_difference.' - '.$current_amount.'<br/>';
							$current_amount -= $section_difference;
							$total_tax += $tax;
						}
						
						else
						{
							$tax = (($paye_amount / 100) * ($current_amount));
							//echo $paye_amount.' - '.$tax.' - '.$current_amount.'<br/>';
							$total_tax += $tax;
							break;
						}
					}
					
					//people earning less than $paye_from
					else
					{
						$tax = ($paye_amount / 100) * $current_amount;
						//echo $paye_amount.' - '.$tax.' - '.$current_amount.'<br/>';
						$total_tax += $tax;
						break;
					}
				}
			}
		}
		
		return round($total_tax);
	}
	
	public function get_overtime()
	{
		$this->db->select('*');
		$result = $this->db->get('overtime');
		return $result;
	}
	
	public function get_all_allowances()
	{
		$table = "allowance";
		$items = "*";
		$order = "allowance_name";
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_all_deductions()
	{
		$table = "deduction";
		$items = "*";
		$order = "deduction_name";
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_all_other_deductions()
	{
		$table = "other_deduction";
		$items = "*";
		$order = "other_deduction_name";
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_relief()
	{
		$table = "relief";
		$items = "*";
		$order = "relief_name";
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_all_savings()
	{
		$table = "savings";
		$items = "*";
		$order = "savings_name";
		$where = "savings_status = 0";
		
		$this->db->select($items);
		$this->db->where($where);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_all_loan_schemes()
	{
		$table = "loan_scheme";
		$items = "*";
		$order = "loan_scheme_name";
		$where = "loan_scheme_status = 0";
		
		$this->db->select($items);
		$this->db->where($where);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_personnel_details($personnel_id)
	{
		$table = "personnel, branch";
		$where = "personnel.branch_id = branch.branch_id AND personnel.personnel_id = ".$personnel_id;
		$items = "*";
		$order = "personnel_id";
		
		$this->db->select($items);
		$this->db->where($where);
		$result = $this->db->get($table);
		return $result;
	}
	
	
	function dateDiff($time1, $time2, $interval) 
	{
		// If not numeric then convert texts to unix timestamps
		if (!is_int($time1)) {
		  $time1 = strtotime($time1);
		}
		if (!is_int($time2)) {
		  $time2 = strtotime($time2);
		}
		
		// If time1 is bigger than time2
		// Then swap time1 and time2
		if ($time1 > $time2) {
		  $ttime = $time1;
		  $time1 = $time2;
		  $time2 = $ttime;
		}
		
		// Set up intervals and diffs arrays
		$intervals = array('year','month','day','hour','minute','second');
		if (!in_array($interval, $intervals)) {
		  return false;
		}
		
		$diff = 0;
		// Create temp time from time1 and interval
		$ttime = strtotime("+1 " . $interval, $time1);
		// Loop until temp time is smaller than time2
		while ($time2 >= $ttime) {
		  $time1 = $ttime;
		  $diff++;
		  // Create new temp time from time1 and interval
		  $ttime = strtotime("+1 " . $interval, $time1);
		}
		
		return $diff;
 	}
	
	public function month_calc($month)
	{
		if($month == "Jan"){
			$month = 1;
		}
		else if($month == "Feb"){
			$month = 2;
		}
		else if($month == "Mar"){
			$month = 3;
		}
		else if($month == "Apr"){
			$month = 4;
		}
		else if($month == "May"){
			$month = 5;
		}
		else if($month == "Jun"){
			$month = 6;
		}
		else if($month == "Jul"){
			$month = 7;
		}
		else if($month == "Aug"){
			$month = 8;
		}
		else if($month == "Sep"){
			$month = 9;
		}
		else if($month == "Oct"){
			$month = 10;
		}
		else if($month == "Nov"){
			$month = 11;
		}
		else if($month == "Dec"){
			$month = 12;
		}
		
		return $month;
	}
	
	function get_financial_year()
	{
		//get the financial year
  		$table = "financial_year";
		$where = "financial_year_status = 0";
		
		$this->db->where($where);
		$result = $this->db->get($table);
		
		$financial_year_id = 0;
		
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $row)
			{
				$financial_year_id = $row->financial_year_id;
			}
		}
		
		return $financial_year_id;
	}
	
	public function get_table_id($table_name)
	{
		$table = "table";
		$where = "table_name = '$table_name'";
		
		$this->db->where($where);
		$result = $this->db->get($table);
		
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $row):
				$table_id = $row->table_id;
			endforeach;
		}
		else
		{
			$items2 = array("table_name" => $table_name);
			$this->db->insert($table, $items2);
			$table_id = $this->db->insert_id();
		}
		return $table_id;
	}
	
	public function create_payroll($year, $month, $branch_id)
	{
		$table = 'payroll';
		$end_month_date = 28;
		$mth = $this->month_calc($month);
		
		//update payrolls of duplicate month/year to inactive
		$where = array(
			"month_id" => $mth,
			"payroll_year" => $year,
			"branch_id" => $branch_id
		);
		$update_data['payroll_status'] = 0;
		$this->db->where($where);
		$this->db->update($table, $update_data);
		
		$data = array(
			'branch_id' 		=> $branch_id,
			'month_id' 			=> $mth,
			'payroll_year' 		=> $year,
			'created'			=> date('Y-m-d H:i:s'),
			'payroll_created_for'=>$year.'-'.$month.'-'.$end_month_date,
			'created_by'		=> $this->session->userdata('personnel_id'),
			'modified_by'		=> $this->session->userdata('personnel_id')
		);
		
		if($this->db->insert($table, $data))
		{
			return $this->db->insert_id();
		}
		
		else
		{
			return FALSE;
		}
	}
	
	public function save_salary($payroll_id, $branch_id, $branch_working_hours)
	{
		$payroll_created_for = NULL;
		
		while($payroll_created_for == NULL)
		{
			$this->db->where('payroll.branch_id = '.$branch_id.' AND payroll_id = '.$payroll_id);
			$payroll_query = $this->db->get('payroll');
			
			if($payroll_query->num_rows() > 0)
			{
				$payroll_rs = $payroll_query->row();
				$payroll_created_for = $payroll_rs->payroll_created_for;				
			}	
		}
		
		//Delete salary for that month
		$table = "payroll_item";
		$total_overtime_hours = $total_overtime_amount = $total_overtime_type = $total_overtime_rate = $total_benefit_amount = $total_payment_amount = $total_allowance_amount = $total_deduction_amount = $total_other_deduction_amount = $total_nssf_amount = $total_nhif_amount = $total_life_ins_amount = $total_paye_amount = $total_monthly_relief_amount = $total_insurance_amount = $total_scheme_amount = $total_scheme_remaining_balance = $total_savings_amount = $total_insurance_relief = array();
		$total_overtime_array = $total_benefits_array = $total_payments_array = $total_allowances_array = $total_deductions_array = $total_other_deductions_array = $total_schemes_array = array();
		
		//get personnel
		$this->db->where('branch_id = '.$branch_id.' AND personnel_type_id = 1 AND personnel_status = 1');
		$result = $this->db->get('personnel');//echo $result->num_rows();die();
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $row):
				$personnel_id = $row->personnel_id;
				$personnel_number = $row->personnel_number;
				$nssf_status = $row->nssf_status;
				$nhif_status = $row->nhif_status;


				$total_benefits = $total_payments = $total_allowances = $total_deductions = $total_other_deductions = 0;
				$this->db->where('personnel_id  = '.$personnel_id.' AND payroll_id = '.$payroll_id);
				$this->db->update('payroll_item', array("payroll_item_status"=>0));
				/*
					--------------------------------------------------------------------------------------
					Payments
					--------------------------------------------------------------------------------------
				*/
				$result2 = $this->payroll_model->get_personnel_payments($personnel_id);
				$table_payment = $this->get_table_id("payment");
				
				if($result2->num_rows() > 0)
				{
					foreach ($result2->result() as $row2):
						$payment = $row2->amount;
						$payment_id = $row2->id;
						$total_payments += $payment;
						
						if($payment_id == 1)
						{
							$overtime_basic_pay = $payment;
						}
				
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_payment,
							"table_id" => $payment_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($payment)
						);
						
						if(!isset($total_payment_amount[$personnel_id][$payment_id]))
						{
							$total_payment_amount[$personnel_id][$payment_id] = 0;
						}
						
						$total_payment_amount[$personnel_id][$payment_id] = round($payment);
						
						if(!isset($total_payments_array[$payment_id]))
						{
							$total_payments_array[$payment_id] = 0;
						}
						
						$total_payments_array[$payment_id] += round($payment);
				
						$this->db->insert($table, $items);
					endforeach;
				}
				
				/*
					--------------------------------------------------------------------------------------
					Benefits
					--------------------------------------------------------------------------------------
				*/
				$result2 = $this->payroll_model->get_personnel_benefits($personnel_id);
				$table_benefit = $this->get_table_id("benefit");
				$total_benefits = 0;
				
				if($result2->num_rows() > 0)
				{
					foreach ($result2->result() as $row2):
						$taxable = $row2->taxable;
						$benefit = $row2->amount;
						$benefit_id = $row2->id;
						
						if($taxable == 1)
						{
							$total_benefits += $benefit;
						}
				
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_benefit,
							"table_id" => $benefit_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($benefit)
						);
						
						if(!isset($total_benefit_amount[$personnel_id][$benefit_id]))
						{
							$total_benefit_amount[$personnel_id][$benefit_id] = 0;
						}
						
						$total_benefit_amount[$personnel_id][$benefit_id] = round($benefit);
						
						if(!isset($total_benefits_array[$benefit_id]))
						{
							$total_benefits_array[$benefit_id] = 0;
						}
						
						$total_benefits_array[$benefit_id] += round($benefit);
				
					$this->db->insert($table, $items);
					endforeach;
				}
				
				/*
					--------------------------------------------------------------------------------------
					Allowances
					--------------------------------------------------------------------------------------
				*/
				$result2 = $this->payroll_model->get_personnel_allowances($personnel_id);
				$table_allowance = $this->get_table_id("allowance");
				$total_allowances = 0;
				
				if($result2->num_rows() > 0)
				{
					foreach ($result2->result() as $row2):
						$allowance = $row2->amount;
						$allowance_id = $row2->id;
						$taxable = $row2->taxable;
						
						if($taxable == 1)
						{
							$total_allowances += $allowance;
						}
				
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_allowance,
							"table_id" => $allowance_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($allowance)
						);
						
						if(!isset($total_allowance_amount[$personnel_id][$allowance_id]))
						{
							$total_allowance_amount[$personnel_id][$allowance_id] = 0;
						}
						
						$total_allowance_amount[$personnel_id][$allowance_id] = round($allowance);
						
						if(!isset($total_allowances_array[$allowance_id]))
						{
							$total_allowances_array[$allowance_id] = 0;
						}
						
						$total_allowances_array[$allowance_id] += round($allowance);
				
					$this->db->insert($table, $items);
					endforeach;
				}
				
				/*
					--------------------------------------------------------------------------------------
					Overtime
					--------------------------------------------------------------------------------------
				*/
				$result_overtime = $this->payroll_model->get_personnel_overtime($personnel_id);
				$table_overtime = $this->get_table_id("overtime");
				$total_overtime_for_tax = 0;
				//var_dump($result_overtime);
				if($result_overtime->num_rows() > 0)
				{
					foreach ($result_overtime->result() as $row2):
						$overtime_id = $row2->id;
						//var_dump($overtime_id);
						$personnel_overtime_hours = $row2->amount;
						$overtime_type = $row2->overtime_type;
						$overtime_type_rate = $row2->overtime_type_rate;
						$total_overtime = $hours = 0;
						
						//calculate overtime
						if($overtime_type_rate == 1)
						{
							$hours = $personnel_overtime_hours;
							if($overtime_type == 1)
							{
								$overtime_rate = $this->config->item('normal_overtime_rate');
							}
							else if($overtime_type == 2)
							{
								$overtime_rate = $this->config->item('holiday_overtime_rate');
							}
							
							$basic_pay = $total_payment_amount[$personnel_id][1];
							
							if ($branch_working_hours > 0)
							{
								$total_overtime = ($basic_pay * $overtime_rate * $personnel_overtime_hours) / $branch_working_hours;
							}
						}
						else
						{
							$total_overtime = $personnel_overtime_hours;
						}
						
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_overtime,
							"table_id" => $overtime_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($total_overtime)
						);
						
						//amount
						if(!isset($total_overtime_amount[$personnel_id][$overtime_id]))
						{
							$total_overtime_amount[$personnel_id][$overtime_id] = 0;
						}
						
						$total_overtime_amount[$personnel_id][$overtime_id] = round($total_overtime);
						$total_overtime_for_tax += $total_overtime;
						
						//hours
						if(!isset($total_overtime_hours[$personnel_id][$overtime_id]))
						{
							$total_overtime_hours[$personnel_id][$overtime_id] = 0;
						}
						
						$total_overtime_hours[$personnel_id][$overtime_id] = $hours;
						
						//type
						if(!isset($total_overtime_type[$personnel_id][$overtime_id]))
						{
							$total_overtime_type[$personnel_id][$overtime_id] = 0;
						}
						
						$total_overtime_type[$personnel_id][$overtime_id] = round($overtime_type);
						
						//rate
						if(!isset($total_overtime_rate[$personnel_id][$overtime_id]))
						{
							$total_overtime_rate[$personnel_id][$overtime_id] = 0;
						}
						
						$total_overtime_rate[$personnel_id][$overtime_id] = round($overtime_type_rate);
						
						//save total amount
						if(!isset($total_overtime_array[$overtime_id]))
						{
							$total_overtime_array[$overtime_id] = 0;
						}
						
						$total_overtime_array[$overtime_id] += round($total_overtime);
				
					$this->db->insert($table, $items);
					endforeach;
				}
				
				/*
					--------------------------------------------------------------------------------------
					PAYE
					--------------------------------------------------------------------------------------
				*/
				$gross_taxable = $total_payments + $total_benefits + $total_allowances + $total_overtime_for_tax;//echo $taxable.'<br/>';
				
				/*
					--------------------------------------------------------------------------------------
					NSSF
					--------------------------------------------------------------------------------------
				*/

				if($nssf_status == 1)
				{
					$nssf_query = $this->payroll_model->get_nssf();
					$nssf = 0;
					
					if(($nssf_query->num_rows() > 0) && ($gross_taxable > 0))
					{
						foreach ($nssf_query->result() as $row2)
						{
							$nssf_id = $row2->nssf_id;
							$nssf = $row2->amount;
							
							$nssf_percentage = $row2->percentage;
							
							if($nssf_percentage == 1)
							{
								$nssf_deduction_amount = $gross_taxable;
								
								if($nssf_deduction_amount > 18000)
								{
									$nssf_deduction_amount = 18000;
								}
								$nssf = $nssf_deduction_amount * ($nssf/100);
							}
						}
					}
							
					if(!isset($total_nssf_amount[$personnel_id]))
					{
						$total_nssf_amount[$personnel_id] = 0;
					}
					
					$total_nssf_amount[$personnel_id] = round($nssf);
					
					$taxable = $gross_taxable - $nssf;
					
					$table_nssf = $this->get_table_id("nssf");
					
					$items = array(
						"payroll_id" => $payroll_id,
						"table" => $table_nssf,
						"table_id" => 1,
						"personnel_id" => $personnel_id,
						"payroll_item_amount" => round($nssf)
					);
				}
				else
				{
					$taxable = $gross_taxable;
					$total_nssf_amount[$personnel_id] = 0;
				}
				
				
				/*if($personnel_id == 242)
				{
					var_dump($taxable); die();
				}*/
				if($taxable > 10164)
				{
					$paye = $this->payroll_model->calculate_paye($taxable);//echo $paye.'<br/>';
				}
				
				else
				{
					$paye = 0;
				}
						
				if(!isset($total_paye_amount[$personnel_id]))
				{
					$total_paye_amount[$personnel_id] = 0;
				}
				
				$total_paye_amount[$personnel_id] = round($paye);
				
				$table_paye = $this->get_table_id("paye");
				
				$items = array(
					"payroll_id" => $payroll_id,
					"table" => $table_paye,
					"table_id" => 1,
					"personnel_id" => $personnel_id,
					"payroll_item_amount" => round($paye)
				);
			
				$this->db->insert($table, $items);
				
				/*
					--------------------------------------------------------------------------------------
					Monthly relief
					--------------------------------------------------------------------------------------
				*/
				$table_relief = $this->get_table_id("relief");
				$monthly_relief = $this->payroll_model->get_monthly_relief();
				$items = array(
					"payroll_id" => $payroll_id,
					"table" => $table_relief,
					"table_id" => 1,
					"personnel_id" => $personnel_id,
					"payroll_item_amount" => round($monthly_relief)
				);
			
				$this->db->insert($table, $items);
						
				if(!isset($total_monthly_relief_amount[$personnel_id]))
				{
					$total_monthly_relief_amount[$personnel_id] = 0;
				}
				
				$total_monthly_relief_amount[$personnel_id] = round($monthly_relief);
				
				/*
					--------------------------------------------------------------------------------------
					Insurance relief
					--------------------------------------------------------------------------------------
				*/
				$table_relief = $this->get_table_id("insurance_relief");
				$monthly_relief = $this->payroll_model->get_monthly_relief();
				$insurance_res = $this->payroll_model->get_insurance_relief($personnel_id);
				$insurance_relief = $insurance_res['relief'];
				$insurance_amount = $insurance_res['amount'];
				$items = array(
					"payroll_id" => $payroll_id,
					"table" => $table_relief,
					"table_id" => 1,
					"personnel_id" => $personnel_id,
					"payroll_item_amount" => round($insurance_relief)
				);
			
				$this->db->insert($table, $items);
						
				if(!isset($total_insurance_relief[$personnel_id]))
				{
					$total_insurance_relief[$personnel_id] = 0;
				}
				
				$total_insurance_relief[$personnel_id] = round($insurance_relief);
				
				//insurance amount
				$table_relief = $this->get_table_id("insurance_amount");
				$items = array(
					"payroll_id" => $payroll_id,
					"table" => $table_relief,
					"table_id" => 1,
					"personnel_id" => $personnel_id,
					"payroll_item_amount" => round($insurance_amount)
				);
			
				$this->db->insert($table, $items);
						
				if(!isset($total_insurance_amount[$personnel_id]))
				{
					$total_insurance_amount[$personnel_id] = 0;
				}
				
				$total_insurance_amount[$personnel_id] = round($insurance_amount);
				
				/*
					--------------------------------------------------------------------------------------
					NHIF
					--------------------------------------------------------------------------------------
				*/
				$gross = ($total_payments + $total_allowances + $total_overtime_for_tax);

				if($nhif_status == 1)
				{
					$nhif_query = $this->payroll_model->calculate_nhif($gross);
					$nhif = 0;
					
					if(($nhif_query->num_rows() > 0) && ($gross_taxable > 0))
					{
						foreach ($nhif_query->result() as $row2)
						{
							$nhif = $row2->amount;
						}
					}
					$table_nhif = $this->get_table_id("nhif");
					
					$items = array(
						"payroll_id" => $payroll_id,
						"table" => $table_nhif,
						"table_id" => 1,
						"personnel_id" => $personnel_id,
						"payroll_item_amount" => round($nhif)
					);

				
					$this->db->insert($table, $items);
							
					if(!isset($total_nhif_amount[$personnel_id]))
					{
						$total_nhif_amount[$personnel_id] = 0;
					}
					$total_nhif_amount[$personnel_id] = round($nhif);
				}
				else
				{

					$total_nhif_amount[$personnel_id] = 0;
				}
				
				
				
				
				/*
					--------------------------------------------------------------------------------------
					Deductions
					--------------------------------------------------------------------------------------
				*/
				$result2 = $this->payroll_model->get_personnel_deductions($personnel_id);
				$table_deduction = $this->get_table_id("deduction");
				
				if($result2->num_rows() > 0)
				{
					foreach ($result2->result() as $row2):
						$deduction = $row2->amount;
						$deduction_id = $row2->id;
						
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_deduction,
							"table_id" => $deduction_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($deduction)
						);
						
						if(!isset($total_deduction_amount[$personnel_id][$deduction_id]))
						{
							$total_deduction_amount[$personnel_id][$deduction_id] = 0;
						}
						
						$total_deduction_amount[$personnel_id][$deduction_id] = round($deduction);
						
						if(!isset($total_deductions_array[$deduction_id]))
						{
							$total_deductions_array[$deduction_id] = 0;
						}
						
						$total_deductions_array[$deduction_id] += round($deduction);
				
					$this->db->insert($table, $items);
					endforeach;
				}
				
				/*
					--------------------------------------------------------------------------------------
					Other deductions
					--------------------------------------------------------------------------------------
				*/
				$result2 = $this->payroll_model->get_personnel_other_deductions($personnel_id);
				$table_other_deduction = $this->get_table_id("other_deduction");
				
				if($result2->num_rows() > 0)
				{
					foreach ($result2->result() as $row2):
						$other_deduction = $row2->amount;
						$other_deduction_id = $row2->id;
						
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_other_deduction,
							"table_id" => $other_deduction_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($other_deduction)
						);
						
						if(!isset($total_other_deduction_amount[$personnel_id][$other_deduction_id]))
						{
							$total_other_deduction_amount[$personnel_id][$other_deduction_id] = 0;
						}
						
						$total_other_deduction_amount[$personnel_id][$other_deduction_id] = round($other_deduction);
						
						if(!isset($total_other_deductions_array[$other_deduction_id]))
						{
							$total_other_deductions_array[$other_deduction_id] = 0;
						}
						
						$total_other_deductions_array[$other_deduction_id] += round($other_deduction);
				
					$this->db->insert($table, $items);
					endforeach;
				}
				
				/*
					--------------------------------------------------------------------------------------
					Savings
					--------------------------------------------------------------------------------------
				*/
				$result3 = $this->payroll_model->get_personnel_savings($personnel_id);
				$table_savings = $this->get_table_id("savings");
				
				if($result3->num_rows() > 0)
				{
					foreach ($result3->result() as $row2):
						$savings = $row2->amount;
						$savings_id = $row2->id;
						
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_savings,
							"table_id" => $savings_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($savings)
						);
						
						if(!isset($total_savings_amount[$personnel_id][$savings_id]))
						{
							$total_savings_amount[$personnel_id][$savings_id] = 0;
						}
						
						$total_savings_amount[$personnel_id][$savings_id] = round($savings);
				
					$this->db->insert($table, $items);
					endforeach;
				}
				
				/*
					--------------------------------------------------------------------------------------
					Loan Schemes
					--------------------------------------------------------------------------------------
				*/
				$result4 = $this->payroll_model->get_personnel_scheme($personnel_id);
				
				$table_scheme = $this->get_table_id("loan_scheme");
				
				if($result4->num_rows() > 0)
				{
					$today = date('Y-m-d');
					foreach ($result4->result() as $row2):
						$amount = $row2->amount;
						$scheme_id = $row2->id;
						
						$monthly = $row2->monthly;
						$interest = $row2->interest;
						$interest2 = $row2->interest2;
						$remaining_balance = $row2->remaining_balance;//6500
						$sdate = $row2->sdate;
						$edate = $row2->edate;
						$prev_payments = $this->payroll_model->get_total_loan_scheme_paid($personnel_id, $scheme_id,$payroll_created_for);//15000
						if($personnel_id == 8194)
						{
							//var_dump($payroll_created_for); die();
						}
						//$prev_payments = $monthly * $this->payroll_model->dateDiff($sdate.' 00:00', $today.' 00:00', 'month');
						$prev_interest = $interest * $this->payroll_model->dateDiff($sdate.' 00:00', $today.' 00:00', 'month');
						//10000
						if($balance < 0)
						{
							$balance = 0;
						}
						$scheme_amount = 0;
						if($amount > 0)
						{
							$balance = $amount - $prev_payments;
							$difference = $balance - $remaining_balance;
							//check that the remaining balance as at the time is present
							if(($remaining_balance > 0) && ($remaining_balance < $balance))
							{
								$diff_items = array(
									"payroll_id" => $payroll_id,
									"table" => $table_scheme,
									"table_id" => $scheme_id,
									"personnel_id" => $personnel_id,
									"payroll_item_amount" => round($difference)
								);
								$this->db->insert($table, $diff_items);
								//check that deduction amount is greater than remaing_balance
								if($monthly < $remaining_balance)
								{
									if($monthly >= $balance)
									{
										$scheme_amount = $balance;
									}
									else
									{
										$scheme_amount = $monthly;
									}
								}
								else
								{
									if($remaining_balance >= $balance)
									{
										$scheme_amount = $balance;
									}
									else
									{
										$scheme_amount = $remaining_balance;
									}
								}	
							}
							else
							{
								//check that the monthly deduction is >= the balance
								if($monthly >= $balance)
								{
									$scheme_amount = $balance;
								}
								else
								{
									$scheme_amount = $monthly;
								}
							}
						}
						else
						{
							$balance = $remaining_balance;
							$difference = $balance - $remaining_balance;
							if(($remaining_balance > 0) &&($remaining_balance < $balance))
							{

								$diff_items = array(
									"payroll_id" => $payroll_id,
									"table" => $table_scheme,
									"table_id" => $scheme_id,
									"personnel_id" => $personnel_id,
									"payroll_item_amount" => round($difference)
								);
								$this->db->insert($table, $diff_items);
								//check that deduction amount is greater than remaing_balance
								if($monthly < $remaining_balance)
								{
									//compare the monthly deduction to the balance
									if($monthly >= $balance)
									{
										$scheme_amount = $balance;
									}
									else
									{
										$scheme_amount = $monthly;
									}
								}
								else
								{
									//if remaining_balance is greater compare it to the balance
									if($remaining_balance >= $balance)
									{
										$scheme_amount = $balance;
									}
									else
									{
										$scheme_amount = $remaining_balance;
									}
								}	
							}
							
							else
							{
								if($monthly >= $balance)
								{
									$scheme_amount = $balance;
								}
								else
								{
									$scheme_amount = $monthly;
								}
							}
						
						
						}
						/*if($personnel_id == 7592)
						{
							var_dump($monthly);die();
						}*/
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_scheme,
							"table_id" => $scheme_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($scheme_amount)
						);
						
						//repayment amount
						if(!isset($total_scheme_amount[$personnel_id][$scheme_id]))
						{
							$total_scheme_amount[$personnel_id][$scheme_id] = 0;
						}
						
						$total_scheme_amount[$personnel_id][$scheme_id] = round($scheme_amount);
						
						//borrowed amount
						if(!isset($total_scheme_borrowed[$personnel_id][$scheme_id]))
						{
							$total_scheme_borrowed[$personnel_id][$scheme_id] = 0;
						}
						
						$total_scheme_borrowed[$personnel_id][$scheme_id] = round($amount);
						
						//remaining balance amount
						if(!isset($total_scheme_remaining_balance[$personnel_id][$scheme_id]))
						{
							$total_scheme_remaining_balance[$personnel_id][$scheme_id] = 0;
						}
						
						$total_scheme_remaining_balance[$personnel_id][$scheme_id] = round($remaining_balance);
						
						//previous payments
						if(!isset($total_scheme_prev_payments[$personnel_id][$scheme_id]))
						{
							$total_scheme_prev_payments[$personnel_id][$scheme_id] = 0;
						}
						
						$total_scheme_prev_payments[$personnel_id][$scheme_id] = round($prev_payments);
						
						//start date
						if(!isset($total_scheme_sdate[$personnel_id][$scheme_id]))
						{
							$total_scheme_sdate[$personnel_id][$scheme_id] = '';
						}
						
						$total_scheme_sdate[$personnel_id][$scheme_id] = round($sdate);
						
						//end date
						if(!isset($total_scheme_edate[$personnel_id][$scheme_id]))
						{
							$total_scheme_edate[$personnel_id][$scheme_id] = '';
						}
						
						$total_scheme_edate[$personnel_id][$scheme_id] = round($edate);
						
						//total repayments
						if(!isset($total_schemes_array[$scheme_id]))
						{
							$total_schemes_array[$scheme_id] = 0;
						}
						
						$total_schemes_array[$scheme_id] += round($scheme_amount);
						
						/*if(!isset($total_scheme_amount[$personnel_id][$scheme_id]))
						{
							$total_scheme_amount[$personnel_id][$scheme_id] = 0;
						}
						
						$total_scheme_amount[$personnel_id][$scheme_id] = round($amount);*/
				
					$this->db->insert($table, $items);
					endforeach;
				}
			endforeach;
		}
		
		$payroll_data = array(
			'benefits' => $total_benefit_amount,
			'total_benefits' => $total_benefits_array,
			'payments' => $total_payment_amount,
			'total_payments' => $total_payments_array,
			'allowances' => $total_allowance_amount,
			'total_allowances' => $total_allowances_array,
			'deductions' => $total_deduction_amount,
			'total_deductions' => $total_deductions_array,
			'other_deductions' => $total_other_deduction_amount,
			'total_other_deductions' => $total_other_deductions_array,
			'nssf' => $total_nssf_amount,
			'nhif' => $total_nhif_amount,
			'life_ins' => $total_life_ins_amount,
			'paye' => $total_paye_amount,
			'monthly_relief' => $total_monthly_relief_amount,
			'insurance_relief' => $total_insurance_relief,
			'insurance' => $total_insurance_amount,
			'scheme' => $total_scheme_amount,
			'scheme_borrowed' => $total_scheme_borrowed,
			'remaining_balance'=>$total_scheme_remaining_balance,
			'scheme_payments' => $total_scheme_prev_payments,
			'scheme_start_date' => $total_scheme_sdate,
			'scheme_end_date' => $total_scheme_edate,
			'total_scheme' => $total_schemes_array,
			'savings' => $total_savings_amount,
			'total_overtime' => $total_overtime_array,
			'overtime' => $total_overtime_amount,
			'overtime_rate' => $total_overtime_rate,
			'overtime_type' => $total_overtime_type,
			'overtime_hours' => $total_overtime_hours
		);
		
		$encoded = json_encode($payroll_data);
		$this->load->helper('file');
		$payroll_path = realpath(APPPATH . '../assets/payroll/');
		//$file_name = md5(date('Y-m-d H:i:s'));
		
		//get the payroll month n year
		$this->db->where('payroll_id = '.$payroll_id);
		$query = $this->db->get('payroll');
		$row = $query->row();
		$month_id = $row->month_id;
		$payroll_year = $row->payroll_year;
		$file_name = $payroll_id.'-'.$month_id.'-'.$payroll_year.'-'.$branch_id.'-'.date('Y-m-d H-i-s');
		$file = $payroll_path.'/'.$file_name.'.txt';
		
		if ( ! write_file($file, $encoded))
		{
			echo 'Unable to write the file';
		}
		else
		{
			$this->db->where('payroll_id', $payroll_id);
			$this->db->update('payroll', array('file_data' => $file_name));
			echo 'File written!';
		}
		return TRUE;
	}
	
	//generate payrol in batches
	public function generate_batch_payroll($payroll_id,$batch_from,$batch_to)
	{
		// GET THE branch id 
		$this->db->where('payroll.branch_id = branch.branch_id AND payroll_id = '.$payroll_id);
		$payroll_query = $this->db->get('payroll, branch');
		$payroll_rs = $payroll_query->result();
		$branch_id = $payroll_rs[0]->branch_id;
		$file_data = $payroll_rs[0]->file_data;
		$payroll_created_for = $payroll_rs[0]->payroll_created_for;
		$branch_working_hours = $payroll_rs[0]->branch_working_hours;

		$this->load->helper('file');
		$payroll_path = realpath(APPPATH . '../assets/payroll/');
		$file_payroll_data = array();

		//if already saved to the db
		if(!empty($file_data))
		{
			$file = $payroll_path.'/'.$file_data.'.txt';
			$content = read_file($file);
			$file_payroll_data = json_decode($content, TRUE);
			//var_dump($file_payroll_data);die();
		}
		
		//$file_name = md5(date('Y-m-d H:i:s'));
		$file_name = $payroll_id.'-'.$month_id.'-'.$payroll_year.'-'.$branch_id.'-'.date('Y-m-d H-i-s');
		$file = $payroll_path.'/'.$file_name.'.txt';
		$table = "payroll_item";

		if(count($file_payroll_data) > 0)
		{
			$total_benefit_amount = $file_payroll_data['benefits'];
			//var_dump($total_benefit_amount); die();
			$total_benefits_array = $file_payroll_data['total_benefits'];
			$total_payment_amount = $file_payroll_data['payments'];
			$total_payments_array = $file_payroll_data['total_payments'];
			$total_allowance_amount = $file_payroll_data['allowances'];
			$total_allowances_array = $file_payroll_data['total_allowances'];
			$total_deduction_amount = $file_payroll_data['deductions'];
			$total_deductions_array = $file_payroll_data['total_deductions'];
			$total_other_deduction_amount = $file_payroll_data['other_deductions'];
			$total_other_deductions_array = $file_payroll_data['total_other_deductions'];
			$total_nssf_amount = $file_payroll_data['nssf'];
			$total_nhif_amount = $file_payroll_data['nhif'];
			$total_life_ins_amount = $file_payroll_data['life_ins'];
			$total_paye_amount = $file_payroll_data['paye'];
			$total_monthly_relief_amount = $file_payroll_data['monthly_relief'];
			$total_insurance_relief = $file_payroll_data['insurance_relief'];
			$total_insurance_amount = $file_payroll_data['insurance'];
			$total_scheme_amount = $file_payroll_data['scheme'];
			$total_scheme_borrowed = $file_payroll_data['scheme_borrowed'];
			$total_scheme_prev_payments = $file_payroll_data['scheme_payments'];
			$total_scheme_sdate = $file_payroll_data['scheme_start_date'];
			$total_scheme_edate = $file_payroll_data['scheme_end_date'];
			$total_schemes_array = $file_payroll_data['total_scheme'];
			$total_savings_amount = $file_payroll_data['savings'];
			
			if(!isset($file_payroll_data['total_overtime']))
			{
				$total_overtime_amount = $total_overtime_type = $total_overtime_rate = array();
			}
			
			else
			{
				$total_overtime_array = $file_payroll_data['total_overtime'];
				$total_overtime_amount = $file_payroll_data['overtime'];
				$total_overtime_rate = $file_payroll_data['overtime_rate'];
				$total_overtime_type = $file_payroll_data['overtime_type'];
			}
			
			if(!isset($file_payroll_data['overtime_hours']))
			{
				$total_overtime_hours = array();
			}
			
			else
			{
				$total_overtime_hours = $file_payroll_data['overtime_hours'];
			}
		}

		else
		{
			//Delete salary for that month
			$total_overtime_hours = $total_overtime_amount = $total_overtime_type = $total_overtime_rate = $total_benefit_amount = $total_payment_amount = $total_allowance_amount = $total_deduction_amount = $total_other_deduction_amount = $total_nssf_amount = $total_nhif_amount = $total_life_ins_amount = $total_paye_amount = $total_monthly_relief_amount = $total_insurance_amount = $total_scheme_amount = $total_scheme_borrowed = $total_scheme_remaining_balance = $total_scheme_prev_payments = $total_scheme_sdate = $total_scheme_edate = $total_savings_amount = $total_insurance_relief = array();
			$total_overtime_array = $total_benefits_array = $total_payments_array = $total_allowances_array = $total_deductions_array = $total_other_deductions_array = $total_schemes_array = array();
		}
		
		//var_dump($total_overtime_hours);die();
		
		//get personnel
		$this->db->where('branch_id = '.$branch_id.' AND personnel_type_id = 1 AND personnel_status = 1');
		$this->db->order_by('personnel_id','ASC');

		$result = $this->db->get('personnel',$batch_from,$batch_to);
		//echo $batch_from;die();
		//echo $result->num_rows();die();
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $row):
				$personnel_id = $row->personnel_id;
				$personnel_number = $row->personnel_number;
				$total_benefits = $total_payments = $total_allowances = $total_deductions = $total_other_deductions = 0;
				
				/*
					--------------------------------------------------------------------------------------
					Payments
					--------------------------------------------------------------------------------------
				*/
				$result2 = $this->payroll_model->get_personnel_payments($personnel_id);
				$table_payment = $this->get_table_id("payment");
				
				if($result2->num_rows() > 0)
				{
					foreach ($result2->result() as $row2):
						$payment = $row2->amount;
						$payment_id = $row2->id;
						$total_payments += $payment;
						
						/*if($personnel_number == 'IH005')
						{
							var_dump($payment);die();
						}*/
				
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_payment,
							"table_id" => $payment_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($payment)
						);
						
						if(!isset($total_payment_amount[$personnel_id][$payment_id]))
						{
							$total_payment_amount[$personnel_id][$payment_id] = 0;
						}
						
						$total_payment_amount[$personnel_id][$payment_id] = round($payment);
						
						if(!isset($total_payments_array[$payment_id]))
						{
							$total_payments_array[$payment_id] = 0;
						}
						
						$total_payments_array[$payment_id] += round($payment);
				
						$this->db->insert($table, $items);
					endforeach;
				}
				
				/*
					--------------------------------------------------------------------------------------
					Benefits
					--------------------------------------------------------------------------------------
				*/
				$result2 = $this->payroll_model->get_personnel_benefits($personnel_id);
				$table_benefit = $this->get_table_id("benefit");
				$total_benefits = 0;
				
				if($result2->num_rows() > 0)
				{
					foreach ($result2->result() as $row2):
						$taxable = $row2->taxable;
						$benefit = $row2->amount;
						$benefit_id = $row2->id;
						
						if($taxable == 1)
						{
							$total_benefits += $benefit;
						}
				
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_benefit,
							"table_id" => $benefit_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($benefit)
						);
						
						if(!isset($total_benefit_amount[$personnel_id][$benefit_id]))
						{
							$total_benefit_amount[$personnel_id][$benefit_id] = 0;
						}
						
						$total_benefit_amount[$personnel_id][$benefit_id] = round($benefit);
						
						if(!isset($total_benefits_array[$benefit_id]))
						{
							$total_benefits_array[$benefit_id] = 0;
						}
						
						$total_benefits_array[$benefit_id] += round($benefit);
				
					$this->db->insert($table, $items);
					endforeach;
				}
				
				/*
					--------------------------------------------------------------------------------------
					Allowances
					--------------------------------------------------------------------------------------
				*/
				$result2 = $this->payroll_model->get_personnel_allowances($personnel_id);
				$table_allowance = $this->get_table_id("allowance");
				$total_allowances = 0;
				
				if($result2->num_rows() > 0)
				{
					foreach ($result2->result() as $row2):
						$allowance = $row2->amount;
						$allowance_id = $row2->id;
						$taxable = $row2->taxable;
						
						if($taxable == 1)
						{
							$total_allowances += $allowance;
						}
				
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_allowance,
							"table_id" => $allowance_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($allowance)
						);
						
						if(!isset($total_allowance_amount[$personnel_id][$allowance_id]))
						{
							$total_allowance_amount[$personnel_id][$allowance_id] = 0;
						}
						
						$total_allowance_amount[$personnel_id][$allowance_id] = round($allowance);
						
						if(!isset($total_allowances_array[$allowance_id]))
						{
							$total_allowances_array[$allowance_id] = 0;
						}
						
						$total_allowances_array[$allowance_id] += round($allowance);
				
					$this->db->insert($table, $items);
					endforeach;
				}
				/*
					--------------------------------------------------------------------------------------
					Overtime
					--------------------------------------------------------------------------------------
				*/
				$result_overtime = $this->payroll_model->get_personnel_overtime($personnel_id);
				$table_overtime = $this->get_table_id("overtime");
				$total_overtime_for_tax = 0;
				//var_dump($result_overtime);
				if($result_overtime->num_rows() > 0)
				{
					foreach ($result_overtime->result() as $row2):
						$overtime_id = $row2->id;
						//var_dump($overtime_id);
						$personnel_overtime_hours = $row2->amount;
						$overtime_type = $row2->overtime_type;
						$overtime_type_rate = $row2->overtime_type_rate;
						$total_overtime = $hours = 0;
						
						//calculate overtime
						if($overtime_type_rate == 1)
						{
							$hours = $personnel_overtime_hours;
							if($overtime_type == 1)
							{
								$overtime_rate = $this->config->item('normal_overtime_rate');
							}
							else if($overtime_type == 2)
							{
								$overtime_rate = $this->config->item('holiday_overtime_rate');
							}
							
							$basic_pay = $total_payment_amount[$personnel_id][1];
							
							if ($branch_working_hours > 0)
							{
								$total_overtime = ($basic_pay * $overtime_rate * $personnel_overtime_hours) / $branch_working_hours;
							}
						}
						else
						{
							$total_overtime = $personnel_overtime_hours;
						}
						
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_overtime,
							"table_id" => $overtime_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($total_overtime)
						);
						
						//amount
						if(!isset($total_overtime_amount[$personnel_id][$overtime_id]))
						{
							$total_overtime_amount[$personnel_id][$overtime_id] = 0;
						}
						
						$total_overtime_amount[$personnel_id][$overtime_id] = round($total_overtime);
						$total_overtime_for_tax += $total_overtime;
						if($personnel_id == 493)
						{
							//echo $total_overtime_amount[$personnel_id][$overtime_id];die();
						}
						//hours
						if(!isset($total_overtime_hours[$personnel_id][$overtime_id]))
						{
							$total_overtime_hours[$personnel_id][$overtime_id] = 0;
						}
						
						$total_overtime_hours[$personnel_id][$overtime_id] = $hours;
						
						//type
						if(!isset($total_overtime_type[$personnel_id][$overtime_id]))
						{
							$total_overtime_type[$personnel_id][$overtime_id] = 0;
						}
						
						$total_overtime_type[$personnel_id][$overtime_id] = round($overtime_type);
						
						//rate
						if(!isset($total_overtime_rate[$personnel_id][$overtime_id]))
						{
							$total_overtime_rate[$personnel_id][$overtime_id] = 0;
						}
						
						$total_overtime_rate[$personnel_id][$overtime_id] = round($overtime_type_rate);
						
						//save total amount
						if(!isset($total_overtime_array[$overtime_id]))
						{
							$total_overtime_array[$overtime_id] = 0;
						}
						
						$total_overtime_array[$overtime_id] += round($total_overtime);
				
					$this->db->insert($table, $items);
					endforeach;
				}
				
				/*
					--------------------------------------------------------------------------------------
					PAYE
					--------------------------------------------------------------------------------------
				*/
				$gross_taxable = $total_payments + $total_benefits + $total_allowances + $total_overtime_for_tax;//echo $taxable.'<br/>';
				
				/*
					--------------------------------------------------------------------------------------
					NSSF
					--------------------------------------------------------------------------------------
				*/
				$nssf_query = $this->payroll_model->get_nssf();
				$nssf = 0;
				
				if(($nssf_query->num_rows() > 0) && ($gross_taxable > 0))
				{
					foreach ($nssf_query->result() as $row2)
					{
						$nssf_id = $row2->nssf_id;
						$nssf = $row2->amount;
						
						$nssf_percentage = $row2->percentage;
						
						if($nssf_percentage == 1)
						{
							$nssf_deduction_amount = $gross_taxable;
							
							if($nssf_deduction_amount > 18000)
							{
								$nssf_deduction_amount = 18000;
							}
							$nssf = $nssf_deduction_amount * ($nssf/100);
						}
					}
				}
						
				if(!isset($total_nssf_amount[$personnel_id]))
				{
					$total_nssf_amount[$personnel_id] = 0;
				}
				
				$total_nssf_amount[$personnel_id] = round($nssf);
				
				$taxable = $gross_taxable - $nssf;
				
				$table_nssf = $this->get_table_id("nssf");
				
				$items = array(
					"payroll_id" => $payroll_id,
					"table" => $table_nssf,
					"table_id" => 1,
					"personnel_id" => $personnel_id,
					"payroll_item_amount" => round($nssf)
				);
				
				/*if($personnel_id == 242)
				{
					var_dump($taxable); die();
				}*/
				if($taxable > 10164)
				{
					$paye = $this->payroll_model->calculate_paye($taxable);//echo $paye.'<br/>';
				}
				
				else
				{
					$paye = 0;
				}
						
				if(!isset($total_paye_amount[$personnel_id]))
				{
					$total_paye_amount[$personnel_id] = 0;
				}
				
				$total_paye_amount[$personnel_id] = round($paye);
				
				$table_paye = $this->get_table_id("paye");
				
				$items = array(
					"payroll_id" => $payroll_id,
					"table" => $table_paye,
					"table_id" => 1,
					"personnel_id" => $personnel_id,
					"payroll_item_amount" => round($paye)
				);
			
				$this->db->insert($table, $items);
				
				/*
					--------------------------------------------------------------------------------------
					Monthly relief
					--------------------------------------------------------------------------------------
				*/
				$table_relief = $this->get_table_id("relief");
				$monthly_relief = $this->payroll_model->get_monthly_relief();
				$items = array(
					"payroll_id" => $payroll_id,
					"table" => $table_relief,
					"table_id" => 1,
					"personnel_id" => $personnel_id,
					"payroll_item_amount" => round($monthly_relief)
				);
			
				$this->db->insert($table, $items);
						
				if(!isset($total_monthly_relief_amount[$personnel_id]))
				{
					$total_monthly_relief_amount[$personnel_id] = 0;
				}
				
				$total_monthly_relief_amount[$personnel_id] = round($monthly_relief);
				
				/*
					--------------------------------------------------------------------------------------
					Insurance relief
					--------------------------------------------------------------------------------------
				*/
				$table_relief = $this->get_table_id("insurance_relief");
				$monthly_relief = $this->payroll_model->get_monthly_relief();
				$insurance_res = $this->payroll_model->get_insurance_relief($personnel_id);
				$insurance_relief = $insurance_res['relief'];
				$insurance_amount = $insurance_res['amount'];
				$items = array(
					"payroll_id" => $payroll_id,
					"table" => $table_relief,
					"table_id" => 1,
					"personnel_id" => $personnel_id,
					"payroll_item_amount" => round($insurance_relief)
				);
			
				$this->db->insert($table, $items);
						
				if(!isset($total_insurance_relief[$personnel_id]))
				{
					$total_insurance_relief[$personnel_id] = 0;
				}
				
				$total_insurance_relief[$personnel_id] = round($insurance_relief);
				
				//insurance amount
				$table_relief = $this->get_table_id("insurance_amount");
				$items = array(
					"payroll_id" => $payroll_id,
					"table" => $table_relief,
					"table_id" => 1,
					"personnel_id" => $personnel_id,
					"payroll_item_amount" => round($insurance_amount)
				);
			
				$this->db->insert($table, $items);
						
				if(!isset($total_insurance_amount[$personnel_id]))
				{
					$total_insurance_amount[$personnel_id] = 0;
				}
				
				$total_insurance_amount[$personnel_id] = round($insurance_amount);
				
				/*
					--------------------------------------------------------------------------------------
					NHIF
					--------------------------------------------------------------------------------------
				*/
				$gross = ($total_payments + $total_allowances + $total_overtime_for_tax);
				$nhif_query = $this->payroll_model->calculate_nhif($gross);
				$nhif = 0;
				
				if(($nhif_query->num_rows() > 0) && ($gross_taxable > 0))
				{
					foreach ($nhif_query->result() as $row2)
					{
						$nhif = $row2->amount;
					}
				}
				$table_nhif = $this->get_table_id("nhif");
				
				$items = array(
					"payroll_id" => $payroll_id,
					"table" => $table_nhif,
					"table_id" => 1,
					"personnel_id" => $personnel_id,
					"payroll_item_amount" => round($nhif)
				);
			
				$this->db->insert($table, $items);
						
				if(!isset($total_nhif_amount[$personnel_id]))
				{
					$total_nhif_amount[$personnel_id] = 0;
				}
				
				$total_nhif_amount[$personnel_id] = round($nhif);
				
				/*
					--------------------------------------------------------------------------------------
					Deductions
					--------------------------------------------------------------------------------------
				*/
				$result2 = $this->payroll_model->get_personnel_deductions($personnel_id);
				$table_deduction = $this->get_table_id("deduction");
				
				if($result2->num_rows() > 0)
				{
					foreach ($result2->result() as $row2):
						$deduction = $row2->amount;
						$deduction_id = $row2->id;
						
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_deduction,
							"table_id" => $deduction_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($deduction)
						);
						
						if(!isset($total_deduction_amount[$personnel_id][$deduction_id]))
						{
							$total_deduction_amount[$personnel_id][$deduction_id] = 0;
						}
						
						$total_deduction_amount[$personnel_id][$deduction_id] = round($deduction);
						
						if(!isset($total_deductions_array[$deduction_id]))
						{
							$total_deductions_array[$deduction_id] = 0;
						}
						
						$total_deductions_array[$deduction_id] += round($deduction);
				
					$this->db->insert($table, $items);
					endforeach;
				}
				
				/*
					--------------------------------------------------------------------------------------
					Other deductions
					--------------------------------------------------------------------------------------
				*/
				$result2 = $this->payroll_model->get_personnel_other_deductions($personnel_id);
				$table_other_deduction = $this->get_table_id("other_deduction");
				
				if($result2->num_rows() > 0)
				{
					foreach ($result2->result() as $row2):
						$other_deduction = $row2->amount;
						$other_deduction_id = $row2->id;
						
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_other_deduction,
							"table_id" => $other_deduction_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($other_deduction)
						);
						
						if(!isset($total_other_deduction_amount[$personnel_id][$other_deduction_id]))
						{
							$total_other_deduction_amount[$personnel_id][$other_deduction_id] = 0;
						}
						//echo $other_deduction; die();
						$total_other_deduction_amount[$personnel_id][$other_deduction_id] = round($other_deduction);
						
						if(!isset($total_other_deductions_array[$other_deduction_id]))
						{
							$total_other_deductions_array[$other_deduction_id] = 0;
						}
						
						$total_other_deductions_array[$other_deduction_id] += round($other_deduction);
				
					$this->db->insert($table, $items);
					endforeach;
				}
				
				/*
					--------------------------------------------------------------------------------------
					Savings
					--------------------------------------------------------------------------------------
				*/
				$result3 = $this->payroll_model->get_personnel_savings($personnel_id);
				$table_savings = $this->get_table_id("savings");
				
				if($result3->num_rows() > 0)
				{
					foreach ($result3->result() as $row2):
						$savings = $row2->amount;
						$savings_id = $row2->id;
						
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_savings,
							"table_id" => $savings_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($savings)
						);
						
						if(!isset($total_savings_amount[$personnel_id][$savings_id]))
						{
							$total_savings_amount[$personnel_id][$savings_id] = 0;
						}
						
						$total_savings_amount[$personnel_id][$savings_id] = round($savings);
				
					$this->db->insert($table, $items);
					endforeach;
				}
				
				/*
					--------------------------------------------------------------------------------------
					Loan Schemes
					--------------------------------------------------------------------------------------
				*/
				$result4 = $this->payroll_model->get_personnel_scheme($personnel_id);
				
				$table_scheme = $this->get_table_id("loan_scheme");
				
				$table_scheme = $this->get_table_id("loan_scheme");
				
				if($result4->num_rows() > 0)
				{
					$today = date('Y-m-d');
					foreach ($result4->result() as $row2):
						$amount = $row2->amount;
						$scheme_id = $row2->id;
						
						$monthly = $row2->monthly;
						$interest = $row2->interest;
						$interest2 = $row2->interest2;
						$remaining_balance = $row2->remaining_balance;//6500
						$sdate = $row2->sdate;
						$edate = $row2->edate;
						$prev_payments = $this->payroll_model->get_total_loan_scheme_paid($personnel_id, $scheme_id,$payroll_created_for);//15000
						if($personnel_id == 8194)
						{
							//var_dump($payroll_created_for); die();
						}
						//$prev_payments = $monthly * $this->payroll_model->dateDiff($sdate.' 00:00', $today.' 00:00', 'month');
						$prev_interest = $interest * $this->payroll_model->dateDiff($sdate.' 00:00', $today.' 00:00', 'month');
						//10000
						if($balance < 0)
						{
							$balance = 0;
						}
						$scheme_amount = 0;
						if($amount > 0)
						{
							$balance = $amount - $prev_payments;
							$difference = $balance - $remaining_balance;
							//check that the remaining balance as at the time is present
							if(($remaining_balance > 0) && ($remaining_balance < $balance))
							{
								$diff_items = array(
									"payroll_id" => $payroll_id,
									"table" => $table_scheme,
									"table_id" => $scheme_id,
									"personnel_id" => $personnel_id,
									"payroll_item_amount" => round($difference)
								);
								$this->db->insert($table, $diff_items);
								//check that deduction amount is greater than remaing_balance
								if($monthly < $remaining_balance)
								{
									if($monthly >= $balance)
									{
										$scheme_amount = $balance;
									}
									else
									{
										$scheme_amount = $monthly;
									}
								}
								else
								{
									if($remaining_balance >= $balance)
									{
										$scheme_amount = $balance;
									}
									else
									{
										$scheme_amount = $remaining_balance;
									}
								}	
							}
							else
							{
								//check that the monthly deduction is >= the balance
								if($monthly >= $balance)
								{
									$scheme_amount = $balance;
								}
								else
								{
									$scheme_amount = $monthly;
								}
							}
						}
						else
						{
							$balance = $remaining_balance;
							$difference = $balance - $remaining_balance;
							if(($remaining_balance > 0) &&($remaining_balance < $balance))
							{

								$diff_items = array(
									"payroll_id" => $payroll_id,
									"table" => $table_scheme,
									"table_id" => $scheme_id,
									"personnel_id" => $personnel_id,
									"payroll_item_amount" => round($difference)
								);
								$this->db->insert($table, $diff_items);
								//check that deduction amount is greater than remaing_balance
								if($monthly < $remaining_balance)
								{
									//compare the monthly deduction to the balance
									if($monthly >= $balance)
									{
										$scheme_amount = $balance;
									}
									else
									{
										$scheme_amount = $monthly;
									}
								}
								else
								{
									//if remaining_balance is greater compare it to the balance
									if($remaining_balance >= $balance)
									{
										$scheme_amount = $balance;
									}
									else
									{
										$scheme_amount = $remaining_balance;
									}
								}	
							}
							
							else
							{
								if($monthly >= $balance)
								{
									$scheme_amount = $balance;
								}
								else
								{
									$scheme_amount = $monthly;
								}
							}
						
						
						}
						/*if($personnel_id == 7592)
						{
							var_dump($monthly);die();
						}*/
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_scheme,
							"table_id" => $scheme_id,
							"personnel_id" => $personnel_id,

							"payroll_item_amount" => round($scheme_amount)
						);
						
						//repayment amount
						if(!isset($total_scheme_amount[$personnel_id][$scheme_id]))
						{
							$total_scheme_amount[$personnel_id][$scheme_id] = 0;
						}
						
						$total_scheme_amount[$personnel_id][$scheme_id] = round($scheme_amount);
						
						//borrowed amount
						if(!isset($total_scheme_borrowed[$personnel_id][$scheme_id]))
						{
							$total_scheme_borrowed[$personnel_id][$scheme_id] = 0;
						}
						
						$total_scheme_borrowed[$personnel_id][$scheme_id] = round($amount);
						
						//remaining balance amount
						if(!isset($total_scheme_remaining_balance[$personnel_id][$scheme_id]))
						{
							$total_scheme_remaining_balance[$personnel_id][$scheme_id] = 0;
						}
						
						$total_scheme_remaining_balance[$personnel_id][$scheme_id] = round($remaining_balance);
						
						//previous payments
						if(!isset($total_scheme_prev_payments[$personnel_id][$scheme_id]))
						{
							$total_scheme_prev_payments[$personnel_id][$scheme_id] = 0;
						}
						
						$total_scheme_prev_payments[$personnel_id][$scheme_id] = round($prev_payments);
						
						//start date
						if(!isset($total_scheme_sdate[$personnel_id][$scheme_id]))
						{
							$total_scheme_sdate[$personnel_id][$scheme_id] = '';
						}
						
						$total_scheme_sdate[$personnel_id][$scheme_id] = round($sdate);
						
						//end date
						if(!isset($total_scheme_edate[$personnel_id][$scheme_id]))
						{
							$total_scheme_edate[$personnel_id][$scheme_id] = '';
						}
						
						$total_scheme_edate[$personnel_id][$scheme_id] = round($edate);
						
						//total repayments
						if(!isset($total_schemes_array[$scheme_id]))
						{
							$total_schemes_array[$scheme_id] = 0;
						}
						
						$total_schemes_array[$scheme_id] += round($scheme_amount);
						
						/*if(!isset($total_scheme_amount[$personnel_id][$scheme_id]))
						{
							$total_scheme_amount[$personnel_id][$scheme_id] = 0;
						}
						
						$total_scheme_amount[$personnel_id][$scheme_id] = round($amount);*/
				
					$this->db->insert($table, $items);
					endforeach;
				}
			endforeach;
		}
		
		$payroll_data = array(
			'benefits' => $total_benefit_amount,
			'total_benefits' => $total_benefits_array,
			'payments' => $total_payment_amount,
			'total_payments' => $total_payments_array,
			'allowances' => $total_allowance_amount,
			'total_allowances' => $total_allowances_array,
			'deductions' => $total_deduction_amount,
			'total_deductions' => $total_deductions_array,
			'other_deductions' => $total_other_deduction_amount,
			'total_other_deductions' => $total_other_deductions_array,
			'nssf' => $total_nssf_amount,
			'nhif' => $total_nhif_amount,
			'life_ins' => $total_life_ins_amount,
			'paye' => $total_paye_amount,
			'monthly_relief' => $total_monthly_relief_amount,
			'insurance_relief' => $total_insurance_relief,
			'insurance' => $total_insurance_amount,
			'scheme' => $total_scheme_amount,
			'scheme_borrowed' => $total_scheme_borrowed,
			'remaining_balance'=>$total_scheme_remaining_balance,
			'scheme_payments' => $total_scheme_prev_payments,
			'scheme_start_date' => $total_scheme_sdate,
			'scheme_end_date' => $total_scheme_edate,
			'total_scheme' => $total_schemes_array,
			'savings' => $total_savings_amount,
			'total_overtime' => $total_overtime_array,
			'overtime' => $total_overtime_amount,
			'overtime_rate' => $total_overtime_rate,
			'overtime_type' => $total_overtime_type,
			'overtime_hours' => $total_overtime_hours
		);
		
		$encoded = json_encode($payroll_data);
		
		if ( ! write_file($file, $encoded))
		{
			echo 'Unable to write the file';
		}
		else
		{
			$this->db->where('payroll_id', $payroll_id);
			$this->db->update('payroll', array('file_data' => $file_name));
			echo 'File written!';
		}
		return $branch_id;
	}
	
	public function calculate_total_payroll_amount($payments_amount, $payment_id)
	{
		$total = 0;
		$count = count($payments_amount->$personnel_id);
		
		if($count > 0)
		{
			if(isset($payments_amount->$personnel_id->$table_id))
			{
				
			}
		}
		
		return $total;
	}
	
	public function get_payroll_amount_old($personnel_id, $payroll_id, $table, $table_id)
	{
		$this->db->select('payroll_item_amount AS amount');
		$this->db->from('payroll_item');
		$this->db->where("personnel_id = $personnel_id AND payroll_id = ".$payroll_id." AND `table` = ".$table." AND table_id = ".$table_id);
		
		$query = $this->db->get();
		$amount = 0;
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$amount = $row->amount;
		}
		
		return $amount;
	}
	
	public function get_payroll_amount2($payroll_id, $table)
	{
		$this->db->select('SUM(payroll_item_amount) AS amount');
		$this->db->from('payroll_item');
		$this->db->where("payroll_id = ".$payroll_id." AND `table` = ".$table);
		
		$query = $this->db->get();
		$amount = 0;
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$amount = $row->amount;
		}
		
		return $amount;
	}
	
	public function get_payroll_amount3($payroll_id, $table, $table_id)
	{
		$this->db->select('SUM(payroll_item_amount) AS amount');
		$this->db->from('payroll_item');
		$this->db->where("payroll_id = ".$payroll_id." AND `table` = ".$table." AND `table_id` = ".$table_id);
		
		$query = $this->db->get();
		$amount = 0;
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$amount = $row->amount;
		}
		
		return $amount;
	}
	
	public function get_total_payroll_amount($payroll_items, $table, $table_id)
	{
		$total = 0;
		if($payroll_items->num_rows() > 0)
		{
			foreach($payroll_items->result() as $res)
			{
				$payroll_item_amount = $res->payroll_item_amount;
				$payroll_table = $res->table;
				$payroll_table_id = $res->table_id;
				
				if(($payroll_table == $table) && ($payroll_table_id == $table_id))
				{
					$total += $payroll_item_amount;
				}
			}
		}
		
		return $total;
	}
	
	public function get_payroll_amount($personnel_id, $payroll_items, $table, $table_id)
	{
		$total = 0;
		try
		{
			if($payroll_items->num_rows() > 0)
			{
				foreach($payroll_items->result() as $res)
				{
					$payroll_item_amount = $res->payroll_item_amount;
					$payroll_personnel_id = $res->personnel_id;
					$payroll_table = $res->table;
					$payroll_table_id = $res->table_id;
					
					if(($payroll_table == $table) && ($payroll_table_id == $table_id) && ($payroll_personnel_id == $personnel_id))
					{
						$total = $payroll_item_amount;
						break;
					}
				}
			}
		}
		
		catch(Exception $e)
		{
		}
		return $total;
	}
	
	public function get_payroll_items($payroll_id)
	{
		$this->db->from('payroll_item');
		$this->db->where("payroll_id = ".$payroll_id);
		$this->db->order_by('`table`');
		$this->db->order_by('`table_id`');
		
		$query = $this->db->get();
		
		return $query;
	}
	
	function get_savings()
	{
		$this->db->where('savings_status', 0);
		$query = $this->db->get('savings');
		
		return $query;
	}
	
	function get_loan_schemes()
	{
		$this->db->where('loan_scheme_status', 0);
		$query = $this->db->get('loan_scheme');
		
		return $query;
	}
	
	function get_loan_scheme_interest($personnel_id, $date, $loan_scheme_id)
	{
		$this->db->select('loan_scheme.loan_scheme_id, personnel_scheme.personnel_scheme_amount AS amount, personnel_scheme.personnel_scheme_interest AS interest, loan_scheme.loan_scheme_name AS scheme_name, personnel_scheme.personnel_scheme_repayment_sdate AS sdate, personnel_scheme.personnel_scheme_repayment_edate AS edate, personnel_scheme_monthly AS monthly, personnel_scheme_int AS total_interest');
		$this->db->where("personnel_scheme.personnel_id = $personnel_id 
		AND loan_scheme.loan_scheme_status = 0
		AND loan_scheme.loan_scheme_id = $loan_scheme_id
		AND personnel_scheme.personnel_scheme_status = 0 
		AND personnel_scheme.personnel_scheme_repayment_sdate <= '$date' 
		AND personnel_scheme.personnel_scheme_repayment_edate >= '$date' 
		AND personnel_scheme.loan_scheme_id = loan_scheme.loan_scheme_id");
		$query = $this->db->get('personnel_scheme, loan_scheme');
		
		return $query;
	}
	
	function get_months()
	{
		$result = $this->db->get("month");
		
		return $result;
	}
	
	public function get_month_id($month)
	{
		$this->db->where('month_name', $month);
		$query = $this->db->get('month');
		
		$row = $query->row();
		return $row->month_id;
	}
	
	public function get_all_payments()
	{
		//$table = "payment";
		//$items = "*";
		//$order = "payment_name";
		$this->db->select('*');
		$result = $this->db->get('payment');
		//var_dump($result );die();
		return $result;
	}
	
	public function get_all_benefits()
	{
		$table = "benefit";
		$items = "*";
		$order = "benefit_name";
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_nssf()
	{
		$table = "nssf";
		$items = "*";
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_nhif()
	{
		$table = "nhif";
		$items = "*";
		$this->db->order_by('nhif_from');
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function calculate_nhif($amount)
	{
		$table = "nhif";
		$items = "nhif_amount AS amount";
		$where = '(('.$amount.' >= nhif_from AND '.$amount.' <= nhif_to) OR ('.$amount.' >= nhif_from AND nhif_to = 0)) AND nhif_status = 1';
		$this->db->where($where);
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function add_new_nhif()
	{
		$data = array(
			'nhif_from'=>$this->input->post('nhif_from'),
			'nhif_to'=>$this->input->post('nhif_to'),
			'nhif_amount'=>$this->input->post('nhif_amount')
		);
		
		if($this->db->insert('nhif', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
	
	public function edit_nhif($nhif_id)
	{
		$data = array(
			'nhif_from'		=> $this->input->post('nhif_from'.$nhif_id),
			'nhif_to'		=> $this->input->post('nhif_to'.$nhif_id),
			'nhif_amount'	=> $this->input->post('nhif_amount'.$nhif_id)
		);
		
		$this->db->where('nhif_id', $nhif_id);
		if($this->db->update('nhif', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function get_paye()
	{
		$table = "paye";
		$items = "*";
		$this->db->order_by('paye_from');
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function add_new_paye()
	{
		$data = array(
			'paye_from'=>$this->input->post('paye_from'),
			'paye_to'=>$this->input->post('paye_to'),
			'paye_amount'=>$this->input->post('paye_amount')
		);
		
		if($this->db->insert('paye', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
	
	public function edit_paye($paye_id)
	{
		$data = array(
			'paye_from'		=> $this->input->post('paye_from'.$paye_id),
			'paye_to'		=> $this->input->post('paye_to'.$paye_id),
			'paye_amount'	=> $this->input->post('paye_amount'.$paye_id)
		);
		
		$this->db->where('paye_id', $paye_id);
		if($this->db->update('paye', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function edit_nssf($nssf_id)
	{
		$data = array(
			'amount'		=> $this->input->post('amount'),
			'percentage'		=> $this->input->post('percentage')
		);
		
		$this->db->where('nssf_id', $nssf_id);
		if($this->db->update('nssf', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function add_new_payment()
	{
		$data = array(
			'payment_name'=>$this->input->post('payment_name')
		);
		
		if($this->db->insert('payment', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
	
	public function edit_payment($payment_id)
	{
		$data = array(
			'payment_name'		=> $this->input->post('payment_name'.$payment_id)
		);
		
		$this->db->where('payment_id', $payment_id);
		if($this->db->update('payment', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function add_new_benefit()
	{
		$data = array(
			'benefit_name'		=> $this->input->post('benefit_name'),
			'benefit_taxable'	=> $this->input->post('benefit_taxable')
		);
		
		if($this->db->insert('benefit', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
	
	public function edit_benefit($benefit_id)
	{
		$data = array(
			'benefit_name'	=> $this->input->post('benefit_name'.$benefit_id),
			'benefit_taxable'	=> $this->input->post('benefit_taxable'.$benefit_id)
		);
		
		$this->db->where('benefit_id', $benefit_id);
		if($this->db->update('benefit', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function add_new_allowance()
	{
		$data = array(
			'allowance_name'		=> $this->input->post('allowance_name'),
			'allowance_taxable'	=> $this->input->post('allowance_taxable')
		);
		
		if($this->db->insert('allowance', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
	
	public function edit_allowance($allowance_id)
	{
		$data = array(
			'allowance_name'	=> $this->input->post('allowance_name'.$allowance_id),
			'allowance_taxable'	=> $this->input->post('allowance_taxable'.$allowance_id)
		);
		
		$this->db->where('allowance_id', $allowance_id);
		if($this->db->update('allowance', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}

	}
	
	public function add_new_deduction()
	{
		$data = array(
			'deduction_name'		=> $this->input->post('deduction_name'),
			'deduction_taxable'	=> $this->input->post('deduction_taxable')
		);
		
		if($this->db->insert('deduction', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
	
	public function edit_deduction($deduction_id)
	{
		$data = array(
			'deduction_name'	=> $this->input->post('deduction_name'.$deduction_id),
			'deduction_taxable'	=> $this->input->post('deduction_taxable'.$deduction_id)
		);
		
		$this->db->where('deduction_id', $deduction_id);
		if($this->db->update('deduction', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function add_new_other_deduction()
	{
		$data = array(
			'other_deduction_name'		=> $this->input->post('other_deduction_name'),
			'other_deduction_taxable'	=> $this->input->post('other_deduction_taxable')
		);
		
		if($this->db->insert('other_deduction', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
	
	public function edit_other_deduction($other_deduction_id)
	{
		$data = array(
			'other_deduction_name'	=> $this->input->post('other_deduction_name'.$other_deduction_id),
			'other_deduction_taxable'	=> $this->input->post('other_deduction_taxable'.$other_deduction_id)
		);
		
		$this->db->where('other_deduction_id', $other_deduction_id);
		if($this->db->update('other_deduction', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function add_new_loan_scheme()
	{
		$data = array(
			'loan_scheme_name'		=> $this->input->post('loan_scheme_name'),
			//'loan_scheme_taxable'	=> $this->input->post('loan_scheme_taxable')
		);
		
		if($this->db->insert('loan_scheme', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
	
	public function edit_loan_scheme($loan_scheme_id)
	{
		$data = array(
			'loan_scheme_name'	=> $this->input->post('loan_scheme_name'.$loan_scheme_id),
			//'loan_scheme_taxable'	=> $this->input->post('loan_scheme_taxable'.$loan_scheme_id)
		);
		
		$this->db->where('loan_scheme_id', $loan_scheme_id);
		if($this->db->update('loan_scheme', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function add_new_saving()
	{
		$data = array(
			'savings_name'		=> $this->input->post('saving_name'),
			//'saving_taxable'	=> $this->input->post('saving_taxable')
		);
		
		if($this->db->insert('savings', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
	
	public function edit_saving($saving_id)
	{
		$data = array(
			'savings_name'	=> $this->input->post('saving_name'.$saving_id),
			//'saving_taxable'	=> $this->input->post('saving_taxable'.$saving_id)
		);
		
		$this->db->where('savings_id', $saving_id);
		if($this->db->update('savings', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function get_personnel_payments($personnel_id)
	{
		$table = "payment";
		$items = "personnel_payment_amount AS amount, payment.payment_id AS id";
		$this->db->join('personnel_payment', "personnel_payment.payment_id = payment.payment_id AND personnel_payment_status = 1 AND personnel_payment.personnel_id = ".$personnel_id, 'left');
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_personnel_benefits($personnel_id)
	{
		$table = "benefit";
		$items = "personnel_benefit_amount AS amount, benefit.benefit_id AS id, benefit_taxable AS taxable";
		$this->db->join('personnel_benefit', "personnel_benefit.benefit_id = benefit.benefit_id AND personnel_benefit_status = 1 AND personnel_benefit.personnel_id = ".$personnel_id, 'left');
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_personnel_allowances($personnel_id)
	{
		$table = "allowance";
		$items = "personnel_allowance_amount AS amount, allowance.allowance_id AS id, allowance_taxable AS taxable";
		$this->db->join('personnel_allowance', "personnel_allowance.allowance_id = allowance.allowance_id AND personnel_allowance_status = 1 AND personnel_allowance.allowance_id != 1 AND personnel_allowance.personnel_id = ".$personnel_id, 'left');
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_personnel_deductions($personnel_id)
	{
		$table = "deduction";
		$items = "personnel_deduction_amount AS amount, deduction.deduction_id AS id, deduction_taxable AS taxable";
		$this->db->join('personnel_deduction', "personnel_deduction.deduction_id = deduction.deduction_id AND personnel_deduction_status = 1 AND personnel_deduction.personnel_id = ".$personnel_id, 'left');
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_personnel_other_deductions($personnel_id)
	{
		$table = "other_deduction";
		$items = "personnel_other_deduction_amount AS amount, other_deduction.other_deduction_id AS id, other_deduction_taxable AS taxable";
		$this->db->join('personnel_other_deduction', "personnel_other_deduction.other_deduction_id = other_deduction.other_deduction_id AND personnel_other_deduction_status = 1 AND personnel_other_deduction.personnel_id = ".$personnel_id, 'left');
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_personnel_savings($personnel_id)
	{
		$table = "savings";
		$items = "personnel_savings.personnel_savings_amount AS amount, savings.savings_name, savings.savings_id AS id, personnel_savings_opening";
		$this->db->join('personnel_savings', "personnel_savings.savings_id = savings.savings_id AND personnel_savings_status = 1 AND personnel_savings.personnel_id = ".$personnel_id, 'left');
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_personnel_scheme($personnel_id)
	{
		$table = "loan_scheme";
		$items = "personnel_scheme_int AS interest2, personnel_scheme_amount AS amount, personnel_scheme_monthly AS monthly, personnel_scheme_interest AS interest, loan_scheme.loan_scheme_id AS id, personnel_scheme_repayment_sdate AS sdate, personnel_scheme_repayment_edate AS edate, remaining_balance";
		$this->db->join('personnel_scheme', "personnel_scheme.loan_scheme_id = loan_scheme.loan_scheme_id AND personnel_scheme_status = 1 AND personnel_scheme.personnel_id = ".$personnel_id, 'left');
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_personnel_overtime($personnel_id)
	{
		$table = "overtime";
		$items = "personnel_overtime.personnel_overtime_hours AS amount, overtime.overtime_name, overtime.overtime_type AS id, overtime_type_rate, personnel_overtime.overtime_type";
		$this->db->join('personnel_overtime', "personnel_overtime.overtime_type = overtime.overtime_type AND personnel_overtime.personnel_id = ".$personnel_id, 'left');
		$this->db->select($items);
		$result = $this->db->get($table);
		return $result;
	}
	
	/*
	*	Retrieve all personnel
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_payrolls($table, $where, $per_page, $page, $order = 'created', $order_method = 'DESC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('payroll.*, month.*, branch.branch_id, branch.branch_name');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	
	public function get_payroll($payroll_id)
	{
		//retrieve all users
		$this->db->from('payroll');
		$this->db->select('*');
		$this->db->where('payroll_id', $payroll_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	public function edit_relief($relief_id)
	{
		$data = array(
			'relief_name'	=> $this->input->post('relief_name'.$relief_id),
			'relief_type'	=> $this->input->post('relief_type'.$relief_id),
			'relief_amount'	=> $this->input->post('relief_amount'.$relief_id)
		);
		
		$this->db->where('relief_id', $relief_id);
		if($this->db->update('relief', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function get_personnel_relief($personnel_id)
	{
		$table = "personnel_relief";
		$items = "personnel_relief_amount AS amount, relief_id AS id";
		$where = "personnel_relief_status = 1 AND personnel_id = ".$personnel_id;
		
		$this->db->select($items);
		$this->db->where($where);
		$result = $this->db->get($table);
		return $result;
	}
	
	public function get_monthly_relief()
	{
		$table = "relief";
		$items = "SUM(relief_amount) AS amount";
		$where = "relief_type = 1";
		$this->db->select($items);
		$this->db->where($where);
		$result = $this->db->get($table);
		
		$amount = 0;
		
		if($result->num_rows() > 0)
		{
			$row = $result->row();
			$amount = $row->amount;
		}
		return $amount;
	}

	
	public function get_insurance_relief($personnel_id)
	{
		$table = "relief";
		$items = "relief_amount, relief_id";
		$where = "relief_type = 0";
		$this->db->select($items);
		$this->db->where($where);
		$result = $this->db->get($table);
		
		$amount = 0;
		$relief = 0;
		
		if($result->num_rows() > 0)
		{
			foreach($result->result() as $row)
			{
				$relief_amount = $row->relief_amount;
				$relief_id = $row->relief_id;
				$where = 'personnel_id = '.$personnel_id.' AND relief_id = '.$relief_id;
				//get personnel_relief
				$this->db->select('personnel_relief_amount AS amount');
				$this->db->where($where);
				$query = $this->db->get('personnel_relief');
				
				if($query->num_rows() > 0)
				{
					$row2 = $query->row();
					$amount = $row2->amount;
					
					//get relief
					$relief = ($relief_amount/100) * $amount;
				}
			}
		}
		$return['amount'] = $amount;
		$return['relief'] = $relief;
		
		return $return;
	}
	public function edit_payment_details($personnel_id)
	{
		$data = array(
			'personnel_account_number' => $this->input->post('personnel_account_number'),
			'personnel_nssf_number' => $this->input->post('personnel_nssf_number'),
			'personnel_kra_pin' => $this->input->post('personnel_kra_pin'),
			'personnel_nhif_number' => $this->input->post('personnel_nhif_number')
		);
		
		$this->db->where('personnel_id', $personnel_id);
		if($this->db->update('personnel', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	//p9 data
	public function get_p9_form_data($personnel_id, $from_month_id,$to_month_id,$year,$branch_id)
	{
		$this->db->select('payroll.*');
		$this->db->where('payroll.payroll_id = payroll_item.payroll_id AND payroll.payroll_status = 1 AND payroll_item.personnel_id ='.$personnel_id. '  AND (payroll.month_id >= '.$from_month_id. ' AND payroll.month_id <= '.$to_month_id. ') AND payroll.payroll_status = 1 AND payroll.payroll_year ='.$year. ' AND payroll.branch_id ='.$branch_id);
		$query = $this->db->get('payroll,payroll_item');
		
		return $query;
	}
	
	//p10 data
	public function get_p10_form_data($from_month_id,$to_month_id,$year)
	{
		$this->db->select('');
		$this->db->where('payroll.payroll_id = payroll_item.payroll_id AND payroll.payroll_status = 1 AND (payroll.month_id >= '.$from_month_id. ' AND payroll.month_id <= '.$to_month_id. ') AND payroll.payroll_year ='.$year. ' AND payroll.branch_id ='.$this->session->userdata('branch_id'));
		$query = $this->db->get('payroll,payroll_item');
		
		return $query;
	}
	
	public function get_p10_payroll_amount($payroll_id, $table, $table_id)
	{
		$this->db->select('SUM(payroll_item_amount) AS amount');
		$this->db->from('payroll_item');
		$this->db->where("payroll_id = ".$payroll_id." AND `table` = ".$table." AND table_id = ".$table_id);
		
		$query = $this->db->get();
		$amount = 0;
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$amount = $row->amount;
		}
		
		return $amount;
	}
	
	//get bank_data reports
	public function get_bank_report_data($personnel_id, $month, $branch_id)
	{
		$this->db->select('');
		$this->db->where('payroll.payroll_id = payroll_item.payroll_id AND payroll.payroll_status = 1 AND payroll_item.personnel_id ='.$personnel_id. '  AND payroll.month_id ='.$month.' AND payroll.payroll_year ='.date('Y').' AND payroll.branch_id ='.$branch_id);
		$query = $this->db->get('payroll,payroll_item');
		
		return $query;
	}
	
	//get payroll reports for the branches
	public function get_payroll_report($table, $where, $config, $page, $order, $order_method)
	{
		$this->db->select();
		$this->db->where($where);
		$this->db->order_by($order);
		$query = $this->db->get($table);
		
		if ($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
		
	}
	public function get_payroll_summary($where)
	{
		$this->db->select('payroll.payroll_id, personnel.personnel_id,payroll.branch_id');
		$this->db->where($where);
		$query = $this->db->get('personnel, branch, payroll_item, payroll,month');
		
		return $query;
	}
	public function get_most_recent_month_active_payroll($branch_id, $month, $year)
	{
		$this->db->where('payroll_status = 1 AND payroll_closed = 0 AND branch_id ='.$branch_id.' AND month_id ='.$month.' AND payroll_year ='.$year);
		$query = $this->db->get('payroll');
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	public function get_most_recent_year_active_payroll($branch_id)
	{
		$this->db->select('MAX(payroll_year) as payroll_year');
		$this->db->where('payroll_status = 1 AND payroll_closed = 0 AND branch_id ='.$branch_id);
		$query = $this->db->get('payroll');
		if($query->num_rows() > 0)
		{
			$recent_year = $query->row();
			$year = $recent_year->payroll_year;
		}
		
		return $year;
	}
	public function get_payroll_summary_report($branch_id, $payroll_items, $payment_table, $table_id)
	{
		$total = 0;
		if($payroll_items->num_rows() > 0)
		{
			foreach($payroll_items->result() as $res)
			{
				$payroll_item_amount = $res->payroll_item_amount;
				$payroll_personnel_id = $res->personnel_id;
				$payroll_table = $res->table;
				$payroll_table_id = $res->table_id;
				
				if(($payroll_table == $table) && ($payroll_table_id == $table_id) && ($payroll_personnel_id == $personnel_id))
				{
					$total += $payroll_item_amount;
				}
			}
		}
		
		return $total;
	}
	
	//update payment amounts except basic pay to 0 when payroll is closed
	public function update_payment_closing_balances($payroll_id)
	{
		$items['personnel_payment_amount'] = 0;
		$items['personnel_payment_date'] = date('Y-m-d H-i-s');
		$this->db->where('payment_id != 1 AND personnel_id IN (SELECT personnel_id from payroll_item where payroll_id = '.$payroll_id.')');
		$query = $this->db->update('personnel_payment',$items);	
		return $query;
	}
	//update all allowances except house allowance
	public function update_allowances_closing_balances($payroll_id)
	{
		$items['personnel_allowance_amount'] = 0;
		$items['personnel_allowance_date'] = date('Y-m-d H-i-s');
		$this->db->where('allowance_id != 7 AND personnel_id = SELECT personnel_id from payroll_item where payroll_id = '.$payroll_id);
		$query = $this->db->update('personnel_allowance',$items);	
		return $query;
	}
	public function update_overtime_closing_balances($payroll_id)
	{
		$items['personnel_overtime_hours'] = 0;
		$this->db->where('personnel_id = SELECT personnel_id from payroll_item where payroll_id = '.$payroll_id);
		$query = $this->db->update('personnel_overtime',$items);	
		return $query;	
	}
	
	public function update_overtime_hours($personnel_id)
	{
		$table = 'personnel_overtime';
		$update_data['personnel_overtime_hours'] = $this->input->post('personnel_overtime_hours');
		$overtime_type = $this->input->post('overtime_type');
		$update_data['overtime_type_rate'] = $this->input->post('overtime_type_rate');
		
		//check if personnel has overtime hours
		$where = array('personnel_id' => $personnel_id, "overtime_type" => $overtime_type);
		$this->db->where($where);
		$query = $this->db->get($table);
		
		//if personnel exists, update
		if($query->num_rows() > 0)
		{
			$this->db->where($where);
			if($this->db->update($table, $update_data))
			{
				return TRUE;
			}
			
			else
			{
				return FALSE;
			}
		}
		
		//if personnel doesn't exist, insert
		else
		{
			$update_data['personnel_id'] = $personnel_id;
			$update_data['overtime_type'] = $overtime_type;
			if($this->db->insert($table, $update_data))
			{
				return TRUE;
			}
			
			else
			{
				return FALSE;
			}
		}
	}
	
	public function get_overtime_hours($personnel_id)
	{
		$this->db->where('personnel.personnel_id = personnel_overtime.personnel_id AND personnel.branch_id = branch.branch_id AND personnel.personnel_id = '.$personnel_id);
		$query = $this->db->get('personnel, branch, personnel_overtime');
		
		return $query;
	}
	
	public function calculate_single_overtime($personnel_overtime_hours, $overtime_type, $overtime_type_rate, $branch_working_hours, $personnel_id)
	{
		$total_overtime = 0;
		if($overtime_type_rate == 1)
		{
			if($overtime_type == 1)
			{
				$overtime_rate = $this->config->item('normal_overtime_rate');
			}
			else if($overtime_type == 2)
			{
				$overtime_rate = $this->config->item('holiday_overtime_rate');
			}
			
			//get basic pay
			$this->db->where('personnel_id', $personnel_id);
			$basic_pay_query = $this->db->get('personnel_payment');
			$basic_pay = 0;
			if($basic_pay_query->num_rows() > 0)
			{
				$basic_row = $basic_pay_query->row();
				$basic_pay = $basic_row->personnel_payment_amount;
			}
			if($branch_working_hours > 0)
			{
				$total_overtime = ($basic_pay * $overtime_rate * $personnel_overtime_hours) / $branch_working_hours;
			}
			else
			{
				$total_overtime = 0;
			}
		}
		
		else
		{
			$total_overtime = $personnel_overtime_hours;
		}
		
		if(($total_overtime >= 0) && !empty($total_overtime))
		{
			return number_format($total_overtime, 2);
		}
		
		else
		{
			return $total_overtime;
		}
	}
	
	public function calculate_overtime($personnel_id)
	{
		$query = $this->payroll_model->get_overtime_hours($personnel_id);
		$total_overtime = 0;
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$personnel_overtime_hours = $row->personnel_overtime_hours;
				$branch_working_hours = $row->branch_working_hours;
				$overtime_type = $row->overtime_type;
				$overtime_type_rate = $row->overtime_type_rate;
				
				if($overtime_type_rate == 1)
				{
					if($overtime_type == 1)
					{
						$overtime_rate = $this->config->item('normal_overtime_rate');
					}
					else if($overtime_type == 2)
					{
						$overtime_rate = $this->config->item('holiday_overtime_rate');
					}
					
					//get basic pay
					$this->db->where('personnel_id', $personnel_id);
					$basic_pay_query = $this->db->get('personnel_payment');
					$basic_pay = 0;
					if($basic_pay_query->num_rows() > 0)
					{
						$basic_row = $basic_pay_query->row();
						$basic_pay = $basic_row->personnel_payment_amount;
					}
					if ($branch_working_hours > 0)
					{
						$total_overtime += ($basic_pay * $overtime_rate * $personnel_overtime_hours) / $branch_working_hours;
					}
					else
					{
						$total_overtime = 0;
					}
				}
				
				else
				{
					$total_overtime += $personnel_overtime_hours;
				}
			}
		}
		
		//save allowance (overtime)
		$this->db->where(array('personnel_id' => $personnel_id, 'allowance_id' => 1));
		$query = $this->db->get('personnel_allowance');
		
		//if personnel exists, update
		if($query->num_rows() > 0)
		{
			$this->db->where(array('personnel_id' => $personnel_id, 'allowance_id' => 1));
			if($this->db->update('personnel_allowance', array('personnel_allowance_amount' => $total_overtime)))
			{
			}
		}
		
		else
		{
			if($this->db->insert('personnel_allowance', array('personnel_allowance_amount' => $total_overtime, 'personnel_id' => $personnel_id, 'allowance_id' => 1)))
			{
			}
		}
		return number_format($total_overtime, 2);
	}
	
	//total basic pay for each payroll
	public function get_total_basic_pay($payroll_id,$branch_id)
	{
		$this->db->select('SUM(payroll_item_amount) AS total_basic_pay');
		$this->db->where('payroll.payroll_id = payroll_item.payroll_id AND payroll_item.table = 7 AND  payroll_item.table_id = 1 AND payroll.payroll_status = 1 AND payroll_item.personnel_id = personnel.personnel_id AND personnel.branch_id = '.$branch_id.' AND payroll.branch_id ='.$branch_id);
		$query = $this->db->get('payroll,payroll_item,personnel');
		
		if($query->num_rows() > 0)
		{
			$basic_row = $query->row();
			$basic_pay = $basic_row->total_basic_pay;
		}
		return $basic_pay;
	}
	
	//total benefits for each payroll
	public function get_total_benefits($payroll_id,$branch_id)
	{
		$this->db->select('SUM(payroll_item_amount) AS total_benefits');
		$this->db->where('payroll.payroll_id = payroll_item.payroll_id AND payroll_item.table = 8 AND  payroll_item.table_id = benefit.benefit_id AND payroll.payroll_status = 1 AND payroll_item.personnel_id = personnel.personnel_id AND personnel.branch_id = '.$branch_id.' AND payroll.branch_id ='.$branch_id);
		$query = $this->db->get('payroll,payroll_item,personnel,benefit');
		
		if($query->num_rows() > 0)
		{
			$benefits = $query->row();
			$total_benefits = $benefits->total_benefits;
		}
		return $total_benefits;
	}
	
	//total allowances for each payroll
	public function get_total_allowances($payroll_id,$branch_id)
	{
		$this->db->select('SUM(payroll_item_amount) AS total_allowances');
		$this->db->where('payroll.payroll_id = payroll_item.payroll_id AND payroll_item.table = 3 AND  payroll_item.table_id = allowance.allowance_id AND payroll.payroll_status = 1 AND payroll_item.personnel_id = personnel.personnel_id AND personnel.branch_id = '.$branch_id.' AND payroll.branch_id ='.$branch_id);
		$query = $this->db->get('payroll,payroll_item,personnel,allowance');
		
		if($query->num_rows() > 0)
		{
			$allowances = $query->row();
			$total_allowances = $allowances->total_allowances;
		}
		return $total_allowances;
	}
	
	//helb
	public function get_total_helb($payroll_id,$branch_id)
	{
		$this->db->select('SUM(payroll_item_amount) AS total_helb');
		$this->db->where('payroll.payroll_id = payroll_item.payroll_id AND payroll_item.table = 4 AND  payroll_item.table_id = deduction.deduction_id AND payroll.payroll_status = 1 AND payroll_item.personnel_id = personnel.personnel_id AND personnel.branch_id = '.$branch_id.' AND payroll.branch_id ='.$branch_id);
		$query = $this->db->get('payroll,payroll_item,personnel,deduction');
		
		if($query->num_rows() > 0)
		{
			$helb = $query->row();
			$helb_total = $helb->total_helb;
		}
		return $helb_total;
	}
	//paye
	public function get_total_paye($payroll_id,$branch_id)
	{
		$this->db->select('SUM(payroll_item_amount) AS total_paye');
		$this->db->where('payroll.payroll_id = payroll_item.payroll_id AND payroll_item.table = 9 AND  payroll_item.table_id = paye.paye_id AND payroll.payroll_status = 1 AND payroll_item.personnel_id = personnel.personnel_id AND personnel.branch_id = '.$branch_id.' AND payroll.branch_id ='.$branch_id);
		$query = $this->db->get('payroll,payroll_item,personnel,paye');
		
		if($query->num_rows() > 0)
		{
			$paye = $query->row();
			$paye_total = $paye->total_paye;
		}
		return $paye_total;
	}
	//import overtime template
	function import_overtime_template()
	{
		$this->load->library('Excel');
		
		$title = 'Overtime Import Template';
		$count=1;
		$row_count=0;
		
		$report[$row_count][0] = 'Employee Number';
		$report[$row_count][1] = 'Amount (Hrs/Value)';
		$report[$row_count][2] = 'Overtime Type (Normal-1,Holiday-2)';
		$report[$row_count][3] = 'Overtime Rate (Rate-1,Amount-2)';
		
		$row_count++;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	//import overtime data
	public function import_csv_overtime($upload_path)
	{
		//load the file model
		$this->load->model('admin/file_model');
		/*
			-----------------------------------------------------------------------------------------
			Upload csv
			-----------------------------------------------------------------------------------------
		*/
		$response = $this->file_model->upload_csv($upload_path, 'import_csv');
		
		if($response['check'])
		{
			$file_name = $response['file_name'];
			
			$array = $this->file_model->get_array_from_csv($upload_path.'/'.$file_name);
			//var_dump($array); die();
			$response2 = $this->sort_overtime_data($array);
		
			if($this->file_model->delete_file($upload_path."\\".$file_name, $upload_path))
			{
			}
			
			return $response2;
		}
		
		else
		{
			$this->session->set_userdata('error_message', $response['error']);
			return FALSE;
		}
	}
	//sort overtime imported data
	public function sort_overtime_data($array)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);//var_dump($array);die();
		
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 4))
		{
			$response = '
				<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Member Number</th>
						  <th>Comment</th>
						</tr>
					  </thead>
					  <tbody>
			';
			
			//retrieve the data from array
			for($r = 1; $r < $total_rows; $r++)
			{
				$personnel_number = $array[$r][0];
				$personnel_number = str_replace(" ", "", $personnel_number);
				$branch_id = $this->input->post('branch_id');
				
				$items['personnel_overtime_hours'] = $array[$r][1];
				$items['overtime_type'] = $array[$r][2];
				$items['overtime_type_rate'] = $array[$r][3];
				$comment = '';
				if(!empty($personnel_number))
				{
					$personnel_id = $this->get_personnel_id($personnel_number, $branch_id);
					$items['personnel_id'] = $personnel_id;
					$overtime_type = $array[$r][2];
					// check if the personnel overtime already exists
					if($this->check_current_personnel_overtime_exists($personnel_id,$overtime_type))
					{
						$overtime_type = $array[$r][2];
						
						//personnel exists for that overtime type then update existing data
						$this->db->where('personnel_id ='.$personnel_id.' AND overtime_type = '.$overtime_type);
						if($this->db->update('personnel_overtime', $items))
						{
							$this->calculate_overtime($personnel_id);
							$comment .= '<br/>'.$personnel_number.' overtime of '.$items['personnel_overtime_hours'].' successfully updated';
							$class = 'success';
						}
						
						else
						{
							$comment .= '<br/>'.$personnel_number.' overtime of '.$items['personnel_overtime_hours'].' could not be updated';
							$class = 'danger';
						}
					}
					else
					{
						// number does not exisit
						//save product in the db
						if($this->db->insert('personnel_overtime', $items))
						{
							$this->calculate_overtime($personnel_id);
							$comment .= '<br/>'.$personnel_number.' overtime of '.$items['personnel_overtime_hours'].' successfully added to the database';
							$class = 'success';
						}
						
						else
						{
							$comment .= '<br/>Internal error. Could not add mpersonnel to the database. Please contact the site administrator';
							$class = 'warning';
						}
					}
				}
				
				else
				{
					$comment .= '<br/>Not saved ensure you have a member number entered';
					$class = 'danger';
				}
				
				
				$response .= '
					
						<tr class="'.$class.'">
							<td>'.$r.'</td>
							<td>'.$personnel_number.'</td>
							<td>'.$comment.'</td>
						</tr> 
				';
			}
			
			$response .= '</table>';
			
			$return['response'] = $response;
			$return['check'] = TRUE;
		}
		
		//if no products exist
		else
		{
			$return['response'] = 'Member data not found ';
			$return['check'] = FALSE;
		}
		
		return $return;
	}
	public function get_personnel_id($personnel_number, $branch_id)
	{
		$this->db->where('personnel_number = "'.$personnel_number.'" AND personnel.branch_id = '.$branch_id);
		$this->db->select('personnel_id');
		$result = $this->db->get('personnel');
		$personnelid = 0;
		if($result->num_rows() > 0)
		{
			foreach($result->result() as $personnel)
			{
				$personnelid = $personnel->personnel_id;
			}
		}
		return $personnelid;
	}
	public function check_current_personnel_overtime_exists($personnel_id,$overtime_type)
	{
		$this->db->where('personnel_id =' .$personnel_id.' AND overtime_type = '.$overtime_type);
		
		$query = $this->db->get('personnel_overtime');
		
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	public function get_personnel_emails($payroll_id)
	{
		$this->db->where('payroll.payroll_id = "'.$payroll_id.'" AND personnel.personnel_id = payroll_item.personnel_id AND payroll.payroll_id = payroll_item.payroll_id ');
		$this->db->select('personnel.*');
		$this->db->group_by(' personnel.personnel_id');
		$result = $this->db->get('personnel, payroll, payroll_item');
		
		return $result;
	}
	public function get_branch_email($branch_id)
	{
		
		$table = "branch";
		$where = "branch_id = ".$branch_id;
		
		$this->db->where($where);
		$this->db->select('branch_email');
		$result = $this->db->get($table);
		if($result->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	public function get_other_benefits()
	{
		$this->db->where('benefit_id != 1 ');
		$this->db->select('*');
		$result = $this->db->get('benefit');
		
			return $result;
	}
	public function get_other_allowances()
	{
		$this->db->where('allowance_id != 1 AND allowance_id != 7 AND allowance_id != 9');
		$this->db->select('*');
		$result = $this->db->get('allowance');
		
		return $result;
	}
	
	public function is_payslip_downloaded($personnel_id, $payroll_id)
	{
		$this->db->where('personnel_payslip_status = 1 AND personnel_id = '.$personnel_id.' AND payroll_id = '.$payroll_id);
		$this->db->select('*');
		$result = $this->db->get('personnel_payslip');
		
		if($result->num_rows() > 0)
		{
			$row = $result->row();
			$personnel_payslip_name = $row->personnel_payslip_name;
			
			return $personnel_payslip_name;
		}
		
		else
		{
			return FALSE;
		}
	}
	
	public function download_payslip($payroll_id, $personnel_id, $branches, $payslip_path)
	{
		$html = '';
		$where = 'payroll_item.personnel_id = personnel.personnel_id AND payroll_item.payroll_id = '.$payroll_id.' AND payroll_item.personnel_id = '.$personnel_id;
		
		if($branches->num_rows() > 0)
		{
			$row = $branches->result();
			$branch_id = $row[0]->branch_id;
			$branch_name = $row[0]->branch_name;
			$month_name = $row[0]->month_name;
			$branch_image_name = $row[0]->branch_image_name;
			$branch_address = $row[0]->branch_address;
			$branch_post_code = $row[0]->branch_post_code;
			$branch_city = $row[0]->branch_city;
			$branch_phone = $row[0]->branch_phone;
			$branch_email = $row[0]->branch_email;
			$branch_location = $row[0]->branch_location;
			$month_id = $row[0]->month_id;
			$payroll_year = $row[0]->payroll_year;
			$file_data = $row[0]->file_data;
			if(empty($file_data))
			{
				echo 'Please generate the payroll again to view the bank report';
				die();
			}
			$this->load->helper('file');
			$payroll_path = realpath(APPPATH . '../assets/payroll/');
			$file = $payroll_path.'/'.$file_data.'.txt';
			$data['payroll_data'] = json_decode(read_file($file));
			$where .= ' AND branch_id = '.$branch_id;
		}
		$result = $this->personnel_model->get_personnel($personnel_id);
		
		if($result->num_rows() > 0)
		{
			$row2 = $result->row();
			$onames = $row2->personnel_onames;
			$fname = $row2->personnel_fname;
			$personnel_number = $row2->personnel_number;
			$nssf_number = $row2->personnel_nssf_number;
			$nhif_number = $row2->personnel_nhif_number;
			$kra_pin = $row2->personnel_kra_pin;
			 
			$data['personnel_number'] = $personnel_number;
			$data['nssf_number'] = $nssf_number;
			$data['nhif_number'] = $nhif_number;
			$data['kra_pin'] = $kra_pin;
			$data['personnel_name'] = $fname." ".$onames;
			$data['personnel_id'] = $personnel_id;
			$data['personnel_number'] = $row2->personnel_number;
		}
		$data['branch_name'] = $branch_name;
		$data['branch_image_name'] = $branch_image_name;
		$data['branch_id'] = $branch_id;
		$data['branch_address'] = $branch_address;
		$data['branch_post_code'] = $branch_post_code;
		$data['branch_city'] = $branch_city;
		$data['branch_phone'] = $branch_phone;
		$data['branch_email'] = $branch_email;
		$data['branch_location'] = $branch_location;
		$data['personnel_id'] = $personnel_id;
		$data['payroll_id'] = $payroll_id;
		$data['savings_table'] = $this->payroll_model->get_table_id("savings");
		$data['loan_scheme_table'] = $this->payroll_model->get_table_id("loan_scheme");
		$data['payroll'] = $this->payroll_model->get_payroll($payroll_id);
		$data['query'] = $this->personnel_model->retrieve_payroll_personnel($where);
		$data['payments'] = $this->payroll_model->get_all_payments();
		$data['benefits'] = $this->payroll_model->get_all_benefits();
		$data['allowances'] = $this->payroll_model->get_all_allowances();
		$data['deductions'] = $this->payroll_model->get_all_deductions();
		$data['savings'] = $this->payroll_model->get_all_savings();
		$data['loan_schemes'] = $this->payroll_model->get_all_loan_schemes();
		$data['other_deductions'] = $this->payroll_model->get_all_other_deductions();
		$data['personel_payments'] = $this->payroll_model->get_personnel_payments($personnel_id);
		$data['personnel_benefits'] = $this->payroll_model->get_personnel_benefits($personnel_id);
		$data['personnel_allowances'] = $this->payroll_model->get_personnel_allowances($personnel_id);
		$data['personnel_deductions'] = $this->payroll_model->get_personnel_deductions($personnel_id);
		$data['personnel_other_deductions'] = $this->payroll_model->get_personnel_other_deductions($personnel_id);
		$data['personnel_savings'] = $this->payroll_model->get_personnel_savings($personnel_id);
		$data['personnel_loan_schemes'] = $this->payroll_model->get_personnel_scheme($personnel_id);
		$data['payroll_items'] = $this->payroll_model->get_payroll_items($payroll_id);
		
		$html = $this->load->view('payroll/monthly_payslips', $data, TRUE);
		
		//echo $html; die();
		//download title
		$row = $data['query']->row();
		$personnel_number = $row->personnel_number;
		$personnel_fname = $row->personnel_fname;
		$personnel_onames = $row->personnel_onames;
		$personnel_national_id_number = $row->personnel_national_id_number;
		$title = $month_name.' '.$payroll_year.' '.$personnel_onames.' '.$personnel_fname.' payslip.pdf';
        //load mPDF library
		
		/*header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1
		header('Pragma: no-cache'); // HTTP 1.0
		header('Expires: 0'); // Proxies*/
        /*$this->load->library('mpdf/mpdf');
		$this->mpdf->WriteHTML($html);
		$this->mpdf->SetProtection(array(), $personnel_national_id_number);
		$this->mpdf->Output($title, 'F');*/
		$mpdf=new mPDF();
		$mpdf->WriteHTML($html);
		$mpdf->SetProtection(array('copy', 'print'), $personnel_national_id_number);
		$mpdf->Output($title, 'F');
		
		//Add payslip to database
		$this->db->where(array('personnel_id' => $personnel_id, 'payroll_id' => $payroll_id));
		$this->db->update('personnel_payslip', array('personnel_payslip_status' => 0));
		
		$this->db->insert('personnel_payslip', array('personnel_payslip_name' => $title, 'personnel_id' => $personnel_id, 'payroll_id' => $payroll_id, 'personnel_payslip_status' => 1, 'created' => date('Y-m-d H:i:s'), 'created_by' => $this->session->userdata('personnel_id'), 'modified_by' => $this->session->userdata('personnel_id')));
		
		//check if file has finished downloaded
		$payslip = $payslip_path.'/'.$title;
		//echo $payslip;die();
		while(!file_exists($payslip))
		{
			//print_r ($payslip);echo '<br/>';
			$payslip = $payslip_path.'/'.$title;
		}
		return $title;
		
		/*$this->mpdf->WriteHTML($html);
		
		$content = $this->mpdf->Output('', 'S');
		
		$content = chunk_split(base64_encode($content));
		
		$mailto = 'alvaro@omnis.co.ke';
		
		$from_name = 'Omnis Limited';
		
		$from_mail = 'hr@omnis.co.ke';
		
		$replyto = 'hr@omnis.co.ke';
		
		$uid = md5(uniqid(time()));
		
		$subject = 'Payslip';
		
		$message = 'Find your payslip attached';
		
		$filename = 'payslip.pdf';
		
		$header = "From: ".$from_name." <".$from_mail.">\r\n";
		
		$header .= "Reply-To: ".$replyto."\r\n";
		
		$header .= "MIME-Version: 1.0\r\n";
		
		$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
		
		$header .= "This is a multi-part message in MIME format.\r\n";
		
		$header .= "--".$uid."\r\n";
		
		$header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
		
		$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		
		$header .= $message."\r\n\r\n";
		
		$header .= "--".$uid."\r\n";
		
		$header .= "Content-Type: application/pdf; name=\"".$filename."\"\r\n";
		
		$header .= "Content-Transfer-Encoding: base64\r\n";
		
		$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
		
		$header .= $content."\r\n\r\n";
		
		$header .= "--".$uid."--";
		
		$is_sent = @mail($mailto, $subject, "", $header);
		
		$this->mpdf->Output();
		
		exit;*/
	}
	public function get_other_payments()
	{
		$this->db->where('payment_id > 1');
		$this->db->select('*');
		$result = $this->db->get('payment');
		
		return $result;
	}
	
	public function create_data_file($payroll_id, $branch_id)
	{
		//Delete salary for that month
		//$table = "payroll_item";
		$payroll_items = $this->payroll_model->get_payroll_items($payroll_id);
		$payments = $this->payroll_model->get_all_payments();
		$benefits = $this->payroll_model->get_all_benefits();
		$allowances = $this->payroll_model->get_all_allowances();
		$deductions = $this->payroll_model->get_all_deductions();
		$savings = $this->payroll_model->get_all_savings();
		$loan_schemes = $this->payroll_model->get_all_loan_schemes();
		$other_deductions = $this->payroll_model->get_all_other_deductions();
		
		$total_benefit_amount = $total_payment_amount = $total_allowance_amount = $total_deduction_amount = $total_other_deduction_amount = $total_nssf_amount = $total_nhif_amount = $total_life_ins_amount = $total_paye_amount = $total_monthly_relief_amount = $total_insurance_amount = $total_scheme_amount = $total_savings_amount = $total_insurance_relief = array();
		$total_benefits_array = $total_payments_array = $total_allowances_array = $total_deductions_array = $total_other_deductions_array = array();
		
		//get personnel
		$this->db->where('branch_id = '.$branch_id.' AND personnel_type_id = 1 AND personnel_status = 1');
		$result = $this->db->get('personnel');//echo $result->num_rows();die();
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $row):
				$personnel_id = $row->personnel_id;
				$personnel_number = $row->personnel_number;
				$total_benefits = $total_payments = $total_allowances = $total_deductions = $total_other_deductions = 0;
				
				/*
					--------------------------------------------------------------------------------------
					Payments
					--------------------------------------------------------------------------------------
				*/
				if($payments->num_rows() > 0)
				{
					$table = $this->payroll_model->get_table_id("payment");
					foreach($payments->result() as $res)
					{
						$payment_id = $res->payment_id;
						$payment_abbr = $res->payment_name;
						$table_id = $payment_id;
						
						$payment = $this->payroll_model->get_payroll_amount($personnel_id, $payroll_items, $table, $table_id);
						$total_payments += $payment;
						if($payment_id == 1)
						{
							//var_dump($payroll_items->result());die();
							//echo $table; echo $table_id; echo $personnel_id;
						}
						
						if(!isset($total_payment_amount[$personnel_id][$payment_id]))
						{
							$total_payment_amount[$personnel_id][$payment_id] = 0;
						}
						
						$total_payment_amount[$personnel_id][$payment_id] = round($payment);
						
						if(!isset($total_payments_array[$payment_id]))
						{
							$total_payments_array[$payment_id] = 0;
						}
						
						$total_payments_array[$payment_id] += round($payment);
					}
				}
				//var_dump($total_payments_array);die();
				/*
					--------------------------------------------------------------------------------------
					Benefits
					--------------------------------------------------------------------------------------
				*/
				if($benefits->num_rows() > 0)
				{
					$table = $this->payroll_model->get_table_id("benefit");
					foreach($benefits->result() as $res)
					{
						$benefit_id = $res->benefit_id;
						$benefit_name = $res->benefit_name;
						$table_id = $benefit_id;
						
						$benefit = $this->payroll_model->get_payroll_amount($personnel_id, $payroll_items, $table, $table_id);
						$total_benefits += $benefit;
						
						if(!isset($total_benefit_amount[$personnel_id][$benefit_id]))
						{
							$total_benefit_amount[$personnel_id][$benefit_id] = 0;
						}
						
						$total_benefit_amount[$personnel_id][$benefit_id] = round($benefit);
						
						if(!isset($total_benefits_array[$benefit_id]))
						{
							$total_benefits_array[$benefit_id] = 0;
						}
						
						$total_benefits_array[$benefit_id] += round($benefit);
					}
				}
				
				/*
					--------------------------------------------------------------------------------------
					Allowances
					--------------------------------------------------------------------------------------
				*/
				if($allowances->num_rows() > 0)
				{
					$table = $this->payroll_model->get_table_id("allowance");
					foreach($allowances->result() as $res)
					{
						$allowance_id = $res->allowance_id;
						$allowance_name = $res->allowance_name;
						$table_id = $allowance_id;
						
						$allowance = $this->payroll_model->get_payroll_amount($personnel_id, $payroll_items, $table, $table_id);
						$total_allowances += $allowance;
						
						if(!isset($total_allowance_amount[$personnel_id][$allowance_id]))
						{
							$total_allowance_amount[$personnel_id][$allowance_id] = 0;
						}
						
						$total_allowance_amount[$personnel_id][$allowance_id] = round($allowance);
						
						if(!isset($total_allowances_array[$allowance_id]))
						{
							$total_allowances_array[$allowance_id] = 0;
						}
						
						$total_allowances_array[$allowance_id] += round($allowance);
					}
				}
				
				/*
					--------------------------------------------------------------------------------------
					PAYE
					--------------------------------------------------------------------------------------
				*/
				$gross_taxable = $total_payments + $total_benefits + $total_allowances + $total_overtime;;//echo $taxable.'<br/>';
				
				/*
					--------------------------------------------------------------------------------------
					NSSF
					--------------------------------------------------------------------------------------
				*/
				//nssf
				$table = $this->payroll_model->get_table_id("nssf");
				$nssf = $this->payroll_model->get_payroll_amount($personnel_id, $payroll_items, $payroll_id, $table, 1);
				//$total_nssf += $nssf;
						
				if(!isset($total_nssf_amount[$personnel_id]))
				{
					$total_nssf_amount[$personnel_id] = 0;
				}
				
				$total_nssf_amount[$personnel_id] = round($nssf);
				
				$taxable = $gross_taxable - $nssf;
				
				//paye
				$table = $this->payroll_model->get_table_id("paye");
				$paye = $this->payroll_model->get_payroll_amount($personnel_id, $payroll_items, $payroll_id, $table, 1);
						
				if(!isset($total_paye_amount[$personnel_id]))
				{
					$total_paye_amount[$personnel_id] = 0;
				}
				
				$total_paye_amount[$personnel_id] = round($paye);
				$total_nhif += $nhif;
				
				//relief
				$table = $this->payroll_model->get_table_id("relief");
				$monthly_relief = $this->payroll_model->get_payroll_amount($personnel_id, $payroll_items, $payroll_id, $table, 1);
						
				if(!isset($total_monthly_relief_amount[$personnel_id]))
				{
					$total_monthly_relief_amount[$personnel_id] = 0;
				}
				
				$total_monthly_relief_amount[$personnel_id] = round($monthly_relief);
				
				//insurance_relief
				$table = $this->payroll_model->get_table_id("insurance_relief");
				$insurance_relief = $this->payroll_model->get_payroll_amount($personnel_id, $payroll_items, $payroll_id, $table, 1);
						
				if(!isset($total_insurance_relief[$personnel_id]))
				{
					$total_insurance_relief[$personnel_id] = 0;
				}
				
				$total_insurance_relief[$personnel_id] = round($insurance_relief);
				
				//relief
				$table = $this->payroll_model->get_table_id("insurance_amount");
				$insurance_amount = $this->payroll_model->get_payroll_amount($personnel_id, $payroll_items, $payroll_id, $table, 1);
						
				if(!isset($total_insurance_amount[$personnel_id]))
				{
					$total_insurance_amount[$personnel_id] = 0;
				}
				
				$total_insurance_amount[$personnel_id] = round($insurance_amount);
				
				//nhif
				$table = $this->payroll_model->get_table_id("nhif");
				$nhif = $this->payroll_model->get_payroll_amount($personnel_id, $payroll_items, $payroll_id, $table, 1);
					
				if(!isset($total_nhif_amount[$personnel_id]))
				{
					$total_nhif_amount[$personnel_id] = 0;
				}
				
				$total_nhif_amount[$personnel_id] = round($nhif);
				
				/*
					--------------------------------------------------------------------------------------
					Deductions
					--------------------------------------------------------------------------------------
				*/
				$table = $this->payroll_model->get_table_id("deduction");
				
				if($deductions->num_rows() > 0)
				{
					foreach($deductions->result() as $res)
					{
						$deduction_id = $res->deduction_id;
						$deduction_name = $res->deduction_name;
						
						$table_id = $deduction_id;
						$deduction = $this->payroll_model->get_payroll_amount($personnel_id, $payroll_items, $table, $table_id);
						
						if(!isset($total_deduction_amount[$personnel_id][$deduction_id]))
						{
							$total_deduction_amount[$personnel_id][$deduction_id] = 0;
						}
						
						$total_deduction_amount[$personnel_id][$deduction_id] = round($deduction);
						
						if(!isset($total_deductions_array[$deduction_id]))
						{
							$total_deductions_array[$deduction_id] = 0;
						}
						
						$total_deductions_array[$deduction_id] += round($deduction);
					}
				}
				
				/*
					--------------------------------------------------------------------------------------
					Other deductions
					--------------------------------------------------------------------------------------
				*/
				$table = $this->payroll_model->get_table_id("other_deduction");
				
				if($other_deductions->num_rows() > 0)
				{
					foreach($other_deductions->result() as $res)
					{
						$other_deduction_id = $res->other_deduction_id;
						$other_deduction_name = $res->other_deduction_name;
						
						$table_id = $other_deduction_id;
						$other_deduction = $this->payroll_model->get_payroll_amount($personnel_id, $payroll_items, $table, $table_id);
						
						if(!isset($total_other_deduction_amount[$personnel_id][$other_deduction_id]))
						{
							$total_other_deduction_amount[$personnel_id][$other_deduction_id] = 0;
						}
						
						$total_other_deduction_amount[$personnel_id][$other_deduction_id] = round($other_deduction);
						
						if(!isset($total_other_deductions_array[$other_deduction_id]))
						{
							$total_other_deductions_array[$other_deduction_id] = 0;
						}
						
						$total_other_deductions_array[$other_deduction_id] += round($other_deduction);
					}
				}
				
				//savings
				$rs_savings = $this->payroll_model->get_savings();
				
				if($rs_savings->num_rows() > 0)
				{
					foreach($rs_savings->result() as $res)
					{
						$savings_name = $res->savings_name;
						$savings_id = $res->savings_id;
						
						$table = $this->payroll_model->get_table_id("savings");
					
						//get schemes
						$savings = $this->payroll_model->get_payroll_amount($personnel_id, $payroll_items, $payroll_id, $table, $savings_id);
						
						if(!isset($total_savings_amount[$personnel_id][$savings_id]))
						{
							$total_savings_amount[$personnel_id][$savings_id] = 0;
						}
						
						$total_savings_amount[$personnel_id][$savings_id] = round($savings);
					}
				}
				
				/*
					--------------------------------------------------------------------------------------
					Loan Schemes
					--------------------------------------------------------------------------------------
				*/
				$result4 = $this->payroll_model->get_personnel_scheme($personnel_id);
				
				$table_scheme = $this->get_table_id("loan_scheme");
				
				if($result4->num_rows() > 0)
				{
					foreach ($result4->result() as $row2):
						$amount = $row2->amount;
						$scheme_id = $row2->id;
						
						$monthly = $row2->monthly;
						$interest = $row2->interest;
						$interest2 = $row2->interest2;
						$sdate = $row2->sdate;
						$edate = $row2->edate;
						$installments = $this->payroll_model->dateDiff($sdate.' 00:00', $today.' 00:00', 'month');
						$installments += 1;
						$prev_payments = $monthly * $installments;
						$prev_interest = $interest * $installments;
						$balance = $amount - $prev_payments;
						$scheme_amount = 0;
						
						if($balance > 0)
						{
							$scheme_amount = $monthly;
						}
						
						/*if($personnel_id == 7592)
						{
							var_dump($monthly);die();
						}*/
						$items = array(
							"payroll_id" => $payroll_id,
							"table" => $table_scheme,
							"table_id" => $scheme_id,
							"personnel_id" => $personnel_id,
							"payroll_item_amount" => round($scheme_amount)
						);
						
						//repayment amount
						if(!isset($total_scheme_amount[$personnel_id][$scheme_id]))
						{
							$total_scheme_amount[$personnel_id][$scheme_id] = 0;
						}
						
						$total_scheme_amount[$personnel_id][$scheme_id] = round($scheme_amount);
						
						//borrowed amount
						if(!isset($total_scheme_borrowed[$personnel_id][$scheme_id]))
						{
							$total_scheme_borrowed[$personnel_id][$scheme_id] = 0;
						}
						
						$total_scheme_borrowed[$personnel_id][$scheme_id] = round($amount);
						
						//previous payments
						if(!isset($total_scheme_prev_payments[$personnel_id][$scheme_id]))
						{
							$total_scheme_prev_payments[$personnel_id][$scheme_id] = 0;
						}
						
						$total_scheme_prev_payments[$personnel_id][$scheme_id] = round($prev_payments);
						
						//start date
						if(!isset($total_scheme_sdate[$personnel_id][$scheme_id]))
						{
							$total_scheme_sdate[$personnel_id][$scheme_id] = '';
						}
						
						$total_scheme_sdate[$personnel_id][$scheme_id] = round($sdate);
						
						//end date
						if(!isset($total_scheme_edate[$personnel_id][$scheme_id]))
						{
							$total_scheme_edate[$personnel_id][$scheme_id] = '';
						}
						
						$total_scheme_edate[$personnel_id][$scheme_id] = round($edate);
						
						//total repayments
						if(!isset($total_schemes_array[$scheme_id]))
						{
							$total_schemes_array[$scheme_id] = 0;
						}
						
						$total_schemes_array[$scheme_id] += round($scheme_amount);
						
						/*if(!isset($total_scheme_amount[$personnel_id][$scheme_id]))
						{
							$total_scheme_amount[$personnel_id][$scheme_id] = 0;
						}
						
						$total_scheme_amount[$personnel_id][$scheme_id] = round($amount);*/
				
					$this->db->insert($table, $items);
					endforeach;
				}
			endforeach;
		}
		
		$payroll_data = array(
			'benefits' => $total_benefit_amount,
			'total_benefits' => $total_benefits_array,
			'payments' => $total_payment_amount,
			'total_payments' => $total_payments_array,
			'allowances' => $total_allowance_amount,
			'total_allowances' => $total_allowances_array,
			'deductions' => $total_deduction_amount,
			'total_deductions' => $total_deductions_array,
			'other_deductions' => $total_other_deduction_amount,
			'total_other_deductions' => $total_other_deductions_array,
			'nssf' => $total_nssf_amount,
			'nhif' => $total_nhif_amount,
			'life_ins' => $total_life_ins_amount,
			'paye' => $total_paye_amount,
			'monthly_relief' => $total_monthly_relief_amount,
			'insurance_relief' => $total_insurance_relief,
			'insurance' => $total_insurance_amount,
			'scheme' => $total_scheme_amount,
			'savings' => $total_savings_amount
		);
		
		$encoded = json_encode($payroll_data);
		$this->load->helper('file');
		$payroll_path = realpath(APPPATH . '../assets/payroll/');
		$file_name = md5(date('Y-m-d H:i:s'));
		$file = $payroll_path.'/'.$file_name.'.txt';
		
		if ( ! write_file($file, $encoded))
		{
			echo 'Unable to write the file';
		}
		else
		{
			$this->db->where('payroll_id', $payroll_id);
			$this->db->update('payroll', array('file_data' => $file_name));
			echo 'File written!';
		}
		return TRUE;
	}
	
	/*
	*	Retrieve all personnel
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_batch_personnel($table, $where, $per_page, $page, $order = 'personnel_id', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	public function get_bank_branch_code($bank_branch_id, $personnel_id)
	{
		$this->db->select('bank_branch_code');
		$this->db->where('bank_branch_id ='.$bank_branch_id);
		$query = $this->db->get('bank_branch');
		
		if($query->num_rows() > 0)
		{
			$branch_code = $query->row();
			$bank_branch_code = $branch_code->bank_branch_code;
			$this->db->where('personnel_id', $personnel_id);
			$this->db->update('personnel', array('bank_branch_id' => $bank_branch_code ));
		}
		
		return $bank_branch_code;
	}
	
	public function get_branch_working_hours($branch_id)
	{
		$this->db->select('branch_working_hours');
		$this->db->where('branch_id ='.$branch_id);
		$query = $this->db->get('branch');
		$branch_working_hours = 0;
		if($query->num_rows() > 0)
		{
			$branch_code = $query->row();
			$branch_working_hours = $branch_code->branch_working_hours;
		}
		
		return $branch_working_hours;
	}
	public function get_branch_personnel($branch_id)
	{
		$this->db->select('personnel_fname,personnel_onames,personnel_id,personnel_kra_pin');
		$this->db->where('branch_id = '.$branch_id);
		$query = $this->db->get('personnel');
		return $query;
	}
	public function get_branch_contacts($branch_id)
	{
		$this->db->select('branch_name,branch_kra_pin');
		$this->db->where('branch_id = '.$branch_id);
		$query = $this->db->get('branch');
		
		return $query;
	}
	public function get_all_personnel_payslips($branch_id,$personnel_id,$year,$from_month,$to_month)
	{
		$this->db->select('');
		$this->db->where('payroll.payroll_id = payroll_item.payroll_id AND payroll.payroll_status = 1 AND payroll_item.personnel_id ='.$personnel_id. '  AND (payroll.month_id >= '.$from_month.' AND payroll.month_id <= '.$to_month.') AND payroll.payroll_year ='.$year. ' AND payroll.branch_id ='.$branch_id);
		$this->db->group_by('payroll_item.payroll_id');
		$query = $this->db->get('payroll,payroll_item');
		
		return $query;
	}
	public function get_total_loan_scheme_paid($personnel_id, $loan_scheme_id, $payroll_created_for)
	{
		$table_id = $this->get_table_id("loan_scheme");
		$repaid_amount = 0;
		$where = '`table` = '.$table_id.' AND personnel_id = '.$personnel_id.' AND table_id = '.$loan_scheme_id.' AND payroll_item_status = 1 AND payroll.payroll_status = 1 AND payroll.payroll_id = payroll_item.payroll_id AND payroll.payroll_created_for <= "'.$payroll_created_for.'"';
		//echo $where; die();
		$this->db->select('payroll_item_amount');
		$this->db->where($where);
		//$this->db->group_by('payroll_id');
		$query = $this->db->get('payroll_item, payroll');
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $sum)
			{
				
				$repaid_amount += $sum->payroll_item_amount;
			}
		}
		
		return $repaid_amount;
	}
	public function get_payrolls($branch_id,$from_month_id,$to_month_id,$year)
	{
		$this->db->select('payroll.*, month.month_name');
		$this->db->where('payroll.month_id = month.month_id AND payroll.payroll_status = 1 AND payroll.branch_id = '.$branch_id.' AND payroll.payroll_year = '.$year.' AND payroll.month_id >= '.$from_month_id.' AND payroll.month_id <= '.$to_month_id);
		$this->db->order_by('payroll.month_id', 'ASC');
		$query = $this->db->get('payroll, month');
		
		return $query;
	}
}
?>