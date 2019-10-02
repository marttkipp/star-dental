<?php
    class Assets_Category_model extends CI_Model 
    {	

    	public function get_all_asset_category($table, $where, $config, $page, $order, $order_method = 'ASC')
    	
     {
    		//retrieve all users
    		$this->db->select('*');
    		$this->db->where($where);
    		$this->db->order_by($order, $order_method);
    		$query = $this->db->get($table, $config, $page);
    		
    		return $query;
    	}
    
       public function all_asset_categories()
    	{
    		$this->db->where('asset_category_name = 1 AND asset_category_status > 0');
    		$this->db->order_by('asset_category_name', 'ASC');
    		$query = $this->db->get('asset_category');
    		
    		return $query;
    
        }
    
      public function update_asset_category($asset_category_id)
	{
		$data = array(
				'asset_category_name'=>$this->input->post('asset_category_name'),
				'asset_category_status'=>$this->input->post('asset_category_status'),
				
			);
			
		$this->db->where('asset_category_id', $asset_category_id);
		if($this->db->update('asset_category', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
    
    
    public function add_asset_category_detail()
	{
		$data = array(
				'asset_category_name'=>ucwords(strtolower($this->input->post('asset_category_name'))),
				'asset_category_status'=>$this->input->post('asset_category_status'),
				'created'=>date('Y-m-d H:i:s'),
				'created_by'=>$this->session->userdata('asset_category_id')
				
			);
			
		if($this->db->insert('asset_category', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
     }

    public function get_asset_category()
	
	  {
		//retrieve all users
		$this->db->from('asset_category');
		$this->db->select('*');
		$this->db->where('asset_category_id > 0 ');
		$query = $this->db->get();
		
		return $query;    	
 
     }

  public function delete_asset_category($asset_category_id)
	{
		if($this->db->delete('asset_category', array('asset_category_id' => $asset_category_id)))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}

   }

   public function activate_asset_category($asset_category_id)
	{
		$data = array(
				'asset_category_status' => 1
			);
		$this->db->where('asset_category_id', $asset_category_id);
		if($this->db->update('asset_category', $data))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}

	 }
   public function deactivate_asset_category($asset_category_id)
	{
		$data = array(
				'asset_category_status' => 0
			);
		$this->db->where('asset_category_id', $asset_category_id);
		if($this->db->update('asset_category', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}	 	 
	
}   	
?>
