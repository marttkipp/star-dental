<?php

class Company_financial_model extends CI_Model
{

	public function get_income_value_new($transactionCategory)
	{
		//retrieve all users

		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  '  (transaction_date >= \''.$date_from.'\' AND transaction_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = '  transaction_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = '  transaction_date = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}
		$select_statement = "
							SELECT
								data.department_name AS service_name,
								data.department_id AS department_id,
								SUM(dr_amount) AS dr_amount,
								SUM(cr_amount) AS cr_amount
								FROM (SELECT
									visit_charge.visit_charge_id AS transaction_id,
									visit.visit_id AS reference_id,
									visit.invoice_number AS reference_code,
									visit.patient_id AS patient_id,
									service.service_id AS parent_service,
									service.department_id AS department_id,
									visit_charge.service_charge_id AS child_service,
									visit.personnel_id AS personnel_id,
									visit.visit_type AS payment_type,
									'' AS payment_method_id,
									'' AS payment_method_name,
									service.service_name AS service_name,
									departments.department_name AS department_name,
									service_charge.service_charge_name AS transaction_name,
									CONCAT( 'Charged for ', service.service_name, ' : ', service_charge.service_charge_name ) AS transaction_description,
									(visit_charge.visit_charge_amount*visit_charge.visit_charge_units) AS dr_amount,
									'0' AS cr_amount,
									visit_charge.date AS transaction_date,
									visit_charge.visit_charge_timestamp AS created_at,
									visit_charge.charged AS `status`,
									'Patient' AS party,
									'Revenue' AS transactionCategory,
									'Invoice Patients' AS transactionClassification,
									'visit_charge' AS transactionTable,
									'visit' AS referenceTable
									FROM
										visit_charge,visit,service_charge,service,departments

									WHERE 
										visit.visit_delete = 0 
										AND visit_charge.charged = 1 
										AND visit_charge.visit_charge_delete = 0
										AND visit.visit_id = visit_charge.visit_id
										AND service_charge.service_charge_id = visit_charge.service_charge_id
										AND service.service_id = service_charge.service_id
										AND departments.department_id = service.department_id

									UNION ALL

									SELECT
										payments.payment_id AS transaction_id,
										visit.visit_id AS reference_id,
										visit.invoice_number AS reference_code,
										visit.patient_id AS patient_id,
										payments.payment_service_id AS parent_service,
										departments.department_id AS department_id,
										'' AS child_service,
										'' AS personnel_id,
										visit.visit_type AS payment_type,
										'' AS payment_method_id,
										'' AS payment_method_name,
										'' AS service_name,
										'' AS department_name,
										CONCAT( 'Credit Note', ' : ', visit.visit_id) AS transaction_name,
										CONCAT( 'Credit Note for ', visit.invoice_number ) AS transaction_description,
										'0' AS dr_amount,
										payments.amount_paid AS cr_amount,
										payments.payment_created AS transaction_date,
										DATE(payments.time) AS created_at,
										payments.payment_status AS `status`,
										'Patient' AS party,
										'Credit Notes' AS transactionCategory,
										'Credit Note Patients' AS transactionClassification,
										'payments' AS transactionTable,
										'visit' AS referenceTable
									FROM
										payments
									LEFT JOIN service ON payments.payment_service_id = service.service_id
									LEFT JOIN departments ON departments.department_id = service.department_id
									LEFT JOIN visit ON visit.visit_id = payments.visit_id
									LEFT JOIN visit_type ON visit.visit_type = visit_type.visit_type_id
										WHERE payments.cancel = 0 
										AND payments.payment_type = 3 

									UNION ALL

									SELECT
										payments.payment_id AS transaction_id,
										visit.visit_id AS reference_id,
										visit.invoice_number AS reference_code,
										visit.patient_id AS patient_id,
										payments.payment_service_id AS parent_service,
										departments.department_id AS department_id,
										'' AS child_service,
										'' AS personnel_id,
										visit.visit_type AS payment_type,
										'' AS payment_method_id,
										'' AS payment_method_name,
										'' AS service_name,
										'' AS department_name,
										CONCAT( 'Credit Note', ' : ', visit.visit_id) AS transaction_name,
										CONCAT( 'Credit Note for ', visit.invoice_number ) AS transaction_description,
										payments.amount_paid AS dr_amount,
										'0' AS cr_amount,
										payments.payment_created AS transaction_date,
										DATE(payments.time) AS created_at,
										payments.payment_status AS `status`,
										'Patient' AS party,
										'Credit Notes' AS transactionCategory,
										'Credit Note Patients' AS transactionClassification,
										'payments' AS transactionTable,
										'visit' AS referenceTable
									FROM
										payments
									LEFT JOIN service ON payments.payment_service_id = service.service_id
									LEFT JOIN departments ON departments.department_id = service.department_id
									LEFT JOIN visit ON visit.visit_id = payments.visit_id
									LEFT JOIN visit_type ON visit.visit_type = visit_type.visit_type_id
									WHERE payments.cancel = 0 AND payments.payment_type = 2 

									) AS data WHERE department_id > 0 AND ".$search_invoice_add." GROUP BY data.department_id";
		
		$query = $this->db->query($select_statement);

		$result = $query->row();

		$product_value  = array();
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$item['name'] = $value->service_name;
				$item['department_id'] = $value->department_id;
				$item['value'] = $value->dr_amount - $value->cr_amount;

				array_push($product_value, $item);
			}
		}

		// var_dump($product_value);
		return  $product_value;
	}

	public function get_income_value($transactionCategory)
	{
		//retrieve all users

		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transaction_date >= \''.$date_from.'\' AND transaction_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transaction_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transaction_date = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}

		$this->db->from('v_all_invoice_payments,service');
		$this->db->select('SUM(cr_amount) AS total_amount,service.service_name AS parent_service,service.service_id');
		$this->db->where('service.service_id = v_all_invoice_payments.parent_service  AND  transactionCategory = "'.$transactionCategory.'" '.$search_invoice_add);
		$this->db->group_by('parent_service');
		$query = $this->db->get();

		return $query;
	}

	public function get_operational_cost_value($transactionCategory)
	{

		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{

				$search_invoice_add =  ' AND (referenceDate >= \''.$date_from.'\' AND referenceDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND referenceDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND referenceDate = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}
		//retrieve all users
		$this->db->from('v_general_ledger');
		$this->db->select('SUM(dr_amount) AS total_amount,accountName,accountId');
		$this->db->where('accountId > 0 AND transactionCategory = "'.$transactionCategory.'" '.$search_invoice_add);
		$this->db->group_by('accountId');
		$query = $this->db->get();

		return $query;
	}


	public function get_cog_value($transactionCategory)
	{
		//retrieve all users
		$this->db->from('v_all_invoice_payments');
		$this->db->select('SUM(dr_amount) AS total_amount,accountName');
		$this->db->where('transactionCategory = "'.$transactionCategory.'" AND (patient_id IS NULL OR patient_id = 0)');
		$this->db->group_by('accountId');
		$query = $this->db->get();

		return $query;
	}


	public function get_payables_aging_report()
	{
		//retrieve all users
		$creditor_search =  $this->session->userdata('creditor_search');

		if(!empty($creditor_search))
		{
			$where = $creditor_search;
		}
		else
		{
			$where = 'recepientId > 0';
		}
		$this->db->from('v_aged_payables');
		$this->db->select('*');
		$this->db->where($where);
		// $this->db->group_by('accountId');
		$query = $this->db->get();

		return $query;
	}


		public function get_payables_aging_report_by_creditor($creditor_id)
		{
			//retrieve all users
			$this->db->from('v_aged_payables');
			$this->db->select('*');
			$this->db->where('recepientId ='.$creditor_id);
			// $this->db->group_by('accountId');
			$query = $this->db->get();

			return $query;
		}

	public function get_receivables_aging_report()
	{
		//retrieve all users
		$this->db->from('v_aged_receivables');
		$this->db->select('*');
		// $this->db->where('');
		// $this->db->group_by('accountId');
		$query = $this->db->get();

		return $query;
	}



	public function get_account_value()
	{

		$search_status = $this->session->userdata('balance_sheet_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add =  ' AND (transactionDate >= \''.date("Y-01-01").'\' AND transactionDate <= \''.date("Y-m-d").'\') ';

		}
		//retrieve all users

		$this->db->from('v_account_ledger_by_date,account');
		$this->db->select('(SUM(dr_amount) - SUM(cr_amount)) AS total_amount,accountName,account_id');
		$this->db->where('accountParentId = 2 AND v_account_ledger_by_date.accountId = account.account_id AND account.paying_account = 0 
			AND (
				v_account_ledger_by_date.transactionClassification = "Purchase Payment" 
				OR 
				v_account_ledger_by_date.transactionCategory = "Transfer"
				OR 
				v_account_ledger_by_date.transactionCategory = "Expense Payment") '.$search_invoice_add);
		$this->db->group_by('v_account_ledger_by_date.accountId');
		$query = $this->db->get();

		return $query;

	}


	public function get_accounts_receivables()
	{


		$search_status = $this->session->userdata('balance_sheet_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transaction_date >= \''.$date_from.'\' AND transaction_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transaction_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transaction_date = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add =  ' AND (transaction_date >= \''.date("Y-01-01").'\' AND transaction_date <= \''.date("Y-m-d").'\') ';

		}
		//retrieve all users
		$this->db->from('v_transactions');
		$this->db->select('SUM(dr_amount) - SUM(cr_amount) AS total_amount');
		$this->db->where('(party = "Patient") '.$search_invoice_add);
		// $this->db->group_by('accountId');
		$query = $this->db->get();
		$query_row = $query->row();
		$total_invoices_balance = $query_row->total_amount;

		return $total_invoices_balance ;

	}

	public function get_cash_on_hand()
	{

		$search_status = $this->session->userdata('balance_sheet_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transaction_date >= \''.$date_from.'\' AND transaction_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transaction_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transaction_date = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add =  ' AND (transaction_date >= \''.date("Y-01-01").'\' AND transaction_date <= \''.date("Y-m-d").'\') ';

		}
		$this->db->from('v_transactions');
		$this->db->select('SUM(cr_amount) AS total_amount');
		$this->db->where('(party = "Patient" AND payment_method_name ="Cash") '.$search_invoice_add);
		// $this->db->group_by('accountId');
		$query = $this->db->get();
		$query_row = $query->row();
		$total_invoices_balance = $query_row->total_amount;

		$search_status = $this->session->userdata('balance_sheet_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_addold =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_addold = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_addold = ' AND transactionDate = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_addold =  ' AND (transactionDate >= \''.date("Y-01-01").'\' AND transactionDate <= \''.date("Y-m-d").'\') ';

		}

		$this->db->from('v_general_ledger_by_date');
		$this->db->select('(SUM(dr_amount) - SUM(cr_amount)) AS total_amount');
		$this->db->where('(
							(v_general_ledger_by_date.transactionClassification = "Purchase Payment" AND v_general_ledger_by_date.accountName = "Cash Account")
									OR (v_general_ledger_by_date.transactionCategory = "Transfer" AND  v_general_ledger_by_date.accountName = "Cash Account")
									OR (v_general_ledger_by_date.transactionCategory = "Expense Payment" AND  v_general_ledger_by_date.accountName = "Cash Account")) '.$search_invoice_addold);
		// $this->db->group_by('accountId');
		$query = $this->db->get();
		$query_row2 = $query->row();
		$total_transfer_balance = $query_row2->total_amount;

		return $total_invoices_balance - $total_transfer_balance;
	}
	public function get_accounts_payable()
	{
		//retrieve all users

		$search_status = $this->session->userdata('balance_sheet_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add =  ' AND (transactionDate >= \''.date("Y-01-01").'\' AND transactionDate <= \''.date("Y-m-d").'\') ';

		}


		$this->db->from('v_general_ledger_by_date');
		$this->db->select('(SUM(dr_amount) - SUM(cr_amount)) AS total_amount');
		$this->db->where('recepientId > 0  '.$search_invoice_add);
		// $this->db->group_by('accountId');
		$query = $this->db->get();
		$query_row = $query->row();
		$total_invoices_balance = $query_row->total_amount;

		//
		// $this->db->from('v_general_ledger');
		// $this->db->select('(SUM(cr_amount)) AS total_payments');
		// $this->db->where('recepientId > 0 AND transactionCategory = "Expense Payment" '.$search_invoice_add);
		// // $this->db->group_by('accountId');
		// $query = $this->db->get();
		// $query_credit_row = $query->row();
		// $total_payments = $query_credit_row->total_payments;

		return $total_invoices_balance;
	}


	public function get_total_wht_tax()
	{
		$search_status = $this->session->userdata('balance_sheet_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND created = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND created = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add =  ' AND (created >= \''.date("Y-01-01").'\' AND created <= \''.date("Y-m-d").'\') ';

		}
		//retrieve all users
		$this->db->from('creditor_invoice_item');
		$this->db->select('SUM(vat_amount) AS total_amount');
		$this->db->where('vat_type_id = 2 AND vat_amount > 0 '.$search_invoice_add);
		// $this->db->group_by('accountId');
		$query = $this->db->get();
		$query_row = $query->row();
		$total_invoices_balance = $query_row->total_amount;

		$search_status = $this->session->userdata('balance_sheet_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND created = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND created = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add =  ' AND (created >= \''.date("Y-01-01").'\' AND created <= \''.date("Y-m-d").'\') ';

		}

		//retrieve all users
		$this->db->from('creditor_credit_note_item');
		$this->db->select('SUM(credit_note_charged_vat) AS total_amount');
		$this->db->where('vat_type_id = 2 AND credit_note_charged_vat > 0 '.$search_invoice_add);
		// $this->db->group_by('accountId');
		$query2 = $this->db->get();
		$query_row2 = $query2->row();
		$total_credit_note_balance = $query_row2->total_amount;

		return $total_invoices_balance - $total_credit_note_balance;

	}

	public function get_total_vat_tax()
	{

		$search_status = $this->session->userdata('balance_sheet_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND created = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND created = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add =  ' AND (created >= \''.date("Y-01-01").'\' AND created <= \''.date("Y-m-d").'\') ';

		}
		//retrieve all users
		$this->db->from('creditor_invoice_item');
		$this->db->select('SUM(vat_amount) AS total_amount');
		$this->db->where('vat_type_id = 1 AND vat_amount > 0 '.$search_invoice_add);
		// $this->db->group_by('accountId');
		$query = $this->db->get();
		$query_row = $query->row();
		$total_invoices_balance = $query_row->total_amount;

		$search_status = $this->session->userdata('balance_sheet_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND created = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND created = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add =  ' AND (created >= \''.date("Y-01-01").'\' AND created <= \''.date("Y-m-d").'\') ';

		}

		//retrieve all users
		$this->db->from('creditor_credit_note_item');
		$this->db->select('SUM(credit_note_charged_vat) AS total_amount');
		$this->db->where('vat_type_id = 1 AND credit_note_charged_vat > 0 '.$search_invoice_add);
		// $this->db->group_by('accountId');
		$query2 = $this->db->get();
		$query_row2 = $query2->row();
		$total_credit_note_balance = $query_row2->total_amount;

		// var_dump($total_credit_note_balance);die();
		return $total_invoices_balance - $total_credit_note_balance;

	}


	public function get_tax_total_wht_tax()
	{
		$search_status = $this->session->userdata('tax_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_tax');
			$date_to = $this->session->userdata('date_to_tax');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND created = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND created = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add =  ' AND (created >= \''.date("Y-01-01").'\' AND created <= \''.date("Y-m-d").'\') ';

		}
		//retrieve all users
		$this->db->from('creditor_invoice_item');
		$this->db->select('SUM(vat_amount) AS total_amount');
		$this->db->where('vat_type_id = 2 AND vat_amount > 0 '.$search_invoice_add);
		// $this->db->group_by('accountId');
		$query = $this->db->get();
		$query_row = $query->row();
		$total_invoices_balance = $query_row->total_amount;


		return $total_invoices_balance;

	}


	public function get_tax_total_vat_tax()
	{

		$search_status = $this->session->userdata('tax_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_tax');
			$date_to = $this->session->userdata('date_to_tax');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND created = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND created = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add =  ' AND (created >= \''.date("Y-01-01").'\' AND created <= \''.date("Y-m-d").'\') ';

		}
		//retrieve all users
		$this->db->from('creditor_invoice_item');
		$this->db->select('SUM(vat_amount) AS total_amount');
		$this->db->where('vat_type_id = 1 AND vat_amount > 0 '.$search_invoice_add);
		// $this->db->group_by('accountId');
		$query = $this->db->get();
		$query_row = $query->row();
		$total_invoices_balance = $query_row->total_amount;




		return $total_invoices_balance;

	}
	public function get_all_vendors()
	{
		$this->db->from('creditor');
		$this->db->select('*');
		$this->db->where('creditor_id > 0');
		$query = $this->db->get();

		return $query;
	}

	public function get_creditor_expenses($creditor_id)
	{

		$search_status = $this->session->userdata('vendor_expense_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_vendor_expense');
			$date_to = $this->session->userdata('date_to_vendor_expense');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add =  ' AND (transactionDate >= \''.date("Y-01-01").'\' AND transactionDate <= \''.date("Y-m-d").'\') ';

		}
		//retrieve all users
		$this->db->from('v_general_ledger');
		$this->db->select('(SUM(dr_amount) - SUM(cr_amount)) AS total_amount');
		$this->db->where('recepientId = '.$creditor_id.''.$search_invoice_add);
		// $this->db->group_by('accountId');
		$query = $this->db->get();
		$query_row = $query->row();
		$total_invoices_balance = $query_row->total_amount;


		return $total_invoices_balance ;
	}



	public function get_all_visit_types()
	{
		$this->db->from('visit_type');
		$this->db->select('*');
		$this->db->where('visit_type_id > 0');
		$query = $this->db->get();

		return $query;
	}

	public function get_receivable_balances($visit_type_id)
	{
		$search_status = $this->session->userdata('customer_income_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_customer_income');
			$date_to = $this->session->userdata('date_to_customer_income');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transaction_date >= \''.$date_from.'\' AND transaction_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transaction_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transaction_date = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = ' AND transaction_date = \''.date('Y-m-d').'\'';

		}

		//retrieve all users
		$this->db->from('v_transactions');
		$this->db->select('SUM(dr_amount) - SUM(cr_amount) AS total_amount');
		$this->db->where('payment_type = '.$visit_type_id.'  AND party = "Patient" '.$search_invoice_add);
		// $this->db->group_by('accountId');
		$query = $this->db->get();
		$query_row = $query->row();
		$total_invoices_balance = $query_row->total_amount;



		return $total_invoices_balance;

	}

	public function get_total_receivable_wht_tax()
	{
		//retrieve all users
		$this->db->from('lease_invoice,invoice');
		$this->db->select('SUM(tax_amount) AS total_amount');
		$this->db->where('tax_amount > 0 AND lease_invoice.lease_invoice_id = invoice.lease_invoice_id');
		// $this->db->group_by('accountId');
		$query = $this->db->get();
		$query_row = $query->row();
		$total_invoices_balance = $query_row->total_amount;


		return $total_invoices_balance;

	}

	public function get_opening_stock()
	{

		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$start_date = date('Y-m-01', strtotime($date_from));
				$search_invoice_add =  '  AND transactionDate < \''.$start_date.'\' ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate < \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate < \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}
		$table = 'v_product_stock';
		$where = '(v_product_stock.transactionClassification = "Product Opening Stock" OR  v_product_stock.transactionClassification = "Supplier Purchases" OR  v_product_stock.transactionClassification = "Product Addition" OR v_product_stock.transactionClassification = "Supplier Credit Note" OR v_product_stock.transactionClassification = "Product Deductions" OR v_product_stock.transactionClassification = "Drug Sales") AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;
		$this->db->select('SUM(dr_amount) - SUM(cr_amount)  AS starting_value');
		$this->db->where($where);
		$query = $this->db->get($table);


		$result = $query->row();

		$starting_value  =0 ;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$starting_value = $value->starting_value;
			}
		}



		return  $starting_value;
	}



	public function get_product_balance_brought_forward()
	{

		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$start_date = date('Y-m-01', strtotime($date_from));
				$search_invoice_add =  '  AND transactionDate < \''.$start_date.'\' ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate < \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate < \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}
		$table = 'v_product_stock';
		$where = '(v_product_stock.transactionClassification = "Product Opening Stock" OR  v_product_stock.transactionClassification = "Supplier Purchases" OR  v_product_stock.transactionClassification = "Product Addition" OR v_product_stock.transactionClassification = "Supplier Credit Note" OR v_product_stock.transactionClassification = "Product Deductions" OR v_product_stock.transactionClassification = "Drug Sales")  '.$search_invoice_add;
		$this->db->select('SUM(dr_amount) AS dr_amount, SUM(cr_amount)  AS cr_amount, SUM(dr_quantity) AS dr_quantity,SUM(cr_quantity) AS cr_quantity');
		$this->db->where($where);
		$query = $this->db->get($table);



		return  $query;
	}

	public function get_product_purchases_new($start_date=null)
	{
		if($start_date == NULL)
		{
			$start_date = date('Y-m-d');
		}


		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' transactionDate = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}

		// $table = 'v_product_stock';
		// $where = '(v_product_stock.transactionClassification = "Product Opening Stock" OR  v_product_stock.transactionClassification = "Supplier Purchases" )  AND ( category_id = 2 OR category_id = 3) '.$search_invoice_add;
		// $this->db->select('SUM(dr_amount) - SUM(cr_amount)  AS starting_value');
		// $this->db->where($where);
		$select_statement = "
							SELECT
								data.transactionCategory,
								SUM(data.dr_amount) AS dr_amount,
								SUM(data.cr_amount) AS cr_amount
							FROM 
							(
								SELECT 
								  `store_product`.`store_product_id` AS `transactionId`, 
								  `product`.`product_id` AS `product_id`, 
								  `product`.`category_id` AS `category_id`, 
								  `store_product`.`owning_store_id` AS `store_id`, 
								  '' AS `receiving_store`, 
								  `product`.`product_name` AS `product_name`, 
								  `store`.`store_name` AS `store_name`, 
								  concat(
								    'Opening Balance of', ' ', `product`.`product_name`
								  ) AS `transactionDescription`, 
								  `store_product`.`store_quantity` AS `dr_quantity`, 
								  '0' AS `cr_quantity`, 
								  (
								    `product`.`product_unitprice` * `store_product`.`store_quantity`
								  ) AS `dr_amount`, 
								  '0' AS `cr_amount`, 
								  `store_product`.`created` AS `transactionDate`, 
								  `product`.`product_status` AS `status`, 
								  `product`.`product_deleted` AS `product_deleted`, 
								  'Purchases' AS `transactionCategory`, 
								  'Product Opening Stock' AS `transactionClassification`, 
								  'store_product' AS `transactionTable`, 
								  'product' AS `referenceTable` 
								from 
								  (
								    (
								      `store_product` 
								      join `product` on(
								        (
								          (
								            `product`.`product_id` = `store_product`.`product_id`
								          ) 
								          and (`product`.`product_deleted` = 0)
								        )
								      )
								    ) 
								    join `store` on(
								      (
								        `store`.`store_id` = `store_product`.`owning_store_id`
								      )
								    )
								  ) 
								union all 
								select 
								  `order_supplier`.`order_supplier_id` AS `transactionId`, 
								  `product`.`product_id` AS `product_id`, 
								  `product`.`category_id` AS `category_id`, 
								  `orders`.`store_id` AS `store_id`, 
								  '' AS `receiving_store`, 
								  `product`.`product_name` AS `product_name`, 
								  `store`.`store_name` AS `store_name`, 
								  concat(
								    'Purchase of', ' ', `product`.`product_name`
								  ) AS `transactionDescription`, 
								  (
								    `order_supplier`.`quantity_received` * `order_supplier`.`pack_size`
								  ) AS `dr_quantity`, 
								  '0' AS `cr_quantity`, 
								  `order_supplier`.`total_amount` AS `dr_amount`, 
								  '0' AS `cr_amount`, 
								  `orders`.`supplier_invoice_date` AS `transactionDate`, 
								  `product`.`product_status` AS `status`, 
								  `product`.`product_deleted` AS `product_deleted`, 
								  'Purchases' AS `transactionCategory`, 
								  'Supplier Purchases' AS `transactionClassification`, 
								  'order_item' AS `transactionTable`, 
								  'orders' AS `referenceTable` 
								from 
								  (
								    (
								      (
								        (
								          `order_item` 
								          join `order_supplier`
								        ) 
								        join `product`
								      ) 
								      join `orders`
								    ) 
								    join `store` on(
								      (
								        `store`.`store_id` = `orders`.`store_id`
								      )
								    )
								  ) 
								where 
								  (
								    (
								      `order_item`.`order_item_id` = `order_supplier`.`order_item_id`
								    ) 
								    and (
								      `order_item`.`product_id` = `product`.`product_id`
								    ) 
								    and (
								      `orders`.`order_id` = `order_item`.`order_id`
								    ) 
								    and (`product`.`product_deleted` = 0) 
								    and (`orders`.`supplier_id` > 0) 
								    and (`orders`.`is_store` < 2) 
								    and (
								      `orders`.`order_approval_status` = 7
								    ) 
								    and (`product`.`product_id` <> 0)
								  ) 
								union all 
								select 
								  `product_purchase`.`purchase_id` AS `transactionId`, 
								  `product_purchase`.`product_id` AS `product_id`, 
								  `product`.`category_id` AS `category_id`, 
								  `product_purchase`.`store_id` AS `store_id`, 
								  '' AS `receiving_store`, 
								  `product`.`product_name` AS `product_name`, 
								  `store`.`store_name` AS `store_name`, 
								  `product_purchase`.`purchase_description` AS `transactionDescription`, 
								  (
								    `product_purchase`.`purchase_quantity` * `product_purchase`.`purchase_pack_size`
								  ) AS `dr_quantity`, 
								  '0' AS `cr_quantity`, 
								  (
								    `product`.`product_unitprice` * (
								      `product_purchase`.`purchase_quantity` * `product_purchase`.`purchase_pack_size`
								    )
								  ) AS `dr_amount`, 
								  '0' AS `cr_amount`, 
								  `product_purchase`.`purchase_date` AS `transactionDate`, 
								  `product`.`product_status` AS `status`, 
								  `product`.`product_deleted` AS `product_deleted`, 
								  'Additions' AS `transactionCategory`, 
								  'Product Addition' AS `transactionClassification`, 
								  'product_purchase' AS `transactionTable`, 
								  'product' AS `referenceTable` 
								from 
								  (
								    (
								      `product_purchase` 
								      join `product`
								    ) 
								    join `store` on(
								      (
								        `store`.`store_id` = `product_purchase`.`store_id`
								      )
								    )
								  ) 
								where 
								  (
								    (
								      `product`.`product_id` = `product_purchase`.`product_id`
								    ) 
								    and (`product`.`product_deleted` = 0)
								  ) 
								union all 
								select 
								  `product_deductions_stock`.`product_deductions_stock_id` AS `transactionId`, 
								  `product_deductions_stock`.`product_id` AS `product_id`, 
								  `product`.`category_id` AS `category_id`, 
								  `product_deductions_stock`.`store_id` AS `store_id`, 
								  '' AS `receiving_store`, 
								  `product`.`product_name` AS `product_name`, 
								  `store`.`store_name` AS `store_name`, 
								  `product_deductions_stock`.`deduction_description` AS `transactionDescription`, 
								  '0' AS `dr_quantity`, 
								  (
								    `product_deductions_stock`.`product_deductions_stock_quantity` * `product_deductions_stock`.`product_deductions_stock_pack_size`
								  ) AS `cr_quantity`, 
								  '0' AS `dr_amount`, 
								  (
								    `product`.`product_unitprice` * (
								      `product_deductions_stock`.`product_deductions_stock_quantity` * `product_deductions_stock`.`product_deductions_stock_pack_size`
								    )
								  ) AS `cr_amount`, 
								  `product_deductions_stock`.`product_deductions_stock_date` AS `transactionDate`, 
								  `product`.`product_status` AS `status`, 
								  `product`.`product_deleted` AS `product_deleted`, 
								  'Deductions' AS `transactionCategory`, 
								  'Product Deductions' AS `transactionClassification`, 
								  'product_deductions_stock' AS `transactionTable`, 
								  'product' AS `referenceTable` 
								from 
								  (
								    (
								      `product_deductions_stock` 
								      join `product`
								    ) 
								    join `store` on(
								      (
								        `store`.`store_id` = `product_deductions_stock`.`store_id` AND store.store_parent = 0
								      )
								    )
								  ) 
								where 
								  (
								    (
								      `product`.`product_id` = `product_deductions_stock`.`product_id`
								    ) 
								    and (`product`.`product_deleted` = 0) AND (product.category_id < 2 OR product.category_id > 2)
								  ) 
								union all 
								select 
								  `order_supplier`.`order_supplier_id` AS `transactionId`, 
								  `product`.`product_id` AS `product_id`, 
								  `product`.`category_id` AS `category_id`, 
								  `orders`.`store_id` AS `store_id`, 
								  '' AS `receiving_store`, 
								  `product`.`product_name` AS `product_name`, 
								  `store`.`store_name` AS `store_name`, 
								  concat(
								    'Credit note of', ' ', `product`.`product_name`
								  ) AS `transactionDescription`, 
								  '0' AS `dr_quantity`, 
								  (
								    `order_supplier`.`quantity_received` * `order_supplier`.`pack_size`
								  ) AS `cr_quantity`, 
								  '0' AS `dr_amount`, 
								  `order_supplier`.`total_amount` AS `cr_amount`, 
								  `orders`.`supplier_invoice_date` AS `transactionDate`, 
								  `product`.`product_status` AS `status`, 
								  `product`.`product_deleted` AS `product_deleted`, 
								  'Return Outwards' AS `transactionCategory`, 
								  'Supplier Credit Note' AS `transactionClassification`, 
								  'order_item' AS `transactionTable`, 
								  'orders' AS `referenceTable` 
								from 
								  (
								    (
								      (
								        (
								          `order_item` 
								          join `order_supplier`
								        ) 
								        join `product`
								      ) 
								      join `orders`
								    ) 
								    join `store` on(
								      (
								        `store`.`store_id` = `orders`.`store_id`
								      )
								    )
								  ) 
								where 
								  (
								    (
								      `order_item`.`order_item_id` = `order_supplier`.`order_item_id`
								    ) 
								    and (
								      `order_item`.`product_id` = `product`.`product_id`
								    ) 
								    and (
								      `orders`.`order_id` = `order_item`.`order_id`
								    ) 
								    and (`product`.`product_deleted` = 0) 
								    and (`orders`.`supplier_id` > 0) 
								    and (
								      `orders`.`order_approval_status` = 7
								    ) 
								    and (`orders`.`is_store` = 3)
								  ) 
								union all 
								select 
								  `visit_charge`.`visit_charge_id` AS `transactionId`, 
								  `product`.`product_id` AS `product_id`, 
								  `product`.`category_id` AS `category_id`, 
								  visit_charge.store_id AS `store_id`, 
								  '' AS `receiving_store`, 
								  `product`.`product_name` AS `product_name`, 
								  `store`.`store_name` AS `store_name`, 
								  concat(
								    'Product Sale', ' ', `product`.`product_name`
								  ) AS `transactionDescription`, 
								  '0' AS `dr_quantity`, 
								  `visit_charge`.`visit_charge_units` AS `cr_quantity`, 
								  '0' AS `dr_amount`, 
								  (
								    `visit_charge`.`visit_charge_units` * `visit_charge`.`buying_price`
								  ) AS `cr_amount`, 
								  `visit_charge`.`date` AS `transactionDate`, 
								  `product`.`product_status` AS `status`, 
								  `product`.`product_deleted` AS `product_deleted`, 
								  'Sales' AS `transactionCategory`, 
								  'Drug Sales' AS `transactionClassification`, 
								  'visit_charge' AS `transactionTable`, 
								  'product' AS `referenceTable` 
								from 
								  (
								    (
								      `visit_charge` 
								      join `product`
								    ) 
								    join `store` on(
								      (`store`.`store_id` = visit_charge.store_id)
								    )
								  ) 
								where 
								  (
								    (`visit_charge`.`charged` = 1) 
								    and (
								      `visit_charge`.`visit_charge_delete` = 0
								    ) 
								    and (
								      `product`.`product_id` = `visit_charge`.`product_id`
								    ) 
								    and (`product`.`product_deleted` = 0)
								  ) 
								union all 
								select 
								  `product_deductions`.`product_deductions_id` AS `transactionId`, 
								  `product`.`product_id` AS `product_id`, 
								  `product`.`category_id` AS `category_id`, 
								  `store`.`store_id` AS `store_id`, 
								  '' AS `receiving_store`, 
								  `product`.`product_name` AS `product_name`, 
								  `store`.`store_name` AS `store_name`, 
								  concat(
								    'Product Transfered', ' ', `product`.`product_name`
								  ) AS `transactionDescription`, 
								  '0' AS `dr_quantity`, 
								  (
								    `product_deductions`.`quantity_given` * `product_deductions`.`pack_size`
								  ) AS `cr_quantity`, 
								  '0' AS `dr_amount`, 
								  (
								    `product`.`product_unitprice` * (
								      `product_deductions`.`quantity_given` * `product_deductions`.`pack_size`
								    )
								  ) AS `cr_amount`, 
								  `product_deductions`.`search_date` AS `transactionDate`, 
								  `product`.`product_status` AS `status`, 
								  `product`.`product_deleted` AS `product_deleted`, 
								  'Deductions' AS `transactionCategory`, 
								  'Drug Transfer' AS `transactionClassification`, 
								  'product_deductions' AS `transactionTable`, 
								  'product' AS `referenceTable` 
								from 
								  (
								    (
								      (
								        `product_deductions` 
								        join `product`
								      ) 
								      join `orders`
								    ) 
								    join `store` on(
								      (
								        `orders`.`store_id` = `store`.`store_id`
								      )
								    )
								  ) 
								where 
								  (
								    (
								      `product_deductions`.`order_id` = `orders`.`order_id`
								    ) 
								    and (
								      `product_deductions`.`product_id` = `product`.`product_id`
								    ) 
								    and (`product`.`product_deleted` = 0) 
								    and (`orders`.`supplier_id` > 0) 
								    and (`orders`.`is_store` = 2) 
								    and (
								      `orders`.`order_approval_status` = 7
								    )
								  )
							) AS data WHERE ".$search_invoice_add."  GROUP BY data.transactionCategory";
		$query = $this->db->query($select_statement);

		// var_dump($query->result());die();
		$result = $query->row();

		$product_value  = array();
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$item['name'] = $value->transactionCategory;
				$item['value'] = $value->dr_amount - $value->cr_amount;

				array_push($product_value, $item);
			}
		}

		// var_dump($product_value);
		return  $product_value;
	}

	public function get_opening_stock_value()
	{

		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$start_date = date('Y-m-01', strtotime($date_from));
				$search_invoice_add =  '  transactionDate < \''.$start_date.'\' ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' transactionDate < \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' transactionDate < \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}
			$select_statement = "
							SELECT
								SUM(data.dr_amount) AS dr_amount,
								SUM(data.cr_amount) AS cr_amount
							FROM 
							(
								SELECT 
								  `store_product`.`store_product_id` AS `transactionId`, 
								  `product`.`product_id` AS `product_id`, 
								  `product`.`category_id` AS `category_id`, 
								  `store_product`.`owning_store_id` AS `store_id`, 
								  '' AS `receiving_store`, 
								  `product`.`product_name` AS `product_name`, 
								  `store`.`store_name` AS `store_name`, 
								  concat(
								    'Opening Balance of', ' ', `product`.`product_name`
								  ) AS `transactionDescription`, 
								  `store_product`.`store_quantity` AS `dr_quantity`, 
								  '0' AS `cr_quantity`, 
								  (
								    `product`.`product_unitprice` * `store_product`.`store_quantity`
								  ) AS `dr_amount`, 
								  '0' AS `cr_amount`, 
								  `store_product`.`created` AS `transactionDate`, 
								  `product`.`product_status` AS `status`, 
								  `product`.`product_deleted` AS `product_deleted`, 
								  'Purchases' AS `transactionCategory`, 
								  'Product Opening Stock' AS `transactionClassification`, 
								  'store_product' AS `transactionTable`, 
								  'product' AS `referenceTable` 
								from 
								  (
								    (
								      `store_product` 
								      join `product` on(
								        (
								          (
								            `product`.`product_id` = `store_product`.`product_id`
								          ) 
								          and (`product`.`product_deleted` = 0)
								        )
								      )
								    ) 
								    join `store` on(
								      (
								        `store`.`store_id` = `store_product`.`owning_store_id`
								      )
								    )
								  ) 
								union all 
								select 
								  `order_supplier`.`order_supplier_id` AS `transactionId`, 
								  `product`.`product_id` AS `product_id`, 
								  `product`.`category_id` AS `category_id`, 
								  `orders`.`store_id` AS `store_id`, 
								  '' AS `receiving_store`, 
								  `product`.`product_name` AS `product_name`, 
								  `store`.`store_name` AS `store_name`, 
								  concat(
								    'Purchase of', ' ', `product`.`product_name`
								  ) AS `transactionDescription`, 
								  (
								    `order_supplier`.`quantity_received` * `order_supplier`.`pack_size`
								  ) AS `dr_quantity`, 
								  '0' AS `cr_quantity`, 
								  `order_supplier`.`total_amount` AS `dr_amount`, 
								  '0' AS `cr_amount`, 
								  `orders`.`supplier_invoice_date` AS `transactionDate`, 
								  `product`.`product_status` AS `status`, 
								  `product`.`product_deleted` AS `product_deleted`, 
								  'Purchases' AS `transactionCategory`, 
								  'Supplier Purchases' AS `transactionClassification`, 
								  'order_item' AS `transactionTable`, 
								  'orders' AS `referenceTable` 
								from 
								  (
								    (
								      (
								        (
								          `order_item` 
								          join `order_supplier`
								        ) 
								        join `product`
								      ) 
								      join `orders`
								    ) 
								    join `store` on(
								      (
								        `store`.`store_id` = `orders`.`store_id`
								      )
								    )
								  ) 
								where 
								  (
								    (
								      `order_item`.`order_item_id` = `order_supplier`.`order_item_id`
								    ) 
								    and (
								      `order_item`.`product_id` = `product`.`product_id`
								    ) 
								    and (
								      `orders`.`order_id` = `order_item`.`order_id`
								    ) 
								    and (`product`.`product_deleted` = 0) 
								    and (`orders`.`supplier_id` > 0) 
								    and (`orders`.`is_store` < 2) 
								    and (
								      `orders`.`order_approval_status` = 7
								    ) 
								    and (`product`.`product_id` <> 0)
								  ) 
								union all 
								select 
								  `product_purchase`.`purchase_id` AS `transactionId`, 
								  `product_purchase`.`product_id` AS `product_id`, 
								  `product`.`category_id` AS `category_id`, 
								  `product_purchase`.`store_id` AS `store_id`, 
								  '' AS `receiving_store`, 
								  `product`.`product_name` AS `product_name`, 
								  `store`.`store_name` AS `store_name`, 
								  `product_purchase`.`purchase_description` AS `transactionDescription`, 
								  (
								    `product_purchase`.`purchase_quantity` * `product_purchase`.`purchase_pack_size`
								  ) AS `dr_quantity`, 
								  '0' AS `cr_quantity`, 
								  (
								    `product`.`product_unitprice` * (
								      `product_purchase`.`purchase_quantity` * `product_purchase`.`purchase_pack_size`
								    )
								  ) AS `dr_amount`, 
								  '0' AS `cr_amount`, 
								  `product_purchase`.`purchase_date` AS `transactionDate`, 
								  `product`.`product_status` AS `status`, 
								  `product`.`product_deleted` AS `product_deleted`, 
								  'Additions' AS `transactionCategory`, 
								  'Product Addition' AS `transactionClassification`, 
								  'product_purchase' AS `transactionTable`, 
								  'product' AS `referenceTable` 
								from 
								  (
								    (
								      `product_purchase` 
								      join `product`
								    ) 
								    join `store` on(
								      (
								        `store`.`store_id` = `product_purchase`.`store_id`
								      )
								    )
								  ) 
								where 
								  (
								    (
								      `product`.`product_id` = `product_purchase`.`product_id`
								    ) 
								    and (`product`.`product_deleted` = 0)
								  ) 
								union all 
								select 
								  `product_deductions_stock`.`product_deductions_stock_id` AS `transactionId`, 
								  `product_deductions_stock`.`product_id` AS `product_id`, 
								  `product`.`category_id` AS `category_id`, 
								  `product_deductions_stock`.`store_id` AS `store_id`, 
								  '' AS `receiving_store`, 
								  `product`.`product_name` AS `product_name`, 
								  `store`.`store_name` AS `store_name`, 
								  `product_deductions_stock`.`deduction_description` AS `transactionDescription`, 
								  '0' AS `dr_quantity`, 
								  (
								    `product_deductions_stock`.`product_deductions_stock_quantity` * `product_deductions_stock`.`product_deductions_stock_pack_size`
								  ) AS `cr_quantity`, 
								  '0' AS `dr_amount`, 
								  (
								    `product`.`product_unitprice` * (
								      `product_deductions_stock`.`product_deductions_stock_quantity` * `product_deductions_stock`.`product_deductions_stock_pack_size`
								    )
								  ) AS `cr_amount`, 
								  `product_deductions_stock`.`product_deductions_stock_date` AS `transactionDate`, 
								  `product`.`product_status` AS `status`, 
								  `product`.`product_deleted` AS `product_deleted`, 
								  'Deductions' AS `transactionCategory`, 
								  'Product Deductions' AS `transactionClassification`, 
								  'product_deductions_stock' AS `transactionTable`, 
								  'product' AS `referenceTable` 
								from 
								  (
								    (
								      `product_deductions_stock` 
								      join `product`
								    ) 
								    join `store` on(
								      (
								        `store`.`store_id` = `product_deductions_stock`.`store_id`
								      )
								    )
								  ) 
								where 
								  (
								    (
								      `product`.`product_id` = `product_deductions_stock`.`product_id`
								    ) 
								    and (`product`.`product_deleted` = 0)
								  ) 
								union all 
								select 
								  `order_supplier`.`order_supplier_id` AS `transactionId`, 
								  `product`.`product_id` AS `product_id`, 
								  `product`.`category_id` AS `category_id`, 
								  `orders`.`store_id` AS `store_id`, 
								  '' AS `receiving_store`, 
								  `product`.`product_name` AS `product_name`, 
								  `store`.`store_name` AS `store_name`, 
								  concat(
								    'Credit note of', ' ', `product`.`product_name`
								  ) AS `transactionDescription`, 
								  '0' AS `dr_quantity`, 
								  (
								    `order_supplier`.`quantity_received` * `order_supplier`.`pack_size`
								  ) AS `cr_quantity`, 
								  '0' AS `dr_amount`, 
								  `order_supplier`.`total_amount` AS `cr_amount`, 
								  `orders`.`supplier_invoice_date` AS `transactionDate`, 
								  `product`.`product_status` AS `status`, 
								  `product`.`product_deleted` AS `product_deleted`, 
								  'Return Outwards' AS `transactionCategory`, 
								  'Supplier Credit Note' AS `transactionClassification`, 
								  'order_item' AS `transactionTable`, 
								  'orders' AS `referenceTable` 
								from 
								  (
								    (
								      (
								        (
								          `order_item` 
								          join `order_supplier`
								        ) 
								        join `product`
								      ) 
								      join `orders`
								    ) 
								    join `store` on(
								      (
								        `store`.`store_id` = `orders`.`store_id`
								      )
								    )
								  ) 
								where 
								  (
								    (
								      `order_item`.`order_item_id` = `order_supplier`.`order_item_id`
								    ) 
								    and (
								      `order_item`.`product_id` = `product`.`product_id`
								    ) 
								    and (
								      `orders`.`order_id` = `order_item`.`order_id`
								    ) 
								    and (`product`.`product_deleted` = 0) 
								    and (`orders`.`supplier_id` > 0) 
								    and (
								      `orders`.`order_approval_status` = 7
								    ) 
								    and (`orders`.`is_store` = 3)
								  ) 
								union all 
								select 
								  `visit_charge`.`visit_charge_id` AS `transactionId`, 
								  `product`.`product_id` AS `product_id`, 
								  `product`.`category_id` AS `category_id`, 
								  visit_charge.store_id AS `store_id`, 
								  '' AS `receiving_store`, 
								  `product`.`product_name` AS `product_name`, 
								  `store`.`store_name` AS `store_name`, 
								  concat(
								    'Product Sale', ' ', `product`.`product_name`
								  ) AS `transactionDescription`, 
								  '0' AS `dr_quantity`, 
								  `visit_charge`.`visit_charge_units` AS `cr_quantity`, 
								  '0' AS `dr_amount`, 
								  (
								    `visit_charge`.`visit_charge_units` * `visit_charge`.`buying_price`
								  ) AS `cr_amount`, 
								  `visit_charge`.`date` AS `transactionDate`, 
								  `product`.`product_status` AS `status`, 
								  `product`.`product_deleted` AS `product_deleted`, 
								  'Sales' AS `transactionCategory`, 
								  'Drug Sales' AS `transactionClassification`, 
								  'visit_charge' AS `transactionTable`, 
								  'product' AS `referenceTable` 
								from 
								  (
								    (
								      `visit_charge` 
								      join `product`
								    ) 
								    join `store` on(
								      (`store`.`store_id` = visit_charge.store_id)
								    )
								  ) 
								where 
								  (
								    (`visit_charge`.`charged` = 1) 
								    and (
								      `visit_charge`.`visit_charge_delete` = 0
								    ) 
								    and (
								      `product`.`product_id` = `visit_charge`.`product_id`
								    ) 
								    and (`product`.`product_deleted` = 0)
								  ) 
								union all 
								select 
								  `product_deductions`.`product_deductions_id` AS `transactionId`, 
								  `product`.`product_id` AS `product_id`, 
								  `product`.`category_id` AS `category_id`, 
								  `store`.`store_id` AS `store_id`, 
								  '' AS `receiving_store`, 
								  `product`.`product_name` AS `product_name`, 
								  `store`.`store_name` AS `store_name`, 
								  concat(
								    'Product Transfered', ' ', `product`.`product_name`
								  ) AS `transactionDescription`, 
								  '0' AS `dr_quantity`, 
								  (
								    `product_deductions`.`quantity_given` * `product_deductions`.`pack_size`
								  ) AS `cr_quantity`, 
								  '0' AS `dr_amount`, 
								  (
								    `product`.`product_unitprice` * (
								      `product_deductions`.`quantity_given` * `product_deductions`.`pack_size`
								    )
								  ) AS `cr_amount`, 
								  `product_deductions`.`search_date` AS `transactionDate`, 
								  `product`.`product_status` AS `status`, 
								  `product`.`product_deleted` AS `product_deleted`, 
								  'Deductions' AS `transactionCategory`, 
								  'Drug Transfer' AS `transactionClassification`, 
								  'product_deductions' AS `transactionTable`, 
								  'product' AS `referenceTable` 
								from 
								  (
								    (
								      (
								        `product_deductions` 
								        join `product`
								      ) 
								      join `orders`
								    ) 
								    join `store` on(
								      (
								        `orders`.`store_id` = `store`.`store_id`
								      )
								    )
								  ) 
								where 
								  (
								    (
								      `product_deductions`.`order_id` = `orders`.`order_id`
								    ) 
								    and (
								      `product_deductions`.`product_id` = `product`.`product_id`
								    ) 
								    and (`product`.`product_deleted` = 0) 
								    and (`orders`.`supplier_id` > 0) 
								    and (`orders`.`is_store` = 2) 
								    and (
								      `orders`.`order_approval_status` = 7
								    )
								  )
							) AS data WHERE ".$search_invoice_add."";
		$query = $this->db->query($select_statement);

		// var_dump($query->result());die();
		$result = $query->row();

		$dr_amount = $result->dr_amount;
		$cr_amount = $result->cr_amount;

		$starting_value = $dr_amount - $cr_amount;
		return  $starting_value;
	}

	public function get_product_purchases($start_date=null)
	{
		if($start_date == NULL)
		{
			$start_date = date('Y-m-d');
		}


		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}

		$table = 'v_product_stock';
		$where = '(v_product_stock.transactionClassification = "Product Opening Stock" OR  v_product_stock.transactionClassification = "Supplier Purchases" )  AND ( category_id = 2 OR category_id = 3) '.$search_invoice_add;
		$this->db->select('SUM(dr_amount) - SUM(cr_amount)  AS starting_value');
		$this->db->where($where);
		$query = $this->db->get($table);


		$result = $query->row();

		$starting_value  =0 ;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$starting_value = $value->starting_value;
			}
		}
		return  $starting_value;
	}



	public function get_product_other_purchases($start_date=null)
	{
		if($start_date == NULL)
		{
			$start_date = date('Y-m-d');
		}


		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}

		$table = 'v_product_stock';
		$where = '(v_product_stock.transactionClassification = "Product Addition" ) AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;
		$this->db->select('SUM(dr_amount)  AS starting_value');
		$this->db->where($where);
		$query = $this->db->get($table);


		$result = $query->row();

		$starting_value  =0 ;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$starting_value = $value->starting_value;
			}
		}
		return  $starting_value;
	}


	public function get_product_return_outwards($start_date=null)
	{
		if($start_date == NULL)
		{
			$start_date = date('Y-m-d');
		}


		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}

		$table = 'v_product_stock';
		$where = '(v_product_stock.transactionClassification = "Supplier Credit Note" ) AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;
		$this->db->select('SUM(cr_amount)  AS starting_value');
		$this->db->where($where);
		$query = $this->db->get($table);


		$result = $query->row();

		$starting_value  =0 ;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$starting_value = $value->starting_value;
			}
		}
		return  $starting_value;
	}


	public function get_total_other_deductions($start_date=null)
	{
		if($start_date == NULL)
		{
			$start_date = date('Y-m-d');
		}


		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}

		$table = 'v_product_stock';
		$where = '(v_product_stock.transactionClassification = "Product Deductions" OR v_product_stock.transactionClassification = "Drug Transfer" ) AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;
		$this->db->select('SUM(cr_amount)  AS starting_value');
		$this->db->where($where);
		$query = $this->db->get($table);


		$result = $query->row();

		$starting_value  =0 ;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$starting_value = $value->starting_value;
			}
		}
		return  $starting_value;
	}
	public function get_product_sales($start_date=null)
	{
		if($start_date == NULL)
		{
			$start_date = date('Y-m-d');
		}


		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}

		$table = 'v_product_stock';
		$where = '(v_product_stock.transactionClassification = "Drug Sales") AND ( category_id = 2 OR category_id = 3)   '.$search_invoice_add;
		$this->db->select('SUM(cr_amount)  AS starting_value');
		$this->db->where($where);
		$query = $this->db->get($table);


		$result = $query->row();

		$starting_value  =0 ;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$starting_value = $value->starting_value;
			}
		}
		return  $starting_value;
	}


	public function get_closing_stock_old()
	{

		$table = 'product,store_product';
		$where = 'product.product_status = 1 AND product.product_deleted = 0 AND product.product_id = store_product.product_id';
		$this->db->select('SUM((store_product.store_quantity * product.product_unitprice)) AS starting_value');
		$this->db->where($where);
		$query = $this->db->get($table);


		$result = $query->row();

		$starting_value  =0 ;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$starting_value = $value->starting_value;
			}
		}

		$inventory_start_date = $this->company_financial_model->get_inventory_start_date();
		$sales_value = $this->company_financial_model->get_prev_drug_units_sold_value($inventory_start_date);
		$procurred_amount = $this->company_financial_model->get_prev_total_purchases();

		return ($procurred_amount + $starting_value) - $sales_value;
	}


	public function get_stock_value()
	{
		$inventory_start_date = $this->company_financial_model->get_inventory_start_date();

		$sales_value = $this->company_financial_model->get_drug_units_sold_value($inventory_start_date);
		// $procurred_amount = $this->company_financial_model->get_total_purchases();

		return $sales_value;
	}

	public function get_prev_drug_units_sold_value($inventory_start_date, $product_id=NULL, $start_date = NULL, $end_date = NULL, $branch_code = NULL)
	{

		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (visit_charge.date >= \''.$date_from.'\' AND visit_charge.date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND visit_charge.date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND visit_charge.date = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}

		$table = "visit_charge, service_charge,product";
		$where = 'visit_charge.service_charge_id = service_charge.service_charge_id AND visit_charge.charged = 1 AND service_charge.product_id > 0 AND visit_charge.visit_charge_delete = 0 AND product.product_id = service_charge.product_id AND product.product_deleted = 0 '.$search_invoice_add;



		$items = "SUM((visit_charge.visit_charge_units * visit_charge.visit_charge_amount)) AS amount";
		$order = "date";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$total_sold = 0;
		if(count($result) > 0)
		{
			foreach ($result as $key) {
				# code...
				$amount = $key->amount;

				$total_sold =$amount;
			}
		}
		return $total_sold;
	}

	public function get_inventory_start_date()
	{
		$this->db->where('branch_code', $this->session->userdata('branch_code'));
		$query = $this->db->get('branch');

		$inventory_start_date = '';
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$inventory_start_date = $row->inventory_start_date;
		}

		return $inventory_start_date;
	}

	public function get_total_purchases_old($start_date=null)
	{
		if($start_date == NULL)
		{
			$start_date = date('Y-m-d');
		}


		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (orders.supplier_invoice_date >= \''.$date_from.'\' AND orders.supplier_invoice_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND orders.supplier_invoice_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND orders.supplier_invoice_date = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}

		// $table = 'v_general_ledger';
		// $where = '(transactionCategory = "Expense" AND accountName="Catering Expenses") '.$search_invoice_add;
		// $this->db->select('SUM(dr_amount) AS total_amount');
		// $this->db->where($where);
		// $query = $this->db->get($table);
		// $row = $query->row();
		//
		// $total_value = $row->total_amount;

		$table = 'order_supplier,orders,order_item';
		$where = 'invoice_number <> "" AND orders.order_id = order_supplier.order_id AND orders.supplier_id > 0 AND orders.order_approval_status = 7 AND order_supplier.order_item_id = order_item.order_item_id AND orders.supplier_invoice_date <> "0000-00-00" '.$search_invoice_add;
		$this->db->select('SUM(order_supplier.less_vat) AS total_amount');
		$this->db->where($where);
		$query = $this->db->get($table);
		$row = $query->row();

		$total_value = $row->total_amount;

		return $total_value;
	}

	public function get_prev_total_purchases($start_date=null)
	{


		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');


			if(!empty($date_from))
			{
				$search_invoice_add = ' AND orders.supplier_invoice_date < \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND orders.supplier_invoice_date < \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}

		$table = 'order_supplier,orders,order_item';
		$where = 'invoice_number <> "" AND orders.order_id = order_supplier.order_id AND orders.supplier_id > 0 AND orders.order_approval_status = 7 AND order_supplier.order_item_id = order_item.order_item_id AND orders.supplier_invoice_date <> "0000-00-00" '.$search_invoice_add;
		$this->db->select('SUM(order_supplier.less_vat) AS total_amount');
		$this->db->where($where);
		$query = $this->db->get($table);
		$row = $query->row();

		$total_value = $row->total_amount;

		return $total_value;
	}

	public function get_drug_units_sold_value($inventory_start_date, $product_id=NULL, $start_date = NULL, $end_date = NULL, $branch_code = NULL)
	{

		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (visit_charge.date >= \''.$date_from.'\' AND visit_charge.date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND visit_charge.date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND visit_charge.date = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}

		$table = "visit_charge, service_charge,product";
		$where = 'visit_charge.service_charge_id = service_charge.service_charge_id AND visit_charge.charged = 1 AND service_charge.product_id > 0 AND visit_charge.visit_charge_delete = 0 AND product.product_id = service_charge.product_id AND product.product_deleted = 0 '.$search_invoice_add;



		$items = "SUM((visit_charge.visit_charge_units * visit_charge.visit_charge_amount)) AS amount";
		$order = "date";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$total_sold = 0;
		if(count($result) > 0)
		{
			foreach ($result as $key) {
				# code...
				$amount = $key->amount;

				$total_sold =$amount;
			}
		}
		return $total_sold;
	}

	/*
	*	Retrieve all creditor
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_service_bills($table, $where, $per_page, $page, $order = 'visit.visit_date', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$this->db->join('personnel','personnel.personnel_id = visit_charge.provider_id','left');
		$query = $this->db->get('', $per_page, $page);

		return $query;
	}

	function export_services_bills($department_id)
	{
		$this->load->library('excel');


		$this->db->where('visit_charge.visit_id = visit.visit_id AND visit.patient_id = patients.patient_id AND visit.visit_type = visit_type.visit_type_id AND visit_charge.service_charge_id = service_charge.service_charge_id AND visit.visit_delete = 0 AND visit_charge.visit_charge_delete = 0 AND visit_charge.charged = 1 AND service_charge.service_id = service.service_id AND service.department_id ='.$department_id);
		$this->db->select('*');
		$this->db->join('personnel','visit_charge.personnel_id = personnel.personnel_id','left');
		$table = 'visit_charge,visit,patients,service_charge,visit_type,service';
		$visits_query = $this->db->get($table);

		$title = 'Service Bill Export '.date('jS M Y H:i a',strtotime(date('Y-m-d H:i:s')));
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
			$report[$row_count][$col_count] = 'Name';
			$col_count++;
			$report[$row_count][$col_count] = 'Category';
			$col_count++;
			$report[$row_count][$col_count] = 'Service';
			$col_count++;
			$current_column = $col_count ;

			$report[$row_count][$current_column] = 'Provider';
			$current_column++;
			$report[$row_count][$current_column] = 'Units';
			$current_column++;
			$report[$row_count][$current_column] = 'Charge Amount';
			$current_column++;
			//display all patient data in the leftmost columns
			foreach($visits_query->result() as $row)
			{
				$row_count++;
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
				$date = $row->date;
				$invoice_date = date('jS M Y',strtotime($row->date));
				$patient_id = $row->patient_id;
				$personnel_id = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$visit_type_id = $row->visit_type;
				$patient_number = $row->patient_number;
				$visit_type = $row->visit_type;
				$service_name = $row->service_name;
				$visit_table_visit_type = $visit_type;
				$patient_table_visit_type = $visit_type_id;
				$rejected_amount = $row->amount_rejected;
				$invoice_number = $row->invoice_number;
				$visit_charge_amount = $row->visit_charge_amount;
				$visit_charge_units = $row->visit_charge_units;
				$visit_type_name = $row->visit_type_name;
				$personnel = $row->personnel_fname.' '.$row->personnel_onames;


				$count++;

				//display services charged to patient
				$total_invoiced2 = 0;


				//display the patient data
				$report[$row_count][$col_count] = $count;
				$col_count++;
				$report[$row_count][$col_count] = $invoice_date;
				$col_count++;
				$report[$row_count][$col_count] = $row->patient_surname.' '.$row->patient_othernames;
				$col_count++;
				$report[$row_count][$col_count] = $visit_type_name;
				$col_count++;
				$report[$row_count][$col_count] = $service_name;
				$col_count++;
				$current_column = $col_count;


				$report[$row_count][$current_column] = $personnel;
				$current_column++;
				$report[$row_count][$current_column] = $visit_charge_units;
				$current_column++;
				$report[$row_count][$current_column] = round($visit_charge_amount);
				$current_column++;
				$report[$row_count][$current_column] = round($visit_charge_amount * $visit_charge_units);
				$current_column++;

			}
		}

		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
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
	public function get_expense_ledger($account_id)
	{
		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}
		//retrieve all users
		$this->db->from('v_general_ledger');
		$this->db->select('dr_amount AS total_amount,v_general_ledger.*');
		$this->db->where('transactionCategory = "Expense" AND accountId = '.$account_id.'  '.$search_invoice_add);
		$query = $this->db->get();

		return $query;
	}



	public function get_accounts_ledger($account_id)
	{
		$search_status = $this->session->userdata('balance_sheet_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add =  ' AND (transactionDate >= \''.date("Y-01-01").'\' AND transactionDate <= \''.date("Y-m-d").'\') ';

		}
		//retrieve all users
		$this->db->from('v_account_ledger_by_date');
		$this->db->select('v_account_ledger_by_date.*');
		$this->db->where('accountsclassfication = "Bank"
						AND 
						(v_account_ledger_by_date.transactionClassification = "Purchase Payment" AND accountId = '.$account_id.')
									OR (v_account_ledger_by_date.transactionCategory = "Transfer" AND  accountId = '.$account_id.')
									OR (v_account_ledger_by_date.transactionCategory = "Expense Payment" AND  accountId = '.$account_id.')
						'.$search_invoice_add);
		$this->db->order_by('v_account_ledger_by_date.transactionDate','DESC');
		$query = $this->db->get();

		return $query;
	}


	public function get_account_opening_balance($account_id)
	{

		$this->db->from('account');
		$this->db->select('account.account_opening_balance AS total_amount');
		$this->db->where('account_id = '.$account_id);

		$query = $this->db->get();
		$balance = $query->row();

		$total_amount = $balance->total_amount;
		return $total_amount;
	}

	public function get_creditor_statement_balance($creditor_id,$start_date=null)
	{

		$search_status = $this->session->userdata('vendor_expense_search');
		$search_payments_add = '';
		$search_invoice_add = '';

		// var_dump($search_status);die();
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_vendor_expense');
			$date_to = $this->session->userdata('date_to_vendor_expense');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate < \''.$date_from.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate < \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate < \''.$date_to.'\'';
			}
		}
		else
		{
		
			$search_invoice_add =  '';

		}
		//retrieve all users
		$this->db->from('v_general_ledger_by_date,creditor');
		$this->db->select('SUM(`dr_amount`) AS dr_amount,SUM(cr_amount) AS cr_amount');
		$this->db->where('creditor.creditor_id = v_general_ledger_by_date.recepientId AND v_general_ledger_by_date.transactionDate >= creditor.start_date AND recepientId = '.$creditor_id.$search_invoice_add);
		$this->db->order_by('transactionDate','ASC');
		$query = $this->db->get();



		return $query;
	}


	public function get_creditor_statement($creditor_id,$start_date=null)
	{

		$search_status = $this->session->userdata('vendor_expense_search');
		$search_payments_add = '';
		$search_invoice_add = '';

		// var_dump($search_status);die();
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_vendor_expense');
			$date_to = $this->session->userdata('date_to_vendor_expense');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
		}
		else
		{
		
			$search_invoice_add =  '';

		}
		//retrieve all users
		$this->db->from('v_creditor_ledger_by_date,creditor');
		$this->db->select('*');
		$this->db->where('creditor.creditor_id = v_creditor_ledger_by_date.recepientId AND v_creditor_ledger_by_date.transactionDate >= creditor.start_date AND recepientId = '.$creditor_id.$search_invoice_add);
		$this->db->order_by('transactionDate','ASC');
		$query = $this->db->get();



		return $query;
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
	public function get_all_income_statement_views($table, $where, $per_page, $page, $order = 'v_product_stock.transactionDate', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		// $this->db->join('personnel','personnel.personnel_id = visit_charge.provider_id','left');
		$query = $this->db->get('', $per_page, $page);

		return $query;
	}


	public function get_salary_expenses()
	{
		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (payroll_created_for >= \''.$date_from.'\' AND payroll_created_for <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND payroll_created_for = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND payroll_created_for = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}
		//retrieve all users
		$this->db->from('v_payroll');
		$this->db->select('SUM(total_additions) AS total_amount');
		$this->db->where('payroll_id > 0 '.$search_invoice_add);
		// $this->db->group_by('accountId');
		$total_amount = 0;
		$query = $this->db->get();
		if($query->num_rows() > 0 )
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_amount = $value->total_amount;
			}
		}

		return $total_amount;
	}


	public function get_statutories($statutory_id)
	{
		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (payroll_created_for >= \''.$date_from.'\' AND payroll_created_for <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND payroll_created_for = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND payroll_created_for = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}
		if($statutory_id == 1)
		{
			$column = 'total_nssf';
		}
		else if($statutory_id == 2)
		{
			$column = 'total_nhif';
		}
		else if($statutory_id == 3)
		{
			$column = 'total_paye';
		}
		else if($statutory_id == 4)
		{
			$column = 'total_relief';
		}
		else if($statutory_id == 5)
		{
			$column = 'total_loans';
		}
		//retrieve all users
		$this->db->from('v_payroll');
		$this->db->select('SUM('.$column.') AS total_amount');
		$this->db->where('payroll_id > 0 '.$search_invoice_add);
		// $this->db->group_by('accountId');
		$total_amount = 0;
		$query = $this->db->get();
		if($query->num_rows() > 0 )
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_amount = $value->total_amount;
			}
		}

		return $total_amount;
	}


	// get non pharm purchases


	public function get_non_pharm_purchases($start_date=null)
	{
		if($start_date == NULL)
		{
			$start_date = date('Y-m-d');
		}


		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}
		// $table = 'v_product_stock_non_pharm';
		// $where = '(v_product_stock_non_pharm.category_id < 2 OR v_product_stock_non_pharm.category_id > 3) AND v_product_stock_non_pharm.transactionClassification ="Product Addition"  '.$search_invoice_add;
		// $this->db->select('SUM(dr_amount) - SUM(cr_amount)  AS starting_value,category_name,v_product_stock_non_pharm.category_id');
		// $this->db->group_by('v_product_stock_non_pharm.category_id');
		// $this->db->join('category','v_product_stock_non_pharm.category_id = category.category_id','left');

		$select_statement = "
							  SELECT
								`product_deductions_stock`.`product_deductions_stock_id` AS transactionId,
								`product_deductions_stock`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								 product_deductions_stock.store_id AS `store_id`,
								 '' AS `receiving_store`,
								 product.product_name AS `product_name`,
								store.store_name AS `store_name`,
								 product_deductions_stock.deduction_description AS `transactionDescription`,
								 '0' AS `dr_quantity`,
								 (product_deductions_stock_quantity * product_deductions_stock_pack_size) AS  `cr_quantity`,
								'0' AS `dr_amount`,
								 (`product`.`product_unitprice` * (product_deductions_stock_quantity * product_deductions_stock_pack_size)) AS `cr_amount`,
								`product_deductions_stock`.`product_deductions_stock_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								category.category_name AS `transactionCategory`,
								'Product Deductions' AS `transactionClassification`,
								'product_deductions_stock' AS `transactionTable`,
								'product' AS `referenceTable`
								FROM (`product_deductions_stock`,product,store,category)
								WHERE product.product_id = product_deductions_stock.product_id AND product.product_deleted = 0
								AND store.store_id = product_deductions_stock.store_id
								AND product.category_id = category.category_id
								AND (product.category_id < 2 OR product.category_id > 2)
								AND store.store_parent > 0
								GROUP BY category.category_id
							  ";



		// $this->db->where($where);
		$query = $this->db->query($select_statement);


		// $result = $query->row();

		// $starting_value  =0 ;
		// if($query->num_rows() > 0)
		// {
		// 	foreach ($query->result() as $key => $value) {
		// 		# code...
		// 		$starting_value = $value->starting_value;
		// 	}
		// }
		return  $query;
	}


	public function export_stock_report($report_id,$category_id)
	{
		
	    $search_status = $this->session->userdata('income_statement_search');
	    $search_payments_add = '';
	    $search_invoice_add = '';
	    if($search_status == 1)
	    {
	        $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
	        // var_dump($stock_search);die();
	       
			if(!empty($stock_search) AND $stock_search == $report_id)
			{
		    	// $exploded = explode('#', $stock_search);
		    	 
		    	$export_report_id = $report_id;
				
		      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
				$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
				// var_dump($date_from);die();
				if(!empty($date_from) AND !empty($date_to))
				{
					$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
				}
		      
		  }
	      else
	      {
	      	$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
	      }
	      
	    }
	    else
	    {
	    	// $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
	    	 $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
	        // var_dump($stock_search);die();
	       
				if(!empty($stock_search) AND $stock_search == $report_id)
				{
			    	// $exploded = explode('#', $stock_search);
			    	 
			    	$export_report_id = $report_id;
					
			      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
					$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
					// var_dump($date_from);die();
					if(!empty($date_from) AND !empty($date_to))
					{
						$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
					}
					else if(!empty($date_from))
					{
						$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
					}
					else if(!empty($date_to))
					{
						$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
					}
			      
			  }
			else
			{
				$search_invoice_add = '';
			}

      
    	}

    	$this->load->library('excel');
    	if($report_id == 1)
    	{
    		$table = 'v_product_stock';
    		$where = '(v_product_stock.transactionClassification = "Product Opening Stock" OR  v_product_stock.transactionClassification = "Supplier Purchases") AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;
    		$report_title = 'Purchases Report';
    	}
    	else if($report_id == 2)
    	{
    		$table = 'v_product_stock';
    		$where = '(v_product_stock.transactionClassification = "Product Opening Stock" OR  v_product_stock.transactionClassification = "Supplier Purchases") AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;
    		$report_title = 'Purchases Report';
    	}

    	else if($report_id == 3)
    	{
    		$table = 'v_product_stock';
			$where = '(v_product_stock.transactionClassification = "Product Addition") AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;
    		$report_title = 'Other Additions Report';
    	}
    	else if($report_id == 4)
    	{
    		$table = 'v_product_stock';
    		$where = '(v_product_stock.transactionClassification = "Supplier Credit Note" ) AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;
    		$report_title = 'Return Outwards';
    	}
    	else if($report_id == 5)
    	{
    		$table = 'v_product_stock';
			$where = '(v_product_stock.transactionClassification = "Product Deductions" OR v_product_stock.transactionClassification = "Drug Transfer") AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;
    		$report_title = 'Other Deductions';
    	}

    	else if($report_id == 7)
    	{
    		$table = 'v_product_stock_non_pharm';
		    $where = '(v_product_stock_non_pharm.category_id < 2 OR v_product_stock_non_pharm.category_id > 3) AND v_product_stock_non_pharm.transactionClassification ="Product Addition" AND v_product_stock_non_pharm.category_id = '.$category_id.'   '.$search_invoice_add;


		    $this->db->where('category_id',$category_id);
		    $query_cat = $this->db->get('category');

		    $row = $query_cat->row();
		    $category_name = $row->category_name;


    		$report_title = $category_name;
    	}
    	$this->db->where($where);
		$this->db->order_by('transactionDate', 'ASC');
		$this->db->select('*');
		$visits_query = $this->db->get($table);

		$title = $report_title.' Export '.date('jS M Y',strtotime(date($date_from))).' - '.date('jS M Y',strtotime(date($date_to)));
		$col_count = 0;

		if($visits_query->num_rows() > 0)
		{
			$count = 0;
			
			$row_count = 0;
			$report[$row_count][$col_count] = '#';
			$col_count++;
			$report[$row_count][$col_count] = 'Transaction Date';
			$col_count++;
			$report[$row_count][$col_count] = 'Product';
			$col_count++;
			$report[$row_count][$col_count] = 'Affected Store';
			$col_count++;
			$report[$row_count][$col_count] = 'Description';
			$col_count++;
			$report[$row_count][$col_count] = 'Quantity';
			$col_count++;
			$report[$row_count][$col_count] = 'Value';
			$col_count++;
			$total_dr_quantity = 0;
			$total_dr_amount = 0;
			//display all patient data in the leftmost columns
			foreach($visits_query->result() as $row)
			{
				$row_count++;
				$total_invoiced = 0;			

				$transaction_id = $row->transactionId;
				$product_id = $row->product_id;
				$store_id = $row->store_id;
				$product_name = $row->product_name;
				$store_name = $row->store_name;
				$transactionDescription = $row->transactionDescription;
				$dr_quantity = $row->dr_quantity;
				$cr_quantity = $row->cr_quantity;
				$dr_amount = $row->dr_amount;
				$cr_amount = $row->cr_amount;
				$transactionDate = $row->transactionDate;
				$transactionDate = date('F j, Y', strtotime($transactionDate));

				$total_dr_quantity += $dr_quantity;
				$total_dr_amount += $dr_amount;

				$count++;
				$report[$row_count][$col_count] = $count;
				$col_count++;
				$report[$row_count][$col_count] = $transactionDate;
				$col_count++;
				$report[$row_count][$col_count] = $product_name;
				$col_count++;
				$report[$row_count][$col_count] = $store_name;
				$col_count++;
				$report[$row_count][$col_count] = $transactionDescription;
				$col_count++;
				$report[$row_count][$col_count] = $dr_quantity;
				$col_count++;
				$report[$row_count][$col_count] = number_format($dr_amount,2);
				$col_count++;

					
				
			}

		}

		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}


	public function export_current_stock_report($report_id,$category_id)
	{
		
	    $search_status = $this->session->userdata('income_statement_search');
	    $search_payments_add = '';
	    $search_invoice_add = '';
	    if($search_status == 1)
	    {
	        $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
	        // var_dump($stock_search);die();
	       
			if(!empty($stock_search) AND $stock_search == $report_id)
			{
		    	// $exploded = explode('#', $stock_search);
		    	 
		    	$export_report_id = $report_id;
				
		      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
				$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
				// var_dump($date_from);die();
				if(!empty($date_from) AND !empty($date_to))
				{
					$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
				}
		      
		  }
	      else
	      {
	      	$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
	      }
	      
	    }
	    else
	    {
	    	// $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
	    	 $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
	        // var_dump($stock_search);die();
	       
				if(!empty($stock_search) AND $stock_search == $report_id)
				{
			    	// $exploded = explode('#', $stock_search);
			    	 
			    	$export_report_id = $report_id;
					
			      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
					$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
					// var_dump($date_from);die();
					if(!empty($date_from) AND !empty($date_to))
					{
						$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
					}
					else if(!empty($date_from))
					{
						$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
					}
					else if(!empty($date_to))
					{
						$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
					}
			      
			  }
			else
			{
				$search_invoice_add = '';
			}

      
    	}

    	$this->load->library('excel');
    	
    	$table = 'v_product_stock';
    	$where = '(v_product_stock.transactionClassification = "Product Opening Stock" OR  v_product_stock.transactionClassification = "Supplier Purchases" OR  v_product_stock.transactionClassification = "Product Addition" OR v_product_stock.transactionClassification = "Supplier Credit Note" OR v_product_stock.transactionClassification = "Product Deductions" OR v_product_stock.transactionClassification = "Drug Sales" OR v_product_stock.transactionClassification = "Drug Transfer") AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;
    	$this->db->where($where);
		$this->db->order_by('transactionDate', 'ASC');
		$this->db->select('*');
		$visits_query = $this->db->get($table);

		$report_title = 'Current Stock';

		$title = $report_title.' Export '.date('jS M Y',strtotime(date($date_from))).' - '.date('jS M Y',strtotime(date($date_to)));
		$col_count = 0;

		if($visits_query->num_rows() > 0)
		{
			$count = 0;
			
			$row_count = 0;
			$report[$row_count][$col_count] = '#';
			$col_count++;
			$report[$row_count][$col_count] = 'Transaction Date';
			$col_count++;
			$report[$row_count][$col_count] = 'Product';
			$col_count++;
			$report[$row_count][$col_count] = 'Affected Store';
			$col_count++;
			$report[$row_count][$col_count] = 'Description';
			$col_count++;
			$report[$row_count][$col_count] = 'Dr QTY';
			$col_count++;
			$report[$row_count][$col_count] = 'Cr QTY';

			$col_count++;
			$report[$row_count][$col_count] = 'Dr Value';
			$col_count++;
			$report[$row_count][$col_count] = 'Cr Value';

			$col_count++;
			// $total_dr_quantity = 0;
			// $total_dr_amount = 0;

			$total_dr_quantity = 0;
			$total_dr_amount = 0;
			$total_cr_quantity = 0;
			$total_cr_amount = 0;
			//display all patient data in the leftmost columns
			foreach($visits_query->result() as $row)
			{
				$row_count++;
				$total_invoiced = 0;			

				$transaction_id = $row->transactionId;
				$product_id = $row->product_id;
				$store_id = $row->store_id;
				$product_name = $row->product_name;
				$store_name = $row->store_name;
				$transactionDescription = $row->transactionDescription;
				$dr_quantity = $row->dr_quantity;
				$cr_quantity = $row->cr_quantity;
				$dr_amount = $row->dr_amount;
				$cr_amount = $row->cr_amount;
				$transactionDate = $row->transactionDate;
				$transactionDate = date('F j, Y', strtotime($transactionDate));

				


				$total_dr_quantity += $dr_quantity;
				$total_dr_amount += $dr_amount;
				$total_cr_quantity += $cr_quantity;
				$total_cr_amount += $cr_amount;


				$count++;
				$report[$row_count][$col_count] = $count;
				$col_count++;
				$report[$row_count][$col_count] = $transactionDate;
				$col_count++;
				$report[$row_count][$col_count] = $product_name;
				$col_count++;
				$report[$row_count][$col_count] = $store_name;
				$col_count++;
				$report[$row_count][$col_count] = $transactionDescription;
				$col_count++;
				$report[$row_count][$col_count] = $dr_quantity;
				$col_count++;
				$report[$row_count][$col_count] = $cr_quantity;
				$col_count++;
				$report[$row_count][$col_count] = number_format($dr_amount,2);
				$col_count++;
				$report[$row_count][$col_count] = number_format($cr_amount,2);
				$col_count++;

					
				
			}

		}

		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

	public function get_all_stock_expense_statement_views($table, $where, $per_page, $page, $order = 'product_deductions_stock_date', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select("*");
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		// $this->db->join('personnel','personnel.personnel_id = visit_charge.provider_id','left');
		$query = $this->db->get('', $per_page, $page);

		return $query;
	}


	public function get_product_expense_balance_brought_forward()
	{

		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$start_date = date('Y-m-01', strtotime($date_from));
				$search_invoice_add =  '   transactionDate < \''.$start_date.'\' ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = '  transactionDate < \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = '  transactionDate < \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}
		// $table = 'v_product_stock';
		// $where = '(v_product_stock.transactionClassification = "Product Opening Stock" OR  v_product_stock.transactionClassification = "Supplier Purchases" OR  v_product_stock.transactionClassification = "Product Addition" OR v_product_stock.transactionClassification = "Supplier Credit Note" OR v_product_stock.transactionClassification = "Product Deductions" OR v_product_stock.transactionClassification = "Drug Sales")  '.$search_invoice_add;
		// $this->db->select('SUM(dr_amount) AS dr_amount, SUM(cr_amount)  AS cr_amount, SUM(dr_quantity) AS dr_quantity,SUM(cr_quantity) AS cr_quantity');


		$select_statement = "SELECT 
								SUM(data.dr_amount) AS dr_amount,
								SUM(data.cr_amount) AS cr_amount,
								SUM(data.dr_quantity) AS dr_quantity,
								SUM(data.cr_quantity) AS cr_quantity
							   	FROM 
								(
									SELECT
									`product_deductions_stock`.`product_deductions_stock_id` AS transactionId,
									`product_deductions_stock`.`product_id` AS `product_id`,
									`product`.`category_id` AS `category_id`,
									 product_deductions_stock.store_id AS `store_id`,
									 '' AS `receiving_store`,
									 product.product_name AS `product_name`,
									store.store_name AS `store_name`,
									 product_deductions_stock.deduction_description AS `transactionDescription`,
									 '0' AS `dr_quantity`,
									 (product_deductions_stock_quantity * product_deductions_stock_pack_size) AS  `cr_quantity`,
									'0' AS `dr_amount`,
									 (`product`.`product_unitprice` * (product_deductions_stock_quantity * product_deductions_stock_pack_size)) AS `cr_amount`,
									`product_deductions_stock`.`product_deductions_stock_date` AS `transactionDate`,
									`product`.`product_status` AS `status`,
									`product`.`product_deleted` AS `product_deleted`,
									`category`.`category_name` AS `transactionCategory`,
									'Product Deductions' AS `transactionClassification`,
									'product_deductions_stock' AS `transactionTable`,
									'product' AS `referenceTable`
									FROM (`product_deductions_stock`,product,store,category)
									WHERE product.product_id = product_deductions_stock.product_id AND product.product_deleted = 0
									AND store.store_id = product_deductions_stock.store_id
									AND product.category_id = category.category_id
									AND (product.category_id < 2 OR product.category_id > 2)
									AND store.store_parent > 0
								) AS data WHERE ".$search_invoice_add;;
		// $this->db->where($where);
		$query = $this->db->query($select_statement);



		return  $query;
	}


 public function get_unallocated_funds($creditor_id)
  {
    $selected_statement = "
                  SELECT 
                      SUM(cr_amount) AS cr_amount

                  FROM
                  (
                  SELECT
                  `creditor_payment_item`.`creditor_payment_item_id` AS `transactionId`,
                  `creditor_payment`.`creditor_payment_id` AS `referenceId`,
                  `creditor_payment`.`reference_number` AS `referenceCode`,
                  `creditor_payment_item`.`creditor_invoice_id` AS `payingFor`,
                  `creditor_payment`.`document_number` AS `transactionCode`,
                  '' AS `patient_id`,
                  `creditor_payment`.`creditor_id` AS `recepientId`,
                  `account`.`parent_account` AS `accountParentId`,
                  `account_type`.`account_type_name` AS `accountsclassfication`,
                  `creditor_payment`.`account_from_id` AS `accountId`,
                  `account`.`account_name` AS `accountName`,
                  `creditor_payment_item`.`description` AS `transactionName`,
                  CONCAT('Payment on account')  AS `transactionDescription`,
                      0 AS `department_id`,
                  0 AS `dr_amount`,
                  `creditor_payment_item`.`amount_paid` AS `cr_amount`,
                  `creditor_payment`.`transaction_date` AS `transactionDate`,
                  `creditor_payment`.`created` AS `createdAt`,
                  `creditor_payment`.`created` AS `referenceDate`,
                  `creditor_payment_item`.`creditor_payment_item_status` AS `status`,
                  'Expense Payment' AS `transactionCategory`,
                  'Creditors Invoices Payments' AS `transactionClassification`,
                  'creditor_payment' AS `transactionTable`,
                  'creditor_payment_item' AS `referenceTable`
                FROM
                  (
                    (
                      (
                        `creditor_payment_item`
                        JOIN `creditor_payment` ON(
                          (
                            creditor_payment.creditor_payment_id = creditor_payment_item.creditor_payment_id
                          )
                        )
                      )
                      JOIN account ON(
                        (
                          account.account_id = creditor_payment.account_from_id
                        )
                      )
                    )
                    JOIN `account_type` ON(
                      (
                        account_type.account_type_id = account.account_type_id
                      )
                    )
                    JOIN `creditor` ON(
                      (
                        creditor.creditor_id = creditor_payment_item.creditor_id
                      )
                    )
                  )
                  WHERE creditor_payment_item.invoice_type = 3) AS data WHERE data.recepientId = ".$creditor_id;
          $query = $this->db->query($selected_statement);
          $checked = $query->row();

          $dr_amount = $checked->cr_amount;

          return $dr_amount;
  	}

  	public function get_asset_categories()
	{
		$search_status = $this->session->userdata('balance_sheet_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			
		}
		else
		{
			$date_to = date('Y-m-d');

		}

		$select_sql = 'SELECT
						  asset_amortization.amortizationDate,
						  SUM(asset_amortization.endBalance) as asset_value,
						  assets_details.asset_description,
						  assets_details.asset_category_id,
						  asset_category.asset_category_name
						FROM
						  asset_amortization
						  LEFT JOIN assets_details
						    ON asset_amortization.asset_id = assets_details.asset_id
						  LEFT JOIN asset_category
						    ON assets_details.asset_category_id = asset_category.asset_category_id
						WHERE MONTH (
						    asset_amortization.amortizationDate
						  ) = MONTH("'.$date_to.'")
						  AND YEAR (
						    asset_amortization.amortizationDate
						  ) = YEAR("'.$date_to.'")
						GROUP BY assets_details.asset_category_id
						ORDER BY asset_category.asset_category_name ASC';
		$query = $this->db->query($select_sql);
		
		return $query;
	}

	public function get_creditor_amount_paid($creditor_invoice_id,$creditor_id)
	{
		 $this->db->where('creditor_payment.creditor_payment_id = creditor_payment_item.creditor_payment_id AND creditor_payment.creditor_payment_status = 1 AND creditor_payment_item.creditor_invoice_id ='.$creditor_invoice_id.' AND creditor_payment_item.creditor_id = '.$creditor_id);
		 $this->db->select('SUM(creditor_payment_item.amount_paid) AS total_paid');
		 $query = $this->db->get('creditor_payment,creditor_payment_item');


		 $total_paid = 0;
		 if($query->num_rows() > 0)
		 {
		  foreach ($query->result() as $key => $value) {
		    # code...
		    $total_paid = $value->total_paid;
		  }
		 }
		 return $total_paid;
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
						'account_opening_balance'=>$this->input->post('account_balance'),
						'start_date'=>$this->input->post('start_date')
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
	                    'account_status'=>$this->input->post('account_status'),
						'start_date'=>$this->input->post('start_date')
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
		 public function get_parent_accounts()		
		{
			//retrieve all users
			$this->db->from('account');
			$this->db->select('*');
			$this->db->where('parent_account = 0');
			$query = $this->db->get();
			
			return $query;    	
	 
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
}
?>
