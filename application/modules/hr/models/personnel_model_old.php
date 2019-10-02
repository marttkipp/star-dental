<?php

class Personnel_model extends CI_Model 
{	
	public function upload_image($path, $location, $resize, $name, $upload, $edit = NULL)
	{
		if(!empty($_FILES[$upload]['tmp_name']))
		{
			$image = $this->session->userdata($name);
			
			if((!empty($image)) || ($edit != NULL))
			{
				if($edit != NULL)
				{
					$image = $edit;
				}
				
				//delete any other uploaded image
				if($this->file_model->delete_file($path."\\".$image, $location))
				{
					//delete any other uploaded thumbnail
					$this->file_model->delete_file($path."\\thumbnail_".$image, $location);
				}
				
				else
				{
					$this->file_model->delete_file($path."/".$image, $location);
					$this->file_model->delete_file($path."/thumbnail_".$image, $location);
				}
			}
			//Upload image
			$response = $this->file_model->upload_file($path, $upload, $resize);
			if($response['check'])
			{
				$file_name = $response['file_name'];
				$thumb_name = $response['thumb_name'];
					
				//Set sessions for the image details
				$this->session->set_userdata($name, $file_name);
			
				return TRUE;
			}
		
			else
			{
				$this->session->set_userdata('upload_error_message', $response['error']);
				
				return FALSE;
			}
		}
		
		else
		{
			$this->session->set_userdata('upload_error_message', '');
			return FALSE;
		}
	}
	public function upload_any_file($path, $location, $name, $upload, $edit = NULL)
	{
		if(!empty($_FILES[$upload]['tmp_name']))
		{
			$image = $this->session->userdata($name);
			
			if((!empty($image)) || ($edit != NULL))
			{
				if($edit != NULL)
				{
					$image = $edit;
				}
				
				//delete any other uploaded image
				if($this->file_model->delete_file($path."\\".$image, $location))
				{
					//delete any other uploaded thumbnail
					$this->file_model->delete_file($path."\\thumbnail_".$image, $location);
				}
				
				else
				{
					$this->file_model->delete_file($path."/".$image, $location);
					$this->file_model->delete_file($path."/thumbnail_".$image, $location);
				}
			}
			//Upload image
			$response = $this->file_model->upload_any_file($path, $upload);
			if($response['check'])
			{
				$file_name = $response['file_name'];
					
				//Set sessions for the image details
				$this->session->set_userdata($name, $file_name);
			
				return TRUE;
			}
		
			else
			{
				$this->session->set_userdata('upload_error_message', $response['error']);
				
				return FALSE;
			}
		}
		
		else
		{
			$this->session->set_userdata('upload_error_message', '');
			return FALSE;
		}
	}

