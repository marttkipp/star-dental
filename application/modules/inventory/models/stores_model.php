<?php

class Stores_model extends CI_Model 
{	
	/*
	*	Retrieve all stores
	*
	*/
	public function all_stores()
	{
		$this->db->where('store_status = 1');
		$this->db->order_by('store_name');
		$query = $this->db->get('store');
		
		return $query;
	}
	/*
	*	Retrieve latest store
	*
	*/
	public function latest_store()
	{
		$this->db->limit(1);
		$this->db->order_by('created', 'DESC');
		$query = $this->db->get('store');
		
		return $query;
	}
	/*
	*	Retrieve all parent stores
	*
	*/
	public function all_parent_stores()
	{
		$this->db->where('store_status = 1 AND store_parent = 0');
		$this->db->order_by('store_name', 'ASC');
		$query = $this->db->get('store');
		
		return $query;
	}
	/*
	*	Retrieve all children stores
	*
	*/
	public function all_child_stores()
	{
		$this->db->where('store_status = 1 AND store_parent > 0');
		$this->db->order_by('store_name', 'ASC');
		$query = $this->db->get('store');
		
		return $query;
	}
	
	/*
	*	Retrieve all stores
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_stores($table, $where, $per_page, $page)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by('store_name, store_parent');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Add a new store
	*	@param string $image_name
	*
	*/
	public function add_store()
	{
		$data = array(
				'store_name'=>ucwords(strtolower($this->input->post('store_name'))),
				'store_parent'=>$this->input->post('store_parent'),
				'store_status'=>$this->input->post('store_status'),
				'created'=>date('Y-m-d H:i:s'),
				'created_by'=>$this->session->userdata('personnel_id'),
				'modified_by'=>$this->session->userdata('personnel_id'),
				'branch_code'=>$this->session->userdata('branch_code')
			);
			
		if($this->db->insert('store', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Update an existing store
	*	@param string $image_name
	*	@param int $store_id
	*
	*/
	public function update_store($store_id)
	{
		$data = array(
				'store_name'=>ucwords(strtolower($this->input->post('store_name'))),
				'store_parent'=>$this->input->post('store_parent'),
				'store_status'=>$this->input->post('store_status'),
				'modified_by'=>$this->session->userdata('personnel_id')
			);
			
		$this->db->where('branch_code = "'.$this->session->userdata('branch_code').'"AND store_id = '.$store_id);
		if($this->db->update('store', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	get a single store's children
	*	@param int $store_id
	*
	*/
	public function get_sub_stores($store_id)
	{
		//retrieve all users
		$this->db->from('store');
		$this->db->select('*');
		$this->db->where('store_parent = '.$store_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	get a single store's details
	*	@param int $store_id
	*
	*/
	public function get_store($store_id)
	{
		//retrieve all users
		$this->db->from('store');
		$this->db->select('*');
		$this->db->where('store_id = '.$store_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	get a single store's details
	*	@param int $store_id
	*
	*/
	public function get_store_by_name($store_name)
	{
		//retrieve all users
		$this->db->from('store');
		$this->db->select('*');
		$this->db->where('store_name = \''.$store_name.'\'');
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Delete an existing store
	*	@param int $store_id
	*
	*/
	public function delete_store($store_id)
	{
		$data = array(
				'store_deleted' => 1,
				'deleted_by' => $this->session->userdata('personnel_id'),
				'deleted_on' =>date('Y-m-d H-i-s')
			);
		$this->db->where('store_id', $store_id);
		
		if($this->db->update('store', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Activate a deactivated store
	*	@param int $store_id
	*
	*/
	public function activate_store($store_id)
	{
		$data = array(
				'store_status' => 1
			);
		$this->db->where('store_id', $store_id);
		
		if($this->db->update('store', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated store
	*	@param int $store_id
	*
	*/
	public function deactivate_store($store_id)
	{
		$data = array(
				'store_status' => 0
			);
		$this->db->where('store_id', $store_id);
		
		if($this->db->update('store', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	public function all_stores_assigned($personnel_id)
	{
		$is_admin = $this->reception_model->check_if_admin($personnel_id,1);

		if($is_admin OR $personnel_id == 0)
		{
			$where = 'store.store_id > 0';
			$table = '';
		}
		else
		{
			$where = 'personnel_store.store_id = store.store_id AND personnel_store.personnel_id = '.$personnel_id;
			$table = ',personnel_store';
		}
		$this->db->select('store.*');
		$this->db->where($where);
		$qquery = $this->db->get('store'.$table);
		
		return $qquery;
	}

	public function get_parent_stores($personnel_id)
	{
		$is_admin = $this->reception_model->check_if_admin($personnel_id,1);	
		$where = 'store.store_parent = 0';
		$table = '';
		
		$this->db->select('store.*');
		$this->db->where($where);
		$qquery = $this->db->get('store'.$table);
		
		return $qquery;
	}
}
?>