<?php
date_default_timezone_set('Africa/Nairobi');
class Messaging extends MX_Controller 
{
	var $csv_path;
	function __construct()
	{
		parent:: __construct();
		$this->load->model('messaging_model');
		$this->load->model('site/site_model');
		$this->load->model('admin/sections_model');
		$this->load->model('admin/admin_model');
		$this->load->model('admin/email_model');
		$this->load->model('admin/users_model');
		$this->load->model('hr/personnel_model');
		// $this->load->model('admin/companies_model');
		// $this->load->model('admin/members_model');
		//$this->load->model('member/patient_model');



		$this->csv_path = realpath(APPPATH . '../assets/csv');
	}
	
	public function index()
	{
		if(!$this->auth_model->check_login())
		{
			redirect('login');
		}
		
		else
		{
			redirect('message/dashboard');
		}
	}
	public function dashboard()
	{
		$where = 'patient_id > 0 ';
		$total_contacts = $this->messaging_model->count_items('patients',$where);

		$sent_where = 'message_status = 1 ';
		$sent_messages = $this->messaging_model->count_items('messages',$sent_where);

		$unsent_where = 'message_status > 1 ';
		$unsent_messages = $this->messaging_model->count_items('messages',$unsent_where);

		// calculate total cost

		$cost = $this->messaging_model->get_total_cost();
		
		$total_amount = 0;//$this->messaging_model->get_amount_toped_up();

		$v_data['title'] = 'Dashboard';
		$data['title'] = 'Dashboard';
		$v_data['total_contacts'] = $total_contacts;
		$v_data['sent_messages'] = $sent_messages;
		$v_data['unsent_messages'] = $unsent_messages;
		$v_data['balance'] = $total_amount - $cost;
		$data['content'] = $this->load->view('dashboard', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);		
	}

