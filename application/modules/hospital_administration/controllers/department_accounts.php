<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/hospital_administration/controllers/hospital_administration.php";

class Department_accounts extends Hospital_administration 
{
	var $csv_path;
	function __construct()
	{
		parent:: __construct();
		
		$this->load->model('reception/reception_model');
		
		$this->load->model('department_accounts_model');
		
		$this->csv_path = realpath(APPPATH . '../assets/csv');
	}

	public function index($order = 'department_account_id', $order_method = 'ASC')
	{
		//check if branch has parent
		$this->db->where('branch_code', $this->session->userdata('branch_code'));
		$query = $this->db->get('branch');
		$branch_code = $this->session->userdata('branch_code');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$branch_parent = $row->branch_parent;
			
			if(!empty($branch_parent))
			{
				$branch_code = $branch_parent;
			}
		}
		// this is it
		$where = 'department_account_delete = 0 AND department_accounts.account_id = account.account_id AND account_type.account_type_id = account.account_type_id';
		$department_account_search = $this->session->userdata('department_account_search');
		
		if(!empty($department_account_search))
		{
			$where .= $department_account_search;
		}
		
		$segment = 5;
		$table = 'department_accounts,account,account_type';
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'administration/department-accounts/'.$order.'/'.$order_method;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
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
		$query = $this->department_accounts_model->get_all_department_accounts($table, $where, $config["per_page"], $page, $order, $order_method);
		
		//change of order method 
		if($order_method == 'DESC')
		{
			$order_method = 'ASC';
		}
		
		else
		{
			$order_method = 'DESC';
		}
		
        $v_data["departments"] = $this->departments_model->all_departments();
        $v_data["department_accounts"] = $this->department_accounts_model->all_department_accounts();
		$v_data['order'] = $order;
		$v_data['order_method'] = $order_method;
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		
		$data['title'] = 'Department accounts';
		$v_data['title'] = 'Department accounts';
		$v_data['module'] = 0;
		