	function upload_personnel_documents($personnel_id, $document)
	{
		$data = array(
			'document_type_id'=> $this->input->post('document_type_id'),
			'document_name'=> $this->input->post('document_item_name'),
			'document_upload_name'=> $document,
			'created_by'=> $this->session->userdata('personnel_id'),
			'modified_by'=> $this->session->userdata('personnel_id'),
			'created'=> date('Y-m-d H:i:s'),
			'personnel_id'=>$personnel_id
		);
		
		if($this->db->insert('personnel_document_uploads', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Retrieve all personnel
	*
	*/
	public function retrieve_personnel()
	{
		$this->db->where('personnel_status = 1');
		$this->db->order_by('personnel_fname');
		$query = $this->db->get('personnel');
		
		return $query;
	}	
	/*
	*	Retrieve payroll personnel
	*
	*/
	public function retrieve_payroll_personnel($where)
	{
		$this->db->where($where);
		$this->db->select('personnel.*, bank_branch.bank_branch_code');
		$this->db->group_by('personnel.personnel_id');
		$this->db->join('bank_branch', 'bank_branch.bank_branch_id = personnel.bank_branch_id', 'left');
		$query = $this->db->get('personnel, payroll_item');
		
		return $query;
	}	
	/*
	*	Retrieve all personnel
	*
	*/
	public function all_personnel()
	{
		$this->db->where('personnel_status = 1');
		$query = $this->db->get('personnel');
		
		return $query;
	}
	
	/*
	*	Retrieve all parent personnel
	*
	*/
	public function all_parent_personnel($order = 'personnel_name')
	{
		$this->db->where('personnel_status = 1 AND personnel_parent = 0');
		$this->db->order_by($order, 'ASC');
		$query = $this->db->get('personnel');
		
		return $query;
	}
	/*
	*	Retrieve all children personnel
	*
	*/
	public function all_child_personnel()
	{
		$this->db->where('personnel_status = 1 AND personnel_parent > 0');
		$this->db->order_by('personnel_name', 'ASC');
		$query = $this->db->get('personnel');
		
		return $query;
	}
	
	/*
	*	Retrieve all personnel
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_personnel($table, $where, $per_page, $page, $order = 'personnel_name', $order_method = 'ASC')
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
	*	Add a new personnel
	*	@param string $image_name
	*
	*/
	public function add_personnel()
	{
		$data = array(
			'personnel_onames'=>ucwords(strtolower($this->input->post('personnel_onames'))),
			'personnel_fname'=>ucwords(strtolower($this->input->post('personnel_fname'))),
			'branch_id'=>$this->input->post('branch_id'),
			'personnel_dob'=>$this->input->post('personnel_dob'),
			'personnel_email'=>$this->input->post('personnel_email'),
			'gender_id'=>$this->input->post('gender_id'),
			'personnel_phone'=>$this->input->post('personnel_phone'),
			'civilstatus_id'=>$this->input->post('civil_status_id'),
			'personnel_address'=>$this->input->post('personnel_address'),
			'personnel_locality'=>$this->input->post('personnel_locality'),
			'title_id'=>$this->input->post('title_id'),
			'personnel_number'=>$this->input->post('personnel_number'),
			'personnel_city'=>$this->input->post('personnel_city'),
			'personnel_post_code'=>$this->input->post('personnel_post_code'),
			'personnel_type_id'=>$this->input->post('personnel_type_id'),
			'personnel_national_id_number'=>$this->input->post('personnel_national_id_number'),
			'personnel_kra_pin' => $this->input->post('personnel_kra_pin'),
			'bank_branch_id' => $this->input->post('bank_branch_id'),
			'bank_account_number'=>$this->input->post('bank_account_number'),
			'cost_center' => $this->input->post('cost_center'),
			'engagement_date'=>$this->input->post('engagement_date')
		);
		
		if($this->db->insert('personnel', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Update an existing personnel
	*	@param string $image_name
	*	@param int $personnel_id
	*
	*/
	public function edit_personnel($personnel_id, $image)
	{
		$data = array(
			'personnel_onames'=>ucwords(strtolower($this->input->post('personnel_onames'))),
			'personnel_fname'=>ucwords(strtolower($this->input->post('personnel_fname'))),
			'branch_id'=>$this->input->post('branch_id'),
			'personnel_dob'=>$this->input->post('personnel_dob'),
			'personnel_email'=>$this->input->post('personnel_email'),
			'gender_id'=>$this->input->post('gender_id'),
			'personnel_phone'=>$this->input->post('personnel_phone'),
			'civilstatus_id'=>$this->input->post('civil_status_id'),
			'personnel_address'=>$this->input->post('personnel_address'),
			'personnel_locality'=>$this->input->post('personnel_locality'),
			'title_id'=>$this->input->post('title_id'),
			'personnel_number' => $this->input->post('personnel_number'),
			'personnel_city' => $this->input->post('personnel_city'),
			'personnel_post_code' => $this->input->post('personnel_post_code'),
			'bank_account_number' => $this->input->post('bank_account_number'),
			'bank_branch_id' => $this->input->post('bank_branch_id'),
			'personnel_nssf_number' => $this->input->post('personnel_nssf_number'),
			'personnel_kra_pin' => $this->input->post('personnel_kra_pin'),
			'personnel_nhif_number' => $this->input->post('personnel_nhif_number'),
			'personnel_national_id_number' => $this->input->post('personnel_national_id_number'),
			'image' => $image,
			'bank_branch_id' => $this->input->post('bank_branch_id'),
			'personnel_type_id'=>$this->input->post('personnel_type_id'),
			'cost_center' => $this->input->post('cost_center'),
			'engagement_date'=>$this->input->post('engagement_date')
		);
		
		$this->db->where('personnel_id', $personnel_id);
		if($this->db->update('personnel', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	function get_document_uploads($personnel_id)
	{
		$this->db->from('personnel_document_uploads, document_type');
		$this->db->select('*');
		$this->db->where('personnel_document_uploads.document_type_id = document_type.document_type_id AND personnel_id = '.$personnel_id);
		$query = $this->db->get();
		
		return $query;
	}

	public function update_personnel_account_details($personnel_id)
	{
		$data = array(
			'personnel_username'=>$this->input->post('personnel_username'),
			'personnel_account_status'=>$this->input->post('personnel_account_status'),
		);
		
		$this->db->where('personnel_id', $personnel_id);
		if($this->db->update('personnel', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}	
	}
	public function update_personnel_roles($personnel_id)
	{
		$section_id = $this->input->post('section_id');
		$child_id = $this->input->post('child_id');
		if($child_id > 0)
		{

			$update_section = $child_id;
		}
		else
		{

			$update_section = $section_id;
		}

		
		$this->db->from('personnel_section,section');
		$this->db->select('*');
		$this->db->where('personnel_section.section_id = section.section_id AND personnel_section.section_id = '.$update_section.' AND personnel_section.personnel_id ='.$personnel_id);
		$query = $this->db->get();

		if($query->num_rows() > 0)
		{
			$row = $query->row;
			$section_parent = $row->section_parent;

			if($section_parent > 0 AND $section_parent)
			{
				$update_section = $section_parent;
				$data = array(
					'personnel_id'=>$personnel_id,
					'section_id'=>$update_section,
					'created_by'=>$this->session->userdata('personnel_id'),
					'modified_by'=>$this->session->userdata('personnel_id'),
					'created'=>date('Y-m-d H:i:s'),
					'last_modified'=>date('Y-m-d H:i:s'),
				);
			}
			else
			{
				$update_section = $update_section;
				$data = array(
					'personnel_id'=>$personnel_id,
					'section_id'=>$update_section,
					'created_by'=>$this->session->userdata('personnel_id'),
					'modified_by'=>$this->session->userdata('personnel_id'),
					'created'=>date('Y-m-d H:i:s'),
					'last_modified'=>date('Y-m-d H:i:s'),
				);
			}
			$this->db->where('personnel_id', $personnel_id);
			if($this->db->update('personnel_section', $data))
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
					'personnel_id'=>$personnel_id,
					'section_id'=>$update_section,
					'created_by'=>$this->session->userdata('personnel_id'),
					'modified_by'=>$this->session->userdata('personnel_id'),
					'created'=>date('Y-m-d H:i:s'),
					'last_modified'=>date('Y-m-d H:i:s'),
			);
			if($this->db->insert('personnel_section', $data))
			{
				return TRUE;
			}
			else{
				return FALSE;
			}	

		}

	}
	
	/*
	*	get a single personnel's children
	*	@param int $personnel_id
	*
	*/
	public function get_sub_personnel($personnel_id)
	{
		//retrieve all users
		$this->db->from('personnel');
		$this->db->select('*');
		$this->db->where('personnel_parent = '.$personnel_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	get a single personnel's details
	*	@param int $personnel_id
	*
	*/
	public function get_personnel($personnel_id)
	{
		//retrieve all users
		$this->db->from('personnel');
		$this->db->select('*');
		$this->db->where('personnel_id = '.$personnel_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Delete an existing personnel
	*	@param int $personnel_id
	*
	*/
	public function delete_personnel($personnel_id)
	{
		//delete parent
		if($this->db->delete('personnel', array('personnel_id' => $personnel_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Delete an existing personnel
	*	@param int $personnel_id
	*
	*/
	public function delete_document_scan($document_upload_id)
	{
		//delete parent
		if($this->db->delete('personnel_document_uploads', array('document_upload_id' => $document_upload_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Activate a deactivated personnel
	*	@param int $personnel_id
	*
	*/
	public function activate_personnel($personnel_id)
	{
		$data = array(
				'personnel_status' => 1
			);
		$this->db->where('personnel_id', $personnel_id);
		

		if($this->db->update('personnel', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated personnel
	*	@param int $personnel_id
	*
	*/
	public function deactivate_personnel($personnel_id)
	{
		$data = array(
				'personnel_status' => 0
			);
		$this->db->where('personnel_id', $personnel_id);
		
		if($this->db->update('personnel', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
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
	*	Select get_job_titles
	*
	*/
	public function get_job_titles()
	{
		$this->db->select('*');
		$this->db->order_by('job_title_name', 'ASC');
		$query = $this->db->get('job_title');
		
		return $query;
	}
	
	/*
	*	get a single personnel's details
	*	@param int $personnel_id
	*
	*/
	public function get_emergency_contacts($personnel_id)
	{
		//retrieve all users
		$this->db->from('personnel_emergency,relationship');
		$this->db->select('*');
		$this->db->where('personnel_emergency.relationship_id = relationship.relationship_id AND personnel_emergency.personnel_id ='.$personnel_id);
		$this->db->order_by('personnel_emergency_fname');
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	get a single personnel's details
	*	@param int $personnel_id
	*
	*/
	public function get_personnel_dependants($personnel_id)
	{
		//retrieve all users
		$this->db->from('personnel_dependant,relationship');
		$this->db->select('*');
		$this->db->where('personnel_dependant.relationship_id = relationship.relationship_id AND personnel_dependant.personnel_id = '.$personnel_id);
		$this->db->order_by('personnel_dependant_fname');
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	get a single personnel's details
	*	@param int $personnel_id
	*
	*/
	public function get_personnel_jobs($personnel_id)
	{
		//retrieve all users
		$this->db->select('personnel_job.*, job_title.job_title_name, departments.department_name');
		$this->db->from('personnel_job, job_title');
		$this->db->join('departments', 'departments.department_id = personnel_job.department_id', 'LEFT');
		$this->db->order_by('personnel_job.job_commencement_date', 'DESC');
		$this->db->where('personnel_job.job_title_id = job_title.job_title_id AND personnel_job.personnel_id = '.$personnel_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	public function get_personnel_leave($personnel_id)
	{
		//retrieve all users
		$this->db->from('leave_duration, leave_type');
		$this->db->select('leave_duration.*, leave_type.leave_type_name, leave_type.leave_type_count, leave_type.leave_days');
		$this->db->order_by('leave_type.leave_type_name');
		$this->db->where('leave_duration.leave_type_id = leave_type.leave_type_id AND leave_duration.personnel_id = '.$personnel_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	public function get_leave_balance($personnel_id, $leave_type_id)
	{
		//retrieve all users
		$this->db->from('leave_duration, leave_type');
		$this->db->select('leave_duration.*, leave_type.leave_type_name, leave_type.leave_type_count, leave_type.leave_days');
		$this->db->order_by('leave_type.leave_type_name');
		$this->db->where('leave_duration.leave_type_id = leave_type.leave_type_id AND leave_duration.personnel_id = '.$personnel_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	public function get_leave_types()
	{
		$table = "leave_type";
		$where = "leave_type_status = 0";
		$items = "leave_type_id, leave_type_name";
		$order = "leave_type_name";
		
		$this->db->where($where);
		$this->db->order_by($order);
		$result = $this->db->get($table);
		
		return $result;
	}
	
	/*
	*	get a single personnel's roles
	*	@param int $personnel_id
	*
	*/
	public function get_personnel_roles($personnel_id)
	{
		//retrieve all users
		$this->db->from('personnel_section, section');
		$this->db->select('personnel_section.*, section.section_name, section.section_position, section.section_parent, section.section_icon');
		$this->db->order_by('section_parent', 'ASC');
		$this->db->order_by('section_position', 'ASC');
		$this->db->where('personnel_section.section_id = section.section_id AND personnel_section.personnel_id = '. $personnel_id);
		$query = $this->db->get();
		
		return $query;
	}


	/*
	*	Emergency listings
	*	@param int $personnel_id
	*
	*/
	public function add_emergency_contact($personnel_id)
	{
		$data = array(
			'personnel_emergency_onames'=>ucwords(strtolower($this->input->post('personnel_emergency_onames'))),
			'personnel_emergency_fname'=>ucwords(strtolower($this->input->post('personnel_emergency_fname'))),
			'personnel_emergency_email'=>$this->input->post('personnel_emergency_email'),
			'gender_id'=>$this->input->post('gender_id'),
			'personnel_id'=>$personnel_id,
			'personnel_emergency_phone'=>$this->input->post('personnel_emergency_phone'),
			'relationship_id'=>$this->input->post('relationship_id'),
			'personnel_emergency_locality'=>$this->input->post('personnel_emergency_locality'),
			'title_id'=>$this->input->post('title_id'),
			//'personnel_emergency_status'=>$this->input->post('personnel_emergency_status'),
			'created_by'=>$this->session->userdata('personnel_id'),
			'modified_by'=>$this->session->userdata('personnel_id'),
			'created'=>date('Y-m-d H:i:s')
		);
		
		if($this->db->insert('personnel_emergency', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}

	/*
	*	Activate a deactivated personnel
	*	@param int $personnel_id
	*
	*/
	public function activate_emergency_contact($personnel_emergency_id)
	{
		$data = array(
				'personnel_emergency_status' => 1
			);
		$this->db->where('personnel_emergency_id', $personnel_emergency_id);
		

		if($this->db->update('personnel_emergency', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated personnel
	*	@param int $personnel_emergency_id
	*
	*/
	public function deactivate_emergency_contact($personnel_emergency_id)
	{
		$data = array(
				'personnel_emergency_status' => 0
			);
		$this->db->where('personnel_emergency_id', $personnel_emergency_id);
		
		if($this->db->update('personnel_emergency', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	/*
	*	Delete an existing personnel
	*	@param int $personnel_id
	*
	*/
	public function delete_personnel_emergency_contact($personnel_emergency_id)
	{
			if($this->db->delete('personnel_emergency', array('personnel_emergency_id' => $personnel_emergency_id)))
			{
				return TRUE;
			}
			else{
				return FALSE;
			}
		
	}

	/*
	*	Emergency listings
	*	@param int $personnel_id
	*
	*/
	public function add_dependant_contact($personnel_id)
	{
		$data = array(
			'personnel_dependant_onames'=>ucwords(strtolower($this->input->post('personnel_dependant_onames'))),
			'personnel_dependant_fname'=>ucwords(strtolower($this->input->post('personnel_dependant_fname'))),
			'personnel_dependant_email'=>$this->input->post('personnel_dependant_email'),
			'gender_id'=>$this->input->post('gender_id'),
			'personnel_id'=>$personnel_id,
			'personnel_dependant_phone'=>$this->input->post('personnel_dependant_phone'),
			'relationship_id'=>$this->input->post('relationship_id'),
			'personnel_dependant_locality'=>$this->input->post('personnel_dependant_locality'),
			'title_id'=>$this->input->post('title_id'),
			//'personnel_dependant_status'=>$this->input->post('personnel_dependant_status'),
			'created_by'=>$this->session->userdata('personnel_id'),
			'modified_by'=>$this->session->userdata('personnel_id'),
			'created'=>date('Y-m-d H:i:s')
		);
		
		if($this->db->insert('personnel_dependant', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}

	/*
	*	Activate a deactivated personnel
	*	@param int $personnel_id
	*
	*/
	public function activate_dependant_contact($personnel_dependant_id)
	{
		$data = array(
				'personnel_dependant_status' => 1
			);
		$this->db->where('personnel_dependant_id', $personnel_dependant_id);
		

		if($this->db->update('personnel_dependant', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated personnel
	*	@param int $personnel_dependant_id
	*
	*/
	public function deactivate_dependant_contact($personnel_dependant_id)
	{
		$data = array(
				'personnel_dependant_status' => 0
			);
		$this->db->where('personnel_dependant_id', $personnel_dependant_id);
		
		if($this->db->update('personnel_dependant', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	/*
	*	Delete an existing personnel
	*	@param int $personnel_id
	*
	*/
	public function delete_personnel_dependant_contact($personnel_dependant_id)
	{
			if($this->db->delete('personnel_dependant', array('personnel_dependant_id' => $personnel_dependant_id)))
			{
				return TRUE;
			}
			else{
				return FALSE;
			}
		
	}



	/*
	*	Emergency listings
	*	@param int $personnel_id
	*
	*/
	public function add_personnel_job($personnel_id)
	{
		$data = array(
			
			'department_id'=>$this->input->post('department_id'),
			'job_title_id'=>$this->input->post('job_title_id'),
			'job_commencement_date'=>$this->input->post('job_commencement_date'),
			'personnel_id'=>$personnel_id,
			'created_by'=>$this->session->userdata('personnel_id'),
			'modified_by'=>$this->session->userdata('personnel_id'),
			'created'=>date('Y-m-d H:i:s')
		);
		
		if($this->db->insert('personnel_job', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}

	/*
	*	Activate a deactivated personnel
	*	@param int $personnel_id
	*
	*/
	public function activate_personnel_job($personnel_job_id)
	{
		$data = array(
				'personnel_job_status' => 1
			);
		$this->db->where('personnel_job_id', $personnel_job_id);
		

		if($this->db->update('personnel_job', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated personnel
	*	@param int $personnel_job_id
	*
	*/
	public function deactivate_personnel_job($personnel_job_id)
	{
		$data = array(
				'personnel_job_status' => 0
			);
		$this->db->where('personnel_job_id', $personnel_job_id);
		
		if($this->db->update('personnel_job', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	/*
	*	Delete an existing personnel
	*	@param int $personnel_id
	*
	*/
	public function delete_personnel_job($personnel_job_id)
	{
		if($this->db->delete('personnel_job', array('personnel_job_id' => $personnel_job_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	public function add_personnel_leave($personnel_id)
	{
		$items = array(
						'personnel_id' => $personnel_id,
						'start_date' => $this->input->post("start_date"),
						'end_date' => $this->input->post("end_date"),
						'leave_type_id' => $this->input->post("leave_type_id"),
						'created_by'=>$this->session->userdata('personnel_id'),
						'modified_by'=>$this->session->userdata('personnel_id'),
						'created'=>date('Y-m-d H:i:s')
					  );
		if($this->db->insert("leave_duration", $items))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	/*
	*	Activate a deactivated personnel
	*	@param int $personnel_id
	*
	*/
	public function activate_personnel_leave($leave_duration_id)
	{
		$data = array(
				'personnel_leave_status' => 1
			);
		$this->db->where('leave_duration_id', $leave_duration_id);
		

		if($this->db->update('leave_duration', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated personnel
	*	@param int $leave_duration_id
	*
	*/
	public function deactivate_personnel_leave($leave_duration_id)
	{
		$data = array(
				'personnel_leave_status' => 0
			);
		$this->db->where('leave_duration_id', $leave_duration_id);
		
		if($this->db->update('leave_duration', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	/*
	*	Delete an existing personnel
	*	@param int $personnel_id
	*
	*/
	public function delete_personnel_leave($leave_duration_id)
	{
		if($this->db->delete('leave_duration', array('leave_duration_id' => $leave_duration_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	/*
	*	Delete an existing personnel
	*	@param int $personnel_id
	*
	*/
	public function delete_personnel_role($personnel_section_id)
	{
		if($this->db->delete('personnel_section', array('personnel_section_id' => $personnel_section_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function get_departments()
	{
		$this->db->where('department_status = 1');
		$this->db->order_by('department_name', 'ASC');
		
		return $this->db->get('departments');
	}
	
	/*
	*	Activate a deactivated personnel
	*	@param int $personnel_id
	*
	*/
	public function edit_invoice_authorize($personnel_id)
	{
		$data = array(
				'authorize_invoice_changes' => $this->input->post('authorize_invoice_changes')
			);
		$this->db->where('personnel_id', $personnel_id);
		

		if($this->db->update('personnel', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	/*
	*	Activate a deactivated personnel
	*	@param int $personnel_id
	*
	*/
	public function edit_store_authorize($personnel_id)
	{
		$data = array(
				'store_id' => $this->input->post('store_id'),
				'personnel_id' => $personnel_id,
				'created'=>date("Y-m-d H:i:s"),
				'created_by'=>$this->session->userdata('personnel_id'),
				'modified_by'=>$this->session->userdata('personnel_id')
			);
		

		if($this->db->insert('personnel_store', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Select get personnel types
	*
	*/
	public function get_personnel_types()
	{
		$this->db->select('*');
		$this->db->order_by('personnel_type_name', 'ASC');
		$query = $this->db->get('personnel_type');
		
		return $query;
	}
	
	//import template
	function import_personnel_template()
	{
		$this->load->library('Excel');
		
		$title = 'Personnel Import Template';
		$count=1;
		$row_count=0;
		
		$report[$row_count][0] = 'Employee Number';
		$report[$row_count][1] = 'First name';
		$report[$row_count][2] = 'Middle name';
		$report[$row_count][3] = 'Last name';
		$report[$row_count][4] = 'NSSF Number';
		$report[$row_count][5] = 'NHIF Number';
		$report[$row_count][6] = 'KRA PIN';
		$report[$row_count][7] = 'Gender (Male -1, Female -2)';
		$report[$row_count][8] = 'ID Number';
		$report[$row_count][9] = 'Bank Branch Code';
		$report[$row_count][10] = 'Bank Account Number';
		$report[$row_count][11] = 'Branch ID';
		$report[$row_count][12] = 'Email Address';
		$report[$row_count][13] = 'Staff ID';
		$report[$row_count][14] = 'Cost Center';
		$report[$row_count][15] = 'Date of Engagement (YYYY-MM-DD)';
		$report[$row_count][16] = 'Date of Exit (YYYY-MM-DD)';
		
		$row_count++;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	//import personnel
	public function import_csv_personnel($upload_path)
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
			$response2 = $this->sort_personnel_data($array);
		
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
	//sort personnel imported data
	public function sort_personnel_data($array)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);//var_dump($array);die();
		
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 17))
		{
			$items['modified_by'] = $this->session->userdata('personnel_id');
			$response = '
				<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Member Number</th>
						  <th>First Name</th>
						  <th>Other Names</th>
						  <th>NSSF</th>
						  <th>NHIF</th>
						  <th>Branch Code</th>
						  <th>Bank Account Number</th>
						  <th>Comment</th>
						</tr>
					  </thead>
					  <tbody>
			';
			
			//retrieve the data from array
			for($r = 1; $r < $total_rows; $r++)
			{
				$items['personnel_onames'] = '';
				$personnel_number = $items['personnel_number'] = $array[$r][0];
				if(!empty($array[$r][1]))
				{
					$items['personnel_fname'] = mysql_real_escape_string(ucwords(strtolower($array[$r][1])));
				}
				
				if(!empty($array[$r][2]))
				{
					$items['personnel_onames'] .= mysql_real_escape_string(ucwords(strtolower($array[$r][2])));
				}
				
				if(!empty($array[$r][3]))
				{
					$items['personnel_onames']  .= ' '.mysql_real_escape_string(ucwords(strtolower($array[$r][3])));
				}
				
				//$items['personnel_dob'] = date('Y-m-d',strtotime($array[$r][4]));
				$items['created'] = date('Y-m-d H:i:s');
				$items['modified_by'] = $this->session->userdata('personnel_id');
				$items['created_by'] = $this->session->userdata('personnel_id');
				
				if(!empty($array[$r][4]))
				{
					$items['personnel_nssf_number']=$array[$r][4];
				}
				
				if(!empty($array[$r][5]))
				{
					$items['personnel_nhif_number']=$array[$r][5];
				}
				
				if(!empty($array[$r][6]))
				{
					$items['personnel_kra_pin']=$array[$r][6];
				}
				
				if(!empty($array[$r][7]))
				{
					$items['gender_id']=$array[$r][7];
				}
				
				if(!empty($array[$r][8]))
				{
					$items['personnel_national_id_number']=$array[$r][8];
				}
				$branch_code=$array[$r][9];
				$bank_branch_id = $this->get_bank_branch_id($branch_code);
				
				if(!empty($array[$r][9]))
				{
					$items['bank_branch_id']=$bank_branch_id;
				}
				//var_dump($array[$r][10]);die();
				if(!empty($array[$r][10]))
				{
					$items['bank_account_number']=$array[$r][10];
				}
				if(!empty($array[$r][12]))
				{
					$items['personnel_email']=$array[$r][12];
				}
				if(!empty($array[$r][13]))
				{
					$items['staff_id']=$array[$r][13];
				}
				if(!empty($array[$r][14]))
				{
					$items['cost_center']=$array[$r][14];
				}
				if(!empty($array[$r][15]))
				{
					$items['engagement_date']=$array[$r][15];
				}
				if(!empty($array[$r][16]))
				{
					$items['date_of_exit']=$array[$r][16];
				}
				$branch_id = $this->input->post('branch_id');
				if($branch_id == 0)
				{
					if(!empty($array[$r][11]))
					{
						$branch_id = $array[$r][11];
					}
				}
				if(!empty($branch_id))
				{
					$items['branch_id'] = $branch_id;
				}
				
				//$items['personnel_onames'] = $personnel_onames1.' '.$personnel_onames2;
				$comment = '';
				if(!empty($personnel_number))
				{
					//orbit
					if($branch_id == 35)
					{
						$personnel_number = 'ORB'.$personnel_number;
					}
					
					// check if the number already exists
					if($this->check_current_personnel_exisits($personnel_number, $branch_id))
					{
						//number exists then update existing data
						$data = array(
							'personnel_number' => $personnel_number,
							'branch_id' => $branch_id
						);
						$this->db->where($data);
						$this->db->update('personnel', $items);
						$comment .= '<br/>Duplicate member number entered, personnel data updated successfully';
						$class = 'warning';
					}
					else
					{
						// number does not exisit
						//save product in the db
						//var_dump($items);die();
						if($this->db->insert('personnel', $items))
						{
							$comment .= '<br/>Personnel successfully added to the database';
							$class = 'success';
						}
						
						else
						{
							$comment .= '<br/>Internal error. Could not add personnel to the database. Please contact the site administrator';
							$class = 'warning';
						}
					}
				}
				
				else
				{
					$comment .= '<br/>Not saved ensure you have a member number entered';
					$class = 'danger';
				}
				
				
				$response .= '
					
						<tr class="'.$class.'">
							<td colspan="7">'.$comment.'</td>
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
			$return['response'] = 'Member data not found ';
			$return['check'] = FALSE;
		}
		
		return $return;
	}
	
	public function get_bank_branch_id($branch_code)
	{
		$this->db->where(array ('bank_branch_code'=> $branch_code));
		$query = $this->db->get('bank_branch');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$bank_branch_id = $row->bank_branch_id;
		}
		
		else
		{
			if($this->db->insert('bank_branch', array ('bank_branch_code'=> $branch_code)))
			{
				$bank_branch_id = $this->db->insert_id();
			}
			
			else
			{
				$bank_branch_id = 0;
			}
		}
		
		return $bank_branch_id;
	}
	
	public function check_current_personnel_exisits($personnel_id,$branch_id)
	{
		$this->db->where(array ('personnel_number'=> $personnel_id,
		'branch_id' => $branch_id));
		
		$query = $this->db->get('personnel');
		
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	
	public function all_document_types()
	{
		$this->db->order_by('document_type_name');
		$query = $this->db->get('document_type');
		
		return $query;
	}
	//edit_order_authorize
	public function edit_order_authorize($personnel_id)
	{
		$data = array(
				'personnel_id' => $personnel_id
			);
		$this->db->where($data);
		$query = $this->db->get('personnel_approval');
		if($query->num_rows() > 0)
		{
			$this->db->where($data);
			if($this->db->update('personnel_approval', array(
				'approval_status_id' => $this->input->post('approval_role_id'))))
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
			
			if($this->db->insert('personnel_approval', array(
				'approval_status_id' => $this->input->post('approval_role_id'),'personnel_id' => $personnel_id)))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
	}
	
	// delete approvals assigned
	public function delete_personnel_approvals_assigned($personnel_approval_id)
	{
		if($this->db->delete('personnel_approval', array('personnel_approval_id' => $personnel_approval_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	//add personnel timesheet
	public function add_personnel_timesheet()
	{
		$personnel_id = $this->session->userdata('personnel_id');
		$date = $this->input->post('date');
		$start_time = $this->input->post('start_time');
		$end_time = $this->input->post('end_time');
		$description = $this->input->post('description');
		$data = array(
						
			'personnel_id'=>$personnel_id,
			'timesheet_date'=>$date,
			'start_time'=>$start_time,
			'end_time'=>$end_time,
			'tasks_done' => $description
			);
				
		$personnel_timesheet_id = $this->db->insert('personnel_timesheet', $data);
		if($personnel_timesheet_id)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	//retrieve personnel timesheets
	public function get_personnel_timesheet($personnel_id)
	{
		$this->db->where('personnel.personnel_id = '.$personnel_id);
		$this->db->select('personnel.* , personnel_timesheet.*');
		$result = $this->db->get('personnel, personnel_timesheet');
		
		return $result;
	}
	//get all personnel bank names
	public function get_bank_names()
	{
		$this->db->select('*');
		$this->db->order_by('bank_name', 'ASC');
		$query = $this->db->get('bank');
		
		return $query;	
	}
	
	//get all bank branches
	public function get_bank_branch_names()
	{
		$this->db->select('*');
		$this->db->order_by('bank_branch_name', 'ASC');
		$query = $this->db->get('bank_branch');
		
		return $query;
	}
	
	public function import_personnel_emails_template()
	{
		$this->load->library('Excel');
		
		$title = 'Personnel Emails Import Template';
		$count=1;
		$row_count=0;
		
		$report[$row_count][0] = 'Employee Number';
		$report[$row_count][1] = 'Email Address';
		
		$row_count++;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	//import personnel emails
	public function import_csv_personnel_emails($upload_path)
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
			$response2 = $this->sort_personnel_emails_data($array);
		
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
	
	public function sort_personnel_emails_data($array)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);//var_dump($array);die();
		
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 2))
		{
			$items['modified_by'] = $this->session->userdata('personnel_id');
			$response = '
				<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>Member Number</th>
						  <th>Email Address</th>
						  <th>Comment</th>
						</tr>
					  </thead>
					  <tbody>
			';
			
			//retrieve the data from array
			for($r = 1; $r < $total_rows; $r++)
			{
				$personnel_number = $array[$r][0];
				if(!empty($array[$r][1]))
				{
					$items['personnel_email']=$array[$r][1];
				}
				
				if(!empty($array[$r][0]))
				{
					$items['personnel_number']=$array[$r][0];
				}
				
				//$items['personnel_onames'] = $personnel_onames1.' '.$personnel_onames2;
				$comment = '';
		
				$branch_id = $this->input->post('branch_id');
				if(!empty($personnel_number))
				{//var_dump($personnel_number,$branch_id);die();
					// check if the number already exists
					if($this->check_current_personnel_exisits($personnel_number, $branch_id))
					{
						//number exists then update existing data
						$data = array(
							'personnel_number' => $personnel_number,
							'branch_id' => $branch_id
						);
						$this->db->where($data);
						$this->db->update('personnel', $items);
						$comment .= '<br/>Duplicate member number entered, Email Address updated successfully';
						$class = 'success';
					}
					else
					{
						$comment .= '<br/>The personnel number was not found, Email Address not saved';
						$class = 'danger';
					}
				}
				
				else
				{
					$comment .= '<br/>Not saved ensure you have a member number entered';
					$class = 'danger';
				}
				
				
				$response .= '
					
						<tr class="'.$class.'">
							<td>'.$items['personnel_number'].'</td>
							<td>'.$items['personnel_email'].'</td>
							<td>'.$comment.'</td>
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
			$return['response'] = 'Member data not found ';
			$return['check'] = FALSE;
		}
		
		return $return;
	}
}
?>