	public function unsent_messages()
	{

		$where = 'messages.message_status > 1';
		$table = 'messages';
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'messaging/unsent-messages';
		$config['total_rows'] = $this->messaging_model->count_items($table, $where);
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

		$query = $this->messaging_model->get_all_messages($table, $where, $config["per_page"], $page);
		$data['title'] = $this->site_model->display_page_title();
		$v_data['title'] = $data['title'];
		$v_data['page'] = $page;
		$v_data['query'] = $query;
		$data['content'] = $this->load->view('sms/unsent_messages', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function message_templates()
	{

		$where = 'message_template_id > 0';
		$table = 'message_template';
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'message/message-templates';
		$config['total_rows'] = $this->messaging_model->count_items($table, $where);
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

		$query = $this->messaging_model->get_all_message_templates($table, $where, $config["per_page"], $page);

		$data['title'] = $this->site_model->display_page_title();
		$v_data['title'] = $data['title'];
		$v_data['page'] = $page;
		$v_data['query'] = $query;
		$data['content'] = $this->load->view('templates/all_message_templates', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function sent_messages()
	{

		$where = 'messages.message_status = 1';
		$table = 'messages';
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'messaging/sent-messages';
		$config['total_rows'] = $this->messaging_model->count_items($table, $where);
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

		$query = $this->messaging_model->get_all_messages($table, $where, $config["per_page"], $page);
		$data['title'] = $this->site_model->display_page_title();
		$v_data['title'] = $data['title'];
		$v_data['page'] = $page;
		$v_data['query'] = $query;
		$data['content'] = $this->load->view('sms/sent_messages', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function import_template()
	{
		$this->messaging_model->import_template();
	}
	
	function do_messages_import($message_category_id)
	{

		if(isset($_FILES['import_csv']))
		{
			// var_dump($message_category_id); die();
			if(is_uploaded_file($_FILES['import_csv']['tmp_name']))
			{
				//import products from excel 

				$response = $this->messaging_model->import_csv_charges($this->csv_path, $message_category_id);
				
				
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
		redirect('messaging/unsent-messages');
	}
	public function spoilt_messages()
	{

		$where = 'messaging.message_category_id = message_category.message_category_id AND messaging.sent_status = 2 AND messaging.branch_code = "'. $this->session->userdata('branch_code').'"';
		$table = 'messaging, message_category';
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'all-posts';
		$config['total_rows'] = $this->messaging_model->count_items($table, $where);
		$config['uri_segment'] = 2;
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
		$config['cur_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        $data["links"] = $this->pagination->create_links();
		$query = $this->messaging_model->get_all_messages($table, $where, $config["per_page"], $page);
		$data['title'] = $this->site_model->display_page_title();
		$v_data['title'] = $data['title'];
		$v_data['query'] = $query;
		$data['content'] = $this->load->view('sms/sent_messages', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function send_messages()
	{
		// $this->messaging_model->send_unsent_messages();

		redirect('messaging/sent-messages');
	}

	/*
	*
	*	Add a new category
	*
	*/
	public function add_message_template() 
	{

		//form validation rules
		$this->form_validation->set_rules('template_description', 'Template Description', 'required|xss_clean');
		$this->form_validation->set_rules('contact_type', 'Contact Type', 'required|xss_clean');
		$this->form_validation->set_rules('template_code', 'Template Code', 'required|is_unique[message_template.message_template_code]|xss_clean');
		$this->form_validation->set_message("is_unique", "A unique preffix is requred.");
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			
			if($this->messaging_model->add_message_template())
			{
				$this->session->set_userdata('success_message', 'message template added successfully');
				redirect('messaging/message-templates');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not add message template. Please try again');
			}
		}
		
		//open the add new category
		
		$data['title'] = 'Add Message Template';
		$v_data['title'] = $data['title'];
		$data['content'] = $this->load->view('templates/add_message_template', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Edit an existing category
	*	@param int $category_id
	*
	*/
	public function edit_message_template($message_template_id) 
	{
		//form validation rules
		$this->form_validation->set_rules('template_description', 'Template Description', 'required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			
			//update category
			if($this->messaging_model->update_message_template($message_template_id))
			{
				$this->session->set_userdata('success_message', 'message template updated successfully');
				redirect('messaging/message-templates');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not update message template. Please try again');
			}
		}
		
		//open the add new message_template
		$data['title'] = 'Edit message_template';
		$v_data['title'] = $data['title'];
		
		//select the message_template from the database
		$query = $this->messaging_model->get_message_template($message_template_id);
		
		if ($query->num_rows() > 0)
		{
			$v_data['message_template'] = $query->result();
			
			$data['content'] = $this->load->view('templates/edit_message_template', $v_data, true);
		}
		
		else
		{
			$data['content'] = 'message template does not exist';
		}
		
		$this->load->view('admin/templates/general_page', $data);
	}
    
	
    
	/*
	*
	*	Activate an existing message_template
	*	@param int $message_template_id
	*
	*/
	public function activate_message_template($message_template_id)
	{
		$this->messaging_model->activate_message_template($message_template_id);
		$this->session->set_userdata('success_message', 'message template activated successfully');
		redirect('messaging/message-templates');
	}
    
	/*
	*
	*	Deactivate an existing message_template
	*	@param int $message_template_id
	*
	*/
	public function deactivate_message_template($message_template_id)
	{
		$this->messaging_model->deactivate_message_template($message_template_id);
		$this->session->set_userdata('success_message', 'Message Template disabled successfully');
		redirect('messaging/message-templates');
	}

	public function template_detail($message_template_id)
	{
		//form validation rules
		$where = 'message_template_id ='.$message_template_id;
		$table = 'message_batch';
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'template-detail/'.$message_template_id;
		$config['total_rows'] = $this->messaging_model->count_items($table, $where);
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

		$query = $this->messaging_model->get_all_message_template_batches($table, $where, $config["per_page"], $page);

		$v_data['query'] = $query;
		$v_data['page'] = $page;

		$counties = $this->messaging_model->get_active_contacts('patient_phone1');
		$rs8 = $counties->result();
		$county_list = '';
		foreach ($rs8 as $property_rs) :
			$Countyname = $property_rs->patient_first_name.' '.$property_rs->patient_surname;

		    $county_list .="<option value='".$Countyname."'>".$Countyname."</option>";

		endforeach;
		$v_data['county_list'] = $county_list;
		
		$query = $this->messaging_model->get_message_template($message_template_id);
		$v_data['message_template'] = $query->result();
		$message_template = $query->result();

		$data['title'] =  $message_template[0]->message_template_code.' Detail';
		$v_data['title'] = $data['title'];

		// var_dump($page);die();
		$data['content'] = $this->load->view('templates/template_detail', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
	public function set_search_parameters($message_template_id)
	{
		$_SESSION['search_template'] = NULL;
		
		$this->session->unset_userdata('search_title');
		$this->session->unset_userdata('search_template');
		
		$countyname = $this->input->post('countyname');
		$gender = $this->input->post('gender');
		$constituencyname = $this->input->post('constituencyname');
		$Pollingstationname = $this->input->post('Pollingstationname');
		$CAWname = $this->input->post('CAWname');
		$age_from = $this->input->post('age_from');
		$age_to = $this->input->post('age_to');

		$search_title = "Showing records for ";
		if(!empty($countyname))
		{
			$search_title .= " County : ".$countyname;
			$countyname = ' AND Countyname = "'.$countyname.'"';
			
		}
		else
		{
			$countyname = '';
			$search_title .= '';
		}
		if(!empty($gender))
		{
				$search_title .= " Gender : ".$gender;
				$gender = ' AND Gender = "'.$gender.'"';
				
		}
		else
		{
			$gender = '';
			$search_title .= '';
		}
		if(!empty($CAWname))
		{
				$search_title .= " Ward :".$CAWname;
				$CAWname = ' AND CAWname = "'.$CAWname.'"';
				
		}
		else
		{
			$gender = '';
			$search_title .= '';
		}
		if(!empty($constituencyname))
		{
				$search_title .= " Constituency :".$constituencyname;
				$constituencyname = ' AND Constituencyname = "'.$constituencyname.'"';
				
		}
		else
		{
			$constituencyname = '';
			$search_title .= '';
		}
		if(!empty($Pollingstationname))
		{
				$search_title .= " Polling Station:".$Pollingstationname;
				$Pollingstationname = ' AND Pollingstationname = "'.$Pollingstationname.'"';
				
		}
		else
		{
			$Pollingstationname = '';
			$search_title .= '';
		}
		if(!empty($age_from) && !empty($age_to))
		{
			$visit_date = ' AND age BETWEEN \''.$age_from.'\' AND \''.$age_to.'\'';
			$search_title .= ' Ages From '.$age_from.' To '.$age_to.' ';
		}
		
		else if(!empty($age_from))
		{
			$visit_date = ' AND age = \''.$age_from.'\'';
			$search_title .= ' Ages From '.$age_from.' ';
		}
		
		else if(!empty($age_to))
		{
			$visit_date = ' AND age = \''.$age_to.'\'';
			$search_title .= ' Ages From '.$age_from.' ';
		}
		
		else
		{
			$visit_date = '';
		}
		$search = $countyname.$constituencyname.$CAWname.$Pollingstationname.$gender.$visit_date;
		$this->session->set_userdata('search_template', $search);
		$this->session->set_userdata('search_title', $search_title);
		
		redirect('template-detail/'.$message_template_id);
	}
	public function create_batch_items($message_template_id)
	{
		if($this->messaging_model->create_batch($message_template_id))
		{
			$this->session->unset_userdata('search_title');
			$this->session->unset_userdata('search_template');
			$this->session->set_userdata("success_message","You have successfully added batch contacts to this template");
		}
		else
		{
			$this->session->set_userdata("error_message","Sorry somthing went wrong. Please try again");

		}
		redirect('template-detail/'.$message_template_id);
	}
	
	public function send_batch_messages($message_batch_id,$message_template_id)
	{
		$this->messaging_model->send_batch_messages($message_batch_id,$message_template_id);

		redirect('template-detail/'.$message_template_id);
	}
	public function members_account($message_batch_id,$message_template_id)
	{
		$order = 'patient_first_name';
		$order_method = 'ASC';
		/*$where = 'patient_status = 1 AND company.company_id = member.company_id AND member.patient_id NOT IN (SELECT entryid FROM messages,message_batch WHERE messages.message_batch_id = message_batch.message_batch_id AND message_batch.message_batch_id = '.$message_batch_id.')';
		
		$table = 'member, company';*/
		
		$where = 'member.patient_id NOT IN (SELECT entryid FROM messages,message_batch WHERE messages.message_batch_id = message_batch.message_batch_id AND message_batch.message_batch_id = '.$message_batch_id.')';
		
		$table = 'member';
		
		$patient_search_item = $this->session->userdata('patient_search_item');

		//var_dump($patient_search_item); die();
		if(!empty($patient_search_item))
		{
			$where .= $patient_search_item;
		}
		//echo $where; die();
		//pagination
		$segment = 4;
		$this->load->library('pagination');
		$config['base_url'] = site_url().'view-senders/'.$message_batch_id.'/'.$message_template_id;
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
		$query = $this->messaging_model->get_all_members($table, $where, $config["per_page"], $page, $order, $order_method);
		
		//change of order method 
		if($order_method == 'DESC')
		{
			$order_method = 'ASC';
		}
		
		else
		{
			$order_method = 'DESC';
		}
		
		$data['title'] = 'Members';
		$search_title = $this->session->userdata('patient_search_item_title');
			
		if(!empty($search_title))
		{
			$v_data['title'] = 'Members filtered by :'.$search_title;
		}
		
		else
		{
			$v_data['title'] = $data['title'];
		}

		$companies = $this->messaging_model->get_active_contacts_list('company');
		$rs8 = $companies->result();
		$company_list = '';
		foreach ($rs8 as $property_rs) :
			$company = $property_rs->company;

		    $company_list .="<option value='".$company."'>".$company."</option>";

		endforeach;
		$v_data['company_list'] = $company_list;


		$v_data['order'] = $order;
		$v_data['order_method'] = $order_method;
		$v_data['query'] = $query;
		$v_data['message_template_id'] = $message_template_id;
		$v_data['message_batch_id'] = $message_batch_id;
		$v_data['companies_list_rs'] = $this->companies_model->all_companies();
		$v_data['patient_statuses'] = $this->members_model->all_patient_statuses();

		// $v_data['companies'] = $this->companies_model->all_companies();
		$v_data['page'] = $page;
		$data['content'] = $this->load->view('templates/all_members', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function view_persons_for_batch($message_batch_id,$message_template_id)
	{
		$where = 'messages.message_batch_id = message_batch.message_batch_id AND messages.message_batch_id = '.$message_batch_id;
		$table = 'messages,message_batch';
		$segment = 4;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'senders-view/'.$message_batch_id.'/'.$message_template_id;
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
		$config['next_tag_close'] = '</l>';
		
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
		$query = $this->messaging_model->get_all_message_details($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['message_template_id'] = $message_template_id;
		$v_data['message_batch_id'] = $message_batch_id;
		$query = $this->messaging_model->get_message_template($message_template_id);
		$v_data['message_template'] = $query->result();
		$message_template = $query->result();
		$v_data['title'] ='Message Contacts';


		$companies = $this->messaging_model->get_active_contacts_list('company');
		$rs8 = $companies->result();
		$company_list = '';
		foreach ($rs8 as $property_rs) :
			$company = $property_rs->company;

		    $company_list .="<option value='".$company."'>".$company."</option>";

		endforeach;
		$v_data['company_list'] = $company_list;


			
		$data['content'] = $this->load->view('templates/message_detail', $v_data, true);
		
		$data['title'] = 'Message Contacts';
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function view_schedules($message_batch_id,$message_template_id)
	{
		$where = 'schedules.schedule_period_id = schedule_period.schedule_period_id AND schedule_delete = 0 AND schedules.message_batch_id = '.$message_batch_id;
		$table = 'schedules,schedule_period';
		$segment = 4;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'view-schedules/'.$message_batch_id.'/'.$message_template_id;
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
        $data["links"] = $this->pagination->create_links();
		$query = $this->messaging_model->get_all_schedule_details($table, $where, $config["per_page"], $page);
		
		$v_data['schedules_query'] = $query;
		$v_data['page'] = $page;
		$v_data['message_template_id'] = $message_template_id;
		$v_data['message_batch_id'] = $message_batch_id;
		$query = $this->messaging_model->get_message_template($message_template_id);
		$v_data['message_template'] = $query->result();
		$message_template = $query->result();
		$v_data['title'] ='Schedules';
			
		$data['content'] = $this->load->view('templates/all_schedules', $v_data, true);
		
		$data['title'] = 'Schedules';
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function delete_contact($message_id,$message_batch_id,$message_template_id)
	{
		if($this->messaging_model->delete_contact($message_id))
		{
			$this->session->set_userdata('contact_success_message', 'The contact has been deleted successfully');

		}
		else
		{
			$this->session->set_userdata('contact_error_message', 'The contact could not be deleted');
		}
		
			redirect('senders-view/'.$message_batch_id.'/'.$message_template_id);
	}
	public function create_new_schedule($message_batch_id,$message_template_id)
	{
		$this->form_validation->set_rules('schedule_period_id', 'Schedule period', 'required|xss_clean');
		$this->form_validation->set_rules('schedule_date', 'Schedule date', 'xss_clean');
		$this->form_validation->set_rules('schedule_time', 'Schedule time', 'xss_clean');


		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			if($this->messaging_model->add_schedule($message_batch_id))
			{
				$this->session->set_userdata("success_message", "Schedule created successfully");
			}
			
			else
			{
				$this->session->set_userdata("error_message","Could not create schedule Please try again");
			}
		}
		redirect('view-schedules/'.$message_batch_id.'/'.$message_template_id);
	}
	public function activate_schedule($schedule_id,$message_batch_id,$message_template_id)
	{
		$visit_data = array('schedule_status'=>1);
		$this->db->where('schedule_id',$schedule_id);
		if($this->db->update('schedules', $visit_data))
		{
				$this->session->set_userdata("success_message", "Activation was successful");
				redirect('view-schedules/'.$message_batch_id.'/'.$message_template_id);
		}
		else
		{
				$this->session->set_userdata("error_message","Could not activate. Please try again");
				redirect('view-schedules/'.$message_batch_id.'/'.$message_template_id);
		}
	}
	public function deactivate_schedule($schedule_id,$message_batch_id,$message_template_id)
	{
		$visit_data = array('schedule_status'=>0);
		$this->db->where('schedule_id',$schedule_id);
		if($this->db->update('schedules', $visit_data))
		{
				$this->session->set_userdata("success_message", "deactivation was successful");
				redirect('view-schedules/'.$message_batch_id.'/'.$message_template_id);
		}
		else
		{
				$this->session->set_userdata("error_message","Could not deactivate. Please try again");
				redirect('view-schedules/'.$message_batch_id.'/'.$message_template_id);
		}
	}
	public function delete_schedule($schedule_id,$message_batch_id,$message_template_id)
	{
		$visit_data = array('schedule_delete'=>1);
		$this->db->where('schedule_id',$schedule_id);
		if($this->db->update('schedules', $visit_data))
		{
				$this->session->set_userdata("success_message", "You've successfully removed the schedule");
				redirect('view-schedules/'.$message_batch_id.'/'.$message_template_id);
		}
		else
		{
				$this->session->set_userdata("error_message","Could not remove the schedule. Please try again");
				redirect('view-schedules/'.$message_batch_id.'/'.$message_template_id);
		}
	}
	public function send_cron_messages()
	{
		if($this->messaging_model->send_cron_messages())
		{
			echo "yes";
			
		}
		else
		{
			echo "no";
		}
	}
	
	public function test_messages($phone = '0726149351', $message = 'Hello World')
	{
		$this->messaging_model->sms($phone,$message);
	}

	public function bulk_add_contacts($message_batch_id,$message_template_id)
	{
		// var_dump($_POST['contacts']);die();
		$total_contacts = sizeof($_POST['contacts']);
		if($total_contacts > 0)
		{	
			for($r = 0; $r < $total_contacts; $r++)
			{	
				$contact = $_POST['contacts'];
				$patient_id = $contact[$r]; 
				//check if card is held
				if($this->messaging_model->add_patient_to_message($patient_id,$message_batch_id,$message_batch_id))
				{
					$this->session->set_userdata('success_message', 'The contact has been deleted successfully');

				}
				else
				{
					$this->session->set_userdata('error_message', 'The contact could not be deleted');
				}
			}
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Please select contacts to delete first');
		}
		redirect('view-senders/'.$message_batch_id.'/'.$message_template_id);
	}
	public function search_members($message_batch_id, $message_template_id)
	{
		$company_id = $this->input->post('company_id');
		$patient_number = $this->input->post('patient_number');
		$patient_phone = $this->input->post('patient_phone');
		$gender_id = $this->input->post('gender_id');
		$status = $this->input->post('status');
		$dob_from = $this->input->post('dob_from');
		$dob_to = $this->input->post('dob_to');
		$patient_status = $this->input->post('patient_status');
		$search_title = $payment = $gender = '';
		
		if(!empty($patient_status))
		{
			$this->db->where('patient_status_id', $patient_status);
			$query = $this->db->get('patient_status');
			$patient_status_name = '';
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$patient_status_name = $row->patient_status_name;
			}
			$search_title .= $patient_status_name.' members';
			$patient_status = ' AND member.patient_status = '.$patient_status;
		}
		
		if(!empty($gender_id))
		{
			if($gender_id == 1)
			{
				$search_title .= ' male members ';
			}
			elseif($gender_id == 2)
			{
				$search_title .= ' female members ';
			}
			$gender = ' AND member.gender_id = '.$gender_id;
			if($gender_id == 0)
			{
				$gender = '';
			}
		}
		
		$current_year = date('Y');
		if($status == 2)
		{
			$search_title .= ' paid invoice for the current year';
			$payment = ' AND member.patient_id IN (SELECT patient_id FROM payment WHERE YEAR(payment_date) >= \''.$current_year.'\')';
		}
		
		else if($status == 3)
		{
			$search_title .= ' unpaid invoice for the current year';
			$payment = ' AND member.patient_id NOT IN (SELECT patient_id FROM payment WHERE YEAR(payment_date) >= \''.$current_year.'\')';
		}
		
		if(!empty($dob_from) && !empty($dob_to))
		{
			$dob_range = ' AND (member.date_of_birth >= \''.$dob_from.'\' OR member.date_of_birth <= \''.$dob_to.'\') AND member.date_of_birth != \'0000-00-00\'';
			$search_title .= 'Date of birth from '.date('jS M Y', strtotime($dob_from)).' to '.date('jS M Y', strtotime($dob_to)).' ';
		}
		
		else if(!empty($dob_from))
		{
			$dob_range = ' AND member.date_of_birth = \''.$dob_from.'\' AND member.date_of_birth != \'0000-00-00\'';
			$search_title .= 'Date of birth of '.date('jS M Y', strtotime($dob_from)).' ';
		}
		
		else if(!empty($dob_to))
		{
			$dob_range = ' AND member.date_of_birth = \''.$dob_to.'\' AND member.date_of_birth != \'0000-00-00\'';
			$search_title .= 'Date of birth of '.date('jS M Y', strtotime($dob_to)).' ';
		}
		
		else
		{
			$dob_range = '';
		}
		
		if(!empty($patient_number))
		{
			$search_title .= ' member number <strong>'.$patient_number.'</strong>';
			$patient_number = ' AND member.patient_number LIKE \'%'.$patient_number.'%\'';
		}
		
		if(!empty($company_id))
		{
			$search_title .= ' company name <strong>'.$company_id.'</strong>';
			$company_id = ' AND member.company = \''.$company_id.'\' ';
		}
		
		if(!empty($patient_phone))
		{
			$search_title .= ' member phone <strong>'.$patient_phone.'</strong>';
			$patient_phone = ' AND member.patient_phone = \''.$patient_phone.'\' ';
		}
		
		//search surname
		if(!empty($_POST['patient_first_name']))
		{
			$search_title .= ' first name <strong>'.$_POST['patient_first_name'].'</strong>';
			$surnames = explode(" ",$_POST['patient_first_name']);
			$total = count($surnames);
			
			$count = 1;
			$surname = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$surname .= ' member.patient_first_name LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\'';
				}
				
				else
				{
					$surname .= ' member.patient_first_name LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\' AND ';
				}
				$count++;
			}
			$surname .= ') ';
		}
		
		else
		{
			$surname = '';
		}
		
		//search other_names
		if(!empty($_POST['patient_surname']))
		{
			$search_title .= ' last name <strong>'.$_POST['patient_surname'].'</strong>';
			$other_names = explode(" ",$_POST['patient_surname']);
			$total = count($other_names);
			
			$count = 1;
			$other_name = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$other_name .= ' member.patient_first_name LIKE \'%'.mysql_real_escape_string($other_names[$r]).'%\'';
				}
				
				else
				{
					$other_name .= ' member.patient_first_name LIKE \'%'.mysql_real_escape_string($other_names[$r]).'%\' AND ';
				}
				$count++;
			}
			$other_name .= ') ';
		}
		
		else
		{
			$other_name = '';
		}
		
		$search = $company_id.$patient_number.$surname.$other_name.$patient_phone.$dob_range.$payment.$gender.$patient_status;
		$this->session->set_userdata('patient_search_item', $search);
		$this->session->set_userdata('patient_search_item_title', $search_title);
		
		redirect('view-senders/'.$message_batch_id.'/'.$message_template_id);
	}
	public function close_search($message_batch_id,$message_template_id)
	{	
		$this->session->unset_userdata('patient_search_item');
		redirect('view-senders/'.$message_batch_id.'/'.$message_template_id);
	}
	public function create_batch_members($message_batch_id,$message_template_id)
	{
		if($this->messaging_model->create_batch_member($message_batch_id))
		{
			$this->session->set_userdata("success_message","You have successfully added batch contacts to this template");
		}
		else
		{
			$this->session->set_userdata("error_message","Sorry somthing went wrong. Please try again");

		}
		redirect('view-senders/'.$message_batch_id.'/'.$message_template_id);
	}

	public function bulk_add_contacts_($message_batch_id, $message_template_id)
	{
		// var_dump($_POST['contacts']);die();
		$total_contacts = sizeof($_POST['contacts']);
		if($total_contacts > 0)
		{	
			for($r = 0; $r < $total_contacts; $r++)
			{	
				$contact = $_POST['contacts'];
				$patient_id = $contact[$r]; 
				//check if card is held
				if($this->messaging_model->add_patient_to_message($patient_id,$message_batch_id,$message_batch_id))
				{
					$this->session->set_userdata('success_message', 'The contact has been deleted successfully');

				}
				else
				{
					$this->session->set_userdata('error_message', 'The contact could not be deleted');
				}
			}
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Please select contacts to delete first');
		}
		redirect('view-senders/'.$message_batch_id.'/'.$message_template_id);
	}
	
	public function custom_contacts_template()
	{
		$this->messaging_model->custom_contacts_template();
	}
	
	function import_custom_contacts($message_batch_id, $message_template_id)
	{
		if(isset($_FILES['import_csv']))
		{
			// var_dump($message_category_id); die();
			if(is_uploaded_file($_FILES['import_csv']['tmp_name']))
			{
				//import products from excel 

				$response = $this->messaging_model->import_custom_contacts($this->csv_path, $message_batch_id, $message_template_id);
				
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
		redirect('view-senders/'.$message_batch_id.'/'.$message_template_id);
	}
	public function remove_all_contacts($message_batch_id,$message_template_id)
	{
		if($this->messaging_model->remove_all_contacts($message_batch_id))
		{
			$this->session->set_userdata("success_message","You have successfully removed all contacts from this batch");
		}
		else
		{
			$this->session->set_userdata("error_message","Sorry somthing went wrong. Please try again");

		}
		redirect('senders-view/'.$message_batch_id.'/'.$message_template_id);
	}

	public function send_appointments()
	{
		$date_tomorrow = date("Y-m-d",strtotime("tomorrow"));

		// $amount = $this->reception_model->get_total_unsent_appointments();
		$dt= $date_tomorrow;
        $dt1 = strtotime($dt);
        $dt2 = date("l", $dt1);
        $dt3 = strtolower($dt2);
  //   	if(($dt3 == "sunday"))
		// {
  //           // echo $dt3.' is weekend'."\n";

  //           $date_tomorrow = strtotime('+1 day', strtotime($dt));
  //           $date_tomorrow = date("Y-m-d",$date_tomorrow);
  //           $date_to_send = 'Monday';
  //       } 
  //   	else
		// {
            // echo $dt3.' is not weekend'."\n";
             $date_tomorrow = $dt;
             $date_to_send = 'tomorrow';
        // }


        // var_dump($amount); die();
		$this->db->select('*');
		$this->db->where('visit.visit_date = "'.$date_tomorrow.'" AND visit.patient_id = patients.patient_id AND visit.visit_delete = 0 AND visit.schedule_id = 0 ');
		$query = $this->db->get('visit,patients');
		// var_dump($query); die();
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$patient_phone = $value->patient_phone1;
				$patient_id = $value->patient_id;
				$visit_id = $value->visit_id;
				$patient_othernames = $value->patient_othernames;
				$patient_surname = $value->patient_surname;
				$time_start = $value->time_start;
				$visit_date = date('jS M Y',strtotime($date_tomorrow));
				// $time_start = date('H:i A',strtotime($time_start));
				$message = 'Hello '.$patient_surname.', please remember that you have a dental appointment scheduled for '.$date_to_send.' '.$visit_date.' at '.$time_start.' Al Hidaya Heights 201, (opposite Al Hidaya Mosque). For more information contact 0717123440.';

				// $patient_phone = 734808007;
				$message_data = array(
						"phone_number" => $patient_phone,
						"entryid" => $patient_id,
						"message" => $message,
						"message_batch_id"=>0,
						'message_status' => 0
					);
				$this->db->insert('messages', $message_data);
				$message_id = $this->db->insert_id();
				
				$patient_phone = str_replace(' ', '', $patient_phone);
				$response = $this->messaging_model->sms($patient_phone,$message);
				// var_dump($response); die();

				$email_message .= $patient_surname.' '.$patient_othernames.' AT '.$time_start.'<br>';
				$visit_update = array('schedule_id' => 1);
				$this->db->where('visit_id',$visit_id);
				$this->db->update('visit', $visit_update);

				if($response == "Success" OR $response == "success")
				{
					


					$service_charge_update = array('message_status' => 1,'delivery_message'=>'Success','sms_cost'=>3,'message_type'=>'Appointment Reminder');
					$this->db->where('message_id',$message_id);
					$this->db->update('messages', $service_charge_update);

				}
				else
				{
					$service_charge_update = array('message_status' => 0,'delivery_message'=>'Success','sms_cost'=>0,'message_type'=>'Appointment Reminder');
					$this->db->where('message_id',$message_id);
					$this->db->update('messages', $service_charge_update);


				}

				
			}
			if($email_message == '')
			{

			}
			else
			{
				// $this->send_email_for_appointment($email_message);
				$date_tomorrow = date('Y-m-d');
				$date_tomorrow = date("Y-m-d", strtotime("+1 day", strtotime($date_tomorrow)));
				$visit_date = date('jS M Y',strtotime($date_tomorrow));
				$branch = $this->config->item('branch_name');
				$message_result['subject'] = $date_tomorrow.' Appointment report';
				$v_data['persons'] = $email_message;
				$text =  $this->load->view('reception/emails_items',$v_data,true);

				// echo $text; die();
				$message_result['text'] = $text;
				$contacts = $this->site_model->get_contacts();
				$sender_email =$this->config->item('sender_email');//$contacts['email'];
				$shopping = "";
				$from = $sender_email; 
				
				$button = '';
				$sender['email']= $sender_email; 
				$sender['name'] = $contacts['company_name'];
				$receiver['name'] = $message_result['subject'];
				// $payslip = $title;

				$sender_email = $sender_email;
				$tenant_email = $this->config->item('recepients_email').'/'.$sender_email;
				// var_dump($sender_email); die();
				$email_array = explode('/', $tenant_email);
				$total_rows_email = count($email_array);

				for($x = 0; $x < $total_rows_email; $x++)
				{
					$receiver['email'] = $email_tenant = $email_array[$x];

					$this->email_model->send_sendgrid_mail($receiver, $sender, $message_result, NULL);		
					

				}
			}
		}
		// redirect('appointments');
		// echo '<script language="JavaScript">';
		// echo 'window.self.close();';
		// echo '</script>';
	}
	public function send_todays_appointments()
	{
		$date_tomorrow = date("Y-m-d");

		// $date_tomorrow = date('Y-m-d', strtotime($date_tomorrow . " +1 days"));


		// $date_tomorrow = date('Y-m-d');
		// $this->db->select('*');
		// $this->db->where('visit.visit_date = "'.$date_tomorrow.'" AND visit.patient_id = patients.patient_id AND visit.schedule_id = 0 ');
	
		// $query = $this->db->get('visit,patients');

			$this->db->select('*,appointments.appointment_id AS appointment_idd');
		$this->db->where('appointments.appointment_date = "'.$date_tomorrow.'" AND visit.patient_id = patients.patient_id AND visit.visit_delete = 0 AND appointments.appointment_delete = 0 AND appointments.visit_id = visit.visit_id AND (appointments.appointment_status <> 6 OR appointments.appointment_status <> 7 OR appointments.appointment_status <> 3 OR appointments.appointment_status <> 8)  AND appointments.appointment_rescheduled = 0 AND visit.branch_id = branch.branch_id ');
		// $this->db->limit(1);
		$this->db->order_by('appointments.resource_id','ASC');
		$query = $this->db->get('visit,patients,appointments,branch');

		// var_dump($query);die();

		$email_message = '';
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$patient_phone = $value->patient_phone1;
				$patient_id = $value->patient_id;
				$patient_othernames = $value->patient_othernames;
				$patient_surname = $value->patient_surname;
				$time_start = $value->time_start;
				$visit_id = $value->visit_id;

				$time_start = $value->time_start;
				$branch_name = $value->branch_name;
				$branch_phone = $value->branch_phone;
				$appointment_id = $value->appointment_idd;
				$appointment_status = $value->appointment_status;
				$time_start = date('H:i A',strtotime($value->appointment_start_time));

				$surname = explode(' ', $patient_surname);
				$patient_name = $surname[0];

				$visit_date = date('jS M Y',strtotime($date_tomorrow));
				$message = 'Dear '.$patient_name.', kindly remember your appointment at Star Dental Clinic today at '.$time_start.'. Al Hidaya Heights 201, (opposite Al Hidaya Mosque). For more information contact 0717123440.';

				$message_data = array(
						"phone_number" => $patient_phone,
						"entryid" => $patient_id,
						"message" => $message,
						"message_batch_id"=>0,
						'message_status' => 0
					);
				$this->db->insert('messages', $message_data);
				$message_id = $this->db->insert_id();
				// $patient_phone = '704808007';
				$patient_phone = str_replace(' ', '', $patient_phone);
				$response = $this->messaging_model->sms($patient_phone,$message);
				
				// $status = $response->success;
				// var_dump($response);die();	
					

				if($response== 'Success')
				{

					// var_dump('you'); die();

					$email_message .= $patient_surname.' '.$patient_othernames.' AT '.$time_start.'<br>';
					$visit_update = array('schedule_id' => 1);
					$this->db->where('visit_id',$visit_id);
					$this->db->update('visit', $visit_update);


					if($appointment_status == 2 )
					{

					}
					else
					{
						
						$appointment_update = array('appointment_status' => 6);
						$this->db->where('appointment_id',$appointment_id);
						$this->db->update('appointments', $appointment_update);
					}

					// $email_message .= $patient_surname.' '.$patient_othernames.' AT '.$time_start.'<br>';
					

					
					$service_charge_update = array('message_status' => 1,'delivery_message'=>'Success','sms_cost'=>3);
					$this->db->where('message_id',$message_id);
					$this->db->update('messages', $service_charge_update);

				}
				else
				{

					if($appointment_status == 2 )
					{

					}
					else
					{
						
						$appointment_update = array('appointment_status' => 7);
						$this->db->where('appointment_id',$appointment_id);
						$this->db->update('appointments', $appointment_update);
					}

					// var_dump('sdada'); die();
					$service_charge_update = array('message_status' => 0,'delivery_message'=>'Success','sms_cost'=>0);
					$this->db->where('message_id',$message_id);
					$this->db->update('messages', $service_charge_update);


				}
			}


			if($email_message == '')
			{

			}
			else
			{
				// $this->send_email_for_appointment($email_message);
				$date_tomorrow = date('Y-m-d');
				$date_tomorrow = date("Y-m-d", strtotime("+1 day", strtotime($date_tomorrow)));
				$visit_date = date('jS M Y',strtotime($date_tomorrow));
				$branch = $this->config->item('branch_name');
				$message_result['subject'] = 'Appointment report';
				$v_data['persons'] = $email_message;
				$text =  $this->load->view('reception/emails_items',$v_data,true);

				// echo $text; die();
				$message_result['text'] = $text;
				$contacts = $this->site_model->get_contacts();
				$sender_email =$this->config->item('sender_email');//$contacts['email'];
				$shopping = "";
				$from = $sender_email; 
				
				$button = '';
				$sender['email']= $sender_email; 
				$sender['name'] = $contacts['company_name'];
				$receiver['name'] = $message_result['subject'];
				// $payslip = $title;

				$sender_email = $sender_email;
				$tenant_email = $this->config->item('appointments_email');
				// var_dump($tenant_email); die();
				$email_array = explode('/', $tenant_email);
				$total_rows_email = count($email_array);

				for($x = 0; $x < $total_rows_email; $x++)
				{
					$receiver['email'] = $email_tenant = $email_array[$x];

					$this->email_model->send_sendgrid_mail($receiver, $sender, $message_result, NULL);		
					

				}
			}
		}
		// redirect('appointments');
		echo '<script language="JavaScript">';
		echo 'window.self.close();';
		echo '</script>';
	}
}