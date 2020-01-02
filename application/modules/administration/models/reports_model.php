<?php

class Reports_model extends CI_Model 
{
	public function get_queue_total($date = NULL, $where = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		if($where == NULL)
		{
			$where = 'visit.visit_id = visit_department.visit_id AND visit.close_card = 0 AND visit.visit_date = \''.$date.'\'';
		}
		
		else
		{
			$where .= ' AND visit.visit_id = visit_department.visit_id AND visit.close_card = 0 AND visit.visit_date = \''.$date.'\' ';
		}
		
		$this->db->select('COUNT(visit.visit_id) AS queue_total');
		$this->db->where($where);
		$query = $this->db->get('visit, visit_department');
		
		$result = $query->row();
		
		return $result->queue_total;
	}
	


	public function get_daily_balance($date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		//select the user by email from the database
		$this->db->select('SUM(amount_paid) AS total_amount');
		$this->db->where('cancel = 0 AND payment_type = 1 AND payment_method_id = 2 AND payment_created = \''.$date.'\'');
		$this->db->from('payments');
		$query = $this->db->get();
		
		$result = $query->row();
		
		return $result->total_amount;
	}

	
	
	public function get_patients_total($date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$this->db->select('COUNT(visit_id) AS patients_total');
		$this->db->where('visit_date = \''.$date.'\'');
		$query = $this->db->get('visit');
		
		$result = $query->row();
		
		return $result->patients_total;
	}
	
	public function get_all_payment_methods()
	{
		$this->db->select('*');
		$query = $this->db->get('payment_method');
		
		return $query;
	}
	
	public function get_payment_method_total($payment_method_id, $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$this->db->select('SUM(amount_paid) AS total_paid');
		$this->db->where('payments.visit_id = visit.visit_id AND payment_method_id = '.$payment_method_id.' AND visit_date = \''.$date.'\'');
		$query = $this->db->get('payments, visit');
		
		$result = $query->row();
		
		return $result->total_paid;
	}
	
	public function get_all_visit_types()
	{
		$this->db->select('*');
		$query = $this->db->get('visit_type');
		
		return $query;
	}
	
	public function get_visit_type_total($visit_type_id, $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$where = 'visit_date = \''.$date.'\' AND visit_type = '.$visit_type_id;
		
		$this->db->select('COUNT(visit_id) AS visit_total');
		$this->db->where($where);
		$query = $this->db->get('visit');
		
		$result = $query->row();
		
		return $result->visit_total;
	}
	
	public function get_patient_type_total($where, $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$where = 'visit_date = \''.$date.'\' '.$where;
		
		$this->db->select('COUNT(visit_id) AS visit_total');
		$this->db->where($where);
		$query = $this->db->get('visit');
		
		$result = $query->row();
		
		return $result->visit_total;
	}
	
	public function get_all_service_types()
	{
		$this->db->select('*');
		$this->db->where('service_delete = 0 AND service_status = 1');
		$query = $this->db->get('service');
		
		return $query;
	}
	
	public function get_service_total($service_id, $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		
		$table = 'visit_charge, service_charge';
		
		$where = 'visit_charge_timestamp LIKE \''.$date.'%\' AND visit_charge.visit_charge_delete = 0 AND visit_charge.service_charge_id = service_charge.service_charge_id AND service_charge.service_id = '.$service_id;
		
		$visit_search = $this->session->userdata('all_departments_search');
		if(!empty($visit_search))
		{
			$where = 'visit_charge.service_charge_id = service_charge.service_charge_id AND visit_charge.visit_charge_delete = 0 AND service_charge.service_id = '.$service_id.' AND visit.visit_id = visit_charge.visit_id'. $visit_search;
			$table .= ', visit';
		}
		
		$this->db->select('SUM(visit_charge_units*visit_charge_amount) AS service_total');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		$result = $query->row();
		$total = $result->service_total;;
		
		if($total == NULL)
		{
			$total = 0;
		}
		
		return $total;
	}


	public function get_service_invoice_total($service_id, $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		
		$table = 'visit_charge, service_charge,visit';
		
		$where = 'visit.visit_id = visit_charge.visit_id AND visit.visit_date = "'.$date.'" AND visit_charge.visit_charge_delete = 0 AND visit.visit_delete = 0 AND visit_charge.service_charge_id = service_charge.service_charge_id AND service_charge.service_id = '.$service_id;
		
			
		$this->db->select('SUM(visit_charge_units*visit_charge_amount) AS service_total');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		$result = $query->row();
		$total = $result->service_total;;
		
		if($total == NULL)
		{
			$total = 0;
		}
		
		return $total;
	}
	

	public function get_service_payments_total($service_id, $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		
		$table = 'payments,visit';
		

		$where = 'visit.visit_id = payments.visit_id AND visit.visit_date = "'.$date.'" AND payments.cancel = 0 and payments.payment_type = 1 AND payments.payment_service_id = '.$service_id;
	
	
		$this->db->select('SUM(amount_paid) AS paid_amount');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		$result = $query->row();
		$total = $result->paid_amount;;
		
		if($total == NULL)
		{
			$total = 0;
		}
		
		return $total;
	}


	public function get_payments_total($service_id, $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		
		$table = 'payments';
		

		$where = 'payment_created = "'.$date.'" AND cancel = 0 and payment_type = 1 ';
	
	
		$this->db->select('SUM(amount_paid) AS paid_amount');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		$result = $query->row();
		$total = $result->paid_amount;;
		
		if($total == NULL)
		{
			$total = 0;
		}
		
		return $total;
	}

	public function get_waiver_payments_total($service_id, $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		
		$table = 'payments';
		

		$where = 'payment_created = "'.$date.'" AND cancel = 0 and payment_type = 2 AND payment_service_id = '.$service_id;
	
	
		$this->db->select('SUM(amount_paid) AS paid_amount');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		$result = $query->row();
		$total = $result->paid_amount;;
		
		if($total == NULL)
		{
			$total = 0;
		}
		
		return $total;
	}
	public function get_all_appointments($date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$where = 'visit.visit_delete = 0 AND patients.patient_delete = 0 AND visit.visit_type = visit_type.visit_type_id AND visit.patient_id = patients.patient_id AND visit.appointment_id = 1 AND visit.close_card = 2 AND visit.visit_date >= \''.$date.'\' AND visit.personnel_id = personnel.personnel_id';
		
		$this->db->select('visit.*, visit_type.visit_type_name, patients.*, personnel.*');
		$this->db->where($where);
		$query = $this->db->get('visit, visit_type, patients, personnel');
		
		return $query;
	}
	
	public function get_doctor_appointments($personnel_id, $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$where = 'visit.visit_delete = 0 AND patients.patient_delete = 0 AND visit.visit_type = visit_type.visit_type_id AND visit.patient_id = patients.patient_id AND visit.appointment_id = 1 AND visit.close_card = 2 AND visit.visit_date >= \''.$date.'\' AND visit.personnel_id = '.$personnel_id;
		
		$this->db->select('visit.*, visit_type.visit_type_name, patients.*');
		$this->db->where($where);
		$query = $this->db->get('visit, visit_type, patients');
		
		return $query;
	}
	
	public function get_all_sessions($date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$where = 'personnel.personnel_id = session.personnel_id AND session.session_name_id = session_name.session_name_id AND session_time LIKE \''.$date.'%\'';
		
		$this->db->select('session_name_name, session_time, personnel_fname, personnel_onames');
		$this->db->where($where);
		$this->db->order_by('session_time', 'DESC');
		$query = $this->db->get('session, session_name, personnel');
		
		return $query;
	}
	
	/*
	*	Retrieve visits
	*	@param string $table
	* 	@param string $where
	*	@param int $per_page
	* 	@param int $page
	*
	*/
	public function get_all_visits($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit.*, (visit.visit_time_out - visit.visit_time) AS waiting_time, patients.*, visit_type.visit_type_name');
		$this->db->where($where);
		$this->db->order_by('visit.visit_date','DESC');
		$this->db->group_by('visit.visit_id');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}


