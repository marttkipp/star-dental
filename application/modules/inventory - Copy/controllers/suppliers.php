<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Suppliers extends MX_Controller {
	var $suppliers_path;
	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('admin/users_model');
		$this->load->model('suppliers_model');
		$this->load->model('admin/file_model');
		$this->load->model('admin/sections_model');
		$this->load->model('admin/admin_model');
		$this->load->model('site/site_model');
		$this->load->model('administration/personnel_model');
		//path to image directory
	}
    
	/*
	*
	*	Default action is to show all the suppliers
	*
	*/

	public function index() 
	{
		//$where = 'created_by IN (0, '.$this->session->userdata('vendor_id').')';
		//$where = 'branch_code = "'.$this->session->userdata('branch_code').'"';
		$where = "creditor_type_id = 1";
		$table = 'creditor';


		$supplier_search = $this->session->userdata('supplier_search');
		
		if(!empty($supplier_search))
		{
			$where .= $supplier_search;
		}
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'procurement/suppliers';
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
		$query = $this->suppliers_model->get_all_suppliers($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['title'] = 'All suppliers';
		//$v_data['child_suppliers'] = $this->suppliers_model->all_child_suppliers();
		$data['content'] = $this->load->view('suppliers/all_suppliers', $v_data, true);
		$data['title'] = 'All suppliers';
		
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Add a new supplier
	*
	*/
	public function add_supplier() 
	{
		//form validation rules
		$this->form_validation->set_rules('supplier_name', 'supplier Name', 'required|xss_clean');
		$this->form_validation->set_rules('supplier_category_id', 'supplier Name', 'required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			//upload product's gallery images
			
			if($this->suppliers_model->add_supplier())
			{
				$this->session->set_userdata('success_message', 'supplier added successfully');
				redirect('procurement/suppliers');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not add supplier. Please try again');
			}
		}
		
		//open the add new supplier
		$data['title'] = 'Add New supplier';
		$v_data['title'] = 'Add New supplier';
		$v_data['supply'] = $this->suppliers_model->get_category();
		$data['content'] = $this->load->view('suppliers/add_supplier', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Edit an existing supplier
	*	@param int $supplier_id
	*
	*/
	public function edit_supplier($supplier_id) 
	{
		//form validation rules
		$this->form_validation->set_rules('creditor_name', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('supplier_category_id', 'supplier Name', 'required|xss_clean');
		$this->form_validation->set_rules('creditor_email', 'Email', 'xss_clean');
		$this->form_validation->set_rules('creditor_phone', 'Phone', 'xss_clean');
		$this->form_validation->set_rules('creditor_location', 'Location', 'xss_clean');
		$this->form_validation->set_rules('creditor_building', 'Building', 'xss_clean');
		$this->form_validation->set_rules('creditor_floor', 'Floor', 'xss_clean');
		$this->form_validation->set_rules('creditor_address', 'Address', 'xss_clean');
		
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			
			if($this->suppliers_model->update_supplier($supplier_id))
			{
				$this->session->set_userdata('success_message', 'supplier updated successfully');
				redirect('procurement/suppliers');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not update supplier. Please try again');
			}
		}
		// var_dump($supplier_id); die();
		//open the add new supplier
		$data['title'] = 'Edit supplier';
		$v_data['title'] = 'Edit supplier';
		$v_data['creditor_id'] = $supplier_id;

		//select the supplier from the database
		$query = $this->suppliers_model->get_supplier($supplier_id);
		$v_data['supply'] = $this->suppliers_model->get_category();		
		$v_data['creditor'] = $query;
		$data['content'] = $this->load->view('suppliers/edit_supplier', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Delete an existing supplier
	*	@param int $supplier_id
	*
	*/
	public function delete_supplier($supplier_id)
	{
		//delete supplier image
		$query = $this->suppliers_model->get_supplier($supplier_id);
		
		if ($query->num_rows() > 0)
        		{
        			$result = $query->result();
        			
        		}
        		$this->suppliers_model->delete_supplier($supplier_id);
        		$this->session->set_userdata('success_message', 'Category has been deleted');
        		redirect('procurement/suppliers');					
      }
    
	/*
	*
	*	Activate an existing supplier
	*	@param int $supplier_id
	*
	*/
	public function activate_supplier($supplier_id)
	{
		$this->suppliers_model->activate_supplier($supplier_id);
		$this->session->set_userdata('success_message', 'supplier activated successfully');
		redirect('procurement/suppliers');
	}
    
	/*
	*
	*	Deactivate an existing supplier
	*	@param int $supplier_id
	*
	*/
	public function deactivate_supplier($supplier_id)
	{
		$this->suppliers_model->deactivate_supplier($supplier_id);
		$this->session->set_userdata('success_message', 'supplier disabled successfully');
		redirect('procurement/suppliers');
	}
	public function search_suppliers()
	{

		$supplier_name = $this->input->post('supplier_name');


		if(!empty($supplier_name))
		{
			$supplier_name = ' AND supplier.supplier_name LIKE \'%'.mysql_real_escape_string($supplier_name).'%\' ';
		}
		
		
		$search = $supplier_name;
		$this->session->set_userdata('supplier_search', $search);
		
		$this->index();
		
	}
	public function close_suppliers_search()													
	{
		$this->session->unset_userdata('supplier_search');
		redirect('procurement/suppliers');
	}

	
	

    
}
?>