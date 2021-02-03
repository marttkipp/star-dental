<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');


require_once "./application/modules/admin/controllers/admin.php";
error_reporting(0);
class Budget extends admin
{
	function __construct()
	{
		parent:: __construct();

    	$this->load->model('finance/budget_model');
	}

	public function index()
  	{
	    // $v_data['property_list'] = $property_list;

	    $budget_year = $this->session->userdata('budget_year');

	    if(empty($budget_year))
	    {
	    	$data['title'] = 'BUDGET FOR '.date('Y');
	    	$budget_year = date('Y');
	    }
	    else
	    {
	    	$data['title'] = 'BUDGET FOR '.$budget_year;
	    }
	    $v_data['budget_year'] = $budget_year;
	    $v_data['title'] = $data['title'];
	    $data['content'] = $this->load->view('budget/budget_view', $v_data, true);
	    $this->load->view('admin/templates/general_page', $data);
  	}
  	public function get_year_budget($budget_year)
  	{
  		$v_data['budget_year'] = $budget_year;
	    $data['result'] = $this->load->view('budget/budget_table', $v_data,true);
	    $data['message'] =  'success';

	    echo json_encode($data);
  	}
  	public function add_budget_item($budget_year,$month=NULL,$account_id=NULL)
  	{
  		$v_data['budget_year'] = $budget_year;
  		$v_data['month_id'] = $month;
  		$v_data['account_id'] = $account_id;
	    $data['result'] = $this->load->view('budget/budget_add_item', $v_data);
	    $data['message'] =  'success';

	    echo json_encode($data);
  	}
  	public function confirm_budget_item($budget_year)
  	{
  		$this->form_validation->set_rules('budget_amount', 'Budget Amount', 'trim|numeric|xss_clean');
		$this->form_validation->set_rules('account_id', 'Account', 'trim|required|xss_clean');
		$this->form_validation->set_rules('budget_year', 'Budget Year', 'trim|required|xss_clean');
		$this->form_validation->set_rules('budget_month', 'Budget Month', 'trim|required|xss_clean');
	   

		if ($this->form_validation->run())
		{
        // var_dump($_POST);die();
				$this->budget_model->confirm_budget_item($budget_year);

				$this->session->set_userdata("success_message", 'Creditor invoice successfully added');
				$response['status'] = 'success';
				$response['message'] = 'Payment successfully added';
		}
		else
		{
			$this->session->set_userdata("error_message", validation_errors());
			$response['status'] = 'fail';
			$response['message'] = strip_tags(validation_errors());

		}

		echo json_encode($response);
  	}
  	public function search_budget()
  	{
  		$budget_year = $this->input->post('budget_year');

  		$this->session->set_userdata('budget_year',$budget_year);

  		redirect('accounting/budget');
  	}
  	public function close_budget_search()
  	{
  		$this->session->unset_userdata('budget_year');

  		redirect('accounting/budget');
  	}
  	public function get_budget_list($budget_year,$month,$account_id)
  	{
  		if (substr($month, 0, 1) === '0') 
		{
			$month = ltrim($month, '0');
		}

  		$v_data['budget_year'] = $budget_year;
  		$v_data['month'] = $month;
  		$v_data['account_id'] = $account_id;
	    $data['result'] = $this->load->view('budget/budget_list', $v_data,true);
	    $data['message'] =  'success';

	    echo json_encode($data);

  	}
  	public function delete_budget_item($budget_item_id)
  	{
  		$array['budget_deleted'] = 1;
  		$array['budget_deleted_by'] = $this->session->userdata('personnel_id');

  		$this->db->where('budget_item_id',$budget_item_id);
  		$this->db->update('budget_item',$array);
  	}
}
?>