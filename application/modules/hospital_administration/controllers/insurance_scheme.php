<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/hospital_administration/controllers/hospital_administration.php";

class insurance_scheme extends Hospital_administration 
{
	function __construct()
	{
		parent:: __construct();
	}
    
	/*
	*
	*	Default action is to show all the insurance_scheme
	*
	*/
	public function index($order = 'insurance_scheme_name', $order_method = 'ASC') 
	{
		$where = 'insurance_scheme_id > 0';
		$table = 'insurance_scheme';
		//pagination
		$segment = 5;
		$this->load->library('pagination');
		$config['base_url'] = site_url().'administration/insurance_scheme/'.$order.'/'.$order_method;
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
		$query = $this->insurance_scheme_model->get_all_insurance_scheme($table, $where, $config["per_page"], $page, $order, $order_method);
		
		//change of order method 
		if($order_method == 'DESC')
		{
			$order_method = 'ASC';
		}
		
		else
		{
			$order_method = 'DESC';
		}
		
		$data['title'] = 'Insurance schemes';
		$v_data['title'] = $data['title'];
		
		$v_data['order'] = $order;
		$v_data['order_method'] = $order_method;
		$v_data['query'] = $query;
		$v_data['all_insurance_scheme'] = $this->insurance_scheme_model->all_insurance_scheme();
		$v_data['page'] = $page;
		$data['content'] = $this->load->view('insurance_scheme/all_insurance_scheme', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Add a new insurance_scheme
	*
	*/
	public function add_insurance_scheme() 
	{
		//form validation rules
		$this->form_validation->set_rules('insurance_scheme_name', 'Insurance scheme Name', 'required|xss_clean');
		$this->form_validation->set_rules('insurance_scheme_status', 'Insurance scheme Status', 'required|xss_clean');
		$this->form_validation->set_rules('visit_type_id', 'Insurance company', 'xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			if($this->insurance_scheme_model->add_insurance_scheme())
			{
				$this->session->set_userdata('success_message', 'Insurance scheme added successfully');
				redirect('administration/insurance-scheme');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not add Insurance scheme. Please try again');
			}
		}
		$v_data['insurance_companies'] = $this->companies_model->all_visit_types();
		$data['title'] = 'Add Insurance scheme';
		$v_data['title'] = $data['title'];
		$data['content'] = $this->load->view('insurance_scheme/add_insurance_scheme', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Edit an existing insurance_scheme
	*	@param int $insurance_scheme_id
	*
	*/
	public function edit_insurance_scheme($insurance_scheme_id) 
	{
		//form validation rules
		$this->form_validation->set_rules('insurance_scheme_name', 'Insurance scheme Name', 'required|xss_clean');
		$this->form_validation->set_rules('insurance_scheme_status', 'Insurance scheme Status', 'required|xss_clean');
		$this->form_validation->set_rules('visit_type_id', 'Insurance company', 'xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			//update insurance_scheme
			if($this->insurance_scheme_model->update_insurance_scheme($insurance_scheme_id))
			{
				$this->session->set_userdata('success_message', 'Insurance scheme updated successfully');
				redirect('administration/insurance-scheme');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not update Insurance scheme. Please try again');
			}
		}
		
		$v_data['insurance_companies'] = $this->companies_model->all_visit_types();
		//open the add new insurance_scheme
		$data['title'] = 'Edit Insurance scheme';
		$v_data['title'] = $data['title'];
		
		//select the insurance_scheme from the database
		$query = $this->insurance_scheme_model->get_insurance_scheme($insurance_scheme_id);
		
		$v_data['insurance_scheme'] = $query->result();
		
		$data['content'] = $this->load->view('insurance_scheme/edit_insurance_scheme', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Delete an existing insurance_scheme
	*	@param int $insurance_scheme_id
	*
	*/
	public function delete_insurance_scheme($insurance_scheme_id)
	{
		$this->insurance_scheme_model->delete_insurance_scheme($insurance_scheme_id);
		$this->session->set_userdata('success_message', 'Insurance scheme has been deleted');
		
		redirect('administration/insurance-scheme');
	}
    
	/*
	*
	*	Activate an existing insurance_scheme
	*	@param int $insurance_scheme_id
	*
	*/
	public function activate_insurance_scheme($insurance_scheme_id)
	{
		$this->insurance_scheme_model->activate_insurance_scheme($insurance_scheme_id);
		$this->session->set_userdata('success_message', 'Insurance scheme activated successfully');
		
		redirect('administration/insurance-scheme');
	}
    
	/*
	*
	*	Deactivate an existing insurance_scheme
	*	@param int $insurance_scheme_id
	*
	*/
	public function deactivate_insurance_scheme($insurance_scheme_id)
	{
		$this->insurance_scheme_model->deactivate_insurance_scheme($insurance_scheme_id);
		$this->session->set_userdata('success_message', 'Insurance scheme disabled successfully');
		
		redirect('administration/insurance-scheme');
	}
}
?>