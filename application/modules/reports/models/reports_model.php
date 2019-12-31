
<?php

class Reports_model extends CI_Model 
{
	public function get_queue_total($branch_code = 'OSE', $date = NULL, $where = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		if($where == NULL)
		{
			$where = 'visit.branch_code = \''.$branch_code.'\' AND visit.close_card = 0 AND visit.visit_date = \''.$date.'\' AND visit.visit_delete = 0';
		}
		
		else
		{
			$where .= ' AND visit.branch_code = \''.$branch_code.'\' AND visit.visit_delete = 0 AND visit.close_card = 0 AND visit.visit_date = \''.$date.'\' ';
		}
		
		$this->db->select('COUNT(visit.visit_id) AS queue_total');
		$this->db->where($where);
		$query = $this->db->get('visit');
		
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
	
	public function get_patients_total($branch_code = 'OSE', $date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$this->db->select('COUNT(visit_id) AS patients_total');
		$this->db->where('visit.branch_code = \''.$branch_code.'\' AND visit_date = \''.$date.'\' AND visit.visit_delete = 0');
		$query = $this->db->get('visit');
		
		$result = $query->row();
		
		return $result->patients_total;
	}

	public function get_totals_items($where_item = NULL)
	{

		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 '.$where_item;
		$table = 'visit, patients, visit_type';


		$visit_report_search = $this->session->userdata('visit_report_search');
		
		if(!empty($visit_report_search))
		{
			$where .= $visit_report_search;
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';
		}

		$this->db->select('COUNT(visit_id) AS patients_total');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		$result = $query->row();
		
		return $result->patients_total;
	}


	public function calculate_distict($item = NULL)
	{

		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0  AND visit.inpatient = 0';
		$table = 'visit, patients, visit_type';


		$visit_report_search = $this->session->userdata('visit_report_search');
		
		if(!empty($visit_report_search))
		{
			$where .= $visit_report_search;
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';
		}

		$this->db->select('visit.patient_id,rip_status');
		$this->db->where($where);
		if($item ==1)
		{
			$this->db->group_by('visit.patient_id');	
		}
		$query = $this->db->get($table);
		$response['total_count'] = $query->num_rows();
		$new_visit = 0;
		$repeat_visit = 0;
		$rip_number=0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key) {
				# code...
				$patient_id = $key->patient_id;
				$rip_status = $key->rip_status;

				$last_visit_rs = $this->reception_model->get_if_patients_first_visit($patient_id);

				// var_dump($last_visit_rs); die();
				if($last_visit_rs->num_rows() == 1)
				{	

					
					$new_visit++;
				}
				
				else if($last_visit_rs->num_rows() > 1)
				{	
					$repeat_visit++;
					
				}

				if($rip_status ==1)
				{
					$rip_number++;
				}

				
			}


		}

		$response['new_visit'] = $new_visit;
		$response['repeat_visit'] = $repeat_visit;
		$response['rip_number'] = $rip_number;
		
	
		
