<?php
date_default_timezone_set('Africa/Nairobi');
class Reception_model extends CI_Model 
{
	/*
	*	Count all items from a table
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function count_items($table, $where, $limit = NULL,$group_by=null)
	{
		if($limit != NULL)
		{
			$this->db->limit($limit);
		}

		if($group_by != NULL)
		{
			$this->db->group_by($group_by);
		}
		$this->db->from($table);
		$this->db->where($where);
		return $this->db->count_all_results();
	}
	
	/*
	*	Retrieve all patients
	*	@param string $table
	* 	@param string $where
	*	@param int $per_page
	* 	@param int $page
	*
	*/
	public function get_all_patients($table, $where, $per_page, $page, $items = '*')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select($items);
		$this->db->where($where);
		$this->db->order_by('suffix,prefix','ASC');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	

	/*
	*	Retrieve all patients
	*	@param string $table
	* 	@param string $where
	*	@param int $per_page
	* 	@param int $page
	*
	*/
	public function get_all_patients_visit($table, $where, $per_page, $page, $items = '*')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select($items);
		$this->db->where($where);
		$this->db->order_by('patient_date','desc');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Retrieve ongoing visits
	*	@param string $table
	* 	@param string $where
	*	@param int $per_page
	* 	@param int $page
	*
	*/
	public function get_all_ongoing_visits($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit.*, visit_department.created AS visit_created, visit_department.accounts, patients.*, visit_type.visit_type_name,room_dr.room_name,room_dr.room_id');
		$this->db->where($where);
		$this->db->join('room_dr','room_dr.room_id = visit.room_id','left');
		$this->db->order_by('visit.visit_time','ASC');
		$this->db->group_by('visit.visit_id');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	public function get_all_ongoing_visits2($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit.*, patients.*, visit_type.visit_type_name');
		$this->db->where($where);
		$this->db->order_by('visit.visit_date','ASC');
		$this->db->join('visit_type','visit_type.visit_type_id = visit.visit_type','left');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	public function get_all_ongoing_appointments($table, $where, $per_page, $page, $order = NULL)
	{
		// var_dump($page);die();


		$sql = 'SELECT `visit`.*, `patients`.*, `visit_type`.`visit_type_name` FROM (`visit`, `patients`) LEFT JOIN `visit_type` ON `visit_type`.`visit_type_id` = `visit`.`visit_type` WHERE '.$where.' ORDER BY `visit`.`visit_date`, STR_TO_DATE(visit.time_start, "%l:%i %p") ASC LIMIT '.$per_page;
		//retrieve all users
		// $this->db->from($table);
		// $this->db->select('visit.*, patients.*, visit_type.visit_type_name');
		// $this->db->where($where);
		// $this->db->order_by('visit.visit_date,STR_TO_DATE(visit.time_start,\'%l:%i%p\')','ASC');
		// $this->db->join('visit_type','visit_type.visit_type_id = visit.visit_type','left');
		// $query = $this->db->get('', $per_page, $page);
		$query = $this->db->query($sql);
		
		return $query;
	}

	 
	
	/*
	*	Retrieve gender
	*
	*/
	public function get_gender()
	{
		$this->db->order_by('gender_name');
		$query = $this->db->get('gender');
		
		return $query;
	}
	
	/*
	*	Retrieve title
	*
	*/
	public function get_title()
	{
		$this->db->order_by('title_name');
		$query = $this->db->get('title');
		
		return $query;
	}
	
	/*
	*	Retrieve civil_status
	*
	*/
	public function get_civil_status()
	{
		$this->db->order_by('civil_status_name');
		$query = $this->db->get('civil_status');
		
		return $query;
	}
	
	/*
	*	Retrieve religion
	*
	*/
	public function get_religion()
	{
		$this->db->order_by('religion_name');
		$query = $this->db->get('religion');
		
		return $query;
	}
	
	/*
	*	Retrieve relationship
	*
	*/
	public function get_relationship()
	{
		$this->db->order_by('relationship_name');
		$query = $this->db->get('relationship');
		
		return $query;
	}
	
	/*
	*	Save other patient
	*
	*/
	public function save_other_patient()
	{
		$current_patient_number = $this->input->post('current_patient_number');

		$patient_phone1 = $this->input->post('patient_phone1');
		$patient_phone1 = str_replace(' ', '', $patient_phone1);


		if(!empty($current_patient_number))
		{
			// $explode = explode($current_patient_number);
			$number = $this->input->post('current_patient_number');
			$year = date('Y-m-d');

			$this->db->where('patient_number ='.$number.' AND patient_year ='.$year);
			$query = $this->db->get('patients');

			if($query->num_rows() > 0)
			{
				return FALSE;
			}
			else
			{
				$data = array(
								'patient_surname'=>ucwords(strtolower($this->input->post('patient_surname'))),
								'patient_othernames'=>ucwords(strtolower($this->input->post('patient_othernames'))),
								'title_id'=>$this->input->post('title_id'),
								'patient_date_of_birth'=>$this->input->post('patient_dob'),
								'gender_id'=>$this->input->post('gender_id'),
								'patient_email'=>$this->input->post('patient_email'),
								'patient_phone1'=>$patient_phone1,
								'patient_phone2'=>$this->input->post('patient_phone2'),
								'patient_kin_sname'=>$this->input->post('patient_kin_sname'),
								'patient_kin_othernames'=>$this->input->post('patient_kin_othernames'),
								'relationship_id'=>$this->input->post('relationship_id'),
								'patient_national_id'=>$this->input->post('patient_national_id'),
								'patient_date'=>date('Y-m-d H:i:s'),
								'created_by'=>$this->session->userdata('personnel_id'),
								'modified_by'=>$this->session->userdata('personnel_id'),
								'visit_type_id'=>3,
								'dependant_id'=>$this->input->post('dependant_id'),
								'current_patient_number'=>$this->input->post('current_patient_number'),
								'branch_code'=>$this->session->userdata('branch_code'),
								'patient_kin_phonenumber1'=>$this->input->post('next_of_kin_contact'),
								'insurance_company_id'=>$this->input->post('insurance_company_id'),
								'patient_town'=>$this->input->post('patient_town'),
								'patient_number'=>$number,
								'patient_year'=>$year
							);

							
				if($this->db->insert('patients', $data))
				{

					return $this->db->insert_id();
				}
				else{
					return FALSE;
				}
			}


		}
		else
		{
			$year = date('Y');
			// $year = str_replace('20', '', $date_year);
			$data = array(
							'patient_surname'=>ucwords(strtolower($this->input->post('patient_surname'))),
							'patient_othernames'=>ucwords(strtolower($this->input->post('patient_othernames'))),
							'title_id'=>$this->input->post('title_id'),
							'patient_date_of_birth'=>$this->input->post('patient_dob'),
							'gender_id'=>$this->input->post('gender_id'),
							'patient_email'=>$this->input->post('patient_email'),
							'patient_phone1'=>$patient_phone1,
							'patient_phone2'=>$this->input->post('patient_phone2'),
							'patient_kin_sname'=>$this->input->post('patient_kin_sname'),
							'patient_kin_othernames'=>$this->input->post('patient_kin_othernames'),
							'relationship_id'=>$this->input->post('relationship_id'),
							'patient_national_id'=>$this->input->post('patient_national_id'),
							'patient_date'=>date('Y-m-d H:i:s'),
							'patient_year'=>$year,
							'created_by'=>$this->session->userdata('personnel_id'),
							'modified_by'=>$this->session->userdata('personnel_id'),
							'visit_type_id'=>3,
							'dependant_id'=>$this->input->post('dependant_id'),
							'current_patient_number'=>$this->input->post('current_patient_number'),
							'branch_code'=>$this->session->userdata('branch_code'),
							'patient_town'=>$this->input->post('patient_town'),
							'patient_kin_phonenumber1'=>$this->input->post('next_of_kin_contact'),
							'insurance_company_id'=>$this->input->post('insurance_company_id'),
						);
			$is_appointment = $this->input->post('appointment_status');
			if($is_appointment == 0)
			{
				$prefix = $this->create_patient_number();

				if($prefix < 10)
				{
					$patient_number = '00'.$prefix.'/'.date('y');
				}
				else if($prefix < 100 AND $prefix >= 10)
				{
					$patient_number = '0'.$prefix.'/'.date('y');
				}
				else
				{
					$patient_number = $prefix.'/'.date('y');
				}


				$data['patient_number'] = $patient_number;
				$data['prefix'] = $prefix;
				$data['branch_code'] = 'N';
				$data['suffix'] = date('Y');

			}
						
			if($this->db->insert('patients', $data))
			{
				return $this->db->insert_id();
			}
			else{
				return FALSE;
			}
		}
	}
	
	/*
	*	Edit other patient
	*
	*/
	public function edit_other_patient($patient_id)
	{
		$current_patient_number = $this->input->post('current_patient_number');

		$patient_phone1 = $this->input->post('patient_phone1');
		$patient_phone1 = str_replace(' ', '', $patient_phone1);
		// if(!empty($current_patient_number))
		// {
		// 	$explode = explode('/', $current_patient_number);
		// 	$number = $explode[0];
		// 	$year = $explode[1];
		// 	// check if it exists
		// 	$this->db->where('patient_number ='.$number.' AND patient_year ='.$year);
		// 	$query = $this->db->get('patients');

		// 	if($query->num_rows() != 1)
		// 	{
		// 		return FALSE;
		// 	}
		// 	else
		// 	{
		// 		$data = array(
		// 						'patient_surname'=>ucwords(strtolower($this->input->post('patient_surname'))),
		// 						'patient_othernames'=>ucwords(strtolower($this->input->post('patient_othernames'))),
		// 						'title_id'=>$this->input->post('title_id'),
		// 						'patient_date_of_birth'=>$this->input->post('patient_dob'),
		// 						'gender_id'=>$this->input->post('gender_id'),
		// 						'patient_email'=>$this->input->post('patient_email'),
		// 						'patient_phone1'=>$patient_phone1,
		// 						'patient_phone2'=>$this->input->post('patient_phone2'),
		// 						'patient_kin_sname'=>$this->input->post('patient_kin_sname'),
		// 						'patient_kin_othernames'=>$this->input->post('patient_kin_othernames'),
		// 						'relationship_id'=>$this->input->post('relationship_id'),
		// 						'patient_national_id'=>$this->input->post('patient_national_id'),
		// 						'patient_date'=>date('Y-m-d H:i:s'),
		// 						'created_by'=>$this->session->userdata('personnel_id'),
		// 						'modified_by'=>$this->session->userdata('personnel_id'),
		// 						'visit_type_id'=>3,
		// 						'dependant_id'=>$this->input->post('dependant_id'),
		// 						'current_patient_number'=>$this->input->post('current_patient_number'),
		// 						'branch_code'=>$this->session->userdata('branch_code'),
		// 						'patient_kin_phonenumber1'=>$this->input->post('next_of_kin_contact'),
		// 						'insurance_company_id'=>$this->input->post('insurance_company_id'),
		// 						'patient_town'=>$this->input->post('patient_town'),
		// 						'patient_number'=>$number,
		// 						'patient_year'=>$year
		// 					);
		// 	}
		// }
		// else
		// {
			$data = array(
								'patient_surname'=>ucwords(strtolower($this->input->post('patient_surname'))),
								'patient_othernames'=>ucwords(strtolower($this->input->post('patient_othernames'))),
								'title_id'=>$this->input->post('title_id'),
								'patient_date_of_birth'=>$this->input->post('patient_dob'),
								'gender_id'=>$this->input->post('gender_id'),
								'patient_email'=>$this->input->post('patient_email'),
								'patient_phone1'=>$patient_phone1,
								'patient_phone2'=>$this->input->post('patient_phone2'),
								'patient_kin_sname'=>$this->input->post('patient_kin_sname'),
								'patient_kin_othernames'=>$this->input->post('patient_kin_othernames'),
								'relationship_id'=>$this->input->post('relationship_id'),
								'patient_national_id'=>$this->input->post('patient_national_id'),
								'patient_date'=>date('Y-m-d H:i:s'),
								'created_by'=>$this->session->userdata('personnel_id'),
								'modified_by'=>$this->session->userdata('personnel_id'),
								'visit_type_id'=>3,
								'dependant_id'=>$this->input->post('dependant_id'),
								'patient_town'=>$this->input->post('patient_town'),
								'current_patient_number'=>$this->input->post('current_patient_number'),
								'branch_code'=>$this->session->userdata('branch_code'),
								'patient_kin_phonenumber1'=>$this->input->post('next_of_kin_contact'),
								'insurance_company_id'=>$this->input->post('insurance_company_id')
							);
		// }
		
		
		$this->db->where('patient_id', $patient_id);
		if($this->db->update('patients', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	/*
	*	Edit other patient
	*
	*/
	public function edit_staff_patient($patient_id)
	{
		$data = array(
			'patient_phone1'=>$this->input->post('phone_number'),
			'patient_phone2'=>$this->input->post('patient_phone2')
		);
		
		$this->db->where('patient_id', $patient_id);
		if($this->db->update('patients', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	public function edit_student_patient($patient_id)
	{
		$data = array(
			'patient_phone1'=>$this->input->post('phone_number'),
			'patient_phone2'=>$this->input->post('patient_phone2')
		);
		
		$this->db->where('patient_id', $patient_id);
		if($this->db->update('patients', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	 
	function edit_staff_dependant_patient($patient_id)
	{
		$data = array(
			'patient_surname'=>ucwords(strtolower($this->input->post('patient_surname'))),
			'patient_othernames'=>ucwords(strtolower($this->input->post('patient_othernames'))),
			'title_id'=>$this->input->post('title_id'),
			'patient_date_of_birth'=>$this->input->post('patient_dob'),
			'gender_id'=>$this->input->post('gender_id'),
			'religion_id'=>$this->input->post('religion_id'),
			'civil_status_id'=>$this->input->post('civil_status_id'),
			'relationship_id'=>$this->input->post('relationship_id'),
			'modified_by'=>$this->session->userdata('personnel_id')
		);
		
		$this->db->where('patient_id', $patient_id);
		if($this->db->update('patients', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Save dependant patient
	*
	*/
	public function save_dependant_patient($dependant_staff)
	{
		$this->db->select('staff_system_id');
		$this->db->where('Staff_Number', $dependant_staff);
		$query = $this->db->get('staff');
		
		if($query->num_rows() > 0)
		{
			$res = $query->row();
			$staff_system_id = $res->staff_system_id;
			// $data = array(
			// 	'surname'=>ucwords(strtolower($this->input->post('patient_surname'))),
			// 	'other_names'=>ucwords(strtolower($this->input->post('patient_othernames'))),
			// 	'title_id'=>$this->input->post('title_id'),
			// 	'DOB'=>$this->input->post('patient_dob'),
			// 	'gender_id'=>$this->input->post('gender_id'),
			// 	'religion_id'=>$this->input->post('religion_id'),
			// 	'staff_id'=>$staff_system_id,
			// 	'civil_status_id'=>$this->input->post('civil_status_id')
			// );
			// $this->db->insert('staff_dependants', $data);
			
			$data2 = array(
				'patient_surname'=>ucwords(strtolower($this->input->post('patient_surname'))),
				'patient_othernames'=>ucwords(strtolower($this->input->post('patient_othernames'))),
				'title_id'=>$this->input->post('title_id'),
				'patient_date_of_birth'=>$this->input->post('patient_dob'),
				'gender_id'=>$this->input->post('gender_id'),
				'dependant_id'=>$dependant_staff,
				'visit_type_id'=>2,
				'relationship_id'=>$this->input->post('relationship_id'),
				'patient_date'=>date('Y-m-d H:i:s'),
				'patient_number'=>$this->create_patient_number(),
				'created_by'=>$this->session->userdata('personnel_id'),
				'modified_by'=>$this->session->userdata('personnel_id')
			);
			
			if($this->db->insert('patients', $data2))
			{
				return $this->db->insert_id();
			}
			else{
				return FALSE;
			}
		}
		
		else
		{
			return FALSE;
		}
	}
	
	/*
	*	Save dependant patient
	*
	*/
	public function save_other_dependant_patient($patient_id)
	{
		$data = array(
			'visit_type_id'=>3,
			'patient_surname'=>ucwords(strtolower($this->input->post('patient_surname'))),
			'patient_othernames'=>ucwords(strtolower($this->input->post('patient_othernames'))),
			'title_id'=>$this->input->post('title_id'),
			'patient_date_of_birth'=>$this->input->post('patient_dob'),
			'gender_id'=>$this->input->post('gender_id'),
			'religion_id'=>$this->input->post('religion_id'),
			'civil_status_id'=>$this->input->post('civil_status_id'),
			'relationship_id'=>$this->input->post('relationship_id'),
			'patient_date'=>date('Y-m-d H:i:s'),
			'patient_number'=>$this->create_patient_number(),
			'created_by'=>$this->session->userdata('personnel_id'),
			'modified_by'=>$this->session->userdata('personnel_id'),
			'dependant_id'=>$patient_id
		);
		
		if($this->db->insert('patients', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
	
	public function get_service_charges($patient_id)
	{
		$table = "service_charge";
		$where = "service_charge.service_id = 1 AND service_charge.visit_type_id = (SELECT visit_type_id FROM patients WHERE patient_id = $patient_id)";
		$items = "service_charge.service_charge_name, service_charge_id";
		$order = "service_charge_name";
		
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	
	public function get_service_charges2($visit_id)
	{
		$table = "service_charge";
		$where = "service_charge.service_id = 1 AND service_charge.visit_type_id = (SELECT visit_type FROM visit WHERE visit_id = $visit_id)";
		$items = "service_charge.service_charge_name, service_charge_id";
		$order = "service_charge_name";
		
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function get_service_charge($id)
	{
		$table = "service_charge";
		$where = "service_charge_id = $id";
		$items = "service_charge_amount AS number";
		$order = "service_charge_amount";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		foreach ($result as $rs1):
			$visit_type2 = $rs1->number;
		endforeach;
		return $visit_type2;
	}
	public function save_consultation_charge($visit_id, $service_charge_id, $service_charge)
	{
		$insert = array(
        	"visit_id" => $visit_id,
        	"service_charge_id" => $service_charge_id,
        	"visit_charge_amount" => $service_charge
    	);
		$table = "visit_charge";
		$this->load->model('database', '',TRUE);
		$this->database->insert_entry($table, $insert);
		
		return TRUE;
	}
	public function get_doctor()
	{
		$table = "personnel, personnel_job,job_title";
		$where = "personnel_job.personnel_id = personnel.personnel_id AND personnel_job.job_title_id = job_title.job_title_id AND job_title.job_title_name = 'Dentist'";
		$items = "personnel.personnel_onames, personnel.personnel_fname, personnel.personnel_id";
		$order = "personnel_onames";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}	

	public function get_all_doctors()
	{
		$this->db->select('personnel.*');
		$this->db->where('personnel.personnel_id = personnel_job.personnel_id AND personnel_job.job_title_id = job_title.job_title_id AND job_title.job_title_name = "Dentist" ');
		$this->db->order_by('personnel_fname');
		$query = $this->db->get('personnel,personnel_job,job_title');
		
		return $query;
	}	
	public function get_personnel_details($personnel_id)
	{
		$table = "personnel, job_title";
		$where = "job_title.job_title_id = personnel.job_title_id AND  personnel.personnel_id = '$personnel_id'";
		$items = "personnel.personnel_onames, personnel.personnel_fname, personnel.personnel_id";
		$order = "personnel_onames";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}	
	public function get_types()
	{
		$table = "visit_type";
		$where = "visit_type_id > 0";
		$items = "visit_type_name, visit_type_id";
		$order = "visit_type_name";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	
	
	public function patient_names2($patient_id, $visit_id = NULL)
	{
		if($visit_id == NULL)
		{
			$table = "patients";
			$where = "patient_id = ".$patient_id;
			$items = "*";
			$order = "patient_surname";
		}
		
		else
		{
			$table = "patients, visit";
			$where = "patients.patient_id = visit.patient_id AND visit.visit_id = ".$visit_id;
			$items = "patients.*, visit.ward_id, visit.patient_insurance_number, visit.patient_insurance_number, visit.inpatient, visit.close_card, visit.insurance_limit, visit.insurance_description,visit.visit_type AS visit_type_id";
			$order = "patient_surname";
		}
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$visit_type_preffix = '';
		$visit_type_id = '';
		$visit_type_name = '';
		$patient_insurance_number = '';
		$inpatient = $insurance_description = '';
		$insurance_limit = 0;
		foreach ($result as $row)
		{
			$patient_id = $row->patient_id;
			$patient_number = $row->patient_number;
			$created_by = $row->created_by;
			$modified_by = $row->modified_by;
			$created = $row->patient_date;
			$last_modified = $row->last_modified;
			$last_visit = $row->last_visit;
			$ward_id = $row->ward_id;
			$patient_national_id = $row->patient_national_id;
			$patient_othernames = $row->patient_othernames;
			$patient_surname = $row->patient_surname;
			$patient_date_of_birth = $row->patient_date_of_birth;
			$gender_id = $row->gender_id;
			$patient_phone_number = $row->patient_phone1;
			$patient_phone_number = $row->patient_phone1;
			$visit_type_id = $row->visit_type_id;
			
			$faculty ='';
			$dependant_id = '';
			$close_card = '';
			if($gender_id == 1)
			{
				$gender = 'M';
			}
			else
			{
				$gender = 'F';
			}
			if($visit_id == NULL)
			{
				// $visit_type_id = '';
				$visit_type_name = 'Other';
				$patient_insurance_number = '';
				$inpatient = $insurance_description = '';
				$insurance_limit = 0;
			}
			
			else
			{
				$insurance_limit = $row->insurance_limit;
				$insurance_description = $row->insurance_description;
				$inpatient = $row->inpatient;
				$patient_insurance_number = $row->patient_insurance_number;
				$this->db->where('visit_type_id', $visit_type_id);
				$this->db->select('visit_type_name, visit_type_preffix');
				$query = $this->db->get('visit_type');
				$visit_type_name = '';
				$close_card = $row->close_card;
				
				if($query->num_rows() > 0)
				{
					$row2 = $query->row();
					$visit_type_name = $row2->visit_type_name;
					$visit_type_preffix = $row2->visit_type_preffix;
				}
			}
		}
		// calculate patient balance
		$this->load->model('administration/administration_model');
		$account_balance = $this->administration_model->patient_account_balance($patient_id);
		// end of patient balance
		$patient['insurance_limit'] = $insurance_limit;
		$patient['visit_type_preffix'] = $visit_type_preffix;
		$patient['patient_insurance_number'] = $patient_insurance_number;
		$patient['inpatient'] = $inpatient;
		$patient['patient_id'] = $patient_id;
		$patient['account_balance'] = $account_balance;
		$patient['patient_national_id'] = $patient_national_id;
		$patient['visit_type'] = $visit_type_id;
		$patient['visit_type_name'] = $visit_type_name;
		$patient['patient_type'] = $visit_type_id;
		$patient['visit_type_id'] = $visit_type_id;
		$patient['patient_othernames'] = $patient_othernames;
		$patient['patient_surname'] = $patient_surname;
		$patient['patient_date_of_birth'] = $patient_date_of_birth;
		$patient['gender'] = $gender;
		$patient['patient_number'] = $patient_number;
		$patient['faculty'] = $faculty;
		$patient['staff_dependant_no'] = $dependant_id;
		$patient['close_card'] = $close_card;
		$patient['ward_id'] = $ward_id;
		$patient['patient_phone_number'] = $patient_phone_number;
		$patient['insurance_description'] = $insurance_description;

		// var_dump($visit_type_name); die();


		return $patient;
	}
	
	public function patient_names3($payment_id)
	{
		$table = "patients, visit, payments";
		$where = "patients.patient_id = visit.patient_id AND visit.visit_id = payments.visit_id AND payments.payment_id = ".$payment_id;
		$items = "patients.*, visit.visit_type, visit.visit_id";
		$order = "patient_surname";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		foreach ($result as $row)
		{
			$visit_id = $row->visit_id;
			$patient_id = $row->patient_id;
			$dependant_id = $row->dependant_id;
			$patient_number = $row->patient_number;
			$dependant_id = $row->dependant_id;
			$strath_no = $row->strath_no;
			$created_by = $row->created_by;
			$modified_by = $row->modified_by;
			$created = $row->patient_date;
			$last_modified = $row->last_modified;
			$last_visit = $row->last_visit;
			
			$patient_national_id = $row->patient_national_id;
			$patient_othernames = $row->patient_othernames;
			$patient_surname = $row->patient_surname;
			$patient_date_of_birth = $row->patient_date_of_birth;
			$gender_id = $row->gender_id;
			$faculty ='';
			if($gender_id == 1)
			{
				$gender = 'M';
			}
			else
			{
				$gender = 'F';
			}
			if($visit_id == NULL)
			{
				$visit_type_id = '';
				$visit_type_name = '';
			}
			
			else
			{
				$visit_type_id = $row->visit_type;
				$this->db->where('visit_type_id', $visit_type_id);
				$this->db->select('visit_type_name');
				$query = $this->db->get('visit_type');
				$visit_type_name = '';
				
				if($query->num_rows() > 0)
				{
					$row2 = $query->row();
					$visit_type_name = $row2->visit_type_name;
				}
			}
		}
		// calculate patient balance
		$this->load->model('administration/administration_model');
		$account_balance = $this->administration_model->patient_account_balance($patient_id);
		// end of patient balance
		$patient['patient_id'] = $patient_id;
		$patient['account_balance'] = $account_balance;
		$patient['patient_national_id'] = $patient_national_id;
		$patient['visit_type'] = $visit_type_id;
		$patient['visit_type_name'] = $visit_type_name;
		$patient['patient_type'] = $visit_type_id;
		$patient['visit_type_id'] = $visit_type_id;
		$patient['patient_othernames'] = $patient_othernames;
		$patient['patient_surname'] = $patient_surname;
		$patient['patient_date_of_birth'] = $patient_date_of_birth;
		$patient['gender'] = $gender;
		$patient['patient_number'] = $patient_number;
		$patient['faculty'] = $faculty;
		$patient['staff_dependant_no'] = $dependant_id;
		$patient['visit_id'] = $visit_id;
		return $patient;
	}
	
	public function get_strath_patient_data($check_id, $visit_id, $strath_no, $row, $dependant_id, $visit_type_id, $patient_id)
	{
		//staff & dependant
		if($check_id == 2)
		{
			//dependant
			if($dependant_id != 0)
			{
				$patient_type = $this->reception_model->get_patient_type($visit_type_id, $dependant_id);
				$visit_type = 'Dependant';
				
				$dependant_query = $this->reception_model->get_dependant($strath_no);
				
				if($dependant_query->num_rows() > 0)
				{
					$dependants_result = $dependant_query->row();
					
					$patient_othernames = $dependants_result->other_names;
					$patient_surname = $dependants_result->surname;
					$patient_date_of_birth = $dependants_result->DOB;
					$relationship = $dependants_result->relation;
					$gender = $dependants_result->Gender;
					$faculty = $this->get_staff_faculty_details($dependant_id);
				}
				
				else if(($row->patient_surname != '0.00') && ($row->patient_othernames != '0.00'))
				{
					$patient_othernames = $row->patient_othernames;
					$patient_surname = $row->patient_surname;
					$patient_date_of_birth = $row->patient_date_of_birth;
					$gender_id = $row->gender_id;
					// get parent faculty 
					$faculty = $this->get_staff_faculty_details($dependant_id);
					// end of parent faculty
					if($gender_id == 1)
					{
						$gender = 'M';
					}
					else
					{
						$gender = 'F';
					}
				}
				
				else
				{
					$patient_othernames = '<span class="label label-important">Dependant not found: '.$strath_no.'</span>';
					$patient_surname = $patient_id;
					$patient_date_of_birth = '';
					$relationship = '';
					$gender = '';
					$faculty ='';
				}
			}
			
			//staff
			else
			{
				$patient_type = $this->reception_model->get_patient_type($visit_type_id, $dependant_id);
				$visit_type = 'Staff';
				
				$staff_query = $this->reception_model->get_staff($strath_no);
				
				if($staff_query->num_rows() > 0)
				{
					$staff_result = $staff_query->row();
					
					$patient_surname = $staff_result->Surname;
					$patient_othernames = $staff_result->Other_names;
					$patient_date_of_birth = $staff_result->DOB;
					$patient_phone1 = $staff_result->contact;
					$gender = $staff_result->gender;
					$faculty = $staff_result->department;
				}
				
				else if(($row->patient_surname != '0.00') && ($row->patient_othernames != '0.00'))
				{
					$patient_othernames = $row->patient_othernames;
					$patient_surname = $row->patient_surname;
					$patient_date_of_birth = $row->patient_date_of_birth;
					$gender_id = $row->gender_id;
					$faculty = '';
					if($gender_id == 1)
					{
						$gender = 'M';
					}
					else
					{
						$gender = 'F';
					}
				}
				
				else
				{
					$patient_othernames = '<span class="label label-important">Staff not found: '.$strath_no.'</span>';
					$patient_surname = '';
					$patient_date_of_birth = '';
					$relationship = '';
					$gender = '';
					$patient_type = '';
					$faculty ='';
				}
			}
		}
		
		//student
		else if($check_id == 1)
		{
			$patient_type = $this->reception_model->get_patient_type($visit_type_id);
			$visit_type = 'Student';
			$student_query = $this->reception_model->get_student($strath_no);
			
			if($student_query->num_rows() > 0)
			{
				$student_result = $student_query->row();
				
				$patient_surname = $student_result->Surname;
				$patient_othernames = $student_result->Other_names;
				$patient_date_of_birth = $student_result->DOB;
				$patient_phone1 = $student_result->contact;
				$gender = $student_result->gender;
				$faculty = $student_result->faculty;
			}
				
			else if(($row->patient_surname != '0.00') && ($row->patient_othernames != '0.00'))
			{
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$gender_id = $row->gender_id;
				$faculty = '';
				
				if($gender_id == 1)
				{
					$gender = 'M';
				}
				else
				{
					$gender = 'F';
				}
			}
			
			else
			{
				$patient_othernames = '<span class="label label-important">Student not found: '.$strath_no.'</span>';
				$patient_surname = $patient_id;
				$patient_date_of_birth = '';
				$relationship = '';
				$gender = '';
				$faculty ='';
			}
		}
		
		else
		{
			$visit_type = $check_id;
			$patient_type = 'Other';
			$patient_othernames = $row->patient_othernames;
			$patient_surname = $row->patient_surname;
			$patient_date_of_birth = $row->patient_date_of_birth;
			$gender_id = $row->gender_id;
			$faculty = '';
			if($gender_id == 1)
			{
				$gender = 'M';
			}
			else
			{
				$gender = 'F';
			}
		}
		
		$patient['visit_type'] = $visit_type;
		$patient['patient_type'] = $patient_type;
		$patient['patient_othernames'] = $patient_othernames;
		$patient['patient_surname'] = $patient_surname;
		$patient['patient_date_of_birth'] = $patient_date_of_birth;
		$patient['gender'] = $gender;
		$patient['faculty'] = $faculty;
		return $patient;
	}
	public function get_staff_faculty_details($strath_no)
	{
		$this->db->from('staff');
		$this->db->select('department');
		$this->db->where('Staff_Number = \''.$strath_no.'\'');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$department_result = $query->row();
			$department = $department_result->department;
		}
		else
		{
			$department = '';
		}
		return $department;
	}
	public function get_patient_insurance($patient_id)
	{
		$table = "insurance_company";
		$where = "insurance_company_status = 1";
		$items = "*";
		$order = "insurance_company_name";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function doctors_schedule($personelle_id,$date){
		// $table = "visit,patients";
		// $where = "personnel_id = ".$personelle_id." and visit_date >= '".$date."' and time_start <> 0 and time_end <> 0";
		// $where = "visit_date = '".$date."' and visit.appointment_id = 1 AND patients.patient_id = visit.patient_id AND visit.personnel_id = ".$personelle_id;
		// $items = "*";
		// $order = "STR_TO_DATE(visit.time_start,\"'%l:%i %p'\")";
		// $order_type = "ASC";
		// $result = $this->database->select_entries_where($table, $where, $items, $order,$order_type);

		$sql = 'SELECT * FROM (`visit`, `patients`) WHERE `visit_date` = "'.$date.'" and visit.appointment_id = 1 AND patients.patient_id = visit.patient_id AND visit.personnel_id = '.$personelle_id.' ORDER BY STR_TO_DATE(visit.time_start, "%l:%i %p") ASC';

		$result = $this->db->query($sql);
		
		// var_dump($result);die();
		return $result;
	}

	public function patients_schedule($patient_id,$date){
		$table = "visit";
		$where = "patient_id = ".$patient_id." and visit_date > '".$date."' and time_start <> 0 and time_end <> 0";
		$items = "*";
		$order = "visit_id";
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function get_visit_details($visit_id){
		$table = "visit";
		$where = "visit_id = ".$visit_id."";
		$items = "*";
		$order = "visit_id";
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}


	public function get_visit_items($visit_id){
		$table = "visit";
		$where = "visit_id = ".$visit_id."";
		$items = "*";
		$order = "visit_id";
		$this->db->where($where);
		$query = $this->db->get($table);
		// $result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $query;
	}
	public function doctors_names($personelle_id){
		$table = "personnel";
		$where = "personnel_id = '$personelle_id'";
		$items = "*";
		$order = "personnel_id";
			//echo $sql;
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function rooms_names($room_id){
		$table = "room_dr";
		$where = "room_id = '$room_id'";
		$items = "*";
		$order = "room_id";
			//echo $sql;
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function get_services_per_department($department_id)
	{
		$table = "service";
		$where = "department_id = $department_id AND service_status = 1";
		$items = "*";
		$order = "department_id";
			//echo $sql;
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function get_service_charges_per_type($patient_type, $service_id)
	{
		$table = "service_charge";
		$where = "visit_type_id = $patient_type and service_id = $service_id and service_charge_status = 1"; 
		$items = "*";
		$order = "visit_type_id";
			//echo $sql;
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}

	public function get_insurance_scheme($patient_type, $service_id)
	{
		$table = "insurance_scheme";
		$where = "visit_type_id = $patient_type"; 
		$items = "*";
		$order = "insurance_scheme_name";
			//echo $sql;
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function get_service_charges_per_visit_type($patient_type)
	{
		$table = "service_charge";
		$where = "visit_type_id = $patient_type and service_charge_status = 1";
		$items = "*";
		$order = "visit_type_id";
			//echo $sql;
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function get_counseling_service_charges_per_type($patient_type){
		$table = "service_charge";
		$where = "visit_type_id = $patient_type and service_id = 11 and service_charge_status = 1";
		$items = "*";
		$order = "visit_type_id";
			//echo $sql;
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function get_counselors(){
		$table = "personnel";
		$where = "job_title_id = 8 AND authorise = 0";
		$items = "*";
		$order = "personnel_id";
			//echo $sql;
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	
	public function get_doctor2($doc_name)
	{
		$table = "personnel, job_title";
		$where = "job_title.job_title_id = personnel.job_title_id AND job_title.job_title_id = 2 AND personnel.personnel_onames = '$doc_name'";
		$items = "personnel.personnel_onames, personnel.personnel_fname, personnel.personnel_id";
		$order = "personnel_onames";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	
	public function get_patient_id_from_visit($visit_id)
	{
		$this->db->where("visit_id = ".$visit_id);
		$this->db->select("patient_id");
		$query = $this->db->get('visit');
		
		$row = $query->row();
		
		return $row->patient_id;
	}
	
	/*
	*	Retrieve a single patient's details
	*	@param int $patient_id
	*
	*/
	public function get_patient_data($patient_id)
	{
		$this->db->from('patients');
		$this->db->select('*');
		$this->db->where('patient_id = '.$patient_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Retrieve all patient dependants
	*	@param int $strath_no
	*
	*/
	public function get_all_patient_dependant($patient_id)
	{
		$this->db->from('patients');
		$this->db->select('*');
		$this->db->where('dependant_id = \''.$patient_id.'\'');
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Retrieve all appointments
	*	@param string $table
	* 	@param string $where
	*	@param int $per_page
	* 	@param int $page
	*
	*/
	public function get_all_appointments($table, $where, $per_page, $page)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit.*, patients.*');
		$this->db->where($where);
		$this->db->order_by('visit_time','desc');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	public function get_patient_details($appointments_result, $visit_type_id, $dependant_id, $strath_no)
	{
		//staff & dependant
		if($visit_type_id == 2)
		{
			//dependant
			if($dependant_id > 0)
			{
				$patient_type = $this->reception_model->get_patient_type($visit_type_id, $dependant_id);
				$visit_type = 'Dependant';
				$dependant_query = $this->reception_model->get_dependant($strath_no);
				
				if($dependant_query->num_rows() > 0)
				{
					$dependants_result = $dependant_query->row();
					$patient_othernames = $dependants_result->other_names;
					$patient_surname = $dependants_result->names;
				}
				
				else
				{
					$patient_othernames = '<span class="label label-important">Dependant not found</span>';
					$patient_surname = '';
				}
			}
			
			//staff
			else
			{
				$patient_type = $this->reception_model->get_patient_type($visit_type_id, $dependant_id);
				$staff_query = $this->reception_model->get_staff($strath_no);
				$visit_type = 'Staff';
				
				if($staff_query->num_rows() > 0)
				{
					$staff_result = $staff_query->row();
					
					$patient_surname = $staff_result->Surname;
					$patient_othernames = $staff_result->Other_names;
				}
				
				else
				{
					$patient_othernames = '<span class="label label-important">Staff not found</span>';
					$patient_surname = '';
				}
			}
		}
		
		//student
		else if($visit_type_id == 1)
		{
			$student_query = $this->reception_model->get_student($strath_no);
			$patient_type = $this->reception_model->get_patient_type($visit_type_id);
			$visit_type = 'Student';
			
			if($student_query->num_rows() > 0)
			{
				$student_result = $student_query->row();
				
				$patient_surname = $student_result->Surname;
				$patient_othernames = $student_result->Other_names;
			}
			
			else
			{
				$patient_othernames = '<span class="label label-important">Student not found</span>';
				$patient_surname = '';
			}
		}
		
		//other patient
		else
		{
			$patient_type = $this->reception_model->get_patient_type($visit_type_id);
			
			if($visit_type_id == 3)
			{
				$visit_type = 'Other';
			}
			else if($visit_type_id == 4)
			{
				$visit_type = 'Insurance';
			}
			else
			{
				$visit_type = 'General';
			}
			$row = $appointments_result->row();
			$patient_othernames = $row->patient_othernames;
			$patient_surname = $row->patient_surname;
		}
		
		$patient = $visit_type.': '.$patient_surname.' '.$patient_othernames;
		
		return $patient;
	}
	
	public function delete_patient($patient_id)
	{
		$data = array
		(
			"patient_delete" => 1,
			"deleted_by" => $this->session->userdata('personnel_id'),
			"date_deleted" => date('Y-m-d H:i:s')
		);
		
		$this->db->where('patient_id', $patient_id);
		if($this->db->update('patients', $data))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	
	public function delete_visit($visit_id)
	{
		$data = array
		(
			"visit_delete" => 1,
			"deleted_by" => $this->session->userdata('personnel_id'),
			"date_deleted" => date('Y-m-d H:i:s')
		);
		
		$this->db->where('visit_id', $visit_id);
		if($this->db->update('visit', $data))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	public function get_visit_date($visit_id)
	{
		$table = "visit";
		$where = "visit_id = ".$visit_id;
		$items = "visit_date";
		$order = "visit_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$num_rows = count($result);
		if($num_rows > 0){
			foreach($result as $key):
				$visit_date = $key->visit_date;
			endforeach;
			return $visit_date;
		}
		else
		{
			return "None";
		}
	}
	public function change_patient_type_to_others($patient_id,$visit_type_idd)
	{
		
		//  get the details
		if($visit_type_idd == 1)
		{
			// get student details from students table
			$student_rs = $this->get_student_details($patient_id);
			$num_rows = count($student_rs);
			
			if($num_rows > 0){
				foreach($student_rs as $key):
					$student_number = $key->student_Number;
					$Surname = $key->Surname;
					$Other_names = $key->Other_names;
					$DOB = $key->DOB;
					$contact = $key->contact;
					$gender = $key->gender;
					$GUARDIAN_NAME = $key->GUARDIAN_NAME;
				endforeach;
				if($gender == "Male")
				{
					$gender_id = 1;
				}
				else
				{
					$gender_id = 2;
				}
				
				$data = array
				(
					"visit_type_id" => 3,
					"strath_no" => $student_number,
					"patient_surname" => $Surname,
					"patient_othernames" => $Other_names,
					"patient_date_of_birth" => $DOB,
					"patient_phone1" => $contact,
					"gender_id" => $gender_id,
					"patient_kin_sname" => $GUARDIAN_NAME,
					"modified_by " => $this->session->userdata('personnel_id')
				);
				
				$this->db->where('patient_id', $patient_id);
				if($this->db->update('patients', $data))
				{
					return TRUE;
				}
				
				else
				{
					return FALSE;
				}
			}else{
				return FALSE;
			}
			
		}
		else
		{
			// get student details from students table
			$staff_rs = $this->get_staff_details($patient_id);
			$num_rows = count($staff_rs);
			
			if($num_rows > 0){
				foreach($staff_rs as $key):
					$Staff_Number = $key->Staff_Number;
					$Surname = $key->Surname;
					$Other_names = $key->Other_names;
					$DOB = $key->DOB;
					$contact = $key->contact;
					$gender = $key->gender;
				endforeach;
				if($gender == "M")
				{
					$gender_id = 1;
				}
				else
				{
					$gender_id = 2;
				}
				
				$data = array
				(
					"visit_type_id" => 3,
					"strath_no" => $Staff_Number,
					"patient_surname" => $Surname,
					"patient_othernames" => $Other_names,
					"patient_date_of_birth" => $DOB,
					"patient_phone1" => $contact,
					"gender_id" => $gender_id,
					"modified_by " => $this->session->userdata('personnel_id')
				);
				
				$this->db->where('patient_id', $patient_id);
				if($this->db->update('patients', $data))
				{
					return TRUE;
				}
				
				else
				{
					return FALSE;
				}
			}else{
				return FALSE;
			}
		}
	}
	public function get_student_details($patient_id)
	{
		$table = "patients,student";
		$where = "patients.patient_id = ".$patient_id." AND patients.strath_no = student.student_Number";
		$items = "student.Surname,student.Other_names,student.DOB,student.contact,student.gender,student.GUARDIAN_NAME,student.student_Number";
		$order = "student.student_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	
	}
	public function get_staff_details($patient_id)
	{
		$table = "patients,staff";
		$where = "patients.patient_id = ".$patient_id." AND patients.strath_no = staff.Staff_Number";
		$items = "staff.Surname,staff.Other_names,staff.DOB,staff.contact,staff.gender,staff.Staff_Number";
		$order = "staff.staff_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	
	}
	public function change_patient_type($patient_id)
	{
	
		// check if the staff of student exist 
		$visit_type_id = $this->input->post('visit_type_id');
		$strath_no = $this->input->post('strath_no');
		if($visit_type_id == 1){
			// check in the staff table
			$student_rs = $this->get_student_number_from_student($strath_no);
			$num_rows = count($student_rs);
			
			if($num_rows > 0){
				foreach($student_rs as $key):
					$student_number = $key->student_Number;
				endforeach;
				
				$data = array
				(
					"visit_type_id" => $visit_type_id,
					"strath_no" => $student_number
				);
				
				$this->db->where('patient_id', $patient_id);
				if($this->db->update('patients', $data))
				{
					return TRUE;
				}
				
				else
				{
					return FALSE;
				}
			}else{
			
			}
			
		}else if($visit_type_id == 3 || $visit_type_id == 4){
			// check if they exisit on the table for staff
			$check_this_people = $this->check_staff_if_exist($visit_type_id,$strath_no);
			if(count($check_this_people) > 0)
			{
				// change the patient type to 2
				$data_array = array(
				'visit_type_id'=>2
				);
				$this->db->where('patient_id', $patient_id);
				$this->db->update('patients', $data_array);
				// end of changing the patient type
				return TRUE;
			}
			else
			{
				// get the patient data
					$patient_data = $this->get_staff_details_from_patients($visit_type_id,$strath_no);
					if(count($patient_data) > 0)
					{
						foreach ($patient_data as $key) {
							# code...
							$patient_surname = $key->patient_surname;
							$patient_othernames = $key->patient_othernames;
							$patient_date_of_birth = $key->patient_date_of_birth;
							$gender_id = $key->gender_id;
							$patient_id = $key->patient_id;
							$contact = $key->patient_phone1;
						}
						if($gender_id == 1)
						{
							$gender = 'M';
						}
						else
						{
							$gender = 'F';
						}
						// insert into staff table
						if($visit_type_id == 3)
						{
							$data = array(
							'Other_names'=>ucwords(strtolower($patient_othernames)),
							'Surname'=>ucwords(strtolower($patient_surname)),
							'DOB'=>$patient_date_of_birth,
							'gender'=>$gender,
							'Staff_Number'=>$strath_no,
							'contact'=>$contact,
							'house_keeping'=>'1',
							'department'=>'Housekeeping'
							);
						}
						else
						{
							$data = array(
							'Other_names'=>ucwords(strtolower($patient_othernames)),
							'Surname'=>ucwords(strtolower($patient_surname)),
							'DOB'=>$patient_date_of_birth,
							'gender'=>$gender,
							'Staff_Number'=>$strath_no,
							'contact'=>$contact,
							'sbs'=>'1',
							'department'=>'Strathmore Business School'
							);
						}
						if($this->db->insert('staff', $data))
						{
							// change the patient type to 2
							$data_array = array(
							'visit_type_id'=>2
							);
							$this->db->where('patient_id', $patient_id);
							$this->db->update('patients', $data_array);
							// end of changing the patient type
							return TRUE;
						}
						else
						{
							return FALSE;
						}
						
						// end of inserting
					}
					else
					{
						return FALSE;
					}
				// end of getting the patient data
				
			}
			// end of checking
		}else{
			// check in the staff table
			$staff_rs = $this->get_staff_number_from_staff($strath_no);
			$num_rows = count($staff_rs);
			
			if($num_rows > 0){
				foreach($staff_rs as $key):
					$staff_number = $key->Staff_Number;
				endforeach;
				
				$data = array
				(
					"visit_type_id" => $visit_type_id,
					"strath_no" => $staff_number
				);
				
				$this->db->where('patient_id', $patient_id);
				if($this->db->update('patients', $data))
				{
					return TRUE;
				}
				
				else
				{
					return FALSE;
				}
			}else{
			// check if the patient is a staff and appears as a 
				$staff_rs = $this->get_staff_number_from_patients($strath_no);
				$num_rows = count($staff_rs);
				
				if($num_rows > 0){
					foreach($staff_rs as $key):
						$national_id = $key->patient_national_id;
					endforeach;
					
					$data = array
					(
						"visit_type_id" => $visit_type_id,
						"patient_national_id" => $national_id
					);
					
					$this->db->where('patient_id', $patient_id);
					if($this->db->update('patients', $data))
					{
						return TRUE;
					}
					
					else
					{
						return FALSE;
					}
				}else{
				}
				
			
				
			
			}
		}
	
		
	}
	public function get_staff_details_from_patients($visit_type_id,$strath_no)
	{
		if($visit_type_id == 3)
		{
			//housekeeping
			$table = "patients";
			$where = "patient_national_id = '$strath_no'";
			$items = "*";
			$order = "patients.patient_id";
			
			$result = $this->database->select_entries_where($table, $where, $items, $order);
		}
		else if($visit_type_id == 4)
		{
			// sbs
			$table = "patients";
			$where = "strath_no = '$strath_no'";
			$items = "*";
			$order = "patients.patient_id";
			
			$result = $this->database->select_entries_where($table, $where, $items, $order);
		}
		
		return $result;
	}
	public function get_staff_number_from_staff($strath_no){
		$table = "staff";
		$where = "Staff_Number = ".$strath_no;
		$items = "*";
		$order = "staff.staff_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	
	public function get_staff_number_from_patients($national_id){
		$table = "patients";
		$where = "patient_national_id = ".$national_id;
		$items = "*";
		$order = "patients.patient_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function get_student_number_from_student($strath_no)
	{
		$table = "student";
		$where = "student_Number = ".$strath_no;
		$items = "*";
		$order = "student.student_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	
	}
	public function add_sbs_patient()
	{
		$staff_type = $this->input->post('staff_type');
		$strath_no = $this->input->post('strath_no');
		
		if($staff_type == "housekeeping"){
			$data = array(
			'Other_names'=>ucwords(strtolower($this->input->post('surname'))),
			'Surname'=>ucwords(strtolower($this->input->post('other_names'))),
			'DOB'=>$this->input->post('date_of_birth'),
			'gender'=>$this->input->post('gender'),
			'Staff_Number'=>$this->input->post('strath_no'),
			'contact'=>$this->input->post('contact'),
			'house_keeping'=>'1'
			);
		}else{
			$data = array(
				'Other_names'=>ucwords(strtolower($this->input->post('surname'))),
				'Surname'=>ucwords(strtolower($this->input->post('other_names'))),
				'DOB'=>$this->input->post('date_of_birth'),
				'gender'=>$this->input->post('gender'),
				'Staff_Number'=>$this->input->post('strath_no'),
				'contact'=>$this->input->post('contact'),
				'sbs'=>'1'
			);
		}
		$check_this_people = $this->check_staff_if_exist($staff_type,$strath_no);
		if(count($check_this_people) > 0)
		{
			return FALSE;
		}
		else
		{
			if($this->db->insert('staff', $data))
			{
				// check if exist in the patients table
				
				 $check_patient = $this->check_patient_if_exist($staff_type,$strath_no);
				// count($check_patient);
				if(count($check_patient) > 0){
						return TRUE;
				}else{
					$data2 = array(
						'strath_no'=>$this->input->post('strath_no'),
						'visit_type_id'=>2,
						'patient_date'=>date('Y-m-d H:i:s'),
						'patient_number'=>$this->create_patient_number(),
						'created_by'=>$this->session->userdata('personnel_id'),
						'modified_by'=>$this->session->userdata('personnel_id')
					);
					
					if($this->db->insert('patients', $data2))
					{
						return $this->db->insert_id();
					}
					else{
						return FALSE;
					}
				}
					
			}
			
			else
			{
				return FALSE;
			}
		}
	}
	public function check_patient_if_exist($staff_type,$strath_no){
		
		$table = "patients";
		if($staff_type == "housekeeping"){
		$where = "patient_national_id = ".$strath_no;
		}else{
		$where = "strath_no = '".$strath_no."'";
		}
		$items = "*";
		$order = "patients.patient_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	
	}
	public function check_staff_if_exist($staff_type,$strath_no){
		
		$table = "staff";
		$where = "Staff_Number = '".$strath_no."'";
		$items = "*";
		$order = "staff_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	
	}
	public function bulk_add_sbs_staff()
	{
		$query = $this->db->get('staff2');
		
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			
			foreach($result as $res)
			{
				$exists = $this->strathmore_population->staff_exists($res->Staff_Number);
				
				if(!$exists)
				{
					echo 'doesn\'t exist '.$res->Staff_Number.'<br/>';
					$data = array(
						'Other_names'=>ucwords(strtolower($res->Other_names)),
						'Surname'=>ucwords(strtolower($res->Surname)),
						'Staff_Number'=>$res->Staff_Number,
						'title'=>$res->title,
						'sbs'=>'1'
					);
					if(!$this->db->insert('staff', $data))
					{
						break;
						return FALSE;
					}
				}
				
				else
				{
					echo 'Exists '.$res->Staff_Number.'<br/>';
				}
			}
		}
		
		return TRUE;
	}
	
	function random_color()
	{
		$rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
    	$color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
		
		return $color;
	}
	
	public function get_patient_data_from_visit($visit_id)
	{
		$this->db->select('visit.*, patients.*');
		$this->db->where("visit.patient_id = patients.patient_id AND visit.visit_id = ".$visit_id);
		$query = $this->db->get('visit, patients');
		
		$row = $query->row();
		
		return $row;
	}
	public function calculate_age($patient_date_of_birth)
	{
		$value = $this->dateDiff(date('y-m-d  h:i'), $patient_date_of_birth." 00:00", 'year');
		
		return $value;
	}
	public function dateDiff($time1, $time2, $interval) 
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
  	function check_patient_exist($patient_id,$visit_date){
  		$table = "visit";
		$where = "visit.patient_id =" .$patient_id ." AND visit.visit_date = '$visit_date' AND close_card = 0 AND visit.visit_delete = 0";
		$items = "*";
		$order = "visit.visit_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
  	}
	/*
	*	Retrieve a single dependant
	*	@param int $strath_no
	*
	*/
	public function get_visit_departments()
	{
		$this->db->from('departments');
		$this->db->select('*');
		$this->db->where('visit = 1');
		$this->db->order_by('department_name');
		$query = $this->db->get();
		
		return $query;
	}
	
	public function get_visit_types()
	{
		$this->db->from('visit_type');
		$this->db->select('*');
		$this->db->where('visit_type_status = 1');
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Create remove visit department
	*
	*/
	public function remove_visit_department($visit_id)
	{
		$update['visit_department_status'] = 0;
		$update['modified_by'] = $this->session->userdata('personnel_id');
		$update['last_modified'] = date('Y-m-d H:i:s');
		
		$this->db->where(array('visit_department_status' => 1, 'visit_id' => $visit_id));
		
		if($this->db->update('visit_department', $update))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	public function visit_account_balance($visit_id)
	{
		//retrieve all users
		$this->db->from('visit');
		$this->db->select('*');
		$this->db->where('visit_id = '.$visit_id);
		$this->db->order_by('visit_date','desc');
		$query = $this->db->get();
		$total_invoiced_amount = 0;
		$total_paid_amount = 0;
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$visit_id = $row->visit_id;
				$visit_date = $row->visit_date;
				$visit_date = $row->visit_date;
				$total_invoice = $this->accounts_model->total_invoice($visit_id);
				$total_payments = $this->accounts_model->total_payments($visit_id);
				$total_paid_amount = $total_paid_amount + $total_payments;
				$total_invoiced_amount = $total_invoiced_amount + $total_invoice;
				
				$invoice_number =  $visit_id;
			}
			$difference = $total_invoiced_amount -$total_paid_amount;
		}
		else
		{
			$difference = $total_invoiced_amount -$total_paid_amount;
		}
		return $difference;
	}
	
	/*
	*	Create visit department
	*
	*/
	public function set_visit_department($visit_id, $department_id, $visit_type_id)
	{
		if($this->remove_visit_department($visit_id))
		{
			$data = array(
				'visit_id'=>$visit_id,
				'department_id'=>$department_id,
				'created'=>date('Y-m-d H:i:s'),
				'created_by'=>$this->session->userdata('personnel_id'),
				'modified_by'=>$this->session->userdata('personnel_id')
			);
			
			$data['accounts'] = 1;
			
			if($visit_type_id == 1)
			{
				//check for balance > 0
				$account_balance = $this->visit_account_balance($visit_id);
				
				if($account_balance > 0)
				{
					$data['accounts'] = 0;
				}
			}
			//var_dump($data);die();
			if($this->db->insert('visit_department', $data))
			{
				return TRUE;
			}
			else{
				return FALSE;
			}
		}
		
		else
		{
			return FALSE;
		}
	}
	
	public function save_visit_consultation_charge($visit_id, $service_charge_id)
	{
		//add charge for visit
		$service_charge = $this->reception_model->get_service_charge($service_charge_id);		
		
		$visit_charge_data = array(
			"visit_id" => $visit_id,
			"service_charge_id" => $service_charge_id,
			"created_by" => $this->session->userdata("personnel_id"),
			"date" => date("Y-m-d"),
			"visit_charge_amount" => $service_charge,
			"charged"=>1
		);
		if($this->db->insert('visit_charge', $visit_charge_data))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	
	public function set_last_visit_date($patient_id, $visit_date)
	{
		$patient_date = array(
			"last_visit" => $visit_date
		);
		$this->db->where('patient_id', $patient_id);
		$this->db->update('patients', $patient_date);
	}
	
	public function create_visit($visit_date, $patient_id, $doctor_id, $insurance_limit, $insurance_number, $visit_type_id, $timepicker_start, $timepicker_end, $appointment_id, $close_card,$insurance_description,$procedure_done=NULL)
	{
		$appointment_date = date("Y-m-d", strtotime("+6 months", strtotime($visit_date)));

		$visit_data = array(
			"branch_code" => $this->session->userdata('branch_code'),
			"visit_date" => $visit_date,
			"patient_id" => $patient_id,
			"personnel_id" => $this->session->userdata('personell_id'),
			"insurance_limit" => $insurance_limit,
			"patient_insurance_number" => $insurance_number,
			"visit_type" => $visit_type_id,
			"time_start"=>$timepicker_start,
			"time_end"=>$timepicker_end,
			"appointment_id"=>$appointment_id,
			"close_card"=>$close_card,
			"procedure_done"=>$procedure_done,
			"visit_time"=>date('Y-m-d H:i:s'),
			"personnel_id"=>$doctor_id,
			"insurance_description"=>$insurance_description,
			"dental_visit"=>$this->input->post('dental_visit')
			//"room_id"=>$room_id,
		);
		$this->db->insert('visit', $visit_data);
		$visit_id = $this->db->insert_id();

		$update_where['invoice_number'] = $visit_id;

		$this->db->where('visit_id',$visit_id);
		$this->db->update('visit',$update_where);



		// chek if there is another visit before this 
		$this->db->where('patient_id = '.$patient_id.' AND visit_id < '.$visit_id);
		$query_less = $this->db->get('visit');

		$less_items = $query_less->num_rows();

		// check if there is another visit of this patient after this day
		$this->db->where('patient_id = '.$patient_id.' AND visit_id > '.$visit_id);
		$query_more = $this->db->get('visit');

		$more_items = $query_more->num_rows();


		if($less_items > 0 AND $more_items > 0)
		{
			// update the visit is like a revisit
			$visit_update['revisit'] = 2;

		}
		else if($less_items == 0 AND $more_items > 0)
		{

			// update the visit new visit
			$visit_update['revisit'] = 1;

		}
		else if($less_items == 0 AND $more_items == 0)
		{

			// update the visit new visit
			$visit_update['revisit'] = 1;

		}

		else if($less_items > 0 AND $more_items == 0)
		{

			// update the visit revisit visit
			$visit_update['revisit'] = 2;

		}

		$this->db->where('visit_id',$visit_id);
		$this->db->update('visit',$visit_update);


		$patient_update['next_appointment_date'] = $appointment_date;
		$this->db->where('patient_id',$patient_id);
		$this->db->update('patients',$patient_update);

		return $visit_id;
	}

	public function update_appointment_accounts($visit_date, $patient_id, $doctor_id, $insurance_limit, $insurance_number, $visit_type_id, $timepicker_start, $timepicker_end, $appointment_id, $close_card,$insurance_description,$visit_id,$procedure_done)
	{
		$visit_data = array(
			"visit_date" => $visit_date,
			"patient_id" => $patient_id,
			"personnel_id" => $doctor_id,
			"insurance_limit" => $insurance_limit,
			"patient_insurance_number" => $insurance_number,
			"visit_type" => $visit_type_id,
			"time_start"=>$timepicker_start,
			"time_end"=>$timepicker_end,
			"appointment_id"=>$appointment_id,
			"close_card"=>2,
			"schedule_id"=>0,
			"personnel_id"=>$doctor_id,
			"procedure_done"=>$procedure_done,
			"insurance_description"=>$insurance_description
			//"room_id"=>$room_id,
		);
		$this->db->where('visit_id',$visit_id);
		$this->db->update('visit', $visit_data);
		// $visit_id = $this->db->insert_id();
		
		return $visit_id;
	}
	
	public function coming_from($visit_id)
	{
		$where = 'visit_department.visit_id = '.$visit_id.' AND visit_department.department_id = departments.department_id AND visit_department.visit_department_status = 0';
		$this->db->select('departments.department_name');
		$this->db->where($where);
		$this->db->order_by('visit_department.last_modified','DESC');
		$query = $this->db->get('visit_department, departments', 1, 0);
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			return $row->department_name;
		}
		
		else
		{
			return 'Reception';
		}
	}
	
	public function going_to($visit_id)
	{
		$where = 'visit_department.visit_id = '.$visit_id.' AND visit_department.department_id = departments.department_id AND visit_department.visit_department_status = 1';
		$this->db->select('departments.department_name');
		$this->db->where($where);
		$query = $this->db->get('visit_department, departments', 1, 0);
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			return $row->department_name;
		}
		
		else
		{
			return 'Reception';
		}
	}
	
	public function get_visit_trail($visit_id)
	{
		$where = 'visit_department.visit_id = '.$visit_id.' AND visit_department.department_id = departments.department_id';
		$this->db->select('departments.department_name, visit_department.*, personnel.personnel_fname');
		$this->db->where($where);
		$this->db->join('personnel', 'visit_department.created_by = personnel.personnel_id', 'left');
		$this->db->order_by('visit_department.created','ASC');
		$query = $this->db->get('visit_department, departments');
		
		return $query;
	}
	/*
	*	Retrieve insurance
	*
	*/
	public function get_insurance()
	{
		$this->db->order_by('visit_type_name');
		$query = $this->db->get('visit_type');
		
		return $query;
	}
	
	public function get_student_data($strath_no)
	{
		$where = 'student_Number = '.$strath_no;
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get('student');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$student['student_number'] = $row->student_Number;
			$student['patient_othernames'] = $row->Other_names;
			$student['patient_surname'] = $row->Surname;
			$student['patient_date_of_birth'] = $row->DOB;
			$student['gender'] = $row->gender;
		}
		
		else
		{
			$student['student_number'] = '';
			$student['patient_othernames'] = '';
			$student['patient_surname'] = '<span class="label label-important">Student not found: '.$strath_no.'</span>';
			$student['patient_date_of_birth'] = '';
			$student['gender'] = '';
		}
		return $student;
	}
	public function get_staff_dependant_data($strath_no)
	{
		$where = 'staff.staff_system_id = staff_dependants.staff_id AND staff.Staff_Number = '.$strath_no;
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get('staff_dependants,staff');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$student['staff_id'] = $row->staff_id;
			$student['patient_othernames'] = $row->other_names;
			$student['patient_surname'] = $row->surname;
			$student['patient_date_of_birth'] = $row->DOB;
			$student['gender'] = $row->gender;
		}
		
		else
		{
			$student['staff_id'] = '';
			$student['patient_othernames'] = '';
			$student['patient_surname'] = '<span class="label label-important">Dependant not found: '.$strath_no.'</span>';
			$student['patient_date_of_birth'] = '';
			$student['gender'] = '';
		}
		return $student;
	}
	
	/*
	*	Retrieve all students in SUMC db
	*
	*/
	public function get_all_students($per_page, $page)
	{
		$this->db->from('student');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Retrieve all students patients in SUMC db
	*
	*/
	public function get_all_student_patients($student_no)
	{
		$this->db->from('patients');
		$this->db->where('strath_no = \''.$student_no.'\' AND visit_type_id = 1');
		$query = $this->db->get();
		
		return $query;
	}
	
	public function change_patient_id($standing_patient_id, $patient_id)
	{
		$where['patient_id'] = $patient_id;
		$items['patient_id'] = $standing_patient_id;
		
		$this->db->where($where);
		if($this->db->update('visit', $items))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	
	public function delete_duplicate_patient($patient_id)
	{
		$where['patient_id'] = $patient_id;
		
		$this->db->where($where);
		if($this->db->delete('patients'))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	public function create_patient_number()
	{
		$year = date('Y');
		//select product code
		$this->db->where('suffix = '.$year.'');
		$this->db->from('patients');
		$this->db->select('MAX(prefix) AS number');
		$this->db->order_by('patient_id','DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		// var_dump($query->result()); die();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;


			$number++;//go to the next number
			$number = $number;
		}
		else{//start generating receipt numbers
			
			$number= 1;
		}
		return $number;
	}


	public function create_invoice_number()
	{
		//select product code
		$this->db->where('invoice_number > 0');
		$this->db->from('visit');
		$this->db->select('invoice_number AS number');
		$this->db->order_by('invoice_number','DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		// var_dump($query->result()); die();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;


			$number++;//go to the next number
			$number = $number;
		}
		else{//start generating receipt numbers
			
			$number= 1;
		}
		return $number;
	}
	
	/*
	*	Retrieve all students in SUMC db
	*
	*/
	public function get_all_dependants()
	{
		$this->db->select('patients.patient_id, staff_dependants.DOB, staff_dependants.Gender, staff_dependants.surname, staff_dependants.other_names');
		$this->db->from('patients, staff_dependants');
		$this->db->where('patients.visit_type_id = 2 AND patients.strath_no > 0 AND patients.strath_no = staff_dependants.staff_dependants_id');
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Import Template
	*
	*/
	function import_template()
	{
		$this->load->library('Excel');
		
		$title = 'Oasis Patients Import Template';
		$count=1;
		$row_count=0;
		
		$report[$row_count][0] = 'Patient Number';
		$report[$row_count][1] = 'Patient Surname';
		$report[$row_count][2] = 'Patient Othernames';
		$report[$row_count][3] = 'Title (i.e. Mr)';
		$report[$row_count][4] = 'Date of Birth (i.e. YYYY/MM/DD)';
		$report[$row_count][5] = 'Civil Status';
		$report[$row_count][6] = 'Address';
		$report[$row_count][7] = 'Postal Code';
		$report[$row_count][8] = 'City';
		$report[$row_count][9] = 'Phone Number';
		$report[$row_count][10] = 'Alternate Phone';
		$report[$row_count][11] = 'Email';
		$report[$row_count][12] = 'National Id';
		$report[$row_count][13] = 'Religion';
		$report[$row_count][14] = 'Gender (i.e. M or F)';
		$report[$row_count][15] = 'Next of Kin Othernames';
		$report[$row_count][16] = 'Next of Kin Surname';
		$report[$row_count][17] = 'N.O.K Phone';
		$report[$row_count][18] = 'N.O.K Phone 2';
		$report[$row_count][19] = 'Relationship';
		$report[$row_count][19] = 'Ragistration Date';
		
		$row_count++;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	public function import_csv_products($upload_path)
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
			$response2 = $this->sort_csv_data($array);
		
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
	public function sort_csv_data($array)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);//var_dump($total_columns);die();
		
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 20))
		{
			$items['modified_by'] = $this->session->userdata('personnel_id');
			$response = '
				<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Surname</th>
						  <th>Other Names</th>
						</tr>
					  </thead>
					  <tbody>
			';
			
			//retrieve the data from array
			for($r = 1; $r < $total_rows; $r++)
			{
				$current_patient_number = $array[$r][0];
				$items['patient_surname'] = mysql_real_escape_string(ucwords(strtolower($array[$r][1])));
				$items['patient_othernames'] = mysql_real_escape_string(ucwords(strtolower($array[$r][2])));
				$title = $array[$r][3];
				$items['patient_date_of_birth'] = $array[$r][4];
				$civil_status_id = $array[$r][5];
				$items['patient_address'] = $array[$r][6];
				$items['patient_postalcode'] = $array[$r][7];
				$items['patient_town'] = $array[$r][8];
				$items['patient_phone1'] = $array[$r][9];
				$items['patient_phone2'] = $array[$r][10];
				$items['patient_email'] = $array[$r][11];
				$items['patient_national_id'] = $array[$r][12];
				$religion = $array[$r][13];
				$gender = $array[$r][14];
				$items['patient_kin_othernames'] = $array[$r][15];
				$items['patient_kin_sname'] = $array[$r][16];
				$items['patient_kin_phonenumber1'] = $array[$r][17];
				$items['patient_kin_phonenumber2'] = $array[$r][18];
				$items['patient_date'] = $array[$r][20];
				$relationship_id = $array[$r][19];
				// $items['patient_date'] = date('Y-m-d H:i:s');
				$items['created_by'] = $this->session->userdata('personnel_id');
				$items['branch_code'] = $branch_code = 'N';// $this->session->userdata('branch_code');
				$comment = '';
				
				if(isset($gender))
				{
					if($gender == 'M')
					{
						$items['gender_id'] = 1;
					}
					else if($gender == 'F')
					{
						$items['gender_id'] = 2;
					}else
					{
						$gender_id = '';
					}
				}

				$explode = explode('/', $current_patient_number);

				$prefix = $prefix_old = (int)$explode[0];
				$suffix = $suffix_old  = $explode[1];


				$items['prefix'] = $prefix = $prefix;
				$items['suffix'] = $suffix = '20'.$suffix;
				if($prefix < 10)
				{
					$patient_number = '00'.$prefix.'/'.$suffix_old;
				}
				else if($prefix < 100 AND $prefix >= 10)
				{
					$patient_number = '0'.$prefix.'/'.$suffix_old;
				}
				else
				{
					$patient_number = $prefix.'/'.$suffix_old;
				}
				$items['patient_number'] = $branch_code.$patient_number;//$this->create_patient_number();
				$items['current_patient_number'] = $branch_code.$patient_number;

				// var_dump($items);die();
				
				if(!empty($current_patient_number))
				{
					// check if the number already exists
					if($this->check_current_number_exisits($current_patient_number))
					{
						//number exists
						$comment .= '<br/>Not saved ensure you have a patient number entered'.$items['patient_surname'];
						$class = 'danger';

						if($this->db->insert('patients', $items))
						{
							$comment .= '<br/>Patient successfully added to the database';
							$class = 'success';
						}
						
						else
						{
							$comment .= '<br/>Internal error. Could not add patient to the database. Please contact the site administrator. Product code '.$items['patient_surname'];
							$class = 'warning';
						}
					}
					else
					{
						// number does not exisit
						//save product in the db
						if($this->db->insert('patients', $items))
						{
							$comment .= '<br/>Patient successfully added to the database';
							$class = 'success';
						}
						
						else
						{
							$comment .= '<br/>Internal error. Could not add patient to the database. Please contact the site administrator. Product code '.$items['patient_surname'];
							$class = 'warning';
						}
					}
				}else
				{
					$comment .= '<br/>Not saved ensure you have a patient number entered'.$items['patient_surname'];
						$class = 'danger';
				}
				
				
				$response .= '
					
						<tr class="'.$class.'">
							<td>'.$r.'</td>
							<td>'.$items['patient_number'].'</td>
							<td>'.$items['patient_othernames'].'</td>
							<td>'.$items['patient_surname'].'</td>
							<td>'.$current_patient_number.'</td>
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
			$return['response'] = 'Patient data not found';
			$return['check'] = FALSE;
		}
		
		return $return;
	}
	
	public function check_current_number_exisits($patient_number)
	{
		$this->db->where('patient_number', $patient_number);
		
		$query = $this->db->get('patients');
		
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	
	public function get_branches()
	{
		$this->db->where('branch_status = 1');
		$this->db->order_by('branch_name', 'ASC');
		return $this->db->get('branch');
	}
	
	public function get_patient_type()
	{
		$this->db->order_by('visit_type_name', 'ASC');
		return $this->db->get('visit_type');
	}

	
	public function get_wards()
	{
		$this->db->order_by('ward_name', 'ASC');
		return $this->db->get('ward');
	}
	
	public function create_inpatient_visit($visit_date, $patient_id, $doctor_id, $insurance_limit, $insurance_number, $visit_type_id, $close_card, $room_id,$insurance_description)
	{
		$visit_data = array(
			"branch_code" => $this->session->userdata('branch_code'),
			"visit_date" => $visit_date,
			"patient_id" => $patient_id,
			"personnel_id" => $doctor_id,
			"time_start" => date('H:i:s A'),
			"insurance_limit" => $insurance_limit,
			"patient_insurance_number" => $insurance_number,
			"insurance_description"=>$insurance_description,
			"visit_type" => $visit_type_id,
			"appointment_id"=> 0,
			"close_card" => $close_card,
			"visit_time"=>date('Y-m-d H:i:s'),
			"mcc" => $this->input->post("mcc".$patient_id),
			"insurance_description" => $this->input->post('insurance_description'),
			"room_id" => $room_id,
			"inpatient" => 0
		);
		$this->db->insert('visit', $visit_data);
		$visit_id = $this->db->insert_id();


		$visit_department_data = array(
			"visit_id" => $visit_id,
			"department_id" => $this->input->post('department_id'),
			"created" => date('Y-m-d'),
			"created_by" => $this->session->userdata('personnel_id'),
			"modified_by" => $this->session->userdata('personnel_id'),
		);
		$this->db->insert('visit_department', $visit_department_data);

		$this->set_last_visit_date($patient_id, $visit_date);


		
		return $visit_id;
	}
	
	public function save_admission_fee($visit_type_id, $visit_id)
	{
		//get admission fee charge
		$admission_fee = 0;
		$service_charge_id = 0;
		
		$this->db->select('service_charge_amount, service_charge_id');
		$this->db->where('visit_type_id = '.$visit_type_id.' AND service_charge_name = \'Admission fee\' AND service_charge_status = 1');
		$query = $this->db->get('service_charge');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$admission_fee = $row->service_charge_amount;
			$service_charge_id = $row->service_charge_id;
		}
		
		if($service_charge_id > 0)
		{
			$data = array(
				"visit_id" => $visit_id,
				"service_charge_id" => $service_charge_id,
				"created_by" => $this->session->userdata("personnel_id"),
				"date" => date("Y-m-d"),
				"visit_charge_amount" => $admission_fee,
			);
			
			if($this->db->insert('visit_charge', $data))
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
			return FALSE;
		}
	}
	
	public function get_inpatient_visits($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit.*, visit.visit_date AS visit_created, patients.*, visit_type.visit_type_name, ward.ward_name');
		$this->db->where($where);
		$this->db->order_by('visit_created','ASC');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	public function get_visit_bed($visit_id)
	{
		//retrieve all users
		$this->db->from('visit_bed, ward, room, bed');
		$this->db->select('ward.ward_name, room.room_name, bed.bed_number');
		$this->db->where('visit_bed.visit_bed_status = 1 AND visit_bed.bed_id = bed.bed_id AND bed.room_id = room.room_id AND room.ward_id = ward.ward_id AND visit_bed.visit_id = '.$visit_id);
		$query = $this->db->get();
		
		return $query;
	}
	// changing ksh to osh


	public function changing_to_osh()
	{
		$this->db->where('branch_code = "OSH"');
		$query = $this->db->get('patients');

		if($query->num_rows() > 0)
		{
			// get the patient in a loop 

			foreach ($query->result() as $key) {
				# code...
				$patient_number = $key->patient_number;
				$patient_id = $key->patient_id;

				$pieces = explode("-", $patient_number);
				$prefix = $pieces[0]; // piece1
				$postfix = $pieces[1]; // piece2

				$new_prefix = "OSH-".$postfix."";

				// create update statement
				$data2 = array('patient_number' => $new_prefix);
		    	$this->db->where('patient_id  ='.$patient_id);
				$this->db->update('patients',$data2);
			}

		}
	}
	
	public function is_card_held($visit_id)
	{
		$this->db->where('visit_id', $visit_id);
		$this->db->join('personnel', 'personnel.personnel_id = visit.held_by', 'left');
		$query = $this->db->get('visit');
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$close_card = $row->close_card;
			$held_by = $row->personnel_fname.' '.$row->personnel_onames;
			
			if($close_card == 7)
			{
				$this->session->set_userdata('error_message', 'You cannot close this card. It has been held by '.$held_by);
				return TRUE;
			}
			
			else
			{
				return FALSE;
			}
		}
		
		else
		{
			return FALSE;
		}
	}
	
	public function change_patient_visit($visit_date, $doctor_id, $visit_id, $ward_id)
	{
		$visit_data = array(
			"visit_date" => $visit_date,
			"personnel_id" => $doctor_id,
			"ward_id" => $ward_id,
			"inpatient" => 1
		);
		$this->db->where('visit_id', $visit_id);
		if($this->db->update('visit', $visit_data))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	
	public function get_personnel($personnel_id)
	{
		$this->db->where('personnel_id', $personnel_id);
		$query = $this->db->get('personnel');
		
		return $query;
	}
	public function close_todays_visits()
	{
		$date = date('Y-m-d');

		$this->db->select('visit_id');
		$this->db->where('visit_date < "'.$date.'" AND close_card = 0');
		$query = $this->db->get('visit');

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key) {
				# code...
				$visit_id = $key->visit_id;

				$response = $this->sync_model->syn_up_on_closing_visit($visit_id);
			}
		}
		else
		{
			$response = 'data not found';
		}
		return $response;
	}
	/*
	*	Retrieve a single dependant
	*	@param int $strath_no
	*
	*/
	public function get_visit($visit_id)
	{
		$this->db->from('visit');
		$this->db->select('*');
		$this->db->where('visit_id', $visit_id);
		$query = $this->db->get();
		
		return $query;
	}
	/*
	*	Retrieve a single dependant
	*	@param int $strath_no
	*
	*/
	public function get_visit_depts($visit_id)
	{
		$this->db->from('visit_department');
		$this->db->select('*');
		$this->db->where('visit_id', $visit_id);
		$query = $this->db->get();
		
		return $query;
	}
	/*
	*	Retrieve a single dependant
	*	@param int $strath_no
	*
	*/
	public function get_visit_charges($visit_id)
	{
		$this->db->from('visit_charge, service_charge');
		$this->db->select('*');
		$this->db->where('visit_charge.service_charge_id = service_charge.service_charge_id AND visit_id = '.$visit_id);
		$query = $this->db->get();
		
		return $query;
	}

	

	public function initiate_appointment_visit($insurance_description, $insurance_number, $insurance_description,$visit_id,$visit_type_id,$mcc,$patient_id)
	{
		$this->update_patient_detail($visit_id);
		$visit_data = array(
		
			"close_card" => 0,
			"insurance_limit" => $insurance_limit,
			"patient_insurance_number" => $insurance_number,
			"visit_type" => $visit_type_id,
			"visit_time" => date('Y-m-d H:i:s'),
			"mcc" => $mcc,
			"insurance_description"=>$insurance_description,
		);
		$this->db->where('visit_id', $visit_id);
		$this->db->update('visit', $visit_data);

		// update patients

		$this->reception_model->set_visit_department($visit_id, 4);
		
		return $visit_id;
	}
	
	public function update_visit($visit_date, $visit_id, $doctor_id, $insurance_description, $insurance_number, $visit_type_id, $timepicker_start, $timepicker_end, $appointment_id, $close_card, $visit_id)
	{
		$visit_data = array(
			"branch_code" => $this->session->userdata('branch_code'),
			"personnel_id" => $doctor_id,
			"insurance_description" => $insurance_description,
			"patient_insurance_number" => $insurance_number,
			"visit_type" => $visit_type_id,
			
			"room_id"=>$this->input->post('room_id'),
			"dental_visit"=>$this->input->post('dental_visit'),
			'schedule_id'=>0

		);
		$this->db->where('visit_id', $visit_id);
		$this->db->update('visit', $visit_data);
		
		return $visit_id;
	}
	public function get_patient_insurance_company($patient_id)
	{
		
		
		$this->db->where("insurance_company.insurance_company_id = patients.insurance_company_id AND patients.patient_id =".$patient_id);
		$this->db->select('*');
		$result = $this->db->get("patients,insurance_company");
		
		if($result->num_rows() > 0)
        {
            $result = $result->result();
            
            foreach($result as $res)
            {
                $insurance_company_id1 = $res->insurance_company_id;
                $insurance_company_name = $res->insurance_company_name;
            }
            return $insurance_company_name;
        }
        else
        {
        	return 'N/A';
        }
	}

	public function get_insurance_name_visit($visit_id)
	{
		
		$table = "visit_type,visit";
		$where = "visit_type.visit_type_id = visit.visit_type AND visit.visit_id = ".$visit_id;
		$items = "visit_type_name";
		$order = "visit_type_name";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$visit_type_name = '';
		foreach ($result as $row)
		{
			$visit_type_name = $row->visit_type_name;
		}

		return $visit_type_name;
	}
	public function get_all_appointments_dates($appoointment_date)
	{
		$this->db->where("visit.visit_date >= '".$appoointment_date."' AND visit.appointment_id = 1");
		$this->db->select('*');
		$this->db->order_by('visit_id');
		$result = $this->db->get("visit");

		return $result;
	}
	public function get_last_personnel_id($patient_id,$visit_date)
	{
		$this->db->where("visit.visit_date = '".$visit_date."' AND visit.patient_id =".$patient_id);
		$this->db->select('*');
		$this->db->order_by('visit_id');
		$result = $this->db->get("visit");
		
		if($result->num_rows() > 0)
        {
            $result = $result->result();
            
            foreach($result as $res)
            {
                $personnel_id = $res->personnel_id;
            }
            return $personnel_id;
        }
        else
        {
        	return 0;
        }
	}

	function check_patient_appointment_exist($patient_id,$visit_date){
  		$table = "visit";
		$where = "visit.patient_id =" .$patient_id ." AND visit.visit_date = '$visit_date' AND close_card = 2 AND visit.visit_delete = 0 AND appointment_id = 1 ";
		$items = "*";
		$order = "visit.visit_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
  	}
  	function check_another_appointment_exist($patient_id,$time_start,$time_end,$visit_date,$personnel_id){
  		$table = "visit";
		$where = "visit.time_end > '$time_start' AND  time_end BETWEEN '".$time_start."' AND '".$time_end."' AND close_card = 2 AND visit.visit_delete = 0 AND appointment_id = 1 AND visit.visit_date ='$visit_date' AND personnel_id = ".$personnel_id;
		$items = "*";
		$order = "visit.visit_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
  	}

  	function check_reschedule_appointment_exist($patient_id,$time_start,$time_end,$visit_date,$personnel_id){
  		$table = "visit";
		$where = "visit.time_end > '$time_start' AND  time_end BETWEEN '".$time_start."' AND '".$time_end."' AND visit.patient_id = $patient_id AND close_card = 2 AND visit.visit_delete = 0 AND appointment_id = 1 AND visit.visit_date ='$visit_date' AND personnel_id = ".$personnel_id;
		$items = "*";
		$order = "visit.visit_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
  	}

  	public function check_if_admin($personnel_id,$job_title_id)
	{
		$this->db->where('job_title_id = '.$job_title_id.' AND personnel_id ='.$personnel_id);
		$query=$this->db->get('personnel_job');
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function get_providers()
	{
		$table = "personnel,personnel_type";
		$where = "personnel.personnel_type_id = personnel_type.personnel_type_id AND personnel_type.personnel_type_name = 'Service Provider'";
		$items = "personnel.personnel_onames, personnel.personnel_fname, personnel.personnel_id";
		$order = "personnel_onames";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}	
	public function get_personnel_department($personnel_id)
	{
		$this->db->where('personnel_id = '.$personnel_id);
		$query=$this->db->get('personnel_job');

		$department_id = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$department_id = $value->department_id;
			}
		}
		else
		{
			$department_id = 0;
		}

		return $department_id;

	}

	public function  update_patient_detail($visit_id)
	{
		$this->db->where('visit_id',$visit_id);
		$query = $this->db->get('visit');
		$query_row = $query->row();

		$patient_id = $query_row->patient_id;

		$this->db->where('patient_id',$patient_id);
		$patient_query = $this->db->get('patients');
		$patient_row = $patient_query->row();

		$patient_number = $patient_row->patient_number;
		if(empty($patient_number))
		{
			$prefix = $this->create_patient_number();

			if($prefix < 10)
			{
				$patient_number = '00'.$prefix.'/'.date('y');
			}
			else if($prefix < 100 AND $prefix >= 10)
			{
				$patient_number = '0'.$prefix.'/'.date('y');
			}
			else
			{
				$patient_number = $prefix.'/'.date('y');
			}


			$array['patient_number'] = $patient_number;
			$array['prefix'] = $prefix;
			$array['suffix'] = date('Y');
			$array['patient_number'] = $patient_number;

			$this->db->where('patient_id',$patient_id);
		    $this->db->update('patients',$array);
		}
		return TRUE;


	}

	public function get_todays_appointments($date = NULL)
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		$where = 'appointments.appointment_date = \''.$date.'\' and appointments.appointment_delete = 0 AND appointments.appointment_status > 0 AND appointments.appointment_rescheduled = 0';
		
		$this->db->select('visit_date, time_start, time_end, appointments.*,appointments.appointment_id AS appointment,appointments.appointment_type,patients.patient_phone1');
		$this->db->where($where);
		$this->db->join('visit','visit.visit_id = appointments.visit_id','left');
		$this->db->join('patients','patients.patient_id = visit.visit_id','left');

		$query = $this->db->get('appointments');
		
		return $query;
	}

	public function get_all_patients_details($table, $where,$order)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('patient_id,patient_surname,patient_othernames,patient_phone1,patient_number,patient_email');
		$this->db->where($where);
		$this->db->order_by($order,'ASC');
		$query = $this->db->get('');
		
		return $query;
	}

	public function get_all_visit_type_details($table, $where,$order)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order,'ASC');
		$query = $this->db->get('');
		
		return $query;
	}
	public function get_todays_calendar_notes($date,$section,$resource_id,$featured,$branch_id)
	{
		if($resource_id == null)
		{
			$resource = '';
		}
		else
		{
			$resource = ' AND resource_id = "'.$resource_id.'"';
		}
		$branch_add ='';
		if(!empty($branch_id))
		{
			// $branch_add = 'AND branch_id = '.$branch_id.'';
		}
		$this->db->from('calendar_note');
		$this->db->select('*');
		// $this->db->where('end_date >= "'.$date.'" AND created <= "'.$date.'" AND note_delete = 0 '.$branch_add.' AND section_id = '.$section.' '.$resource.' AND featured_note = '.$featured.'');
		$this->db->where('created = "'.$date.'" AND note_delete = 0 '.$branch_add.' AND section_id = '.$section.' '.$resource.' AND featured_note = '.$featured.'');
		$query = $this->db->get('');
		
		return $query;
	}


	public function get_todays_rescheduled_patients($date,$section,$resource_id,$featured,$branch_id)
	{
		if($resource_id == null)
		{
			$resource = '';
		}
		else
		{
			$resource = ' AND resource_id = "'.$resource_id.'"';
		}
		$branch_add ='';
		if(!empty($branch_id))
		{
			// $branch_add = 'AND branch_id = '.$branch_id.'';
		}
		$this->db->from('appointments');
		$this->db->select('*');
		// $this->db->where('end_date >= "'.$date.'" AND created <= "'.$date.'" AND note_delete = 0 '.$branch_add.' AND section_id = '.$section.' '.$resource.' AND featured_note = '.$featured.'');
		$this->db->where('appointment_date = "'.$date.'"  '.$resource.' AND appointment_delete = 0 AND appointment_rescheduled = 1');
		$query = $this->db->get('');
		
		return $query;
	}


	public function get_todays_featured_notes($date,$section,$resource_id,$featured,$branch_id)
	{
		if($resource_id == null)
		{
			$resource = '';
		}
		else
		{
			$resource = ' AND resource_id = "'.$resource_id.'"';
		}
		$branch_add ='';
		if(!empty($branch_id))
		{
			// $branch_add = 'AND branch_id = '.$branch_id.'';
		}
		$this->db->from('calendar_note');
		$this->db->select('*');
		// $this->db->where('end_date >= "'.$date.'" AND created <= "'.$date.'" AND note_delete = 0 '.$branch_add.' AND section_id = '.$section.' '.$resource.' AND featured_note = '.$featured.'');
		$this->db->where('created = "'.$date.'" AND note_delete = 0 '.$branch_add.' AND section_id = '.$section.'  AND featured_note = '.$featured.'');
		$query = $this->db->get('');
		
		return $query;
	}
	public function get_notes_detail($calendar_note_id)
	{

		$this->db->from('calendar_note');
		$this->db->select('*');
		$this->db->where('calendar_note_id',$calendar_note_id);
		$query = $this->db->get('');
		
		return $query;

	}


	public function get_schedule_views()
	{

		$this->db->from('schedule_views');
		$this->db->select('*');
		$query = $this->db->get('');
		
		return $query;

	}

	public function get_schedule_list()
	{

		$this->db->from('schedule_list');
		$this->db->select('*');
		$query = $this->db->get('');
		
		return $query;

	}
	public function get_patient_recall_list($visit_id,$patient_id)
	{

		$this->db->from('recall_list,schedule_list');
		$this->db->select('recall_list.*,recall_list.created as date_created,schedule_list.*,personnel.*');
		$this->db->where('recall_list.list_id = schedule_list.list_id AND patient_id = '.$patient_id);
		$this->db->join('personnel','personnel.personnel_id = recall_list.created_by','left');
		$query = $this->db->get('');
		
		return $query;

	}

	public function get_appointment_details($appointment_id)
	{

		$this->db->from('appointments');
		$this->db->select('*');
		$this->db->where('appointment_id',$appointment_id);
		$query = $this->db->get('');
		
		return $query;

	}

	public function get_patient_appointment_details($appointment_id)
	{

		$this->db->from('appointments');
		$this->db->select('*');
		$this->db->where('appointments.appointment_id',$appointment_id);
		$this->db->join('visit','visit.visit_id = appointments.visit_id','left');
		$query = $this->db->get('');
		
		return $query;

	}

	/*
	*	Export Time report
	*
	*/
	function export_patients($category_id = null)
	{
		$this->load->library('excel');
		
		if($category_id > 0)
		{
			$add_category = ' AND category_id = '.$category_id;
			$patient_search = NULL;
		}
		else
		{
			$patient_search = $this->session->userdata('patient_search');
			$add_category = '';
		}
		//get all transactions
		$where = 'patient_delete = 0 '.$add_category;
		$table = 'patients';
		
		//$where = '(visit_type_id <> 2 OR visit_type_id <> 1) AND patient_delete = '.$delete;
		
		if(!empty($patient_search))
		{
			$where .= $patient_search;
		}
		
		else
		{
			// $where .= ' AND patients.branch_code = \''.$this->session->userdata('branch_code').'\'';
			$where .='';
		}
		
		$this->db->where($where);
		$this->db->order_by('prefix,suffix', 'ASC');
		$this->db->select('patients.*');
		$this->db->join('relationship','relationship.relationship_id = patients.relationship_id','left');
		$visits_query = $this->db->get($table);
		
		$title = 'Patients Export as at '.date('jS M Y',strtotime(date('Y-m-d')));
		// var_dump($visits_query); die();
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
			$report[$row_count][1] = 'New Patient Number';
			$report[$row_count][2] = 'Patient Number';
			$report[$row_count][3] = 'Scheme Name';
			$report[$row_count][4] = 'First Appointment Date';
			$report[$row_count][5] = 'Patient';
			$report[$row_count][6] = 'Registration Date';
			$report[$row_count][7] = 'Patient Date of Birth';
			$report[$row_count][8] = 'Patient Address';
			$report[$row_count][9] = 'Postal Code';
			$report[$row_count][10] = 'Town';
			$report[$row_count][11] = 'Phone';
			$report[$row_count][12] = 'Alternate Phone';
			$report[$row_count][13] = 'Email';
			$report[$row_count][14] = 'Next Of Kin';
			$report[$row_count][15] = 'Next of Kin Phone';
			$report[$row_count][16] = 'Relationship to Kin';
			$report[$row_count][17] = 'Occupation';
			$report[$row_count][18] = 'Place of Work';
			$report[$row_count][19] = 'Group';
			//get & display all services
			
			//display all patient data in the leftmost columns
			foreach($visits_query->result() as $row)
			{
				$row_count++;
				$total_invoiced = 0;
				$registration_date = date('jS M Y',strtotime($row->patient_date));
				$patient_id = $row->patient_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$created_by = $row->created_by;
				$modified_by = $row->modified_by;
				$deleted_by = $row->deleted_by;
				$visit_type_id = $row->visit_type_id;
				$created = $row->patient_date;
				$last_modified = $row->last_modified;
				$patient_year = $row->patient_year;
				$last_visit = $row->last_visit;
				$patient_phone1 = $row->patient_phone1;
				$patient_number = $row->patient_number;
				$patient_email = $row->patient_email;
				$patient_surname = $row->patient_surname;
				$patient_othernames = $row->patient_othernames;
				$patient_first_name = $row->patient_first_name;
				$patient_postalcode = $row->patient_postalcode;
				$patient_date_of_birth = date('jS M Y',strtotime($row->patient_date_of_birth));
				$place_of_work = $row->place_of_work;
				$occupation = $row->occupation;
				$patient_address = $row->patient_address;
				$patient_town = $row->patient_town;
				$last_visit = $row->last_visit;
				$age_group = $row->age_group;
				$relationship_name = '';
				$patient_kin_sname = $row->patient_kin_sname;
				$patient_kin_othernames = $row->patient_kin_othernames;
				$patient_kin_phonenumber1 = $row->patient_kin_phonenumber1;
				$patient_phone2 = $row->patient_phone2;
				$patient_date = $row->patient_date;
				$new_patient_number = $row->new_patient_number;
				if($patient_date != NULL)
				{
					$patient_date = date('jS M Y',strtotime($patient_date));
				}
				
				else
				{
					$patient_date = '';
				}
				

				$insurance_company = $this->reception_model->get_patient_insurance_company($patient_id);

				$last_visit_date = $this->reception_model->get_last_visit_date($patient_id);
				// var_dump($last_visit_date);die();
				if($last_visit_date != NULL)
				{
					$last_visit_date = date('jS M Y',strtotime($last_visit_date));
				}
				
				else
				{
					$last_visit_date = '';
				}

				if($last_visit_date == "0000-00-00")
				{
					$last_visit_date = '';
				}

				if($age_group == "A")
				{
					$age_group = "Adult";
				}
				else if($age_group == "D")
				{
					$age_group = "Dependant";
				}
				else
				{
					$age_group ="";
				}
				$count++;
				
				if($last_visit == "0000-00-00")
				{
					$last_visit = '';
				}

				//display the patient data
				$report[$row_count][0] = $count;
				$report[$row_count][1] = $new_patient_number;
				$report[$row_count][2] = $patient_number;
				$report[$row_count][3] = $insurance_company;
				$report[$row_count][4] = $last_visit_date;
				$report[$row_count][5] = $patient_othernames.' '.$patient_first_name.' '.$patient_surname;
				$report[$row_count][6] = $patient_date;
				$report[$row_count][7] = $patient_date_of_birth;
				$report[$row_count][8] = $patient_address;
				$report[$row_count][9] = $patient_postalcode;
				$report[$row_count][10] = $patient_town;
				$report[$row_count][11] = $patient_phone1;
				$report[$row_count][12] = $patient_phone2;
				$report[$row_count][13] = $patient_email;
				$report[$row_count][14] = $patient_kin_sname.' '.$patient_kin_othernames;
				$report[$row_count][15] = $patient_kin_phonenumber1;
				$report[$row_count][16] = $relationship_name;
				$report[$row_count][17] = $occupation;
				$report[$row_count][18] = $place_of_work;
				$report[$row_count][19] = $age_group;

					
				
				
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

	function dateDiffInDays($date1, $date2)  
	{ 
	    // Calulating the difference in timestamps 
	    $diff = strtotime($date2) - strtotime($date1); 
	      
	    // 1 day = 24 hours 
	    // 24 * 60 * 60 = 86400 seconds 
	    return abs(round($diff / 86400)); 
	} 


	/*
	*	Import Template
	*
	*/
	function import_template_list()
	{
		$this->load->library('Excel');
		
		$title = 'UHDC Patients Recall List Import Template';
		$count=1;
		$row_count=0;
		
		$report[$row_count][0] = 'File Number';
		$report[$row_count][1] = 'Patient Name';
		$report[$row_count][2] = 'Pending dental procedure';
		$report[$row_count][3] = 'Accounts';
		$report[$row_count][4] = 'Last Date Seen';
		$report[$row_count][5] = 'Remarks';
		$report[$row_count][6] = 'Recall Date';
		
		$row_count++;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}


	public function import_csv_patient_recall_list($upload_path)
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
			$response2 = $this->sort_csv_data_recall_list($array);
		
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
	public function sort_csv_data_recall_list($array)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);//var_dump($total_columns);die();
		
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 8))
		{
			$items['modified_by'] = $this->session->userdata('personnel_id');
			$response = '
				<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Number</th>
						  <th>Patient</th>
						</tr>
					  </thead>
					  <tbody>
			';
			
			//retrieve the data from array
			for($r = 1; $r < $total_rows; $r++)
			{
				$current_patient_number = $array[$r][0];
				$patient_surname = mysql_real_escape_string(ucwords(strtolower($array[$r][1])));				
				$procedure = $array[$r][2];
				$accounts = $array[$r][3];
				$last_date_seen = $array[$r][4];
				$items['summary_notes'] = $array[$r][5];
				$items['period_date'] = $array[$r][6];


				$this->db->where('patient_number = "'.$current_patient_number.'" ');
				$patients_query = $this->db->get('patients');

				if($patients_query->num_rows() > 0)
				{
					foreach ($patients_query->result() as $item_rs) {
						# code...
						$items['patient_id'] = $patient_id  = $item_rs->patient_id;
						$patient_date  = $item_rs->patient_date;
					}
				}
				else
				{
					
					$patient_id = 0;	

				}
				

				if($patient_id > 0)
				{

					// get the last visit detail 

					// $this->db->where('visit.patient_id = "'.$patient_id.'" AND visit.visit_id = appointments.visit_id AND (appointments.appointment_status = 4 OR appointments.appointment_status = 7)  ');
					$this->db->where('visit.patient_id = "'.$patient_id.'" AND visit.visit_id = appointments.visit_id ');
					$this->db->limit(1);
					$this->db->order_by('appointments.appointment_date','DESC');
					$appointments_query = $this->db->get('visit,appointments');
					if($appointments_query->num_rows() > 0)
					{
						foreach ($appointments_query->result() as $key => $value) {
							# code...
							$items['created'] = $appointment_date  = $value->appointment_date;
							$items['visit_id'] = $visit_id  = $value->visit_id;
						}
					}
					else
					{
						$items['created'] = $patient_date;
					}


					$items['list_id'] = $this->input->post('list_id');
					$items['doctor_id'] = $this->input->post('doctor_id');
				}

				// var_dump($items); die();
				if(!empty($current_patient_number))
				{
					// check if the number already exists
					if($this->check_current_number_exisits($current_patient_number))
					{
						//number exists
						$comment .= '<br/>Not saved ensure you have a patient number entered'.$items['patient_surname'];
						$class = 'danger';
						$this->db->where('patient_number',$current_patient_number);
						$this->db->update('patients', $items);
					}
					else
					{
						// number does not exisit
						//save product in the db
						if($this->db->insert('patients', $items))
						{
							$comment .= '<br/>Patient successfully added to the database';
							$class = 'success';
						}
						
						else
						{
							$comment .= '<br/>Internal error. Could not add patient to the database. Please contact the site administrator. Product code '.$items['patient_surname'];
							$class = 'warning';
						}
					}
				}else
				{
					$comment .= '<br/>Not saved ensure you have a patient number entered'.$items['patient_surname'];
						$class = 'danger';
				}
				
				
				$response .= '
					
						<tr class="'.$class.'">
							<td>'.$r.'</td>
							<td>'.$items['patient_number'].'</td>
							<td>'.$items['patient_othernames'].'</td>
							<td>'.$items['patient_surname'].'</td>
							<td>'.$current_patient_number.'</td>
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
			$return['response'] = 'Patient data not found';
			$return['check'] = FALSE;
		}
		
		return $return;
	}

	public function send_patients_to_cloud($patient_id)
	{
		// $this->db->select('patients.*');
		// $this->db->where('patient_id = '.$patient_id);
		// $unsynced_visits = $this->db->get('patients');

		// $patients['patients_list'] = array();
		
		// if($unsynced_visits->num_rows() > 0)
		// {
		// 	//delete all unsynced visits
			
		// 	foreach($unsynced_visits->result() as $res)
		// 	{
		// 		$sync_table_name = 'patients';				
		// 		array_push($patients['patients_list'], $res);
		// 	}
			
		// }
		// $this->cloud_model->send_unsynced_patients($patients);

	}

	public function send_visit_to_cloud($visit_id)
	{
		// $this->db->select('visit.*');
		// $this->db->where('visit_id = '.$visit_id);
		// $unsynced_visits = $this->db->get('visit');

		// $patients['visits_list'] = array();
		
		// if($unsynced_visits->num_rows() > 0)
		// {
		// 	//delete all unsynced visits			
		// 	foreach($unsynced_visits->result() as $res)
		// 	{
		// 		$sync_table_name = 'visit';				
		// 		array_push($patients['visits_list'], $res);
		// 	}			
		// }

		// $this->cloud_model->send_unsynced_visits($patients);
		
	}

	public function send_appointments_to_cloud($appointment_id)
	{
		// $this->db->select('appointments.*');
		// $this->db->where('appointment_id = '.$appointment_id);
		// // $this->db->limit(3);
		// $unsynced_appointments = $this->db->get('appointments');

		// $patients['appointments_list'] = array();
		
		// if($unsynced_appointments->num_rows() > 0)
		// {
		// 	//delete all unsynced appointments
			
		// 	foreach($unsynced_appointments->result() as $res)
		// 	{
		// 		$sync_table_name = 'appointments';				
		// 		array_push($patients['appointments_list'], $res);
		// 	}
			
		// }
		// $this->cloud_model->send_unsynced_appointments($patients);
		
	}
	public function get_last_visit_date($patient_id)
	{
		$this->db->where('patient_id',$patient_id);		
		$this->db->order_by('visit_date','ASC');
		// $this->db->limit(1);
		$query_database = $this->db->get('visit');

		$visit_date = '';
		if($query_database->num_rows() > 0)
		{
			foreach ($query_database->result() as $key => $value) {
				# code...
				$visit_date = $value->visit_date;
			}
		}
		return $visit_date;
		
	}
	public function export_appointments($list_id)
	{
		$this->load->library('excel');
		
		if($category_id > 0)
		{
			$add_category = ' AND appointments.appointment_status = '.$page;
			$patient_search = NULL;
		}
		else
		{
			$patient_search = $this->session->userdata('appointment_search');
			$add_category = '';
		}
		//get all transactions
		$where = 'visit.visit_delete = 0 AND appointments.visit_id = visit.visit_id AND visit.patient_id = patients.patient_id AND visit_delete = 0 '.$add_category;
		
		$table = 'visit,patients,appointments';
		$appointment_search = $this->session->userdata('appointment_search');
		// var_dump($appointment_search); die();
		if(!empty($appointment_search))
		{
			$where .= $appointment_search;
		}
		else
		{
			// $where .= ' AND patients.branch_code = \''.$this->session->userdata('branch_code').'\'';
			$where .='';
		}
		
		// $this->db->from($table);
		$this->db->select('visit.*, patients.*, visit_type.visit_type_name,appointments.*,visit.personnel_id AS doctor_id');
		$this->db->where($where);
		$this->db->order_by('visit.visit_date','ASC');
		$this->db->join('visit_type','visit_type.visit_type_id = visit.visit_type','left');
		$visits_query = $this->db->get($table);
		
		$title = 'Appointments Export';
		// var_dump($visits_query); die();
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
			$report[$row_count][1] = 'Patient Number';
			$report[$row_count][2] = 'Patient Name';
			$report[$row_count][3] = 'Appointment Date';
			$report[$row_count][4] = 'Phone';
			$report[$row_count][5] = 'Procedure';
			$report[$row_count][6] = 'Account';
			$report[$row_count][7] = 'Time Start';
			$report[$row_count][8] = 'Time End';
			$report[$row_count][9] = 'Doctor';
			$report[$row_count][10] = 'Type';
			//get & display all services
			
			//display all patient data in the leftmost columns
			foreach($visits_query->result() as $row)
			{
				$row_count++;
				$total_invoiced = 0;
				
				$visit_date = date('jS M Y',strtotime($row->visit_date));
				$visit_date_old = $row->visit_date;
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
				$personnel_id3 = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				$patient_number = $row->patient_number;
				$room_id2 = $row->room_id;
				$patient_year = $row->patient_year;
				$visit_table_visit_type = $visit_type;
				$patient_table_visit_type = $visit_type_id;
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$patient_national_id = $row->patient_national_id;
				$patient_phone = $row->patient_phone1;
				$time_start = $row->time_start;
				$time_end = $row->time_end;
				$doctor_id = $row->doctor_id;
				$procedure_done = $row->procedure_done;
				$appointment_start_time = $row->appointment_start_time;
				$appointment_end_time = $row->appointment_end_time;
				$appointment_status = $row->appointment_status;
				$event_description = $row->event_description;
				$category_id = $row->category_id;
				$new_patient_number = $row->new_patient_number;
				$uncategorised_patient_number = $row->uncategorised_patient_number;



				if($category_id == 1)
				{
					// new patient
					$patient_number = $new_patient_number;
					$number_color = 'info';
					$buttons = '';
				}
				else if($category_id == 2)
				{
					$patient_number = $patient_number;
					$number_color = 'success';
					$buttons = '';
				}
				else
				{
					$patient_number = $uncategorised_patient_number;
					$number_color = 'warning';
					$buttons = '';
				}
				
				$last_visit = $row->last_visit;
				$last_visit_date = $row->last_visit;

				if($last_visit != NULL)
				{
					$last_visit = date('jS M Y',strtotime($last_visit));
				}
				
				else
				{
					$last_visit = '';
				}
				//creators and editors
				if($personnel_query->num_rows() > 0)
				{
					$personnel_result = $personnel_query->result();
					
					foreach($personnel_result as $adm)
					{
						$doctor_id = $adm->personnel_id;
						
						if($personnel_id3 == $personnel_id2)
						{
							$doctor = $adm->personnel_fname;
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

				if($appointment_status == 3)
				{
					$status = 'Rescheduled';
					$color = 'red';
					$font = 'white';
				}
				else
				{
					$status = 'No Show';
					$color = 'black';
					$font = 'white';
				}

				$count++;
				

				//display the patient data
				$report[$row_count][0] = $count;
				$report[$row_count][1] = $patient_number;
				$report[$row_count][2] = $patient_othernames.' '.$patient_first_name.' '.$patient_surname;
				$report[$row_count][3] = $visit_date;
				$report[$row_count][4] = $patient_phone1 ;
				$report[$row_count][5] = $event_description;
				$report[$row_count][6] = $visit_type_name;
				$report[$row_count][7] = $appointment_start_time;
				$report[$row_count][8] = $appointment_end_time;
				$report[$row_count][9] = $doctor;
				$report[$row_count][10] = $status;					
				
				
			}
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

	public function get_service_charge_detail($service_charge_id)
	{
		$table = "service_charge";
		$where = "service_charge_id = ". $service_charge_id;
		$items = "*";
		$order = "service_charge_name";
		$this->db->where($where);
		$this->db->select($items);
		$result = $this->db->get($table);
		$service_charge_name = 0;
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $value) {
				# code...
				$service_charge_name = $value->service_charge_name;
			}
		}
			
		return $service_charge_name;
	}
	
}
?>