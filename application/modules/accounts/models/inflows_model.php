<?php

class Inflows_model extends CI_Model 
{	
	
	/*
	*	Add a new creditor
	*
	*/
	public function add_inflow()
	{
		$data = array(
		
			'inflow_name'=>$this->input->post('inflow_name'),
			'contact_name'=>$this->input->post('contact_name'),
			'contact_phone'=>$this->input->post('contact_phone'),
			'location'=>$this->input->post('location'),
			'description'=>$this->input->post('description')
			
		);
		
		if($this->db->insert('inflow', $data))
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
	public function edit_inflow($inflow_id)
	{
		$data = array(
			'inflow_name'=>$this->input->post('inflow_name'),
			'contact_name'=>$this->input->post('contact_name'),
			'contact_phone'=>$this->input->post('contact_phone'),
			'location'=>$this->input->post('location'),
			'description'=>$this->input->post('description'),
			'modified_by'=>0
		);
		
		$this->db->where('inflow_id', $inflow_id);
		if($this->db->update('inflow', $data))
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
	public function get_inflow($inflow_id)
	{
		//retrieve all users
		$this->db->from('inflow');
		$this->db->select('*');
		$this->db->where('inflow_id = '.$inflow_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Retrieve all creditor
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_inflows($table, $where, $per_page, $page, $order = 'inflow_name', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	public function calculate_balance_brought_forward($date_from,$creditor_id)
	{

		if(empty($date_from))
		{
			
			return 0;

		}
		else
		{

			
			$this->db->select('(
(SELECT SUM(creditor_account_amount) FROM creditor_account WHERE creditor_account_status = 1 AND transaction_type_id = 1 AND creditor_account_date < \''.$date_from.'\' AND creditor_id= '.$creditor_id.')
-
(SELECT SUM(creditor_account_amount) FROM creditor_account WHERE creditor_account_status = 1 AND transaction_type_id = 2 AND creditor_account_date < \''.$date_from.'\' AND creditor_id = '.$creditor_id.')
) AS balance_brought_forward', TRUE); 
		$this->db->where('creditor_account_date < \''.$date_from.'\' AND creditor_id = '.$creditor_id.'' );

			$this->db->group_by('balance_brought_forward');
			$query = $this->db->get('creditor_account');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				// var_dump($row->balance_brought_forward); die();
				return $row->balance_brought_forward;

			}
			
			else
			{
				return 0;
			}


		}
	
		
	}
	
	public function get_inflow_account($where, $table)
	{
		$this->db->select('*');
		//$this->db->join('account', 'creditor_account.account_id = account.account_id', 'left');
		$this->db->where($where);
		$this->db->order_by('inflow_account_date', 'ASC');
		$query = $this->db->get($table);
		
		return $query;
	}
	
	public function record_inflow_account($inflow_id)
	{
		$array = array(
			"inflow_account_date" => $this->input->post('creditor_account_date'),
			"transaction_type_id" => $this->input->post('transaction_type_id'),
			"property_id" => $this->input->post('property_id'),
			"inflow_service_id" => $this->input->post('inflow_service_id'),
			"inflow_account_amount" => $this->input->post('inflow_account_amount'),
			"inflow_id" => $inflow_id,
			"property_id" => $this->input->post('property_id'),
			"transaction_code"=>$this->input->post('transaction_code'),
			'created' => $this->input->post('inflow_account_date'),
			"created_by" => $this->session->userdata('personnel_id'),
			"modified_by" => $this->session->userdata('personnel_id')
		);
		
		$transaction_type_id = $this->input->post('transaction_type_id');
		
		if($this->db->insert('inflow_account', $array))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	public function get_active_inflow_services()
	{
		$table = "inflow_service";
		$where = "inflow_service_status = 1";
		
		$this->db->where($where);
		$query = $this->db->get($table);
		
		return $query;
	}
	public function get_inflow_service_name($inflow_service_id)
	{
		$this->db->select('inflow_service_name');
		$this->db->where('inflow_service_id = '.$inflow_service_id);
		$query = $this->db->get('inflow_service');
		$service_name = '';
		if($query->num_rows()>0)
		{
			foreach($query->result() as $name)
			{
				$service_name = $name->inflow_service_name;
			}
		}
		return $service_name;
	}
	public function get_property_name($property_id)
	{
		$this->db->select('property_name');
		$this->db->where('property_id = '.$property_id);
		$query = $this->db->get('property');
		$property_name = '';
		if($query->num_rows()>0)
		{
			foreach($query->result() as $name)
			{
				$property_name = $name->property_name;
			}
		}
		return $property_name;
	}
	public function get_opening_balance($creditor_id)
	{
		$opening_bal = 0;
		$this->db->select('creditor_opening_balance');
		$this->db->where('creditor_id = '.$creditor_id);
		$query = $this->db->get('creditor');
		
		if($query->num_rows() > 0)
		{
			$bal_row = $query->row();
			$opening_bal = $bal_row->creditor_opening_balance;
		}
		return $opening_bal;
	}
	public function get_debit_opening_balance($creditor_id)
	{
		$debit_opening_bal = 0;
		$this->db->select('debit_creditor_opening_balance');
		$this->db->where('creditor_id = '.$creditor_id);
		$query = $this->db->get('creditor');
		
		if($query->num_rows() > 0)
		{
			$bal_row = $query->row();
			$debit_opening_bal = $bal_row->debit_creditor_opening_balance;
		}
		return $debit_opening_bal;
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
	public function get_inflow_total($inflow_id)
	{
		$payment_total = 0;

		$this->db->select(' SUM(inflow_account_amount) AS total_payment');
		$this->db->where('inflow_account_status = 1 AND transaction_type_id = 1 AND inflow_account_delete = 0 AND inflow_id = '.$inflow_id);
		$query = $this->db->get ('inflow_account'); 
		
		$payment_total_row = $query->row();
		$payment_total = $payment_total_row->total_payment;

		return $payment_total;

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
		else
		{
			return FALSE;
		}
	}
}