		return $response;
	}


	public function get_totals_inpatient_items($where_item = NULL)
	{

		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 '.$where_item;
		$table = 'visit, patients, visit_type';


		$inpatient_report_search = $this->session->userdata('inpatient_report_search');
		
		if(!empty($inpatient_report_search))
		{
			$where .= $inpatient_report_search;
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';
		}

		$this->db->select('COUNT(visit_id) AS patients_total');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		$result = $query->row();
		
		return $result->patients_total;
	}


	public function calculate_distict_inpatient($item = NULL)
	{

		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0  AND visit.inpatient = 1';
		$table = 'visit, patients, visit_type';


		$inpatient_report_search = $this->session->userdata('inpatient_report_search');
		
		if(!empty($inpatient_report_search))
		{
			$where .= $inpatient_report_search;
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';
		}

		$this->db->select('visit.patient_id,rip_status');
		$this->db->where($where);
		if($item ==1)
		{
			$this->db->group_by('visit.patient_id');	
		}
		$query = $this->db->get($table);
		$response['total_count'] = $query->num_rows();
		$new_visit = 0;
		$repeat_visit = 0;
		$rip_number=0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key) {
				# code...
				$patient_id = $key->patient_id;
				$rip_status = $key->rip_status;

				$last_visit_rs = $this->reception_model->get_if_patients_first_visit($patient_id);

				// var_dump($last_visit_rs); die();
				if($last_visit_rs->num_rows() == 1)
				{	

					
					$new_visit++;
				}
				
				else if($last_visit_rs->num_rows() > 1)
				{	
					$repeat_visit++;
					
				}

				if($rip_status ==1)
				{
					$rip_number++;
				}

				
			}


		}

		$response['new_visit'] = $new_visit;
		$response['repeat_visit'] = $repeat_visit;
		$response['rip_number'] = $rip_number;
		
	
		
		return $response;
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

		// $this->db->join('staff', 'staff.payroll_no = patients.strath_no', 'left');
		// $this->db->join('staff_dependant', 'staff_dependant.staff_dependant_id = patients.dependant_id', 'left');
		$this->db->where($where);
		$this->db->order_by('visit.visit_date, visit.visit_time','DESC');
		$this->db->group_by('visit.visit_id');
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
	public function get_all_patient_rip($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');

		// $this->db->join('staff', 'staff.payroll_no = patients.strath_no', 'left');
		// $this->db->join('staff_dependant', 'staff_dependant.staff_dependant_id = patients.dependant_id', 'left');
		$this->db->where($where);
		$this->db->order_by('patients.rip_date','ASC');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	public function get_all_visits_content($table, $where, $order_by, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit.*, (visit.visit_time_out - visit.visit_time) AS waiting_time, patients.*, visit_type.visit_type_name');
		// $this->db->join('staff', 'staff.payroll_no = patients.strath_no', 'left');
		// $this->db->join('staff_dependant', 'staff_dependant.staff_dependant_id = patients.dependant_id', 'left');
		$this->db->where($where);
		$this->db->order_by('visit.visit_date, visit.visit_time','DESC');
		$this->db->group_by('visit.visit_id');
		$query = $this->db->get('');
		
		return $query;
	}

	public function get_all_sick_off_content($table, $where, $order_by, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('patient_leave.*,patient_leave.created_by AS personnel_id,patients.*, visit.department_name');
		$this->db->where($where);
		$this->db->order_by('patient_leave.start_date','DESC');
		$query = $this->db->get('');
		
		return $query;
	}

	public function get_all_visits_sick_offs($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('patient_leave.*, patient_leave.created_by AS personnel_id ,patients.*, visit.department_name, leave_type.leave_type_name');
		$this->db->where($where);
		$this->db->order_by('patient_leave.start_date','DESC');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	public function get_all_departments()
	{
		$this->db->distinct('department_name');
		$this->db->select('department_name');
		$this->db->where('department_name IS NOT NULL');
		$query = $this->db->get('visit');
		//var_dump($query); die();
		return $query;
	}
	public function get_all_patient_leave($table, $where, $per_page, $page, $order, $order_method)
	{
		$this->db->from($table);
		//$this->db->join('staff', 'staff.payroll_no = patients.strath_no', 'left');
		$this->db->select('patient_leave.*, patients.*, visit.department_name, leave_type.leave_type_name');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	public function export_outpatient_report()
	{
		$this->load->library('excel');
		
		//get all transactions
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 AND visit.inpatient = 0 AND patients.rip_status =0 AND (visit.close_card = 0 OR visit.close_card = 2)';
		$table = 'visit, patients, visit_type';
		$visit_report_search = $this->session->userdata('visit_report_search');
		
		if(!empty($visit_report_search))
		{
			$where .= $visit_report_search;
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';
		}
		
		$this->db->where($where);
		$this->db->order_by('visit.visit_date, visit.visit_time','DESC');
		$this->db->select('visit.*, (visit.visit_time_out - visit.visit_time) AS waiting_time, patients.*, visit_type.visit_type_name');
		$this->db->group_by('visit.visit_id');
		$visits_query = $this->db->get($table);
		
		$title = 'Outpatient Report';

		$personnel_query = $this->personnel_model->get_all_personnel();
		
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
			$report[$row_count][2] = 'Patient No';
			$report[$row_count][3] = 'Patient Name';
			$report[$row_count][4] = 'Gender';
			$report[$row_count][5] = 'Age';
			$report[$row_count][6] = 'Chemo / Review';
			$report[$row_count][7] = 'Visit';
			$report[$row_count][8] = 'D X';
			$report[$row_count][9] = 'RIP';
			$report[$row_count][10] = 'Patient Type';
			$report[$row_count][11] = 'HC Time In';
			//get & display all services
			
			//display all patient data in the leftmost columns
			foreach($visits_query->result() as $row)
			{
				$row_count++;
				$total_invoiced = 0;
				$visit_date =  date('jS M Y',strtotime($row->visit_date));
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
				$patient_number = $row->patient_number;

				$strath_no = $row->strath_no;
				$personnel_id = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				$gender_id = $row->gender_id;
				$visit_table_visit_type = $visit_type;
				$patient_table_visit_type = $visit_type_id;
				// $first_visit_department = $this->reception_model->first_department($visit_id);
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$last_visit = $row->last_visit;
				// $department_name = $row->department_name;
				$branch_code = $row->branch_code;
				$department = $row->department;
				$inpatient = $row->inpatient;
				// $relative_code = $row->relative_code;
				$referral_reason = $row->referral_reason;
				$rip_status = $row->rip_status;
				$rip_date = $row->rip_date;
				$visit_date1 = $row->visit_date;
				// var_dump($difference);
				if($rip_status == 1  AND $visit_date1 >= $rip_date)
				{
					$rip_status = 'RIP';
				}
				else
				{
					$rip_status = '';
				}
				
				//branch Code
				// if($branch_code =='OSE')
				// {
					$branch_code = 'Main HC';
				// }
				// else
				// {
				// 	$branch_code = 'Oserengoni';
				// }
				
				$close_card = $row->close_card;
				if($close_card == 1)
				{
					$visit_time_out = date('jS M Y H:i a',strtotime($row->visit_time_out));
				}
				else
				{
					$visit_time_out = '-';
				}
				$last_visit_rs = $this->reception_model->get_if_patients_first_visit($patient_id);
				// var_dump($last_visit_rs); die();
				if($last_visit_rs->num_rows() > 1)
				{
					$last_visit_name = 'Re Visit';
				}
				
				else
				{
					$last_visit_name = 'First Visit';
				}

				if($gender_id == 1)
				{
					$gender = 'Male';
				}
				else
				{
					$gender = 'Female';
				}

				// this is to check for any credit note or debit notes
				$payments_value = $this->accounts_model->total_payments($visit_id);

				$invoice_total = $this->accounts_model->total_invoice($visit_id);

				$balance = $this->accounts_model->balance($payments_value,$invoice_total);
				// end of the debit and credit notes


				//creators and editors
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


				if($inpatient == 0)
				{
					$patient_type = 'Outpatient';
				}
				else
				{
					$patient_type = 'Inpatient';
				}
				

				$age = $this->reception_model->calculate_age($patient_date_of_birth);


				$diagnosis_rs = $this->nurse_model->get_visit_diagnosis($visit_id);
				$diagnosis = '';
				if($diagnosis_rs->num_rows() > 0)
				{
					foreach ($diagnosis_rs->result() as $key_other) {
						# code...
						$diseases_name = $key_other->diseases_name;
						$diseases_code = $key_other->diseases_code;

						$diagnosis .= $diseases_name.'  '.$diseases_code.' ';
					}
				}

				$count++;
				
				//display the patient data
				$report[$row_count][0] = $count;
				$report[$row_count][1] = $visit_date;
				$report[$row_count][2] = $patient_number;
				$report[$row_count][3] = $patient_surname.' '.$patient_othernames;
				$report[$row_count][4] = $gender;
				$report[$row_count][5] = $age;
				$report[$row_count][6] = '-';
				$report[$row_count][7] = $last_visit_name;
				$report[$row_count][8] = $diagnosis;
				$report[$row_count][9] = $rip_status;
				$report[$row_count][10] = $patient_type;
				$report[$row_count][11] = $visit_time;
					
				
				
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	
	}

	public function export_inpatient_report()
	{
		$this->load->library('excel');
		
		//get all transactions
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 AND visit.inpatient = 1 AND patients.rip_status =0 AND (visit.close_card = 0 OR visit.close_card = 2)';
		$table = 'visit, patients, visit_type';
		$inpatient_report_search = $this->session->userdata('inpatient_report_search');
		
		if(!empty($inpatient_report_search))
		{
			$where .= $inpatient_report_search;
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';

		}
		
		$this->db->where($where);
		$this->db->order_by('visit.visit_date, visit.visit_time','DESC');
		$this->db->select('visit.*, (visit.visit_time_out - visit.visit_time) AS waiting_time, patients.*, visit_type.visit_type_name');
		$this->db->group_by('visit.visit_id');
		$visits_query = $this->db->get($table);
		
		$title = 'Inpatient Report';

		$personnel_query = $this->personnel_model->get_all_personnel();
		
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
			$report[$row_count][1] = 'Patient No';
			$report[$row_count][2] = 'Patient Name';
			$report[$row_count][3] = 'Gender';
			$report[$row_count][4] = 'Age';
			$report[$row_count][5] = 'Date of Admission';
			$report[$row_count][6] = 'Status';
			$report[$row_count][7] = 'D X';
			$report[$row_count][8] = 'RIP';
			$report[$row_count][9] = 'HC Time In';
			$report[$row_count][10] = 'HC Time Out';
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
				}
				else
				{
					$visit_time_out = '-';
				}
				
				$visit_id = $row->visit_id;
				$patient_id = $row->patient_id;
				$patient_number = $row->patient_number;

				$strath_no = $row->strath_no;
				$personnel_id = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				$gender_id = $row->gender_id;
				$visit_table_visit_type = $visit_type;
				$patient_table_visit_type = $visit_type_id;
				// $first_visit_department = $this->reception_model->first_department($visit_id);
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$last_visit = $row->last_visit;
				// $department_name = $row->department_name;
				$branch_code = $row->branch_code;
				$department = $row->department;
				$inpatient = $row->inpatient;
				$rip_status = $row->rip_status;
				// $relative_code = $row->relative_code;
				$referral_reason = $row->referral_reason;
				
				//branch Code
				// if($branch_code =='OSE')
				// {
					$branch_code = 'Main HC';
				// }
				// else
				// {
				// 	$branch_code = 'Oserengoni';
				// }
				
				$close_card = $row->close_card;
				if($close_card == 1)
				{
					$visit_time_out = date('jS M Y H:i a',strtotime($row->visit_time_out));
					$close_card_status = 'Discharged';
				}
				else if($close_card == 0)
				{
					$close_card_status = 'Patient Admitted';
					$visit_time_out = '-';
				}
				else 
				{
					$close_card_status = 'Discharged In';
					$visit_time_out = '-';
				}
				$last_visit_rs = $this->reception_model->get_if_patients_first_visit($patient_id);
				// var_dump($last_visit_rs); die();
				if($last_visit_rs->num_rows() > 1)
				{
					$last_visit_name = 'Re Visit';
				}
				
				else
				{
					$last_visit_name = 'First Visit';
				}

				if($gender_id == 1)
				{
					$gender = 'Male';
				}
				else
				{
					$gender = 'Female';
				}


				if($gender_id == 1)
				{
					$gender = 'Male';
				}
				else
				{
					$gender = 'Female';
				}
				if($rip_status == 1)
				{
					$rip_status = 'RIP';
				}
				else
				{
					$rip_status = '';
				}

				// this is to check for any credit note or debit notes
				$payments_value = $this->accounts_model->total_payments($visit_id);

				$invoice_total = $this->accounts_model->total_invoice($visit_id);

				$balance = $this->accounts_model->balance($payments_value,$invoice_total);
				// end of the debit and credit notes


				//creators and editors
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


				if($inpatient == 0)
				{
					$patient_type = 'Outpatient';
				}
				else
				{
					$patient_type = 'Inpatient';
				}
				
				
				

				$age = $this->reception_model->calculate_age($patient_date_of_birth);


				$diagnosis_rs = $this->nurse_model->get_visit_diagnosis($visit_id);
				$diagnosis = '';
				if($diagnosis_rs->num_rows() > 0)
				{
					foreach ($diagnosis_rs->result() as $key_other) {
						# code...
						$diseases_name = $key_other->diseases_name;

						$diseases_code = $key_other->diseases_code;

						$diagnosis .= $diseases_name.'  '.$diseases_code.' ';
					}
				}


				$count++;
				
				//display the patient data
				$report[$row_count][0] = $count;
				$report[$row_count][1] = $patient_number;
				$report[$row_count][2] = $patient_surname.' '.$patient_othernames;
				$report[$row_count][3] = $gender;
				$report[$row_count][4] = $age;
				$report[$row_count][5] = $visit_date;
				$report[$row_count][6] = $close_card_status;
				$report[$row_count][7] = $diagnosis;
				$report[$row_count][8] = $rip_status;
				$report[$row_count][9] = $visit_time;
				$report[$row_count][10] = $visit_time_out;
					
				
				
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	
	}

	public function get_all_procedures_visit($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('service_charge_name,sum(visit_charge.visit_charge_units) AS total_count,sum(visit_charge.visit_charge_units*visit_charge.visit_charge_amount) AS total_revenue,service_charge.service_charge_amount,service_charge.service_charge_id');

		// $this->db->join('staff', 'staff.payroll_no = patients.strath_no', 'left');
		// $this->db->join('staff_dependant', 'staff_dependant.staff_dependant_id = patients.dependant_id', 'left');
		$this->db->where($where);
		$this->db->order_by('total_count','DESC');
		$this->db->group_by('service_charge.service_charge_id');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	public function export_procedures_report($service_charge_id)
	{
		$this->load->library('excel');
		
		//get all transactions
		$where = 'visit_charge.service_charge_id = service_charge.service_charge_id AND visit.visit_id = visit_charge.visit_id';
		$table = 'visit_charge,service_charge,visit';
		$inpatient_report_search = $this->session->userdata('procedure_report_search');
		
		if(!empty($inpatient_report_search))
		{
			$where .= $inpatient_report_search;
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';

		}
		if(!empty($service_charge_id))
		{
			$where .= ' AND visit_charge.service_charge_id = '.$service_charge_id;
		}
		
		$this->db->where($where);
		$this->db->select('service_charge_name,sum(visit_charge.visit_charge_units) AS total_count,sum(visit_charge.visit_charge_units*visit_charge.visit_charge_amount) AS total_revenue,service_charge.service_charge_amount');
		$this->db->order_by('total_count','DESC');
		$this->db->group_by('service_charge.service_charge_id');
		$visits_query = $this->db->get($table);
		
		$title = 'Procedure Report ';

		
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
			$report[$row_count][1] = 'Procedure Name';
			$report[$row_count][2] = 'Procedure Count';
			$report[$row_count][3] = 'Rate';
			$report[$row_count][4] = 'Revenue';

			//get & display all services
			
			//display all patient data in the leftmost columns
			foreach($visits_query->result() as $row)
			{
				$row_count++;
				$total_invoiced = 0;
				$service_charge_name = $row->service_charge_name;
				$total_count = $row->total_count;
				$total_revenue = $row->total_revenue;
				$service_charge_amount = $row->service_charge_amount;

				$count++;
				
				//display the patient data
				$report[$row_count][0] = $count;
				$report[$row_count][1] = $service_charge_name;
				$report[$row_count][2] = $total_count;
				$report[$row_count][3] = $service_charge_amount;
				$report[$row_count][4] = $total_revenue;
					
				
				
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}


	public function export_visit_procedures_report($service_charge_id)
	{
		$this->load->library('excel');
		
		//get all transactions
		$where = 'visit_charge.service_charge_id = service_charge.service_charge_id AND visit.visit_id = visit_charge.visit_id AND visit.patient_id = patients.patient_id';
		$table = 'visit_charge,service_charge,visit,patients';
		$inpatient_report_search = $this->session->userdata('procedure_report_search');
		
		if(!empty($inpatient_report_search))
		{
			$where .= $inpatient_report_search;
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';

		}
		if(!empty($service_charge_id))
		{
			$where .= ' AND visit_charge.service_charge_id = '.$service_charge_id;
		}
		
		$this->db->where($where);
		$this->db->select('service_charge_name,sum(visit_charge.visit_charge_units) AS total_count,sum(visit_charge.visit_charge_units*visit_charge.visit_charge_amount) AS total_revenue,service_charge.service_charge_amount,patients.patient_othernames,patients.patient_surname,visit.visit_date');
		$this->db->order_by('visit.visit_date','ASC');
		$this->db->group_by('visit_charge.visit_id');
		$visits_query = $this->db->get($table);
		
		$title = 'Procedure Report ';

		
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
			$report[$row_count][2] = 'Patient Name';
			$report[$row_count][3] = 'Procedure Count';
			$report[$row_count][4] = 'Rate';
			$report[$row_count][5] = 'Revenue';

			//get & display all services
			
			//display all patient data in the leftmost columns
			foreach($visits_query->result() as $row)
			{
				$row_count++;
				$total_invoiced = 0;
				$service_charge_name = $row->service_charge_name;
				$patient_surname = $row->patient_surname;
				$total_count = $row->total_count;
				$total_revenue = $row->total_revenue;
				$visit_date = $row->visit_date;
				$service_charge_amount = $row->service_charge_amount;

				$count++;
				
				//display the patient data
				$report[$row_count][0] = $count;
				$report[$row_count][1] = $visit_date;
				$report[$row_count][2] = $patient_surname;
				$report[$row_count][3] = $total_count;
				$report[$row_count][4] = $service_charge_amount;
				$report[$row_count][5] = $total_revenue;
					
				
				
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

}


?>