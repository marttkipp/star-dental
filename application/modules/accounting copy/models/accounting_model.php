<?php

class accounting_model extends CI_Model 
{

	/*
	*	Select all personnel
	*
	*/
	public function get_all_personnel()
	{
		$this->db->select('*');
		$query = $this->db->get('personnel');
		
		return $query;
	}

	public function get_all_visits_view($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('patients.*,v_patient_account_balances.*');
		$this->db->where($where);
		$this->db->order_by('patients.patient_id','DESC');
		// $this->db->group_by('patients.patient_id');
		// $this->db->join('v_patient_balances','v_patient_balances.patient_id = visit.visit_id','LEFT');
		// $this->db->join('personnel','visit.personnel_id = personnel.personnel_id','left');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	public function get_all_visits($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('patients.*,v_patient_visit_statement.*');
		$this->db->where($where);
		$this->db->order_by('patients.patient_id','DESC');
		// $this->db->group_by('patients.patient_id');
		// $this->db->join('v_patient_balances','v_patient_balances.patient_id = visit.visit_id','LEFT');
		// $this->db->join('personnel','visit.personnel_id = personnel.personnel_id','left');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	public function get_all_visits_old($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		// $this->db->select('visit.*, (visit.visit_time_out - visit.visit_time) AS waiting_time, patients.*, visit_type.visit_type_name,personnel.personnel_fname,personnel.personnel_onames,visit.rejected_amount AS amount_rejected');
		// $this->db->where($where);
		// $this->db->order_by('visit.invoice_number','DESC');
		// $this->db->group_by('visit.visit_id');

		// $this->db->join('personnel','visit.personnel_id = personnel.personnel_id','left');

		$this->db->select('v_transactions_by_date.*, patients.*, payment_method.*, personnel.personnel_fname, personnel.personnel_onames,visit_invoice.visit_invoice_number,branch.branch_name,branch.branch_code,visit.visit_id,visit_invoice.visit_invoice_id');
		$this->db->join('patients', 'patients.patient_id = v_transactions_by_date.patient_id', 'left');
		$this->db->join('payment_method', 'payment_method.payment_method_id = v_transactions_by_date.payment_method_id', 'left');
		$this->db->join('payments', 'payments.payment_id = v_transactions_by_date.transaction_id', 'left');
		$this->db->join('visit', 'visit.visit_id = visit_invoice.visit_id', 'left');
		$this->db->join('personnel', 'visit.personnel_id = personnel.personnel_id', 'left');
		$this->db->join('branch', 'branch.branch_id = v_transactions_by_date.branch_id', 'left');

		$this->db->where($where);
		$this->db->order_by('v_transactions_by_date.created_at','DESC');


		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	public function get_visit_amounts($visit_id)
	{
		$query = $this->db->query('SELECT  sum(vc.visit_charge_amount*vc.visit_charge_units) as total_invoice, sum(k.amount_paid) as amount_paid, sum(j.amount_paid) as waiver_amount 
								FROM 
								(`visit` AS v) 
								LEFT JOIN `visit_charge` AS vc ON `vc`.`visit_id` = `v`.`visit_id` AND vc.visit_charge_delete = 0 AND vc.charged = 1 
								LEFT JOIN `payments` AS k ON `k`.`visit_id` = `v`.`visit_id` AND k.cancel = 0 AND k.payment_type = 1 
								LEFT JOIN `payments` AS j ON `j`.`visit_id` = `v`.`visit_id` AND j.cancel = 0 AND j.payment_type = 2 
								WHERE  v.visit_id = '.$visit_id.'
								GROUP BY `v`.`visit_id` 
								ORDER BY `v`.`visit_date` DESC ');
		$total_invoice = 0;
		$amount_paid = 0;
		$waiver_amount = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_invoice = $value->total_invoice;
				$amount_paid = $value->amount_paid;
				$waiver_amount = $value->waiver_amount;

			}
		}
		if(empty($total_invoice))
		{
			$total_invoice = 0;
		}
		if(empty($amount_paid))
		{
			$amount_paid  = 0;
		}
		if(empty($waiver_amount))
		{
			$waiver_amount = 0;
		}

		$response['total_invoice'] = $total_invoice;
		$response['amount_paid'] = $amount_paid;
		$response['waiver_amount'] = $waiver_amount;

		return $response;
	}

