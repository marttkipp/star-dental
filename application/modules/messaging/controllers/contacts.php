<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contacts extends MX_Controller
{	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('contacts_model');
		$this->load->model('admin/sections_model');
		$this->load->model('admin/admin_model');
		$this->load->model('auth/auth_model');
		$this->load->model('site/site_model');
		$this->load->model('administration/personnel_model');


		$this->csv_path = realpath(APPPATH . '../assets/csv');
		
		if(!$this->auth_model->check_login())
		{
			redirect('login');
		}
	}
	
	public function index()
	{
		// this is it
		$where = 'entryid > 0';
		$personnel_search = $this->session->userdata('personnel_search');
		
		if(!empty($personnel_search))
		{
			$where .= $personnel_search;
		}
		
		$segment = 3;
		$table = 'allcounties';
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'messaging/contacts';
		$config['total_rows'] = $this->contacts_model->count_items($table, $where);
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
		$query = $this->contacts_model->get_all_contacts($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $personnel_search;
		
		$data['title'] = 'Contact List';
		$v_data['title'] = 'Contact List';
		
		$data['content'] = $this->load->view('contacts/contact_list', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
		// end of it
	}
	public function import_template()
	{
		$this->contacts_model->import_template();
	}
	function do_messages_import($message_category_id)
	{

		if(isset($_FILES['import_csv']))
		{
			// var_dump($message_category_id); die();
			if(is_uploaded_file($_FILES['import_csv']['tmp_name']))
			{
				//import products from excel 

				$response = $this->contacts_model->import_csv_charges($this->csv_path, $message_category_id);
				
				
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
		redirect('contacts');
	}
	public function delete_contact($contact_id)
	{
		if($this->contacts_model->delete_contact($contact_id))
		{
			$this->session->set_userdata('contact_success_message', 'The contact has been deleted successfully');

		}
		else
		{
			$this->session->set_userdata('contact_error_message', 'The contact could not be deleted');
		}
		
			redirect('contacts');
	}
	public function bulk_delete_contacts($page)
	{
		$total_contacts = sizeof($_POST['contacts']);
		
		//check if any checkboxes have been ticked
		if($total_contacts > 0)
		{	
			for($r = 0; $r < $total_contacts; $r++)
			{	
				$contact = $_POST['contacts'];
				$contact_id = $contact[$r]; 
				//check if card is held
				if($this->contacts_model->delete_contact($contact_id))
				{
					$this->session->set_userdata('contact_success_message', 'The contact has been deleted successfully');

				}
				else
				{
					$this->session->set_userdata('contact_error_message', 'The contact could not be deleted');
				}
			}
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Please select contacts to delete first');
		}
		
		redirect('contacts');
	}
	
	/*
	*	Edit personnel
	*
	*/
	public function edit_contact($contact_id)
	{
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
		//$this->form_validation->set_rules('balance', 'Balance', 'required|xss_clean');
		$this->form_validation->set_rules('Phonenumber', 'Phone Number', 'required|xss_clean');
		
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			if($this->contacts_model->edit_contact($contact_id))
			{
				$this->session->set_userdata("success_message", "Contact edited successfully");
				redirect('contacts');
			}
			
			else
			{
				$this->session->set_userdata("error_message","Could not edit oontact. Please try again");
			}
		}
		
		$v_data['contact_details'] = $this->contacts_model->get_contact($contact_id);
		$data['content'] = $this->load->view('contacts/edit_contact', $v_data, true);
		
		$data['title'] = 'Edit Contact';
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	/*
	*	Add personnel
	*
	*/
	public function add_contact()
	{
		//form validation rules
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
		//$this->form_validation->set_rules('balance', 'Balance', 'required|xss_clean');
		$this->form_validation->set_rules('Phonenumber', 'Phone Number', 'required|xss_clean');
		
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			if($this->contacts_model->add_contact())
			{
				$this->session->set_userdata("success_message", "Contact added successfully");
				redirect('contacts');
			}
			
			else
			{
				$this->session->set_userdata("error_message","Could not add oontact. Please try again");
			}
		}
		
		$data['content'] = $this->load->view('contacts/add_contact', '', true);
		
		$data['title'] = 'Add Contact';
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function update_records()
	{
		
		$this->db->where('dependant_id = 0');
		$query = $this->db->get('patients');

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value_one) {
				# code...
				$phone = $value_one->patient_phone1;
				$patient_surname = $value_one->patient_surname;
				$patient_id = $value_one->patient_id;

				if(!empty($phone))
				{
					$Phonenumber = substr($phone, -9);

					$this->db->where('Phonenumber = "'.$Phonenumber.'"');
					$query_two = $this->db->get('allcounties');
					if($query_two->num_rows() == 0 )
					{
						$service_charge_insert['name'] = $patient_surname;
						$service_charge_insert['Phonenumber']= $Phonenumber;
						$service_charge_insert['contact_type']= 1;
						$this->db->insert('allcounties', $service_charge_insert);
						
					}

				}

				$update_array['dependant_id']= 1;
				$this->db->where('patient_id',$patient_id);
				$this->db->update('patients', $update_array);
				
			}
		}
		redirect('messaging/contacts');
		
	}
}
?>