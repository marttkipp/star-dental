<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class Rooms extends MX_Controller
    {
    	function __construct()
    	{
    		parent:: __construct();
		   
		   $this->load->model('site/site_model');
		   $this->load->model('admin/sections_model');
		   $this->load->model('online_diary/rooms_model');
		   $this->load->model('admin/admin_model');
		   $this->load->model('admin/users_model');
           $this->load->model('hr/personnel_model');
		
    
    	}
        
    	/*
    	*
    	*	Default action is to show all the sections
    	*
    	*/
    	public function index() 
    	{
    		$where = 'room_id > 0';
    		$table = 'room_dr';
    		$order = 'room_name';
    		$order_method = 'ASC';
    		//pagination
    		$segment = 3;
    		$this->load->library('pagination');
    		$config['base_url'] = site_url().'online-dairies/rooms';
    		$config['total_rows'] = $this->users_model->count_items($table, $where);
    		$config['uri_segment'] = $segment;
    		$config['per_page'] = 20;
    		$config['num_links'] = 5;
    		
    		$config['full_tag_open'] = '<ul class="pagination pull-right">';
    		$config['full_tag_close'] = '</ul>';
    		
    		$config['first_tag_open'] = '<li>';
    		$config['first_tag_close'] = '</li>';
    		
    		$config['last_tag_open'] = '<li>';
    		$config['last_tag_close'] = '</li>';
    		
    		$config['next_tag_open'] = '<li>';
    		$config['next_link'] = 'Next';
    		$config['next_tag_close'] = '</span>';
    		
    		$config['prev_tag_open'] = '<li>';
    		$config['prev_link'] = 'Prev';
    		$config['prev_tag_close'] = '</li>';
    		
    		$config['cur_tag_open'] = '<li class="active"><a href="#">';
    		$config['cur_tag_close'] = '</a></li>';
    		
    		$config['num_tag_open'] = '<li>';
    		$config['num_tag_close'] = '</li>';
    		$this->pagination->initialize($config);
    		
    		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
            $v_data["links"] = $this->pagination->create_links();
    		$query = $this->rooms_model->get_all_rooms($table, $where, $config["per_page"], $page, $order, $order_method);
    		
    		//change of order method 
    		if($order_method == 'DESC')
    		{
    			$order_method = 'ASC';
    		}
    		
    		else
    		{
    			$order_method = 'DESC';
    		}
    		
    		$data['title'] = 'Rooms';
    		$v_data['title'] = $data['title'];
    		
    		$v_data['order'] = $order;
    		$v_data['order_method'] = $order_method;
    		$v_data['query'] = $query;
    		$v_data['page'] = $page;
    		$data['content'] = $this->load->view('all_rooms', $v_data, true);
    		
    		$this->load->view('admin/templates/general_page', $data);
    	}
        public function add_rooms() 
    	{
    		//form validation rules
    		$this->form_validation->set_rules('room_name', 'rooms', 'required|xss_clean');
    		$this->form_validation->set_rules('room_description', 'Room Description', 'required|xss_clean');
    	
    		
    		//if form has been submitted
    		if ($this->form_validation->run())
    		{
    			
    			if($this->rooms_model->add_rooms_details())
    			{
    				$this->session->set_userdata('success_message', 'Room added successfully');
    				redirect('online-dairies/rooms');
    			}
    			
    			else
    			{
    				$this->session->set_userdata('error_message', 'Could not add room. Please try again');
    			}
    		}
    		
    		//open the add Rooms
    		$data['title'] = 'Add Rooms';
    		$v_data['title'] = 'Add Rooms ';
    		
    		$data['content'] = $this->load->view('add_rooms', $v_data, true);
    		$this->load->view('admin/templates/general_page', $data);
    	}
    
     public function edit_rooms($room_id) 

      {
    		//form validation rules
    		$this->form_validation->set_rules('room_name', 'Room Name', 'required|xss_clean');
    		$this->form_validation->set_rules('room_description', 'Room Description', 'required|xss_clean');
    		
    		
    		
    		//if form has been submitted
    		if ($this->form_validation->run())
    		{
    			
    			if($this->rooms_model->update_room($room_id))
    			{
    				$this->session->set_userdata('success_message', 'Room updated successfully');
    				redirect('online-dairies/rooms');
    			}
    			
    			else
    			{
    				$this->session->set_userdata('error_message', 'Could not update Room. Please try again');
    			}
    		}
    		
    		//edit room
    		$data['title'] = 'Edit Room';
    		$v_data['title'] = 'Edit Room';

    		
    		//select the room from the database
    		$query = $this->rooms_model->get_room($room_id);
    		$v_data['room_dr'] = $query->result();

    		//var_dump($query);die();
    		
    		if ($query->num_rows() > 0)
    		{
    			$v_data['room_dr'] = $query->result();
    		
    			
    			$data['content'] = $this->load->view('edit_rooms', $v_data, true);
    		}
    		
    		else
    		{
    			$data['content'] = 'Asset does not exist';
    		}
    		
    		$this->load->view('admin/templates/general_page', $data);

       }

     public function delete_room($room_id)
        	
        {
        		//delete room
        		$query = $this->rooms_model->get_room($room_id);
        		
        		if ($query->num_rows() > 0)
        		{
        			$result = $query->result();
        			
        		}
        		$this->rooms_model->delete_room($room_id);
        		$this->session->set_userdata('success_message', 'Asset has been deleted');
        		redirect('online-dairies/rooms');
        	}
    
       public function activate_room($room_id)
    	{
    		$this->rooms_model->activate_room($room_id);
    		$this->session->set_userdata('success_message', 'Asset activated successfully');
    		redirect('online-dairies/rooms');
    	} 
    
    	public function deactivate_room($room_id)
    	{
    		$this->rooms_model->deactivate_room($room_id);
    		$this->session->set_userdata('success_message', 'Asset disabled successfully');
    		redirect('online-dairies/rooms');
    
    	}		
    
    
        public function search_room()
    	
    	{
            $room_name = $this->input->post('room_name');
    
    
    		if(!empty($room_name))
    		{
    			$room_name ='AND room_dr.room_name LIKE \'%'.mysql_real_escape_string($room_name).'%\' ';
    		}
    		
    		
    		$search = $room_name;
    		$this->session->set_userdata('room_search', $search);
    		
    		$this->index();
    		
    	}
    	public function close_room()
    	{
    		$this->session->unset_userdata('room_search');
    		redirect('online-dairies/rooms');
    	}

}



?>