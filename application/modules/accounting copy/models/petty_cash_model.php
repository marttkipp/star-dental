	<?php

	class Petty_cash_model extends CI_Model 
	{
		public function calculate_balance_brought_forward($date_from)
		{
			$this->db->select('(
	(SELECT SUM(petty_cash_amount) FROM petty_cash WHERE petty_cash_status = 1 AND transaction_type_id = 1 AND petty_cash_date < \''.$date_from.'\')
	-
	(SELECT SUM(petty_cash_amount) FROM petty_cash WHERE petty_cash_status = 1 AND transaction_type_id = 2 AND petty_cash_date < \''.$date_from.'\')
	) AS balance_brought_forward', FALSE); 
			$this->db->where('petty_cash_date < \''.$date_from.'\'');
			$this->db->group_by('balance_brought_forward');
			$query = $this->db->get('petty_cash');
			$row = $query->row();
			return $row->balance_brought_forward;
		}
		
		public function get_petty_cash($where, $table)
		{
			$this->db->select('*');
			// $this->db->join('account', 'petty_cash.account_id = account.account_id', 'left');
			$this->db->where($where);
			$this->db->order_by('petty_cash_date', 'ASC');
			$query = $this->db->get($table);
			
			return $query;
		}
		
		public function get_accounts()
		{
			$this->db->where('account_status = 1');
			$this->db->order_by('account_name');
			$query = $this->db->get('account');
			
			return $query;
		}

		public function get_expense_accounts()
		{

			$this->db->where('account_name = "Expense Accounts"');
			$this->db->order_by('account_name');
			$query = $this->db->get('account');
			$account_id = 0;
			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$account_id = $value->account_id;
				}
			}
			$this->db->where('account_status = 1 AND parent_account = '.$account_id);
			$this->db->order_by('account_name');
			$query_accounts = $this->db->get('account');
			
			return $query_accounts;
		}

		public function get_all_departments()
		{

			$this->db->where('department_status = 1');
			$this->db->order_by('department_id');
			$query = $this->db->get('departments');
			
			
			return $query;
		}

		public function get_account_starting_balance($account_name)
		{

			$this->db->where('account_name = "'.$account_name.'"');
			$this->db->order_by('account_name');
			$query = $this->db->get('account');
			$account_opening_balance = 0;
			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$account_opening_balance = $value->account_opening_balance;
				}
			}
		
			
			return $account_opening_balance;
		}


		
		
		public function record_petty_cash()
		{
			$transaction_type_id = $this->input->post('transaction_type_id');
			// var_dump($transaction_type_id); die();
			
			$array = array(
							"petty_cash_date" => $this->input->post('petty_cash_date'),
							"petty_cash_description" => $this->input->post('petty_cash_description'),
							"petty_cash_amount" => $this->input->post('petty_cash_amount'),			
							'created' => date('Y-m-d H:i:s'),
							"created_by" => $this->session->userdata('personnel_id'),
							"modified_by" => $this->session->userdata('personnel_id')
						);

			if($transaction_type_id == 1 OR $transaction_type_id == 3)
			{
				$transaction_type_id = 1;
				$array["transaction_type_id"] = $transaction_type_id;
				$array["account_id"] = $this->input->post('account_id');
				$array["from_account_id"] =$this->input->post('from_account_id');
			}
			else
			{
				$transaction_type_id = 2;
				$array["transaction_type_id"] = $transaction_type_id;
				$array["account_id"] = $this->input->post('from_account_id');
				$array["from_account_id"] =$this->input->post('account_id');
			}
			
			
			if($this->db->insert('petty_cash', $array))
			{
				//if deposit was select, credit the from account with the same amount
				
				
					$credit_data = array(
										"petty_cash_date" => $this->input->post('petty_cash_date'),
										"petty_cash_description" => $this->input->post('petty_cash_description'),
										"petty_cash_amount" => $this->input->post('petty_cash_amount'),
										'created' => date('Y-m-d H:i:s'),
										"created_by" => $this->session->userdata('personnel_id'),
										"modified_by" => $this->session->userdata('personnel_id')
										);
					if($transaction_type_id == 1 OR $transaction_type_id == 3)
					{
						$transaction_type_id = 2;
						$credit_data["transaction_type_id"] = $transaction_type_id;
						$credit_data["account_id"] = $this->input->post('from_account_id');
						$credit_data["from_account_id"] =$this->input->post('account_id');
					}
					else
					{
						$transaction_type_id = 1;
						$credit_data["transaction_type_id"] = $transaction_type_id;
						$credit_data["account_id"] = $this->input->post('account_id');
						$credit_data["from_account_id"] =$this->input->post('from_account_id');
					}
					if($this->db->insert('petty_cash', $credit_data))
					{
					}
				
				return TRUE;
			}
			
			else
			{
				return FALSE;
			}
		}
		public function get_account_name($from_account_id)
		{
			$account_name = '';
			$this->db->select('account_name');
			$this->db->where('account_id = '.$from_account_id);
			$query = $this->db->get('account');
			
			$account_details = $query->row();
			$account_name = $account_details->account_name;
			
			return $account_name;
		}
		public function get_doctor_name($personnel_id)
		{
			$account_name = '';
			$this->db->select('personnel_fname,personnel_onames');
			$this->db->where('personnel_id = '.$personnel_id);
			$query = $this->db->get('personnel');
			
			$account_details = $query->row();
			$account_name = $account_details->personnel_fname.' '.$account_details->personnel_onames;
			
			return $account_name;
		}
		public function get_creditor_name($creditor_id)
		{
			$account_name = '';
			$this->db->select('creditor_name');
			$this->db->where('creditor_id = '.$creditor_id);
			$query = $this->db->get('creditor');
			
			$account_details = $query->row();
			$account_name = $account_details->creditor_name;
			
			return $account_name;
		}
		public function get_total_deposited($account_id)
		{
			$amount_deposited = 0;
			$this->db->select('SUM(petty_cash_amount) AS total_deposited');
			$this->db->where('transaction_type_id = 1 AND account_id = '.$account_id);
			
			$query = $this->db->get('petty_cash');
			$deposits_row = $query->row();
			$amount_deposited = $deposits_row->total_deposited;
			
			return $amount_deposited;
		}
		public function get_total_spent($account_id)
		{
			$expenditure = 0 ;
			$this->db->select('SUM(petty_cash_amount) AS total_spent');
			$this->db->where('transaction_type_id = 2 AND account_id = '.$account_id);
			
			$query = $this->db->get('petty_cash'); 
			$expenditure_row = $query->row();
			$expenditure = $expenditure_row->total_spent;
			
			return $expenditure;
		}
		public function get_all_cash_accounts($table, $where, $config, $page, $order, $order_method)
		{
			//retrieve all accounts
			$this->db->from($table);
			$this->db->select('*');
			$this->db->where($where);
			$this->db->order_by($order, $order_method);
			$query = $this->db->get('', $config, $page);
			
			return $query;
		}

		public function get_account_payments_transactions($table, $where, $config, $page, $order, $order_method)
		{
			//retrieve all accounts
			$this->db->from($table);
			$this->db->select('*');
			$this->db->where($where);
			$this->db->order_by($order, $order_method);
			$query = $this->db->get('', $config, $page);
			
			return $query;
		}
		public function deactivate_account($account_id)
		{
			$this->db->where('account_id = '.$account_id);
			if($this->db->update('account',array('account_status'=>0)))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		public function activate_account($account_id)
		{
			$this->db->where('account_id = '.$account_id);
			if($this->db->update('account',array('account_status'=>1)))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		public function get_account($account_id)
		{
			$this->db->select('*');
			$this->db->where('account_id = '.$account_id);
			$query = $this->db->get('account');
			
			return $query->row();
		}
		public function update_account($account_id)
		{
			$account_data = array(
						'account_name'=>$this->input->post('account_name'),
						'account_type_id'=>$this->input->post('account_type_id'),
						'parent_account'=>$this->input->post('parent_account'),
						'account_opening_balance'=>$this->input->post('account_balance')
						);
			$this->db->where('account_id = '.$account_id);
			if($this->db->update('account', $account_data))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		public function add_account()
		{
			$account = array(
						'account_name'=>$this->input->post('account_name'),
						'account_opening_balance'=>$this->input->post('account_balance'),
						'parent_account'=>$this->input->post('parent_account'),
						'account_type_id'=>$this->input->post('account_type_id'),
	                    'account_status'=>$this->input->post('account_status')
						);
			if($this->db->insert('account',$account))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		public function get_account_id($account_name)
		{
			$account_id = 0;
			
			$this->db->select('account_id');
			$this->db->where('account_name = "'.$account_name.'"');
			$query = $this->db->get('account');
			
			$bal = $query->row();
			$account_id = $bal->account_id;
			// var_dump($account_id); die();
			return $account_id;
			
		}
		public function get_account_opening_bal($account)
		{
			$opening_bal = 0;
			
			$this->db->select('account_opening_balance');
			$this->db->where('account_id = '.$account);
			$query = $this->db->get('account');
			
			$bal = $query->row();
			$opening_bal = $bal->account_opening_balance;

			return $opening_bal;
			
		}
		public function get_total_opening_bal()
		{
			$opening_bal = 0;
			
			$this->db->select('SUM(account_opening_balance) AS total_opening_bal');
			$query = $this->db->get('account');
			
			$bal = $query->row();
			$opening_bal = $bal->total_opening_bal;

			return $opening_bal;
		}
		public function delete_petty_cash($petty_cash_id)
		{
			$this->db->where('petty_cash_id',$petty_cash_id);
			$query = $this->db->get('petty_cash');

			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$petty_cash_amount = $value->petty_cash_amount;
					$petty_cash_date = $value->petty_cash_date;
					$account_id = $value->account_id;
					$transaction_type_id = $value->transaction_type_id;
					$from_account_id = $value->from_account_id;
					$petty_cash_description = $value->petty_cash_description;

				}
			}


				$update_array = array(
						'petty_cash_delete'=>1
					);
				$this->db->where('petty_cash_id', $petty_cash_id);
				if($this->db->update('petty_cash', $update_array))
				{

					if($transaction_type_id == 1)
		            {
		            	$array = array(
		             					'petty_cash_amount'=>$petty_cash_amount,
		             					'petty_cash_date'=>$petty_cash_date,
		             					'account_id'=>$from_account_id,
		             					'from_account_id'=>$account_id,
		             					'petty_cash_description'=>$petty_cash_description,
		             					'transaction_type_id'=>2
		             				   );
		            }
		            else
		            {

		            	$array = array(
		             					'petty_cash_amount'=>$petty_cash_amount,
		             					'petty_cash_date'=>$petty_cash_date,
		             					'account_id'=>$account_id,
		             					'from_account_id'=>$from_id,
		             					'petty_cash_description'=>$petty_cash_description,
		             					'transaction_type_id'=>1
		             				   );

		            }

		            $update_array2 = array(
											'petty_cash_delete'=>1
										);
					$this->db->where($array);
					if($this->db->update('petty_cash', $update_array2))
					{
						return TRUE;	
					}
					else
					{
						return FALSE;
					}
					
				}
				else{
					return FALSE;
				}
		}
		public function get_type()		
		{
			//retrieve all users
			$this->db->from('account_type');
			$this->db->select('*');
			$this->db->where('account_type_id > 0 ');
			$query = $this->db->get();
			
			return $query;    	
	 
	    }

	    public function get_parent_accounts()		
		{
			//retrieve all users
			$this->db->from('account');
			$this->db->select('*');
			$this->db->where('parent_account = 0');
			$query = $this->db->get();
			
			return $query;    	
	 
	    }

	    public function get_parent_account($parent_account)
	    {
	    	$this->db->from('account');
			$this->db->select('*');
			$this->db->where('account_id = '.$parent_account);
			$query = $this->db->get();
			$account_name = '';
			if($query->num_rows() > 0)  
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$account_name = $value->account_name;
				}
			}

			return $account_name;
	    }

	    public function get_child_accounts($parent_account_name)
	    {
	    	$this->db->from('account');
			$this->db->select('*');
			$this->db->where('account_name = "'.$parent_account_name.'"');
			$query = $this->db->get();
			
			if($query->num_rows() > 0)  
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$account_id = $value->account_id;
				}
				//retrieve all users
				$this->db->from('account');
				$this->db->select('*');
				$this->db->where('parent_account = '.$account_id);
				$query = $this->db->get();
				
				return $query;    	


			}
			else
			{
				return FALSE;
			}

	    }

	    public function get_account_deposit($parent_account_name)
	    {
	    	$this->db->from('account');
			$this->db->select('*');
			$this->db->where('account_name = "'.$parent_account_name.'"');
			$query = $this->db->get();
			$account_opening_balance =0;
			if($query->num_rows() > 0)  
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$account_opening_balance = $value->account_opening_balance;
				}
			}
			
			return $account_opening_balance;

	    }


	    public function get_type_variables($table,$where,$select)		
		{
			//retrieve all users
			$this->db->from($table);
			$this->db->select($select);
			$this->db->where($where);
			$query = $this->db->get();
			
			return $query;    	
	 
	    }

	    public function add_account_payment()
		{
			$account = array(
						'account_to_id'=>$this->input->post('account_to_id'),
						'account_from_id'=>$this->input->post('account_from_id'),
						'amount_paid'=>$this->input->post('amount'),
						'account_payment_description'=>$this->input->post('description'),
	                    'account_to_type'=>$this->input->post('account_to_type'),
	                    'receipt_number'=>$this->input->post('cheque_number'),
	                    'payment_date'=>$this->input->post('payment_date'),
	                    'payment_to'=>$this->input->post('payment_to'),
	                    'created_by'=>$this->session->userdata('personnel_id'),
	                    'created'=>date('Y-m-d')
						);
			// var_dump($account); die();
			if($this->db->insert('account_payments',$account))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}


		public function add_account_invoice()
		{
			$account = array(
						'account_to_id'=>$this->input->post('account_to_id'),
						'account_from_id'=>$this->input->post('account_from_id'),
						'invoice_amount'=>$this->input->post('petty_cash_amount'),
						'department_id'=>$this->input->post('department_id'),
						'account_invoice_description'=>$this->input->post('petty_cash_description'),
	                    'account_to_type'=>1,//$this->input->post('transaction_type_id'),
	                    'invoice_date'=>$this->input->post('petty_cash_date'),
	                    'created_by'=>$this->session->userdata('personnel_id'),
	                    'created'=>date('Y-m-d')
						);
			// var_dump($account); die();
			if($this->db->insert('account_invoices',$account))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		



		public function get_all_provider_invoices($provider_id)
		{
			
			$this->db->from('account_invoices');
			$this->db->select('SUM(invoice_amount) AS invoice_amount,invoice_date,invoice_number');
			$this->db->where('account_to_type = 3 AND account_to_id = '.$provider_id);
			$this->db->order_by('invoice_date','ASC');
			$this->db->group_by('invoice_number');
			$query = $this->db->get();
			return $query;
		}
		public function get_all_payments_provider($provider_id)
		{
			$this->db->from('account_payments');
			$this->db->select('*');
			$this->db->where('account_to_type = 3 AND account_to_id = '.$provider_id.'  AND account_payment_status = 1 ');
			$this->db->order_by('payment_date','ASC');
			$query = $this->db->get();
			return $query;
		}


		

		public function get_provider_statement($provider_id)
		{


			$bills = $this->petty_cash_model->get_all_provider_invoices($provider_id);
			// var_dump($bills); 
			$payments = $this->petty_cash_model->get_all_payments_provider($provider_id);

			$x=0;

			$bills_result = '';
			$last_date = '';
			$current_year = date('Y');
			$total_invoices = $bills->num_rows();
			$invoices_count = 0;
			$total_invoice_balance = 0;
			$total_arrears = 0;
			$total_payment_amount = 0;
			$result = '';
			$total_pardon_amount = 0;
			if($bills->num_rows() > 0)
			{
				foreach ($bills->result() as $key_bills) {
					# code...
					$invoice_date = $key_bills->invoice_date;
					$invoice_number = $key_bills->invoice_number;
					$invoice_amount = $key_bills->invoice_amount;
					$invoice_explode = explode('-', $invoice_date);
					$invoice_year = $invoice_explode[0];
					$invoice_month = $invoice_explode[1];
					// var_dump($bills->result()); die();
					$invoices_count++;
					if($payments->num_rows() > 0)
					{
						foreach ($payments->result() as $payments_key) {
							# code...
							$payment_date = $payments_key->payment_date;
							$payment_explode = explode('-', $payment_date);
							$payment_year = $payment_explode[0];
							$payment_month = $payment_explode[1];
							$payment_amount = $payments_key->amount_paid;

							if(($payment_date <= $invoice_date) && ($payment_date > $last_date) && ($payment_amount > 0))
							{
								$total_arrears -= $payment_amount;
								// var_dump($payment_year); die();
								// if($payment_year >= $current_year)
								// {
									$result .= 
									'
										<tr>
											<td>'.date('d M Y',strtotime($payment_date)).' </td>
											<td>Payment</td>
											<td></td>
											<td></td>
											<td>'.number_format($payment_amount, 2).'</td>
											<td>'.number_format($total_arrears, 2).'</td>
											<td></td>
										</tr> 
									';
								// }
								
								$total_payment_amount += $payment_amount;

							}
						}
					}
					
					//display disbursment if cheque amount > 0
					if($invoice_amount != 0)
					{
						$total_arrears += $invoice_amount;
						$total_invoice_balance += $invoice_amount;
							
						// if($invoice_year >= $current_year)
						// {
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($invoice_date)).' </td>
									<td>Invoice</td>
									<td>'.$invoice_number.'</td>
									<td>'.number_format($invoice_amount, 2).'</td>
									<td></td>
									<td>'.number_format($total_arrears, 2).'</td>
									<td></td>
								</tr> 
							';
						// }
					}
							
					//check if there are any more payments
					if($total_invoices == $invoices_count)
					{
						//get all loan deductions before date
						if($payments->num_rows() > 0)
						{
							foreach ($payments->result() as $payments_key) {
								# code...
								$payment_date = $payments_key->payment_date;

								$payment_explode = explode('-', $payment_date);
								$payment_year = $payment_explode[0];
								$payment_month = $payment_explode[1];
								$payment_amount = $payments_key->amount_paid;

								if(($payment_date > $invoice_date) &&  ($payment_amount > 0))
								{
									$total_arrears -= $payment_amount;
									// if($payment_year >= $current_year)
									// {
										$result .= 
										'
											<tr>
												<td>'.date('d M Y',strtotime($payment_date)).' </td>
												<td>Payment</td>
												<td></td>
												<td></td>
												<td>'.number_format($payment_amount, 2).'</td>
												<td>'.number_format($total_arrears, 2).'</td>
												<td></td>
											</tr> 
										';
									// }
									
									$total_payment_amount += $payment_amount;

								}
							}
						}

						
					}
							$last_date = $invoice_date;
				}
			}	
			else
			{
				//get all loan deductions before date
				if($payments->num_rows() > 0)
				{
					foreach ($payments->result() as $payments_key) {
						# code...
						$payment_date = $payments_key->payment_date;
						$payment_explode = explode('-', $payment_date);
						$payment_year = $payment_explode[0];
						$payment_month = $payment_explode[1];
						$payment_amount = $payments_key->amount_paid;

						if(($payment_amount > 0))
						{
							$total_arrears -= $payment_amount;
							// if($payment_year >= $current_year)
							// {
								$result .= 
								'
									<tr>
										<td>'.date('d M Y',strtotime($payment_date)).' </td>
										<td>Payment</td>
										<td></td>
										<td></td>
										<td>'.number_format($payment_amount, 2).'</td>
										<td>'.number_format($total_arrears, 2).'</td>
										<td></td>
									</tr> 
								';
							// }
							
							$total_payment_amount += $payment_amount;

						}
					}
				}
				

			}
							
			//display loan
			$result .= 
			'
				<tr>
					<th colspan="3">Total</th>
					<th>'.number_format($total_invoice_balance, 2).'</th>
					<th>'.number_format($total_payment_amount, 2).'</th>
					<th>'.number_format($total_arrears, 2).'</th>
					<td></td>
				</tr> 
			';



			$response['total_arrears'] = $total_arrears;
			$response['invoice_date'] = $invoice_date;
			$response['result'] = $result;
			$response['total_payment_amount'] = $total_payment_amount;

			// var_dump($response); die();

			return $response;
		}

		public function get_all_ledger_invoices($creditor_id)
		{	
			$ledger_search = $this->session->userdata('ledger_search');
			$search_add = '';
			$search_payment_add = '';
			if($ledger_search == 1)
			{
				$account = $this->session->userdata('account_id');
				$date_from = $this->session->userdata('date_from');
				$date_to = $this->session->userdata('date_to');

				if(!empty($date_from) AND !empty($date_to))
				{
					$search_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
					$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_add = ' AND created = \''.$date_from.'\'';
					$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_add = ' AND created = \''.$date_to.'\'';
					$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
				}

			}

			
			$this->db->from('account_payments');
			$this->db->select('account_payments.amount_paid AS invoice_amount,account_payments.payment_date AS invoice_date,account_payments.receipt_number AS invoice_number,account_payments.account_payment_description AS account_invoice_description,account_from_id,account_to_id,account_payments.account_payment_id AS account_invoice_id');
			$this->db->where('account_from_id = '.$creditor_id.' AND account_payment_deleted = 0  '.$search_payment_add);
			$this->db->order_by('payment_date','ASC');
			$query = $this->db->get();
			return $query;
		}


		public function get_all_ledger_credits($creditor_id)
		{	
			$ledger_search = $this->session->userdata('ledger_search');
			$search_add = '';
			$search_payment_add = '';
			if($ledger_search == 1)
			{
				$account = $this->session->userdata('account_id');
				$date_from = $this->session->userdata('date_from');
				$date_to = $this->session->userdata('date_to');

				if(!empty($date_from) AND !empty($date_to))
				{
					$search_add =  ' AND (invoice_date >= \''.$date_from.'\' AND invoice_date <= \''.$date_to.'\') ';
					$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_add = ' AND invoice_date = \''.$date_from.'\'';
					$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_add = ' AND invoice_date = \''.$date_to.'\'';
					$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
				}

			}

			
			$this->db->from('account_invoices');
			$this->db->select('*');
			$this->db->where('account_to_id = '.$creditor_id.' AND transaction_type_id = 0 AND account_invoice_deleted = 0  '.$search_add);
			$this->db->order_by('invoice_date','ASC');
			$query = $this->db->get();
			return $query;
		}
		public function get_all_ledger_cash($creditor_id)
		{
			$ledger_search = $this->session->userdata('ledger_search');
			$search_add = '';
			$search_payment_add = '';
			if($ledger_search == 1)
			{
				$account = $this->session->userdata('account_id');
				$date_from = $this->session->userdata('date_from');
				$date_to = $this->session->userdata('date_to');

				if(!empty($date_from) AND !empty($date_to))
				{
					$search_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
					$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_add = ' AND created = \''.$date_from.'\'';
					$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_add = ' AND created = \''.$date_to.'\'';
					$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
				}

			}
			$this->db->from('account_payments');
			$this->db->select('*');
			$this->db->where('account_to_id = '.$creditor_id.' AND account_payment_deleted = 0  '.$search_payment_add);
			$this->db->order_by('payment_date','ASC');
			$query = $this->db->get();
			return $query;
		}


		public function get_all_ledger_cash_received($payment_method_id = null)
		{
			$ledger_search = $this->session->userdata('ledger_search');
			$search_add = '';
			$search_payment_add = '';
			if($ledger_search == 1)
			{
				$account = $this->session->userdata('account_id');
				$date_from = $this->session->userdata('date_from');
				$date_to = $this->session->userdata('date_to');

				if(!empty($date_from) AND !empty($date_to))
				{
					$search_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
					$search_payment_add =  ' AND (payment_created >= \''.$date_from.'\' AND payment_created <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_add = ' AND created = \''.$date_from.'\'';
					$search_payment_add = ' AND payment_created = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_add = ' AND created = \''.$date_to.'\'';
					$search_payment_add = ' AND payment_created = \''.$date_to.'\'';
				}

			}

			if($payment_method_id == null)
			{
				$add = 'payment_method_id = 2';
			}
			else
			{
				$add = 'payment_method_id = '.$payment_method_id;
			}

			$this->db->from('payments');
			$this->db->select('payments.payment_created AS payment_date, MONTH(payments.payment_created) AS payment_month,YEAR(payments.payment_created) AS payment_year, SUM(amount_paid) AS amount_paid ');
			$this->db->where(''.$add.' AND payments.payment_type = 1 AND cancel = 0  '.$search_payment_add);
			$this->db->order_by('payment_created','ASC');
			$this->db->group_by('payment_created','ASC');
			$query = $this->db->get();
			return $query;
		}

		public function get_account_invoice_brought_forward($account_id,$invoice_search)
		{
			
			$this->db->from('account_invoices');
			$this->db->select('SUM(invoice_amount) AS amount');
			$this->db->where('account_invoice_deleted = 0  AND account_from_id = '.$account_id.''.$invoice_search);
			$this->db->order_by('invoice_date','ASC');
			$this->db->group_by('invoice_number');
			$query = $this->db->get();
			$amount = 0;
			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$amount = $value->amount;
				}
			}
			return $amount;
			
		}
		public function get_account_payment_brought_forward($account_id,$invoice_search)
		{


			$this->db->from('account_payments');
			$this->db->select('SUM(amount_paid) AS amount');
			$this->db->where('account_payment_deleted = 0  AND account_from_id = '.$account_id.''.$invoice_search);
			$this->db->order_by('payment_date','ASC');
			$query = $this->db->get();
			$amount = 0;
			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$amount = $value->amount;
				}
			}
			return $amount;


		}

		public function cash_balance_brought_forward($payment_method_id)
		{
			$date_from = $this->session->userdata('date_from');
			$date_to = $this->session->userdata('date_to');

			if(!empty($date_from))
			{
				$invoice_date_from = ' AND account_invoices.invoice_date < "'.$date_from.'" ';
				$search_payment_add = ' AND payment_created < "'.$date_from.'" ';

				$this->db->from('payments');
				$this->db->select('SUM(amount_paid) AS amount_paid ');
				$this->db->where('payment_method_id = '.$payment_method_id.' AND payments.payment_type = 1 AND cancel = 0  '.$search_payment_add);
				$this->db->order_by('payment_created','ASC');

				$query = $this->db->get();
				$collection = 0;
				if($query->num_rows() > 0)
				{
					foreach ($query->result() as $key => $value) {
						# code...
						$collection =  $value->amount_paid;
					}
				}
				return $collection;

			}
			else
			{
				return 0;
			}

		}
		public function get_account_balance_brought_forward($account_id)
		{
			$date_from = $this->session->userdata('date_from');
			$date_to = $this->session->userdata('date_to');

			if(!empty($date_from))
			{
				$invoice_date_from = ' AND account_invoices.invoice_date < "'.$date_from.'" ';
				$payment_date_from = ' AND account_payments.payment_date < "'.$date_from.'" ';
				$invoice_total = $this->get_account_invoice_brought_forward($account_id,$invoice_date_from);
				$payment_total = $this->get_account_payment_brought_forward($account_id,$payment_date_from);



				$balance = $payment_total - $invoice_total;

				return $balance;
			}
			else
			{
				return 0;
			}
		}
		function check_if_parent($account_id,$parent_account)
		{
			$query = $this->db->query('SELECT * FROM account WHERE account_id = '.$account_id.' AND parent_account = (SELECT account_id FROM account WHERE account_name = "'.$parent_account.'")');
			if($query->num_rows() > 0)
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}

		}

		public function get_ledger_statement($account_id,$account_name_item = null)
		{


			$bills = $this->petty_cash_model->get_all_ledger_invoices($account_id);


			$credits = $this->petty_cash_model->get_all_ledger_credits($account_id);

			// check if account is a bank account 
			$is_bank = $this->check_if_parent($account_id,'Bank');

			// var_dump($account_name); die();
			if($account_name_item == 'Cash Account')
			{

			$payments = $this->petty_cash_model->get_all_ledger_cash_received(2);
			$cash_balance_brought_forward = $this->petty_cash_model->cash_balance_brought_forward($account_id);
			}
			else if($account_name_item == 'Mpesa')
			{

			$payments = $this->petty_cash_model->get_all_ledger_cash_received(5);
			$cash_balance_brought_forward = $this->petty_cash_model->cash_balance_brought_forward($account_id);
			}
			else if($account_name_item == 'KCB')
			{

			$payments = $this->petty_cash_model->get_all_ledger_cash_received(7);
			$cash_balance_brought_forward = $this->petty_cash_model->cash_balance_brought_forward($account_id);
			}
			else if($account_name_item == 'EQUITY')
			{

			$payments = $this->petty_cash_model->get_all_ledger_cash_received(7);
			$cash_balance_brought_forward = $this->petty_cash_model->cash_balance_brought_forward($account_id);
			}
			else
			{

				$payments = $this->petty_cash_model->get_all_ledger_cash($account_id);
			}
			$query_opening = $this->get_account_opening_balance($account_id);

			$balance_brought_forward = $this->get_account_balance_brought_forward($account_id);

			$account_opening_balance = 0;
			$created_date = date('Y-m-d');
			$result = '';

			$x=0;

			$bills_result = '';
			$last_date = '';
			$current_year = date('Y');
			$total_invoices = $bills->num_rows();
			$invoices_count = 0;
			$total_invoice_balance = 0;
			$total_arrears = 0;
			$total_payment_amount = 0;
			$total_pardon_amount = 0;


			if($query_opening->num_rows() > 0)
			{
				foreach ($query_opening->result() as $key => $value) {
					# code...
					$account_opening_balance = $value->account_opening_balance;
					$created = $value->created;
				}
			}
			// $creditor_query = $this->creditors_model->get_opening_provider_balance($provider_id);
			if($account_opening_balance < 0)
			{
				// this is deni
				$total_arrears += $account_opening_balance;
				$account_opening_balance = ($account_opening_balance);
				$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($created)).' </td>
									<td colspan=2>Opening Balance</td>
									<td>'.number_format($account_opening_balance, 2).'</td>
									<td></td>
									<td>'.number_format($total_arrears, 2).'</td>
								</tr> 
							';
					$total_invoice_balance = $account_opening_balance;

			}
			else
			{
				$total_arrears -= $account_opening_balance;
				// this is a prepayment
				$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($created)).' </td>
									<td colspan=3>Opening Balance</td>
									<td>'.number_format($account_opening_balance, 2).'</td>
									<td>'.number_format($total_arrears,2).'</td>
								</tr> 
							';
				$total_payment_amount = $account_opening_balance;
			}

			// var_dump($balance_brought_forward); die();
			if($balance_brought_forward < 0)
			{
				// this is deni
				$balance_brought_forward = ($balance_brought_forward);
				$date_from = $this->session->userdata('date_from');
				$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($date_from)).' </td>
									<td colspan=2>Balance B/F</td>
									<td>'.number_format($balance_brought_forward, 2).'</td>
									<td></td>
								</tr> 
							';
				$total_invoice_balance += $balance_brought_forward;

			}
			else
			{
				// this is a prepayment
				$date_from = $this->session->userdata('date_from');
				$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($date_from)).' </td>
									<td colspan=2>Balance B/F</td>
									<td></td>
									<td>'.number_format($balance_brought_forward+$cash_balance_brought_forward, 2).'</td>
								</tr> 
							';
				$total_payment_amount += $balance_brought_forward+$cash_balance_brought_forward;
			}
			
			if($bills->num_rows() > 0)
			{
				foreach ($bills->result() as $key_bills) {
					# code...
					$invoice_date = $key_bills->invoice_date;
					$account_invoice_id = $key_bills->account_invoice_id;
					$invoice_number = $key_bills->invoice_number;
					$invoice_amount = $key_bills->invoice_amount;
					$account_invoice_description = $key_bills->account_invoice_description;
					$account_to_id = $key_bills->account_to_id;
					$account_from_id = $key_bills->account_from_id;
					$invoice_explode = explode('-', $invoice_date);
					$invoice_year = $invoice_explode[0];
					$invoice_month = $invoice_explode[1];
					// var_dump($bills->result()); die();
					$invoices_count++;
					if($payments->num_rows() > 0)
					{
						foreach ($payments->result() as $payments_key) {
							# code...
							$account_payment_id = $payments_key->account_payment_id;
							$payment_date = $payments_key->payment_date;
							$payment_explode = explode('-', $payment_date);
							$payment_year = $payment_explode[0];
							$payment_month = $payment_explode[1];
							$payment_amount = $payments_key->amount_paid;
							$account_from_id = $payments_key->account_from_id;
							$account_payment_description = $payments_key->account_payment_description;
							if($is_bank)
							{
							$account_name = $account_name_item.' Revenue';//$this->get_account_name($account_from_id);
							}
							else
							{
							$account_name = $this->get_account_name($account_from_id);

							}

							if(($payment_date <= $invoice_date) && ($payment_date > $last_date) && ($payment_amount > 0))
							{
								$total_arrears += $payment_amount;
								// var_dump($payment_year); die();
								// if($payment_year >= $current_year)
								// {
									$result .= 
									'
										<tr>
											<td>'.date('d M Y',strtotime($payment_date)).' </td>
											<td>'.$account_name.'</td>
											<td>'.$account_payment_description.'</td>
											<td></td>
											<td>'.number_format($payment_amount, 2).'</td>
											<td>'.number_format($total_arrears, 2).'</td>
										</tr> 
									';
								// }
								
								$total_payment_amount += $payment_amount;

							}
						}
					}
					
					//display disbursment if cheque amount > 0
					if($invoice_amount != 0)
					{
						$total_arrears -= $invoice_amount;
						$total_invoice_balance += $invoice_amount;
						$account_name = $this->get_account_name($account_to_id);
						// if($invoice_year >= $current_year)
						// {


							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($invoice_date)).' </td>
									<td>'.$account_name.'</td>
									<td>'.$account_invoice_description.'</td>
									<td>'.number_format($invoice_amount, 2).'</td>
									<td></td>
									<td>'.number_format($total_arrears,2).'</td>
									<td><a href="'.site_url().'delete-payment-ledger-entry/'.$account_invoice_id.'" class="btn btn-sm btn-danger fa fa-trash" onclick="return confirm(\'Do you really want delete this entry?\');"></a></td>
									
								</tr> 
							';
						// }
					}

					if($credits->num_rows() > 0)
					{
						foreach ($credits->result() as $credits_key) {
							# code...
							$account_invoice_id_credit = $credits_key->account_invoice_id;
							$invoice_date_credit = $credits_key->invoice_date;
							$invoice_explode = explode('-', $invoice_date_credit);
							$invoice_year = $invoice_explode[0];
							$invoice_month = $invoice_explode[1];
							$invoice_amount_credit = $credits_key->invoice_amount;
							$account_from_id = $credits_key->account_from_id;
							$account_to_type = $credits_key->account_to_type;
							$account_to_id = $credits_key->account_to_id;
							$account_invoice_description = $credits_key->account_invoice_description;
							if($account_to_type == 3)
							{
								$account_from_name = 'Provider';
							}
							else
							{

								$account_from_name = $this->get_account_name($account_from_id);
							}
							
							$account_to_name = $this->get_account_name($account_to_id);


							if(($invoice_date_credit <= $invoice_date) && ($invoice_date_credit > $last_date) && ($invoice_amount_credit > 0))
							{
								$total_arrears -= $invoice_amount_credit;
								// var_dump($invoice_year); die();
								// if($invoice_year >= $current_year)
								// {
									$result .= 
									'
										<tr>
											<td>'.date('d M Y',strtotime($invoice_date_credit)).' </td>
											<td>'.$account_from_name.' - '.$account_to_name.'</td>
											<td>'.$account_invoice_description.'</td>
											<td></td>
											<td>'.number_format($invoice_amount_credit, 2).'</td>
											<td>'.number_format($total_arrears, 2).'</td>
											<td><a href="'.site_url().'delete-invoice-ledger-entry/'.$account_invoice_id_credit.'" class="btn btn-sm btn-danger fa fa-trash" onclick="return confirm(\'Do you really want delete this entry?\');"></a></td>
										</tr> 
									';
								// }
								
								$total_invoice_amount += $invoice_amount;

							}
						}
					}
							
					//check if there are any more payments
					if($total_invoices == $invoices_count)
					{
						//get all loan deductions before date
						if($payments->num_rows() > 0)
						{
							foreach ($payments->result() as $payments_key) {
								# code...
								$payment_date = $payments_key->payment_date;
								$account_payment_id = $payments_key->account_payment_id;
								$payment_explode = explode('-', $payment_date);
								$payment_year = $payment_explode[0];
								$payment_month = $payment_explode[1];
								$account_from_id = $payments_key->account_from_id;
								$account_payment_description = $payments_key->account_payment_description;
								if($is_bank)
								{
								$account_name = $account_name_item.' Revenue';//$this->get_account_name($account_from_id);
								}
								else
								{
								$account_name = $this->get_account_name($account_from_id);

								}
								$payment_amount = $payments_key->amount_paid;

								if(($payment_date > $invoice_date) &&  ($payment_amount > 0))
								{
									$total_arrears -= $payment_amount;
									// if($payment_year >= $current_year)
									// {
										$result .= 
													'
														<tr>
															<td>'.date('d M Y',strtotime($payment_date)).' </td>
															<td>'.$account_name.'</td>
															<td>'.$account_payment_description.'</td>
															<td></td>
															<td>'.number_format($payment_amount, 2).'</td>
															
														</tr> 
													';
									// }
									
									$total_payment_amount += $payment_amount;

								}
							}
						}

						
					}
							$last_date = $invoice_date;
				}
			}	
			else
			{
				//get all loan deductions before date
				if($payments->num_rows() > 0)
				{
					foreach ($payments->result() as $payments_key) {
						# code...
						$account_payment_id = $payments_key->account_payment_id;
						$payment_date = $payments_key->payment_date;
						$payment_explode = explode('-', $payment_date);
						$payment_year = $payment_explode[0];
						$payment_month = $payment_explode[1];
						$payment_amount = $payments_key->amount_paid;
						$account_from_id = $payments_key->account_from_id;
						$account_payment_description = $payments_key->account_payment_description;
						if($is_bank)
						{
						$account_name = $account_name_item.' Revenue';//$this->get_account_name($account_from_id);
						}
						else
						{
						$account_name = $this->get_account_name($account_from_id);

						}

						if(($payment_amount > 0))
						{
							$total_arrears -= $payment_amount;
							// if($payment_year >= $current_year)
							// {
								$result .= 
									'
										<tr>
											<td>'.date('d M Y',strtotime($payment_date)).' </td>
											<td>'.$account_name.'</td>
											<td>'.$account_payment_description.'</td>
											<td></td>
											<td>'.number_format($payment_amount, 2).'</td>
										</tr> 
									';
							// }
							
							$total_payment_amount += $payment_amount;

						}
					}
				}
				

			}
							
			//display loan
			$result .= 
			'
				<tr>
					<th colspan="3">Total</th>
					<th>'.number_format($total_invoice_balance, 2).'</th>
					<th>'.number_format($total_payment_amount, 2).'</th>
					
				</tr> 
			';
			$result .= 
		'
			<tr>
				<th colspan="3"></th>
				<th colspan="2" style="text-align:center;">'.number_format($total_payment_amount-$total_invoice_balance, 2).'</th>
			</tr> 
		';



			$response['total_arrears'] = $total_arrears;
			$response['invoice_date'] = $invoice_date;
			$response['result'] = $result;
			$response['total_payment_amount'] = $total_payment_amount;

			// var_dump($response); die();

			return $response;
		}
		public function get_all_petty_cash_invoices($creditor_id)
		{	
			$ledger_search = $this->session->userdata('accounts_petty_search');
			$search_add = '';
			$search_payment_add = '';
			if($ledger_search == 1)
			{
				$account = $this->session->userdata('account_id');
				$date_from = $this->session->userdata('date_from');
				$date_to = $this->session->userdata('date_to');

				if(!empty($date_from) AND !empty($date_to))
				{
					$search_add =  ' AND (invoice_date >= \''.$date_from.'\' AND invoice_date <= \''.$date_to.'\') ';
					$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_add = ' AND invoice_date = \''.$date_from.'\'';
					$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_add = ' AND invoice_date = \''.$date_to.'\'';
					$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
				}

			}
			else
			{
				$date = date('Y-m-01');
				$a_date = date('Y-m-d');
				$end_date = date("Y-m-d", strtotime($a_date));
				
				$search_add .= ' AND invoice_date BETWEEN "'.$date.'" AND "'.$end_date.'"';
			}

			
			$this->db->from('account_invoices');
			$this->db->select('invoice_amount,invoice_date,invoice_number,account_invoice_description,account_from_id,account_to_id,account_invoice_id');
			$this->db->where('account_invoice_deleted = 0 AND account_invoices.account_to_type = 1 AND account_from_id = '.$creditor_id.' '.$search_add);
			$this->db->order_by('invoice_date','ASC');
			// $this->db->group_by('invoice_number');
			$query = $this->db->get();
			return $query;
		}
		public function get_all_payments_petty_cash($creditor_id)
		{
			$ledger_search = $this->session->userdata('accounts_petty_search');
			$search_add = '';
			$search_payment_add = '';
			if($ledger_search == 1)
			{
				$account = $this->session->userdata('account_id');
				$date_from = $this->session->userdata('date_from');
				$date_to = $this->session->userdata('date_to');

				if(!empty($date_from) AND !empty($date_to))
				{
					$search_add =  ' AND (invoice_date >= \''.$date_from.'\' AND invoice_date <= \''.$date_to.'\') ';
					$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_add = ' AND invoice_date = \''.$date_from.'\'';
					$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_add = ' AND invoice_date = \''.$date_to.'\'';
					$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
				}

			}
			else
			{
				$date = date('Y-m-01');
				$a_date = date('Y-m-d');
				$end_date = date("Y-m-d", strtotime($a_date));
				
				$search_payment_add .= ' AND payment_date BETWEEN "'.$date.'" AND "'.$end_date.'"';
			}



			$this->db->from('account_payments');
			$this->db->select('*');
			$this->db->where('account_to_id = '.$creditor_id.' AND account_to_type = 1 AND account_payment_deleted = 0  '.$search_payment_add);
			$this->db->order_by('payment_date','ASC');
			$query = $this->db->get();
			return $query;
		}


		public function get_account_opening_balance($account_id)
		{
			$this->db->from('account');
			$this->db->select('account_opening_balance');
			$this->db->where('account_id = '.$account_id);
			$query_opening = $this->db->get('');
			

			return $query_opening;

		}

		public function get_petty_cash_opening_balance($account_id)
		{

			$ledger_search = $this->session->userdata('accounts_petty_search');
			$search_add = '';
			$search_payment_add = '';
			if($ledger_search == 1)
			{
				$account = $this->session->userdata('account_id');
				$date_from = $this->session->userdata('date_from');
				$date_to = $this->session->userdata('date_to');

				if(!empty($date_from) AND !empty($date_to))
				{
					$search_add =  ' AND invoice_date < \''.$date_from.'\' ';
					$search_payment_add =  ' AND payment_date < \''.$date_from.'\'';
				}
				else if(!empty($date_from))
				{
					$search_add =  ' AND invoice_date < \''.$date_from.'\' ';
					$search_payment_add =  ' AND payment_date < \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_add =  ' AND invoice_date < \''.$date_to.'\' ';
					$search_payment_add =  ' AND payment_date < \''.$date_to.'\'';
				}

			}
			else
			{
				$date = date('Y-m-01');
				$a_date = date('Y-m-d');
				$end_date = date("Y-m-d", strtotime($a_date));
				
				$search_add =  ' AND invoice_date < \''.$date.'\' ';
				$search_payment_add =  ' AND payment_date < \''.$date.'\'';
			}
			// $date = date('Y-m-01');



			$search_add = ' AND invoice_date < \''.$date.'\'';

			$this->db->from('account_invoices');
			$this->db->select('SUM(invoice_amount) AS total_invoice');
			$this->db->where('account_invoice_deleted = 0 AND account_invoices.account_to_type = 1 AND account_from_id = '.$account_id.' '.$search_add);
			$this->db->order_by('invoice_date','ASC');
			// $this->db->group_by('invoice_number');
			$total_invoice = 0;
			$query = $this->db->get();
			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$total_invoice = $value->total_invoice;
				}
			}

			// $search_payment_add = ' AND payment_date < \''.$date.'\'';

			$this->db->from('account_payments');
			$this->db->select('SUM(amount_paid) AS total_paid');
			$this->db->where('account_to_id = '.$account_id.' AND account_to_type = 1 AND account_payment_deleted = 0  '.$search_payment_add);
			$this->db->order_by('payment_date','ASC');
			$query_payment = $this->db->get();
			$total_paid = 0;
			if($query_payment->num_rows() > 0)
			{
				foreach ($query_payment->result() as $key => $value) {
					# code...
					$total_paid = $value->total_paid;
				}
			}


			$this->db->from('account');
			$this->db->select('account_opening_balance');
			$this->db->where('account_id = '.$account_id);
			$query_opening = $this->db->get('');

			$account_opening_balance = 0;
			if($query_payment->num_rows() > 0)
			{
				foreach ($query_payment->result() as $key => $value) {
					# code...
					$account_opening_balance = $value->account_opening_balance;
				}
			}

			// var_dump($total_paid); die();
			$balance = $account_opening_balance + $total_paid - $total_invoice;

			return $balance;

		}

		public function get_petty_cash_statement($account_id)
		{
			$date_month = date('Y-m-01');

			$bills = $this->petty_cash_model->get_all_petty_cash_invoices($account_id);
			// var_dump($bills->num_rows()); 
			$payments = $this->petty_cash_model->get_all_payments_petty_cash($account_id);


			$petty_cash_opening_balance = $this->petty_cash_model->get_petty_cash_opening_balance($account_id);

			$x=0;

			$bills_result = '';
			$last_date = '';
			$current_year = date('Y');
			$total_invoices = $bills->num_rows();
			$invoices_count = 0;
			$total_invoice_balance = 0;
			$total_arrears = 0;
			$total_payment_amount = 0;
			$result = '';
			$total_pardon_amount = 0;

			if($petty_cash_opening_balance > 0)
			{
				// this is a debit on the accounnt

				$total_arrears += $petty_cash_opening_balance;
				$total_payment_amount += $petty_cash_opening_balance;
				$result .= 
						'
							<tr>
								<td colspan=3>Account opening balance as at '.date('d M Y',strtotime($date_month)).'</td>
								<td>'.number_format($petty_cash_opening_balance, 2).'</td>
								<td></td>
								<td>'.number_format($total_arrears, 2).'</td>
							</tr> 
						';
			}
			else
			{
				// this is a credit on the account
				$total_arrears -= $petty_cash_opening_balance;
				$total_invoice_balance += $petty_cash_opening_balance;
				$result .= 
						'
							<tr>
								<td colspan=3>Account opening balance as at '.date('Y-m-d').'</td>
								<td></td>
								<td>'.number_format($petty_cash_opening_balance, 2).'</td>
								<td>'.number_format($total_arrears, 2).'</td>
							</tr> 
						';

			}

			

			// var_dump($total_arrears); die();
			if($bills->num_rows() > 0)
			{
				foreach ($bills->result() as $key_bills) {
					# code...
					$invoice_date = $key_bills->invoice_date;
					$account_invoice_id = $key_bills->account_invoice_id;
					$invoice_number = $key_bills->invoice_number;
					$invoice_amount = $key_bills->invoice_amount;
					$account_invoice_description = $key_bills->account_invoice_description;
					$account_to_id = $key_bills->account_to_id;
					$account_from_id = $key_bills->account_from_id;
					$invoice_explode = explode('-', $invoice_date);
					$invoice_year = $invoice_explode[0];
					$invoice_month = $invoice_explode[1];
					// var_dump($bills->result()); die();
					$invoices_count++;
					if($payments->num_rows() > 0)
					{
						foreach ($payments->result() as $payments_key) {
							# code...
							$account_payment_id = $payments_key->account_payment_id;
							$payment_date = $payments_key->payment_date;
							$payment_explode = explode('-', $payment_date);
							$payment_year = $payment_explode[0];
							$payment_month = $payment_explode[1];
							$payment_amount = $payments_key->amount_paid;
							$account_from_id = $payments_key->account_from_id;
							$account_payment_description = $payments_key->account_payment_description;
							$account_name = $this->get_account_name($account_from_id);

							if(($payment_date <= $invoice_date) && ($payment_date > $last_date) && ($payment_amount > 0))
							{
								$total_arrears += $payment_amount;
								// var_dump($payment_year); die();
								// if($payment_year >= $current_year)
								// {
								if($payment_date == date('Y-m-d'))
								{
									$add_payment = '<td><a href="'.site_url().'delete-payment-entry/'.$account_payment_id.'" class="btn btn-sm btn-danger fa fa-trash" onclick="return confirm(\'Do you really want delete this entry?\');"></a></td>';
								}
								else
								{
									$add_payment = '';
								}
									$result .= 
									'
										<tr>
											<td>'.date('d M Y',strtotime($payment_date)).' </td>
											<td>'.$account_name.'</td>
											<td>'.$account_payment_description.'</td>
											<td>'.number_format($payment_amount, 2).'</td>
											<td></td>											
											<td>'.number_format($total_arrears, 2).'</td>
											'.$add_payment.'
										</tr> 
									';
								// }
								
								$total_payment_amount += $payment_amount;

							}
						}
					}
					
					//display disbursment if cheque amount > 0
					if($invoice_amount != 0)
					{
						$total_arrears -= $invoice_amount;
						$total_invoice_balance += $invoice_amount;
						$account_name = $this->get_account_name($account_to_id);
						// if($invoice_year >= $current_year)
						// {

							if($invoice_date == date('Y-m-d'))
							{
								$add_invoice = '<td><a href="'.site_url().'delete-invoice-entry/'.$account_invoice_id.'" class="btn btn-sm btn-danger fa fa-trash" onclick="return confirm(\'Do you really want delete this entry?\');"></a></td>';
							}
							else
							{
								$add_invoice = '';
							}
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($invoice_date)).' </td>
									<td>'.$account_name.'</td>
									<td>'.$account_invoice_description.'</td>
									<td></td>
									<td>'.number_format($invoice_amount, 2).'</td>	
									<td>'.number_format($total_arrears, 2).'</td>
									'.$add_invoice.'
								</tr> 
							';
						// }
					}
						// var_dump($total_arrears); die();
					//check if there are any more payments
					
							$last_date = $invoice_date;
				}
				if($total_invoices == $invoices_count)
					{
						//get all loan deductions before date
						if($payments->num_rows() > 0)
						{
							foreach ($payments->result() as $payments_key) {
								# code...
								$payment_date = $payments_key->payment_date;
								$account_payment_id = $payments_key->account_payment_id;
								$payment_explode = explode('-', $payment_date);
								$payment_year = $payment_explode[0];
								$payment_month = $payment_explode[1];
								$account_from_id = $payments_key->account_from_id;
								$account_payment_description = $payments_key->account_payment_description;
								$account_name = $this->get_account_name($account_from_id);
								$payment_amount = $payments_key->amount_paid;

								if(($payment_date > $invoice_date) &&  ($payment_amount > 0))
								{
									$total_arrears += $payment_amount;

									if($payment_date == date('Y-m-d'))
									{
										$add_payment = '<td><a href="'.site_url().'delete-payment-entry/'.$account_payment_id.'" class="btn btn-sm btn-danger fa fa-trash" onclick="return confirm(\'Do you really want delete this entry?\');"></a></td>';
									}
									else
									{
										$add_payment = '';
									}
												// if($payment_year >= $current_year)
									// {
										$result .= 
													'
														<tr>
															<td>'.date('d M Y',strtotime($payment_date)).' </td>
															<td>'.$account_name.'</td>
															<td>'.$account_payment_description.'</td>
															<td>'.number_format($payment_amount, 2).'</td>
															<td></td>
															<td>'.number_format($total_arrears,2).'</td>
															'.$add_payment.'
														</tr> 
													';
									// }
									
									$total_payment_amount += $payment_amount;

								}
							}
						}

						
					}
			}	
			else
			{
				//get all loan deductions before date
				if($payments->num_rows() > 0)
				{
					foreach ($payments->result() as $payments_key) {
						# code...
						$account_payment_id = $payments_key->account_payment_id;
						$payment_date = $payments_key->payment_date;
						$payment_explode = explode('-', $payment_date);
						$payment_year = $payment_explode[0];
						$payment_month = $payment_explode[1];
						$payment_amount = $payments_key->amount_paid;
						$account_from_id = $payments_key->account_from_id;
						$account_payment_description = $payments_key->account_payment_description;
						$account_name = $this->get_account_name($account_from_id);

						if($payment_date == date('Y-m-d'))
						{
							$add_payment = '<td><a href="'.site_url().'delete-payment-entry/'.$account_payment_id.'" class="btn btn-sm btn-danger fa fa-trash" onclick="return confirm(\'Do you really want delete this entry?\');"></a></td>';
						}
						else
						{
							$add_payment = '';
						}
						if(($payment_amount > 0))
						{
							$total_arrears += $payment_amount;
							// if($payment_year >= $current_year)
							// {
								$result .= 
									'
										<tr>
											<td>'.date('d M Y',strtotime($payment_date)).' </td>
											<td>'.$account_name.'</td>
											<td>'.$account_payment_description.'</td>
											<td>'.number_format($payment_amount, 2).'</td>
											<td></td>
											<td>'.number_format($total_arrears, 2).'</td>

											'.$add_payment.'
										</tr> 
									';
							// }
							
							$total_payment_amount += $payment_amount;

						}
					}
				}
				

			}
							
			//display loan
			$result .= 
			'
				<tr>
					<th colspan="3">Subtotals</th>
					<th>'.number_format($total_payment_amount, 2).'</th>
					<th>'.number_format($total_invoice_balance, 2).'</th>
					<td></td>
				</tr> 
			';

				$result .= 
			'
				<tr>
					<th colspan="3">Total</th>
					<th colspan="2" class="center-align">'.number_format($total_payment_amount-$total_invoice_balance, 2).'</th>
				</tr> 
			';




			$response['total_arrears'] = $total_arrears;
			$response['invoice_date'] = $invoice_date;
			$response['result'] = $result;
			$response['total_payment_amount'] = $total_payment_amount;

			// var_dump($response); die();

			return $response;
		}

		public function print_petty_cash_statement($account_id)
		{
			
			$date_month = date('Y-m-01');

			$bills = $this->petty_cash_model->get_all_petty_cash_invoices($account_id);
			// var_dump($bills->num_rows()); 
			$payments = $this->petty_cash_model->get_all_payments_petty_cash($account_id);


			$petty_cash_opening_balance = $this->petty_cash_model->get_petty_cash_opening_balance($account_id);

			$x=0;

			$bills_result = '';
			$last_date = '';
			$current_year = date('Y');
			$total_invoices = $bills->num_rows();
			$invoices_count = 0;
			$total_invoice_balance = 0;
			$total_arrears = 0;
			$total_payment_amount = 0;
			$result = '';
			$total_pardon_amount = 0;

			if($petty_cash_opening_balance > 0)
			{
				// this is a debit on the accounnt

				$total_arrears += $petty_cash_opening_balance;
				$total_payment_amount += $petty_cash_opening_balance;
				$result .= 
						'
							<tr>
								<td colspan=3>Account opening balance as at '.date('d M Y',strtotime($date_month)).'</td>
								<td>'.number_format($petty_cash_opening_balance, 2).'</td>
								<td></td>
								<td>'.number_format($total_arrears, 2).'</td>
							</tr> 
						';
			}
			else
			{
				// this is a credit on the account
				$total_arrears -= $petty_cash_opening_balance;
				$total_invoice_balance += $petty_cash_opening_balance;
				$result .= 
						'
							<tr>
								<td colspan=3>Account opening balance as at '.date('Y-m-d').'</td>
								<td></td>
								<td>'.number_format($petty_cash_opening_balance, 2).'</td>
								<td>'.number_format($total_arrears, 2).'</td>
							</tr> 
						';

			}

			

			// var_dump($total_arrears); die();
			if($bills->num_rows() > 0)
			{
				foreach ($bills->result() as $key_bills) {
					# code...
					$invoice_date = $key_bills->invoice_date;
					$account_invoice_id = $key_bills->account_invoice_id;
					$invoice_number = $key_bills->invoice_number;
					$invoice_amount = $key_bills->invoice_amount;
					$account_invoice_description = $key_bills->account_invoice_description;
					$account_to_id = $key_bills->account_to_id;
					$account_from_id = $key_bills->account_from_id;
					$invoice_explode = explode('-', $invoice_date);
					$invoice_year = $invoice_explode[0];
					$invoice_month = $invoice_explode[1];
					// var_dump($bills->result()); die();
					$invoices_count++;
					if($payments->num_rows() > 0)
					{
						foreach ($payments->result() as $payments_key) {
							# code...
							$account_payment_id = $payments_key->account_payment_id;
							$payment_date = $payments_key->payment_date;
							$payment_explode = explode('-', $payment_date);
							$payment_year = $payment_explode[0];
							$payment_month = $payment_explode[1];
							$payment_amount = $payments_key->amount_paid;
							$account_from_id = $payments_key->account_from_id;
							$account_payment_description = $payments_key->account_payment_description;
							$account_name = $this->get_account_name($account_from_id);

							if(($payment_date <= $invoice_date) && ($payment_date > $last_date) && ($payment_amount > 0))
							{
								$total_arrears += $payment_amount;
								// var_dump($payment_year); die();
								// if($payment_year >= $current_year)
								// {
								
									$result .= 
									'
										<tr>
											<td>'.date('d M Y',strtotime($payment_date)).' </td>
											<td>'.$account_name.'</td>
											<td>'.$account_payment_description.'</td>
											<td>'.number_format($payment_amount, 2).'</td>
											<td></td>											
											<td>'.number_format($total_arrears, 2).'</td>
										</tr> 
									';
								// }
								
								$total_payment_amount += $payment_amount;

							}
						}
					}
					
					//display disbursment if cheque amount > 0
					if($invoice_amount != 0)
					{
						$total_arrears -= $invoice_amount;
						$total_invoice_balance += $invoice_amount;
						$account_name = $this->get_account_name($account_to_id);
						// if($invoice_year >= $current_year)
						// {

							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($invoice_date)).' </td>
									<td>'.$account_name.'</td>
									<td>'.$account_invoice_description.'</td>
									<td></td>
									<td>'.number_format($invoice_amount, 2).'</td>	
									<td>'.number_format($total_arrears, 2).'</td>
								</tr> 
							';
						// }
					}
							
					//check if there are any more payments
					if($total_invoices == $invoices_count)
					{
						//get all loan deductions before date
						if($payments->num_rows() > 0)
						{
							foreach ($payments->result() as $payments_key) {
								# code...
								$payment_date = $payments_key->payment_date;
								$account_payment_id = $payments_key->account_payment_id;
								$payment_explode = explode('-', $payment_date);
								$payment_year = $payment_explode[0];
								$payment_month = $payment_explode[1];
								$account_from_id = $payments_key->account_from_id;
								$account_payment_description = $payments_key->account_payment_description;
								$account_name = $this->get_account_name($account_from_id);
								$payment_amount = $payments_key->amount_paid;

								if(($payment_date > $invoice_date) &&  ($payment_amount > 0))
								{
									$total_arrears -= $payment_amount;

									
												// if($payment_year >= $current_year)
									// {
										$result .= 
													'
														<tr>
															<td>'.date('d M Y',strtotime($payment_date)).' </td>
															<td>'.$account_name.'</td>
															<td>'.$account_payment_description.'</td>
															<td>'.number_format($payment_amount, 2).'</td>
															<td></td>
														</tr> 
													';
									// }
									
									$total_payment_amount += $payment_amount;

								}
							}
						}

						
					}
							$last_date = $invoice_date;
				}
			}	
			else
			{
				//get all loan deductions before date
				if($payments->num_rows() > 0)
				{
					foreach ($payments->result() as $payments_key) {
						# code...
						$account_payment_id = $payments_key->account_payment_id;
						$payment_date = $payments_key->payment_date;
						$payment_explode = explode('-', $payment_date);
						$payment_year = $payment_explode[0];
						$payment_month = $payment_explode[1];
						$payment_amount = $payments_key->amount_paid;
						$account_from_id = $payments_key->account_from_id;
						$account_payment_description = $payments_key->account_payment_description;
						$account_name = $this->get_account_name($account_from_id);

						
						if(($payment_amount > 0))
						{
							$total_arrears -= $payment_amount;
							// if($payment_year >= $current_year)
							// {
								$result .= 
									'
										<tr>
											<td>'.date('d M Y',strtotime($payment_date)).' </td>
											<td>'.$account_name.'</td>
											<td>'.$account_payment_description.'</td>
											<td>'.number_format($payment_amount, 2).'</td>
											<td></td>
										</tr> 
									';
							// }
							
							$total_payment_amount += $payment_amount;

						}
					}
				}
				

			}
							
			//display loan
			$result .= 
			'
				<tr>
					<th colspan="3">Subtotals</th>
					<th>'.number_format($total_payment_amount, 2).'</th>
					<th>'.number_format($total_invoice_balance, 2).'</th>
					<td></td>
				</tr> 
			';

				$result .= 
			'
				<tr>
					<th colspan="3">Total</th>
					<th colspan="2" class="center-align">'.number_format($total_payment_amount-$total_invoice_balance, 2).'</th>
				</tr> 
			';




			$response['total_arrears'] = $total_arrears;
			$response['invoice_date'] = $invoice_date;
			$response['result'] = $result;
			$response['total_payment_amount'] = $total_payment_amount;

			// var_dump($response); die();

			return $response;
		
		}

	}
	?>