<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(E_ALL);
class Accounts extends MX_Controller
{
	function __construct()
	{
		parent:: __construct();

		$this->load->model('site/site_model');
		$this->load->model('administration/reports_model');
		$this->load->model('admin/users_model');
		$this->load->model('admin/sections_model');
		$this->load->model('admin/admin_model');
		$this->load->model('payroll/payroll_model');
		$this->load->model('hr/personnel_model');
		$this->load->model('admin/branches_model');
		$this->load->model('accounting/petty_cash_model');
		$this->load->model('accounts/accounts_model');
		$this->load->model('nurse/nurse_model');
		$this->load->model('reception/reception_model');
		$this->load->model('dental/dental_model');
		$this->load->model('reception/database');

		$this->load->model('medical_admin/medical_admin_model');
		$this->load->model('pharmacy/pharmacy_model');
		$this->load->model('accounting/hospital_accounts_model');
		$this->load->model('administration/sync_model');
		$this->load->model('messaging/messaging_model');
		$this->load->model('admin/email_model');

		$this->load->model('auth/auth_model');
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

		$data['content'] = $this->load->view('dashboard', $v_data, true);

		$this->load->view('templates/general_page', $data);
	}
	public function index()
	{
		$this->session->unset_userdata('all_transactions_search');

		$data['content'] = $this->load->view('dashboard', '', TRUE);

		$data['title'] = 'Dashboard';
		$data['sidebar'] = 'accounts_sidebar';
		$this->load->view('admin/templates/general_page', $data);
	}

	public function accounts_queue()
	{
		$branch_code = $this->session->userdata('search_branch_code');

		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}