		$data['content'] = $this->load->view('department_accounts/department_accounts', $v_data, true);
		
		
		$data['sidebar'] = 'admin_sidebar';
		
		
		$this->load->view('admin/templates/general_page', $data);
		// end of it
	}


	public function department_account_search()
	{
		$service_name = $this->input->post('department_account_name');
		$department_id = $this->input->post('department_id');
		
		if(!empty($service_name))
		{
			$service_name = ' AND department_account.department_account_name LIKE \'%'.$department_account_name.'%\' ';
		}
		else
		{
			$service_name = '';
		}
		
		if(!empty($department_id))
		{
			$department_id = ' AND department_accounts.department_id = \''.$department_id.'\' ';
		}
		else
		{
			$department_id = '';
		}
		
		$search = $service_name.$department_id;
		$this->session->set_userdata('department_account_search', $search);
		
		redirect('administration/department-accounts');
	}
	
	public function close_department_account_search()
	{
		$this->session->unset_userdata('department_account_search');
		
		redirect('administration/department-accounts');
	}
	
	public function department_account_charges($department_account_id, $order = 'department_account_charge_name', $order_method = 'ASC')
	{
		// this is it
		$where = 'department_account_charge_delete = 0 AND service.department_account_id = department_account_charge.department_account_id AND department_account_charge.visit_type_id = visit_type.visit_type_id AND department_account_charge.department_account_id = '.$department_account_id;
		$department_account_charge_search = $this->session->userdata('department_account_charge_search');
		
		if(!empty($department_account_charge_search))
		{
			$where .= $department_account_charge_search;
		}
		
		$segment = 6;
		$table = 'service,department_account_charge,visit_type';
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'administration/service-charges/'.$department_account_id.'/'.$order.'/'.$order_method;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
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
		$query = $this->department_accounts_model->get_all_department_account_charges($table, $where, $config["per_page"], $page, $order, $order_method);
		$v_data["department_id"] = $this->department_accounts_model->get_department_id($department_account_id);
		
		//change of order method 
		if($order_method == 'DESC')
		{
			$order_method = 'ASC';
		}
		
		else
		{
			$order_method = 'DESC';
		}
		
		$v_data['order'] = $order;
		$v_data['order_method'] = $order_method;
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$department_account_name = $this->department_accounts_model->get_department_account_names($department_account_id);
		$data['title'] = $v_data['title'] = $department_account_name.' charges';
		$v_data['visit_types'] = $this->department_accounts_model->get_visit_types();
		
		$v_data['department_account_id'] = $department_account_id;
		$v_data['department_account_name'] = $department_account_name;
		$data['content'] = $this->load->view('department_accounts/department_account_charges', $v_data, true);
		
		$data['sidebar'] = 'admin_sidebar';
		
		
		$this->load->view('admin/templates/general_page', $data);
		// end of it
	}
	
	public function add_department_account()
	{
		$this->form_validation->set_rules('department_id', 'Department', 'trim|required|xss_clean');
		$this->form_validation->set_rules('account_id', 'Account name', 'trim|required|xss_clean');

		if ($this->form_validation->run())
		{
			$result = $this->department_accounts_model->submit_department_accounts();
			if($result == FALSE)
			{
				$this->session->set_userdata("error_message", "Unable to add this Department Account. Please try again");
			}
			else
			{
				$this->session->set_userdata("success_message", "Successfully created a Department Account ");
			}
			redirect('administration/department-accounts');
		}
		
        $v_data["departments"] = $this->departments_model->all_departments();
        $v_data["accounts"] = $this->department_accounts_model->all_accounts();
		
		$data['title'] = 'Add Department Account';
		$v_data['title'] = 'Add Department Account';
		$v_data['department_account_id'] = 0;
		$data['content'] = $this->load->view('department_accounts/add_department_account',$v_data,TRUE);
		$this->load->view('admin/templates/general_page', $data);	
	}
	
	public function edit_department_account($department_account_id)
	{
		$this->form_validation->set_rules('department_account_name', 'Service name', 'trim|required|xss_clean');
		$this->form_validation->set_message("is_unique", "A unique name is requred.");

		if ($this->form_validation->run())
		{
			$department_account_name = $this->input->post('department_account_name');
			$department_id = $this->input->post('department_id');
			$visit_data = array('department_account_name'=>$department_account_name, 'department_id'=>$department_id, 'modified_by'=>$this->session->userdata('personnel_id'));
			$this->db->where('department_account_id',$department_account_id);
			$this->db->update('service', $visit_data);
			
			$this->session->set_userdata("success_message", "Department Account updated successfully");
			redirect('administration/department_accounts');
		}
		
        $v_data["departments"] = $this->departments_model->all_departments();
		
		$department_account_name = $this->department_accounts_model->get_department_account_names($department_account_id);
		$v_data['dept_id'] = $this->department_accounts_model->get_department_account_department($department_account_id);
		$data['title'] = $v_data['title'] = 'Edit '.$department_account_name;
		
		$v_data['department_account_id'] = $department_account_id;
		$v_data['department_account_name'] = $department_account_name;
		$data['content'] = $this->load->view('department_accounts/edit_service',$v_data,TRUE);
		$this->load->view('admin/templates/general_page', $data);
	}
	
	public function add_department_account_charge($department_account_id)
	{
		$this->form_validation->set_rules('department_account_charge_name', 'Service Charge name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('patient_type', 'Patient Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('charge', 'charge', 'trim|required|xss_clean');

		if ($this->form_validation->run())
		{
			$result = $this->department_accounts_model->submit_department_account_charges($department_account_id);
			if($result == FALSE)
			{
				$this->session->set_userdata("error_message", "Unable to add service charge. Please try again");
			}
			else
			{
				$this->session->set_userdata("success_message","Successfully created a service charge");
			}
			redirect('administration/service-charges/'.$department_account_id);
		}
		
		$department_account_name = $this->department_accounts_model->get_department_account_names($department_account_id);
		$data['title'] = $v_data['title'] = 'Add '.$department_account_name.' charge';
		
		$v_data['department_account_id'] = $department_account_id;
		$v_data['department_account_name'] = $department_account_name;
		$v_data['type'] = $this->reception_model->get_types();
		
		$data['content'] = $this->load->view('department_accounts/add_department_account_charge',$v_data,TRUE);
		$this->load->view('admin/templates/general_page', $data);	
	}
	
	public function edit_department_account_charge($department_account_id, $department_account_charge_id)
	{
		$this->form_validation->set_rules('department_account_charge_name', 'Service Charge name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('patient_type', 'Patient Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('charge', 'charge', 'trim|required|xss_clean');

		if ($this->form_validation->run())
		{
			$department_account_charge_name = $this->input->post('department_account_charge_name');
			$patient_type = $this->input->post('patient_type');
			$charge = $this->input->post('charge');
			if($patient_type != 1)
			{
				// select * insurance service charge 

				$this->db->where('patient_type <> 1 AND  department_account_charge_name = "'.$department_account_charge_name.'"');
				$department_account_charges = $this->db->get('department_account_charge');

				if($department_account_charges->num_rows() > 0)
				{
					foreach ($department_account_charges->result() as $key) {
						$department_account_charge_id_id = $key->department_account_charge_id;
						$visit_type_idd = $key->visit_type_id;

						

					}			
				}
				$visit_data = array('department_account_charge_name'=>$department_account_charge_name,'visit_type_id'=>$visit_type_idd,'department_account_charge_amount'=>$charge, 'modified_by'=>$this->session->userdata('personnel_id'));
				$this->db->where('department_account_charge_id',$department_account_charge_id);
				$this->db->update('department_account_charge', $visit_data);


			}
			else
			{
				$visit_data = array('department_account_charge_name'=>$department_account_charge_name,'visit_type_id'=>$patient_type,'department_account_charge_amount'=>$charge, 'modified_by'=>$this->session->userdata('personnel_id'));
				$this->db->where('department_account_charge_id',$department_account_charge_id);
				$this->db->update('department_account_charge', $visit_data);
			}
			
			
			$this->session->set_userdata("success_message","Successfully updated service charge");
				
			redirect('administration/service-charges/'.$department_account_id);
		}
		
		$department_account_name = $this->department_accounts_model->get_department_account_names($department_account_id);
		$data['title'] = $v_data['title'] = 'Edit '.$department_account_name.' charge';
		
		$v_data['department_account_id'] = $department_account_id;
		$v_data['department_account_name'] = $department_account_name;
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['department_account_charge_id'] = $department_account_charge_id;
		$data['content'] = $this->load->view('department_accounts/edit_department_account_charge',$v_data,TRUE);
		$this->load->view('admin/templates/general_page', $data);	
	}
	
	public function delete_visit_charge($visit_charge_id)
	{
		$visit_data = array('visit_charge_delete'=>1);
		$this->db->where(array("visit_charge_id"=>$visit_charge_id));
		$this->db->update('visit_charge', $visit_data);
		redirect('reception/general_queue/administration');
	}
	
	// public function department_account_search()
	// {
	// 	$department_account_name = $this->input->post('department_account_name');
	// 	$department_id = $this->input->post('department_account_id');
		
	// 	if(!empty($department_account_name))
	// 	{
	// 		$department_account_name = ' AND department_accounts.department_account_name LIKE \'%'.$department_account_name.'%\' ';
	// 	}
	// 	else
	// 	{
	// 		$department_account_name = '';
	// 	}
		
	// 	if(!empty($department_id))
	// 	{
	// 		$department_account_id = ' AND department_accounts.department_account_id = \''.$department_account_id.'\' ';
	// 	}
	// 	else
	// 	{
	// 		$department_account_id = '';
	// 	}
		
	// 	$search = $department_account_name.$department_account_id;
	// 	$this->session->set_userdata('department_account_search', $search);
		
	// 	redirect('administration/department_accounts');
	// }
	
	// public function close_department_account_search()
	// {
	// 	$this->session->unset_userdata('department_account_search');
		
	// 	redirect('administration/department_accounts');
	// }
	
	public function department_account_charge_search($department_account_id)
	{
		$department_account_charge_name = $this->input->post('department_account_charge_name');
		$visit_type_id = $this->input->post('visit_type_id');
		
		if(!empty($department_account_charge_name))
		{
			$department_account_charge_name = ' AND department_account_charge.department_account_charge_name LIKE \'%'.$department_account_charge_name.'%\' ';
		}
		else
		{
			$department_account_charge_name = '';
		}
		
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND department_account_charge.visit_type_id = '.$visit_type_id.' ';
		}
		else
		{
			$visit_type_id = '';
		}
		
		$search = $department_account_charge_name.$visit_type_id;
		$this->session->set_userdata('department_account_charge_search', $search);
		
		redirect('administration/service-charges/'.$department_account_id);
	}
	
	public function close_department_account_charge_search($department_account_id)
	{
		$this->session->unset_userdata('department_account_charge_search');
		
		redirect('administration/service-charges/'.$department_account_id);
	}
	
	public function delete_department_account($department_account_id)
	{
		if($this->department_accounts_model->delete_department_accounts($department_account_id))
		{
			$this->session->set_userdata('department_account_success_message', 'The Department Account has been deleted successfully');

		}
		else
		{
			$this->session->set_userdata('department_account_error_message', 'The Department Account could not be deleted');
		}
		
			redirect('administration/department-accounts');
	}
	
	public function delete_department_account_charge($department_account_id, $department_account_charge_id)
	{
		if($this->department_accounts_model->delete_department_account_charge($department_account_charge_id))
		{
			$this->session->set_userdata('success_message', 'The charge has been deleted successfully');

		}
		else
		{

			$this->session->set_userdata('error_message', 'The charge could not be deleted');
		}
		redirect('administration/service-charges/'.$department_account_id);
	}
    
	/*
	*
	*	Activate an existing service
	*	@param int $department_account_id
	*
	*/
	public function activate_department_account($department_account_id)
	{
		$this->department_accounts_model->activate_department_accounts($department_account_id);
		$this->session->set_userdata('success_message', 'Department Account activated successfully');
		redirect('administration/department-accounts');
	}
    
	/*
	*
	*	Deactivate an existing service
	*	@param int $department_account_id
	*
	*/
	public function deactivate_department_account($department_account_id)
	{
		$this->department_accounts_model->deactivate_department_accounts($department_account_id);
		$this->session->set_userdata('success_message', 'Department Account disabled successfully');
		redirect('administration/department-accounts');
	}
    
	/*
	*
	*	Activate an existing department_account_charge
	*	@param int $department_account_charge_id
	*
	*/
	public function activate_department_account_charge($department_account_id, $department_account_charge_id)
	{
		$this->department_accounts_model->activate_department_account_charge($department_account_charge_id);
		$this->session->set_userdata('success_message', 'Charge activated successfully');
		redirect('administration/service-charges/'.$department_account_id);
	}
    
	/*
	*
	*	Deactivate an existing department_account_charge
	*	@param int $department_account_charge_id
	*
	*/
	public function deactivate_department_account_charge($department_account_id, $department_account_charge_id)
	{
		$this->department_accounts_model->deactivate_department_account_charge($department_account_charge_id);
		$this->session->set_userdata('success_message', 'Charge disabled successfully');
		redirect('administration/service-charges/'.$department_account_id);
	}
	
	public function import_lab_charges($department_account_id)
	{
		if($this->department_accounts_model->import_lab_charges($department_account_id))
		{
		}
		
		else
		{
		}
		
		redirect('administration/service-charges/'.$department_account_id);
	}
	
	public function import_bed_charges($department_account_id)
	{
		if($this->department_accounts_model->import_bed_charges($department_account_id))
		{
		}
		
		else
		{
		}
		
		redirect('administration/service-charges/'.$department_account_id);
	}
	
	public function import_pharmacy_charges($department_account_id)
	{
		if($this->department_accounts_model->import_pharmacy_charges($department_account_id))
		{
		}
		
		else
		{
		}
		
		redirect('administration/service-charges/'.$department_account_id);
	}
	
	function import_charges_template()
	{
		//export products template in excel 
		 $this->department_accounts_model->import_charges_template();
	}
	
	function import_charges($department_account_id)
	{
		//open the add new product
		$v_data['department_account_id'] = $department_account_id;
		$v_data['title'] = 'Import Charges';
		$data['title'] = 'Import Charges';
		$data['content'] = $this->load->view('department_accounts/import_charges', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
	
	function do_charges_import($department_account_id)
	{
		if(isset($_FILES['import_csv']))
		{
			if(is_uploaded_file($_FILES['import_csv']['tmp_name']))
			{
				//import products from excel 
				$response = $this->department_accounts_model->import_csv_charges($this->csv_path, $department_account_id);
				
				if($response == FALSE)
				{
				}
				
				else
				{
					if($response['check'])
					{
						$v_data['import_response'] = $response['response'];
					}
					
					else
					{
						$v_data['import_response_error'] = $response['response'];
					}
				}
			}
			
			else
			{
				$v_data['import_response_error'] = 'Please select a file to import.';
			}
		}
		
		else
		{
			$v_data['import_response_error'] = 'Please select a file to import.';
		}
		
		//open the add new product
		$v_data['department_account_id'] = $department_account_id;
		$v_data['title'] = 'Import Charges';
		$data['title'] = 'Import Charges';
		$data['content'] = $this->load->view('department_accounts/import_charges', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
}

?>