<?php
    class Rooms_model extends CI_Model 
    {	

   public function get_all_rooms($table, $where, $config, $page, $order, $order_method = 'ASC')
    	
    	 {
    		//retrieve all users
    		$this->db->select('*');
    		$this->db->where($where);
    		$this->db->order_by($order, $order_method);
    		$query = $this->db->get($table, $config, $page);
    		
    		return $query;
    	}
 
   public function add_rooms_details()
	
	 {
		$data = array(
				'room_name'=>ucwords(strtolower($this->input->post('room_name'))),
				'room_status'=>$this->input->post('room_status'),
				'room_description'=>$this->input->post('room_description'),
				'created'=>date('Y-m-d H:i:s'),
				'created_by'=>$this->session->userdata('personnel_id')
				
			);
			
       if($this->db->insert('room_dr', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
     }

     public function all_rooms()
    	{
    		$this->db->where('room_status = 1 AND room_id > 0');
    		$this->db->order_by('room_name', 'ASC');
    		$query = $this->db->get('room_dr');
    		
    		return $query;
    
        }
    
     public function update_room($room_id)
	   {
		$data = array(
				'room_name'=>$this->input->post('room_name'),
				'room_status'=>$this->input->post('room_status'),
				'room_description'=>$this->input->post('room_description')
				
			);
			
		$this->db->where('room_id', $room_id);
		if($this->db->update('room_dr', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public function get_room($room_id)
	
	  {
		//retrieve all users
		$this->db->from('room_dr');
		$this->db->select('*');
		$this->db->where('room_id = '.$room_id);
		$query = $this->db->get();
		
		return $query;    	
 
     }

  public function delete_room($room_id)
	{
		if($this->db->delete('room_dr', array('room_id' => $room_id)))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}

    }


   public function activate_room($room_id)
	 {
		$data = array(
				'room_status' => 1
			);
		$this->db->where('room_id', $room_id);
		
		if($this->db->update('room_dr', $data))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}

	 }
  public function deactivate_room($room_id)
	
	{
		$data = array(
				'room_status' => 0
			);
		$this->db->where('room_id', $room_id);
		
		if($this->db->update('room_dr', $data))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
   } 

 } 