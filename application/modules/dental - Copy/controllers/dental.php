<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/auth/controllers/auth.php";

class Dental extends auth
{	
	var $document_upload_path;
	var $document_upload_location;
	function __construct()
	{
		parent:: __construct();
		
		$this->load->library('image_lib');

		$this->document_upload_path = realpath(APPPATH . '../assets/document_uploads');
		$this->document_upload_location = base_url().'assets/document_uploads/';
		
		$this->load->model('dental_model');
		$this->load->model('nurse/nurse_model');
		$this->load->model('reception/reception_model');
		$this->load->model('accounts/accounts_model');
		$this->load->model('database');
		$this->load->model('hr/personnel_model');
		$this->load->model('admin/sections_model');
		$this->load->model('admin/admin_model');
		$this->load->model('admin/file_model');
		$this->load->model('online_diary/rooms_model');
		// $this->load->model('medical_admin/medical_admin_model');
		// $this->load->model('pharmacy/pharmacy_model');
		
		$this->load->model('auth/auth_model');
		// if(!$this->auth_model->check_login())
		// {
		// 	redirect('login');
		// }
	}
	public function index()
	{
		$this->session->unset_userdata('visit_search');
		$this->session->unset_userdata('patient_search');
		
		$where = 'visit_department.visit_id = visit.visit_id AND visit_department.department_id = 2 AND visit_department.visit_department_status = 1 AND visit.patient_id = patients.patient_id AND visit.close_card = 0 AND visit.visit_date = \''.date('Y-m-d').'\' AND visit.personnel_id = '.$this->session->userdata('personnel_id');
		
		$table = 'visit_department, visit, patients';
		$query = $this->reception_model->get_all_ongoing_visits($table, $where, 6, 0);
		$v_data['query'] = $query;
		$v_data['page'] = 0;
		
		$v_data['visit'] = 0;
		$v_data['doctor_appointments'] = 1;
		$v_data['department'] = 2;
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		
		$data['content'] = $this->load->view('nurse/nurse_dashboard', $v_data, TRUE);
		
		$data['title'] = 'Dashboard';
		$data['sidebar'] = 'dental_sidebar';
		$this->load->view('admin/templates/general_page', $data);	
	}
	
	public function dental_queue($page_name = NULL)
	{
		// this is it
		
		$where = 'visit_department.visit_id = visit.visit_id AND visit_department.department_id = 10 AND visit_department.visit_department_status = 1 AND visit.patient_id = patients.patient_id AND visit.close_card = 0 AND visit.visit_date = \''.date('Y-m-d').'\' AND visit.visit_type = visit_type.visit_type_id';
		
		$table = 'visit_department, visit, patients, visit_type';
		$visit_search = $this->session->userdata('visit_search');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		}
		
		if($page_name != NULL)
		{
			$segment = 4;
		}
		
		else
		{
			$segment = 3;
		}
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'dental/dental_queue/'.$page_name;
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
		$query = $this->reception_model->get_all_ongoing_visits($table, $where, $config["per_page"], $page, 'ASC');
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		
		$data['title'] = 'Dental Queue';
		$v_data['title'] = 'Dental Queue';
		$v_data['module'] = 1;
		
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		
		$data['content'] = $this->load->view('dental_queue', $v_data, true);
		
