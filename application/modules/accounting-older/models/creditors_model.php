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
	*	get a single creditor's details
	*	@param int $creditor_id
	*
	*/
	public function get_personnel_names($personnel_id)
	{
		//retrieve all users
		$this->db->from('personnel');
		$this->db->select('*');
		$this->db->where('personnel_id = '.$personnel_id);
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
		$account = array(
			'account_to_id'=>12,//$this->input->post('account_to_id'),
			'account_from_id'=>$this->input->post('account_from_id'),
			'invoice_amount'=>$this->input->post('creditor_account_amount'),
			'account_invoice_description'=>$this->input->post('creditor_account_description'),
            'account_to_type'=>2,//$this->input->post('transaction_type_id'),
            'invoice_date'=>$this->input->post('creditor_account_date'),
            'created_by'=>$this->session->userdata('personnel_id'),
            'invoice_number'=>$this->input->post('transaction_code'),
            'created'=>date('Y-m-d'),
            'transaction_type_id'=>$this->input->post('transaction_type_id')
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

	public function record_provider_account($provider_id,$transaction_type_id)
	{
		// $transaction_type = $this->input->post('transaction_type_id');
		$account = array(
			'account_to_id'=> $this->input->post('account_to_id'),
			'account_from_id'=>$this->input->post('account_from_id'),
			'invoice_amount'=>$this->input->post('creditor_account_amount'),
			'invoice_number'=>$this->input->post('transaction_code'),
			'created'=>$this->input->post('payment_date'),
			'account_invoice_description'=>$this->input->post('creditor_account_description'),
            'account_to_type'=>3,//$this->input->post('transaction_type_id'),
            'invoice_date'=>$this->input->post('creditor_account_date'),
            'created_by'=>$this->session->userdata('personnel_id'),
            'transaction_type_id'=>$transaction_type_id
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


	public function update_provider_account($provider_id)
	{
		$account = array(
			'provider_id'=>$provider_id,//$this->input->post('account_to_id'),
			'opening_balance'=>$this->input->post('opening_balance'),
			'debit_id'=>$this->input->post('debit_id'),
			'created'=>$this->input->post('start_date')
			);

		// check if it exists

		$this->db->where('provider_id',$provider_id);
		$query = $this->db->get('provider_account');

		if($query->num_rows() > 0)
		{
			// update
			$this->db->where('provider_id',$provider_id);
			if($this->db->update('provider_account',$account))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}

		}
		else
		{
			// insert
			if($this->db->insert('provider_account',$account))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		// var_dump($account); die();
		
		
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
			// 30 days
			$invoice = ' AND invoice_date >= "'.$first_date.'" AND invoice_date <= "'.$date.'" ';
			$supplier_invoice = ' AND orders.supplier_invoice_date >= "'.$first_date.'" AND orders.supplier_invoice_date <= "'.$date.'" ';
			$balance = ' AND payment_date >= "'.$first_date.'" AND payment_date <= "'.$date.'" ';
			$account_balance = ' AND date(last_modified) >= "'.$first_date.'" AND date(last_modified) <= "'.$date.'" ';
		}
		else if($value == 2)
		{
			// 60 days
			$three_months = date('Y-m-01', strtotime('-1 months'));

			$last_date =  date("Y-m-t", strtotime($three_months));
			// var_dump($last_date); die();
			$invoice = ' AND invoice_date >= "'.$three_months.'" AND invoice_date <= "'.$last_date.'" ';
			$supplier_invoice = ' AND orders.supplier_invoice_date >= "'.$three_months.'" AND orders.supplier_invoice_date <= "'.$last_date.'" ';
			$balance = ' AND payment_date >= "'.$three_months.'" AND payment_date <= "'.$last_date.'" ';
			$account_balance = ' AND date(last_modified) >= "'.$three_months.'" AND date(last_modified) <= "'.$last_date.'" ';
		}
		else if($value == 3)
		{
			// 90 days

			$three_months = date('Y-m-01', strtotime('-2 months'));
			// $send_first = date('Y-m-01', strtotime('-1 months'));
			$last_date =  date("Y-m-t", strtotime($three_months));
			$invoice = ' AND invoice_date >= "'.$three_months.'" AND invoice_date <= "'.$last_date.'" ';
			$supplier_invoice = ' AND orders.supplier_invoice_date >= "'.$three_months.'" AND orders.supplier_invoice_date <= "'.$last_date.'" ';
			$balance = ' AND payment_date >= "'.$three_months.'" AND payment_date <= "'.$last_date.'" ';
			$account_balance = ' AND date(last_modified) >= "'.$three_months.'" AND date(last_modified) <= "'.$last_date.'" ';
		}

		else if($value == 4)
		{
			// over 90 days

			$three_months = date('Y-m-01', strtotime('-3 months'));
			// $send_second = date('Y-m-01', strtotime('-3 months'));
			$last_date =  date("Y-m-t", strtotime($three_months));
			$invoice = ' AND invoice_date <= "'.$last_date.'" ';
			$supplier_invoice = ' AND orders.supplier_invoice_date <= "'.$last_date.'"  ';
			$balance = ' AND payment_date <= "'.$last_date.'"  ';
			$account_balance = ' AND date(last_modified) <= "'.$last_date.'"  ';
		}
	

		// creditor statements

		$this->db->where('orders.is_store = 0 AND orders.order_approval_status = 7 AND orders.order_id = order_supplier.order_id AND orders.supplier_invoice_number IS NOT NULL '.$supplier_invoice.' AND orders.supplier_id ='.$creditor_id);
		$this->db->select('SUM(order_supplier.total_amount) AS total_amount');
		$query_supplier = $this->db->get('orders,order_supplier');
		
		$supplier_total_invoice = 0; 		
		if($query_supplier->num_rows() > 0)
		{
			$supplier_total_invoice_row = $query_supplier->row();
			$supplier_total_invoice = $supplier_total_invoice_row->total_amount;
		}
		

		$this->db->select(' SUM(invoice_amount) AS total_invoice');
		$this->db->where('account_to_type = 2 AND account_invoice_deleted = 0  AND account_from_id = '.$creditor_id.' '.$invoice);
		$query = $this->db->get ('account_invoices');

		$invoice_total = 0; 		
		if($query->num_rows() > 0)
		{
			$invoice_total_row = $query->row();
			$invoice_total = $invoice_total_row->total_invoice;
		}


		// payments
		$payment_total = 0;
		$this->db->select(' SUM(amount_paid) AS total_payment');
		$this->db->where('account_to_type = 2 AND account_payment_deleted = 0  AND account_to_id = '.$creditor_id.''.$payment_search.' '.$balance);
		$query_payments = $this->db->get ('account_payments'); 
		
		if($query_payments->num_rows() > 0)
		{
			$payment_total_row = $query_payments->row();
			$payment_total = $payment_total_row->total_payment;
		}

		$this->db->where('creditor_id = '.$creditor_id.''.$account_balance);
		$creditor = $this->db->get('creditor');
		$balance_amount = 0;
		if($creditor->num_rows() > 0)
		{
			$row = $creditor->row();
			$creditor_name = $row->creditor_name;
			$opening_balance = $row->opening_balance;
			$created = $row->created;
			$debit_id = $row->debit_id;

			if($debit_id == 2)
			{
				$invoice_total = $invoice_total + $opening_balance;
			}
			else
			{
				$payment_total = $payment_total + $opening_balance;
			}


		}

		
		$amount = ($invoice_total + $supplier_total_invoice) - $payment_total;
		// if($creditor_id == 103 AND $value == 4)
		// {
		// 	var_dump($amount); die();
		// }
		if($amount < 0)
		{
			$amount = -$amount;
		}

		return $amount;

	}


	public function get_provider_statement_value($provider_id,$date,$value)
	{
		// invoices
		$invoice = '';
		$first_date = date('Y-m').'-01';
		if($value == 1)
		{
			$invoice = ' AND invoice_date >= "'.$first_date.'" AND invoice_date <= "'.$date.'" ';

			$balance = ' AND payment_date >= "'.$first_date.'" AND payment_date <= "'.$date.'" ';
		}
		else if($value == 2)
		{

			$three_months = date('Y-m-d', strtotime('-2 months'));
			$invoice = ' AND invoice_date >= "'.$three_months.'" AND invoice_date < "'.$first_date.'" ';
			$balance = ' AND payment_date >= "'.$three_months.'" AND payment_date <= "'.$first_date.'" ';
		}
		else if($value == 3)
		{

			$three_months = date('Y-m-d', strtotime('-3 months'));
			$send_first = date('Y-m-01', strtotime('-2 months'));
			$invoice = ' AND invoice_date >= "'.$three_months.'" AND invoice_date <= "'.$send_first.'" ';
			$balance = ' AND payment_date >= "'.$three_months.'" AND payment_date <= "'.$send_first.'" ';
		}

		else if($value == 4)
		{

			$three_months = date('Y-m-d', strtotime('-4 months'));
			$send_second = date('Y-m-01', strtotime('-3 months'));
			$invoice = ' AND invoice_date >= "'.$three_months.'" AND invoice_date <= "'.$send_second.'" ';
			$balance = ' AND payment_date >= "'.$three_months.'" AND payment_date <= "'.$send_second.'" ';
		}
		else if($value == 5)
		{

			$three_months = date('Y-m-d', strtotime('-5 months'));
			$send_fourth = date('Y-m-01', strtotime('-4 months'));
			$invoice = ' AND invoice_date >= "'.$three_months.'" AND invoice_date <= "'.$send_fourth.'" ';
			$balance = ' AND payment_date >= "'.$three_months.'" AND payment_date <= "'.$send_fourth.'" ';
		}
		else if($value == 6)
		{
			$three_months = date('Y-m-d', strtotime('-6 months'));
		    $send_third = date('Y-m-01', strtotime('-5 months'));
			$invoice = ' AND invoice_date <= "'.$send_third.'" ';
			$balance = ' AND payment_date <= "'.$send_third.'" ';
		}


		
		

		$this->db->select(' SUM(invoice_amount) AS total_invoice');
		$this->db->where('account_to_type = 3 AND account_invoice_deleted = 0  AND account_from_id = '.$provider_id.' '.$invoice);
		$query = $this->db->get ('account_invoices');

		$invoice_total = 0; 		
		if($query->num_rows() > 0)
		{
			$invoice_total_row = $query->row();
			$invoice_total = $invoice_total_row->total_invoice;
		}

		// payments
		$payment_total = 0;
		$this->db->select(' SUM(amount_paid) AS total_payment');
		$this->db->where('account_to_type = 3 AND account_payment_deleted = 0  AND account_to_id = '.$provider_id.''.$payment_search.' '.$balance);
		$query_payments = $this->db->get ('account_payments'); 
		
		if($query_payments->num_rows() > 0)
		{
			$payment_total_row = $query_payments->row();
			$payment_total = $payment_total_row->total_payment;
		}

		$this->db->where('provider_id = '.$provider_id);
		$creditor = $this->db->get('provider_account');
		$balance_amount = 0;
		if($creditor->num_rows() > 0)
		{
			$row = $creditor->row();
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

		if($amount < 0)
		{
			$amount = -$amount;
		}

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
		public function get_all_creditor_invoices($creditor_id)
		{
			$search = $this->session->userdata('creditor_invoice_search');

			if(!empty($search))
			{
				$invoice_search = $search;
			}
			else
			{
				$invoice_search = '';
			}
			
			$this->db->from('account_invoices');
			$this->db->select('*');
			$this->db->where('account_to_type = 2 AND account_invoice_deleted = 0  AND account_from_id = '.$creditor_id.''.$invoice_search);
			$this->db->order_by('invoice_date','ASC');
			$this->db->group_by('invoice_number');
			$query = $this->db->get();
			return $query;
		}

		

		public function get_all_provider_invoices($provider_id)
		{
			$search = $this->session->userdata('provider_invoice_search');

			if(!empty($search))
			{
				$invoice_search = $search;
			}
			else
			{
				$invoice_search = '';
			}


			$date_from = $this->session->userdata('providers_date_from');
			$date_to = $this->session->userdata('providers_date_to');

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
		
			$this->db->from('account_invoices');
			$this->db->select('*');
			$this->db->where('account_to_type = 3 AND account_invoice_deleted = 0  AND account_from_id = '.$provider_id.''.$search_add);
			$this->db->order_by('invoice_date','ASC');
			$this->db->group_by('invoice_number');
			$query = $this->db->get();
			return $query;
		}



		public function get_all_provider_credit_month($provider_id,$start_date,$end_date)
		{
			$search = $this->session->userdata('provider_invoice_search');

			if(!empty($search))
			{
				$invoice_search = $search;
			}
			else
			{
				$invoice_search = '';
			}


			$date_from = $this->session->userdata('providers_date_from');
			$date_to = $this->session->userdata('providers_date_to');

			if(!empty($start_date) AND !empty($end_date))
			{
				$search_add =  ' AND (invoice_date >= \''.$start_date.'\' AND invoice_date <= \''.$end_date.'\') ';
				$search_payment_add =  ' AND (payment_date >= \''.$start_date.'\' AND payment_date <= \''.$end_date.'\') ';
			}
			else if(!empty($start_date))
			{
				$search_add = ' AND invoice_date = \''.$start_date.'\'';
				$search_payment_add = ' AND payment_date = \''.$start_date.'\'';
			}
			else if(!empty($end_date))
			{
				$search_add = ' AND invoice_date = \''.$end_date.'\'';
				$search_payment_add = ' AND payment_date = \''.$end_date.'\'';
			}
		
			$this->db->from('account_invoices');
			$this->db->select('SUM(invoice_amount) AS total_charged_amount');
			$this->db->where('account_to_type = 3 AND account_invoice_deleted = 0 AND transaction_type_id = 0 AND account_from_id = '.$provider_id.''.$search_add);
			$this->db->order_by('invoice_date','ASC');
			$query = $this->db->get();
			$result = $query->row();

			return $result->total_charged_amount;
		}

		public function get_all_lab_works($month,$year,$provider_id,$visit_type_id)
		{
			if($visit_type_id == 1)
			{
				$visit_search = ' AND visit.visit_type = 1';
			}
			else
			{
				$visit_search = ' AND visit.visit_type <> 1';
			}


			$date_from = $this->session->userdata('providers_date_from');
			$date_to = $this->session->userdata('providers_date_to');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (visit_date >= \''.$date_from.'\' AND visit_date <= \''.$date_to.'\') ';
				$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = ' AND visit_date = \''.$date_from.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = ' AND visit_date = \''.$date_to.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
			}

			$this->db->from('visit,visit_lab_work');
			$this->db->select('SUM(visit_lab_work.amount_to_charge) AS total_charged_amount');
			$this->db->where('visit.visit_id = visit_lab_work.visit_id AND visit.visit_delete = 0 AND visit.personnel_id = '.$provider_id.' AND MONTH(visit.visit_date) = "'.$month.'" AND YEAR(visit_date) = "'.$year.'" '.$visit_search.' '.$search_add);
			$query = $this->db->get();

			$result = $query->row();

			return $result->total_charged_amount;
		}

		public function get_all_provider_work_done($provider_id)
		{
			$search = $this->session->userdata('provider_invoice_search');

			if(!empty($search))
			{
				$invoice_search = $search;
			}
			else
			{
				$invoice_search = '';
			}

			$date_from = $this->session->userdata('providers_date_from');
			$date_to = $this->session->userdata('providers_date_to');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (visit_date >= \''.$date_from.'\' AND visit_date <= \''.$date_to.'\') ';
				$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = ' AND visit_date = \''.$date_from.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = ' AND visit_date = \''.$date_to.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
			}
			
			$this->db->from('visit,visit_charge');
			$this->db->select('visit_date,SUM(visit_charge.visit_charge_amount*visit_charge.visit_charge_units) AS total_charged_amount');
			$this->db->where('visit.visit_id = visit_charge.visit_id AND visit.visit_delete = 0 AND visit_charge.charged = 1 AND visit.personnel_id = '.$provider_id.' '.$search_add);
			$this->db->order_by('YEAR(visit.visit_date),MONTH(visit.visit_date)','ASC');
			$this->db->group_by('YEAR(visit.visit_date),MONTH(visit.visit_date)');
			$query = $this->db->get();
			return $query;
		}

		public function get_all_provider_work_done_weekly($provider_id)
		{
			$search = $this->session->userdata('provider_invoice_search');

			if(!empty($search))
			{
				$invoice_search = $search;
			}
			else
			{
				$invoice_search = '';
			}

			$date_from = $this->session->userdata('providers_date_from');
			$date_to = $this->session->userdata('providers_date_to');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (visit_date >= \''.$date_from.'\' AND visit_date <= \''.$date_to.'\') ';
				$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = ' AND visit_date = \''.$date_from.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = ' AND visit_date = \''.$date_to.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
			}
			
			$this->db->from('visit,visit_charge');
			$this->db->select('visit_date,YEAR(visit.visit_date) AS year,MONTH(visit.visit_date) AS month,WEEK(visit.visit_date) AS week, SUM(visit_charge.visit_charge_amount*visit_charge.visit_charge_units) AS total_charged_amount');
			$this->db->where('visit.visit_id = visit_charge.visit_id AND visit.visit_delete = 0 AND visit_charge.charged = 1 AND visit.personnel_id = '.$provider_id.' '.$search_add);
			$this->db->order_by('YEAR(visit.visit_date),MONTH(visit.visit_date),WEEK(visit.visit_date)','ASC');
			$this->db->group_by('YEAR(visit.visit_date),MONTH(visit.visit_date),WEEK(visit.visit_date)');
			$query = $this->db->get();
			return $query;
		}


		public function get_all_provider_work_invoiced($provider_id)
		{
			$search = $this->session->userdata('provider_invoice_search');

			if(!empty($search))
			{
				$invoice_search = $search;
			}
			else
			{
				$invoice_search = '';
			}
			
			$date_from = $this->session->userdata('providers_date_from');
			$date_to = $this->session->userdata('providers_date_to');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (visit_date >= \''.$date_from.'\' AND visit_date <= \''.$date_to.'\') ';
				$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = ' AND visit_date = \''.$date_from.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = ' AND visit_date = \''.$date_to.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
			}


			$this->db->from('visit,doctor_invoice');
			$this->db->select('visit_date,SUM(doctor_invoice.invoiced_amount) AS total_charged_amount');
			$this->db->where('visit.visit_id = visit_charge.visit_id AND visit.visit_delete = 0 AND doctor_invoice.doctor_invoice_status = 1 AND visit.personnel_id = '.$provider_id.' '.$search_add);
			$this->db->order_by('YEAR(visit.visit_date),MONTH(visit.visit_date)','ASC');
			$this->db->group_by('YEAR(visit.visit_date),MONTH(visit.visit_date)');
			$query = $this->db->get();
			return $query;
		}



		public function get_all_payments_creditor($creditor_id)
		{
			$search = $this->session->userdata('creditor_payment_search');

			if(!empty($search))
			{
				$payment_search = $search;
			}
			else
			{
				$payment_search = '';
			}

			$date_from = $this->session->userdata('providers_date_from');
			$date_to = $this->session->userdata('providers_date_to');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (visit_date >= \''.$date_from.'\' AND visit_date <= \''.$date_to.'\') ';
				$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = ' AND visit_date = \''.$date_from.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = ' AND visit_date = \''.$date_to.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
			}


			$this->db->from('account_payments');
			$this->db->select('*');
			$this->db->where('account_to_type = 2 AND account_payment_deleted = 0  AND account_to_id = '.$creditor_id.''.$search_payment_add);
			$this->db->order_by('payment_date','ASC');
			$query = $this->db->get();
			return $query;
		}


		public function get_all_payments_provider($provider_id)
		{
			$search = $this->session->userdata('provider_payment_search');

			if(!empty($search))
			{
				$payment_search = $search;
			}
			else
			{
				$payment_search = '';
			}

			$date_from = $this->session->userdata('providers_date_from');
			$date_to = $this->session->userdata('providers_date_to');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (visit_date >= \''.$date_from.'\' AND visit_date <= \''.$date_to.'\') ';
				$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = ' AND visit_date = \''.$date_from.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = ' AND visit_date = \''.$date_to.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
			}

			$this->db->from('account_payments');
			$this->db->select('*');
			$this->db->where('account_to_type = 3 AND account_payment_deleted = 0 AND payment_to = 0 AND account_to_id = '.$provider_id.''.$search_payment_add);
			$this->db->order_by('payment_date','ASC');
			$query = $this->db->get();
			return $query;
		}



		public function get_all_payments_provider_weekly($provider_id,$start_date,$end_date,$payment_week)
		{
			$search = $this->session->userdata('provider_payment_search');

			if(!empty($search))
			{
				$payment_search = $search;
			}
			else
			{
				$payment_search = '';
			}

			$date_from = $this->session->userdata('providers_date_from');
			$date_to = $this->session->userdata('providers_date_to');

			if(!empty($start_date) AND !empty($end_date))
			{
				$search_add =  ' AND (visit_date >= \''.$start_date.'\' AND visit_date <= \''.$end_date.'\') ';
				$search_payment_add =  ' AND (payment_date >= \''.$start_date.'\' AND payment_date <= \''.$end_date.'\') ';
			}
			else if(!empty($start_date))
			{
				$search_add = ' AND visit_date = \''.$start_date.'\'';
				$search_payment_add = ' AND payment_date = \''.$start_date.'\'';
			}
			else if(!empty($end_date))
			{
				$search_add = ' AND visit_date = \''.$end_date.'\'';
				$search_payment_add = ' AND payment_date = \''.$end_date.'\'';
			}


			$this->db->from('account_payments');
			$this->db->select('SUM(amount_paid) AS total_amount');
			$this->db->where('account_to_type = 3 AND account_payment_deleted = 0 AND payment_to = 1 AND account_to_id = '.$provider_id.''.$search_payment_add);
			$this->db->order_by('payment_date','ASC');
			$query = $this->db->get();
			$total_amount = 0;
			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$total_amount = $value->total_amount;
				}
			}
			return $total_amount;
		}

		public function get_all_payments_provider_monthly($provider_id,$start_date,$end_date,$payment_week)
		{
			$search = $this->session->userdata('provider_payment_search');

			if(!empty($search))
			{
				$payment_search = $search;
			}
			else
			{
				$payment_search = '';
			}

			$date_from = $this->session->userdata('providers_date_from');
			$date_to = $this->session->userdata('providers_date_to');

			if(!empty($start_date) AND !empty($end_date))
			{
				$search_add =  ' AND (visit_date >= \''.$start_date.'\' AND visit_date <= \''.$end_date.'\') ';
				$search_payment_add =  ' AND (payment_date >= \''.$start_date.'\' AND payment_date <= \''.$end_date.'\') ';
			}
			else if(!empty($start_date))
			{
				$search_add = ' AND visit_date = \''.$start_date.'\'';
				$search_payment_add = ' AND payment_date = \''.$start_date.'\'';
			}
			else if(!empty($end_date))
			{
				$search_add = ' AND visit_date = \''.$end_date.'\'';
				$search_payment_add = ' AND payment_date = \''.$end_date.'\'';
			}


			$this->db->from('account_payments');
			$this->db->select('SUM(amount_paid) AS total_amount');
			$this->db->where('account_to_type = 3 AND account_payment_deleted = 0 AND payment_to = 0 AND account_to_id = '.$provider_id.''.$search_payment_add);
			$this->db->order_by('payment_date','ASC');
			$query = $this->db->get();
			$total_amount = 0;
			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$total_amount = $value->total_amount;
				}
			}
			return $total_amount;
		}


		public function get_all_payments_weekly($provider_id,$payment_year,$payment_month,$payment_week)
		{
			$search = $this->session->userdata('provider_payment_search');

			if(!empty($search))
			{
				$payment_search = $search;
			}
			else
			{
				$payment_search = '';
			}

			$date_from = $this->session->userdata('providers_date_from');
			$date_to = $this->session->userdata('providers_date_to');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (visit_date >= \''.$date_from.'\' AND visit_date <= \''.$date_to.'\') ';
				$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = ' AND visit_date = \''.$date_from.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = ' AND visit_date = \''.$date_to.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
			}

			$this->db->from('account_payments');
			$this->db->select('SUM(amount_paid) AS total_amount');
			$this->db->where('account_to_type = 3 AND account_payment_deleted = 0 AND YEAR(payment_date) = "'.$payment_year.'" AND MONTH(payment_date) = "'.$payment_month.'" AND WEEK(payment_date) = "'.$payment_week.'" AND account_to_id = '.$provider_id.''.$search_payment_add);
			$this->db->order_by('payment_date','ASC');
			$query = $this->db->get();
			$total_amount = 0;
			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$total_amount = $value->total_amount;
				}
			}
			return $query;
		}

		public function get_invoice_brought_forward($creditor_id,$invoice_search)
		{
			
			$this->db->from('account_invoices');
			$this->db->select('SUM(invoice_amount) AS amount');
			$this->db->where('account_to_type = 2 AND account_invoice_deleted = 0  AND account_from_id = '.$creditor_id.''.$invoice_search);
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

		public function get_provider_invoice_brought_forward($provider_id,$invoice_search)
		{
			
			$this->db->from('account_invoices');
			$this->db->select('SUM(invoice_amount) AS amount');
			$this->db->where('account_to_type = 3 AND account_invoice_deleted = 0  AND account_from_id = '.$provider_id.''.$invoice_search);
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

		public function get_payment_brought_forward($creditor_id,$payment_search)
		{
			
			$this->db->from('account_payments');
			$this->db->select('SUM(amount_paid) AS amount');
			$this->db->where('account_to_type = 2 AND account_payment_deleted = 0  AND account_to_id = '.$creditor_id.''.$payment_search);
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

		public function get_provider_payment_brought_forward($provider_id,$payment_search)
		{
			
			$this->db->from('account_payments');
			$this->db->select('SUM(amount_paid) AS amount');
			$this->db->where('account_to_type = 3 AND account_payment_deleted = 0  AND account_to_id = '.$provider_id.''.$payment_search);
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


		// public function get_provider_payment_brought_forward($provider_id,$payment_search)
		// {
			
		// 	$this->db->from('account_payments');
		// 	$this->db->select('SUM(amount_paid) AS amount');
		// 	$this->db->where('account_to_type = 3 AND account_payment_deleted = 0  AND account_to_id = '.$provider_id.''.$payment_search);
		// 	$this->db->order_by('payment_date','ASC');
		// 	$query = $this->db->get();
		// 	$amount = 0;
		// 	if($query->num_rows() > 0)
		// 	{
		// 		foreach ($query->result() as $key => $value) {
		// 			# code...
		// 			$amount = $value->amount;
		// 		}
		// 	}
		// 	return $amount;
			
		// }

		public function get_balance_brought_forward($creditor_id)
		{
			$invoice_search = $this->session->userdata('balance_invoice_search');
			$payment_search = $this->session->userdata('balance_payment_search');

			if(!empty($invoice_search))
			{
				$invoice_total = $this->get_invoice_brought_forward($creditor_id,$invoice_search);
				$payment_total = $this->get_payment_brought_forward($creditor_id,$payment_search);

				$balance = $payment_total - $invoice_total;

				return $balance;
			}
			else
			{
				return FALSE;
			}
		}


		public function get_provider_balance_brought_forward($provider_id)
		{
			$invoice_search = $this->session->userdata('balance_invoice_search');
			$payment_search = $this->session->userdata('balance_payment_search');

			if(!empty($invoice_search))
			{
				$invoice_total = $this->get_provider_invoice_brought_forward($provider_id,$invoice_search);
				$payment_total = $this->get_provider_payment_brought_forward($provider_id,$payment_search);

				$balance = $payment_total - $invoice_total;

				return $balance;
			}
			else
			{
				return FALSE;
			}
		}

		public function get_all_suppplier_invoices($creditor_id)
		{
			$this->db->where('orders.is_store = 0 AND orders.order_approval_status = 7 AND orders.supplier_invoice_number IS NOT NULL AND orders.supplier_id ='.$creditor_id);
			$this->db->select('orders.supplier_invoice_number AS invoice_number,orders.supplier_invoice_date AS invoice_date,orders.order_id');
			$query = $this->db->get('orders');

			return $query;
		}

		public function get_all_suppplier_credit_note($creditor_id)
		{
			$this->db->where('orders.is_store = 3 AND orders.order_approval_status = 7 AND orders.supplier_invoice_number IS NOT NULL AND orders.supplier_id ='.$creditor_id);
			$this->db->select('orders.reference_number AS invoice_number,orders.supplier_invoice_date AS invoice_date,orders.order_id');
			$query = $this->db->get('orders');

			return $query;
		}
		public function get_total_supplied_invoice($order_id)
		{
			$this->db->where('order_supplier.order_id ='.$order_id);
			$this->db->select('SUM(order_supplier.total_amount) AS invoice_amount');
			$query = $this->db->get('order_supplier');
			$invoice_amount = 0;
			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$invoice_amount = $value->invoice_amount;
				}
			}
			return $invoice_amount;
		}
		public function get_creditor_statement($creditor_id)
		{

			$creditor_query = $this->creditors_model->get_opening_creditor_balance($creditor_id);
			$bills = $this->get_all_creditor_invoices($creditor_id);

			$bills_query = $this->get_all_suppplier_invoices($creditor_id);
			$credit_note_query = $this->get_all_suppplier_credit_note($creditor_id);
			// var_dump($bills_query); 
			$payments = $this->get_all_payments_creditor($creditor_id);

			$brought_forward_balance = $this->get_balance_brought_forward($creditor_id);

			


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



			if($creditor_query->num_rows() > 0)
			{
				$row = $creditor_query->row();
				$opening_balance = $row->opening_balance;
				$created = $row->created;
				$debit_id = $row->debit_id;
				// var_dump($debit_id); die();
				if($debit_id == 2)
				{
					// this is deni
					$total_arrears += $opening_balance;
					$result .= 
								'
									<tr>
										<td>'.date('d M Y',strtotime($created)).' </td>
										<td>OPENING BALANCE</td>
										<td></td>
										<td></td>
										<td>'.number_format($opening_balance, 2).'</td>
										<td>'.number_format($total_arrears, 2).'</td>
										<td></td>
									</tr> 
								';
					$total_invoice_balance = $opening_balance;

				}
				else
				{
					$total_arrears -= $opening_balance;
					// this is a prepayment
					$result .= 
								'
									<tr>
										<td>'.date('d M Y',strtotime($created)).' </td>
										<td>OPENING BALANCE</td>
										<td></td>
										<td>'.number_format($opening_balance, 2).'</td>
										<td></td>
										<td>'.number_format($total_arrears, 2).'</td>
									</tr> 
								';
					$total_payment_amount = $opening_balance;
				}
			}
			

			if($brought_forward_balance == FALSE)
			{
				$result .='';
			}

			else
			{
				$search_title = $this->session->userdata('creditor_search_title');
				if($brought_forward_balance < 0)
				{

					$positive = -$brought_forward_balance;

					$total_arrears += $positive;
					$result .= 
								'
									<tr>
										<td colspan=3> B/F</td>
										<td>'.number_format($positive, 2).'</td>
										<td></td>
										<td>'.number_format($total_arrears, 2).'</td>
									</tr> 
								';
					$total_invoice_balance += $positive;

				}
				else
				{
					$total_arrears += $brought_forward_balance;
					$result .= 
								'
									<tr>
										<td > B/F</td>
										<td></td>
										<td>'.number_format($brought_forward_balance, 2).'</td>
										<td></td>
										<td></td>
										<td>'.number_format($total_arrears, 2).'</td>
									</tr> 
								';


					$total_invoice_balance += $brought_forward_balance;
				}
			}


			if($bills->num_rows() > 0)
			{
				foreach ($bills->result() as $key_bills) {
					# code...
					$invoice_date = $key_bills->invoice_date;
					$invoice_number = $key_bills->invoice_number;
					$invoice_amount = $key_bills->invoice_amount;
					$transaction_type_id = $key_bills->transaction_type_id;
					$invoice_explode = explode('-', $invoice_date);
					$invoice_year = $invoice_explode[0];
					$invoice_month = $invoice_explode[1];
					$account_invoice_description = $key_bills->account_invoice_description;
					$account_to_id = $key_bills->account_to_id;
					$account_from_id = $key_bills->account_from_id;
					$account_invoice_id = $key_bills->account_invoice_id;
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
							$receipt_number = $payments_key->receipt_number;
							$account_payment_id = $payments_key->account_payment_id;

							$account_payment_description = $payments_key->account_payment_description;


							if(($payment_date <= $invoice_date) && ($payment_date > $last_date) && ($payment_amount > 0))
							{
								$total_arrears -= $payment_amount;
								// var_dump($payment_year); die();
								// if($payment_year >= $current_year)
								// {
								$payment_button =  '';
								if($payment_date == date('Y-m-d'))
								{
									$payment_button = '<td><a href="'.site_url().'delete-creditor-payment-entry/'.$account_payment_id.'/'.$creditor_id.'" class="btn btn-sm btn-danger fa fa-trash" onclick="return confirm(\'Do you really want delete this entry?\');"></a></td>';
								}
									$result .= 
									'
										<tr>
											<td>'.date('d M Y',strtotime($payment_date)).' </td>
											<td>'.strtoupper($receipt_number).'</td>
											<td>'.$account_payment_description.'</td>
											<td>'.number_format($payment_amount, 2).'</td>
											<td></td>
											<td>'.number_format($total_arrears, 2).'</td>
											'.$payment_button.'
										</tr> 
									';
								// }
								
								$total_payment_amount += $payment_amount;

							}
						}
					}


					
					if($invoice_amount != 0)
					{
						
						$account_name = $this->get_account_name($account_to_id);
						if($transaction_type_id == 1)
						{
							$total_arrears += $invoice_amount;
							$total_invoice_balance += $invoice_amount;

							$invoice_button =  '';
							if($invoice_date == date('Y-m-d'))
							{
								$invoice_button = '<td><a href="'.site_url().'delete-creditor-invoice-entry/'.$account_invoice_id.'/'.$creditor_id.'" class="btn btn-sm btn-danger fa fa-trash" onclick="return confirm(\'Do you really want delete this entry?\');"></a></td>';
							}
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($invoice_date)).' </td>
									<td>'.strtoupper($invoice_number).'</td>
									<td>'.$account_invoice_description.'</td>
									<td></td>
									<td>'.number_format($invoice_amount, 2).'</td>
									<td>'.number_format($total_arrears, 2).'</td>
									'.$invoice_button.'
								</tr> 
							';
						}
						else
						{
							$total_arrears -= $invoice_amount;
							$total_invoice_balance -= $invoice_amount;

							$invoice_button =  '';
							if($invoice_date == date('Y-m-d'))
							{
								$invoice_button = '<td><a href="'.site_url().'delete-creditor-invoice-entry/'.$account_invoice_id.'/'.$creditor_id.'" class="btn btn-sm btn-danger fa fa-trash" onclick="return confirm(\'Do you really want delete this entry?\');"></a></td>';
							}
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($invoice_date)).' </td>
									<td>'.strtoupper($invoice_number).'</td>
									<td>CREDIT NOTE '.$account_invoice_description.'</td>
									<td></td>
									<td>('.number_format($invoice_amount, 2).')</td>
									<td>'.number_format($total_arrears, 2).'</td>
									'.$invoice_button.'
								</tr> 
							';

						}
					}

					if($bills_query->num_rows() > 0)
					{
						foreach ($bills_query->result() as $supplier) {
							# code...
							$invoice_date_bill = $supplier->invoice_date;
							$invoice_number = $supplier->invoice_number;
							$order_id = $supplier->order_id;
							$invoice_amount = $this->get_total_supplied_invoice($order_id);
							$invoice_explode = explode('-', $invoice_date_bill);

							if(($invoice_date_bill <= $invoice_date) && ($invoice_date_bill > $last_date) && ($invoice_amount > 0))
							{
								$total_arrears += $invoice_amount;
								$total_invoice_balance += $invoice_amount;
							
									$result .= 
									'
										<tr>
											<td>'.date('d M Y',strtotime($invoice_date_bill)).' </td>
											<td>'.strtoupper($invoice_number).'</td>
											<td>Drug Purchases</td>
											<td></td>
											<td>'.number_format($invoice_amount, 2).'</td>
											<td>'.number_format($total_arrears, 2).'</td>
											<td><a href="'.site_url().'inventory/orders/goods_received_notes/'.$order_id.'" class="btn btn-xs btn-success" target="_blank"> View Invoice </a></td>
										</tr> 
									';
								
							}
						}

					}

					if($credit_note_query->num_rows() > 0)
					{
						foreach ($credit_note_query->result() as $credit_note) {
							# code...
							$invoice_date_bill = $credit_note->invoice_date;
							$invoice_number = $credit_note->invoice_number;
							$order_id = $credit_note->order_id;
							$credit_note_amount = $this->get_total_supplied_invoice($order_id);
							$invoice_explode = explode('-', $invoice_date_bill);

							if(($invoice_date_bill <= $invoice_date) && ($invoice_date_bill > $last_date) && ($credit_note_amount > 0))
							{
								$total_arrears -= $credit_note_amount;
								$total_payment_amount += $credit_note_amount;
							
									$result .= 
									'
										<tr>
											<td>'.date('d M Y',strtotime($invoice_date_bill)).' </td>
											<td>'.strtoupper($invoice_number).'</td>
											<td>Credit Note</td>
											<td>'.number_format($credit_note_amount, 2).'</td>
											<td></td>
											<td>'.number_format($total_arrears, 2).'</td>
											<td><a href="'.site_url().'goods-transfered-notes/'.$order_id.'" class="btn btn-xs btn-success" target="_blank"> View Note </a></td>
										</tr> 
									';
								
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

								$payment_explode = explode('-', $payment_date);
								$payment_year = $payment_explode[0];
								$payment_month = $payment_explode[1];
								$payment_amount = $payments_key->amount_paid;
								$account_payment_id = $payments_key->account_payment_id;
								$receipt_number = $payments_key->receipt_number;
								$account_payment_description = $payments_key->account_payment_description;

								if(($payment_date > $invoice_date) &&  ($payment_amount > 0))
								{
									$total_arrears -= $payment_amount;
									// if($payment_year >= $current_year)
									// {
									$payment_button =  '';
									if($payment_date == date('Y-m-d'))
									{
										$payment_button = '<td><a href="'.site_url().'delete-creditor-payment-entry/'.$account_payment_id.'/'.$creditor_id.'" class="btn btn-sm btn-danger fa fa-trash" onclick="return confirm(\'Do you really want delete this entry?\');"></a></td>';
									}
										$result .= 
										'
											<tr>
												<td>'.date('d M Y',strtotime($payment_date)).' </td>
												<td>'.strtoupper($receipt_number).'</td>
												<td>'.$account_payment_description.'</td>
												<td>'.number_format($payment_amount, 2).'</td>
												<td></td> 
												<td>'.number_format($total_arrears, 2).'</td>
												'.$payment_date.'
											</tr> 
										';
									// }
									
									$total_payment_amount += $payment_amount;

								}
							}
						}

						if($bills_query->num_rows() > 0)
						{
							foreach ($bills_query->result() as $supplier) {
								# code...
								$invoice_date_bill = $supplier->invoice_date;
								$invoice_number = $supplier->invoice_number;
								$order_id = $supplier->order_id;
								$invoice_amount = $this->get_total_supplied_invoice($order_id);
								$invoice_explode = explode('-', $invoice_date_bill);

								if(($invoice_date_bill > $invoice_date) &&  ($invoice_amount > 0))
								{
									$total_arrears += $invoice_amount;
									$total_invoice_balance += $invoice_amount;
								
										$result .= 
										'
											<tr>
												<td>'.date('d M Y',strtotime($invoice_date_bill)).' </td>
												<td>'.strtoupper($invoice_number).'</td>
												<td>Drug Purchases</td>
												<td></td>
												<td>'.number_format($invoice_amount, 2).'</td>
												<td>'.number_format($total_arrears, 2).'</td>
												<td><a href="'.site_url().'inventory/orders/goods_received_notes/'.$order_id.'" class="btn btn-xs btn-success" target="_blank"> View Invoice </a></td>
											</tr> 
										';
									
								}
							}

						}


						if($credit_note_query->num_rows() > 0)
						{
							foreach ($credit_note_query->result() as $credit_note) {
								# code...
								$invoice_date_bill = $credit_note->invoice_date;
								$invoice_number = $credit_note->invoice_number;
								$order_id = $credit_note->order_id;
								$credit_note_amount = $this->get_total_supplied_invoice($order_id);
								$invoice_explode = explode('-', $invoice_date_bill);

								if(($invoice_date_bill > $invoice_date) &&  ($credit_note_amount > 0))
								{
									$total_arrears -= $credit_note_amount;
									$total_payment_amount += $credit_note_amount;
								
										$result .= 
										'
											<tr>
												<td>'.date('d M Y',strtotime($invoice_date_bill)).' </td>
												<td>'.strtoupper($invoice_number).'</td>
												<td>Credit Note</td>
												<td>'.number_format($credit_note_amount, 2).'</td>
												<td></td>
												<td>'.number_format($total_arrears, 2).'</td>
												<td><a href="'.site_url().'goods-transfered-notes/'.$order_id.'" class="btn btn-xs btn-success" target="_blank"> View Note </a></td>
											</tr> 
										';
									
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
						$receipt_number = $payments_key->receipt_number;
						$account_payment_id = $payments_key->account_payment_id;

						if(($payment_amount > 0))
						{
							$total_arrears -= $payment_amount;
							// if($payment_year >= $current_year)
							// {
							$payment_button =  '';
							if($payment_date == date('Y-m-d'))
							{
								$payment_button = '<td><a href="'.site_url().'delete-creditor-payment-entry/'.$account_payment_id.'/'.$creditor_id.'" class="btn btn-sm btn-danger fa fa-trash" onclick="return confirm(\'Do you really want delete this entry?\');"></a></td>';
							}

								$result .= 
								'
									<tr>
										<td>'.date('d M Y',strtotime($payment_date)).' </td>
										<td>'.strtoupper($receipt_number).'</td>
										<td></td>
										<td>'.number_format($payment_amount, 2).'</td>
										<td></td>
										<td>'.number_format($total_arrears, 2).'</td>
										'.$payment_button.'
									</tr> 
								';
							// }
							
							$total_payment_amount += $payment_amount;

						}
					}
				}

				if($bills_query->num_rows() > 0)
				{
					foreach ($bills_query->result() as $supplier) {
						# code...
						$invoice_date_bill = $supplier->invoice_date;
						$invoice_number = $supplier->invoice_number;
						$order_id = $supplier->order_id;
						$invoice_amount = $this->get_total_supplied_invoice($order_id);
						$invoice_explode = explode('-', $invoice_date_bill);

						if(($invoice_amount > 0))
						{
							$total_arrears += $invoice_amount;
							$total_invoice_balance += $invoice_amount;
						
								$result .= 
								'
									<tr>
										<td>'.date('d M Y',strtotime($invoice_date_bill)).' </td>
										<td>'.strtoupper($invoice_number).'</td>
										<td>Drug Purchases</td>
										<td></td>
										<td>'.number_format($invoice_amount, 2).'</td>
										<td>'.number_format($total_arrears, 2).'</td>
										<td><a href="'.site_url().'inventory/orders/goods_received_notes/'.$order_id.'" class="btn btn-xs btn-success" target="_blank"> View Invoice </a></td>
									</tr> 
								';
							
						}
					}

				}


				if($credit_note_query->num_rows() > 0)
				{
					foreach ($credit_note_query->result() as $credit_note) {
						# code...
						$invoice_date_bill = $credit_note->invoice_date;
						$invoice_number = $credit_note->invoice_number;
						$order_id = $credit_note->order_id;
						$credit_note_amount = $this->get_total_supplied_invoice($order_id);
						$invoice_explode = explode('-', $invoice_date_bill);

						if(($credit_note_amount > 0))
						{
							$total_arrears -= $credit_note_amount;
							$total_payment_amount += $credit_note_amount;
						
								$result .= 
								'
									<tr>
										<td>'.date('d M Y',strtotime($invoice_date_bill)).' </td>
										<td>'.strtoupper($invoice_number).'</td>
										<td>Credit Note</td>
										<td>'.number_format($credit_note_amount, 2).'</td>
										<td></td>
										<td>'.number_format($total_arrears, 2).'</td>
										<td><a href="'.site_url().'goods-transfered-notes/'.$order_id.'" class="btn btn-xs btn-success" target="_blank"> View Note </a></td>
									</tr> 
								';
							
						}
					}

				}
				

			}
							
			//display loan
			$result .= 
			'
				<tr>
					<th colspan="3">Total</th>
					<th>'.number_format($total_payment_amount, 2).'</th>
					<th>'.number_format($total_invoice_balance, 2).'</th>
					<td>'.number_format($total_arrears, 2).'</td>
				</tr> 
			';
			



			$response['total_arrears'] = $total_arrears;
			$response['total_invoice_balance'] = $total_invoice_balance;
			$response['invoice_date'] = $invoice_date;
			$response['result'] = $result;
			$response['total_payment_amount'] = $total_payment_amount;

			// var_dump($response); die();

			return $response;
		}

		public function get_creditor_statement_print($creditor_id)
		{

			$creditor_query = $this->creditors_model->get_opening_creditor_balance($creditor_id);
			$bills = $this->get_all_creditor_invoices($creditor_id);

			$bills_query = $this->get_all_suppplier_invoices($creditor_id);
			$credit_note_query = $this->get_all_suppplier_credit_note($creditor_id);
			// var_dump($bills_query); 
			$payments = $this->get_all_payments_creditor($creditor_id);

			$brought_forward_balance = $this->get_balance_brought_forward($creditor_id);

			


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



			if($creditor_query->num_rows() > 0)
			{
				$row = $creditor_query->row();
				$opening_balance = $row->opening_balance;
				$created = $row->created;
				$debit_id = $row->debit_id;
				// var_dump($debit_id); die();
				if($debit_id == 2)
				{
					// this is deni
					$result .= 
								'
									<tr>
										<td>'.date('d M Y',strtotime($created)).' </td>
										<td>OPENING BALANCE</td>
										<td></td>
										<td>'.number_format($opening_balance, 2).'</td>
										<td></td>
									</tr> 
								';
					$total_payment_amount = $opening_balance;

				}
				else
				{
					// this is a prepayment
					$result .= 
								'
									<tr>
										<td>'.date('d M Y',strtotime($created)).' </td>
										<td>OPENING BALANCE</td>
										<td></td>
										<td></td>
										<td>'.number_format($opening_balance, 2).'</td>
									</tr> 
								';
					$total_invoice_balance = $opening_balance;
				}
			}
			

			if($brought_forward_balance == FALSE)
			{
				$result .='';
			}

			else
			{
				$search_title = $this->session->userdata('creditor_search_title');
				if($brought_forward_balance < 0)
				{
					$positive = -$brought_forward_balance;
					$result .= 
								'
									<tr>
										<td colspan=3> B/F</td>
										<td>'.number_format($positive, 2).'</td>
										<td></td>
									</tr> 
								';
					$total_invoice_balance += $positive;

				}
				else
				{
					$result .= 
								'
									<tr>
										<td > B/F</td>
										<td></td>
										<td>'.number_format($brought_forward_balance, 2).'</td>
									</tr> 
								';


					$total_invoice_balance += $brought_forward_balance;
				}
			}


			if($bills->num_rows() > 0)
			{
				foreach ($bills->result() as $key_bills) {
					# code...
					$invoice_date = $key_bills->invoice_date;
					$invoice_number = $key_bills->invoice_number;
					$invoice_amount = $key_bills->invoice_amount;
					$transaction_type_id = $key_bills->transaction_type_id;
					$invoice_explode = explode('-', $invoice_date);
					$invoice_year = $invoice_explode[0];
					$invoice_month = $invoice_explode[1];
					$account_invoice_description = $key_bills->account_invoice_description;
					$account_to_id = $key_bills->account_to_id;
					$account_from_id = $key_bills->account_from_id;
					$account_invoice_id = $key_bills->account_invoice_id;
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
							$account_payment_id = $payments_key->account_payment_id;


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
											<td>PAYMENT</td>
											<td></td>
											<td>'.number_format($payment_amount, 2).'</td>
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
						
						$account_name = $this->get_account_name($account_to_id);
						if($transaction_type_id == 1)
						{
							$total_arrears += $invoice_amount;
							$total_invoice_balance += $invoice_amount;
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($invoice_date)).' </td>
									<td>'.strtoupper($invoice_number).'</td>
									<td>'.$account_invoice_description.'</td>
									<td></td>
									<td>'.number_format($invoice_amount, 2).'</td>
								</tr> 
							';
						}
						else
						{
							$total_arrears -= $invoice_amount;
							$total_invoice_balance -= $invoice_amount;
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($invoice_date)).' </td>
									<td>'.strtoupper($invoice_number).'</td>
									<td>CREDIT NOTE: '.$account_invoice_description.'</td>
									<td></td>
									<td>('.number_format($invoice_amount, 2).')</td>
								</tr> 
							';
						}
					}

					if($bills_query->num_rows() > 0)
					{
						foreach ($bills_query->result() as $supplier) {
							# code...
							$invoice_date_bill = $supplier->invoice_date;
							$invoice_number = $supplier->invoice_number;
							$order_id = $supplier->order_id;
							$invoice_amount = $this->get_total_supplied_invoice($order_id);
							$invoice_explode = explode('-', $invoice_date_bill);

							if(($invoice_date_bill <= $invoice_date) && ($invoice_date_bill > $last_date) && ($invoice_amount > 0))
							{
								$total_arrears += $invoice_amount;
								$total_invoice_balance += $invoice_amount;
							
									$result .= 
									'
										<tr>
											<td>'.date('d M Y',strtotime($invoice_date_bill)).' </td>
											<td>'.strtoupper($invoice_number).'</td>
											<td>Drug Purchases</td>
											<td></td>
											<td>'.number_format($invoice_amount, 2).'</td>
										</tr> 
									';
								
							}
						}

					}

					if($credit_note_query->num_rows() > 0)
					{
						foreach ($credit_note_query->result() as $credit_note) {
							# code...
							$invoice_date_bill = $credit_note->invoice_date;
							$invoice_number = $credit_note->invoice_number;
							$order_id = $credit_note->order_id;
							$credit_note_amount = $this->get_total_supplied_invoice($order_id);
							$invoice_explode = explode('-', $invoice_date_bill);

							if(($invoice_date_bill <= $invoice_date) && ($invoice_date_bill > $last_date) && ($credit_note_amount > 0))
							{
								$total_arrears -= $credit_note_amount;
								$total_payment_amount += $credit_note_amount;
							
									$result .= 
									'
										<tr>
											<td>'.date('d M Y',strtotime($invoice_date_bill)).' </td>
											<td>'.strtoupper($invoice_number).'</td>
											<td>Credit Note</td>
											<td>'.number_format($credit_note_amount, 2).'</td>
											<td></td>
										</tr> 
									';
								
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

								$payment_explode = explode('-', $payment_date);
								$payment_year = $payment_explode[0];
								$payment_month = $payment_explode[1];
								$payment_amount = $payments_key->amount_paid;
								$account_payment_id = $payments_key->account_payment_id;

								if(($payment_date > $invoice_date) &&  ($payment_amount > 0))
								{
									$total_arrears -= $payment_amount;
									// if($payment_year >= $current_year)
									// {
										$result .= 
										'
											<tr>
												<td>'.date('d M Y',strtotime($payment_date)).' </td>
												<td>PAYMENT</td>
												<td></td>
												<td>'.number_format($payment_amount, 2).'</td>
												<td></td>
											</tr> 
										';
									// }
									
									$total_payment_amount += $payment_amount;

								}
							}
						}

						if($bills_query->num_rows() > 0)
						{
							foreach ($bills_query->result() as $supplier) {
								# code...
								$invoice_date_bill = $supplier->invoice_date;
								$invoice_number = $supplier->invoice_number;
								$order_id = $supplier->order_id;
								$invoice_amount = $this->get_total_supplied_invoice($order_id);
								$invoice_explode = explode('-', $invoice_date_bill);

								if(($invoice_date_bill > $invoice_date) &&  ($invoice_amount > 0))
								{
									$total_arrears += $invoice_amount;
									$total_invoice_balance += $invoice_amount;
								
										$result .= 
										'
											<tr>
												<td>'.date('d M Y',strtotime($invoice_date_bill)).' </td>
												<td>'.strtoupper($invoice_number).'</td>
												<td>Drug Purchases</td>
												<td></td>
												<td>'.number_format($invoice_amount, 2).'</td>
											</tr> 
										';
									
								}
							}

						}


						if($credit_note_query->num_rows() > 0)
						{
							foreach ($credit_note_query->result() as $credit_note) {
								# code...
								$invoice_date_bill = $credit_note->invoice_date;
								$invoice_number = $credit_note->invoice_number;
								$order_id = $credit_note->order_id;
								$credit_note_amount = $this->get_total_supplied_invoice($order_id);
								$invoice_explode = explode('-', $invoice_date_bill);

								if(($invoice_date_bill > $invoice_date) &&  ($credit_note_amount > 0))
								{
									$total_arrears -= $credit_note_amount;
									$total_payment_amount += $credit_note_amount;
								
										$result .= 
										'
											<tr>
												<td>'.date('d M Y',strtotime($invoice_date_bill)).' </td>
												<td>'.strtoupper($invoice_number).'</td>
												<td>Credit Note</td>
												<td>'.number_format($credit_note_amount, 2).'</td>
												<td></td>
											</tr> 
										';
									
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
						$account_payment_id = $payments_key->account_payment_id;

						if(($payment_amount > 0))
						{
							$total_arrears -= $payment_amount;
							// if($payment_year >= $current_year)
							// {
								$result .= 
								'
									<tr>
										<td>'.date('d M Y',strtotime($payment_date)).' </td>
										<td>PAYMENT</td>
										<td></td>
										<td>'.number_format($payment_amount, 2).'</td>
									</tr> 
								';
							// }
							
							$total_payment_amount += $payment_amount;

						}
					}
				}

				if($bills_query->num_rows() > 0)
				{
					foreach ($bills_query->result() as $supplier) {
						# code...
						$invoice_date_bill = $supplier->invoice_date;
						$invoice_number = $supplier->invoice_number;
						$order_id = $supplier->order_id;
						$invoice_amount = $this->get_total_supplied_invoice($order_id);
						$invoice_explode = explode('-', $invoice_date_bill);

						if(($invoice_amount > 0))
						{
							$total_arrears += $invoice_amount;
							$total_invoice_balance += $invoice_amount;
						
								$result .= 
								'
									<tr>
										<td>'.date('d M Y',strtotime($invoice_date_bill)).' </td>
										<td>'.strtoupper($invoice_number).'</td>
										<td>Drug Purchases</td>
										<td></td>
										<td>'.number_format($invoice_amount, 2).'</td>
									</tr> 
								';
							
						}
					}

				}


				if($credit_note_query->num_rows() > 0)
				{
					foreach ($credit_note_query->result() as $credit_note) {
						# code...
						$invoice_date_bill = $credit_note->invoice_date;
						$invoice_number = $credit_note->invoice_number;
						$order_id = $credit_note->order_id;
						$credit_note_amount = $this->get_total_supplied_invoice($order_id);
						$invoice_explode = explode('-', $invoice_date_bill);

						if(($credit_note_amount > 0))
						{
							$total_arrears -= $credit_note_amount;
							$total_payment_amount += $credit_note_amount;
						
								$result .= 
								'
									<tr>
										<td>'.date('d M Y',strtotime($invoice_date_bill)).' </td>
										<td>'.strtoupper($invoice_number).'</td>
										<td>Credit Note</td>
										<td>'.number_format($credit_note_amount, 2).'</td>
										<td></td>
									</tr> 
								';
							
						}
					}

				}
				

			}
							
			//display loan
			$result .= 
			'
				<tr>
					<th colspan="3">Total</th>
					<th>'.number_format($total_payment_amount, 2).'</th>
					<th>'.number_format($total_invoice_balance, 2).'</th>
				</tr> 
			';
			$result .= 
			'
				<tr>
					<th colspan="3"></th>
					<th colspan="2" style="text-align:center;">'.number_format(($total_invoice_balance-$total_payment_amount ), 2).'</th>
				</tr> 
			';





			$response['total_arrears'] = $total_arrears;
			$response['total_invoice_balance'] = $total_invoice_balance;
			$response['invoice_date'] = $invoice_date;
			$response['result'] = $result;
			$response['total_payment_amount'] = $total_payment_amount;

			// var_dump($response); die();

			return $response;
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

	public function get_opening_creditor_balance($creditor_id)
	{
		$this->db->select('*'); 
		$this->db->where('creditor_id = '.$creditor_id.'' );
		$query = $this->db->get('creditor');
		
		return $query;
	}

	public function get_opening_provider_balance($provider_id)
	{
		$this->db->select('*'); 
		$this->db->where('provider_id = '.$provider_id.'' );
		$query = $this->db->get('provider_account');
		
		return $query;
	}


	public function get_all_creditors_values()
	{
		$this->db->select('*'); 
		$this->db->where('creditor_id  > 0' );
		$creditor_result = $this->db->get('creditor');
		if($creditor_result->num_rows() > 0)
		{
			foreach ($creditor_result->result() as $key => $creditor) {
				# code...
				$creditor_id = $creditor->creditor_id;

				$this->db->select('*'); 
				$this->db->where('creditor_id = '.$creditor_id.' AND creditor_account_delete = 0' );
				$query = $this->db->get('creditor_account');

				if($query->num_rows() > 0)
				{
					foreach ($query->result() as $key => $value) {
						# code...

						$creditor_account_description = $value->creditor_account_description;
						$creditor_account_amount = $value->creditor_account_amount;
						$creditor_account_date = $value->creditor_account_date;
						$transaction_type_id = $value->transaction_type_id;
						$transaction_code = $value->transaction_code;

						if($transaction_type_id == 2)
						{
							$account = array(
								'account_to_id'=>12,//$this->input->post('account_to_id'),
								'account_from_id'=>$creditor_id,
								'invoice_amount'=>$creditor_account_amount,
								'account_invoice_description'=>$creditor_account_description,
			                    'account_to_type'=>2,//$this->input->post('transaction_type_id'),
			                    'invoice_date'=>$creditor_account_date,
			                    'invoice_number'=>$transaction_code,
			                    'created_by'=>$this->session->userdata('personnel_id'),
			                    'created'=>date('Y-m-d')
								);
							$this->db->insert('account_invoices',$account);
						}
						else if($transaction_type_id == 1)
						{


							$account = array(
								'account_to_id'=>$creditor_id,//$this->input->post('account_to_id'),
								'account_from_id'=>3,
								'amount_paid'=>$creditor_account_amount,//$this->input->post('amount'),
								'account_payment_description'=>$creditor_account_description,//$this->input->post('description'),
			                    'account_to_type'=>2,//$this->input->post('account_to_type'),
			                    'payment_date'=>$creditor_account_date,
			                    'created_by'=>$this->session->userdata('personnel_id'),
			                    'created'=>date('Y-m-d')
								);
							$this->db->insert('account_payments',$account);

						}
						
					}
				}
			}
		}

			
	}

	public function get_cash_collection($payment_month,$payment_year,$provider_id,$patient_type = 0,$payment_week=NULL)
	{
		$search = $this->session->userdata('provider_invoice_search');

		if(!empty($search))
		{
			$invoice_search = $search;
		}
		else
		{
			$invoice_search = '';
		}

		if($payment_week == NULL)
		{
			$payment_week = '';
			$select_addition ='';
			$addition = '';
		}
		else
		{
			$payment_week = ' AND WEEK(visit.visit_date) = '.$payment_week;
			$select_addition =',WEEK(visit.visit_date) AS week';
			$addition =',WEEK(visit.visit_date)';
		}


		if($patient_type == 1)
		{
			$visit_type = ' AND MONTH(visit.visit_date) = "'.$payment_month.'" AND YEAR(visit.visit_date) = "'.$payment_year.'" '.$payment_week.' AND visit.visit_type = 1';
		}
		else
		{
			$visit_type = ' AND MONTH(visit.visit_date) = "'.$payment_month.'" AND YEAR(visit.visit_date) = "'.$payment_year.'" '.$payment_week.' AND visit.visit_type <> 1';
		}

		$date_from = $this->session->userdata('providers_date_from');
		$date_to = $this->session->userdata('providers_date_to');

		if(!empty($date_from) AND !empty($date_to))
		{
			$search_add =  ' AND (visit_date >= \''.$date_from.'\' AND visit_date <= \''.$date_to.'\') ';
			$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
		}
		else if(!empty($date_from))
		{
			$search_add = ' AND visit_date = \''.$date_from.'\'';
			$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
		}
		else if(!empty($date_to))
		{
			$search_add = ' AND visit_date = \''.$date_to.'\'';
			$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
		}
		
		$this->db->from('visit,visit_charge');
		$this->db->select('visit_date,YEAR(visit.visit_date),MONTH(visit.visit_date) '.$select_addition.',SUM(visit_charge.visit_charge_amount*visit_charge.visit_charge_units) AS total_charged_amount');
		$this->db->where('visit.visit_id = visit_charge.visit_id AND visit_charge.charged = 1 AND visit_charge.visit_charge_delete = 0 AND visit.visit_delete = 0 AND visit.personnel_id = '.$provider_id.''.$visit_type.' '.$search_add);
		$this->db->order_by('YEAR(visit.visit_date),MONTH(visit.visit_date)'.$addition,'ASC');
		$this->db->group_by('YEAR(visit.visit_date),MONTH(visit.visit_date)'.$addition);
		$query = $this->db->get();
		$total_charged_amount = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_charged_amount = $value->total_charged_amount;
			}
		}
		return $total_charged_amount;
	}



	public function get_cash_waivers_collection($payment_month,$payment_year,$provider_id,$patient_type = 0,$payment_week=NULL)
	{
		$search = $this->session->userdata('provider_invoice_search');

		if(!empty($search))
		{
			$invoice_search = $search;
		}
		else
		{
			$invoice_search = '';
		}

		if($payment_week == NULL)
		{
			$payment_week = '';
			$select_addition ='';
			$addition = '';
		}
		else
		{
			$payment_week = ' AND WEEK(visit.visit_date) = '.$payment_week;
			$select_addition =',WEEK(visit.visit_date) AS week';
			$addition =',WEEK(visit.visit_date)';
		}


		if($patient_type == 1)
		{
			$visit_type = ' AND MONTH(visit.visit_date) = "'.$payment_month.'" AND YEAR(visit.visit_date) = "'.$payment_year.'" '.$payment_week.' AND visit.visit_type = 1';
		}
		else
		{
			$visit_type = ' AND MONTH(visit.visit_date) = "'.$payment_month.'" AND YEAR(visit.visit_date) = "'.$payment_year.'" '.$payment_week.' AND visit.visit_type <> 1';
		}

		$date_from = $this->session->userdata('providers_date_from');
		$date_to = $this->session->userdata('providers_date_to');

		if(!empty($date_from) AND !empty($date_to))
		{
			$search_add =  ' AND (visit_date >= \''.$date_from.'\' AND visit_date <= \''.$date_to.'\') ';
			$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
		}
		else if(!empty($date_from))
		{
			$search_add = ' AND visit_date = \''.$date_from.'\'';
			$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
		}
		else if(!empty($date_to))
		{
			$search_add = ' AND visit_date = \''.$date_to.'\'';
			$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
		}
		
		$this->db->from('visit,payments');
		$this->db->select('visit_date,YEAR(visit.visit_date),MONTH(visit.visit_date) '.$select_addition.',SUM(amount_paid) AS total_paid_amount');
		$this->db->where('visit.visit_id = payments.visit_id AND payments.cancel = 0 AND payments.payment_type = 2 AND visit.visit_delete = 0 AND visit.personnel_id = '.$provider_id.''.$visit_type.' '.$search_add);
		$this->db->order_by('YEAR(visit.visit_date),MONTH(visit.visit_date)'.$addition,'ASC');
		$this->db->group_by('YEAR(visit.visit_date),MONTH(visit.visit_date)'.$addition);
		$query = $this->db->get();
		$total_paid_amount = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_paid_amount = $value->total_paid_amount;
			}
		}
		return $total_paid_amount;
	}


	public function get_cash_collection_charged($payment_month,$payment_year,$provider_id,$patient_type = 0,$payment_week = null)
	{
		$search = $this->session->userdata('provider_invoice_search');

		if(!empty($search))
		{
			$invoice_search = $search;
		}
		else
		{
			$invoice_search = '';
		}

		if($payment_week == NULL)
		{
			$payment_week = '';
			$select_addition ='';
			$addition = '';
		}
		else
		{
			$payment_week = ' AND WEEK(visit.visit_date) = '.$payment_week;
			$select_addition =',WEEK(visit.visit_date) AS week';
			$addition =',WEEK(visit.visit_date)';
		}
		if($patient_type == 1)
		{
			$visit_type = ' AND MONTH(visit.visit_date) = "'.$payment_month.'" AND YEAR(visit.visit_date) = "'.$payment_year.'" '.$payment_week.' AND doctor_invoice.type = 1';
		}
		else
		{
			$visit_type = ' AND MONTH(visit.visit_date) = "'.$payment_month.'" AND YEAR(visit.visit_date) = "'.$payment_year.'" '.$payment_week.' AND doctor_invoice.type <> 1';
		}
		
		$date_from = $this->session->userdata('providers_date_from');
		$date_to = $this->session->userdata('providers_date_to');

		if(!empty($date_from) AND !empty($date_to))
		{
			$search_add =  ' AND (visit_date >= \''.$date_from.'\' AND visit_date <= \''.$date_to.'\') ';
			$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
		}
		else if(!empty($date_from))
		{
			$search_add = ' AND visit_date = \''.$date_from.'\'';
			$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
		}
		else if(!empty($date_to))
		{
			$search_add = ' AND visit_date = \''.$date_to.'\'';
			$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
		}


		$this->db->from('visit,doctor_invoice');
		$this->db->select('visit_date,SUM(doctor_invoice.invoiced_amount) AS total_charged_amount,YEAR(visit.visit_date) AS year,MONTH(visit.visit_date) AS month'.$select_addition);
		$this->db->where('visit.visit_id = doctor_invoice.visit_id AND (doctor_invoice.doctor_invoice_status = 1 OR doctor_invoice.doctor_invoice_status = 2) AND visit.visit_type = 1 AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL) AND visit.visit_delete = 0 AND visit.personnel_id = '.$provider_id.''.$visit_type.' '.$search_add);
		$this->db->order_by('YEAR(visit.visit_date),MONTH(visit.visit_date)'.$addition,'ASC');
		$this->db->group_by('YEAR(visit.visit_date),MONTH(visit.visit_date)'.$addition);
		$query = $this->db->get();
		$total_charged_amount = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_charged_amount = $value->total_charged_amount;
			}
		}
		return $total_charged_amount;
	}

	public function get_all_personnel_providers($table, $where,$order,$order_method,$config,$page)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $config, $page);
		//var_dump($query);die();
		return $query;
	}
	public function getStartAndEndDate($week, $year)
	{

	    $time = strtotime("1 January $year", time());
	    $day = date('w', $time);
	    $time += ((7*$week)+1-$day)*24*3600;
	    $return[0] = date('Y-n-j', $time);
	    $time += 6*24*3600;
	    $return[1] = date('Y-n-j', $time);
	    return $return;
	}
	public function get_provider_cash_statement($provider_id)
	{
		$creditor_query = $this->creditors_model->get_opening_provider_balance($provider_id);
		$bills = $this->get_all_provider_invoices($provider_id);
		$all_collections = $this->get_all_provider_work_done_weekly($provider_id);
		// var_dump($all_collections); die();
		$payments = $this->get_all_payments_provider($provider_id);

		$brought_forward_balance = $this->get_provider_balance_brought_forward($provider_id);


		// $week_start_


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


		$opening_balance = 0;

		$opening_date = date('Y-m-d');
		$debit_id = 2;
		// var_dump($creditor_query->num_rows()); die();
		if($creditor_query->num_rows() > 0)
		{
			$row = $creditor_query->row();
			$opening_balance = $row->opening_balance;
			$opening_date = $row->created;
			$debit_id = $row->debit_id;
			// var_dump($debit_id); die();
			if($debit_id == 2)
			{
				// this is deni
				$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($created)).' </td>
									<td colspan=4>Opening Balance</td>
									<td>'.number_format($opening_balance, 2).'</td>
									<td></td>
									<td></td>
								</tr> 
							';
				$total_invoice_balance = $opening_balance;

			}
			else
			{
				// this is a prepayment
				$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($created)).' </td>
									<td colspan=5>Opening Balance</td>
									<td>'.number_format($opening_balance, 2).'</td>
									<td></td>
								</tr> 
							';
				$total_payment_amount = $opening_balance;
			}
		}
		

		if($brought_forward_balance == FALSE)
		{
			$result .='';
		}

		else
		{
			$search_title = $this->session->userdata('creditor_search_title');
			if($brought_forward_balance < 0)
			{
				$positive = -$brought_forward_balance;
				$result .= 
							'
								<tr>
									<td colspan=4> B/F</td>
									<td>'.number_format($positive, 2).'</td>
									<td></td>
								</tr> 
							';
				$total_invoice_balance += $positive;

			}
			else
			{
				$result .= 
							'
								<tr>
									<td colspan=5> B/F</td>
									<td></td>
									<td>'.number_format($brought_forward_balance, 2).'</td>
								</tr> 
							';


				$total_invoice_balance += $brought_forward_balance;
			}
		}


		if($all_collections->num_rows() > 0)
		{
			foreach ($all_collections->result() as $collections_key) {
				# code...
				$visit_date = $collections_key->visit_date;
				$year = $collections_key->year;
				$month = $collections_key->month;
				$week = $collections_key->week;
				$payment_explode = explode('-', $visit_date);
				$payment_year = $payment_explode[0];
				$payment_month = $payment_explode[1];
				$visit_charge_amount = $collections_key->visit_charge_amount;
				$amount_charged = $collections_key->total_charged_amount;
				$cash_amount = $this->get_cash_collection($month,$year,$provider_id,1,$week);
				$invoice_amount = $this->get_cash_collection($month,$year,$provider_id,0,$week);
				$waiver_amount = $this->get_cash_waivers_collection($month,$year,$provider_id,1,$week);

				$cash_amount = $cash_amount - $waiver_amount;
				$cash_amount_charged = $this->get_cash_collection_charged($month,$year,$provider_id,1,$week);
				$invoice_amount_charged = $this->get_cash_collection_charged($month,$year,$provider_id,0,$week);

				$lab_work_charge_cash = $this->get_all_lab_works($month,$year,$provider_id,1,$week);
				$lab_work_charge_insurance = $this->get_all_lab_works($month,$year,$provider_id,0,$week);


				 $timestamp = mktime( 0, 0, 0, 1, 1,  $year ) + ( $week * 7 * 24 * 60 * 60 );
		        $timestamp_for_monday = $timestamp - 86400 * ( date( 'N', $timestamp ) - 1 );
		        $start_date = date( 'Y-m-d', $timestamp_for_monday );

		        $end_date = date('Y-m-d', strtotime($start_date. ' + 5 days'));


				$payments = $this->get_all_payments_provider_weekly($provider_id,$start_date,$end_date,$week);
				$total_payment_amount += $payments;

				$cash_amount_charged =  $cash_amount_charged - $lab_work_charge_cash;
				$invoice_amount_charged =  $invoice_amount_charged - $lab_work_charge_insurance;

				$cash_charged = 0.4 * $cash_amount_charged;
				$insurance_charged = 0.3 * $invoice_amount_charged;

				$amount_charged = $cash_charged;

				// calculate all lab works done then

				// if(empty($lab_work_charge))
				// {
				$lab_work_charge = $lab_work_charge_insurance;
				// }

				$amount_charged = $amount_charged;

			
				$total_invoice_balance += $amount_value;
				$date = $year.'-'.$month.'-01';
				$amount_value =  $amount_charged;







				// $amount_value = 0.3 * $amount_value;
				// if(($amount_value > 0))
				// {
					$total_arrears += $amount_value - $payments;
					// var_dump($payment_year); die();
					// if($payment_year >= $current_year)
					// {
						$result .= 
						'
							<tr>
								<td>'.date('M Y',strtotime($date)).' Week '.$week.' </td>
								<td>'.number_format($cash_amount, 2).'</td>
								<td>'.number_format($cash_amount_charged, 2).'</td>
								<td>'.number_format($lab_work_charge, 2).'</td>
								<td>'.number_format($amount_charged, 2).'</td>
								<td>'.number_format($amount_value, 2).'</td>
								<td>'.number_format($payments,2).' </td>
								<td>'.number_format($total_arrears,2).' </td>
								<td><a href="'.site_url().'view-doctor-patients/'.$provider_id.'/'.$month.'/'.$year.'/'.$week.'/1" target="_blank" class="btn btn-xs btn-success" >view patients</a></td>
							</tr> 
						';
					// }
					

				// }

				
			}
		}
						
		//display loan
		$result .= 
		'
			<tr>
				<th colspan="5">Total</th>
				<th>'.number_format($total_invoice_balance, 2).'</th>
				<th>'.number_format($total_payment_amount, 2).'</th>
				<td></td>
			</tr> 
		';
		$result .= 
		'
			<tr>
				<th colspan="5"></th>
				<th colspan="2" style="text-align:center;">'.number_format($total_invoice_balance - $total_payment_amount, 2).'</th>
			</tr> 
		';



		$response['total_arrears'] = $total_arrears;
		$response['total_invoice_balance'] = $total_invoice_balance;
		$response['invoice_date'] = $invoice_date;
		$response['opening_balance'] = $opening_balance;
		$response['opening_date'] = $opening_date;
		$response['debit_id'] = $debit_id;
		$response['result'] = $result;
		$response['total_payment_amount'] = $total_payment_amount;

		// var_dump($response); die();

		return $response;
	}
	public function get_provider_statement_old($provider_id)
	{
		$creditor_query = $this->creditors_model->get_opening_provider_balance($provider_id);
		$bills = $this->get_all_provider_invoices($provider_id);
		$all_collections = $this->get_all_provider_work_done($provider_id);
		// var_dump($all_collections); die();
		$payments = $this->get_all_payments_provider($provider_id);

		$brought_forward_balance = $this->get_provider_balance_brought_forward($provider_id);

		


		$x=0;

		$bills_result = '';
		$last_date = '';
		$visit_last_date = '';
		$current_year = date('Y');
		$total_invoices = $bills->num_rows();
		$invoices_count = 0;
		$total_invoice_balance = 0;
		$total_arrears = 0;
		$total_payment_amount = 0;
		$result = '';
		$total_pardon_amount = 0;


		$opening_balance = 0;

		$opening_date = date('Y-m-d');
		$debit_id = 2;
		// var_dump($creditor_query->num_rows()); die();
		if($creditor_query->num_rows() > 0)
		{
			$row = $creditor_query->row();
			$opening_balance = $row->opening_balance;
			$opening_date = $row->created;
			$debit_id = $row->debit_id;
			// var_dump($debit_id); die();
			if($debit_id == 2)
			{
				// this is deni
				$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($created)).' </td>
									<td colspan=5>Opening Balance</td>
									<td>'.number_format($opening_balance, 2).'</td>
									<td></td>
									<td></td>
								</tr> 
							';
				$total_invoice_balance = $opening_balance;

			}
			else
			{
				// this is a prepayment
				$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($created)).' </td>
									<td colspan=6>Opening Balance</td>
									<td>'.number_format($opening_balance, 2).'</td>
									<td></td>
								</tr> 
							';
				$total_payment_amount = $opening_balance;
			}
		}
		

		if($brought_forward_balance == FALSE)
		{
			$result .='';
		}

		else
		{
			$search_title = $this->session->userdata('creditor_search_title');
			if($brought_forward_balance < 0)
			{
				$positive = -$brought_forward_balance;
				$result .= 
							'
								<tr>
									<td colspan=5> B/F</td>
									<td>'.number_format($positive, 2).'</td>
									<td></td>
								</tr> 
							';
				$total_invoice_balance += $positive;

			}
			else
			{
				$result .= 
							'
								<tr>
									<td colspan=6> B/F</td>
									<td></td>
									<td>'.number_format($brought_forward_balance, 2).'</td>
								</tr> 
							';


				$total_invoice_balance += $brought_forward_balance;
			}
		}
		if($all_collections->num_rows() > 0)
		{
			foreach ($all_collections->result() as $collections_key) {
				# code...
				$visit_date = $collections_key->visit_date;
				$bill_explode = explode('-', $visit_date);
				$billing_year = $bill_explode[0];
				$billing_month = $bill_explode[1];
				$start_date = $billing_year.'-'.$billing_month.'-01';

				$end_date =  date("Y-m-t", strtotime($start_date));
				$visit_charge_amount = $collections_key->visit_charge_amount;
				$amount_charged = $collections_key->total_charged_amount;



				//get all loan deductions before date
				


				$cash_amount = $this->get_cash_collection($billing_month,$billing_year,$provider_id,1);
				$invoice_amount = $this->get_cash_collection($billing_month,$billing_year,$provider_id,0);


				$cash_amount_charged = $this->get_cash_collection_charged($billing_month,$billing_year,$provider_id,1);
				$invoice_amount_charged = $this->get_cash_collection_charged($billing_month,$billing_year,$provider_id,0);


				$lab_work_charge_cash = $this->get_all_lab_works($billing_month,$billing_year,$provider_id,1);
				$lab_work_charge_insurance = $this->get_all_lab_works($billing_month,$billing_year,$provider_id,0);





				$payments = $this->get_all_payments_provider_monthly($provider_id,$start_date,$end_date,$week);
				$credit = $this->get_all_provider_credit_month($provider_id,$start_date,$end_date);
				$total_payment_amount += $payments;



				$cash_amount_charged =  $cash_amount_charged - $lab_work_charge_cash;
				$invoice_amount_charged =  $invoice_amount_charged - $lab_work_charge_insurance;

				$cash_charged = 0.4 * $cash_amount_charged;
				$insurance_charged = 0.3 * $invoice_amount_charged;

				$amount_charged = $cash_charged+$insurance_charged;

				// calculate all lab works done then

				// if(empty($lab_work_charge))
				// {
					$lab_work_charge = $lab_work_charge_insurance + $lab_work_charge_insurance;
				// }

				$amount_charged = $amount_charged;

				if($amount_charged > 24000)
				{
					$wht = 0.05 * $amount_charged;
					$amount_value = $amount_charged - $wht;
				}
				else
				{
					$wht = 0;
					$amount_value =  $amount_charged;
				}
				// $amount_value = 0.3 * $amount_value;
				// if(($amount_value > 0))
				// {
					$total_arrears += $amount_value;
					// var_dump($billing_year); die();
					// if($billing_year >= $current_year)
					// {
						$result .= 
						'
							<tr>
								<td>'.date('M Y',strtotime($visit_date)).' Invoice </td>
								<td>'.number_format($invoice_amount, 2).'</td>
								<td>'.number_format($invoice_amount_charged, 2).'</td>
								<td>'.number_format($amount_charged, 2).'</td>
								<td>'.number_format($lab_work_charge, 2).'</td>
								<td>'.number_format($wht, 2).'</td>
								<td>'.number_format($amount_value, 2).'</td>
								<td>'.number_format($payments,2).' </td>

								<td>('.number_format($credit,2).') </td>
								<td>'.number_format($amount_value  - $payments - $credit,2).' </td>
								<td><a href="'.site_url().'view-doctor-patients/'.$provider_id.'/'.$billing_month.'/'.$billing_year.'" target="_blank" class="btn btn-xs btn-success" >view patients</a></td>
							</tr> 
						';
					// }
					
					$total_invoice_balance += $amount_value;

				// }


					
				$visit_last_date = $end_month;
			}
		}

		
		
						
		//display loan
		$result .= 
		'
			<tr>
				<th colspan="6">Total</th>
				<th>'.number_format($total_invoice_balance, 2).'</th>
				<th>'.number_format($total_payment_amount, 2).'</th>
				<td></td>
			</tr> 
		';
		$result .= 
		'
			<tr>
				<th colspan="6"></th>
				<th colspan="2" style="text-align:center;">'.number_format($total_invoice_balance - $total_payment_amount, 2).'</th>
			</tr> 
		';



		$response['total_arrears'] = $total_arrears;
		$response['total_invoice_balance'] = $total_invoice_balance;
		$response['invoice_date'] = $invoice_date;
		$response['opening_balance'] = $opening_balance;
		$response['opening_date'] = $opening_date;
		$response['debit_id'] = $debit_id;
		$response['result'] = $result;
		$response['total_payment_amount'] = $total_payment_amount;

		// var_dump($response); die();

		return $response;
	}

	public function get_provider_statement($provider_id,$personnel_type_id,$personnel_percentage)
	{
		$creditor_query = $this->creditors_model->get_opening_provider_balance($provider_id);
		$bills = $this->get_all_provider_invoices($provider_id);
		$all_collections = $this->get_all_provider_work_done($provider_id);
		// var_dump($all_collections); die();
		$payments = $this->get_all_payments_provider($provider_id);

		$brought_forward_balance = $this->get_provider_balance_brought_forward($provider_id);

		


		$x=0;

		$bills_result = '';
		$last_date = '';
		$visit_last_date = '';
		$current_year = date('Y');
		$total_invoices = $bills->num_rows();
		$invoices_count = 0;
		$total_invoice_balance = 0;
		$total_arrears = 0;
		$total_payment_amount = 0;
		$result = '';
		$total_pardon_amount = 0;


		$opening_balance = 0;

		$opening_date = date('Y-m-d');
		$debit_id = 2;
		// var_dump($creditor_query->num_rows()); die();
		if($creditor_query->num_rows() > 0)
		{
			$row = $creditor_query->row();
			$opening_balance = $row->opening_balance;
			$opening_date = $row->created;
			$debit_id = $row->debit_id;
			// var_dump($debit_id); die();
			if($debit_id == 2)
			{
				// this is deni
				$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($created)).' </td>
									<td colspan=5>Opening Balance</td>
									<td>'.number_format($opening_balance, 2).'</td>
									<td></td>
									<td></td>
								</tr> 
							';
				$total_invoice_balance = $opening_balance;

			}
			else
			{
				// this is a prepayment
				$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($created)).' </td>
									<td colspan=6>Opening Balance</td>
									<td>'.number_format($opening_balance, 2).'</td>
									<td></td>
								</tr> 
							';
				$total_payment_amount = $opening_balance;
			}
		}
		

		if($brought_forward_balance == FALSE)
		{
			$result .='';
		}

		else
		{
			$search_title = $this->session->userdata('creditor_search_title');
			if($brought_forward_balance < 0)
			{
				$positive = -$brought_forward_balance;
				$result .= 
							'
								<tr>
									<td colspan=5> B/F</td>
									<td>'.number_format($positive, 2).'</td>
									<td></td>
								</tr> 
							';
				$total_invoice_balance += $positive;

			}
			else
			{
				$result .= 
							'
								<tr>
									<td colspan=6> B/F</td>
									<td></td>
									<td>'.number_format($brought_forward_balance, 2).'</td>
								</tr> 
							';


				$total_invoice_balance += $brought_forward_balance;
			}
		}
		$hospital_total = 0;
		$doctors_total = 0;
		$total_invoice = 0;
		$total_charged = 0;
		$total_gross_payable = 0;
		$total_wht = 0;
		$total_net_payable = 0;
		$total_payments = 0;
		$total_balance = 0;
		$rate_total = 0;
		$days_total = 0;
		$hosp_payable = 0;
		$gross_total = 0;
		$gross_payable = 0;
		$total_doctors = 0;
		$total_hospital = 0;

		if($all_collections->num_rows() > 0)
		{
			foreach ($all_collections->result() as $collections_key) {
				# code...
				$visit_date = $collections_key->visit_date;
				$bill_explode = explode('-', $visit_date);
				$billing_year = $bill_explode[0];
				$billing_month = $bill_explode[1];
				$start_date = $date_from = $billing_year.'-'.$billing_month.'-01';

				$end_date = $date_to =  date("Y-m-t", strtotime($start_date));
				$visit_charge_amount = $collections_key->visit_charge_amount;
				$amount_charged = $collections_key->total_charged_amount;



				//get all loan deductions before date

				$cash_invoices = $this->reports_model->get_total_collected($provider_id, $date_from, $date_to,1);
				$insurance_invoices = $this->reports_model->get_total_collected($provider_id, $date_from, $date_to,2);
				
				
				// var_dump($cash_invoices);die();
				$doc_total = $personnel_percentage * $doc_total;
				
				$gross_payable = $cash_invoices + $insurance_invoices;

				$checked_values = $this->get_hospital_billed_item($provider_id,$billing_year,$billing_month);

				$days = $checked_values['days'];
				$rate = $checked_values['rate'];
				$amount = $checked_values['amount'];
				$lab_work = $amount;
				$payments = $this->get_all_payments_provider_monthly($provider_id,$start_date,$end_date,$week);
				$credit = $this->get_all_provider_credit_month($provider_id,$start_date,$end_date);
				$total_payment_amount += $payments;

				$net_payable = $gross_payable - $lab_work;


				$hospital_total += $cash_invoices;
				$doctors_total += $insurance_invoices;
				$total_invoice += $cash_invoices + $insurance_invoices;
				$total_charged += $invoice_amount_charged;
				$total_gross_payable += $amount_charged;
				$total_wht += $lab_work;
				$total_net_payable += $net_payable;
				$total_payments += $credit;
				$total_balance += $amount_value - $credit;

				$hosp_payable += $amount;
				$rate_total += $rate;
				$days_total += $days;
				$doctors_rate = 0.4 * $net_payable;
				$hospital_rate = 0.6 * $net_payable;


				$total_doctors += $doctors_rate;
				$total_hospital += $hospital_rate;
				// if(($amount_value > 0))
				// {
				$total_balance += $doctors_rate  - $payments - $credit;
					// var_dump($billing_year); die();
					// if($billing_year >= $current_year)
					// {
						$result .= 
						'
							<tr>
								<td>'.date('M Y',strtotime($visit_date)).' Invoice </td>
								<td>'.number_format($cash_invoices, 2).'</td>
								<td>'.number_format($insurance_invoices, 2).'</td>
								<td>'.number_format($gross_payable, 2).'</td>
								<td>('.number_format($lab_work, 2).')</td>
								<td>'.number_format($net_payable, 2).'</td>
								<td>'.number_format($hospital_rate,2).' </td>
								<td>'.number_format($doctors_rate,2).' </td>
								<td>'.number_format($credit + $payments,2).' </td>

								<td>'.number_format($doctors_rate  - $payments - $credit,2).' </td>
								<td><a href="'.site_url().'view-doctor-patients/'.$provider_id.'/'.$billing_month.'/'.$billing_year.'"  class="btn btn-xs btn-success" >view patients</a></td>

								<td><button type="button" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#book-appointment'.$billing_year.''.$billing_month.''.$provider_id.''.$personnel_type_id.'"><i class="fa fa-plus"></i> Update </button>
								<div class="modal fade " id="book-appointment'.$billing_year.''.$billing_month.''.$provider_id.''.$personnel_type_id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
								    <div class="modal-dialog modal-lg" role="document">
								        <div class="modal-content ">
								            <div class="modal-header">
								            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								            	<h4 class="modal-title" id="myModalLabel">Lab Work for the Month '.$personnel_onames.' '.$personnel_fname.'</h4>
								            </div>
								            '.form_open('accounting/creditors/save_billing/'.$provider_id.'/'.$billing_month.'/'.$billing_year.'/'.$personnel_type_id, array("class" => "form-horizontal")).'

								            <div class="modal-body">
								            	<div class="row">
								            		<input type="hidden" name="redirect_url" id="redirect_url" value="'.$this->uri->uri_string().'">
								            		<div class="form-group" style="display:none">
					                                    <label class="col-md-4 control-label">No of Days Worked *</label>
					                                    
					                                    <div class="col-md-7">
					                                        <input type="text" class="form-control" name="days" placeholder="5" value="1"/>
					                                    </div>
					                                </div>
					                                <div class="form-group">
					                                    <label class="col-md-4 control-label">Lab Work</label>
					                                    
					                                    <div class="col-md-7">
					                                        <input type="text" class="form-control" name="rate" placeholder="20000" value="'.$rate.'"/>
					                                    </div>
					                                </div>
								            		
														
								              	</div>
								            </div>
								            <div class="modal-footer">
								            	<button type="submit"  class="btn btn-sm btn-success" onclick="return confirm(\' Do you want to update the statemnt ? \')">Update Values</button>
								                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
								            </div>

								               '.form_close().'
								        </div>
								    </div>
								</div>

							</td>

							
							</tr> 
						';
					// }
					
					$total_invoice_balance += $amount_value;

				// }


					
				$visit_last_date = $end_month;
			}
		}

		
		
						
		//display loan
		$result .= 
		'
			<tr>
				<th colspan="1">Total</th>
				<th>'.number_format($hospital_total, 2).'</th>
				<th>'.number_format($doctors_total, 2).'</th>
				<th>'.number_format($total_invoice, 2).'</th>
				<th>('.number_format($total_wht, 2).')</th>
				<th>'.number_format($total_net_payable, 2).'</th>
				<th>'.number_format($total_hospital, 2).'</th>
				<th>'.number_format($total_doctors, 2).'</th>
				<th>'.number_format($total_payments, 2).'</th>
				<th>'.number_format($total_balance, 2).'</th>
				<td></td>
			</tr> 
		';
		$result .= 
		'
			<tr>
				<th colspan="7"></th>
				<th colspan="3" style="text-align:center;">'.number_format($total_balance, 2).'</th>
			</tr> 
		';



		$response['total_gross_payable'] = $total_invoice;
		$response['total_net_payable'] = $total_net_payable;
		$response['hosp_payable'] = $hospital_total;
		$response['doctors_total'] = $doctors_total;
		$response['total_wht'] = $total_wht;
		$response['total_hospital'] = $total_hospital;
		$response['total_doctors'] = $total_doctors;
		$response['opening_balance'] = $opening_balance;
		$response['opening_date'] = $opening_date;
		$response['debit_id'] = $debit_id;
		$response['result'] = $result;
		$response['total_payments'] = $total_payments;
		$response['total_balance'] = $total_balance;
		// if($provider_id == 3)
		// {
		// 	var_dump($response['hosp_payable']); die();
		// }
		

		return $response;
	}



	public function get_provider_statement_print($provider_id)
	{

		$creditor_query = $this->creditors_model->get_opening_provider_balance($provider_id);
		$bills = $this->get_all_provider_invoices($provider_id);
		// var_dump($bills); 
		$payments = $this->get_all_payments_provider($provider_id);

		$brought_forward_balance = $this->get_provider_balance_brought_forward($provider_id);

		


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


		$opening_balance = 0;

		$opening_date = date('Y-m-d');
		$debit_id = 2;
		// var_dump($creditor_query->num_rows()); die();
		if($creditor_query->num_rows() > 0)
		{
			$row = $creditor_query->row();
			$opening_balance = $row->opening_balance;
			$opening_date = $row->created;
			$debit_id = $row->debit_id;
			// var_dump($debit_id); die();
			if($debit_id == 2)
			{
				// this is deni
				$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($created)).' </td>
									<td>Opening Balance</td>
									<td></td>
									<td>'.number_format($opening_balance, 2).'</td>
									<td></td>
								</tr> 
							';
				$total_invoice_balance = $opening_balance;

			}
			else
			{
				// this is a prepayment
				$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($created)).' </td>
									<td>Opening Balance</td>
									<td></td>
									<td></td>
									<td>'.number_format($opening_balance, 2).'</td>
								</tr> 
							';
				$total_payment_amount = $opening_balance;
			}
		}
		

		if($brought_forward_balance == FALSE)
		{
			$result .='';
		}

		else
		{
			$search_title = $this->session->userdata('creditor_search_title');
			if($brought_forward_balance < 0)
			{
				$positive = -$brought_forward_balance;
				$result .= 
							'
								<tr>
									<td colspan=3> B/F</td>
									<td>'.number_format($positive, 2).'</td>
								</tr> 
							';
				$total_invoice_balance += $positive;

			}
			else
			{
				$result .= 
							'
								<tr>
									<td > B/F</td>
									<td></td>
									<td>'.number_format($brought_forward_balance, 2).'</td>
								</tr> 
							';


				$total_invoice_balance += $brought_forward_balance;
			}
		}


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
				$account_invoice_description = $key_bills->account_invoice_description;
				$account_to_id = $key_bills->account_to_id;
				$account_from_id = $key_bills->account_from_id;
				$account_invoice_id = $key_bills->account_invoice_id;
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
						$account_payment_id = $payments_key->account_payment_id;


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
					$account_name = $this->get_account_name($account_to_id);
					// if($invoice_year >= $current_year)
					// {
						$result .= 
						'
							<tr>
								<td>'.date('d M Y',strtotime($invoice_date)).' </td>
								<td>'.$invoice_number.'</td>
								<td>'.$account_invoice_description.'</td>
								<td>'.number_format($invoice_amount, 2).'</td>
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
							$account_payment_id = $payments_key->account_payment_id;

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
					$account_payment_id = $payments_key->account_payment_id;

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
				<th colspan="2" style="text-align:center;">'.number_format($total_invoice_balance - $total_payment_amount, 2).'</th>
			</tr> 
		';



		$response['total_arrears'] = $total_arrears;
		$response['total_invoice_balance'] = $total_invoice_balance;
		$response['invoice_date'] = $invoice_date;
		$response['opening_balance'] = $opening_balance;
		$response['opening_date'] = $opening_date;
		$response['debit_id'] = $debit_id;
		$response['result'] = $result;
		$response['total_payment_amount'] = $total_payment_amount;

		// var_dump($response); die();

		return $response;
	}
