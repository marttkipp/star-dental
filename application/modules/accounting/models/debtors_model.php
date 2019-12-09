<?php

class Debtors_model extends CI_Model 
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
	public function get_all_debtors($table, $where, $per_page, $page, $order = 'visit_type_name', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$this->db->join('visit_type_account','visit_type.visit_type_id = visit_type_account.visit_type','left');
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


	public function get_debtor_statement_value($visit_type_id,$date,$checked)
	{
		// invoices
		$invoice = '';
		$start_date = '2018-03-01';
		$first_date = date('Y-m').'-01';
		if($checked == 1)
		{
			// 30 days
			$invoice = ' AND visit.visit_date >= "'.$start_date.'" AND visit.visit_date >= "'.$first_date.'" AND visit.visit_date <= "'.$date.'" ';
		
		}
		else if($checked == 2)
		{
			// 30 days
			$sixty_months = date('Y-m-01');
			$sixty_months = date('Y-m-d',strtotime ( '-1 month' , strtotime ( $sixty_months ) ) );
			// var_dump($sixty_months); die();
			$newdate = date('Y-m-t',strtotime ( '+0 month' , strtotime ( $sixty_months ) ) );
			$last_date = date('Y-m-t', strtotime($newdate));

			// $last_date = date('Y-m-d', strtotime('-2 months'));
			// var_dump($last_date); die();
			$invoice = ' AND visit.visit_date >= "'.$start_date.'" AND visit.visit_date >= "'.$sixty_months.'" AND visit.visit_date <= "'.$last_date.'" ';
		}
		else if($checked == 3)
		{
			// 60 days
			// var_dump($checked); die();
			// 30 days
			$sixty_months = date('Y-m-01');
			$sixty_months = date('Y-m-d',strtotime ( '-2 month' , strtotime ( $sixty_months ) ) );
			// var_dump($sixty_months); die();
			$newdate = date('Y-m-t',strtotime ( '+0 month' , strtotime ( $sixty_months ) ) );
			$last_date = date('Y-m-t', strtotime($newdate));


			$invoice = ' AND visit.visit_date >= "'.$start_date.'"AND visit.visit_date >= "'.$sixty_months.'" AND visit.visit_date <= "'.$last_date.'" ';
		}

		else if($checked == 4)
		{
			// over 90 days

			$sixty_months = date('Y-m-01');
			$sixty_months = date('Y-m-d',strtotime ( '-3 month' , strtotime ( $sixty_months ) ) );
			// var_dump($sixty_months); die();
			$newdate = date('Y-m-t',strtotime ( '+0 month' , strtotime ( $sixty_months ) ) );
			$last_date = date('Y-m-t', strtotime($newdate));

			$invoice = ' AND visit.visit_date >= "'.$start_date.'"AND visit.visit_date >= "'.$sixty_months.'" AND visit.visit_date <= "'.$last_date.'" ';
			// $invoice = ' AND visit.visit_date >= "'.$start_date.'" AND visit.visit_date <= "'.$last_date.'" ';
		}
		else if($checked == 5)
		{
			// over 120 days

			$sixty_months = date('Y-m-01');
			$sixty_months = date('Y-m-d',strtotime ( '-4 month' , strtotime ( $sixty_months ) ) );
			// var_dump($sixty_months); die();
			$newdate = date('Y-m-t',strtotime ( '+0 month' , strtotime ( $sixty_months ) ) );
			$last_date = date('Y-m-t', strtotime($newdate));

			$invoice = ' AND visit.visit_date >= "'.$start_date.'" AND visit.visit_date >= "'.$sixty_months.'" AND visit.visit_date <= "'.$last_date.'" ';
			// $invoice = ' AND visit.visit_date >= "'.$start_date.'" AND visit.visit_date <= "'.$last_date.'" ';
		}
		else if($checked == 6)
		{
			// over 120 days

			$sixty_months = date('Y-m-01');
			$sixty_months = date('Y-m-d',strtotime ( '-5 month' , strtotime ( $sixty_months ) ) );
			// var_dump($sixty_months); die();
			$newdate = date('Y-m-t',strtotime ( '+0 month' , strtotime ( $sixty_months ) ) );
			$last_date = date('Y-m-t', strtotime($newdate));

			// var_dump($last_date); die();

			$invoice = ' AND visit.visit_date >= "'.$start_date.'" AND visit.visit_date <= "'.$last_date.'" ';
			// $invoice = ' AND visit.visit_date >= "'.$start_date.'" AND visit.visit_date <= "'.$last_date.'" ';
		}
	

		$this->db->where('visit_charge.visit_charge_delete = 0 AND (visit.parent_visit IS NULL OR visit.parent_visit = 0) AND visit.visit_id = visit_charge.visit_id AND visit.visit_delete = 0 AND visit_charge.charged = 1 AND visit.visit_type = '.$visit_type_id.' '.$invoice);
		$this->db->select('SUM(visit_charge_amount*visit_charge_units) AS total_invoice');
		$query = $this->db->get('visit_charge,visit');
		$total_invoice = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_invoice = $value->total_invoice;
			}
		}


		$this->db->where('visit.visit_id = visit_bill.visit_parent AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL) AND visit.visit_delete = 0 AND visit_bill.visit_type_id = '.$visit_type_id.' '.$invoice);
		$this->db->select('SUM(visit_bill_amount) AS total_invoice');
		$query = $this->db->get('visit_bill,visit');
		$total_rejected_invoice = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_rejected_invoice = $value->total_invoice;
			}
		}




		$this->db->where('(visit.parent_visit = 0 OR visit.parent_visit IS NULL) AND visit.visit_delete = 0 AND visit.visit_type = '.$visit_type_id.' '.$invoice);
		$this->db->select('SUM(rejected_amount) AS total_invoice');
		$rejected = $this->db->get('visit');
		$rejections = 0;
		if($rejected->num_rows() > 0)
		{
			foreach ($rejected->result() as $key => $value) {
				# code...
				$rejections = $value->total_invoice;
			}
		}

		$total_rejected_amount += $rejections;

		$this->db->where('cancel = 0 AND visit.visit_id = payments.visit_id AND visit.visit_delete = 0 AND payments.payment_type = 2 AND visit.visit_type = '.$visit_type_id.' '.$invoice);
		$this->db->select('SUM(amount_paid) AS total_payments');
		$query_waiver = $this->db->get('payments,visit');
		$total_waiver = 0;
		if($query_waiver->num_rows() > 0)
		{
			foreach ($query_waiver->result() as $key => $value) {
				# code...
				$total_waiver = $value->total_payments;
			}
		}




		$this->db->where('cancel = 0 AND visit.visit_id = payments.visit_id AND visit.visit_delete = 0 AND payments.payment_type = 1 AND visit.visit_type = '.$visit_type_id.' '.$invoice);
		$this->db->select('SUM(amount_paid) AS total_payments');
		$query_payments = $this->db->get('payments,visit');
		$total_payments = 0;
		if($query_payments->num_rows() > 0)
		{
			foreach ($query_payments->result() as $key => $value) {
				# code...
				$total_payments = $value->total_payments;
			}
		}
		
		$amount = ($total_invoice) - ($total_payments + $total_waiver);
		// if($visit_type_id == 1 AND $checked == 2)
		// {
		// 	var_dump($total_invoice); die();
		// }
		// if($amount < 0)
		// {
		// 	$amount = -$amount;
		// }

		return $amount;

	}

	public function get_debtor_total_payments($visit_type_id)
	{
		$start_date = date('2018-03-01');
		$invoice = ' AND visit.visit_date >= "'.$start_date.'"';

		$this->db->where('cancel = 0 AND visit.visit_id = payments.visit_id AND visit.visit_delete = 0 AND payments.payment_type = 1 AND visit.visit_type = '.$visit_type_id.' '.$invoice);
		$this->db->select('SUM(amount_paid) AS total_payments');
		$query_payments = $this->db->get('payments,visit');
		$total_payments = 0;
		if($query_payments->num_rows() > 0)
		{
			foreach ($query_payments->result() as $key => $value) {
				# code...
				$total_payments = $value->total_payments;
			}
		}

		return $total_payments;

	}
	public function get_all_provider_work_done($visit_type)
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
			$start_date = date('2018-03-01');
			$invoice = ' AND visit.visit_date >= "'.$start_date.'"';
			
			$this->db->from('visit');
			$this->db->select('visit_date');
			$this->db->where('visit.visit_delete = 0 AND visit.close_card <> 0 AND visit.visit_type = '.$visit_type.''.$invoice);
			$this->db->order_by('YEAR(visit.visit_date),MONTH(visit.visit_date)','ASC');
			$this->db->group_by('YEAR(visit.visit_date),MONTH(visit.visit_date)');
			$query = $this->db->get();
			return $query;
		}
		public function get_opening_debtor_balance($visit_type_id)
		{
			$this->db->select('*'); 
			$this->db->where('visit_type = '.$visit_type_id.'' );
			$query = $this->db->get('visit_type_account');
			
			return $query;
		}

	public function get_total_invoice_collection($visit_type_id,$start_date,$end_date,$week)
	{
		if(!empty($start_date) AND !empty($end_date))
		{
			$search_add =  ' AND (visit_date >= \''.$start_date.'\' AND visit_date <= \''.$end_date.'\') ';
			$search_payment_add =  ' AND (visit_date >= \''.$start_date.'\' AND visit_date <= \''.$end_date.'\') ';
		}
		else if(!empty($start_date))
		{
			$search_add = ' AND visit_date = \''.$start_date.'\'';
			$search_payment_add = ' AND visit_date = \''.$start_date.'\'';
		}
		else if(!empty($end_date))
		{
			$search_add = ' AND visit_date = \''.$end_date.'\'';
			$search_payment_add = ' AND visit_date = \''.$end_date.'\'';
		}
		$start_date = date('2018-03-01');
		$invoice = ' AND visit.visit_date >= "'.$start_date.'"';
		$this->db->from('visit,visit_charge');
		$this->db->select('SUM(visit_charge.visit_charge_amount*visit_charge.visit_charge_units) AS total_charged_amount');
		$this->db->where('visit.visit_id = visit_charge.visit_id AND visit_charge.charged = 1 AND visit_charge.visit_charge_delete = 0  AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL) AND visit.visit_delete = 0 AND visit.visit_type = '.$visit_type_id.''.$invoice.''.$search_add);
		$query = $this->db->get();
		$total_charged_amount = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_charged_amount = $value->total_charged_amount;
			}
		}
		// var_dump($total_charged_amount); die();
		return $total_charged_amount;
	}

	public function get_all_payments_debtor_monthly($visit_type,$start_date,$end_date,$payment_week)
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
			$search_payment_add =  ' AND (visit_date >= \''.$start_date.'\' AND visit_date <= \''.$end_date.'\') ';
		}
		else if(!empty($start_date))
		{
			$search_add = ' AND visit_date = \''.$start_date.'\'';
			$search_payment_add = ' AND visit_date = \''.$start_date.'\'';
		}
		else if(!empty($end_date))
		{
			$search_add = ' AND visit_date = \''.$end_date.'\'';
			$search_payment_add = ' AND visit_date = \''.$end_date.'\'';
		}
		$start_date = date('2018-03-01');
			$invoice = ' AND visit.visit_date >= "'.$start_date.'"';

		
		$this->db->from('visit,payments');
		$this->db->select('SUM(amount_paid) AS total_payments');
		$this->db->where('visit.visit_delete = 0  AND payments.payment_type = 1 AND payments.cancel = 0 AND visit.visit_id = payments.visit_id AND visit.visit_type = '.$visit_type.$search_add.$invoice.'');
		$waiver_query = $this->db->get('');
		$total_amount = 0;
		if($waiver_query->num_rows() > 0)
		{
			foreach ($waiver_query->result() as $key => $value) {
				# code...
				$total_amount =$value->total_payments;

			}
		}
		// var_dump($total_amount); die();
		return $total_amount;
	}
	public function get_all_provider_waiver_month($visit_type,$start_date,$end_date,$payment_week)
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
			$search_payment_add =  ' AND (visit_date >= \''.$start_date.'\' AND visit_date <= \''.$end_date.'\') ';
		}
		else if(!empty($start_date))
		{
			$search_add = ' AND visit_date = \''.$start_date.'\'';
			$search_payment_add = ' AND visit_date = \''.$start_date.'\'';
		}
		else if(!empty($end_date))
		{
			$search_add = ' AND visit_date = \''.$end_date.'\'';
			$search_payment_add = ' AND visit_date = \''.$end_date.'\'';
		}


		
		$this->db->from('visit,payments');
		$this->db->select('SUM(amount_paid) AS total_payments');
		$this->db->where('visit.visit_delete = 0  AND payments.payment_type = 2 AND payments.cancel = 0 AND visit.visit_id = payments.visit_id AND visit.visit_type = '.$visit_type.$search_payment_add.'');
		$waiver_query = $this->db->get('');
		$total_amount = 0;
		if($waiver_query->num_rows() > 0)
		{
			foreach ($waiver_query->result() as $key => $value) {
				# code...
				$total_amount =$value->total_payments;

			}
		}

		return $total_amount;
	}
	public function get_all_debtor_rejections($visit_type,$start_date,$end_date,$payment_week)
	{

		if(!empty($start_date) AND !empty($end_date))
		{
			$search_add =  ' AND (visit_date >= \''.$start_date.'\' AND visit_date <= \''.$end_date.'\') ';
			$search_payment_add =  ' AND (visit_date >= \''.$start_date.'\' AND visit_date <= \''.$end_date.'\') ';
		}
		else if(!empty($start_date))
		{
			$search_add = ' AND visit_date = \''.$start_date.'\'';
			$search_payment_add = ' AND visit_date = \''.$start_date.'\'';
		}
		else if(!empty($end_date))
		{
			$search_add = ' AND visit_date = \''.$end_date.'\'';
			$search_payment_add = ' AND visit_date = \''.$end_date.'\'';
		}
		$start_date = date('2018-03-01');
		$invoice = ' AND visit.visit_date >= "'.$start_date.'"';

		$this->db->where('visit.visit_id = visit_bill.visit_parent AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL) AND visit.visit_delete = 0 AND visit.visit_type = '.$visit_type.' '.$search_add.''.$invoice);
		$this->db->select('SUM(visit_bill_amount) AS total_invoice');
		$rejected_query = $this->db->get('visit_bill,visit');
		$total_rejected_invoice = 0;
		if($rejected_query->num_rows() > 0)
		{
			foreach ($rejected_query->result() as $key => $value) {
				# code...
				$total_rejected_invoice = $value->total_invoice;
			}
		}
		return $total_rejected_invoice;

	}
	public function get_debtor_statement($visit_type_id)
	{
		$creditor_query = $this->get_opening_debtor_balance($visit_type_id);
		// $bills = $this->get_all_provider_invoices($visit_type_id);
		$all_collections = $this->get_all_provider_work_done($visit_type_id);
		// var_dump($all_collections); die();
		// $payments = $this->get_all_payments_provider($visit_type_id);

		// $brought_forward_balance = $this->get_provider_balance_brought_forward($visit_type_id);

		// var_dump($all_collections);


		$x=0;

		$bills_result = '';
		$last_date = '';
		$visit_last_date = '';
		$current_year = date('Y');
		// $total_invoices = $bills->num_rows();
		$invoices_count = 0;
		$total_invoice_balance = 0;
		$total_arrears = 0;
		$total_payment_amount = 0;
		$result = '';
		$total_pardon_amount = 0;


		$opening_balance = 0;

		$total_invoice_amount = 0;
			$total_transfer_amount = 0;
			$total_credit_amount = 0;
			$total_bill_amount = 0;
			$total_payment_amount = 0;
			$total_rejected_amount = 0;
			$total_arrears_amount = 0;

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
				// this is deniget_all_provider_credit_month
				$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($created)).' </td>
									<td colspan=5>Opening Balance</td>
									<td>'.number_format($opening_balance, 2).'</td>
								</tr> 
							';
				$total_arrears_amount += $opening_balance;

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
								</tr> 
							';
				$total_payment_amount = $opening_balance;
				$total_arrears_amount -= $opening_balance;
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
				$invoice_amount = $this->get_total_invoice_collection($visit_type_id,$start_date,$end_date,$week);
				$rejected_amount = $this->get_all_debtor_rejections($visit_type_id,$start_date,$end_date,$week);
				$payments = $this->get_all_payments_debtor_monthly($visit_type_id,$start_date,$end_date,$week);
				$credit = $this->get_all_provider_waiver_month($visit_type_id,$start_date,$end_date,$week);
				// $total_payment_amount += $payments;



				$total_bill = ($invoice_amount + $rejected_amount) - $credit;

				$total_invoice_amount += $invoice_amount;
				$total_waiver_amount += $credit;
				$total_rejected_amount += $rejected_amount;
				$total_payment_amount += $payments;
				$total_bill_amount += $total_bill;
				$total_arrears = $total_bill - $payments - $rejected_amount;
				$total_arrears_amount += $total_arrears;
				
					$result .= 
					'
						<tr>
							<td>'.date('M Y',strtotime($visit_date)).' Invoice </td>
							<td>'.number_format($invoice_amount - $credit, 2).'</td>
							<td>'.number_format($rejected_amount, 2).'</td>
							<td>('.number_format($credit, 2).')</td>
							<td>'.number_format($total_bill, 2).'</td>
							<td>('.number_format($payments, 2).')</td>
							<td>'.number_format($total_arrears, 2).'</td>
							<td><a href="'.site_url().'export-debtor-invoices/'.$visit_type_id.'/'.$start_date.'/'.$end_date.'"  class="btn btn-xs btn-success" >export invoices</a></td>
						</tr> 
					';
				
				$total_invoice_balance += $amount_value;

					
				$visit_last_date = $end_month;
			}

			$result .= 
					'
						<tr>
							<td><strong>Total Amount</strong> </td>
							<td><strong>'.number_format($total_invoice_amount, 2).'</strong></td>
							<td><strong>'.number_format($total_rejected_amount, 2).'</strong></td>
							<td><strong>('.number_format($total_credit_amount, 2).')</strong></td>
							<td><strong>'.number_format($total_bill_amount, 2).'</strong></td>
							<td><strong>('.number_format($total_payment_amount, 2).')</strong></td>
							<td><strong>'.number_format($total_arrears_amount, 2).'</strong></td>
						</tr> 
					';
		}

		
		
	



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

	function export_debtor_statement($visit_type_id,$start_date,$end_date)
	{
		$this->load->library('excel');
		
		
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL) AND visit.visit_delete = 0 AND visit.visit_type = '.$visit_type_id.' AND (visit.visit_date >= "'.$start_date.'" AND visit.visit_date <= "'.$end_date.'" ) ';
		$table = 'visit, patients, visit_type';
		$visit_search = $this->session->userdata('debtors_search_query');
		// var_dump($visit_search);die();
		if(!empty($visit_search))
		{
			$where .= '';//$visit_search;
		
			
			
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'" ';
			$where .= '';

		}
		
		$this->db->where($where);
		$this->db->select('visit.*, (visit.visit_time_out - visit.visit_time) AS waiting_time, patients.*, visit_type.visit_type_name,personnel.personnel_fname,personnel.personnel_onames,visit.rejected_amount AS amount_rejected');
		$this->db->join('personnel','visit.personnel_id = personnel.personnel_id','left');
		$this->db->order_by('visit.visit_date','DESC');
		$this->db->group_by('visit.visit_id');
		$visits_query = $this->db->get($table);

		// var_dump($visits_query); die();
		
		$title = 'Debtors Report '.date('jS M Y',strtotime($start_date)).' '.date('jS M Y',strtotime($end_date));
		$col_count = 0;
		
		if($visits_query->num_rows() > 0)
		{
			$count = 0;
			/*
				-----------------------------------------------------------------------------------------
				Document Header
				-----------------------------------------------------------------------------------------
			*/
			$row_count = 0;
			$report[$row_count][$col_count] = '#';
			$col_count++;
			$report[$row_count][$col_count] = 'Visit Date';
			$col_count++;
			$report[$row_count][$col_count] = 'Name';
			$col_count++;
			$report[$row_count][$col_count] = 'Invoice Number';
			$col_count++;
			$report[$row_count][$col_count] = 'Procedures';
			$col_count++;
			$report[$row_count][$col_count] = 'Doctor';
			$col_count++;
			$report[$row_count][$col_count] = 'Invoice Amount';
			$col_count++;
			$report[$row_count][$col_count] = 'Balance';
			$col_count++;
			//display all patient data in the leftmost columns
			foreach($visits_query->result() as $row)
			{
				$row_count++;
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
				$patient_number = $row->patient_number;
				$patient_id = $row->patient_id;
				$personnel_id = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				$visit_table_visit_type = $visit_type;
				$patient_table_visit_type = $visit_type_id;
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$rejected_amount = $row->amount_rejected;
				$parent_visit = $row->parent_visit;
				$invoice_number = $row->invoice_number;
				$patient_date_of_birth = $row->patient_date_of_birth;
				if(empty($rejected_amount))
				{
					$rejected_amount = 0;
				}
				
				

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
				
				$count++;
				
				//payment data
				$charges = '';
				
				$payments_value = $this->accounts_model->total_payments($visit_id);

				$invoice_total = $amount_payment = $this->accounts_model->get_visit_total_invoice($visit_id);

				// var_dump($parent_visit); die();
				$balance = $this->accounts_model->balance($payments_value,$invoice_total);



				$item_invoiced_rs = $this->accounts_model->get_patient_visit_charge_items($visit_id);
			
				$procedures = '';
				if(count($item_invoiced_rs) > 0)
				{
					foreach ($item_invoiced_rs as $key_items):
						$s++;
						$service_charge_name = $key_items->service_charge_name;
						$visit_charge_amount = $key_items->visit_charge_amount;
						$service_name = $key_items->service_name;
						$units = $key_items->visit_charge_units;
						$visit_total = $visit_charge_amount * $units;
						$personnel_id = $key_items->personnel_id;
						$procedures .= strtoupper($service_charge_name).',';
					endforeach;
				}

				//display the patient data
				$report[$row_count][$col_count] = $count;
				$col_count++;
				$report[$row_count][$col_count] = $visit_date;
				$col_count++;
				$report[$row_count][$col_count] = $patient_surname.' '.$patient_othernames;
				$col_count++;
				$report[$row_count][$col_count] = $visit_id;
				$col_count++;
				$report[$row_count][$col_count] = $procedures;
				$col_count++;
				$report[$row_count][$col_count] = $doctor;
				$col_count++;
				$report[$row_count][$col_count] = $invoice_total;
				$col_count++;
				$report[$row_count][$col_count] = $balance;
				$col_count++;
				
				
				
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

	public function update_debtor_account($visit_type_id)
	{
		$account = array(
			'visit_type'=>$visit_type_id,//$this->input->post('account_to_id'),
			'opening_balance'=>$this->input->post('opening_balance'),
			'debit_id'=>$this->input->post('debit_id'),
			'created'=>$this->input->post('start_date')
			);

		// check if it exists

		$this->db->where('visit_type',$visit_type_id);
		$query = $this->db->get('visit_type_account');

		if($query->num_rows() > 0)
		{
			// update
			$this->db->where('visit_type',$visit_type_id);
			if($this->db->update('visit_type_account',$account))
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
			if($this->db->insert('visit_type_account',$account))
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
}
?>