	public function get_all_visits_doctors($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit.*, (visit.visit_time_out - visit.visit_time) AS waiting_time, patients.*, visit_type.visit_type_name,visit_type.visit_type_id AS visit_type_idd,doctor_invoice.invoiced_amount,doctor_invoice.doctor_invoice_status,doctor_invoice.approved_by,personnel.personnel_fname,personnel.personnel_onames');
		$this->db->where($where);
		$this->db->order_by('visit.visit_date','DESC');
		$this->db->group_by('visit.visit_id');
		$this->db->join('doctor_invoice','visit.visit_id = doctor_invoice.visit_id','left');
		$this->db->join('personnel','visit.personnel_id = personnel.personnel_id','left');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	public function get_patient_invoiced_items($visit_id)
	{
		//retrieve all users
		$this->db->from('doctor_invoice');
		$this->db->where('visit_id = '.$visit_id);
		$query = $this->db->get();
		
		return $query;
	}

	public function get_visit_waiver($visit_id)
	{
		
		
		$table = 'payments';
		

		$where = 'cancel = 0 and payment_type = 2 AND visit_id = '.$visit_id;
	
	
		$this->db->select('SUM(amount_paid) AS paid_amount');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		$result = $query->row();
		$total = $result->paid_amount;;
		
		if($total == NULL)
		{
			$total = 0;
		}
		
		return $total;
	}
	public function get_all_visits_time($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit.*, (visit.visit_time_out - visit.visit_time) AS waiting_time, patients.*, visit_type.visit_type_name,patients.*,personnel.personnel_fname,personnel.personnel_onames');
		$this->db->where($where);
		$this->db->order_by('visit.visit_date','DESC');
		$this->db->group_by('visit.visit_id');
		$this->db->join('personnel','visit.personnel_id = personnel.personnel_id','left');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	/*
	*	Retrieve visits
	*	@param string $table
	* 	@param string $where
	*	@param int $per_page
	* 	@param int $page
	*
	*/
	public function get_all_visits_lab_work($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit.*, (visit.visit_time_out - visit.visit_time) AS waiting_time, patients.*, visit_type.visit_type_name,visit_lab_work.*');
		$this->db->where($where);
		$this->db->order_by('visit.visit_date','DESC');
		$this->db->group_by('visit.visit_id');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Retrieve all active services
	*
	*/
	public function get_all_active_services()
	{
		//retrieve all users
		$this->db->from('service');
		$this->db->where('service_delete = 0');
		$this->db->order_by('service_name','ASC');
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Retrieve all active branches
	*
	*/
	public function get_all_active_branches()
	{
		//retrieve all users
		$this->db->from('branch');
		$this->db->where('branch_status = 1');
		$this->db->order_by('branch_name','ASC');
		$query = $this->db->get();
		
		return $query;
	}
	/*
	*	Retrieve all active services
	*
	*/
	public function get_all_active_payment_method()
	{
		//retrieve all users
		$this->db->from('payment_method');
		$this->db->where('payment_method_id > 0');
		$this->db->order_by('payment_method_id','ASC');
		$query = $this->db->get();
		
		return $query;
	}
	
	
	/*
	*	Retrieve all visit payments
	*
	*/
	public function get_all_visit_payments($visit_id)
	{
		//retrieve all users
		$this->db->from('payments');
		$this->db->select('SUM(payments.amount_paid) AS total_paid');
		$this->db->where('visit_id', $visit_id);
		// $this->db->group_by('visit_id');
		$query = $this->db->get();
		
		$cash = $query->row();
		
		if($cash->total_paid > 0)
		{
			return $cash->total_paid;
		}
		
		else
		{
			return 0;
		}
	}
	
	/*
	*	Retrieve all service charges
	*
	*/
	public function get_all_visit_charges($visit_id, $service_id)
	{
		//retrieve all users
		$this->db->from('visit_charge, service_charge');
		$this->db->select('SUM(visit_charge.visit_charge_amount * visit_charge.visit_charge_units) AS total_invoiced');
		$this->db->where('visit_charge.visit_id = '.$visit_id.' AND service_charge.service_id = '.$service_id.' AND visit_charge.service_charge_id = service_charge.service_charge_id AND visit_charge.visit_charge_delete = 0');
		$query = $this->db->get();
		
		$cash = $query->row();
		
		if($cash->total_invoiced > 0)
		{
			return $cash->total_invoiced;
		}
		
		else
		{
			return 0;
		}
	}
	
	public function get_service_notes($visit_id, $service_id, $payment_type)
	{
		//retrieve all users
		$this->db->from('payments');
		$this->db->select('SUM(amount_paid) AS total_invoiced');
		$this->db->where('payments.visit_id = '.$visit_id.' AND payments.payment_service_id = '.$service_id.' AND payments.payment_type = '.$payment_type);
		$query = $this->db->get();
		
		$cash = $query->row();
		
		if($cash->total_invoiced > 0)
		{
			return $cash->total_invoiced;
		}
		
		else
		{
			return 0;
		}
	}
	
	public function get_all_payment_values($visit_id,$payment_method_id)
	{
		# code...
		//retrieve all users
		$this->db->from('payments');
		$this->db->select('SUM(amount_paid) AS total_paid');
		$this->db->where('payments.cancel = 0 AND visit_id = '.$visit_id.' AND payment_method_id = '.$payment_method_id.' AND payment_type = 1');
		$query = $this->db->get();
		
		$cash = $query->row();
		
		if($cash->total_paid > 0)
		{
			return $cash->total_paid;
		}
		
		else
		{
			return 0;
		}
	}
	/*
	*	Retrieve total revenue
	*
	*/
	public function get_total_services_revenue($where, $table)
	{
		//invoiced
		$this->db->from($table.', visit_charge');
		$this->db->select('SUM(visit_charge.visit_charge_amount * visit_charge.visit_charge_units) AS total_invoiced');
		$this->db->where($where.' AND visit.visit_id = visit_charge.visit_id AND visit_charge.visit_charge_delete = 0 AND visit_charge.charged = 1 AND visit.visit_delete = 0');
		$query = $this->db->get();
		
		$cash = $query->row();
		$total_invoiced = $cash->total_invoiced;
		
		if($total_invoiced > 0)
		{
			
		}
		
		else
		{
			$total_invoiced = 0;
		}
		
		return $total_invoiced;
	}

	public function get_total_rejected_revenue($where, $table)
	{
		//invoiced
		$this->db->from($table);
		$this->db->select('SUM(rejected_amount) AS total_rejected');
		$this->db->where($where);
		$query = $this->db->get();
		
		$cash = $query->row();
		$total_rejected = $cash->total_rejected;
		
		if($total_rejected > 0)
		{
			
		}
		
		else
		{
			$total_rejected = 0;
		}
		
		return $total_rejected;
	}
	
	/*
	*	Retrieve total revenue
	*
	*/
	public function get_total_cash_collection($where, $table, $page = NULL)
	{
		//payments
		$table_search = $this->session->userdata('all_transactions_tables');
		
		if($page != 'cash')
		{
			$where .= ' AND visit.visit_id = payments.visit_id AND payments.cancel = 0';
		}
		if((!empty($table_search)) || ($page == 'cash'))
		{
			$this->db->from($table);
		}
		
		else
		{
			$this->db->from($table.', payments');
		}
		$this->db->select('SUM(payments.amount_paid) AS total_paid');
		$this->db->where($where);
		$query = $this->db->get();
		
		$cash = $query->row();
		$total_paid = $cash->total_paid;
		if($total_paid > 0)
		{
		}
		
		else
		{
			$total_paid = 0;
		}
		
		return $total_paid;
	}
	
	/*
	*	Retrieve total revenue
	*
	*/
	public function get_normal_payments($where, $table, $page = NULL)
	{
		if($page != 'cash')
		{
			$where .= ' AND visit.visit_id = payments.visit_id AND payments.cancel = 0';
		}
		//payments
		$table_search = $this->session->userdata('all_transactions_tables');
		if((!empty($table_search)) || ($page == 'cash'))
		{
			$this->db->from($table);
		}
		
		else
		{
			$this->db->from($table.', payments');
		}
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get();
		
		return $query;
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
	function export_transactions()
	{
		$this->load->library('excel');
		
		//get all transactions
		$branch_code = $this->session->userdata('search_branch_code');
		
		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.branch_code = \''.$branch_code.'\'';
		$table = 'visit, patients, visit_type';
		$visit_search = $this->session->userdata('all_transactions_search');
		$table_search = $this->session->userdata('all_transactions_tables');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			if(!empty($table_search))
			{
				$table .= $table_search;
			}
		}
		
		$this->db->where($where);
		$this->db->order_by('visit_date', 'ASC');
		$this->db->select('visit.*, patients.visit_type_id, patients.*, visit_type.visit_type_name');
		$this->db->group_by('visit_id');
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
			$services_query = $this->get_all_active_services();
			
			foreach($services_query->result() as $service)
			{
				$report[$row_count][$current_column] = $service->service_name;
				$current_column++;
			}
			/*$report[$row_count][$current_column] = 'Debit Note Total';
			$current_column++;
			$report[$row_count][$current_column] = 'Credit Note Total';
			$current_column++;*/
			$report[$row_count][$current_column] = 'Invoice Total';
			$current_column++;
			
			//get & display all services
			$payment_method_query = $this->get_all_active_payment_method();
			
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
				$coming_from = $this->reception_model->coming_from($visit_id);
				$sent_to = $this->reception_model->going_to($visit_id);
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;

				// this is to check for any credit note or debit notes
				$payments_value = $this->accounts_model->total_payments($visit_id);

				$invoice_total = $this->accounts_model->total_invoice($visit_id);

				$balance = $this->accounts_model->balance($payments_value,$invoice_total);
				// end of the debit and credit notes

				// total of debit and credit notes amounts
				$credit_note_amount = $this->accounts_model->get_sum_credit_notes($visit_id);
				$debit_note_amount = $this->accounts_model->get_sum_debit_notes($visit_id);
				// end of total debit and credit notes amount

				// get all the payment methods used in payments
				//$payment_type = $this->accounts_model->get_visit_payment_method($visit_id);
				// end of all payments details
				
				//creators and editors
				$personnel_query = $this->personnel_model->get_all_personnel();
				if($personnel_query->num_rows() > 0)
				{
					$personnel_result = $personnel_query->result();
					
					foreach($personnel_result as $adm)
					{
						$personnel_id2 = $adm->personnel_id;
						
						if($personnel_id == $personnel_id2)
						{
							$doctor = $adm->personnel_onames.' '.$adm->personnel_fname;
							break;
						}
						
						else
						{
							$doctor = '-';
						}
					}
				}
				
				else
				{
					$doctor = '-';
				}
				
				$count++;
				$cash = $this->reports_model->get_all_visit_payments($visit_id);
				
				//display services charged to patient
				$total_invoiced2 = 0;
				foreach($services_query->result() as $service)
				{
					$service_id = $service->service_id;
					$visit_charge = $this->reports_model->get_all_visit_charges($visit_id, $service_id);
					$total_invoiced2 += $visit_charge;
				}
				
				//display all debtors
				$debtors = $this->session->userdata('debtors');
				// if($debtors == 'true' && (($cash - $total_invoiced2) > 0))
				if($debtors == 'true' && ($balance > 0))
				{
					$col_count = 0;
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
					$report[$row_count][$current_column] = $total_invoiced;
					$current_column++;
					// display amounts collected on every payment method
					foreach($payment_method_query->result() as $paymentmethod)
					{
						$payment_method_id = $paymentmethod->payment_method_id;
						$amount_paid = $this->reports_model->get_all_payment_values($visit_id, $payment_method_id);
						$report[$row_count][$current_column] = $amount_paid;
						$current_column++;
					}
					// //display total for the current visit

					$report[$row_count][$current_column] = $payments_value;
					$current_column++;
					$report[$row_count][$current_column] = $balance;
					$current_column++;
				}
				
				//display cash & all transactions
				else
				{
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
					$report[$row_count][$current_column] = $invoice_total;
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
					$report[$row_count][$current_column] = $balance;
					$current_column++;
				}
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	
	/*
	*	Export Time report
	*
	*/
	function export_time_report()
	{
		$this->load->library('excel');
		
		//get all transactions
		$where = 'visit.patient_id = patients.patient_id AND visit.close_card = 1';
		$table = 'visit, patients';
		$visit_search = $this->session->userdata('time_reports_search');
		$table_search = $this->session->userdata('time_reports_tables');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			if(!empty($table_search))
			{
				$table .= $table_search;
			}
		}
		
		$this->db->where($where);
		$this->db->order_by('visit_date', 'ASC');
		$this->db->select('visit.*, patients.visit_type_id, patients.visit_type_id, patients.patient_othernames, patients.patient_surname, patients.dependant_id, patients.strath_no,patients.patient_national_id,patients.dependant_id');
		$visits_query = $this->db->get($table);
		
		$title = 'Time report Export';
		
		if($visits_query->num_rows() > 0)
		{
			$count = 0;
			/*
				-----------------------------------------------------------------------------------------
				Document Header
				-----------------------------------------------------------------------------------------
			*/

			$row_count = 0;
			$report[$row_count][0] = '#';
			$report[$row_count][1] = 'Visit Date';
			$report[$row_count][2] = 'Patient';
			$report[$row_count][3] = 'Category';
			$report[$row_count][4] = 'Start Time';
			$report[$row_count][5] = 'End time';
			$report[$row_count][6] = 'Total Time (Days h:m:s)';
			//get & display all services
			
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
					$seconds = strtotime($row->visit_time_out) - strtotime($row->visit_time);//$row->waiting_time;
					$days    = floor($seconds / 86400);
					$hours   = floor(($seconds - ($days * 86400)) / 3600);
					$minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
					$seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));
					
					//$total_time = date('H:i',(strtotime($row->visit_time_out) - strtotime($row->visit_time)));//date('H:i',$row->waiting_time);
					$total_time = $days.' '.$hours.':'.$minutes.':'.$seconds;
				}
				else
				{
					$visit_time_out = '-';
					$total_time = '-';
				}
					
				$visit_id = $row->visit_id;
				$patient_id = $row->patient_id;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				
				$patient = $this->reception_model->patient_names2($patient_id, $visit_id);
				$visit_type = $patient['visit_type'];
				$patient_type = $patient['patient_type'];
				$patient_othernames = $patient['patient_othernames'];
				$patient_surname = $patient['patient_surname'];
				$patient_date_of_birth = $patient['patient_date_of_birth'];
				$gender = $patient['gender'];
				$faculty = $patient['faculty'];
				$count++;
				
				//display the patient data
				$report[$row_count][0] = $count;
				$report[$row_count][1] = $visit_date;
				$report[$row_count][2] = $patient_surname.' '.$patient_othernames;
				$report[$row_count][3] = $visit_type;
				$report[$row_count][4] = $visit_time;
				$report[$row_count][5] = $visit_time_out;
				$report[$row_count][6] = $total_time;
					
				
				
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	
	/*
	*	Retrieve total revenue
	*
	*/
	public function get_visit_departments($where, $table)
	{
		//invoiced
		$this->db->from($table.', visit_department');
		$this->db->select('visit_department.*');
		$this->db->where($where.' AND visit.visit_id = visit_department.visit_id');
		$query = $this->db->get();
		
		return $query;
	}


	public function get_insurance_company()
	{
		//invoiced
		$this->db->from('insurance_company');
		$this->db->select('*');
		$this->db->order_by('insurance_company_name');
		$query = $this->db->get();
		
		return $query;
	}
	
	public function calculate_debt_total($debtor_invoice_id, $where, $table)
	{
		$where .= ' AND debtor_invoice.debtor_invoice_id = '.$debtor_invoice_id;
		
		$total_services_revenue = $this->reports_model->get_total_services_revenue($where, $table);
		
		$where2 = $where.' AND payments.payment_type = 1 AND payment_method_id < 9';
		$total_cash_collection = $this->reports_model->get_total_cash_collection($where2, $table);

		$where2 = $where.' AND payments.payment_type = 1 AND payment_method_id = 9';
		$total_insurance_collection = $this->reports_model->get_total_cash_collection($where2, $table);

		$where3 = $where.' AND payments.payment_type = 2';
		$total_waiver_collection = $this->reports_model->get_total_cash_collection($where3, $table);


		$where4 = $where.' AND debtor_invoice.debtor_invoice_id = '.$debtor_invoice_id;
		
		$total_rejected_collection = $this->reports_model->get_total_rejected_revenue($where4, $table);
		$cash_balance = 0;

        if(!empty($total_rejected_collection))
        {
            $cash_balance = $total_cash_collection - $total_rejected_collection;
        }


        $total_services_revenue -= $total_insurance_collection + $total_cash_collection;
        // var_dump($total_insurance_collection+$total_cash_collection); die();
		return $total_services_revenue - $total_rejected_collection - $total_waiver_collection;
	}
	
	public function get_debtor_invoice($where, $table)
	{
		$this->db->where($where);
		$query = $this->db->get($table);
		
		return $query;
	}


	public function get_all_doctors()
	{
		$this->db->select('personnel.*');
		$this->db->where('personnel.personnel_id = personnel_job.personnel_id AND personnel_job.job_title_id = job_title.job_title_id AND job_title.job_title_name = "Dentist" ');
		$this->db->order_by('personnel_fname');
		$query = $this->db->get('personnel,personnel_job,job_title');
		
		return $query;
	}

	public function get_total_collected($doctor_id, $date_from = NULL, $date_to = NULL,$visit_type_id = NULL)
	{
		if($visit_type_id == 1)
		{
			$add = ' AND visit.visit_type = 1';
		}
		else
		{
			$add = ' AND visit.visit_type > 1';
		}
		$table = 'visit_charge, visit';
		
		$where = 'visit_charge.visit_id = visit.visit_id AND visit.visit_delete = 0  AND visit_charge.visit_charge_delete = 0 AND visit.personnel_id = '.$doctor_id.$add;
		
		$visit_search = $this->session->userdata('all_doctors_search');
		if(!empty($visit_search))
		{
			$where = 'visit_charge.visit_id = visit.visit_id AND visit.visit_delete = 0 AND visit_charge.visit_charge_delete = 0 AND visit.personnel_id = '.$doctor_id.' '. $visit_search;
		}
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (visit.visit_date >= \''.$date_from.'\' AND visit.visit_date <= \''.$date_to.'\') ';
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$where .= ' AND visit_date LIKE \''.$date_to.'\'';
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$where .= ' AND visit_date LIKE \''.$date_from.'\'';
		}
		
		$this->db->select('SUM(visit_charge_units*visit_charge_amount) AS service_total');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		// $result = $query->row();
		// $total = $result[0]->service_total;
		
		if($query->num_rows() > 0)
		{

			foreach ($query->result() as $key):
				# code...
				$total = $key->service_total;

				if(!is_numeric($total))
				{
					return 0;
				}
				else
				{
					return $total;
				}
			endforeach;
		}
		else
		{
			return 0;
		}
		
	}

	public function get_total_collected_invoice($doctor_id, $date_from = NULL, $date_to = NULL)
	{
		$table = 'visit_charge, visit';
		
		$where = 'visit_charge.visit_id = visit.visit_id AND visit.visit_delete = 0 AND visit.visit_type >= 2 AND visit_charge.visit_charge_delete = 0 AND visit.personnel_id = '.$doctor_id;
		
		$visit_search = $this->session->userdata('all_doctors_search');
		if(!empty($visit_search))
		{
			$where = 'visit_charge.visit_id = visit.visit_id AND visit.visit_delete = 0 AND visit_charge.visit_charge_delete = 0 AND visit.personnel_id = '.$doctor_id.' '. $visit_search;
		}
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (visit.visit_date >= \''.$date_from.'\' AND visit.visit_date <= \''.$date_to.'\') ';
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$where .= ' AND visit_date LIKE \''.$date_to.'\'';
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$where .= ' AND visit_date LIKE \''.$date_from.'\'';
		}
		
		$this->db->select('SUM(visit_charge_units*visit_charge_amount) AS service_total');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		// $result = $query->row();
		// $total = $result[0]->service_total;
		
		if($query->num_rows() > 0)
		{

			foreach ($query->result() as $key):
				# code...
				$total = $key->service_total;

				if(!is_numeric($total))
				{
					return 0;
				}
				else
				{
					return $total;
				}
			endforeach;
		}
		else
		{
			return 0;
		}
		
	}

	public function get_total_collected_invoice_total($doctor_id, $date_from = NULL, $date_to = NULL)
	{
		$table = 'visit_charge, visit';
		
		$where = 'visit_charge.visit_id = visit.visit_id AND visit.visit_delete = 0 AND visit.visit_type <> 2 AND visit_charge.visit_charge_delete = 0 AND visit.personnel_id = '.$doctor_id;
		
		$visit_search = $this->session->userdata('all_doctors_search');
		if(!empty($visit_search))
		{
			$where = 'visit_charge.visit_id = visit.visit_id AND visit.visit_delete = 0 AND visit_charge.visit_charge_delete = 0 AND visit.personnel_id = '.$doctor_id.' '. $visit_search;
		}
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (visit.visit_date >= \''.$date_from.'\' AND visit.visit_date <= \''.$date_to.'\') ';
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$where .= ' AND visit_date LIKE \''.$date_to.'\'';
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$where .= ' AND visit_date LIKE \''.$date_from.'\'';
		}
		
		$this->db->select('SUM(visit_charge_units*visit_charge_amount) AS service_total');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		// $result = $query->row();
		// $total = $result[0]->service_total;
		
		if($query->num_rows() > 0)
		{

			foreach ($query->result() as $key):
				# code...
				$total = $key->service_total;

				if(!is_numeric($total))
				{
					return 0;
				}
				else
				{
					return $total;
				}
			endforeach;
		}
		else
		{
			return 0;
		}
		
	}


	public function get_total_waivers($doctor_id, $date_from = NULL, $date_to = NULL)
	{
		$table = 'payments, visit';
		
		$where = 'payments.visit_id = visit.visit_id AND visit.visit_delete = 0 AND payments.cancel = 0 and payments.payment_type = 2 AND visit.personnel_id = '.$doctor_id;
		
		
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (visit.visit_date >= \''.$date_from.'\' AND visit.visit_date <= \''.$date_to.'\') ';
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$where .= ' AND visit.visit_date LIKE \''.$date_to.'\'';
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$where .= ' AND visit.visit_date LIKE \''.$date_from.'\'';
		}
		
		$this->db->select('SUM(amount_paid) AS service_total');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		// $result = $query->row();
		// $total = $result[0]->service_total;
		
		if($query->num_rows() > 0)
		{

			foreach ($query->result() as $key):
				# code...
				$total = $key->service_total;

				if(!is_numeric($total))
				{
					return 0;
				}
				else
				{
					return $total;
				}
			endforeach;
		}
		else
		{
			return 0;
		}
		
	}

	public function get_total_payments_made($doctor_id, $date_from = NULL, $date_to = NULL)
	{
		$table = 'payments, visit';
		
		$where = 'payments.visit_id = visit.visit_id AND visit.visit_delete = 0 AND payments.cancel = 0 and payments.payment_type = 1 AND visit.personnel_id = '.$doctor_id;
		
		
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (visit.visit_date >= \''.$date_from.'\' AND visit.visit_date <= \''.$date_to.'\') ';
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$where .= ' AND visit.visit_date LIKE \''.$date_to.'\'';
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$where .= ' AND visit.visit_date LIKE \''.$date_from.'\'';
		}
		
		$this->db->select('SUM(amount_paid) AS service_total');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		// $result = $query->row();
		// $total = $result[0]->service_total;
		
		if($query->num_rows() > 0)
		{

			foreach ($query->result() as $key):
				# code...
				$total = $key->service_total;

				if(!is_numeric($total))
				{
					return 0;
				}
				else
				{
					return $total;
				}
			endforeach;
		}
		else
		{
			return 0;
		}
		
	}

	public function get_total_patients($doctor_id, $date_from = NULL, $date_to = NULL,$revisit_status = NULL)
	{
		$table = 'visit';
		
		$where = 'visit.visit_delete = 0 AND close_card <> 2 AND visit.personnel_id = '.$doctor_id;
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (visit_date >= \''.$date_from.'\' AND visit_date <= \''.$date_to.'\') ';
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$where .= ' AND visit_date = \''.$date_to.'\'';
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$where .= ' AND visit_date = \''.$date_from.'\'';
		}

		if($revisit_status == 1)
		{
			$where .= ' AND (revisit = 1 OR revisit = 0)';
		}
		else
		{
			$where .= ' AND revisit = 2';
		}

		
		$this->db->where($where);
		$total = $this->db->count_all_results('visit');
		
		return $total;
	}

	/*
	*	Export Time report
	*
	*/
	function doctor_reports_export($date_from = NULL, $date_to = NULL)
	{
		$this->load->library('excel');
		$report = array();
		
		//export title
		if(!empty($date_from) && !empty($date_to))
		{
			$title = 'Doctors report from '.date('jS M Y',strtotime($date_from)).' to '.date('jS M Y',strtotime($date_to));
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$title = 'Doctors report for '.date('jS M Y',strtotime($date_to));
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$title = 'Doctors report for '.date('jS M Y',strtotime($date_from));
		}
		
		else
		{
			$date_from = date('Y-m-d');
			$title = 'Doctors report for '.date('jS M Y',strtotime($date_from));
		}
		
		//document ehader
		$row_count = 0;
		$report[$row_count][0] = '#';
		$report[$row_count][1] = 'Doctor\'s name';
		$report[$row_count][2] = 'New Patients';
		$report[$row_count][3] = 'Revisits';
		$report[$row_count][4] = 'Total Patients';
		$report[$row_count][5] = 'Cash Invoices';
		$report[$row_count][6] = 'Insurance Invoices';
		$report[$row_count][7] = 'Total Invoices';
		
		//get all doctors
		$doctor_results = $this->reports_model->get_all_doctors();
		$result = $doctor_results->result();
		$grand_total = 0;
		$patients_total = 0;
		$count = 0;
		$grand_total = 0;
		$patients_total = 0;
		$insurance_grand = 0;
		$total_revisits = 0;
		$total_new = 0;
		foreach($result as $res)
		{
			$personnel_id = $res->personnel_id;
			$personnel_onames = $res->personnel_onames;
			$personnel_fname = $res->personnel_fname;
			$count++;
			$row_count++;
			
			//get service total
			$total = $this->reports_model->get_total_collected($personnel_id, $date_from, $date_to,1);
			$total_insurance = $this->reports_model->get_total_collected($personnel_id, $date_from, $date_to,2);
			
			$new = $this->reports_model->get_total_patients($personnel_id, $date_from, $date_to,1);
			$revisit = $this->reports_model->get_total_patients($personnel_id, $date_from, $date_to,2);
			$patients = $new+$revisit;
			$grand_total += $total;
			$patients_total += $patients;
			$insurance_grand = $total_insurance;
			$total_new += $new;
			$total_revisits += $revisit;


			$report[$row_count][0] = $count;
			$report[$row_count][1] = $personnel_fname.' '.$personnel_onames;
			$report[$row_count][2] = $new;
			$report[$row_count][3] = $revisit;
			$report[$row_count][4] = $patients;
			$report[$row_count][5] = number_format($total, 0);
			$report[$row_count][6] = number_format($total_insurance, 0);
			$report[$row_count][7] = number_format($total+$total_insurance, 0);
		}
		$row_count++;
		
		$report[$row_count][0] = '';
		$report[$row_count][1] = '';
		$report[$row_count][2] = number_format($total_new, 0);
		$report[$row_count][3] = $total_revisits;
		$report[$row_count][4] = $patients_total;
		$report[$row_count][5] = $grand_total;
		$report[$row_count][6] = $insurance_grand;
		$report[$row_count][7] = $grand_total + $insurance_grand;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	
	function doctor_patients_export($personnel_id, $date_from = NULL, $date_to = NULL)
	{
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 AND visit.close_card <> 2 AND visit.personnel_id = '.$personnel_id;
		$table = 'visit, patients, visit_type';
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (visit_date >= \''.$date_from.'\' AND visit_date <= \''.$date_to.'\') ';
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$where .= ' AND visit_date = \''.$date_to.'\'';
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$where .= ' AND visit_date = \''.$date_from.'\'';
		}
		$_SESSION['all_transactions_search'] = $where;
		
		
		$this->db->where($where);
		$this->db->join('personnel','personnel.personnel_id = visit.personnel_id','LEFT');
		$visits_query = $this->db->get($table);

		$title = 'Doctors Report '.date('jS M Y',strtotime($date_from)).' '.date('jS M Y',strtotime($date_to));
		$col_count = 0;
		$this->load->library('excel');
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
				$rejected_amount = 0;//$row->amount_rejected;
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
						// $s++;
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
	public function calculate_hours_worked($personnel_id, $date_from, $date_to)
	{
		$where = 'personnel_id = '.$personnel_id;
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (schedule_date >= \''.$date_from.'\' AND schedule_date <= \''.$date_to.'\') ';
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$where .= ' AND schedule_date = \''.$date_to.'\'';
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$where .= ' AND schedule_date = \''.$date_from.'\'';
		}
		
		$this->db->where($where);
		$query = $this->db->get('schedule_item');
		$total_hours = 0;
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $res)
			{
				$schedule_start_time = $res->schedule_start_time;
				$schedule_end_time = $res->schedule_end_time;
				
				$hours_difference = (strtotime($schedule_end_time) - strtotime($schedule_start_time)) / 3600;
				$total_hours += $hours_difference;
			}
		}
		
		return $total_hours;
	}
	
	public function calculate_days_worked($personnel_id, $date_from, $date_to)
	{
		$where = 'personnel_id = '.$personnel_id;
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (schedule_date >= \''.$date_from.'\' AND schedule_date <= \''.$date_to.'\') ';
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$where .= ' AND schedule_date = \''.$date_to.'\'';
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$where .= ' AND schedule_date = \''.$date_from.'\'';
		}
		
		$this->db->where($where);
		$query = $this->db->get('schedule_item');
		$total_days = $query->num_rows();
		
		return $total_days;
	}
	
	public function get_visit_type()
	{
		//invoiced
		$this->db->select('*');
		$this->db->from('visit_type');
		$this->db->where('visit_type_id > 1');
		$this->db->order_by('visit_type_name');
		$query = $this->db->get();
		
		return $query;
	}
	/*
	*	Retrieve total visits
	*
	*/
	public function get_total_visits($where, $table)
	{
		$this->db->from($table);
		$this->db->where($where);
		$total = $this->db->count_all_results();
		
		return $total;
	}
	
	/*
	*	Retrieve debtors_invoices
	*	@param string $table
	* 	@param string $where
	*	@param int $per_page
	* 	@param int $page
	*
	*/
	public function get_all_debtors_invoices($table, $where, $per_page, $page, $order, $order_method)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	public function add_debtor_invoice($visit_type_id)
	{
		$data = array(
			'debtor_invoice_created'=>date('Y-m-d H:i:s'),
			'debtor_invoice_created_by'=>$this->session->userdata('personnel_id'),
			'batch_no'=>$this->create_batch_number(),
			'visit_type_id'=>$visit_type_id,
			'debtor_invoice_modified_by'=>$this->session->userdata('personnel_id'),
			'date_from' => $this->input->post('invoice_date_from'),
			'date_to' => $this->input->post('invoice_date_to')
		);
		
		if($this->db->insert('debtor_invoice', $data))
		{
			$debtor_invoice_id = $this->db->insert_id();
			
			if($debtor_invoice_id > 0)
			{
				//get all invoices within the selected dates
				$this->db->where(
					array(
					
						'visit_delete' => 0,
						'visit_type' => $visit_type_id,
						'visit_date >= ' => $this->input->post('invoice_date_from'),
						'visit_date <= ' => $this->input->post('invoice_date_to')
					)
				);
				$this->db->select('visit_id');
				$query = $this->db->get('visit');
				
				if($query->num_rows() > 0)
				{
					$invoice_data['debtor_invoice_id'] = $debtor_invoice_id;
					
					foreach($query->result() as $res)
					{
						$visit_id = $res->visit_id;
						
						$invoice_data['visit_id'] = $visit_id;
						
						if($this->db->insert('debtor_invoice_item', $invoice_data))
						{
						}
						
						else
						{
							$this->session->set_userdata('error_message', 'Unable to add details for visit ID '.$visit_id);
						}
					}
					$this->session->set_userdata('success_message', 'Batch added successfully');
					return TRUE;
				}
				
				else
				{
					$this->session->set_userdata('error_message', 'The selected date range does not contain any invoices');
					return FALSE;
				}
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'The selected date range does not contain any invoices');
				return FALSE;
			}
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Create batch number
	*
	*/
	public function create_batch_number()
	{
		//select product code
		$this->db->from('debtor_invoice');
		$this->db->where("batch_no LIKE '".$this->session->userdata('branch_code').'-'.date('y')."-%'");
		$this->db->select('MAX(batch_no) AS number');
		$query = $this->db->get();
		$preffix = $this->session->userdata('branch_code').'-'.date('y').'-';
		
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;
			$real_number = str_replace($preffix, "", $number);
			$real_number++;//go to the next number
			$number = $preffix.sprintf('%06d', $real_number);
		}
		else{//start generating receipt numbers
			$number = $preffix.sprintf('%06d', 1);
		}
		
		return $number;
	}
	
	/*
	*	Retrieve visits
	*	@param string $table
	* 	@param string $where
	*	@param int $per_page
	* 	@param int $page
	*
	*/
	public function get_all_payments($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit.*, (visit.visit_time_out - visit.visit_time) AS waiting_time, patients.*, visit_type.visit_type_name, payments.*, payment_method.*, personnel.personnel_fname, personnel.personnel_onames, service.service_name');
		$this->db->join('personnel', 'payments.payment_created_by = personnel.personnel_id', 'left');
		$this->db->join('service', 'payments.payment_service_id = service.service_id', 'left');
		$this->db->where($where);
		$this->db->order_by('payments.time','DESC');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Export Transactions
	*
	*/
	function export_cash_report()
	{
		$this->load->library('excel');
		
		$branch_code = $this->session->userdata('search_branch_code');
		
		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}
		
		$this->db->where('branch_code', $branch_code);
		$query = $this->db->get('branch');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$branch_name = $row->branch_name;
		}
		
		else
		{
			$branch_name = '';
		}
		$v_data['branch_name'] = $branch_name;
		
		$where = 'payments.payment_method_id = payment_method.payment_method_id AND payments.visit_id = visit.visit_id AND payments.payment_type = 1 AND visit.visit_delete = 0  AND visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND payments.cancel = 0';
		
		$table = 'payments, visit, patients, visit_type, payment_method';


		$visit_search = $this->session->userdata('visit_payments');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		}
		
		$this->db->select('visit.*, (visit.visit_time_out - visit.visit_time) AS waiting_time, patients.*, visit_type.visit_type_name, payments.*, payment_method.*, personnel.personnel_fname, personnel.personnel_onames, service.service_name');
		$this->db->join('personnel', 'payments.payment_created_by = personnel.personnel_id', 'left');
		$this->db->join('service', 'payments.payment_service_id = service.service_id', 'left');
		$this->db->where($where);
		$this->db->order_by('payments.time','DESC');
		$query = $this->db->get($table);
		
		$title = 'Cash report '.date('jS M Y H:i a',strtotime(date('Y-m-d H:i:s')));
		$col_count = 0;
		
		if($query->num_rows() > 0)
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
			$report[$row_count][$col_count] = 'Payment Date';
			$col_count++;
			$report[$row_count][$col_count] = 'Time recorded';
			$col_count++;
			$report[$row_count][$col_count] = 'Patient Number';
			$col_count++;
			$report[$row_count][$col_count] = 'Patient';
			$col_count++;
			$report[$row_count][$col_count] = 'Category';
			$col_count++;
			$report[$row_count][$col_count] = 'Service';
			$col_count++;
			$report[$row_count][$col_count] = 'Amount';
			$col_count++;
			$report[$row_count][$col_count] = 'Method';
			$col_count++;
			$report[$row_count][$col_count] = 'Description';
			$col_count++;
			$report[$row_count][$col_count] = 'Recorded by';
			$col_count++;
			$current_column = $col_count ;
			
			foreach ($query->result() as $row)
			{
				$count++;
				$row_count++;
				$col_count = 0;
				
				$total_invoiced = 0;
				$payment_created = date('jS M Y',strtotime($row->payment_created));
				$time = date('H:i a',strtotime($row->time));
				$visit_id = $row->visit_id;
				$patient_id = $row->patient_id;
				$personnel_id = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				$visit_table_visit_type = $visit_type;
				$patient_table_visit_type = $visit_type_id;
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_number = $row->patient_number;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$payment_method = $row->payment_method;
				$amount_paid = $row->amount_paid;
				$service_name = $row->service_name;
				$transaction_code = $row->transaction_code;
				$created_by = $row->personnel_fname.' '.$row->personnel_onames;
				
				$report[$row_count][$col_count] = $count;
				$col_count++;
				$report[$row_count][$col_count] = $payment_created;
				$col_count++;
				$report[$row_count][$col_count] = $time;
				$col_count++;
				$report[$row_count][$col_count] = $patient_number;
				$col_count++;
				$report[$row_count][$col_count] = $patient_surname.' '.$patient_othernames;
				$col_count++;
				$report[$row_count][$col_count] = $visit_type_name;
				$col_count++;
				$report[$row_count][$col_count] = $service_name;
				$col_count++;
				$report[$row_count][$col_count] = number_format($amount_paid, 2);
				$col_count++;
				$report[$row_count][$col_count] = $payment_method;
				$col_count++;
				$report[$row_count][$col_count] = $transaction_code;
				$col_count++;
				$report[$row_count][$col_count] = $created_by;
				$col_count++;
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	
	public function get_debtor_invoice_items($debtor_invoice_id)
	{
		$this->db->select('patients.patient_surname, patients.patient_othernames, patients.patient_number, patients.current_patient_number, visit.visit_id, visit.visit_date, visit.patient_insurance_number, debtor_invoice_item.debtor_invoice_item_status, debtor_invoice_item.debtor_invoice_item_id,visit.rejected_amount,patients.insurance_number,patients.scheme_name,visit_type.visit_type_name');
		$this->db->where('visit.visit_delete = 0 AND visit.visit_type = visit_type.visit_type_id AND visit.visit_id = debtor_invoice_item.visit_id AND visit.patient_id = patients.patient_id AND debtor_invoice_item.debtor_invoice_id = '.$debtor_invoice_id);
		
		$this->db->group_by('visit_id');
		$this->db->order_by('visit_date');
		$query = $this->db->get('debtor_invoice_item, visit, patients,visit_type');
		
		return $query;
	}
	public function get_symptoms($table, $where, $config, $order, $order_method, $page)
	{
		$this->db->from($table);
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $config, $page);
		
		return $query;
	}
	public function get_all_symptoms()
	{
		$table = 'visit_symptoms, visit, symptoms';
		$where = 'visit_symptoms.visit_id = visit.visit_id AND visit_symptoms.symptoms_id = symptoms.symptoms_id AND visit.visit_delete = 0';
		
		$search = $this->session->userdata('all_symptoms_search');
		$search_title = $this->session->userdata('all_symptoms_search_title');
		
		if(!empty($search))
		{
			$where .= $search;
		}
		
		$date_search = $this->session->userdata('all_symptoms_date_search');
		if(empty($date_search))
		{
			$where .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';
		}
		$this->db->where($where);
		$query = $this->db->get($table);
		return $query;
	}
	public function get_objectives($table, $where, $config, $order, $order_method, $page)
	{
		$this->db->from($table);
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $config, $page);
		
		return $query;
	}
	public function get_all_objectives()
	{
		$table = 'visit_objective_findings, visit, objective_findings, objective_findings_class';
		$where = 'visit_objective_findings.visit_id = visit.visit_id AND visit_objective_findings.objective_findings_id = objective_findings.objective_findings_id AND visit.visit_delete = 0 AND objective_findings_class.objective_findings_clasS_id = objective_findings.objective_findings_id';
		
		$search = $this->session->userdata('all_objectives_search');
		$search_title = $this->session->userdata('all_objectives_search_title');
		
		if(!empty($search))
		{
			$where .= $search;
		}
		
		$date_search = $this->session->userdata('all_objectives_date_search');
		if(empty($date_search))
		{
			$where .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';
		}
		$this->db->where($where);
		$query = $this->db->get($table);
		return $query;
	}
	public function get_tests($table, $where, $config, $order, $order_method, $page)
	{
		$this->db->from($table);
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $config, $page);
		
		return $query;
	}
	public function get_all_lab_tests()
	{
		$table = 'visit, visit_lab_test, service_charge';
		$where = 'visit_lab_test.service_charge_id = service_charge.service_charge_id AND visit_lab_test.visit_id = visit.visit_id AND visit_lab_test.visit_lab_test_status = 1 AND visit.visit_delete = 0';
		$search = $this->session->userdata('all_tests_search');
		$search_title = $this->session->userdata('all_tests_search_title');
		
		if(!empty($search))
		{
			$where .= $search;
		}
		
		$date_search = $this->session->userdata('all_tests_date_search');
		if(empty($date_search))
		{
			$where .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';
		}
		$this->db->where($where);
		$query = $this->db->get($table);
		return $query;
	}
	public function get_drugs($table, $where, $config, $order, $order_method, $page)
	{
		$this->db->from($table);
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $config, $page);
		