public function get_doctor()
	{
		$table = "personnel,personnel_type";
		$where = "personnel.personnel_type_id = personnel_type.personnel_type_id AND personnel_type.personnel_type_name = 'Service Provider'";
		$items = "personnel.personnel_onames, personnel.personnel_fname, personnel.personnel_id";
		$order = "personnel_onames";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}

	public function get_hospital_billed_item($provider_id,$billing_year=null,$billing_month=null)
	{
		if(!empty($billing_year) AND !empty($billing_month))
		{
			$checked_items = ' AND billing_year = "'.$billing_year.'" AND billing_month ="'.$billing_month.'"';
		}
		else
		{
			$checked_items = '';
		}

		$this->db->where('provider_id = '.$provider_id.$checked_items);

		$query = $this->db->get('providers_billing');
		$amount = 0;
		$days = 0;
		$rate = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$days = $value->days;
				$rate = $value->rate;
				$amount = $days * $rate;


			}
			
		}

		$checked['days'] = $days;
		$checked['rate'] = $rate;
		$checked['amount'] = $amount;

		return $checked;
		
	}

	public function record_providers_billing($provider_id,$billing_month,$billing_year,$personnel_type_id)
	{
		$array['provider_id'] = $provider_id;
		$array['billing_month'] = $billing_month;
		$array['billing_month'] = $billing_month;
		$array['billing_year'] = $billing_year;
		$array['days'] = $this->input->post('days');
		$array['rate'] = $this->input->post('rate');
		$array['created_by'] = $this->session->userdata('personnel_id');
		$array['created'] = date('Y-m-d');


		$this->db->where('provider_id = '.$provider_id.' AND billing_year = "'.$billing_year.'" AND billing_month ="'.$billing_month.'"');

		$query = $this->db->get('providers_billing');

		if($query->num_rows() > 0)
		{
			$this->db->where('provider_id = '.$provider_id.' AND billing_year = "'.$billing_year.'" AND billing_month ="'.$billing_month.'"');
			$this->db->update('providers_billing',$array);
		}
		else
		{
			$this->db->insert('providers_billing',$array);
		}

		return TRUE;
	}

}
?>