		$this->db->where('branch_code', $branch_code);
		$query = $this->db->get('branch');

		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$branch_name = $row->branch_name;
		}

		else
		{
			$branch_name = '';
		}
		$v_data['branch_name'] = $branch_name;
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$where = 'visit.inpatient = 0 AND visit.visit_delete = 0 AND visit_department.visit_id = visit.visit_id AND (visit_department.department_id = 6 OR visit_department.accounts = 0) AND visit_department.visit_department_status = 1 AND visit.patient_id = patients.patient_id AND (visit.close_card = 0 OR visit.close_card = 7) AND visit_type.visit_type_id = visit.visit_type AND visit.branch_code = \''.$branch_code.'\'AND visit.visit_date = \''.date('Y-m-d').'\'';

		$table = 'visit_department, visit, patients, visit_type';

		$visit_search = $this->session->userdata('visit_accounts_search');

		if(!empty($visit_search))
		{
			$where .= $visit_search;
		}
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounts/accounts_queue';
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = 3;
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

		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$v_data['type_links'] =1;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->reception_model->get_all_ongoing_visits($table, $where, $config["per_page"], $page);

		$v_data['query'] = $query;
		$v_data['page'] = $page;

		$data['title'] = 'Accounts Queue';
		$v_data['title'] = 'Accounts Queue';
		$v_data['module'] = 0;
		$v_data['close_page'] = 1;

		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();

		$data['content'] = $this->load->view('accounts_queue', $v_data, true);
		$data['sidebar'] = 'accounts_sidebar';

		$this->load->view('admin/templates/general_page', $data);
		// end of it

	}
	public function search_visits($pager=NULL)
	{
		$visit_type_id = $this->input->post('visit_type_id');
		$surnames = $this->input->post('surname');
		$personnel_id = $this->input->post('personnel_id');
		$visit_date = $this->input->post('visit_date');
		$othernames = $this->input->post('othernames');
		$branch_code = $this->input->post('branch_code');
		$this->session->set_userdata('search_branch_code', $branch_code);

		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
		}

		if(!empty($personnel_id))
		{
			$personnel_id = ' AND visit.personnel_id = '.$personnel_id.' ';
		}

		if(!empty($visit_date))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date.'\' ';
		}

		//search surname
		$surnames = explode(" ",$surnames);
		$total = count($surnames);

		$count = 1;
		$surname = ' AND (';
		for($r = 0; $r < $total; $r++)
		{
			if($count == $total)
			{
				$surname .= ' patients.patient_surname LIKE \'%'.addslashes($surnames[$r]).'%\'';
			}

			else
			{
				$surname .= ' patients.patient_surname LIKE \'%'.addslashes($surnames[$r]).'%\' AND ';
			}
			$count++;
		}
		$surname .= ') ';

		//search other_names
		$other_names = explode(" ",$othernames);
		$total = count($other_names);

		$count = 1;
		$other_name = ' AND (';
		for($r = 0; $r < $total; $r++)
		{
			if($count == $total)
			{
				$other_name .= ' patients.patient_othernames LIKE \'%'.addslashes($other_names[$r]).'%\'';
			}

			else
			{
				$other_name .= ' patients.patient_othernames LIKE \'%'.addslashes($other_names[$r]).'%\' AND ';
			}
			$count++;
		}
		$other_name .= ') ';

		$search = $visit_type_id.$surname.$other_name.$visit_date.$personnel_id;
		$this->session->unset_userdata('visit_accounts_search');
		$this->session->set_userdata('visit_accounts_search', $search);

		redirect('cash-office/patient-visits');


	}
	public function close_queue_search($pager)
	{
		$this->session->unset_userdata('visit_accounts_search');
		redirect('cash-office/patient-visits');
	}
	public function accounts_unclosed_queue()
	{
		//$where = 'visit.visit_delete = 0 AND visit_department.visit_id = visit.visit_id AND visit_department.department_id = 6 AND visit_department.visit_department_status = 1 AND visit.patient_id = patients.patient_id AND (visit.close_card = 0 OR visit.close_card = 7)';
		$branch_code = $this->session->userdata('search_branch_code');

		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}

		$this->db->where('branch_code', $branch_code);
		$query = $this->db->get('branch');

		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$branch_name = $row->branch_name;
		}

		else
		{
			$branch_name = '';
		}
		$v_data['branch_name'] = $branch_name;
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$where = 'visit.inpatient = 0 AND visit.visit_delete = 0 AND visit_department.visit_id = visit.visit_id AND visit_department.visit_department_status = 1 AND visit.patient_id = patients.patient_id AND (visit.close_card = 0 OR visit.close_card = 7) AND visit_type.visit_type_id = visit.visit_type AND visit.branch_code = \''.$branch_code.'\'';

		$table = 'visit_department, visit, patients, visit_type';

		$visit_search = $this->session->userdata('visit_accounts_search');
		$segment = 3;

		if(!empty($visit_search))
		{
			$where .= $visit_search;
			$segment = 4;
		}
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounts/accounts_unclosed_queue';
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 40;
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
		$v_data['type_links'] =2;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->reception_model->get_all_ongoing_visits2($table, $where, $config["per_page"], $page);

		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['close_page'] = 2;

		$data['title'] = 'Accounts Unclosed Visits';
		$v_data['title'] = 'Accounts Unclosed Visits';
		$v_data['module'] = 0;

		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();

		$data['content'] = $this->load->view('accounts_queue', $v_data, true);
		$data['sidebar'] = 'accounts_sidebar';

		$this->load->view('admin/templates/general_page', $data);
		// end of it

	}
	public function accounts_closed_visits()
	{
		$branch_code = $this->session->userdata('search_branch_code');

		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}

		$this->db->where('branch_code', $branch_code);
		$query = $this->db->get('branch');

		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$branch_name = $row->branch_name;
		}

		else
		{
			$branch_name = '';
		}
		$v_data['branch_name'] = $branch_name;
		$where = 'visit.visit_delete = 0  AND visit.patient_id = patients.patient_id AND visit.close_card = 1 ';
		$where = 'visit.visit_delete = 0 AND visit_department.visit_id = visit.visit_id AND visit_department.visit_department_status = 1 AND visit.patient_id = patients.patient_id AND visit.close_card = 1 AND visit_type.visit_type_id = visit.visit_type AND visit.branch_code = \''.$branch_code.'\'';

		$table = 'visit_department, visit, patients, visit_type';

		$visit_search = $this->session->userdata('visit_accounts_search');
		$segment = 3;

		if(!empty($visit_search))
		{
			$where .= $visit_search;
			$segment = 3;
		}
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounts/accounts_closed_visits';
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
        $v_data['type_links'] =3;
		$query = $this->reception_model->get_all_ongoing_visits2($table, $where, $config["per_page"], $page);

		$v_data['query'] = $query;
		$v_data['page'] = $page;

		$data['title'] = 'Accounts closed Visits';
		$v_data['title'] = 'Accounts closed Visits';
		$v_data['module'] = 7;
		$v_data['close_page'] = 3;
		$v_data['branches'] = $this->reports_model->get_all_active_branches();

		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();

		$data['content'] = $this->load->view('accounts_queue', $v_data, true);
		$data['sidebar'] = 'accounts_sidebar';

		$this->load->view('admin/templates/general_page', $data);
		// end of it

	}
	public function invoice($visit_id)
	{
		?>
        	<script type="text/javascript">
        		var config_url = $('#config_url').val();
				window.open(config_url+"/accounts/print_invoice/<?php echo $visit_id;?>","Popup","height=900,width=1200,,scrollbars=yes,"+"directories=yes,location=yes,menubar=yes,"+"resizable=no status=no,history=no top = 50 left = 100");
				window.location.href="<?php echo base_url("index.php/accounts/accounts_queue")?>";
			</script>
        <?php

		$this->accounts_queue();
	}
	public function payments($patient_id, $close_page = NULL)
	{
		$v_data = array('patient_id'=>$patient_id);

		$v_data['cancel_actions'] = $this->accounts_model->get_cancel_actions();
		$v_data['visit_types_rs'] = $this->reception_model->get_visit_types();
		$patient = $this->reception_model->get_patient_data($patient_id);
		$patient = $patient->row();
		$patient_othernames = $patient->patient_othernames;
		$patient_surname = $patient->patient_surname;

		$v_data['doctor'] = $this->reception_model->get_providers();


		$v_data['title'] = $patient_othernames.' '.$patient_surname;


		// $rs = $this->nurse_model->check_visit_type($visit_id);
		// if(count($rs)>0){
		//   foreach ($rs as $rs1) {
		//     # code...
		//       $visit_t = $rs1->visit_type;
		//   }
		// }
		// var_dump($visit_t); die();
		$order = 'service_charge.service_charge_name';
		$where = 'service_charge.service_id = service.service_id AND service.service_name <> "Pharmarcy" AND service.service_delete = 0 AND service_charge.service_charge_status = 1 AND  service_charge.service_charge_delete = 0 AND service_charge.visit_type_id = visit_type.visit_type_id AND service_charge.visit_type_id = 1';

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



		$order = 'service.service_name';
		$where = 'service.service_name <> "Pharmacy" AND service_status = 1';

		$table = 'service';
		$service_query = $this->nurse_model->get_other_procedures($table, $where, $order);

		$rs9 = $service_query->result();
		$services_items = '';
		foreach ($rs9 as $rs11) :


			$service_id = $rs11->service_id;
			$service_name = $rs11->service_name;

			$services_items .="<option value='".$service_id."'>".$service_name."</option>";

		endforeach;

		$v_data['services_items'] = $services_items;



		$v_data['close_page'] = 1;
		$data['content'] = $this->load->view('payments', $v_data, true);

		$data['title'] = 'Payments';
		$data['sidebar'] = 'accounts_sidebar';
		$this->load->view('admin/templates/general_page', $data);
	}

	public function get_visits_div($patient_id,$page=NULL)
	{
		$v_data['patient_id'] = $patient_id;
		$v_data = array('patient_id'=>$patient_id);


		if($page == NULL)
		{
			$page = 0;
		}
		// $page = 1;

		// var_dump($counted);
		$table= 'visit';
		$where='patient_id ='.$patient_id;
		$config["per_page"] = $v_data['per_page'] = $per_page = 15;
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
		$query = $this->accounts_model->get_all_visits_parent($table, $where, $config["per_page"], $page);

		$v_data['visit_list'] = $query;
		$primary_key = $patient['patient_id'];

		$this->load->view('visit_list', $v_data);
	}

	public function get_patient_details_header($visit_id)
	{

		$v_data['cancel_actions'] = $this->accounts_model->get_cancel_actions();
		$v_data['going_to'] = $this->accounts_model->get_going_to($visit_id);
		$patient = $this->reception_model->patient_names2(NULL, $visit_id);
		$v_data['patient_type'] = $patient['patient_type'];
		$patient_othernames = $patient['patient_othernames'];
		$patient_surname= $patient['patient_surname'];
		$v_data['patient_type_id'] = $patient['visit_type_id'];
		$account_balance= $patient['account_balance'];
		$visit_type_name= $patient['visit_type_name'];
		$patient_type = $patient['patient_type'];
		$v_data['patient_id'] = $patient['patient_id'];
		$close_card = $patient['close_card'];
		$v_data['inpatient'] = $inpatient = $patient['inpatient'];
		$payments_value = $this->accounts_model->total_payments($visit_id);
		$invoice_total = $this->accounts_model->total_invoice($visit_id);
		$balance = $this->accounts_model->balance($payments_value,$invoice_total);

		// echo $visit_id;


		if($inpatient == 1)
		{
			// $visit_discharge = '<a target="_blank" onclick="close_visit('.$visit_id.')" class="btn btn-sm btn-danger pull-right" style="margin-top:-25px;" ><i class="fa fa-folder"></i> Discharge Patient</a>';

			$visit_discharge = '<a class="btn btn-sm btn-danger pull-right" style="margin-top:-25px;" data-toggle="modal" data-target="#end_visit_date" ><i class="fa fa-times"></i> Discharge</a>   ';
			$banner = 'success';
		}
		else if($inpatient == 0)
		{
			$visit_discharge = '<a class="btn btn-sm btn-danger pull-right"  onclick="close_visit('.$visit_id.')"  style="margin-top:-25px;" ><i class="fa fa-folder"></i> End Visit</a>';
			$banner = 'success';
		}


		if($patient_type == 0)
		{
			$checked = '<a class="btn btn-sm btn-info pull-left" style="margin-top:-25px;margin-right:2px;" data-toggle="modal" data-target="#change_patient_type" ><i class="fa fa-pencil"></i> Edit Type</a>
					<a href="'.site_url().'accounts/print_invoice_new/'.$visit_id.'/1" target="_blank" class="btn btn-sm btn-success pull-left" style="margin-top:-25px;margin-right:2px;" ><i class="fa fa-print"></i> All Invoice</a>


					';
		}
		else
		{
			$checked = '';
		}

		$title = '<h2 class="panel-title panel-'.$banner.'"><strong>Visit: </strong>'.$visit_type_name.'.<strong> Total: </strong> Kes '.number_format($account_balance, 2).' <strong> Current: </strong> Kes '.$balance.'</h2>
				<div class="pull-right">
				    '.$checked.'
					'.$visit_discharge.'
					<a href="'.site_url().'accounts/print_invoice_new/'.$visit_id.'" target="_blank" class="btn btn-sm btn-warning pull-right" style="margin-top:-25px; margin-right:2px;" ><i class="fa fa-print"></i> Current Invoice</a>
				</div>';

		echo $title;
	}
	public function charge_sheet($visit_id, $close_page = NULL)
	{
		$v_data = array('visit_id'=>$visit_id);

		$v_data['cancel_actions'] = $this->accounts_model->get_cancel_actions();
		$v_data['going_to'] = $this->accounts_model->get_going_to($visit_id);
		$patient = $this->reception_model->patient_names2(NULL, $visit_id);
		$v_data['patient_type'] = $patient['patient_type'];
		$v_data['patient_othernames'] = $patient['patient_othernames'];
		$v_data['patient_surname'] = $patient['patient_surname'];
		$v_data['patient_type_id'] = $patient['visit_type_id'];
		$v_data['account_balance'] = $patient['account_balance'];
		$v_data['visit_type_name'] = $patient['visit_type_name'];
		$v_data['patient_id'] = $patient['patient_id'];
		$v_data['inatient'] = $patient['inatient'];

		$v_data['doctor'] = $this->reception_model->get_doctor();


		$primary_key = $patient['patient_id'];


		$rs = $this->nurse_model->check_visit_type($visit_id);
		if(count($rs)>0){
		  foreach ($rs as $rs1) {
		    # code...
		      $visit_t = $rs1->visit_type;
		  }
		}

		$order = 'service_charge.service_charge_name';
		$where = 'service_charge.service_id = service.service_id AND (service.service_name = "Others" OR service.service_name = "Procedures" OR service.service_name = "Consultation") AND service.service_delete = 0 AND service_charge.visit_type_id = visit_type.visit_type_id AND service_charge.visit_type_id ='.$visit_t;

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



		$order = 'service.service_name';
		$where = 'service.service_name <> "Pharmacy" AND service_status = 1';

		$table = 'service';
		$service_query = $this->nurse_model->get_other_procedures($table, $where, $order);

		$rs9 = $service_query->result();
		$services_items = '';
		foreach ($rs9 as $rs11) :


			$service_id = $rs11->service_id;
			$service_name = $rs11->service_name;

			$services_items .="<option value='".$service_id."'>".$service_name."</option>";

		endforeach;

		$v_data['services_items'] = $services_items;

		$table= 'visit_charge, service_charge, service';
		$where='visit_charge.visit_charge_delete = 0 AND visit_charge.visit_id = '.$visit_id.' AND visit_charge.service_charge_id = service_charge.service_charge_id AND service.service_id = service_charge.service_id AND (service.service_name = "Others" OR service.service_name = "Procedures" OR service.service_name = "Consultation" OR service.service_name = "Laboratory") AND visit_charge.charged = 1';

		$config["per_page"] = $v_data['per_page'] = $per_page = 10;


		$v_data['visit_id'] = $visit_id;
		$v_data['total_rows'] = $this->reception_model->count_items($table, $where);
		$query = $this->accounts_model->get_all_visits_invoice_items($table, $where, $config["per_page"], $page);
		$v_data['charge_sheet_query'] = $query;





		$v_data['close_page'] = $close_page;
		$data['content'] = $this->load->view('charge_sheet', $v_data, true);

		$data['title'] = 'Payments';
		$data['sidebar'] = 'accounts_sidebar';
		$this->load->view('admin/templates/general_page', $data);
	}

	public function make_payments($visit_id, $close_page = NULL)
	{

		$this->form_validation->set_rules('type_payment', 'Type of payment', 'trim|required|xss_clean');
		$payment_method = $this->input->post('payment_method');
		// normal or credit note or debit note
		$type_payment = $this->input->post('type_payment');


		// if($payment_method == 0)
		// {
		// 	$response['result'] ='fail';
		// 	$response['message'] ='Please select the type of payment';
		// }
		// else
		// {	// Normal
			if($type_payment == 1)
			{
				$this->form_validation->set_rules('amount_paid', 'Amount', 'trim|required|xss_clean');
				$this->form_validation->set_rules('payment_method', 'Payment Method', 'trim|required|xss_clean');
				$this->form_validation->set_rules('payment_service_id', 'Payment Service', 'trim|required|xss_clean');
				$this->form_validation->set_rules('service_id', 'Service', 'xss_clean');
				if(!empty($payment_method))
				{
					if($payment_method == 1)
					{
						// check for cheque number if inserted
						$this->form_validation->set_rules('cheque_number', 'Cheque Number', 'trim|required|xss_clean');
					}
					else if($payment_method == 6)
					{
						// check for insuarance number if inserted
						$this->form_validation->set_rules('insuarance_number', 'Credit Card Detail', 'trim|required|xss_clean');
					}
					else if($payment_method == 5)
					{
						//  check for mpesa code if inserted
						$this->form_validation->set_rules('mpesa_code', 'Amount', 'trim|required|xss_clean');
					}
					else if($payment_method == 7)
					{
						//  check for mpesa code if inserted
						$this->form_validation->set_rules('deposit_detail', 'Bank Deposit', 'trim|required|xss_clean');
					}
					else if($payment_method == 8)
					{
						//  check for mpesa code if inserted
						$this->form_validation->set_rules('debit_card_detail', 'Debit Card', 'trim|required|xss_clean');
					}
				}
			}
			else if($type_payment == 2)
			{
				$this->form_validation->set_rules('waiver_amount', 'Amount', 'trim|required|xss_clean');
				// debit note
				// $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
				// $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
				// $this->form_validation->set_rules('payment_service_id', 'Service', 'required|xss_clean');
			}
			else if($type_payment == 3)
			{
				// $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
				// $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
				// $this->form_validation->set_rules('payment_service_id', 'Service', 'required|xss_clean');
			}
			//if form conatins invalid data
			if ($this->form_validation->run())
			{
				if($type_payment == 2 OR $type_payment == 3)
				{
					$checked = $this->session->userdata('authorize_invoice_changes');


				}
				else
				{
					$checked = TRUE;
				}

				if($checked)
				{


					if($this->accounts_model->receipt_payment($visit_id))
					{
						$response['result'] ='success';
						$response['message'] ='You have successfully receipted the payment';
						$this->session->set_userdata('success_message', 'You have successfully receipted the payment');
					}
					else
					{
						$response['result'] ='fail';
						$response['message'] ='Seems like you dont have the priviledges to effect this event. Please contact your administrator.';
						$this->session->set_userdata('error_message', 'Seems like you dont have the priviledges to effect this event. Please contact your administrator.');
					}
				}
				else
				{
					$response['message'] ='Seems like you dont have the priviledges to effect this event. Please contact your administrator.';
					$this->session->set_userdata('error_message', 'Seems like you dont have the priviledges to effect this event. Please contact your administrator.');

				}


			}
			else
			{
				$response['result'] ='fail';
				$response['message'] =validation_errors();
			}
		// }
		echo json_encode($response);
	}

	public function receipt_payment($visit_id, $close_page = NULL)
	{
		$v_data = array('visit_id'=>$visit_id);

		$v_data['cancel_actions'] = $this->accounts_model->get_cancel_actions();
		$v_data['going_to'] = $this->accounts_model->get_going_to($visit_id);
		$v_data['visit_types_rs'] = $this->reception_model->get_visit_types();
		$patient = $this->reception_model->patient_names2(NULL, $visit_id);
		$v_data['patient_type'] = $patient['patient_type'];
		$v_data['patient_othernames'] = $patient['patient_othernames'];
		$v_data['patient_surname'] = $patient['patient_surname'];
		$v_data['patient_type_id'] = $patient['visit_type_id'];
		$v_data['account_balance'] = $patient['account_balance'];
		$v_data['visit_type_name'] = $patient['visit_type_name'];
		$v_data['patient_id'] = $patient['patient_id'];
		$v_data['inatient'] = $patient['inatient'];

		$v_data['doctor'] = $this->reception_model->get_doctor();


		$primary_key = $patient['patient_id'];


		$rs = $this->nurse_model->check_visit_type($visit_id);
		if(count($rs)>0){
		  foreach ($rs as $rs1) {
		    # code...
		      $visit_t = $rs1->visit_type;
		  }
		}

		$order = 'service_charge.service_charge_name';
		$where = 'service_charge.service_id = service.service_id AND service.service_delete = 0 AND service_charge.visit_type_id = visit_type.visit_type_id AND service_charge.visit_type_id = 1';

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



		$order = 'service.service_name';
		$where = 'service.service_name <> "Pharmacy" AND service_status = 1';

		$table = 'service';
		$service_query = $this->nurse_model->get_other_procedures($table, $where, $order);

		$rs9 = $service_query->result();
		$services_items = '';
		foreach ($rs9 as $rs11) :


			$service_id = $rs11->service_id;
			$service_name = $rs11->service_name;
			$services_items .="<option value='".$service_id."'>".$service_name."</option>";

		endforeach;

		$v_data['services_items'] = $services_items;

		$table= 'visit_charge, service_charge, service';
		$where='visit_charge.visit_charge_delete = 0 AND visit_charge.visit_id = '.$visit_id.' AND visit_charge.service_charge_id = service_charge.service_charge_id AND service.service_id = service_charge.service_id';

		$config["per_page"] = $v_data['per_page'] = $per_page = 10;


		$v_data['visit_id'] = $visit_id;
		$v_data['total_rows'] = $this->reception_model->count_items($table, $where);
		$query = $this->accounts_model->get_all_visits_invoice_items($table, $where, $config["per_page"], $page);
		$v_data['charge_sheet_query'] = $query;

		$v_data['close_page'] = $close_page;
		$data['content'] = $this->load->view('receipt_payment', $v_data, true);

		$data['title'] = 'Payments';
		$data['sidebar'] = 'accounts_sidebar';
		$this->load->view('admin/templates/general_page', $data);
	}

	public function add_billing($visit_id, $close_page = NULL)
	{
		$this->form_validation->set_rules('billing_method_id', 'Billing Method', 'required|numeric');

		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			if($this->accounts_model->add_billing($visit_id))
			{
				$this->session->set_userdata('success_message', 'Billing method successfully added');
			}
			else
			{
				$this->session->set_userdata("error_message","Unable to add billing method. Please try again");
			}
		}
		else
		{
			$this->session->set_userdata("error_message","Fill in the fields");
		}

		redirect('accounts/payments/'.$visit_id.'/'.$close_page);
	}
	public function add_service_item()
	{

		$this->form_validation->set_rules('parent_service_id', 'Service Name', 'required|numeric');
		$this->form_validation->set_rules('service_charge_item', 'Charge Name', 'required');
		$this->form_validation->set_rules('service_amount', 'Charge Name', 'required|numeric');

		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			if($this->accounts_model->add_service_item())
			{
				$this->session->set_userdata('success_message', 'Service charge successfully added');
			}
			else
			{
				$this->session->set_userdata("error_message","Unable to add service charge. Please try again");
			}
		}
		else
		{
			$this->session->set_userdata("error_message","Fill in the fields");
		}

		// redirect('accounts/payments/'.$visit_id);

		$redirect_url = $this->input->post('redirect_url');
		redirect($redirect_url);

	}
	public function print_invoice($visit_id)
	{
		$this->accounts_model->receipt($visit_id);
	}

	public function print_invoice_old($visit_id)
	{
		$this->accounts_model->receipt($visit_id, TRUE);
	}

	public function print_invoice_new($visit_id,$page_item = NULL)
	{
		$data = array('visit_id'=>$visit_id);
		$data['contacts'] = $this->site_model->get_contacts();
		$data['page_item'] = $page_item;
		$patient = $this->reception_model->patient_names2(NULL, $visit_id);
		$data['patient'] = $patient;
		$this->load->view('invoice', $data);

	}
	public function print_self_invoice($visit_id,$page_item = NULL)
	{
		$data = array('visit_id'=>$visit_id);
		$data['contacts'] = $this->site_model->get_contacts();
		$data['page_item'] = $page_item;
		$patient = $this->reception_model->patient_names2(NULL, $visit_id);
		$data['patient'] = $patient;
		$this->load->view('self_invoice', $data);

	}
	public function print_receipt_new($visit_id)
	{
		$data = array('visit_id'=>$visit_id);
		$data['contacts'] = $this->site_model->get_contacts();

		$patient = $this->reception_model->patient_names2(NULL, $visit_id);
		$data['patient'] = $patient;
		$this->load->view('receipt', $data);
	}
	public function print_single_receipt($payment_id)
	{
		$data = array('payment_id' => $payment_id);
		$data['contacts'] = $this->site_model->get_contacts();
		$data['receipt_payment_id'] = $payment_id;

		$patient = $this->reception_model->patient_names3($payment_id);
		$data['patient'] = $patient;
		$this->load->view('single_receipt', $data);
	}
	public function bulk_close_visits($page)
	{
		$total_visits = sizeof($_POST['visit']);

		//check if any checkboxes have been ticked
		if($total_visits > 0)
		{
			for($r = 0; $r < $total_visits; $r++)
			{
				$visit = $_POST['visit'];
				$visit_id = $visit[$r];
				//check if card is held
				if($this->reception_model->is_card_held($visit_id))
				{
				}

				else
				{
					if($this->accounts_model->end_visit($visit_id))
					{
						$this->session->set_userdata('success_message', 'Visits ended successfully');
					}

					else
					{
						$this->session->set_userdata('error_message', 'Unable to end visits');
					}
				}
			}
		}

		else
		{
			$this->session->set_userdata('error_message', 'Please select visits to terminate first');
		}

		redirect('accounts/accounts_unclosed_queue/'.$page);
	}

	public function close_visit($visit_id)
	{

		$payments_value = $this->accounts_model->total_payments($visit_id);

		$invoice_total = $this->accounts_model->total_invoice($visit_id);

		$balance = $this->accounts_model->balance($payments_value,$invoice_total);

		if($balance > 0)
		{


			if($this->accounts_model->end_visit_with_status($visit_id,2))
			{


			}

			else
			{
				$this->session->set_userdata('error_message', 'Unable to end visits');


			}
			$response['message'] ="visit has been ended successfully";
		}
		else
		{

			if($this->accounts_model->end_visit_with_status($visit_id,1))
			{
				$response['message'] ="You have successfully ended the visit";

			}

			else
			{
				$response['message'] ="Sorry could not end visit at this time. Please try again";


			}
		}
		echo json_encode($response);
	}

	public function send_message($visit_id)
	{

		$patient = $this->reception_model->patient_names2(NULL, $visit_id);
		$v_data['patient_type'] = $patient['patient_type'];
		$v_data['patient_othernames'] = $patient['patient_othernames'];
		$v_data['patient_surname'] = $patient['patient_surname'];
		$v_data['patient_type_id'] = $patient['visit_type_id'];
		$v_data['account_balance'] = $patient['account_balance'];
		$v_data['visit_type_name'] = $patient['visit_type_name'];
		$v_data['patient_id'] = $patient['patient_id'];
		$v_data['inatient'] = $patient['inatient'];
		$v_data['patient_phone1'] = $patient_phone = $patient['patient_phone_number'];

		$message  = 'Thank you '.$v_data['patient_surname'].' '.$v_data['patient_othernames'].'  for visiting us. Keep healthy, keep smiling and have a pleasant day.';



		$message_data = array(
						"phone_number" => $patient_phone,
						"entryid" => $v_data['patient_id'],
						"message" => $message,
						"message_batch_id"=>0,
						'message_status' => 0
					);
		$this->db->insert('messages', $message_data);
		$message_id = $this->db->insert_id();
		// $patient_phone = 721481703;
		$patient_phone = str_replace(' ', '', $patient_phone);
		// var_dump($patient); die();
		if(!empty($patient_phone))
		{
			$response = $this->messaging_model->sms($patient_phone,$message);

			if($response == "Success" OR $response == "success")
			{

				$service_charge_update = array('message_status' => 1,'delivery_message'=>'Success','sms_cost'=>3,'message_type'=>'Thank You Note');
				$this->db->where('message_id',$message_id);
				$this->db->update('messages', $service_charge_update);

			}
			else
			{
				$service_charge_update = array('message_status' => 0,'delivery_message'=>'Success','sms_cost'=>0,'message_type'=>'Thank You Note');
				$this->db->where('message_id',$message_id);
				$this->db->update('messages', $service_charge_update);


			}
		}
		else
		{
			$response['status'] = 1;
			$response['message'] = "Sorry could not send message";
		}

		 echo json_encode($response);
	}

	public function discharge_patient($visit_id)
	{
		$visit_date = $this->input->post('visit_date_charged');

		$payments_value = $this->accounts_model->total_payments($visit_id);

		$invoice_total = $this->accounts_model->total_invoice($visit_id);

		$balance = $this->accounts_model->balance($payments_value,$invoice_total);

		if($balance > 0)
		{


			if($this->accounts_model->discharge_visit_with_status($visit_id,2,$visit_date))
			{


			}

			else
			{
				$this->session->set_userdata('error_message', 'Unable to end visits');


			}
			$response['message'] ="visit has been ended successfully";
		}
		else
		{

			if($this->accounts_model->discharge_visit_with_status($visit_id,1,$visit_date))
			{
				$response['message'] ="You have successfully ended the visit";

			}

			else
			{
				$response['message'] ="Sorry could not end visit at this time. Please try again";


			}
		}
		 echo json_encode($response);
	}

	public function get_change($visit_id)
	{

		$this->form_validation->set_rules('amount_paid', 'Amount', 'trim|required|xss_clean|numeric');

		if($this->form_validation->run())
		{
			$amount_paid = $this->input->post('amount_paid');
			$payments_value = $this->accounts_model->total_payments($visit_id);

			$invoice_total = $this->accounts_model->total_invoice($visit_id);

			$balance = $this->accounts_model->balance($payments_value,$invoice_total);
			if($balance < $amount_paid)
			{
				$change = $amount_paid - $balance;
			}
			else
			{
				$change = 0;
			}
		}
		else
		{
			$change = 0;
		}

		$response['change'] = $change;

		echo json_encode($response);
	}

	public function send_to_department($visit_id, $department_id)
	{
		$data['accounts'] = 1;
		$this->db->where('visit_department.visit_department_status = 1 AND visit_department.visit_id = '.$visit_id);
		if($this->db->update('visit_department', $data))
		{
			$this->db->where('visit_id', $visit_id);
			$query = $this->db->get('visit');
			$row = $query->row();
			$visit_type = $row->visit_type;

			if($this->reception_model->set_visit_department($visit_id, $department_id, $visit_type))
			{
				$this->session->set_userdata('success_message', 'Patient has been sent');
				redirect('accounts/accounts-queue');
			}
			else
			{
				$this->session->set_userdata('error_message', 'Unable to send patient');
				redirect('accounts/payments/'.$visit_id.'/1');
			}
		}

		else
		{
			$this->session->set_userdata('error_message', 'Patient could not be sent');
			redirect('accounts/payments/'.$visit_id.'/1');
		}
	}

	public function cancel_payment($payment_id, $visit_id)
	{
		$this->form_validation->set_rules('cancel_description', 'Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cancel_action_id', 'Action', 'trim|required|xss_clean');

		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			// end of checker function
			if($this->accounts_model->cancel_payment($payment_id))
			{
				$this->session->set_userdata("success_message", "Payment action saved successfully");
			}
			else
			{
				$this->session->set_userdata("error_message", "Oops something went wrong. Please try again");
			}
		}
		else
		{
			$this->session->set_userdata("error_message", validation_errors());
		}

		redirect('receipt-payment/'.$visit_id.'/1');
	}
	public function view_patient_bill($visit_id,$page=NULL)
	{
		// $data = array('visit_id'=>$visit_id);

		if($page == NULL)
		{
			$page = 0;
		}
		$table= 'visit_charge, service_charge, service';
		$where='visit_charge.visit_charge_delete = 0 AND visit_charge.visit_id = '.$visit_id.' AND visit_charge.service_charge_id = service_charge.service_charge_id AND service.service_id = service_charge.service_id AND service.service_name <> "Others" AND visit_charge.charged = 1';

		$config["per_page"] = $v_data['per_page'] = $per_page = 10;
		if($page==0)
		{

			$counted = 0;
		}
		else if($page > 0)
		{

			$counted = $per_page*$page;
		}

		$v_data['page'] = $page;
		$v_data['visit_id'] = $visit_id;
		$page = $counted;
		$v_data['total_rows'] = $this->reception_model->count_items($table, $where);
		$query = $this->accounts_model->get_all_visits_invoice_items($table, $where, $config["per_page"], $page);



		$v_data['invoice_items'] = $query;

		$order = 'service_charge.service_charge_name';
		$where = 'service_charge.service_id = service.service_id AND service.service_name <> "Pharmarcy" AND service.service_delete = 0 AND service_charge.visit_type_id = visit_type.visit_type_id AND service_charge.visit_type_id = 1';

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



		$order = 'service.service_name';
		$where = 'service.service_name <> "Pharmacy" AND service_status = 1';

		$table = 'service';
		$service_query = $this->nurse_model->get_other_procedures($table, $where, $order);

		$rs9 = $service_query->result();
		$services_items = '';
		foreach ($rs9 as $rs11) :


			$service_id = $rs11->service_id;
			$service_name = $rs11->service_name;

			$services_items .="<option value='".$service_id."'>".$service_name."</option>";

		endforeach;

		$v_data['services_items'] = $services_items;





		// payments


		// old
		$v_data['cancel_actions'] = $this->accounts_model->get_cancel_actions();

		$this->load->view('view_bill',$v_data);

	}

	public function get_patient_receipt($visit_id,$page=NULL)
	{
		if($page == NULL)
		{
			$page = 0;
		}
		// $visit_id = 23;
		$table= 'payments, payment_method';
		$where="payments.cancel = 0 AND payment_method.payment_method_id = payments.payment_method_id AND payments.visit_id =". $visit_id;
		$config["per_page"] = $v_data['per_page'] = $per_page = 10;
		if($page==0)
		{

			$counted = 0;
		}
		else if($page > 0)
		{

			$counted = $per_page*$page;
		}

		$v_data['page'] = $page;
		$v_data['visit_id'] = $visit_id;
		$page = $counted;
		$v_data['total_rows'] = $this->reception_model->count_items($table, $where);

		$query = $this->accounts_model->get_all_visits_payments_items($table, $where, $config["per_page"], $page);



		$v_data['receipts_items'] = $query;

		$v_data['cancel_actions'] = $this->accounts_model->get_cancel_actions();

		$this->load->view('payments_made',$v_data);
	}

	public function get_services_billed($visit_id)
	{
		$v_data['visit_id'] = $visit_id;


		$this->load->view('billed_services',$v_data);
	}
	public function update_service_total($procedure_id,$units,$amount,$visit_id){

		$status = $this->accounts_model->check_if_visit_active($visit_id);
		if($status)
		{
			$notes = $this->input->post('notes');
			$visit_data = array('visit_charge_units'=>$units,'teeth'=>$notes,'visit_charge_amount'=>$amount, 'modified_by'=>$this->session->userdata("personnel_id"),'date_modified'=>date("Y-m-d"));
			$this->db->where(array("visit_charge_id"=>$procedure_id));
			$this->db->update('visit_charge', $visit_data);

			$response['status'] = "success";
			$response['message'] = "You have successfully updated the charge";
		}
		else
		{
			$response['status'] = "success";
			$response['message'] = "Sorry the visit has been ended";
		}
		echo json_encode($response);
	}


	public function update_quotation_total($procedure_id,$units,$amount,$visit_id){

		$status = $this->accounts_model->check_if_visit_active($visit_id);
		if($status)
		{
			$notes = $this->input->post('notes');
			$visit_data = array('visit_charge_units'=>$units,'visit_charge_notes'=>$notes,'visit_charge_amount'=>$amount, 'modified_by'=>$this->session->userdata("personnel_id"),'date_modified'=>date("Y-m-d"));
			$this->db->where(array("visit_charge_id"=>$procedure_id));
			$this->db->update('visit_quotation', $visit_data);

			$response['status'] = "success";
			$response['message'] = "You have successfully updated the charge";
		}
		else
		{
			$response['status'] = "success";
			$response['message'] = "Sorry the visit has been ended";
		}
		echo json_encode($response);
	}
	public function delete_service_billed($procedure_id,$visit_id)
	{

		$status = $this->accounts_model->check_if_visit_active($visit_id);
		if($status)
		{

			$visit_data = array('visit_charge_delete'=>1,'deleted_by'=>$this->session->userdata("personnel_id"),'deleted_on'=>date("Y-m-d"),'modified_by'=>$this->session->userdata("personnel_id"),'date_modified'=>date("Y-m-d"));

			$this->db->where(array("visit_charge_id"=>$procedure_id));
			$this->db->update('visit_charge', $visit_data);
			$response['status'] = "success";
			$response['message'] = "You have successfully updated the charge";
		}
		else
		{
			$response['status'] = "success";
			$response['message'] = "Sorry the visit has been ended";
		}
		echo json_encode($response);
	}

	public function add_patient_bill($visit_id)
	{
		$status = $this->accounts_model->check_if_visit_active($visit_id);
		if($status)
		{
			$service_charge_id = $this->input->post('service_charge_id');
			$provider_id = $this->input->post('provider_id');
			$visit_date = $this->input->post('visit_date');
			$amount = $this->accounts_model->get_service_charge_detail($service_charge_id);

			$visit_data = array('visit_charge_units'=>1,'visit_id'=>$visit_id,'visit_charge_amount'=>$amount,'service_charge_id'=>$service_charge_id, 'created_by'=>$this->session->userdata("personnel_id"),'provider_id'=>$provider_id,'date'=>$visit_date,'time'=>date('H:i:s'),'personnel_id'=>$procedure_id);

			if($this->db->insert('visit_charge', $visit_data))
			{

				$response['status'] = "success";
				$response['message'] = 'Sorry this visit has been closed';
			}
			else
			{
				$response['status'] = "fail";
				$response['message'] = 'Sorry this visit charge could not be added';
			}


		}
		else
		{
			$response['status'] = "fail";
			$response['message'] = 'Sorry this visit has been closed';
		}



		echo json_encode($response);
	}

	public function add_accounts_personnel()
	{
		//form validation rules
		$this->form_validation->set_rules('personnel_onames', 'Other Names', 'xss_clean');
		$this->form_validation->set_rules('personnel_fname', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('personnel_phone', 'Phone', 'xss_clean');
		$this->form_validation->set_rules('personnel_address', 'Address', 'xss_clean');
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			$personnel_id = $this->accounts_model->add_personnel();
			if($personnel_id > 0)
			{
				$this->session->set_userdata("success_message", "Personnel added successfully");

			}

			else
			{
				$this->session->set_userdata("error_message","Could not add personnel. Please try again ".$personnel_id);
			}
		}
		$redirect_url = $this->input->post('redirect_url');
		redirect($redirect_url);
	}


	public function change_patient_visit($visit_id)
	{
		$this->form_validation->set_rules('visit_type_id', 'Visit Type', 'required|xss_clean');
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			$visit_type_id = $this->input->post('visit_type_id');
			$array = array('visit_type'=>$visit_type_id);
			$this->db->where('visit_id',$visit_id);
			if($this->db->update('visit',$array))
			{
				$this->session->set_userdata("success_message", "You have successfully changed the patient type");
			}

			else
			{
				$this->session->set_userdata("error_message","Please try again ");
			}
		}else
		{
			$this->session->set_userdata("error_message","Please try again ");
		}
		$redirect_url = $this->input->post('redirect_url');
		redirect($redirect_url);

	}

	public function patient_visits()
	{

		$delete = 0;
		$segment = 3;

		$patient_search = $this->session->userdata('visit_accounts_search');
		$where = 'patient_delete = 0 AND patients.patient_type = 0 AND patients.patient_id <> 293 AND patients.patient_id IN (SELECT patient_id FROM visit WHERE visit_delete =0)';
		if(!empty($patient_search))
		{
			$where .= $patient_search;
		}

		else
		{

		}

		$table = 'patients';
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'cash-office/patient-visits';
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
		$query = $this->accounts_model->get_all_patients_accounts($table, $where, $config["per_page"], $page);


		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['delete'] = 0;
		$v_data['title'] = 'Patients Visits';
		$v_data['branches'] = $this->reception_model->get_branches();
		$data['content'] = $this->load->view('all_patients_list', $v_data, true);

		$data['sidebar'] = 'reception_sidebar';

		$this->load->view('admin/templates/general_page', $data);
		// end of it

	}


	function get_visit_balance($visit_id)
	{
		$payments_value = $this->accounts_model->total_payments($visit_id);
		$invoice_total = $this->accounts_model->total_invoice($visit_id);
		$balance = $this->accounts_model->balance($payments_value,$invoice_total);
		$response['balance'] = $balance;

		echo json_encode($response);
	}

	public function send_todays_report()
	{
		$date_tomorrow = date('Y-m-d');
		$visit_date = date('jS M Y',strtotime($date_tomorrow));
		$branch = $this->config->item('branch_name');
		$message['subject'] =  $branch_name.' '.$visit_date.' report';

		$where = $where1 = $where6 = 'visit.patient_id = patients.patient_id AND visit.visit_delete = 0 AND visit.visit_date = "'.date('Y-m-d').'"';
		$payments_where = 'visit.patient_id = patients.patient_id AND visit.visit_delete = 0 ';
		$table = 'visit, patients';



		//cash payments
		$where2 = $payments_where.' AND payments.payment_method_id = 2 AND payments.payment_type = 1 AND payments.cancel = 0 AND payments.payment_created = "'.date('Y-m-d').'"';
		$total_cash_collection = $this->reports_model->get_total_cash_collection($where2, $table);

		// mpesa
		$where2 = $payments_where.' AND payments.payment_method_id = 5 AND payments.payment_type = 1 AND payments.cancel = 0 AND payments.payment_created = "'.date('Y-m-d').'"';
		$total_mpesa_collection = $this->reports_model->get_total_cash_collection($where2, $table);

		$where2 = $payments_where.' AND (payments.payment_method_id = 1 OR  payments.payment_method_id = 6 OR  payments.payment_method_id = 7 OR  payments.payment_method_id = 8)  AND payments.payment_type = 1 AND payments.cancel = 0 AND payments.payment_created = "'.date('Y-m-d').'"';
		$total_other_collection = $this->reports_model->get_total_cash_collection($where2, $table);


		$where4 = 'payments.payment_method_id = payment_method.payment_method_id AND payments.visit_id = visit.visit_id  AND visit.visit_delete = 0  AND visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND payments.cancel = 0 AND payments.payment_type = 3 AND payments.payment_created = "'.date('Y-m-d').'"';
		$total_waiver = $this->reports_model->get_total_cash_collection($where4, 'payments, visit, patients, visit_type, payment_method', 'cash');


		 // var_dump($total_other_collection+$total_mpesa_collection+$total_cash_collection); die();

		//count outpatient visits
		$where2 = $where1.' AND visit.inpatient = 0';

		(int)$outpatients = $this->reception_model->count_items($table, $where2);
		// var_dump($outpatients); die();
		//count inpatient visits
		$where2 = $where6.' AND visit.inpatient = 1';

		(int)$inpatients = $this->reception_model->count_items($table, $where2);


		$table1 = 'petty_cash,account';
		$where1 = 'petty_cash.account_id = account.account_id AND (account.account_name = "Cash Box" OR account.account_name = "Cash Collection") AND petty_cash.petty_cash_delete = 0';


		$where1 .=' AND petty_cash.petty_cash_date = "'.date('Y-m-d').'"';
		$total_transfers = $this->reports_model->get_total_transfers($where1,$table1);

		$total_patients = $outpatients + $inpatients;

		$message['text'] = ' <p>Good evening to you,<br>
								Herein is a report of todays transactions. This is sent at '.date('H:i:s A').'
								</p>
							  <table>
							  		<thead>
							  			<tr>
							  				<th width="50%"></th>
							  				<th width="50%"></th>
							  			</tr>
							  		</thead>
							  		</tbody>
							      	<tr>
							      		<td>Out-Patients </td><td>  '.$outpatients.'</td>
							      	</tr>
							      	<tr>
							      		<td>Inpatients </td><td> '.$inpatients.'</td>
							      	</tr>
							      	<tr>
							      		<td><strong>Total patients</strong> </td><td><strong> '.$total_patients.' </strong></td>
							      	</tr>
							      	<tr>
							      		<td>Total Cash Collection </td><td>KES. '.number_format($total_cash_collection,2).'</td>
							      	</tr>
							      	<tr>
							      		<td>Total M-pesa Collection </td><td> KES. '.number_format($total_mpesa_collection,2).'</td>
							      	</tr>
							      	<tr>
							      		<td>Total Other Collections </td><td> KES. '.number_format($total_other_collection,2).'</td>
							      	</tr>

							      	<tr>
							      		<td>Total Cash - Petty cash transer </td><td> ( KES. '.number_format($total_transfers,2).' )</td>
							      	</tr>
							      	<tr>
							      		<td>Total Waivers </td><td> KES. '.number_format($total_waiver,2).'</td>
							      	</tr>
							      	<tr>
							      		<td><strong>Total Revenue Collected</strong> </td><td><strong> KES. '.number_format(($total_mpesa_collection + $total_cash_collection + $total_other_collection) - $total_transfers,2).' </strong></td>
							      	</tr>
							      	</tbody>

							  </table>
							';
		$contacts = $this->site_model->get_contacts();
		$sender_email =$this->config->item('sender_email');//$contacts['email'];
		$shopping = "";
		$from = $sender_email;

		$button = '';
		$sender['email']= $sender_email;
		$sender['name'] = $contacts['company_name'];
		$receiver['name'] = $subject;
		// $payslip = $title;

		$sender_email = $sender_email;
		$tenant_email .= $this->config->item('recepients_email');;
		// var_dump($tenant_email); die();
		$email_array = explode('/', $tenant_email);
		$total_rows_email = count($email_array);

		for($x = 0; $x < $total_rows_email; $x++)
		{
			$receiver['email'] = $email_tenant = $email_array[$x];

			$this->email_model->send_sendgrid_mail($receiver, $sender, $message, $payslip=NULL);


		}
	}


	public function make_payment_charge($visit_id, $close_page = NULL)
	{

		$this->form_validation->set_rules('type_payment', 'Type of payment', 'trim|required|xss_clean');
		$payment_method = $this->input->post('payment_method');
		// normal or credit note or debit note
		$type_payment = $this->input->post('type_payment');


			if($type_payment == 1)
			{
				$this->form_validation->set_rules('amount_paid', 'Amount', 'trim|required|xss_clean');
				$this->form_validation->set_rules('payment_method', 'Payment Method', 'trim|required|xss_clean');
				$this->form_validation->set_rules('payment_service_id', 'Payment Service', 'trim|xss_clean');
				$this->form_validation->set_rules('service_id', 'Service', 'xss_clean');
				if(!empty($payment_method))
				{
					if($payment_method == 1)
					{
						// check for cheque number if inserted
						$this->form_validation->set_rules('cheque_number', 'Cheque Number', 'trim|required|xss_clean');
					}
					else if($payment_method == 6)
					{
						// check for insuarance number if inserted
						$this->form_validation->set_rules('insuarance_number', 'Credit Card Detail', 'trim|required|xss_clean');
					}
					else if($payment_method == 5)
					{
						//  check for mpesa code if inserted
						$this->form_validation->set_rules('mpesa_code', 'Amount', 'trim|xss_clean');
					}
					else if($payment_method == 7)
					{
						//  check for mpesa code if inserted
						$this->form_validation->set_rules('deposit_detail', 'Bank Deposit', 'trim|xss_clean');
					}
					else if($payment_method == 8)
					{
						//  check for mpesa code if inserted
						$this->form_validation->set_rules('debit_card_detail', 'Debit Card', 'trim|required|xss_clean');
					}
				}
			}
			else if($type_payment == 2)
			{
				$this->form_validation->set_rules('waiver_amount', 'Amount', 'trim|required|xss_clean');
				$this->form_validation->set_rules('reason', 'Reason', 'trim|required|xss_clean');
				// var_dump($_POST); die();
				// debit note
				// $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
				// $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
				// $this->form_validation->set_rules('payment_service_id', 'Service', 'required|xss_clean');
			}
			else if($type_payment == 3)
			{
				$this->form_validation->set_rules('waiver_amount', 'Amount', 'trim|required|xss_clean');
				$this->form_validation->set_rules('reason', 'Reason', 'trim|required|xss_clean');
				// $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
				// $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
				// $this->form_validation->set_rules('payment_service_id', 'Service', 'required|xss_clean');
			}
			//if form conatins invalid data
			if ($this->form_validation->run())
			{
				// var_dump($_POST); die();

				if($type_payment == 2 OR $type_payment == 3)
				{
					$checked = $this->session->userdata('authorize_invoice_changes');


				}
				else
				{
					$checked = TRUE;
				}

				if($checked)
				{


					if($this->accounts_model->receipt_payment($visit_id))
					{
						$response['result'] ='success';
						$response['message'] ='You have successfully receipted the payment';
						$this->session->set_userdata('success_message', 'You have successfully receipted the payment');
					}
					else
					{
						$response['result'] ='fail';
						$response['message'] ='Seems like you dont have the priviledges to effect this event. Please contact your administrator.';
						$this->session->set_userdata('error_message', 'Seems like you dont have the priviledges to effect this event. Please contact your administrator.');
					}
				}
				else
				{
					$response['message'] ='Seems like you dont have the priviledges to effect this event. Please contact your administrator.';
					$this->session->set_userdata('error_message', 'Seems like you dont have the priviledges to effect this event. Please contact your administrator.');

				}


			}
			else
			{
				$response['result'] ='fail';
				$response['message'] =validation_errors();
				$this->session->set_userdata('error_message', $validation_errors());
			}

			redirect('receipt-payment/'.$visit_id.'/'.$close_page);

			// }

	}

	public function send_back_to_department($visit_id,$close_page)
	{
		$array_charge['closed'] = 1;
		$this->db->where('visit_id',$visit_id);
		$this->db->update('visit',$array_charge);

		if($close_page == 5)
		{
			$array['charged'] = 1;
			$this->db->where('visit_id',$visit_id);
			$this->db->update('visit_charge',$array);
		}

		if($this->reception_model->set_visit_department($visit_id, $close_page))
		{
			redirect('queues/walkins');
		}
		else
		{
			FALSE;
		}
	}


	public function bill_patient($visit_id,$module =null)
	{
		$status = $this->accounts_model->check_if_visit_active($visit_id);
		if($status)
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

		}
		else
		{
			$this->session->set_userdata('error_message', 'Sorry visit has been ended');
		}

		if(empty($module))
		{
			$module = 0;
		}
		redirect('receipt-payment/'.$visit_id.'/'.$module);

	}


	public function add_payment_to_invoice($visit_id, $close_page = NULL)
	{

		$this->form_validation->set_rules('type_payment'.$visit_id, 'Type of payment', 'trim|required|xss_clean');
		$payment_method = $this->input->post('payment_method'.$visit_id);
		// normal or credit note or debit note
		$type_payment = $this->input->post('type_payment'.$visit_id);


			if($type_payment == 1)
			{
				$this->form_validation->set_rules('amount_paid'.$visit_id, 'Amount', 'trim|required|xss_clean');
				$this->form_validation->set_rules('payment_method'.$visit_id, 'Payment Method', 'trim|required|xss_clean');
				if(!empty($payment_method))
				{
					if($payment_method == 1)
					{
						// check for cheque number if inserted
						$this->form_validation->set_rules('cheque_number', 'Cheque Number', 'trim|required|xss_clean');
					}
					else if($payment_method == 6)
					{
						// check for insuarance number if inserted
						$this->form_validation->set_rules('insuarance_number', 'Credit Card Detail', 'trim|required|xss_clean');
					}
					else if($payment_method == 5)
					{
						//  check for mpesa code if inserted
						$this->form_validation->set_rules('mpesa_code', 'Amount', 'trim|required|xss_clean');
					}
					else if($payment_method == 7)
					{
						//  check for mpesa code if inserted
						$this->form_validation->set_rules('deposit_detail', 'Bank Deposit', 'trim|required|xss_clean');
					}
					else if($payment_method == 8)
					{
						//  check for mpesa code if inserted
						$this->form_validation->set_rules('debit_card_detail', 'Debit Card', 'trim|required|xss_clean');
					}
				}
			}
			else if($type_payment == 2)
			{
				$this->form_validation->set_rules('waiver_amount', 'Amount', 'trim|required|xss_clean');


				// debit note
				// $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
				// $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
				// $this->form_validation->set_rules('payment_service_id', 'Service', 'required|xss_clean');
			}
			else if($type_payment == 3)
			{
				// $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
				// $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
				// $this->form_validation->set_rules('payment_service_id', 'Service', 'required|xss_clean');
			}
			//if form conatins invalid data
			if ($this->form_validation->run())
			{
				// var_dump($_POST); die();
				if($type_payment == 2 OR $type_payment == 3)
				{
					$checked = $this->session->userdata('authorize_invoice_changes');


				}
				else
				{
					$checked = TRUE;
				}

				if($checked)
				{


					if($this->accounts_model->receipt_payment($visit_id))
					{
						$response['result'] ='success';
						$response['message'] ='You have successfully receipted the payment';
						$this->session->set_userdata('success_message', 'You have successfully receipted the payment');
					}
					else
					{
						$response['result'] ='fail';
						$response['message'] ='Seems like you dont have the priviledges to effect this event. Please contact your administrator.';
						$this->session->set_userdata('error_message', 'Seems like you dont have the priviledges to effect this event. Please contact your administrator.');
					}
				}
				else
				{
					$response['message'] ='Seems like you dont have the priviledges to effect this event. Please contact your administrator.';
					$this->session->set_userdata('error_message', 'Seems like you dont have the priviledges to effect this event. Please contact your administrator.');

				}


			}
			else
			{
				$response['result'] ='fail';
				$response['message'] =validation_errors();
			}
			redirect('receipt-payment/'.$visit_id.'/'.$close_page);

	}

	public function delete_service_visit_billed($visit_charge_id,$visit_id,$module)
	{

		$status = $this->accounts_model->check_if_visit_active($visit_id);
		if($status)
		{

			$visit_data = array('visit_charge_delete'=>1,'deleted_by'=>$this->session->userdata("personnel_id"),'deleted_on'=>date("Y-m-d"),'modified_by'=>$this->session->userdata("personnel_id"),'date_modified'=>date("Y-m-d"));

			$this->db->where(array("visit_charge_id"=>$visit_charge_id));
			$this->db->update('visit_charge', $visit_data);
			$response['status'] = "success";
			$response['message'] = "You have successfully updated the charge";
		}
		else
		{
			$response['status'] = "success";
			$response['message'] = "Sorry the visit has been ended";
		}
		redirect('receipt-payment/'.$visit_id.'/0');
	}

	public function end_visit($visit_id)
	{
		if($this->accounts_model->end_visit($visit_id))
		{

			$this->send_message($visit_id);
			$this->session->set_userdata('success_message', 'Visits ended successfully');
			redirect('queue');
		}

		else
		{
			$this->session->set_userdata('error_message', 'Unable to end visits');
			redirect('receipt-payment/'.$visit_id.'/1');
		}
	}

	public function remove_invoice($visit_id,$parent_invoice)
	{
		if($this->accounts_model->close_visit($visit_id))
		{

			$this->session->set_userdata('success_message', 'Visits deleted successfully');
			redirect('receipt-payment/'.$visit_id.'/1');
		}

		else
		{
			$this->session->set_userdata('error_message', 'Unable to end visits');
			redirect('receipt-payment/'.$visit_id.'/1');
		}
	}
	public function remove_rejected_amount($visit_id)
	{

		$array['rejected_reason'] = '';
		$array['rejected_amount'] = NULL;
		$this->db->where('visit_id',$visit_id);
		$this->db->update('visit',$array);

		redirect('receipt-payment/'.$visit_id.'/1');

	}
	public function update_rejected_reasons($visit_id)
	{
		$this->form_validation->set_rules('rejected_amount', 'Amount', 'trim|required|xss_clean');
        $this->form_validation->set_rules('rejected_reason', 'Reason', 'trim|required|xss_clean');
        $this->form_validation->set_rules('visit_type_id', 'Visit Type', 'trim|required|xss_clean');
        $redirect_url = $this->input->post('redirect_url');
        //if form conatins invalid data
        if ($this->form_validation->run())
        {
            // end of checker function
            if($this->accounts_model->update_rejected_reasons($visit_id))
            {
                $this->session->set_userdata("success_message", "Reject  saved successfully");
            }
            else
            {
                $this->session->set_userdata("error_message", "Oops something went wrong. Please try again");
            }
        }
        else
        {
            $this->session->set_userdata("error_message", validation_errors());

        }
        redirect($redirect_url);
	}
	public function update_invoices_amounts()
	{
		$this->db->where('visit.parent_visit > 0 AND visit.visit_id = visit_bill.visit_id AND payment_updated = 0 ');
		$query = $this->db->get('visit,visit_bill');

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$visit_id = $value->visit_id;
				$parent_visit = $value->parent_visit;

				$update_data['visit_id'] = $parent_visit;
				$this->db->where('visit_id',$visit_id);
				$this->db->update('payments',$update_data);



				$update['payment_updated'] = 1;
				$this->db->where('visit_id',$visit_id);
				$this->db->update('visit_bill',$update);


			}
		}
	}


	public function update_doctor_amounts()
	{
		$this->db->where('visit.parent_visit > 0 AND visit.visit_id = visit_bill.visit_id AND payment_updated = 1 ');
		$query = $this->db->get('visit,visit_bill');

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$visit_id = $value->visit_id;
				$parent_visit = $value->parent_visit;

				$update_data['visit_id'] = $parent_visit;
				$this->db->where('visit_id',$visit_id);
				$this->db->update('doctor_invoice',$update_data);



				$update['payment_updated'] = 2;
				$this->db->where('visit_id',$visit_id);
				$this->db->update('visit_bill',$update);


			}
		}
	}
	public function view_procedure($visit_id){
		$data = array('visit_id'=>$visit_id);
		$this->load->view('view_bill',$data);
	}

	public function approved_invoice($visit_id)
	{
		if($this->accounts_model->approve_invoice($visit_id))
		{

			$this->send_approved_message($visit_id);
			$this->session->set_userdata('success_message', 'Invoice has been marked as approved');
			redirect('preauths');
		}

		else
		{
			$this->session->set_userdata('error_message', 'Unable to end visits');
			redirect('receipt-payment/'.$visit_id.'/2');
		}
	}


	public function send_approved_message($visit_id)
	{

		$patient = $this->reception_model->patient_names2(NULL, $visit_id);
		$v_data['patient_type'] = $patient['patient_type'];
		$v_data['patient_othernames'] = $patient['patient_othernames'];
		$v_data['patient_surname'] = $patient['patient_surname'];
		$v_data['patient_type_id'] = $patient['visit_type_id'];
		$v_data['account_balance'] = $patient['account_balance'];
		$v_data['visit_type_name'] = $patient['visit_type_name'];
		$v_data['patient_id'] = $patient['patient_id'];
		$v_data['inatient'] = $patient['inatient'];
		$v_data['patient_phone1'] = $patient_phone = $patient['patient_phone_number'];

		$message  = 'Hello '.$v_data['patient_othernames'].' the preauthorization has been approved. Kindly book an appointment with us on 0740579064. Thank you Arrow Dental';



		$message_data = array(
						"phone_number" => $patient_phone,
						"entryid" => $v_data['patient_id'],
						"message" => $message,
						"message_batch_id"=>0,
						'message_status' => 0
					);
		$this->db->insert('messages', $message_data);
		$message_id = $this->db->insert_id();
		// $patient_phone = 721481703;
		$patient_phone = str_replace(' ', '', $patient_phone);
		// var_dump($patient); die();
		if(!empty($patient_phone))
		{
			$response = $this->messaging_model->sms($patient_phone,$message);

			if($response == "Success" OR $response == "success")
			{

				$service_charge_update = array('message_status' => 1,'delivery_message'=>'Success','sms_cost'=>3);
				$this->db->where('message_id',$message_id);
				$this->db->update('messages', $service_charge_update);

			}
			else
			{
				$service_charge_update = array('message_status' => 0,'delivery_message'=>'Success','sms_cost'=>0);
				$this->db->where('message_id',$message_id);
				$this->db->update('messages', $service_charge_update);


			}
		}
		else
		{
			$response['status'] = 1;
			$response['message'] = "Sorry could not send message";
		}

		 echo json_encode($response);
	}


	public function accounts_update_bill($procedure_id,$v_id,$suck)
	{
		$service_charge_rs = $this->accounts_model->get_service_charge($procedure_id);

		foreach ($service_charge_rs as $key) :
			# code...
			$visit_charge_amount = $key->service_charge_amount;
		endforeach;

		$rs = $this->nurse_model->check_visit_type($v_id);
		if(count($rs)>0){
		  foreach ($rs as $rs1) {
			# code...
			  $visit_t = $rs1->visit_type;
		  }
		}

		$visit_data = array('service_charge_id'=>$procedure_id,'visit_id'=>$v_id,'visit_charge_amount'=>$visit_charge_amount,'visit_charge_units'=>$suck,'charge_to'=>$visit_t,'created_by'=>$this->session->userdata("personnel_id"),'date'=>date("Y-m-d"),'charged'=>1);
		$this->db->insert('visit_charge', $visit_data);

		$response['status'] = 1;
		$response['message'] = "Sorry could not send message";
		 echo json_encode($response);
	}

	public function change_payer($visit_charge_id,$service_charge_id,$visit_id){

		// $status = $this->accounts_model->check_if_visit_active($visit_id);
		// if($status)
		// {

			$rs = $this->nurse_model->check_visit_type($visit_id);
			if(count($rs)>0){
			  foreach ($rs as $rs1) {
				# code...
				  $visit_t = $rs1->visit_type;
			  }
			}

			$this->db->where('visit_charge_id',$visit_charge_id);
			$query = $this->db->get('visit_charge');

			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$charge_to = $value->charge_to;
				}
			}

			if($charge_to == $visit_t)
			{
				$charge = 1;
			}
			else
			{
				$charge = $visit_t;
			}

			// if(empty($charge))
			// {
			// 	$charge = $visit_t;
			// }
			$visit_data = array('charge_to'=>$charge);
			$this->db->where(array("visit_charge_id"=>$visit_charge_id));
			$this->db->update('visit_charge', $visit_data);

			$response['status'] = "success";
			$response['message'] = "You have successfully updated the charge";
		// }
		// else
		// {
		// 	$response['status'] = "success";
		// 	$response['message'] = "Sorry the visit has been ended";
		// }
		echo json_encode($response);
	}


	public function print_quote($visit_id,$page_item = NULL)
	{
		$data = array('visit_id'=>$visit_id);
		$data['contacts'] = $this->site_model->get_contacts();
		$data['page_item'] = $page_item;
		$patient = $this->reception_model->patient_names2(NULL, $visit_id);
		$data['patient'] = $patient;
		$this->load->view('quote', $data);

	}

}
?>
