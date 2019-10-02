<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MX_Controller 
{
	function __construct()
	{
		parent:: __construct();
		
		$this->load->model('admin/admin_model');
		$this->load->model('auth/auth_model');
		$this->load->model('site/site_model');
		$this->load->model('admin/reports_model');
		$this->load->model('admin/dashboard_model');
		$this->load->model('admin/sections_model');
		$this->load->model('reception/reception_model');
		$this->load->model('hr/personnel_model');
		
		// if(!$this->auth_model->check_login())
		// {
		// 	redirect('login');
		// }
	}
    
	/*
	*
	*	Dashboard
	*
	*/
	public function dashboard() 
	{
		$data['title'] = $this->site_model->display_page_title();
		$v_data['title'] = $data['title'];



		// if($page == NULL)
		// {
		// 	$page = 0;
		// }
		$page = 0;
		
		$table= 'visit,patients';
		$where='visit.close_card = 2 AND visit.patient_id = patients.patient_id AND visit.visit_delete = 0 AND visit.visit_date = "'.date('Y-m-d').'" AND preauth = 0';
		$config["per_page"] = $v_data['per_page'] = $per_page = 30;
		if($page==0)
		{

			$counted = 0;
		}
		else if($page > 0)
		{

			$counted = $per_page*$page;
		}
		$v_data['page'] = $page;
		$page = $counted;
		$v_data['total_rows'] = $this->reception_model->count_items($table, $where);
		$query = $this->admin_model->get_all_visits_parent($table, $where, $config["per_page"], $page);

		$v_data['appointment_list'] = $query;


		$table= 'visit,patients';
		$where='(visit.close_card = 0 OR visit.close_card = 1 OR visit.close_card = 4 OR visit.close_card = 3 OR visit.close_card = 5) AND visit.patient_id = patients.patient_id AND visit.visit_delete = 0  AND visit.visit_date = "'.date('Y-m-d').'" AND preauth = 0';
		$config["per_page"] = $v_data['per_page'] = $per_page = 30;
		if($page==0)
		{

			$counted = 0;
		}
		else if($page > 0)
		{

			$counted = $per_page*$page;
		}
		$v_data['page'] = $page;
		$page = $counted;
		$v_data['total_rows'] = $this->reception_model->count_items($table, $where);
		$query = $this->admin_model->get_all_visits_parent($table, $where, $config["per_page"], $page);

		$v_data['todays_visit'] = $query;


		$date_tomorrow = date("Y-m-d",strtotime("tomorrow"));

		$dt= $date_tomorrow;
        $dt1 = strtotime($dt);
        $dt2 = date("l", $dt1);
        $dt3 = strtolower($dt2);
    	if(($dt3 == "sunday"))
		{
            // echo $dt3.' is weekend'."\n";

            $date_tomorrow = strtotime('+1 day', strtotime($dt));
            $date_tomorrow = date("Y-m-d",$date_tomorrow);
            $date_to_send = 'Monday';
        } 
    	else
		{
            // echo $dt3.' is not weekend'."\n";
             $date_tomorrow = $dt;
             $date_to_send = 'Tomorrow';
        }


		$table= 'visit,patients';
		$where='visit.close_card = 2 AND visit.patient_id = patients.patient_id AND visit.visit_delete = 0  AND visit.visit_date = "'.$date_tomorrow.'" AND preauth = 0';
		$config["per_page"] = $v_data['per_page'] = $per_page = 30;
		if($page==0)
		{

			$counted = 0;
		}
		else if($page > 0)
		{

			$counted = $per_page*$page;
		}
		$v_data['page'] = $page;
		$v_data['date_to_send'] = $date_to_send;
		$page = $counted;
		$v_data['total_rows'] = $this->reception_model->count_items($table, $where);
		$query = $this->admin_model->get_all_visits_parent($table, $where, $config["per_page"], $page);

		$v_data['tomorrows_appointments'] = $query;





		
		$data['content'] = $this->load->view('list_dashboard', $v_data, true);
		// $data['content'] = $this->load->view('profile_page', $v_data, true);
		
		$this->load->view('templates/general_page', $data);
	}


	public function profile() 
	{
		$data['title'] = $this->site_model->display_page_title();
		$v_data['title'] = $data['title'];
		$personnel_id = $this->session->userdata('personnel_id');
		$v_data['leave'] = $this->personnel_model->get_personnel_leave($personnel_id);
		$v_data['leave_types'] = $this->personnel_model->get_leave_types();
		$v_data['personnel_query'] = $this->personnel_model->get_personnel($personnel_id);
		// $data['content'] = $this->load->view('dashboard', $v_data, true);
		$data['content'] = $this->load->view('profile_page', $v_data, true);
		
		$this->load->view('templates/general_page', $data);
	}
    
	/*
	*
	*	Edit admin configuration
	*
	*/
	public function configuration()
	{
		$data['title'] = $this->site_model->display_page_title();
		$v_data['title'] = $data['title'];
		$v_data['configuration'] = $this->admin_model->get_configuration();
		
		$data['content'] = $this->load->view('configuration', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function edit_configuration($configuration_id)
    {
    	$this->form_validation->set_rules('mandrill', 'Email API key', 'xss_clean');
    	$this->form_validation->set_rules('sms_key', 'SMS key', 'xss_clean');
    	$this->form_validation->set_rules('sms_user', 'SMS User', 'xss_clean');
		
		//if form conatins valid data
		if ($this->form_validation->run())
		{
			if($this->admin_model->edit_configuration($configuration_id))
			{
				$this->session->set_userdata("success_message", "Configuration updated successfully");
			}
			
			else
			{
				$this->session->set_userdata("error_message","Could not update configuration. Please try again");
			}
		}
		else
		{
			$this->session->set_userdata("error_message", validation_errors());
		}
		
		redirect('administration/configuration');
    }
	
	public function clickatel_sms()
	{
        // This will override any configuration parameters set on the config file
        $params = array('user' => 'amasitsa', 'password' => 'GRICWfQAfOEAHK', 'api_id' => '3557139');  
        $this->load->library('clickatel', $params);
		
        // Send the message
        $this->clickatel->send_sms('+254726149351', 'This is a test message');

        // Get the reply
        echo $this->clickatel->last_reply();

        // Send message to multiple numbers
        /*$numbers = array('351965555555', '351936666666', '351925555555');
        $this->clickatel->send_sms($numbers, 'This is a test message');*/
    }
	
	public function sms()
	{
        // This will override any configuration parameters set on the config file
		// max of 160 characters
		// to get a unique name make payment of 8700 to Africastalking/SMSLeopard
		// unique name should have a maximum of 11 characters
        $params = array('username' => 'alviem', 'apiKey' => '1f61510514421213f9566191a15caa94f3d930305c99dae2624dfb06ef54b703');  
        $this->load->library('africastalkinggateway', $params);
		
        // Send the message
		try 
		{
        	$results = $this->africastalkinggateway->sendMessage('+254770827872', 'Halo Martin. I am sending this message from the ERP');
			
			//var_dump($results);die();
			foreach($results as $result) {
				// status is either "Success" or "error message"
				echo " Number: " .$result->number;
				echo " Status: " .$result->status;
				echo " MessageId: " .$result->messageId;
				echo " Cost: "   .$result->cost."\n";
			}
		}
		
		catch(AfricasTalkingGatewayException $e)
		{
			echo "Encountered an error while sending: ".$e->getMessage();
		}
    }
   	public function calendar() 
	{
		$branch_id = $this->session->userdata('branch_id');
		$branch_name = $this->session->userdata('branch_name');
		$data['title'] = 'Online Diary';
		$v_data['title'] = $data['title'];
		
		$data['content'] = $this->load->view('calendar', $v_data, true);
		
		$this->load->view('templates/general_page', $data);
	}


	/*
	*
	*	Dashboard
	*
	*/
	public function patients_turnover() 
	{
		$data['title'] = $this->site_model->display_page_title();
		$v_data['title'] = $data['title'];
		$personnel_id = $this->session->userdata('personnel_id');
		$v_data['leave'] = $this->personnel_model->get_personnel_leave($personnel_id);
		$v_data['leave_types'] = $this->personnel_model->get_leave_types();
		$v_data['personnel_query'] = $this->personnel_model->get_personnel($personnel_id);
		
		$data['content'] = $this->load->view('dashboard', $v_data, true);
		// $data['content'] = $this->load->view('profile_page', $v_data, true);
		
		$this->load->view('templates/general_page', $data);
	}


}
?>