		return $query;
	}
	public function get_all_drugs_given()
	{
		$table = 'visit, pres, service_charge';
		$where = 'pres.service_charge_id = service_charge.service_charge_id AND pres.visit_id = visit.visit_id AND visit.visit_delete = 0';
		$search = $this->session->userdata('all_drugs_search');
		$search_title = $this->session->userdata('all_drugs_search_title');
		
		if(!empty($search))
		{
			$where .= $search;
		}
		
		$date_search = $this->session->userdata('all_drugs_date_search');
		if(empty($date_search))
		{
			$where .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';
		}
		$this->db->where($where);
		$query = $this->db->get($table);
		return $query;
	}
	
	public function get_highest_drug_sales()
	{
		$where = 'pres.service_charge_id = service_charge.service_charge_id AND pres.visit_id = visit.visit_id AND visit.visit_delete = 0 AND pres.visit_charge_id = visit_charge.visit_charge_id AND visit_charge.charged = 1';
		$this->db->select('service_charge.service_charge_name, service_charge.product_id, SUM(pres.units_given) AS total_sales');
		$search = $this->session->userdata('all_drugs_search');
		
		if(!empty($search))
		{
			$where .= $search;
		}
		
		$date_search = $this->session->userdata('all_drugs_date_search');
		if(empty($date_search))
		{
			$where .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';
		}
		$this->db->where($where);
		$this->db->group_by('product_id');
		$this->db->order_by('total_sales', 'DESC');
		$query = $this->db->get('visit, pres, service_charge,visit_charge', 10);
		return $query;
	}
	
	public function get_highest_test_sales()
	{
		$table = 'visit, visit_lab_test, service_charge';
		$where = 'visit_lab_test.service_charge_id = service_charge.service_charge_id AND visit_lab_test.visit_id = visit.visit_id AND visit_lab_test.visit_lab_test_status = 1 AND visit.visit_delete = 0';
		
		$this->db->select('service_charge.service_charge_name, service_charge.lab_test_id, COUNT(visit_lab_test.visit_lab_test_id) AS total_sales');
		$search = $this->session->userdata('all_tests_search');
		$search_title = $this->session->userdata('all_tests_search_title');
		
		if(!empty($search))
		{
			$where .= $search;
		}
		
		$date_search = $this->session->userdata('all_tests_date_search');
		if(empty($date_search))
		{
			$where .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';
		}
		$this->db->where($where);
		$this->db->group_by('lab_test_id');
		$this->db->order_by('total_sales', 'DESC');
		$query = $this->db->get($table, 10);
		return $query;
	}
	
	public function get_highest_objectives()
	{
		$table = 'visit_objective_findings, visit, objective_findings, objective_findings_class';
		$where = 'visit_objective_findings.visit_id = visit.visit_id AND visit_objective_findings.objective_findings_id = objective_findings.objective_findings_id AND visit.visit_delete = 0 AND objective_findings_class.objective_findings_clasS_id = objective_findings.objective_findings_id';
		
		$search = $this->session->userdata('all_objectives_search');
		$search_title = $this->session->userdata('all_objectives_search_title');
		
		if(!empty($search))
		{
			$where .= $search;
		}
		
		$date_search = $this->session->userdata('all_objectives_date_search');
		if(empty($date_search))
		{
			$where .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';
		}
		$this->db->select('objective_findings.objective_findings_name, visit_objective_findings.objective_findings_id, COUNT(visit_objective_findings.objective_findings_id) AS total_sales');
		$this->db->where($where);
		$this->db->group_by('objective_findings_id');
		$this->db->order_by('total_sales', 'DESC');
		$query = $this->db->get($table, 10);
		return $query;
	}
	
	public function get_highest_symptoms()
	{
		$table = 'visit_symptoms, visit, symptoms';
		$where = 'visit_symptoms.visit_id = visit.visit_id AND visit_symptoms.symptoms_id = symptoms.symptoms_id AND visit.visit_delete = 0';
		
		$search = $this->session->userdata('all_symptoms_search');
		$search_title = $this->session->userdata('all_symptoms_search_title');
		
		if(!empty($search))
		{
			$where .= $search;
		}
		
		$date_search = $this->session->userdata('all_symptoms_date_search');
		if(empty($date_search))
		{
			$where .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';
		}
		$this->db->select('symptoms.symptoms_name, visit_symptoms.symptoms_id, COUNT(visit_symptoms.symptoms_id) AS total_sales');
		$this->db->where($where);
		$this->db->group_by('symptoms_id');
		$this->db->order_by('total_sales', 'DESC');
		$query = $this->db->get($table, 10);
		return $query;
	}
	public function get_all_malaria_tests($table, $where, $per_page, $page, $order, $order_method)
	{
		$this->db->from($table);
		//$this->db->join('staff', 'staff.payroll_no = patients.strath_no', 'left');
		$this->db->select('service_charge.*, patients.*, visit.visit_date, visit_lab_test.*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	public function get_all_malaria_tests_download($table, $where)
	{
		$this->db->select('service_charge.*, patients.*, visit.visit_date, visit_lab_test.*');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		return $query;
	}
	public function get_all_cholinestrase_tests($table, $where, $per_page, $page, $order, $order_method)
	{
		$this->db->from($table);
		//$this->db->join('staff', 'staff.payroll_no = patients.strath_no', 'left');
		$this->db->select('visit.visit_id,visit.department_name, patients.*, visit.visit_date, visit_type.visit_type_name');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	public function get_all_cholinestrase_tests_download($table, $where)
	{
		$this->db->select('visit.visit_id,visit.department_name, patients.*, visit.visit_date, visit_type.visit_type_name');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		return $query;
	}
	public function get_cholinestrase_results($visit_id)
	{
		$where = 'lab_visit_results.visit_id = "'.$visit_id.'"';
		$table = 'lab_visit_results';
		
		$this->db->where($where);
		$query = $this->db->get($table);
		
		return $query;
	}
	public function get_all_mpesa_payments($table, $where,$order,$order_method,$config,$page)
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
	function mpesa_reports_export()
	{
		$this->load->library('excel');
		
		$branch_code = $this->session->userdata('search_branch_code');
		
		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}
		
		$this->db->where('branch_code', $branch_code);
		$query = $this->db->get('branch');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$branch_name = $row->branch_name;
		}
		
		else
		{
			$branch_name = '';
		}
		$v_data['branch_name'] = $branch_name;
		
		$where = 'payment_method_id = 5 AND payment_type = 1 AND payments.visit_id = visit.visit_id AND visit.patient_id = patients.patient_id';
		$table = 'payments, visit, patients';
		
		$mpesa_search = $this->session->userdata('mpesa_search');
		if(!empty($mpesa_search))
		{
			$where .= $mpesa_search;
		}
		
		$this->db->select('patients.*, payments.transaction_code, payments.payment_created, payments.payment_for_name, payments.amount_paid');
		$this->db->where($where);
		$this->db->order_by('payments.payment_created','DESC');
		$query = $this->db->get($table);
		
		$title = 'MPESA report '.date('jS M Y H:i a',strtotime(date('Y-m-d H:i:s')));
		$col_count = 0;
		
		if($query->num_rows() > 0)
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
			$report[$row_count][$col_count] = 'MPESA TX Code';
			$col_count++;
			$report[$row_count][$col_count] = 'Amount';
			$col_count++;
			$report[$row_count][$col_count] = 'Payment Date';
			$col_count++;
			$report[$row_count][$col_count] = 'Patient Full Names';
			$col_count++;
			
			$current_column = $col_count ;
			
			foreach ($query->result() as $row)
			{
				$count++;
				$row_count++;
				$col_count = 0;
				
				$total_invoiced = 0;
				$transaction_code = $row->transaction_code;
				$payment_created = $row->payment_created;
				$payment_for_name = $row->payment_for_name;
				$payment_amount = $row->amount_paid;
				if(empty($payment_for_name))
				{
					$patient_fname = $row->patient_surname;
					$patient_oname = $row->patient_othernames;
					$patient_name = $patient_fname.' '.$patient_oname;
					
				}
				else
				{
					$patient_name = $payment_for_name;
				}
				
				$report[$row_count][$col_count] = $count;
				$col_count++;
				$report[$row_count][$col_count] = strtoupper($transaction_code);
				$col_count++;
				$report[$row_count][$col_count] = number_format($payment_amount,2);
				$col_count++;
				$report[$row_count][$col_count] = date('jS M Y',strtotime($payment_created));
				$col_count++;
				$report[$row_count][$col_count] = $patient_name;
				$col_count++;
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

	function export_provider_report($provider_id,$report_type)
	{
		$this->load->library('excel');
		
		if($report_type == 1)
		{
			$add = ' AND visit_type.visit_type_name = "Cash paying"';
		}
		else
		{
			$add = ' AND visit_type.visit_type_name <> "Cash paying"';
		}
		$where = 'visit.visit_id = visit_charge.visit_id AND visit_charge.visit_charge_delete = 0 AND visit.visit_type = visit_type.visit_type_id '.$add.' AND service_charge.service_charge_id = visit_charge.service_charge_id AND visit.patient_id = patients.patient_id AND personnel.personnel_id = visit_charge.provider_id AND visit_charge.provider_id = '.$provider_id;
		$table = 'patients, visit, visit_type,visit_charge,service_charge,personnel';
		
		$providers_search = $this->session->userdata('providers_search');
		if(!empty($providers_search))
		{
			$where .= $providers_search;
		}

		$charges_search = $this->session->userdata('charges_search');
		if(!empty($charges_search))
		{
			$where .= $charges_search;
		}
		
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by('visit.visit_date','DESC');
		$query = $this->db->get($table);
		
		$title = 'Provider Report '.date('jS M Y H:i a',strtotime(date('Y-m-d H:i:s')));
		$col_count = 0;
		$report =array();
		if($query->num_rows() > 0)
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
			$report[$row_count][$col_count] = 'Date / Time';
			$col_count++;
			$report[$row_count][$col_count] = 'Patient';
			$col_count++;
			$report[$row_count][$col_count] = 'Service';
			$col_count++;
			$report[$row_count][$col_count] = 'Amount Charged (Ksh.)';
			$col_count++;
			$report[$row_count][$col_count] = 'Waived (Ksh.)';
			$col_count++;
			$report[$row_count][$col_count] = 'Amount to Provider (Ksh.)';
			$col_count++;
			$report[$row_count][$col_count] = 'Provider';
			$col_count++;
			
			$current_column = $col_count ;
			
			foreach ($query->result() as $row)
			{
				$count++;
				$row_count++;
				$col_count = 0;
				
				$total_invoiced = 0;
				$personnel_id = $row->personnel_id;
				$personnel_onames = $row->personnel_onames;
				$personnel_fname = $row->personnel_fname;
				$personnel_type_id = $row->personnel_type_id;
				$provider_id = $row->provider_id;
				
				$date = $row->date;
				$time = $row->time;
				$visit_charge_amount = $row->visit_charge_amount;
				$service_charge_amount = $row->service_charge_amount;
				$service_charge_name = $row->service_charge_name;
				$patient_surname = $row->patient_surname;
				$patient_othernames = $row->patient_othernames;

				$visit_charge_date = date('jS M Y',strtotime($date));
				$visit_charge_time = date('H:i:s A',strtotime($time));

				$report[$row_count][$col_count] = $count;
				$col_count++;
				$report[$row_count][$col_count] = $visit_charge_date.' '.$visit_charge_time;
				$col_count++;
				$report[$row_count][$col_count] = $patient_surname." ".$patient_othernames;
				$col_count++;
				$report[$row_count][$col_count] = $service_charge_name;
				$col_count++;
				$report[$row_count][$col_count] = number_format($visit_charge_amount,2);
				$col_count++;
				$report[$row_count][$col_count] = number_format(0,2);
				$col_count++;
				$report[$row_count][$col_count] = number_format($visit_charge_amount,2);
				$col_count++;
				$report[$row_count][$col_count] = $personnel_fname.' '.$personnel_onames;
				$col_count++;
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	public function get_total_transfers($where, $table)
	{
		//payments
		$table_search = $this->session->userdata('all_transactions_tables');
		
		$this->db->from($table);
		$this->db->select('SUM(petty_cash.petty_cash_amount) AS total_paid');
		$this->db->where($where);
		$query = $this->db->get();
		
		$cash = $query->row();
		$total_paid = $cash->total_paid;
		if($total_paid > 0)
		{
		}
		
		else
		{
			$total_paid = 0;
		}
		
		return $total_paid;
	}
	public function get_total_cash_today($where, $table)
	{
		//payments
		
		$this->db->from($table);
		$this->db->select('SUM(payments.amount_paid) AS total_paid');
		$this->db->where($where);
		$query = $this->db->get();
		
		$cash = $query->row();
		$total_paid = $cash->total_paid;
		if($total_paid > 0)
		{
		}
		
		else
		{
			$total_paid = 0;
		}
		
		return $total_paid;
	}

	public function get_all_drugs_sold($where, $table)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('product.product_id, product.product_name, product.quantity AS starting_stock,service_charge.*,visit_charge.*,pres.*,visit.*');
		$this->db->where($where);
		$query = $this->db->get('');
		
		return $query;
	}

	public function get_visit_type_invoice($visit_type_id,$visit_date = NULL)
	{
		if(!empty($visit_date))
		{
			$date  = ' AND visit_date = "'.$visit_date.'" ';
		}
		else
		{
			$date  = ' AND visit_date = "'.date('Y-m-d').'" ';
		}
		//retrieve all users
		$this->db->from('visit');
		$this->db->select('*');
		$this->db->where('visit.visit_delete = 0 '.$date.' AND visit_type = '.$visit_type_id);
		$query = $this->db->get('');
		$invoice_amount = 0;
		$payment_amount = 0;
		$balance_amount = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$visit_id = $value->visit_id;


				$payments_value = $this->accounts_model->total_payments($visit_id);

				$invoice_total = $this->accounts_model->total_invoice($visit_id);

				$balance = $this->accounts_model->balance($payments_value,$invoice_total);

				$invoice_amount = $invoice_amount + $invoice_total;
				$payment_amount = $payment_amount + $payments_value;
				$balance_amount = $balance_amount + $balance;

			}
		}

		$response['invoice_total'] = $invoice_amount;
		$response['payments_value']= $payment_amount;
		$response['balance'] = $balance_amount;
		return $response;
	}

	public function get_visit_type_invoice_todays($visit_type_id,$visit_date = NULL)
	{
		if(!empty($visit_date))
		{
			$date  = ' AND visit_date = "'.$visit_date.'" ';
		}
		else
		{
			$date  = ' AND visit_date = "'.date('Y-m-d').'" ';
		}
		//retrieve all users
		$this->db->from('visit');
		$this->db->select('*');
		$this->db->where('visit.visit_delete = 0 '.$date.' AND visit_type = '.$visit_type_id);
		$query = $this->db->get('');
		$invoice_amount = 0;
		$payment_amount = 0;
		$balance_amount = 0;
		// if($query->num_rows() > 0)
		// {
		// 	foreach ($query->result() as $key => $value) {

				// $visit_id = $value->visit_id;


				$payments_value = $this->accounts_model->total_payments_today($visit_date,$visit_type_id);

				$invoice_total = $this->accounts_model->total_invoice_today($visit_date,$visit_type_id);

				$balance = $this->accounts_model->balance($payments_value,$invoice_total);

				$invoice_amount = $invoice_amount + $invoice_total;
				$payment_amount = $payment_amount + $payments_value;
				$balance_amount = $balance_amount + $balance;

		// 	}
		// }

		$response['invoice_total'] = $invoice_amount;
		$response['payments_value']= $payment_amount;
		$response['balance'] = $balance_amount;
		return $response;
	}

	public function get_doctors_patients($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by('visit.visit_id','ASC');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}


	public function get_normal_collections($where, $table, $page = NULL)
	{
		
		$table_search = $this->session->userdata('all_transactions_tables');		
		$this->db->from($table);
		$this->db->select('SUM(amount_paid) AS total_amount');
		$this->db->where($where);
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


	public function get_normal_invoices($where, $table, $page = NULL)
	{
		
		$table_search = $this->session->userdata('all_transactions_tables');		
		$this->db->from($table);
		$this->db->select('SUM(visit_charge.visit_charge_amount*visit_charge.visit_charge_units) AS total_amount');
		$this->db->where($where);
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

	public function get_all_cash_staff($where)
	{		
		$table_search = $this->session->userdata('all_transactions_tables');	


		$where = 'personnel.personnel_id = payments.personnel_id '.$where;
		$this->db->from('payments,personnel');
		$this->db->select('*');
		$this->db->where($where);
		$this->db->group_by('personnel.personnel_id');
		$query = $this->db->get();
		return $query;
	}
	public function get_collected_staff_cash($personnel_id,$where)
	{
		$table_search = $this->session->userdata('all_transactions_tables');	
		$table= 'payments';
		$where = 'personnel_id = '.$personnel_id.'  AND payments.cancel = 0 '.$where	;
		$this->db->from($table);
		$this->db->select('SUM(amount_paid) AS total_amount');
		$this->db->where($where);
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

	public function receipt_payment($visit_id,$personnel_id = NULL){
		
		$payment_method= $this->input->post('payment_method'.$visit_id);
		$type_payment= 1; //$this->input->post('type_payment');
		$payment_service_id= 0;//$this->input->post('payment_service_id');
		
		$amount = $this->input->post('amount'.$visit_id);
	
		
		if($payment_method == 1)
		{
			// check for cheque number if inserted			
			$transaction_code = $this->input->post('cheque_number'.$visit_id);
		}
		else if($payment_method == 6)
		{
			// check for insuarance number if inserted
			$transaction_code = $this->input->post('debit_card_detail'.$visit_id);
		}
		else if($payment_method == 5)
		{
			//  check for mpesa code if inserted
			$transaction_code = $this->input->post('mpesa_code'.$visit_id);
		}
		else if($payment_method == 7)
		{
			//  check for mpesa code if inserted
			$transaction_code = $this->input->post('deposit_detail'.$visit_id);
		}
		else if($payment_method == 8)
		{
			//  check for mpesa code if inserted
			$transaction_code = $this->input->post('debit_card_detail'.$visit_id);
		}
		else
		{
			$transaction_code = '';
		}
		$data = array(
			'visit_id' => $visit_id,
			'payment_method_id'=>$payment_method,
			'amount_paid'=>$amount,
			'personnel_id'=>$this->session->userdata("personnel_id"),
			'payment_type'=>$type_payment,
			'payment_service_id'=>$payment_service_id,
			'transaction_code'=>$transaction_code,
			'change'=>0,
			'payment_created'=>date("Y-m-d"),
			'payment_created_by'=>$this->session->userdata("personnel_id"),
			'approved_by'=>$this->session->userdata("personnel_id"),
			'date_approved'=>date('Y-m-d')
		);

		// var_dump($data);die();
		if($this->db->insert('payments', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}


	public function invoice_hospital($visit_id,$type){
		

		if($type == 2)
		{
			$amount = $this->input->post('amount'.$visit_id);
			$cash_amount = $this->input->post('cash_amount'.$visit_id);


			$this->db->where('visit_id = '.$visit_id.' AND type = 1');
			$query = $this->db->get('doctor_invoice');
			if($query->num_rows() > 0)
			{
				// do an update

				$data = array(
					'visit_id' => $visit_id,
					'invoiced_amount'=>$cash_amount,
					'modified_by'=>$this->session->userdata("personnel_id"),
				);

				// var_dump($data);die();
				$this->db->where('visit_id = '.$visit_id.'');
				if($this->db->update('doctor_invoice', $data))
				{
					// return TRUE;
				}
				else{
					// return FALSE;
				}
			}
			else
			{
				$data = array(
					'visit_id' => $visit_id,
					'invoiced_amount'=>$cash_amount,
					'created_by'=>$this->session->userdata("personnel_id"),
					'modified_by'=>$this->session->userdata("personnel_id"),
					'doctor_invoice_status'=>0,
					'type'=>1,
					'created'=>date("Y-m-d")
				);

				// var_dump($data);die();
				if($this->db->insert('doctor_invoice', $data))
				{
					// return TRUE;
				}
				else{
					// return FALSE;
				}
			}

			// insurance patients
			$this->db->where('visit_id = '.$visit_id.' AND type = 0');
			$query = $this->db->get('doctor_invoice');
			if($query->num_rows() > 0)
			{
				// do an update

				$data = array(
					'visit_id' => $visit_id,
					'invoiced_amount'=>$amount,
					'modified_by'=>$this->session->userdata("personnel_id"),
				);

				// var_dump($data);die();
				$this->db->where('visit_id = '.$visit_id.'');
				if($this->db->update('doctor_invoice', $data))
				{
					// return TRUE;
				}
				else{
					// return FALSE;
				}
			}
			else
			{
				$data = array(
					'visit_id' => $visit_id,
					'invoiced_amount'=>$amount,
					'created_by'=>$this->session->userdata("personnel_id"),
					'modified_by'=>$this->session->userdata("personnel_id"),
					'doctor_invoice_status'=>0,
					'type'=>0,
					'created'=>date("Y-m-d")
				);

				// var_dump($data);die();
				if($this->db->insert('doctor_invoice', $data))
				{
					// return TRUE;
				}
				else{
					// return FALSE;
				}
			}
			return TRUE;

		}
		else
		{
			$amount = $this->input->post('amount'.$visit_id);
			// check if exisit

			$this->db->where('visit_id = '.$visit_id.' AND type = '.$type);
			$query = $this->db->get('doctor_invoice');
			if($query->num_rows() > 0)
			{
				// do an update

				$data = array(
					'visit_id' => $visit_id,
					'invoiced_amount'=>$amount,
					'modified_by'=>$this->session->userdata("personnel_id"),
				);

				// var_dump($data);die();
				$this->db->where('visit_id = '.$visit_id.'');
				if($this->db->update('doctor_invoice', $data))
				{
					return TRUE;
				}
				else{
					return FALSE;
				}
			}
			else
			{
				$data = array(
					'visit_id' => $visit_id,
					'invoiced_amount'=>$amount,
					'created_by'=>$this->session->userdata("personnel_id"),
					'modified_by'=>$this->session->userdata("personnel_id"),
					'doctor_invoice_status'=>0,
					'type'=>$type,
					'created'=>date("Y-m-d")
				);

				// var_dump($data);die();
				if($this->db->insert('doctor_invoice', $data))
				{
					return TRUE;
				}
				else{
					return FALSE;
				}
			}
		}
		
		
		
	}

	public function get_invoiced_values($where, $table, $page = NULL)
	{
		
		$table_search = $this->session->userdata('all_transactions_tables');		
		$this->db->from($table);
		$this->db->select('SUM(visit_charge.visit_charge_amount*visit_charge.visit_charge_units) AS total_amount');
		$this->db->where($where);
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

	public function get_all_data_content($table, $where, $config, $page,$order_by, $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order_by,'ASC');
		$query = $this->db->get('', $config, $page);

		return $query;
	}
	function export_debt_transactions($debtor_invoice_id)
	{


		$where = 'debtor_invoice.debtor_invoice_id = '.$debtor_invoice_id.' AND debtor_invoice.visit_type_id = visit_type.visit_type_id';
		$table = 'debtor_invoice, visit_type';

		$query =  $this->reports_model->get_debtor_invoice($where, $table);
		$debtor_invoice_items = $this->reports_model->get_debtor_invoice_items($debtor_invoice_id);



		$contacts = $this->site_model->get_contacts();

		
		
		$this->load->library('excel');
		
		// $debtors_row = $debtor_invoice_items->row();
		$date_from = '';
		$date_to = '';
		foreach($query->result() as $debtors_row)
		{
		$date_from = $debtors_row->date_from;
		$date_to = $debtors_row->date_to;
		}
		$title =  'Uncleared Claims for period '.$date_from.' to '.$date_to;
		$col_count = 0;
		$total_invoice = 0;
		$row_count = 0;
		$report =array();
		$report[$row_count][$col_count] = 'IRIS DENTAL CLINIC';
		$row_count++;
		$report[$row_count][$col_count] = $title;
		$row_count++;
		
		if($debtor_invoice_items->num_rows() > 0)
		{
			$count = 0;
			/*
				-----------------------------------------------------------------------------------------
				Document Header
				-----------------------------------------------------------------------------------------
			*/

		
        	// $report->mergeCells("GA".($row_count+1).":I".($row_count+1)); = 'sdajdlakjdklaj';
			
			$report[$row_count][$col_count] = '#';
			$col_count++;
			$report[$row_count][$col_count] = 'Visit Date';
			$col_count++;
			$report[$row_count][$col_count] = 'Name';
			$col_count++;
			$report[$row_count][$col_count] = 'Patient number';
			$col_count++;
			$report[$row_count][$col_count] = 'Member Number';
			$col_count++;
			$report[$row_count][$col_count] = 'Scheme Name';
			$col_count++;
			$report[$row_count][$col_count] = 'Insurance';
			$col_count++;
			$report[$row_count][$col_count] = 'Invoice Number';
			$col_count++;
			$report[$row_count][$col_count] = 'Amount Due';
			$col_count++;

			$current_column = $col_count ;
			
			//display all patient data in the leftmost columns
			foreach($debtor_invoice_items->result() as $row)
			{
				$row_count++;
				$total_invoiced = 0;
				$visit_date = date('jS M Y',strtotime($row->visit_date));
				
				 $patient_surname = $row->patient_surname;
                $patient_othernames = $row->patient_othernames;
                $patient_number = $row->patient_number;
                $patient_insurance_number = $row->patient_insurance_number;
                $current_patient_number = $row->current_patient_number;
				$debtor_invoice_item_status = $row->debtor_invoice_item_status;
				$debtor_invoice_item_id = $row->debtor_invoice_item_id;
                $rejected_amount = $row->rejected_amount;
                $insurance_number = $row->insurance_number;
                $scheme_name = $row->scheme_name;
                $visit_type_name = $row->visit_type_name;
                $rejected_amount = $row->rejected_amount;
                $visit_id = $row->visit_id;

				// this is to check for any credit note or debit notes
				$payments_value = $this->accounts_model->total_payments($visit_id);

				$invoice_total = $this->accounts_model->total_invoice($visit_id);

				$invoice_amount = $invoice_total - $payments_value;

                $cash_balance = 0;
                if(!empty($rejected_amount))
                {
                    $cash_balance = $rejected_amount - $payments_value;
                }
                $invoice_amount -= $cash_balance;
				$total_invoice += $invoice_amount;
				$count++;
				
				if($invoice_amount > 0)
				{
						//display the patient data
					$report[$row_count][$col_count] = $count;
					$col_count++;
					$report[$row_count][$col_count] = $visit_date;
					$col_count++;
					$report[$row_count][$col_count] = $patient_surname.' '.$patient_othernames;
					$col_count++;
					$report[$row_count][$col_count] = $patient_number;
					$col_count++;
					$report[$row_count][$col_count] = $insurance_number;
					$col_count++;
					$report[$row_count][$col_count] = $scheme_name;
					$col_count++;
					$report[$row_count][$col_count] = $visit_type_name;
					$col_count++;
					$report[$row_count][$col_count] = $visit_id;
					$col_count++;
					$report[$row_count][$col_count] = number_format($invoice_amount,2);
					$col_count++;
				}
			
				

			}
			$row_count++;
			$report[$row_count][1] = '';
			$report[$row_count][2] = '';
			$report[$row_count][3] = '';
			$report[$row_count][4] = '';
			$report[$row_count][5] = '';
			$report[$row_count][6] = '';
			$report[$row_count][7] = '';
			$report[$row_count][8] = '';
			$report[$row_count][9] = number_format($total_invoice,2);

			
			
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

	public function get_total_account_transfers($where, $table,$select)
	{
		$table_search = $this->session->userdata('all_transactions_tables');
		
		$this->db->from($table);
		$this->db->select($select);
		$this->db->where($where);
		$query = $this->db->get();
		
		$cash = $query->row();
		$total_paid = $cash->total_paid;
		if($total_paid > 0)
		{
		}
		
		else
		{
			$total_paid = 0;
		}
		
		return $total_paid;
	}
}