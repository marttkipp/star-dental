<?php

class insurance_scheme_model extends CI_Model 
{	
	/*
	*	Retrieve all insurance_scheme
	*
	*/
	public function all_insurance_scheme()
	{
		$this->db->where('insurance_scheme_status = 1');
		$query = $this->db->get('insurance_scheme');
		
		return $query;
	}
	
	/*
	*	Retrieve all insurance_scheme
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_insurance_scheme($table, $where, $per_page, $page, $order = 'insurance_scheme_name', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->join('visit_type', 'visit_type.visit_type_id = insurance_scheme.visit_type_id', 'left');
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Add a new insurance_scheme
	*	@param string $image_name
	*
	*/
	public function add_insurance_scheme()
	{
		$data = array(
				'visit_type_id'=>$this->input->post('visit_type_id'),
				'insurance_scheme_name'=>$this->input->post('insurance_scheme_name'),
				'insurance_scheme_status'=>$this->input->post('insurance_scheme_status'),
				'branch_id'=>$this->input->post('branch_id'),
				'created'=>date('Y-m-d H:i:s'),
				'created_by'=>$this->session->userdata('personnel_id'),
				'modified_by'=>$this->session->userdata('personnel_id')
			);
			
		if($this->db->insert('insurance_scheme', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Update an existing insurance_scheme
	*	@param string $image_name
	*	@param int $insurance_scheme_id
	*
	*/
	public function update_insurance_scheme($insurance_scheme_id)
	{
		$data = array(
				'visit_type_id'=>$this->input->post('visit_type_id'),
				'insurance_scheme_name'=>$this->input->post('insurance_scheme_name'),
				'insurance_scheme_status'=>$this->input->post('insurance_scheme_status'),
				'branch_id'=>$this->input->post('branch_id'),
				'modified_by'=>$this->session->userdata('personnel_id')
			);
			
		$this->db->where('insurance_scheme_id', $insurance_scheme_id);
		if($this->db->update('insurance_scheme', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	get a single insurance_scheme's children
	*	@param int $insurance_scheme_id
	*
	*/
	public function get_sub_insurance_scheme($insurance_scheme_id)
	{
		//retrieve all users
		$this->db->from('insurance_scheme');
		$this->db->select('*');
		$this->db->where('insurance_scheme_parent = '.$insurance_scheme_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	get a single insurance_scheme's details
	*	@param int $insurance_scheme_id
	*
	*/
	public function get_insurance_scheme($insurance_scheme_id)
	{
		//retrieve all users
		$this->db->from('insurance_scheme');
		$this->db->select('*');
		$this->db->where('insurance_scheme_id = '.$insurance_scheme_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Delete an existing insurance_scheme
	*	@param int $insurance_scheme_id
	*
	*/
	public function delete_insurance_scheme($insurance_scheme_id)
	{
		if($this->db->delete('insurance_scheme', array('insurance_scheme_id' => $insurance_scheme_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Activate a deactivated insurance_scheme
	*	@param int $insurance_scheme_id
	*
	*/
	public function activate_insurance_scheme($insurance_scheme_id)
	{
		$data = array(
				'insurance_scheme_status' => 1
			);
		$this->db->where('insurance_scheme_id', $insurance_scheme_id);
		
		if($this->db->update('insurance_scheme', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated insurance_scheme
	*	@param int $insurance_scheme_id
	*
	*/
	public function deactivate_insurance_scheme($insurance_scheme_id)
	{
		$data = array(
				'insurance_scheme_status' => 0
			);
		$this->db->where('insurance_scheme_id', $insurance_scheme_id);
		
		if($this->db->update('insurance_scheme', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
}
?>