		$data['sidebar'] = 'dental_sidebar';
		
		
		$this->load->view('admin/templates/general_page', $data);
		// end of it
	}
	public function queue_cheker($page_name = NULL)
	{
		$where = 'visit_department.visit_id = visit.visit_id AND visit_department.department_id = 2 AND visit_department.visit_department_status = 1 AND visit.patient_id = patients.patient_id AND visit.close_card = 0 AND visit.visit_date = \''.date('Y-m-d').'\' AND visit.personnel_id = '.$this->session->userdata('personnel_id');
		$table = 'visit_department, visit, patients';
		$items = "*";
		$order = "visit.visit_id";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		if(count($result) > 0)
		{
			echo 1;
		}
		else
		{
			echo 0;
		}

	}
	public function patient_card($visit_id, $mike = NULL)
	{
		$patient = $this->reception_model->patient_names2(NULL, $visit_id);
		$visit_type = $patient['visit_type'];
		$patient_type = $patient['patient_type'];
		$patient_othernames = $patient['patient_othernames'];
		$patient_surname = $patient['patient_surname'];
		$patient_date_of_birth = $patient['patient_date_of_birth'];
		$age = $this->reception_model->calculate_age($patient_date_of_birth);
		$gender = $patient['gender'];
		$account_balance = $patient['account_balance'];
		$phone_number = $patient['patient_phone_number'];
		$patient_id = $patient['patient_id'];
		$visit_type_name = $patient['visit_type_name'];
		$v_data['patient_details'] = $this->reception_model->get_patient_data($patient_id);
		$v_data['insurance'] = $this->reception_model->get_insurance();
		$v_data['relationships'] = $this->reception_model->get_relationship();
		// $v_data['religions'] = $this->reception_model->get_religion();
		// $v_data['civil_statuses'] = $this->reception_model->get_civil_status();
		// $v_data['titles'] = $this->reception_model->get_title();
		$v_data['genders'] = $this->reception_model->get_gender();

		$insurance_company = $this->reception_model->get_patient_insurance_company($patient_id);
		$v_data['document_types'] = $this->dental_model->all_document_types();
		$v_data['doctor'] = $this->reception_model->get_doctor();
		$v_data['patient_other_documents'] = $this->dental_model->get_document_uploads($patient_id);

		$personnel_id = $this->session->userdata('personnel_id');
		$department_id = $this->reception_model->get_personnel_department($personnel_id);
		// var_dump($department_id); die();
		$personnel_check = FALSE;
		if($department_id == 4)
		{
			//  check if the doctor is the one seing the patient 
			$this->db->where('visit_id ='.$visit_id.' AND personnel_id ='.$personnel_id.' AND visit.close_card = 0');
			$query = $this->db->get('visit');
			if($query->num_rows() == 1)
			{
				$update_array['close_card'] = 4;
				$this->db->where('visit_id',$visit_id);
				$this->db->update('visit',$update_array);
				$personnel_check = TRUE;
			}

			

			
			
		}
		$cash_balance = $this->accounts_model->get_cash_balance($patient_id);
		$insurance_balance = $this->accounts_model->get_insurance_balance($patient_id);
		
		$v_data['patient'] = 'Name: <span style="font-weight: normal;">'.$patient_surname.' '.$patient_othernames.'</span>Visit.: <span style="font-weight: normal;">'.$visit_type_name.' </span>Balance Cash : <span style="font-weight: bold;">'.$cash_balance.'</span>  Insurance : <span style="font-weight: bold;">'.$insurance_balance.'</span> <a href="'.site_url().'administration/individual_statement/'.$patient_id.'/2" class="btn btn-sm btn-primary" target="_blank" style="margin-top: 5px;">Statement</a>';
		
		$v_data['mike'] = $mike;
		$v_data['visit_id'] = $visit_id;
		$v_data['patient_id'] = $patient_id;
		$v_data['dental'] = 1;

		$order = 'service_charge.service_charge_name';
		$where = 'service_charge.service_id = service.service_id AND service.service_delete = 0 AND service_charge.visit_type_id = visit_type.visit_type_id AND service_charge.visit_type_id = 1 AND service_charge.service_charge_delete = 0';

		$table = 'service_charge,visit_type,service';
		$config["per_page"] = 0;
		$procedure_query = $this->nurse_model->get_other_procedures($table, $where, $order);

		$rs9 = $procedure_query->result();
		$procedures = '';
		foreach ($rs9 as $rs10) :


		$procedure_id = $rs10->service_charge_id;
		$proced = $rs10->service_charge_name;
		$visit_type = $rs10->visit_type_id;
		$visit_type_name = $rs10->visit_type_name;

		$stud = $rs10->service_charge_amount;

		    $procedures .="<option value='".$procedure_id."'>".$proced." KES.".$stud."</option>";

		endforeach;

		$v_data['services_list'] = $procedures;
		$v_data['personnel_check'] = $personnel_check;

		// var_dump($personnel_check); die();
		
		$data['content'] = $this->load->view('patient_card', $v_data, true);
		
		$data['title'] = 'Patient Card';
		
		$data['sidebar'] = 'dental_sidebar';
		
		if(($mike != NULL) && ($mike != 'a')){
			$this->load->view('admin/templates/general_page', $data);	
		}else{
			$this->load->view('admin/templates/general_page', $data);	
		}
	}
	public function search_dental_billing($visit_id)
	{
		$this->form_validation->set_rules('search_item', 'Search', 'trim|required|xss_clean');
		
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			$search = ' AND service_charge_name LIKE \'%'.$this->input->post('search_item').'%\'';
			$this->session->set_userdata('billing_search', $search);
		}
		
		$this->dental_services($visit_id);
	}
	public function close_dental_billing_search($visit_id)
	{
		$this->session->unset_userdata('billing_search');
		$this->dental_services($visit_id);
	}
	function dental_services($visit_id)
	{
		//check patient visit type
		$rs = $this->nurse_model->check_visit_type($visit_id);
		if(count($rs)>0){
		  foreach ($rs as $rs1) {
			# code...
			  $visit_t = $rs1->visit_type;
		  }
		}
		
		$order = 'service_charge_name';
		
		$where = 'service.service_id = service_charge.service_id AND service.service_name ="Dental Procedures" AND service_charge.service_charge_status = 1 ';
		$billing_search = $this->session->userdata('billing_search');
		
		if(!empty($billing_search))
		{
			$where .= $billing_search;
		}
		
		$table = 'service,service_charge';
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'dental/dental_services/'.$visit_id;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = 4;
		$config['per_page'] = 15;
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
		
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->nurse_model->get_procedures($table, $where, $config["per_page"], $page, $order);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		
		$data['title'] = 'Billing List';
		$v_data['title'] = 'Billing List';
		
		$v_data['visit_id'] = $visit_id;
		$data['content'] = $this->load->view('billing_list', $v_data, true);
		
		$data['title'] = 'Billing List';
		$this->load->view('admin/templates/no_sidebar', $data);	
	}

	public function view_billing($visit_id)
	{
		$personnel_id = $this->session->userdata('personnel_id');
		
		$this->db->where('visit_id ='.$visit_id.' AND personnel_id ='.$personnel_id.' AND visit.close_card = 4');
		$query = $this->db->get('visit');
		$personnel_check = FALSE;
		if($query->num_rows() == 1)
		{
			$personnel_check = TRUE;
		}
		$data = array('visit_id'=>$visit_id,'personnel_check'=>$personnel_check);

		$this->load->view('view_billing',$data);
	}
	function billing_service($service_id,$visit_id,$suck){
		$data = array('procedure_id'=>$service_id,'visit_id'=>$visit_id,'suck'=>$suck);
		$this->load->view('billing/billing',$data);	
	}
	public function billing_total($procedure_id,$units,$amount){
		$visit_data = array('visit_charge_units'=>$units,'charged'=>1);
		$this->db->where(array("visit_charge_id"=>$procedure_id));
		$this->db->update('visit_charge', $visit_data);
	}
	public function save_other_deductions($visit_id)
	{
		$visit_data = array('payment_info'=>$this->input->post('notes'));
		$this->db->where(array("visit_id"=>$visit_id));
		$this->db->update('visit', $visit_data);

	}
	public function save_other_patient_sickoff($visit_id)
	{
		$visit_data = array('sick_leave_note'=>$this->input->post('notes'));
		$this->db->where(array("visit_id"=>$visit_id));
		$this->db->update('visit', $visit_data);

	}
	function delete_billing($procedure_id)
	{
		$this->db->where(array("visit_charge_id"=>$procedure_id));
		$this->db->delete('visit_charge', $visit_data);
	}
	public function send_to_accounts($visit_id)
	{

		// check if the notes have been written

		$this->db->where('visit_id',$visit_id);
		$query = $this->db->get('doctor_patient_notes');

		// hpco
		$hpco_notes=$this->input->post('hpco'.$visit_id);

		$rs = $this->nurse_model->get_hpco_notes($visit_id);
		$num_doc_notes = count($rs);		
		
		// tca
		$tca_notes=$this->input->post('tca'.$visit_id);

		$rs = $this->nurse_model->get_tca_notes($visit_id);
		$num_tca_notes = count($rs);				
		
		// save findings

		$findings_notes=$this->input->post('findings'.$visit_id);

		$rs = $this->nurse_model->get_findings_notes($visit_id);
		$num_findings_notes = count($rs);		
		

		// save histories

		$past_medical_hx=$this->input->post('past_medical_hx'.$visit_id);
		$past_dental_hx=$this->input->post('past_dental_hx'.$visit_id);
		$rs = $this->nurse_model->get_histories_notes($visit_id);
		$num_past_dental_notes = count($rs);
		
		
		// plan

		$plan_notes=$this->input->post('plan'.$visit_id);
		$rs = $this->nurse_model->get_plan_notes($visit_id);
		$num_plan_notes = count($rs);
		// $checker_one = FALSE;
		// if($query->num_rows() > 0){
		// 	$doctor_notes ='';
		// 	foreach ($query->result() as $key => $value) {
		// 		# code...
		// 		$doctor_notes = $value->doctor_notes;
		// 	}

		// 	if(empty($doctor_notes))
		// 	{
		// 		// redirect("dental/dental_queue");
		// 		$checker_one = FALSE;
		// 	}
		// 	else
		// 	{
		// 		$checker_one = TRUE;			

		// 	}
		// }
		// else
		// {
		// 	$checker_one = FALSE;
		// }
		// var_dump($checker_one);die();
		if($num_doc_notes > 0 AND $num_tca_notes > 0){
			$checker_one = TRUE;
		}
		else
		{
			$checker_one = FALSE;
		}


		if($num_plan_notes > 0 AND $num_past_dental_notes > 0 AND $num_findings_notes > 0 AND $num_tca_notes > 0){

			$checker_two = TRUE;

		}
		else
		{
			$checker_two = FALSE;
		}


		if($checker_one == TRUE OR $checker_two == TRUE)
		{

			if($this->reception_model->set_visit_department($visit_id, 6))
			{
				// $update_array['close_card'] = 3;
				// $this->db->where('visit_id',$visit_id);
				// $this->db->update('visit',$update_array);

				$this->session->set_userdata('success_message', 'Patient has been sent successfully to accounts office');
				echo json_encode("Patient has been sent successfully to accounts office");
					redirect("queue");
			}
			else
			{
				$this->session->set_userdata('error_message', 'Sorry something went wrong please try to send the patient again');
				echo json_encode("Sorry something went wrong please try to send the patient again");
				redirect("patient-card/".$visit_id);
			}

		}
		else
		{
			echo json_encode("Sorry, you have to write todays notes to be able to send to accounts");
			$this->session->set_userdata('error_message', 'Sorry, you have to write todays notes to be able to send to accounts');
			redirect("patient-card/".$visit_id);
		}

		
		

	}
	public function send_to_pharmacy($visit_id)
	{
		if($this->reception_model->set_visit_department($visit_id, 5))
		{
			redirect("dental/dental_queue");
		}
		else
		{
			FALSE;
		}
	}
	public function send_to_labs($visit_id)
	{
		if($this->reception_model->set_visit_department($visit_id, 4))
		{
			redirect("dental/dental_queue");
			
		}
		else
		{
			FALSE;
		}
	}
	// new things ending
	public function save_current_notes($visit_id)
	{
		$notes=$this->input->post('oral_examination'.$visit_id);
		
		
		$rs = $this->nurse_model->get_oe_notes($visit_id);
		$num_oe_notes = count($rs);
		if($num_oe_notes == 0){	

			$visit_data = array('visit_id'=>$visit_id,'oe_description'=>$notes);
			$this->db->insert('visit_oe', $visit_data);

		}
		else {

			$visit_data = array('oe_description'=>$notes);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_oe', $visit_data);
		}



		// hpco
		$hpco_notes=$this->input->post('hpco'.$visit_id);

		$rs = $this->nurse_model->get_hpco_notes($visit_id);
		$num_doc_notes = count($rs);
		
		if($num_doc_notes == 0){	
			$visit_data = array('visit_id'=>$visit_id,'hpco_description'=>$hpco_notes);
			$this->db->insert('visit_hpco', $visit_data);

		}
		else {
			$visit_data = array('hpco_description'=>$hpco_notes);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_hpco', $visit_data);
		}

		// tca
		$tca_notes=$this->input->post('tca'.$visit_id);

		$rs = $this->nurse_model->get_tca_notes($visit_id);
		$num_doc_notes = count($rs);
		
		if($num_doc_notes == 0){	
			$visit_data = array('visit_id'=>$visit_id,'tca_description'=>$tca_notes);
			$this->db->insert('visit_tca', $visit_data);

		}
		else {
			$visit_data = array('tca_description'=>$tca_notes);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_tca', $visit_data);
		}

		// xra

		$rx_notes= $this->input->post('rx'.$visit_id);

		$rs = $this->nurse_model->get_rxdone_notes($visit_id);
		$num_doc_notes = count($rs);
		
		if($num_doc_notes == 0){	
			$visit_data = array('visit_id'=>$visit_id,'rx_description'=>$rx_notes);
			$this->db->insert('visit_rx', $visit_data);

		}
		else {
			$visit_data = array('rx_description'=>$rx_notes);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_rx', $visit_data);
		}
		// save investigations

		$investigations_notes=$this->input->post('investigations'.$visit_id);

		$rs = $this->nurse_model->get_investigations_notes($visit_id);
		$num_doc_notes = count($rs);
		
		if($num_doc_notes == 0){	
			$visit_data = array('visit_id'=>$visit_id,'investigation'=>$investigations_notes);
			$this->db->insert('visit_investigations', $visit_data);

		}
		else {
			$visit_data = array('investigation'=>$investigations_notes);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_investigations', $visit_data);
		}

		
		redirect('dental/patient_card/'.$visit_id);
	}
	public function save_new_notes($visit_id)
	{
		// hpco
		$hpco_notes=$this->input->post('hpco'.$visit_id);

		$rs = $this->nurse_model->get_hpco_notes($visit_id);
		$num_doc_notes = count($rs);
		
		if($num_doc_notes == 0){	
			$visit_data = array('visit_id'=>$visit_id,'hpco_description'=>$hpco_notes);
			$this->db->insert('visit_hpco', $visit_data);

		}
		else {
			$visit_data = array('hpco_description'=>$hpco_notes);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_hpco', $visit_data);
		}

		// tca
		$tca_notes=$this->input->post('tca'.$visit_id);

		$rs = $this->nurse_model->get_tca_notes($visit_id);
		$num_doc_notes = count($rs);
		
		if($num_doc_notes == 0){	
			$visit_data = array('visit_id'=>$visit_id,'tca_description'=>$tca_notes);
			$this->db->insert('visit_tca', $visit_data);

		}
		else {
			$visit_data = array('tca_description'=>$tca_notes);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_tca', $visit_data);
		}

		

		// xra

		$rx_notes= $this->input->post('rx'.$visit_id);

		$rs = $this->nurse_model->get_rxdone_notes($visit_id);
		$num_doc_notes = count($rs);
		
		if($num_doc_notes == 0){	
			$visit_data = array('visit_id'=>$visit_id,'rx_description'=>$rx_notes);
			$this->db->insert('visit_rx', $visit_data);

		}
		else {
			$visit_data = array('rx_description'=>$rx_notes);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_rx', $visit_data);
		}
		// occlusal report

		$occlusal_exam=$this->input->post('occlusal_exam'.$visit_id);

		$rs = $this->nurse_model->get_occlusal_exam_notes($visit_id);
		$num_doc_notes = count($rs);
		
		if($num_doc_notes == 0){	
			$visit_data = array('visit_id'=>$visit_id,'occlusal_exam_description'=>$occlusal_exam);
			$this->db->insert('visit_occlusal_exam', $visit_data);

		}
		else {
			$visit_data = array('occlusal_exam_description'=>$occlusal_exam);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_occlusal_exam', $visit_data);
		}

		// save findings

		$findings_notes=$this->input->post('findings'.$visit_id);

		$rs = $this->nurse_model->get_findings_notes($visit_id);
		$num_doc_notes = count($rs);
		
		if($num_doc_notes == 0){	
			$visit_data = array('visit_id'=>$visit_id,'finding_description'=>$findings_notes);
			$this->db->insert('visit_finding', $visit_data);

		}
		else {
			$visit_data = array('finding_description'=>$findings_notes);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_finding', $visit_data);
		}

		// save histories

		$past_medical_hx=$this->input->post('past_medical_hx'.$visit_id);
		$past_dental_hx=$this->input->post('past_dental_hx'.$visit_id);

		$rs = $this->nurse_model->get_histories_notes($visit_id);
		$num_doc_notes = count($rs);
		
		if($num_doc_notes == 0){	
			$visit_data = array('visit_id'=>$visit_id,'past_medical_history'=>$past_medical_hx, 'past_dental_history'=>$past_dental_hx);
			$this->db->insert('visit_history', $visit_data);

		}
		else {
			$visit_data = array('past_medical_history'=>$past_medical_hx, 'past_dental_history'=>$past_dental_hx);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_history', $visit_data);
		}

		// save oc

		$filled=$this->input->post('filled'.$visit_id);
		$missing=$this->input->post('missing'.$visit_id);
		$decayed=$this->input->post('decayed'.$visit_id);
		$soft_tissue=$this->input->post('soft_tissue'.$visit_id);
		$general=$this->input->post('general'.$visit_id);
		$others=$this->input->post('others'.$visit_id);

		$rs = $this->nurse_model->get_oc_notes($visit_id);
		$num_doc_notes = count($rs);
		
		if($num_doc_notes == 0){	
			$visit_data = array('visit_id'=>$visit_id,'filled'=>$filled, 'missing' => $missing, 'decayed' => $decayed, 'soft_tissue'=>$soft_tissue , 'general'=>$general , 'other'=>$others);
			$this->db->insert('visit_oc', $visit_data);

		}
		else {
			$visit_data = array('filled'=>$filled, 'missing' => $missing, 'decayed' => $decayed, 'soft_tissue'=>$soft_tissue , 'general'=>$general , 'other'=>$others);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_oc', $visit_data);
		}

		// save investigations

		$investigations_notes=$this->input->post('investigations'.$visit_id);

		$rs = $this->nurse_model->get_investigations_notes($visit_id);
		$num_doc_notes = count($rs);
		
		if($num_doc_notes == 0){	
			$visit_data = array('visit_id'=>$visit_id,'investigation'=>$investigations_notes);
			$this->db->insert('visit_investigations', $visit_data);

		}
		else {
			$visit_data = array('investigation'=>$investigations_notes);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_investigations', $visit_data);
		}

		// plan

		$plan_notes=$this->input->post('plan'.$visit_id);

		$rs = $this->nurse_model->get_plan_notes($visit_id);
		$num_doc_notes = count($rs);
		
		if($num_doc_notes == 0){	
			$visit_data = array('visit_id'=>$visit_id,'plan_description'=>$plan_notes);
			$this->db->insert('visit_plan', $visit_data);

		}
		else {
			$visit_data = array('plan_description'=>$plan_notes);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_plan', $visit_data);
		}


		$notes=$this->input->post('oral_examination'.$visit_id);
		
		
		$rs = $this->nurse_model->get_oe_notes($visit_id);
		$num_oe_notes = count($rs);
		if($num_oe_notes == 0){	

			$visit_data = array('visit_id'=>$visit_id,'oe_description'=>$notes);
			$this->db->insert('visit_oe', $visit_data);

		}
		else {

			$visit_data = array('oe_description'=>$notes);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_oe', $visit_data);
		}

		redirect('dental/patient_card/'.$visit_id);

	}

	/*
	*
	*	Add documents 
	*	@param int $personnel_id
	*
	*/
	public function upload_documents($patient_id, $visit_id) 
	{
		$image_error = '';
		$this->session->unset_userdata('upload_error_message');
		$document_name = 'document_scan';
		
		//upload image if it has been selected
		$response = $this->dental_model->upload_any_file($this->document_upload_path, $this->document_upload_location, $document_name, 'document_scan');
		if($response)
		{
			$document_upload_location = $this->document_upload_location.$this->session->userdata($document_name);
		}
		
		//case of upload error
		else
		{
			$image_error = $this->session->userdata('upload_error_message');
			$this->session->unset_userdata('upload_error_message');
		}

		$document = $this->session->userdata($document_name);
		$this->form_validation->set_rules('document_item_name', 'Document Name', 'xss_clean');
		$this->form_validation->set_rules('document_type_id', 'Document Type', 'required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			if($this->dental_model->upload_personnel_documents($patient_id, $document))
			{
				$this->session->set_userdata('success_message', 'Document uploaded successfully');
				$this->session->unset_userdata($document_name);
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not upload document. Please try again');
			}
		}
		else
		{
			$this->session->set_userdata('error_message', 'Could not upload document. Please try again');
		}
		
		redirect('dental/patient_card/'.$visit_id);
	}
    
	/*
	*
	*	Delete an existing personnel
	*	@param int $personnel_id
	*
	*/
	public function delete_document_scan($document_upload_id, $visit_id)
	{
		if($this->dental_model->delete_document_scan($document_upload_id))
		{
			$this->session->set_userdata('success_message', 'Document has been deleted');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Document could not deleted');
		}
		redirect('dental/patient_card/'.$visit_id);
	}
	function doc_schedule($personnel_id,$date)
	{
		$data = array('personnel_id'=>$personnel_id,'date'=>$date);
		$this->load->view('reception/show_schedule',$data);	
	}

	public function save_dentine($visit_id,$patient_id)
	{

		$this->form_validation->set_rules('cavity_status', 'Cavity Status', 'required|trim|xss_clean');
		$this->form_validation->set_rules('tooth_id', 'tooth', 'required|trim|xss_clean');
		$this->form_validation->set_rules('patient_id', 'Patient', 'required|trim|xss_clean');
		
		//if form conatins invalid data
		if ($this->form_validation->run() == TRUE)
		{
			$cavity_status = $this->input->post('cavity_status');
			$tooth_id = $this->input->post('tooth_id');
			$data['cavity_status'] = $cavity_status;
			$data['teeth_id'] = $tooth_id;
			$data['patient_id'] = $patient_id;
			$data['created'] = date('Y-m-d');
			$data['created_by'] = $this->session->userdata('personnel_id');



			$this->db->where('teeth_id = '.$tooth_id.' AND patient_id = '.$patient_id);
			$query = $this->db->get('dentine');

			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$dentine_id = $value->dentine_id;
				}

				$this->db->where('dentine_id',$dentine_id);
				$this->db->update('dentine',$data);


				$response['status'] = 'success';
				$response['message'] = 'successfully updated updated dentine info';

			}
			else
			{
				$this->db->insert('dentine',$data);
				$response['status'] = 'success';
				$response['message'] = 'successfully added dentine ino';
			}

		}
		else
		{
			$response['status'] = 'fail';
			$response['message'] = 'Please fill in all the required fields with (*)';
		}


		echo json_encode($response);

	}

	public function get_page_item($page_id,$patient_id,$visit_id=null)
	{
		$data = array('page_id'=>$page_id,'patient_id'=>$patient_id,'visit_id'=>$visit_id);

		// if($page_id == 1)
		// {

		// 	$response['page_item'] = $this->load->view('history_page',$data,true);	

		// }
		// else if($page_id == 2)
		// {

		// 	$response['page_item'] = $this->load->view('diagnosis',$data,true);	
		// }
		// else if($page_id == 3)
		// {

		// 	$response['page_item'] = $this->load->view('treatment',$data,true);	
		// }
		// else if($page_id == 4)
		// {

		// 	$response['page_item'] = $this->load->view('bills',$data,true);	
		// }

		// else if($page_id == 5)
		// {

		// 	$response['page_item'] = $this->load->view('medical_history',$data,true);	
		// }
		// else if($page_id == 6)
		// {

			$response['page_item'] = $this->load->view('dentine',$data,true);	
		// }
		// else if($page_id == 7)
		// {

		// 	$response['page_item'] = $this->load->view('uploads',$data,true);	
		// }
		
		echo json_encode($response);
	}
	function display_dental_formula($teeth_id,$visit_id,$patient_id)
	{
		$v_data['visit_id'] = $visit_id;
		$v_data['patient_id'] = $patient_id;
		$v_data['teeth_id'] = $teeth_id;
		$this->load->view('dental_formula',$v_data);
	}


	function display_patient_prescription($visit_id=null,$patient_id)
	{
		$v_data['visit_id'] = $visit_id;
		$v_data['patient_id'] = $patient_id;
		$this->load->view('prescription',$v_data);
	}

	public function bill_patient($visit_id,$module =null)
	{
		
		$service_charge_id = $this->input->post('service_charge_id');
		$provider_id = $this->input->post('provider_id');
		$visit_date = $this->input->post('visit_date_date');
		$amount = $this->accounts_model->get_service_charge_detail($service_charge_id);

		$visit_data = array('visit_charge_units'=>1,'visit_id'=>$visit_id,'visit_charge_amount'=>$amount,'service_charge_id'=>$service_charge_id, 'created_by'=>$this->session->userdata("personnel_id"),'provider_id'=>$provider_id,'date'=>$visit_date,'time'=>date('H:i:s'),'personnel_id'=>$procedure_id,'charged'=>1);

		if($this->db->insert('visit_charge', $visit_data))
		{
			$this->session->set_userdata('success_message', 'You have successfully added to bill');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Sorry please try again');
		}

		
		redirect('patient-card/'.$visit_id);
        
	}

	public function print_sick_leave($visit_id)
	{
		

		$data['contacts'] = $this->site_model->get_contacts();

		$patient = $this->reception_model->patient_names2(NULL, $visit_id);
		$data['patient'] = $patient;
		$data['visit_id'] = $visit_id;		
		$this->load->view('print_sick_leave', $data);
	}
	public function print_prescription($visit_id)
	{
		$data['contacts'] = $this->site_model->get_contacts();

		$patient = $this->reception_model->patient_names2(NULL, $visit_id);
		$data['patient'] = $patient;
		$data['visit_id'] = $visit_id;		
		$this->load->view('print_prescription', $data);
	}
	public function view_lab_work($visit_id)
	{
		$data = array('visit_id'=>$visit_id);
		$this->load->view('view_lab_work',$data);
	}
	public function save_lab_work($visit_id)
	{
		$this->form_validation->set_rules('notes', 'Lab Work', 'required|trim|xss_clean');
		
		//if form conatins invalid data
		if ($this->form_validation->run() == TRUE)
		{
			$lab_work['lab_work_done'] = $this->input->post('notes');
			$lab_work['visit_id'] = $visit_id;
			$lab_work['created'] = date('Y-m-d');
			$lab_work['created_by'] = $this->session->userdata('personnel_id');
			$lab_work['lab_work_deleted'] = 0;

			$this->db->insert('visit_lab_work', $lab_work);

			$response['status'] = 'success';
			$response['message'] ='You have successfully created the lab work';
		}
		else
		{
			$response['status'] = 'fail';
			$response['message'] ='Sorry, ensure that you added a lab work';
		}	

		echo json_encode($response);
	}

	function delete_lab_work($visit_lab_work_id)
	{
		$this->db->where(array("visit_lab_work_id"=>$visit_lab_work_id));
		$this->db->delete('visit_lab_work');
	}
	public function save_prescription($patient_id,$visit_id)
	{
		// prescription
		$prescription_notes=$this->input->post('prescription');

		$rs = $this->nurse_model->get_prescription_notes_visit($visit_id);
		$num_doc_notes = count($rs);
		
		if($num_doc_notes == 0){	
			$visit_data = array('visit_id'=>$visit_id,'visit_prescription'=>$prescription_notes,'patient_id'=>$patient_id);
			$this->db->insert('visit_prescription', $visit_data);

		}
		else {
			$visit_data = array('visit_prescription'=>$prescription_notes,'patient_id'=>$patient_id);
			$this->db->where('visit_id = '.$visit_id);
			$this->db->update('visit_prescription', $visit_data);
		}

		$response['status'] = 'success';
		$response['message'] ='You have added the prescription';
		echo json_encode($response);
	}
	public function get_patient_balance($patient_id){

	
		// $this->db->where('v_patient_balances.patient_id = '.$patient_id);
		// $query = $this->db->get('v_patient_balances');
		// $balance = 0;
		// if($query->num_rows() > 0)
		// {
		// 	foreach ($query->result() as $key => $value) {
		// 		# code...
		// 		$balance = $value->balance;
		// 	}
		// }
		$cash_balance = $this->accounts_model->get_cash_balance($patient_id);
		$insurance_balance = $this->accounts_model->get_insurance_balance($patient_id);
		$balance = $cash_balance+$insurance_balance;
		echo "<h3>Balance: KSH. ".number_format($balance,2)."</h3>";
	}

	public function get_patient_waivers($patient_id){

		$data = array('patient_id'=>$patient_id);
		$this->load->view('view_patient_waivers',$data);
		
	}
	public function add_patient_waiver($patient_id,$visit_id)
	{
		$this->form_validation->set_rules('waiver_amount', 'Waiver Amount', 'required|trim|xss_clean');
		$this->form_validation->set_rules('reason', 'Reason', 'required|trim|xss_clean');
		
		//if form conatins invalid data
		if ($this->form_validation->run() == TRUE)
		{
			$reason = $this->input->post('reason');
			$waiver_amount = $this->input->post('waiver_amount');
			$data['reason'] = $reason;
			$data['amount_paid'] = $waiver_amount;
			$data['visit_id'] = $visit_id;
			$data['payment_type'] = 2;
			$data['payment_method_id'] = 7;
			$data['payment_created'] = date('Y-m-d');
			$data['payment_created_by'] = $this->session->userdata('personnel_id');
			
			$this->db->insert('payments',$data);
			$response['status'] = 'success';
			$response['message'] = 'successfully added dentine ino';

		}
		else
		{
			$response['status'] = 'fail';
			$response['message'] = 'Please fill in all the required fields with (*)';
		}


		echo json_encode($response);
	}
	public function remove_patient_waiver($payment_id)
	{

		$data['cancel'] = 1;
		$data['cancelled_date'] = date('Y-m-d');
		$data['cancelled_by'] = $this->session->userdata('personnel_id');
		$this->db->where('payment_id',$payment_id);
		$this->db->update('payments',$data);
		$response['status'] = 'success';
		$response['message'] = 'successfully added dentine ino';
		echo json_encode($response);
	}
}