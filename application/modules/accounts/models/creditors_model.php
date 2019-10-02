<?php

class Creditors_model extends CI_Model 
{	
	
	/*
	*	Add a new creditor
	*
	*/
	public function add_creditor()
	{
		$creditor_type_id = $this->input->post('creditor_type_id');

		if(isset($creditor_type_id))
		{
			$creditor_type_id = 1;
		}
		else
		{
			$creditor_type_id = 0;
		}
		$data = array(
			'creditor_name'=>$this->input->post('creditor_name'),
			'creditor_email'=>$this->input->post('creditor_email'),
			'creditor_phone'=>$this->input->post('creditor_phone'),
			'creditor_location'=>$this->input->post('creditor_location'),
			'creditor_building'=>$this->input->post('creditor_building'),
			'creditor_floor'=>$this->input->post('creditor_floor'),
			'creditor_address'=>$this->input->post('creditor_address'),
			'creditor_post_code'=>$this->input->post('creditor_post_code'),
			'creditor_city'=>$this->input->post('creditor_city'),
			'opening_balance'=>$this->input->post('opening_balance'),
			'creditor_contact_person_name'=>$this->input->post('creditor_contact_person_name'),
			'creditor_contact_person_onames'=>$this->input->post('creditor_contact_person_onames'),
			'creditor_contact_person_phone1'=>$this->input->post('creditor_contact_person_phone1'),
			'creditor_contact_person_phone2'=>$this->input->post('creditor_contact_person_phone2'),
			'creditor_contact_person_email'=>$this->input->post('creditor_contact_person_email'),
			'creditor_description'=>$this->input->post('creditor_description'),
			'branch_code'=>$this->session->userdata('branch_code'),
			'created_by'=>$this->session->userdata('creditor_id'),
			'debit_id'=>$this->input->post('debit_id'),
			'modified_by'=>$this->session->userdata('creditor_id'),
			'creditor_type_id'=>$creditor_type_id,
			'created'=>date('Y-m-d H:i:s')
		);
		
		if($this->db->insert('creditor', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Update an existing creditor
	*	@param string $image_name
	*	@param int $creditor_id
	*
	*/
	public function edit_creditor($creditor_id)
	{
		$data = array(
			'creditor_name'=>$this->input->post('creditor_name'),
			'creditor_email'=>$this->input->post('creditor_email'),
			'creditor_phone'=>$this->input->post('creditor_phone'),
			'creditor_location'=>$this->input->post('creditor_location'),
			'creditor_building'=>$this->input->post('creditor_building'),
			'creditor_floor'=>$this->input->post('creditor_floor'),
			'creditor_address'=>$this->input->post('creditor_address'),
			'creditor_post_code'=>$this->input->post('creditor_post_code'),
			'creditor_city'=>$this->input->post('creditor_city'),
			'opening_balance'=>$this->input->post('opening_balance'),
			'creditor_contact_person_name'=>$this->input->post('creditor_contact_person_name'),
			'creditor_contact_person_onames'=>$this->input->post('creditor_contact_person_onames'),
			'creditor_contact_person_phone1'=>$this->input->post('creditor_contact_person_phone1'),
			'creditor_contact_person_phone2'=>$this->input->post('creditor_contact_person_phone2'),
			'creditor_contact_person_email'=>$this->input->post('creditor_contact_person_email'),
			'creditor_description'=>$this->input->post('creditor_description'),
			'debit_id'=>$this->input->post('debit_id'),
			'modified_by'=>$this->session->userdata('creditor_id'),
		);
		
		$this->db->where('creditor_id', $creditor_id);
		if($this->db->update('creditor', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	get a single creditor's details
	*	@param int $creditor_id
	*
	*/
	public function get_creditor($creditor_id)
	{
		//retrieve all users
		$this->db->from('creditor');
		$this->db->select('*');
		$this->db->where('creditor_id = '.$creditor_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Retrieve all creditor
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_creditors($table, $where, $per_page, $page, $order = 'creditor_name', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	/*
	*	Retrieve all creditor
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_providers($table, $where, $per_page, $page, $order = 'personnel_fname', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	/*
	*	Retrieve all creditor
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_creditors_account($table, $where, $per_page, $page, $order = 'creditor_name', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	public function get_creditors_detail_summary($where, $table)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by('creditor_name', 'ASC');
		$query = $this->db->get('');
		
		return $query;
	}

	public function calculate_balance_brought_forward($date_from,$creditor_id)
	{
		$this->db->select('(
(SELECT SUM(creditor_account_amount) FROM creditor_account WHERE creditor_account_status = 1 AND transaction_type_id = 1 AND creditor_account_date < \''.$date_from.'\' AND creditor_id= '.$creditor_id.')
-
(SELECT SUM(creditor_account_amount) FROM creditor_account WHERE creditor_account_status = 1 AND transaction_type_id = 2 AND creditor_account_date < \''.$date_from.'\' AND creditor_id = '.$creditor_id.')
) AS balance_brought_forward', FALSE); 
		$this->db->where('creditor_account_date < \''.$date_from.'\' AND creditor_id = '.$creditor_id.'' );
		$this->db->group_by('balance_brought_forward');
		$query = $this->db->get('creditor_account');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			return $row->balance_brought_forward;
		}
		
		else
		{
			return 0;
		}
	}
	
	public function get_creditor_account($where, $table)
	{
		$this->db->select('*');
		//$this->db->join('account', 'creditor_account.account_id = account.account_id', 'left');
		$this->db->where($where);
		$this->db->order_by('creditor_account_date', 'ASC');
		$query = $this->db->get($table);
		
		return $query;
	}

	public function get_creditor_transactions($where, $table)
	{
		$this->db->select('*');
		$this->db->where($where);
		$this->db->group_by('transaction_code', 'ASC');
		$query = $this->db->get($table);
		
		return $query;
	}
	
	public function record_creditor_account($creditor_id)
	{
		$transaction_type = $this->input->post('transaction_type_id');
		$array = array(
			"creditor_account_date" => $this->input->post('creditor_account_date'),
			"transaction_type_id" => $transaction_type,
			"creditor_account_description" => $this->input->post('creditor_account_description'),
			"creditor_account_amount" => $this->input->post('creditor_account_amount'),
			"creditor_id" => $this->input->post('creditor_id'),
			'created' => date('Y-m-d H:i:s'),
			"transaction_code"=>$this->input->post('transaction_code'),
			"created_by" => $this->session->userdata('personnel_id'),
			"modified_by" => $this->session->userdata('personnel_id')
		);
		
		$transaction_type_id = $this->input->post('transaction_type_id');
		
		if($this->db->insert('creditor_account', $array))
		{
			//if payment was made then reduce the amount from the account it was made from
			// if($transaction_type == 1)
			// {
				//update the account with an expenditure 
				// $account_expenditure_data = array(
				// 		"petty_cash_date" => $this->input->post('creditor_account_date'),
				// 		"transaction_type_id" => 2,
				// 		"petty_cash_description" => $this->input->post('creditor_account_description'),
				// 		"petty_cash_amount" => $this->input->post('creditor_account_amount'),
				// 		"account_id" => $this->input->post('payment_from_account_id'),
				// 		'created' => date('Y-m-d H:i:s'),
				// 		"created_by" => $this->session->userdata('personnel_id'),
				// 		"modified_by" => $this->session->userdata('personnel_id')
				// 		);
				// if($this->db->insert('petty_cash', $account_expenditure_data))
				// {
				// }
			// }
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}


	public function record_provider_account($creditor_id)
	{
		$transaction_type = $this->input->post('transaction_type_id');
		$array = array(
			"provider_account_date" => $this->input->post('creditor_account_date'),
			"transaction_type_id" => $transaction_type,
			"provider_account_description" => $this->input->post('creditor_account_description'),
			"provider_account_amount" => $this->input->post('creditor_account_amount'),
			"provider_id" => $this->input->post('provider_id'),
			'created' => date('Y-m-d H:i:s'),
			"transaction_code"=>$this->input->post('transaction_code'),
			"created_by" => $this->session->userdata('personnel_id'),
			"modified_by" => $this->session->userdata('personnel_id')
		);
		
		$transaction_type_id = $this->input->post('transaction_type_id');
		
		if($this->db->insert('provider_account', $array))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}

	public function get_invoice_total($creditor_id)
	{
		$invoice_total = 0;

		$this->db->select(' SUM(creditor_account_amount) AS total_invoice');
		$this->db->where('creditor_account_status = 1 AND transaction_type_id = 2 AND creditor_account_delete = 0 AND creditor_id = '.$creditor_id);
		$query = $this->db->get ('creditor_account'); 
		
		$invoice_total_row = $query->row();
		$invoice_total = $invoice_total_row->total_invoice;

		return $invoice_total;

	}
	public function get_payments_total($creditor_id)
	{
		$payment_total = 0;

		$this->db->select(' SUM(creditor_account_amount) AS total_payment');
		$this->db->where('creditor_account_status = 1 AND transaction_type_id = 1 AND creditor_account_delete = 0 AND creditor_id = '.$creditor_id);
		$query = $this->db->get ('creditor_account'); 
		
		$payment_total_row = $query->row();
		$payment_total = $payment_total_row->total_payment;

		return $payment_total;

	}
	public function get_statement_value($creditor_id,$date,$value)
	{
		// invoices
		$invoice = '';
		$first_date = date('Y-m').'-01';
		if($value == 1)
		{
			$invoice = ' AND creditor_account_date >= "'.$first_date.'" AND creditor_account_date <= "'.$date.'" ';

			$balance = ' AND created >= "'.$first_date.'" AND created <= "'.$date.'" ';
		}
		else if($value == 2)
		{

			$three_months = date('Y-m-d', strtotime('-2 months'));
			$invoice = ' AND creditor_account_date >= "'.$three_months.'" AND creditor_account_date < "'.$first_date.'" ';
			$balance = ' AND created >= "'.$three_months.'" AND created <= "'.$first_date.'" ';
		}
		else if($value == 3)
		{

			$three_months = date('Y-m-d', strtotime('-3 months'));
			$send_first = date('Y-m-01', strtotime('-2 months'));
			$invoice = ' AND creditor_account_date >= "'.$three_months.'" AND creditor_account_date <= "'.$send_first.'" ';
			$balance = ' AND created >= "'.$three_months.'" AND created <= "'.$send_first.'" ';
		}

		else if($value == 4)
		{

			$three_months = date('Y-m-d', strtotime('-4 months'));
			$send_second = date('Y-m-01', strtotime('-3 months'));
			$invoice = ' AND creditor_account_date >= "'.$three_months.'" AND creditor_account_date <= "'.$send_second.'" ';
			$balance = ' AND created >= "'.$three_months.'" AND created <= "'.$send_second.'" ';
		}
		else if($value == 5)
		{

			$three_months = date('Y-m-d', strtotime('-5 months'));
			$send_fourth = date('Y-m-01', strtotime('-4 months'));
			$invoice = ' AND creditor_account_date >= "'.$three_months.'" AND creditor_account_date <= "'.$send_fourth.'" ';
			$balance = ' AND created >= "'.$three_months.'" AND created <= "'.$send_fourth.'" ';
		}
		else if($value == 6)
		{
			$three_months = date('Y-m-d', strtotime('-6 months'));
		    $send_third = date('Y-m-01', strtotime('-5 months'));
			$invoice = ' AND creditor_account_date <= "'.$send_third.'" ';
			$balance = ' AND created <= "'.$send_third.'" ';
		}


		
		

		$this->db->select(' SUM(creditor_account_amount) AS total_invoice');
		$this->db->where('creditor_account_status = 1 AND transaction_type_id = 2 AND creditor_account_delete = 0 AND creditor_id = '.$creditor_id.' '.$invoice);
		$query = $this->db->get ('creditor_account');

		$invoice_total = 0; 		
		if($query->num_rows() > 0)
		{
			$invoice_total_row = $query->row();
			$invoice_total = $invoice_total_row->total_invoice;
		}

		// payments
		$payment_total = 0;
		$this->db->select(' SUM(creditor_account_amount) AS total_payment');
		$this->db->where('creditor_account_status = 1 AND transaction_type_id = 1 AND creditor_account_delete = 0 AND creditor_id = '.$creditor_id.' '.$invoice);
		$query_payments = $this->db->get ('creditor_account'); 
		
		if($query_payments->num_rows() > 0)
		{
			$payment_total_row = $query_payments->row();
			$payment_total = $payment_total_row->total_payment;
		}

		$this->db->where('creditor_id = '.$creditor_id.'  '.$balance);
		$creditor = $this->db->get('creditor');
		$balance_amount = 0;
		if($creditor->num_rows() > 0)
		{
			$row = $creditor->row();
			$creditor_name = $row->creditor_name;
			$opening_balance = $row->opening_balance;
			$created = $row->created;
			$debit_id = $row->debit_id;

			if($debit_id == 1)
			{
				$invoice_total = $invoice_total + $opening_balance;
			}
			else
			{
				$payment_total = $payment_total + $opening_balance;
			}


		}

		$amount = $invoice_total - $payment_total;

		return $amount;

	}


	public function get_creditor3($creditor_account_id)
	
	  {
		//retrieve all users
		$this->db->from('creditor_account');
		$this->db->select('*');
		$this->db->where('creditor_account_id = 1'.$creditor_account_id);
		$query = $this->db->get();
		
		return $query;    	
 
     }	
    public function delete_creditor($creditor_account_id)
   {
		$array = array(
			'creditor_account_delete'=>1
		);
		$this->db->where('creditor_account_id', $creditor_account_id);
		if($this->db->update('creditor_account', $array))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	public function record_creditor_invoices($creditor_id)
	{

		$array = array(
						"invoice_date" => $this->input->post('creditor_account_date'),
						"account_to_type" =>2 ,
						"account_invoice_description" => $this->input->post('creditor_account_description'),
						"invoice_amount" => $this->input->post('creditor_account_amount'),
						"account_to_id" => $creditor_id,
						'created' => date('Y-m-d'),
						"invoice_number"=>$this->input->post('transaction_code'),
						"created_by" => $this->session->userdata('personnel_id')
					);
		
		
		if($this->db->insert('account_invoices', $array))
		{
			
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}


	public function record_provider_invoices($creditor_id)
	{

		$array = array(
						"invoice_date" => $this->input->post('creditor_account_date'),
						"account_to_type" =>3 ,
						"account_invoice_description" => $this->input->post('creditor_account_description'),
						"invoice_amount" => $this->input->post('creditor_account_amount'),
						"account_to_id" => $creditor_id,
						'created' => date('Y-m-d'),
						"invoice_number"=>$this->input->post('transaction_code'),
						"created_by" => $this->session->userdata('personnel_id')
					);
		
		
		if($this->db->insert('account_invoices', $array))
		{
			
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}


}
?>