	public function get_visit_totals($visit_id)
	{


		// calculate all payments
		$table = "payments";
		$where = "payments.payment_type = 1 AND cancel = 0 AND payments.visit_id =". $visit_id;
		$items = "SUM(payments.amount_paid) AS amount_paid";
		$order = "payments.payment_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$amount_paid = 0;
		if(count($result) > 0)
		{
			foreach ($result as $key):
				# code...
				$amount_paid = $key->amount_paid;
				
			endforeach;
		}
		else
		{
			$amount_paid = 0;
		}



		// calculate all invoices

		$table = "visit_charge";
		$where = "visit_charge_delete = 0 AND visit_id =". $visit_id;
		$items = "SUM(visit_charge_amount*visit_charge_units) AS amount_invoiced";
		$order = "visit_charge.visit_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$total_invoice = 0;
		if(count($result) > 0)
		{
			foreach ($result as $key):
				# code...
				$total_invoice = $key->amount_invoiced;
				
			endforeach;
		}
		else
		{
			$total_invoice = 0;
		}


		// var_dump($total_invoice); die();
		// calculate all waivers


		$table = "payments";
		$where = "payments.payment_type = 2 AND cancel = 0 AND payments.visit_id =". $visit_id;
		$items = "SUM(payments.amount_paid) AS amount_paid";
		$order = "payments.payment_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$waiver_amount = 0;
		if(count($result) > 0)
		{
			foreach ($result as $key):
				# code...
				$waiver_amount = $key->amount_paid;
				
			endforeach;
		}
		else
		{
			$waiver_amount = 0;
		}
		
		if(empty($total_invoice))
		{
			$total_invoice = 0;
		}
		if(empty($amount_paid))
		{
			$amount_paid  = 0;
		}
		if(empty($waiver_amount))
		{
			$waiver_amount = 0;
		}

		$response['total_invoice'] = $total_invoice;
		$response['amount_paid'] = $amount_paid;
		$response['waiver_amount'] = $waiver_amount;

		return $response;
	}
	public function get_visit_invoice_totals()
	{
		$visit_invoices = $this->session->userdata('visit_invoices');
		$visit_type_id = $this->session->userdata('visit_type_id');
		$patient_number = $this->session->userdata('patient_number');
		$add ='';
		$table_add = '';

		$debtor_query = $this->session->userdata('debtors_search_query');

		// var_dump($visit_type_id); die();
		if(!empty($debtor_query))
		{
			$add .= $debtor_query;
		}
		else
		{
			$add .= ' AND v_transactions_by_date.transaction_date = \''.date('Y-m-d').'\'';
		}

		$branch_session = $this->session->userdata('branch_id');

		if($branch_session > 0)
		{
			$add .= ' AND v_transactions_by_date.branch_id = '.$branch_session;
		
		}
		// if(!empty($visit_type_id))
		// {
		// 	$add .= $visit_type_id;
		// }
		// if(!empty($patient_number))
		// {
		// 	$add .= $patient_number.' AND patients.patient_id = visit.patient_id';
		// 	$table_add .=',patients';
		// }
		
		$this->db->where('v_transactions_by_date.transactionCategory = "Revenue"'.$add);
		$this->db->select('SUM(dr_amount) AS total_invoice');
		$query = $this->db->get('v_transactions_by_date');
		$total_invoice = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_invoice = $value->total_invoice;
			}
		}
		if(empty($total_invoice))
		{
			$total_invoice = 0;
		}
		return $total_invoice;
	}

	public function get_visit_invoice_children_totals()
	{
		$visit_invoices = $this->session->userdata('visit_invoices');
		$visit_type_id = $this->session->userdata('visit_type_id');
		$patient_number = $this->session->userdata('patient_number');
		$add ='';
		$table_add = '';

		// var_dump($visit_type_id); die();
		if(!empty($visit_invoices))
		{
			$add .= $visit_invoices;
		}
		if(!empty($visit_type_id))
		{
			$add .= $visit_type_id;
		}
		if(!empty($patient_number))
		{
			$add .= $patient_number.' AND patients.patient_id = visit.patient_id';
			$table_add .=',patients';
		}
		
		$this->db->where('visit.parent_visit > 0 AND visit.visit_id = visit_bill.visit_id AND visit.visit_delete = 0 '.$add);
		$this->db->select('SUM(visit_bill_amount) AS total_invoice');
		$query = $this->db->get('visit_bill,visit'.$table_add);
		$total_invoice = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_invoice = $value->total_invoice;
			}
		}
		return $total_invoice;
	}



	public function get_rejected_amounts()
	{
		$visit_invoices = $this->session->userdata('visit_invoices');
		$visit_type_id = $this->session->userdata('visit_type_id');
		$patient_number = $this->session->userdata('patient_number');
		$add ='';
		$table_add = '';
		if(!empty($visit_invoices))
		{
			$add .= $visit_invoices;
		}
		if(!empty($visit_type_id))
		{
			$add .= $visit_type_id;
		}
		if(!empty($patient_number))
		{
			$add .= $patient_number.' AND patients.patient_id = visit.patient_id';
			$table_add .=',patients';
		}
		
		$this->db->where('visit.visit_delete = 0  '.$add);
		$this->db->select('SUM(visit.rejected_amount) AS total_invoice');
		$query = $this->db->get('visit'.$table_add);
		$total_invoice = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_invoice = $value->total_invoice;
			}
		}
		return $total_invoice;
	}
	public function get_rejected_amounts_value()
	{
		$visit_invoices = $this->session->userdata('visit_invoices');
		$visit_type_id = $this->session->userdata('visit_type_id');
		$patient_number = $this->session->userdata('patient_number');
		$add ='';
		$table_add = '';
		if(!empty($visit_invoices))
		{
			$add .= $visit_invoices;
		}
		// if(!empty($visit_type_id))
		// {
		// 	$add .= $visit_type_id;
		// }
		if(!empty($patient_number))
		{
			$add .= $patient_number.' AND patients.patient_id = visit.patient_id';
			$table_add .=',patients';
		}

		$visit_type = $this->session->userdata('visit_type');
		if($visit_type == 1 AND !empty($visit_type))
		{
			$add .= 'AND visit.visit_type  = 1';
		}
		else if($visit_type != 1 AND !empty($visit_type))
		{
			$add .= ' AND visit.visit_type <> '.$visit_type;
		}
		else
		{
			$add .= '';
		}
		
		$this->db->where('visit.visit_delete = 0 AND visit.visit_id = visit_bill.visit_id  '.$add);
		$this->db->select('SUM(visit_bill.visit_bill_amount) AS total_invoice');
		$query = $this->db->get('visit,visit_bill'.$table_add);
		$total_invoice = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_invoice = $value->total_invoice;
			}
		}
		return $total_invoice;
	}

	public function get_patients_visits($patient_visit)
	{
		$visit_invoices = $this->session->userdata('visit_invoices');
		$visit_type_id = $this->session->userdata('visit_type_id');
		$patient_number = $this->session->userdata('patient_number');
		$add ='';
		$table_add = '';

		

		$debtor_query = $this->session->userdata('debtors_search_query');

		// var_dump($visit_type_id); die();
		if(!empty($debtor_query))
		{
			$debtor_query = str_replace('v_transactions_by_date.transaction_date','visit.visit_date', $debtor_query);
			$debtor_query = str_replace('v_transactions_by_date.branch_id','visit.branch_id', $debtor_query);
			$debtor_query = str_replace('v_transactions_by_date.payment_type','visit.visit_type', $debtor_query);
			// var_dump($debtor_query);die();
			$add .= $debtor_query;

			$branch_session = $this->session->userdata('branch_id');

			if($branch_session > 0)
			{
				$add .= ' AND visit.branch_id = '.$branch_session;
			
			}
		}
		else
		{
			$add .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';

			$branch_session = $this->session->userdata('branch_id');

			if($branch_session > 0)
			{
				$add .= ' AND visit.branch_id = '.$branch_session;
			
			}
		}

		

		// if(!empty($visit_invoices))
		// {
		// 	$visit_invoices = str_replace('v_transactions_by_date.transaction_date','visit.visit_date', $visit_invoices);
		// 	$add .= $visit_invoices;
		// }
		// else if(!empty($visit_type_id))
		// {
		// 	$add .= $visit_type_id;
		// }
		// else if(!empty($patient_number))
		// {
		// 	$add .= '';//$patient_number;
		// 	$table_add .='patients';
		// }
		// else
		// {
		// 	$add = '';
		// }
		if($patient_visit == 1)
		{
			$add .= ' AND patients.patient_id = visit.patient_id';
		}
		else if($patient_visit == 0)
		{
			$add .= ' AND patients.patient_id = visit.patient_id';
		}
		$this->db->where('visit.visit_delete = 0 AND visit.close_card <> 2 '.$add);
		$this->db->select('visit.patient_id');
		$query = $this->db->get('visit,patients');
		$total_new = 0;
		$total_old = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$patient_id = $value->patient_id;

				$this->db->where('visit.visit_delete = 0 AND visit.close_card <> 2  AND visit.patient_id ='.$patient_id);
				$this->db->select('*');
				$query_numbers = $this->db->get('visit');
				if($query_numbers->num_rows() == 1)
				{
					$total_new +=1;
				}
				else
				{
					$total_old +=1;
				}
			}
		}
		$response['total_new'] = $total_new;
		$response['total_old'] = $total_old;

		return $response;
	}


	public function get_all_visit_invoices($visit_type)
	{
		$visit_invoices = $this->session->userdata('visit_invoices');
		$visit_type_id = $this->session->userdata('visit_type_id');
		$patient_number = $this->session->userdata('patient_number');
		$add ='';
		$table_add = '';
		if(!empty($visit_invoices))
		{
			$add .= $visit_invoices;
		}
		if(!empty($visit_type_id))
		{
			$add .= $visit_type_id;
		}
		if(!empty($patient_number))
		{
			$add .= $patient_number.' AND patients.patient_id = visit.patient_id';
			$table_add .=',patients';
		}
		

		// if($visit_type == 1 AND empty($visit_type_id))
		// {
		// 	$add .= ' AND visit.visit_type = 1'; 
		// }

		// else if($visit_type == 0 AND empty($visit_type_id))
		// {
		// 	$add .= ' AND visit.visit_type <> 1';
		// }
		// else
		// {
		// 	$add .= '';
		// }
		$this->db->where('charged = 1 AND visit.visit_id = visit_charge.visit_id AND visit.visit_delete = 0 AND visit_charge.visit_charge_delete = 0 AND visit.parent_visit IS NULL'.$add);
		$this->db->select('SUM(visit_charge_amount*visit_charge_units) AS total_invoice');
		$this->db->group_by('visit.invoice_number');
		$query = $this->db->get('visit_charge,visit'.$table_add);
		$total_invoice = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_invoice = $value->total_invoice;
			}
		}

		// var_dump($query->num_rows());
		return $total_invoice;
	}
	public function get_visit_payment_totals()
	{
		// $visit_payments = $this->session->userdata('visit_payments');
		$visit_invoices = $this->session->userdata('visit_invoices');
		
		$visit_type_id = $this->session->userdata('visit_type_id');
		$visit_type = $this->session->userdata('visit_type');
		$patient_number = $this->session->userdata('patient_number');
		$add ='';
		$table_add = '';

		$debtor_query = $this->session->userdata('debtors_search_query');

		// var_dump($visit_type_id); die();
		if(!empty($debtor_query))
		{
			$add .= $debtor_query;
		}
		else
		{
			$add .= ' AND v_transactions_by_date.transaction_date = \''.date('Y-m-d').'\'';
		}

		$branch_session = $this->session->userdata('branch_id');

		if($branch_session > 0)
		{
			$add .= ' AND v_transactions_by_date.branch_id = '.$branch_session;
		
		}
		// if(!empty($visit_invoices))
		// {
		// 	$add .= $visit_invoices;
		// }
		// if(!empty($visit_type_id))
		// {
		// 	$add .= $visit_type_id;
		// }
		// if(!empty($patient_number))
		// {
		// 	$add .= $patient_number.' AND patients.patient_id = visit.patient_id';
		// 	$table_add .= ',patients';
		// }
		$visit_type = $this->session->userdata('visit_type');
		// if($visit_type == 1 AND !empty($visit_type))
		// {
		// 	$add .= 'AND payments.payment_method_id < 9';
		// }
		// else if($visit_type != 1 AND !empty($visit_type))
		// {
		// 	$add .= ' AND payments.payment_method_id = 9';
		// }
		// else
		// {
		// 	$add .= '';
		// }
		$this->db->where('v_transactions_by_date.transactionCategory = "Revenue Payment" AND v_transactions_by_date.reference_id > 0 AND v_transactions_by_date.reference_id  IN (SELECT v_transactions_by_date.transaction_id FROM v_transactions_by_date WHERE  v_transactions_by_date.transactionCategory = "Revenue" '.$add.' )');
		$this->db->select('SUM(cr_amount) AS total_payments');
		$query = $this->db->get('v_transactions_by_date');
		$total_payments = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_payments = $value->total_payments;
			}
		}
		return $total_payments;
	}


	public function get_all_visit_payments_totals($visit_type,$type=null)
	{
		// $visit_payments = $this->session->userdata('visit_payments');
		$visit_invoices = $this->session->userdata('visit_invoices');
		$visit_type_id = $this->session->userdata('visit_type_id');
		$patient_number = $this->session->userdata('patient_number');
		$add ='';
		$table_add = '';
		if(!empty($visit_invoices))
		{
			$add .= $visit_invoices;
		}
		if(!empty($visit_type_id))
		{
			$add .= $visit_type_id;
		}
		if(!empty($patient_number))
		{
			$add .= $patient_number.' AND patients.patient_id = visit.patient_id';
			$table_add .= ',patients';
		}
		
		// $visit_type = $this->session->userdata('visit_type');
		if($visit_type == 1 AND !empty($visit_type))
		{
			$add .= 'AND payments.payment_method_id < 9';
		}
		else if($visit_type != 1 AND !empty($visit_type))
		{
			$add .= ' AND payments.payment_method_id = 9';
		}
		else
		{
			$add .= '';
		}

		if($type == 1)
		{
			$add .= ' AND visit.visit_date <> payments.payment_created ';
			// if($visit_type == 2)
			// {
			// 	var_dump($add); die();
			// }
		}

		$this->db->where('cancel = 0 AND visit.visit_id = payments.visit_id AND visit.visit_delete = 0 AND payments.payment_type = 1 '.$add);
		$this->db->select('SUM(amount_paid) AS total_payments');
		$query = $this->db->get('payments,visit'.$table_add);
		$total_payments = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_payments = $value->total_payments;
			}
		}
		return $total_payments;
	}





	public function all_payments_period()
	{
		$visit_payments = $this->session->userdata('visit_payments');
		$visit_type_id = $this->session->userdata('visit_type_id');
		$patient_number = $this->session->userdata('patient_number');
		$add ='';
		$table_add = '';
		// if(!empty($visit_payments))
		// {
		// 	$add .= $visit_payments;
		// }
		// if(!empty($visit_type_id))
		// {
		// 	$add .= $visit_type_id;
		// }

		$debtor_query = $this->session->userdata('debtors_search_query');

		// var_dump($visit_type_id); die();
		if(!empty($debtor_query))
		{
			$add .= $debtor_query;
		}
		else
		{
			$add .= ' AND v_transactions_by_date.transaction_date = \''.date('Y-m-d').'\'';
		}

		$branch_session = $this->session->userdata('branch_id');

		if($branch_session > 0)
		{
			$add .= ' AND v_transactions_by_date.branch_id = '.$branch_session;
		
		}
		// if(!empty($patient_number))
		// {
		// 	$add .= $patient_number.' AND patients.patient_id = visit.patient_id';
		// 	$table_add .= ',patients';
		// }
		
		$this->db->where('v_transactions_by_date.transactionCategory = "Revenue Payment"'.$add);
		$this->db->select('SUM(cr_amount) AS total_payments');
		$query = $this->db->get('v_transactions_by_date');
		$total_payments = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_payments = $value->total_payments;
			}
		}
		if(empty($total_payments))
		{
			$total_payments = 0;
		}
		return $total_payments;
	}

	public function get_visit_waiver_totals()
	{
		$visit_invoices = $this->session->userdata('visit_invoices');
		$visit_type_id = $this->session->userdata('visit_type_id');
		$patient_number = $this->session->userdata('patient_number');
		$add ='';
		$table_add = '';
		if(!empty($visit_invoices))
		{
			$add .= $visit_invoices;
		}
		if(!empty($visit_type_id))
		{
			$add .= $visit_type_id;
		}
		if(!empty($patient_number))
		{
			$add .= $patient_number.' AND patients.patient_id = visit.patient_id';
			$table_add .= ',patients';
		}
		
		$this->db->where('cancel = 0 AND visit.visit_id = payments.visit_id AND visit.visit_delete = 0 AND payments.payment_type = 2 '.$add);
		$this->db->select('SUM(amount_paid) AS total_payments');
		$query = $this->db->get('payments,visit'.$table_add);
		$total_payments = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_payments = $value->total_payments;
			}
		}
		return $total_payments;
	}


	public function get_visit_debits_totals()
	{
		$visit_invoices = $this->session->userdata('visit_invoices');
		$visit_type_id = $this->session->userdata('visit_type_id');
		$patient_number = $this->session->userdata('patient_number');
		$add ='';
		$table_add = '';
		if(!empty($visit_invoices))
		{
			$add .= $visit_invoices;
		}
		if(!empty($visit_type_id))
		{
			$add .= $visit_type_id;
		}
		if(!empty($patient_number))
		{
			$add .= $patient_number.' AND patients.patient_id = visit.patient_id';
			$table_add .= ',patients';
		}
		
		$this->db->where('cancel = 0 AND visit.visit_id = payments.visit_id AND visit.visit_delete = 0 AND payments.payment_type = 3 '.$add);
		$this->db->select('SUM(amount_paid) AS total_payments');
		$query = $this->db->get('payments,visit'.$table_add);
		$total_payments = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_payments = $value->total_payments;
			}
		}
		return $total_payments;
	}


	public function get_all_visit_waiver($visit_type)
	{
		$visit_invoices = $this->session->userdata('visit_invoices');
		$visit_type_id = $this->session->userdata('visit_type_id');
		$patient_number = $this->session->userdata('patient_number');
		$add ='';
		$table_add = '';
		if(!empty($visit_invoices))
		{
			$add .= $visit_invoices;
		}
		if(!empty($visit_type_id))
		{
			$add .= $visit_type_id;
		}
		if(!empty($patient_number))
		{
			$add .= $patient_number.' AND patients.patient_id = visit.patient_id';
			$table_add .= ',patients';
		}
		


		$this->db->where('cancel = 0 AND visit.visit_id = payments.visit_id AND visit.visit_delete = 0 AND payments.payment_type = 2 '.$add);
		$this->db->select('SUM(amount_paid) AS total_payments');
		$query = $this->db->get('payments,visit'.$table_add);
		$total_payments = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_payments = $value->total_payments;
			}
		}
		return $total_payments;
	}

	public function get_amount_collected($payment_method_id)
	{
		$visit_invoices = $this->session->userdata('visit_invoices');
		$visit_type_id = $this->session->userdata('visit_type_id');
		$patient_number = $this->session->userdata('patient_number');
		$add ='';
		$table_add = '';

		$debtor_query = $this->session->userdata('debtors_search_query');

		// var_dump($visit_type_id); die();
		if(!empty($debtor_query))
		{	

			
			// var_dump($debtor_query);die();
			$add .= $debtor_query;
		}
		// if(!empty($visit_invoices))
		// {
		// 	$add .= $visit_invoices;
		// }
		// if(!empty($visit_type_id))
		// {
		// 	$add .= $visit_type_id;
		// }
		// if(!empty($patient_number))
		// {
		// 	$add .= $patient_number.' AND patients.patient_id = visit.patient_id';
		// 	$table_add .= ',patients';
		// }
		
		$this->db->where('payment_method_id = '.$payment_method_id.' AND v_transactions_by_date.reference_id > 0 '.$add);
		$this->db->select('SUM(cr_amount) AS total_payments');
		$query = $this->db->get('v_transactions_by_date');
		$total_payments = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_payments = $value->total_payments;
			}
		}

		if(empty($total_payments))
		{
			$total_payments = 0;
		}
		return $total_payments;
	}

	public function get_payment_methods()
	{
		$this->db->select('*');
		$query = $this->db->get('payment_method');
		
		return $query;
	}
	/*
	*	Export Transactions
	*
	*/
	function export_debtors()
	{
		$this->load->library('excel');
		
		
		$where = 'v_transactions_by_date.transactionCategory = "Revenue" AND visit_invoice.visit_invoice_id = v_transactions_by_date.transaction_id';
		
		$table = 'v_transactions_by_date,visit_invoice';
		$visit_search = $this->session->userdata('debtors_search_query');
		// var_dump($visit_search);die();
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			
			
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'" ';
			$where .= ' AND v_transactions_by_date.transaction_date = "'.date('Y-m-d').'" ';
		}
		

		$branch_session = $this->session->userdata('branch_id');

		if($branch_session > 0)
		{
			$where .= ' AND v_transactions_by_date.branch_id = '.$branch_session;
			// $where .= $visit_search;
		
		}
		
		$this->db->select('v_transactions_by_date.*, patients.*, payment_method.*, personnel.personnel_fname, personnel.personnel_onames,visit_invoice.visit_invoice_number,branch.branch_name,branch.branch_code,visit.visit_id,visit_invoice.visit_invoice_id');
		$this->db->join('patients', 'patients.patient_id = v_transactions_by_date.patient_id', 'left');
		$this->db->join('payment_method', 'payment_method.payment_method_id = v_transactions_by_date.payment_method_id', 'left');
		$this->db->join('payments', 'payments.payment_id = v_transactions_by_date.transaction_id', 'left');
		$this->db->join('visit', 'visit.visit_id = visit_invoice.visit_id', 'left');
		$this->db->join('personnel', 'visit.personnel_id = personnel.personnel_id', 'left');
		$this->db->join('branch', 'branch.branch_id = v_transactions_by_date.branch_id', 'left');

		$this->db->where($where);

		$visits_query = $this->db->get($table);
		
		$title = 'Transactions Export '.date('jS M Y H:i a',strtotime(date('Y-m-d H:i:s')));
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
			$report[$row_count][$col_count] = 'Invoice Date';
			$col_count++;
			$report[$row_count][$col_count] = 'Patient No.';
			$col_count++;
			$report[$row_count][$col_count] = 'Patient';
			$col_count++;
			$report[$row_count][$col_count] = 'Category';
			$col_count++;
			$report[$row_count][$col_count] = 'Doctor';
			$col_count++;
			$report[$row_count][$col_count] = 'Invoice No.';
			$col_count++;
			$report[$row_count][$col_count] = 'Branch Code';
			$col_count++;
			$report[$row_count][$col_count] = 'Invoice Amount';
			$col_count++;
			$report[$row_count][$col_count] = 'Payments';
			$col_count++;
			$report[$row_count][$col_count] = 'Balance';
			$col_count++;	
			//display all patient data in the leftmost columns
			$total_waiver = 0;
			$total_payments = 0;
			$total_invoice = 0;
			$total_balance = 0;
			$total_rejected_amount = 0;
			$total_cash_balance = 0;
			$total_insurance_payments =0;
			$total_insurance_invoice =0;
			$total_payable_by_patient = 0;
			$total_payable_by_insurance = 0;
			$total_debit_notes = 0;
			$total_credit_notes= 0;
			foreach ($visits_query->result() as $row)
			{
				$row_count++;
				$total_invoiced = 0;
				$visit_date = date('jS M Y',strtotime($row->transaction_date));
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
				$visit_type_id = $row->visit_type;
				$patient_number = $row->patient_number;
				$visit_type = $row->visit_type;
				$visit_table_visit_type = $visit_type;
				$patient_table_visit_type = $visit_type_id;
				$rejected_amount = $row->amount_rejected;
				$visit_invoice_number = $row->visit_invoice_number;
				$visit_invoice_id = $row->visit_invoice_id;
				$parent_visit = $row->parent_visit;
				$branch_code = $row->branch_code;

				if(empty($rejected_amount))
				{
					$rejected_amount = 0;
				}
				// $coming_from = $this->reception_model->coming_from($visit_id);
				// $sent_to = $this->reception_model->going_to($visit_id);
				$visit_type_name = $row->payment_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;

				$doctor = $row->personnel_fname;
				$count++;
				$invoice_total = $row->dr_amount;
				$payments_value = $this->accounts_model->get_visit_invoice_payments($visit_invoice_id);
				$balance  = $this->accounts_model->balance($payments_value,$invoice_total);

				$total_payable_by_patient += $invoice_total;
				$total_payments += $payments_value;
				$total_balance += $balance;


				$report[$row_count][$col_count] = $count;
				$col_count++;
				$report[$row_count][$col_count] = $visit_date;
				$col_count++;
				$report[$row_count][$col_count] = $patient_number;
				$col_count++;
				$report[$row_count][$col_count] = ucwords(strtolower($patient_surname));
				$col_count++;
				$report[$row_count][$col_count] = $visit_type_name;
				$col_count++;
				$report[$row_count][$col_count] = $doctor;
				$col_count++;
				$report[$row_count][$col_count] = $visit_invoice_number;
				$col_count++;
				$report[$row_count][$col_count] = $branch_code;
				$col_count++;
				$report[$row_count][$col_count] = number_format($invoice_total,2);
				$col_count++;
				$report[$row_count][$col_count] = (number_format($payments_value,2));
				$col_count++;
				$report[$row_count][$col_count] = (number_format($balance,2));
				$col_count++;				
				
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}


		function export_debtors_old()
	{
		$this->load->library('excel');
		
		
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 ';
		$table = 'visit, patients, visit_type';
		$visit_search = $this->session->userdata('debtors_search_query');
		// var_dump($visit_search);die();
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			
			
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
		
		$title = 'Transactions Export '.date('jS M Y H:i a',strtotime(date('Y-m-d H:i:s')));
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
			$report[$row_count][$col_count] = 'Patient number';
			$col_count++;
			$report[$row_count][$col_count] = 'Category';
			$col_count++;
			$report[$row_count][$col_count] = 'Doctor';
			$col_count++;
			$current_column = $col_count ;
			
			
			//get & display all services
			$services_query = $this->reports_model->get_all_active_services();
			
			foreach($services_query->result() as $service)
			{
				$report[$row_count][$current_column] = $service->service_name;
				$current_column++;
			}
			/*$report[$row_count][$current_column] = 'Debit Note Total';
			$current_column++;
			$report[$row_count][$current_column] = 'Credit Note Total';
			$current_column++;*/
			$report[$row_count][$current_column] = 'Insurance Invoice';
			$current_column++;
			$report[$row_count][$current_column] = 'Cash Invoice';
			$current_column++;
			$report[$row_count][$current_column] = 'Cash Balance';
			$current_column++;			
			$report[$row_count][$current_column] = 'Waiver';
			$current_column++;
			$report[$row_count][$current_column] = 'Invoice Total';
			$current_column++;
			
			//get & display all services
			$payment_method_query = $this->reports_model->get_all_active_payment_method();
			
			foreach($payment_method_query->result() as $paymentmethod)
			{
				$report[$row_count][$current_column] = $paymentmethod->payment_method;
				$current_column++;
			}

			$report[$row_count][$current_column] = 'Payments Total';
			$current_column++;
			$report[$row_count][$current_column] = 'Balance';
			$current_column++;
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
				$patient_date_of_birth = $row->patient_date_of_birth;
				if(empty($rejected_amount))
				{
					$rejected_amount = 0;
				}
				// this is to check for any credit note or debit notes
				// $transactions = $this->accounting_model->get_visit_totals($visit_id);

				// $payments_value = $transactions['amount_paid'];
				// $invoice_total = $transactions['total_invoice'];
				// $waiver_amount = $transactions['waiver_amount'];
				// $balance = $invoice_total - ($payments_value + $waiver_amount);

				$payments_value = $this->accounts_model->total_payments($visit_id);

                $invoice_amount = $this->accounts_model->total_invoice($visit_id);

                $balance = $this->accounts_model->balance($payments_value,$invoice_total);

                $invoice_total = $invoice_amount - $payments_value ;

                $waiver_amount = $this->accounts_model->get_sum_debit_notes($visit_id);


                $cash_balance = 0;
                if(!empty($rejected_amount))
                {
                	$cash_balance = $rejected_amount - $payments_value;
                }
                $total_cash_balance +=$cash_balance;
                $invoice_total -= $cash_balance;
				$total_invoice += $invoice_amount;
				$total_waiver += $waiver_amount;
				$total_payments += $payments_value;
				$total_balance += $invoice_total;
				$total_rejected_amount += $rejected_amount;

				$doctor = $row->personnel_onames.' '.$row->personnel_fname;
			




				$doctor = $row->personnel_onames.' '.$row->personnel_fname;

				
				$count++;
				
				//display services charged to patient
				$total_invoiced2 = 0;
				foreach($services_query->result() as $service)
				{
					$service_id = $service->service_id;
					$visit_charge = $this->reports_model->get_all_visit_charges($visit_id, $service_id);
					$total_invoiced2 += $visit_charge;
				}
				
			
				//display the patient data
				$report[$row_count][$col_count] = $count;
				$col_count++;
				$report[$row_count][$col_count] = $visit_date;
				$col_count++;
				$report[$row_count][$col_count] = $patient_surname.' '.$patient_othernames;
				$col_count++;
				$report[$row_count][$col_count] = $patient_number;
				$col_count++;
				$report[$row_count][$col_count] = $visit_type_name;
				$col_count++;
				$report[$row_count][$col_count] = $doctor;
				$col_count++;
				$current_column = $col_count;

				//display services charged to patient
				foreach($services_query->result() as $service)
				{
					$service_id = $service->service_id;
					$visit_charge = $this->reports_model->get_all_visit_charges($visit_id, $service_id);
					$total_invoiced += $visit_charge;
					
					//get debit notes for that service
					$service_debit_notes = $this->reports_model->get_service_notes($visit_id, $service_id, 2);
					
					//get debit notes for that service
					$service_credit_notes = $this->reports_model->get_service_notes($visit_id, $service_id, 3);
					
					$notes_difference = $service_debit_notes - $service_credit_notes;
					
					$report[$row_count][$current_column] = (intval($visit_charge) + intval($notes_difference));
					
					$current_column++;
				}
				/*$report[$row_count][$current_column] = $debit_note_amount;
				$current_column++;
				$report[$row_count][$current_column] = $credit_note_amount;
				$current_column++;*/
				$report[$row_count][$current_column] = $invoice_amount;
				$current_column++;
				$report[$row_count][$current_column] = $rejected_amount;
				$current_column++;

				$report[$row_count][$current_column] = $cash_balance;
				$current_column++;
				
				$report[$row_count][$current_column] = $waiver_amount;
				$current_column++;
				$report[$row_count][$current_column] = $invoice_amount;
				$current_column++;
				foreach($payment_method_query->result() as $paymentmethod)
				{
					$payment_method_id = $paymentmethod->payment_method_id;
					$amount_paid = $this->reports_model->get_all_payment_values($visit_id, $payment_method_id);
					$report[$row_count][$current_column] = $amount_paid;
					$current_column++;
				}
			
				//display total for the current visit
				
				$report[$row_count][$current_column] = $payments_value;
				$current_column++;

				$report[$row_count][$current_column] = $invoice_total;
				$current_column++;
				
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}



	function export_deleted_invoices()
	{
		$this->load->library('excel');
		
		
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 1 ';
		$table = 'visit, patients, visit_type';
		$visit_search = $this->session->userdata('visit_deleted_search_query');
		// var_dump($visit_search);die();
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			
			
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
		
		$page_title = $this->session->userdata('search_visit_deleted_title');
		if(empty($page_title))
		{
			$page_title = 'Deleted Invoices ';
		}

		$title = $page_title;
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
			$report[$row_count][$col_count] = 'Patient number';
			$col_count++;
			$report[$row_count][$col_count] = 'Category';
			$col_count++;
			$report[$row_count][$col_count] = 'Doctor';
			$col_count++;
			$report[$row_count][$col_count] = 'Insurance Invoice';
			$col_count++;
			$report[$row_count][$col_count] = 'Cash Invoice';
			$col_count++;
			$report[$row_count][$col_count] = 'Cash Balance';
			$col_count++;			
			$report[$row_count][$col_count] = 'Waiver';
			$col_count++;
			$report[$row_count][$col_count] = 'Invoice Total';
			$col_count++;			
			$report[$row_count][$col_count] = 'Payments Total';
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
				$patient_date_of_birth = $row->patient_date_of_birth;
				if(empty($rejected_amount))
				{
					$rejected_amount = 0;
				}
				// this is to check for any credit note or debit notes
				// $transactions = $this->accounting_model->get_visit_totals($visit_id);

				// $payments_value = $transactions['amount_paid'];
				// $invoice_total = $transactions['total_invoice'];
				// $waiver_amount = $transactions['waiver_amount'];
				// $balance = $invoice_total - ($payments_value + $waiver_amount);

				$payments_value = $this->accounts_model->total_payments($visit_id);

                $invoice_amount = $this->accounts_model->total_invoice($visit_id);

                $balance = $this->accounts_model->balance($payments_value,$invoice_total);

                $invoice_total = $invoice_amount - $payments_value ;

                $waiver_amount = $this->accounts_model->get_sum_debit_notes($visit_id);


                $cash_balance = 0;
                if(!empty($rejected_amount))
                {
                	$cash_balance = $rejected_amount - $payments_value;
                }
                $total_cash_balance +=$cash_balance;
                $invoice_total -= $cash_balance;
				$total_invoice += $invoice_amount;
				$total_waiver += $waiver_amount;
				$total_payments += $payments_value;
				$total_balance += $invoice_total;
				$total_rejected_amount += $rejected_amount;

				$doctor = $row->personnel_onames.' '.$row->personnel_fname;
			




				$doctor = $row->personnel_onames.' '.$row->personnel_fname;

				
				$count++;
				
				//display services charged to patient
				$total_invoiced2 = 0;
			
			
				//display the patient data
				$report[$row_count][$col_count] = $count;
				$col_count++;
				$report[$row_count][$col_count] = $visit_date;
				$col_count++;
				$report[$row_count][$col_count] = $patient_surname.' '.$patient_othernames;
				$col_count++;
				$report[$row_count][$col_count] = $patient_number;
				$col_count++;
				$report[$row_count][$col_count] = $visit_type_name;
				$col_count++;
				$report[$row_count][$col_count] = $doctor;
				$col_count++;
				$report[$row_count][$col_count] = $invoice_amount;
				$col_count++;
				$report[$row_count][$col_count] = $rejected_amount;
				$col_count++;

				$report[$row_count][$col_count] = $cash_balance;
				$col_count++;
				
				$report[$row_count][$col_count] = $waiver_amount;
				$col_count++;
				$report[$row_count][$col_count] = $invoice_amount;
				$col_count++;
				
				//display total for the current visit
				
				$report[$row_count][$col_count] = $payments_value;
				$col_count++;

				$report[$row_count][$col_count] = $invoice_total;
				$col_count++;
				
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	public function get_transactions_by_categories()
	{
		$search_items = $this->session->userdata('accounts_cheques_search');

		if(!empty($search_items))
		{
			$add_where = $search_items;
		}
		else
		{
			$add_where = ' AND MONTH(payment_date) = "'.date('m').'" AND YEAR(payment_date) = '.date('Y').' ';
		}
		$this->db->select('SUM(amount_paid) AS total_amount,account_to_type');
		$this->db->where('account_payment_deleted = 0 '.$add_where);
		$this->db->group_by('account_to_type');
		$query = $this->db->get('account_payments');

		return $query;

	}



	public function get_general_report_data($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('v_transactions_by_date.*, patients.*,branch.branch_name,branch.branch_code');
		// $this->db->join('patients', 'patients.patient_id = v_transactions_by_date.patient_id', 'left');
		$this->db->join('branch', 'branch.branch_id = v_transactions_by_date.branch_id', 'left');

		$this->db->where($where);
		$this->db->order_by('v_transactions_by_date.created_at','ASC');


		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	public function export_general_report()
	{

		$this->load->library('excel');
		
		
		$branch_session = $this->session->userdata('branch_id');
		if($branch_session == 0)
		{
			$branch = '';
		}
		else
		{
			$branch = ' AND v_transactions_by_date.branch_id = '.$branch_session;
		}

		$where = '(v_transactions_by_date.transactionCategory = "Revenue" OR v_transactions_by_date.transactionCategory = "Revenue Payment")'.$branch;
		
		$table = 'v_transactions_by_date';


		$visit_search = $this->session->userdata('general_report_search');
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			
			
		}
		else
		{
			$where .= ' AND v_transactions_by_date.transaction_date = "'.date('Y-m-d').'" ';

		}

		$this->db->select('v_transactions_by_date.*, patients.*,branch.branch_name,branch.branch_code');
		$this->db->join('patients', 'patients.patient_id = v_transactions_by_date.patient_id', 'left');
		$this->db->join('branch', 'branch.branch_id = v_transactions_by_date.branch_id', 'left');
		$this->db->where($where);
		$this->db->order_by('v_transactions_by_date.created_at','ASC');

		$visits_query = $this->db->get($table);
		
		$title = 'Transactions Export '.date('jS M Y H:i a',strtotime(date('Y-m-d H:i:s')));
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
			$report[$row_count][$col_count] = 'TYPE';
			$col_count++;
			$report[$row_count][$col_count] = 'DATE';
			$col_count++;
			$report[$row_count][$col_count] = 'INVOICE/RECEIPT NO.';
			$col_count++;
			$report[$row_count][$col_count] = 'PATIENTS\'s NAME';
			$col_count++;
			$report[$row_count][$col_count] = 'VISIT TYPE';
			$col_count++;
			$report[$row_count][$col_count] = 'PROCEDURES.';
			$col_count++;
			$report[$row_count][$col_count] = 'AMOUNT';
			$col_count++;
			$report[$row_count][$col_count] = 'PAID BY';
			$col_count++;
			$report[$row_count][$col_count] = 'TRANSACTION NO';
			$col_count++;
			$report[$row_count][$col_count] = 'BRANCH';
			$col_count++;	
			//display all patient data in the leftmost columns
			$total_waiver = 0;
			$total_payments = 0;
			$total_invoice = 0;
			$total_balance = 0;
			$total_rejected_amount = 0;
			$total_cash_balance = 0;
			$total_insurance_payments =0;
			$total_insurance_invoice =0;
			$total_payable_by_patient = 0;
			$total_payable_by_insurance = 0;
			$total_debit_notes = 0;
			$total_credit_notes= 0;
			foreach ($visits_query->result() as $row)
			{
				$row_count++;
				$count++;

				$parent_visit = $row->parent_visit;
				$branch_code = $row->branch_code;
				$visit_type_name = $row->payment_type_name;
				$transactionCategory = $row->transactionCategory;
				$transaction_date = $row->transaction_date;
				$dr_amount = $row->dr_amount;
				$cr_amount = $row->cr_amount;
				$payment_method_name = $row->payment_method_name;
				$payment_type_name = $row->payment_type_name;
				$patient_surname = $row->patient_surname;
				$reference_code = $row->reference_code;
				$transaction_code = $row->transactionCode;
				$payment_method_name = $row->payment_method_name;

				$branch_code = $row->branch_code;

				$visit_date = date('jS M Y',strtotime($row->transaction_date));

				$doctor = '';//$row->personnel_fname;
				$count++;
				// $invoice_total = $row->dr_amount;
				$payments_value = 0;//$this->accounts_model->get_visit_invoice_payments($visit_invoice_id);
				// $balance  = $this->accounts_model->balance($payments_value,$invoice_total);

				$total_payable_by_patient += $invoice_total;
				$total_payments += $payments_value;
				$total_balance += 0;

				if($transactionCategory == "Revenue")
				{
					$amount = $dr_amount;
					$transactionCategory = "Invoice";
				}
				else if($transactionCategory == "Revenue Payment")
				{
					$amount = $cr_amount;
					$transactionCategory = "Receipt";
				}


				$report[$row_count][$col_count] = $count;
				$col_count++;
				$report[$row_count][$col_count] = $transactionCategory;
				$col_count++;
				$report[$row_count][$col_count] = $visit_date;
				$col_count++;
				$report[$row_count][$col_count] = $reference_code;
				$col_count++;
				$report[$row_count][$col_count] = ucwords(strtolower($patient_surname));
				$col_count++;
				$report[$row_count][$col_count] = $visit_type_name;
				$col_count++;
				$report[$row_count][$col_count] = '-';
				$col_count++;
				$report[$row_count][$col_count] = number_format($amount,2);
				$col_count++;
				$report[$row_count][$col_count] = ucwords(strtolower($payment_method_name));
				$col_count++;
				$report[$row_count][$col_count] = ucwords(strtolower($transaction_code));
				$col_count++;
				$report[$row_count][$col_count] = ucwords(strtolower($branch_code));
				$col_count++;			
				
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

}
?>