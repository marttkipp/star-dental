<?php

class Budget_model extends CI_Model
{

	function get_all_expense_account($account_id = NULL)
	{
		if(!empty($account_id))
		{
			$add = ' AND account_id <> '.$account_id;
		}
		else
		{
			$add ='';
		}

		$this->db->where('account_type_id = 2 AND parent_account <> 0'.$add);
      	$this->db->order_by('parent_account','ASC');
		$query = $this->db->get('account');

		return $query;
	}


	function get_all_parent_expense_accounts($account_id = NULL)
	{
		

		$this->db->where('account_type_id = 2 AND parent_account = 0 ');
      	$this->db->order_by('parent_account','ASC');
		$query = $this->db->get('account');

		return $query;
	}

	function get_all_child_expense_accounts($parent_account)
	{
		

		$this->db->where('account_type_id = 2 AND parent_account = '.$parent_account);
      	$this->db->order_by('account_name','ASC');
		$query = $this->db->get('account');

		return $query;
	}
	public function get_account_name($from_account_id)
	{
		$account_name = '';
		$this->db->select('account_name');
		$this->db->where('account_id = '.$from_account_id);
		$query = $this->db->get('account');

		$account_details = $query->row();
		$account_name = $account_details->account_name;

		return $account_name;
	}
	public function get_month()
	{
		$result = $this->db->get("month");
		
		return $result;
	}

	public function get_budget_list($budget_year,$month,$account_id)
	{
		$this->db->select('departments.*,personnel.personnel_fname,budget_item.*,account.*');
		$this->db->where("departments.department_id = budget_item.department_id AND account.account_id = budget_item.account_id AND budget_item.budget_deleted = 0 AND budget_item.budget_year = ".$budget_year." AND budget_item.budget_month = '".$month."' AND budget_item.account_id= ".$account_id." ");
		$this->db->join("personnel",'personnel.personnel_id = budget_item.created_by','LEFT');
		$result = $this->db->get("departments,budget_item,account");
		
		return $result;
	}
	public function get_departments()
	{
		$this->db->where("department_status = 1");
		$result = $this->db->get("departments");
		
		return $result;
	}

	public function get_total_amount_sum($year,$month=NULL,$account_id)
	{
		$this->db->select("SUM(budget_item_amount) AS total_amount");
		$this->db->where("budget_deleted = 0 AND budget_item.account_id = ".$account_id." AND budget_year = '".$year."' AND budget_month = '".$month."' ");
		$result = $this->db->get("budget_item");
		
		$budget_item_amount = 0;
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $key => $value) {
				# code...
				$budget_item_amount = $value->total_amount;
			}
		}
		if(empty($budget_item_amount))
		{
			$budget_item_amount = 0;
		}
		return $budget_item_amount;
	}


	public function get_total_amount_sum_parent($year,$account_id = NULL,$month=NULL)
	{
		$add = '';
		if(!empty($month))
		{
			$add .= ' AND budget_item.budget_month = "'.$month.'"';
		}

		if(!empty($account_id))
		{
			$add .= ' AND account.parent_account = '.$account_id;
		}
		$this->db->select("SUM(budget_item_amount) AS total_amount");
		$this->db->where("budget_deleted = 0 AND budget_item.account_id = account.account_id AND account.account_type_id = 2  AND budget_year = '".$year."' ".$add);
		$result = $this->db->get("budget_item,account");
		
		$budget_item_amount = 0;
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $key => $value) {
				# code...
				$budget_item_amount = $value->total_amount;
			}
		}
		if(empty($budget_item_amount))
		{
			$budget_item_amount = 0;
		}
		return $budget_item_amount;
	}

	public function get_total_amount_sum_actual_parent($year,$account_id=NULL,$month=NULL)
	{
		$add = '';
		if(!empty($month))
		{
			$add .= ' AND MONTH(referenceDate) = "'.$month.'"';
		}
		if(!empty($account_id))
		{
			$add .= ' AND account.parent_account = '.$account_id;
		}
		$this->db->select("SUM(dr_amount) AS total_amount");
		$this->db->where("YEAR(referenceDate) = '".$year."'  AND v_general_ledger.accountId = account.account_id AND account.account_type_id = 2   ".$add);
		$result = $this->db->get("v_general_ledger,account");
		
		$budget_item_amount = 0;
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $key => $value) {
				# code...
				$budget_item_amount = $value->total_amount;
			}
		}
		if(empty($budget_item_amount))
		{
			$budget_item_amount = 0;
		}
		return $budget_item_amount;
	}


	public function get_total_amount_sum_actual($year,$month,$account_id)
	{
		$this->db->select("SUM(dr_amount) AS total_amount");
		$this->db->where("accountId = ".$account_id." AND YEAR(referenceDate) = '".$year."' AND MONTH(referenceDate) = '".$month."' ");
		$result = $this->db->get("v_general_ledger");
		
		$budget_item_amount = 0;
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $key => $value) {
				# code...
				$budget_item_amount = $value->total_amount;
			}
		}
		if(empty($budget_item_amount))
		{
			$budget_item_amount = 0;
		}
		return $budget_item_amount;
	}

	public function confirm_budget_item($budget_year)
	{
		$month =  $this->input->post('budget_month');

		// if($month < 10)
		// {
		// 	$month = '0'.$month;
		// }
		$array['budget_year'] = $this->input->post('budget_year');
		$array['budget_month'] = $month;
		$array['department_id'] = $this->input->post('department_id');
		$array['account_id'] = $this->input->post('account_id');
		$array['budget_deleted_by'] = NULL;
		$array['budget_deleted'] = 0;

		$this->db->where($array);
		$query = $this->db->get('budget_item');

		if($query->num_rows() > 0)
		{
			$this->db->where($array);
			$array['budget_item_amount'] = $this->input->post('budget_amount');
			$array['created_by'] = $this->session->userdata('budget_amount');
			$array['created'] = date('Y-m-d');


			if($this->db->update('budget_item',$array))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}

		}
		else
		{
			$array['budget_item_amount'] = $this->input->post('budget_amount');
			$array['created_by'] = $this->session->userdata('budget_amount');
			$array['created'] = date('Y-m-d');

			if($this->db->insert('budget_item',$array))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}


	}

	public function get_actual_salary_expenses($year,$month)
	{
		
		//retrieve all users
		$this->db->from('v_payroll');
		$this->db->select('SUM(total_payroll) AS total_amount');
		$this->db->where('payroll_id > 0 AND payroll_year = "'.$year.'" AND month_id ="'.$month.'" ');
		// $this->db->group_by('accountId');
		$total_amount = 0;
		$query = $this->db->get();
		if($query->num_rows() > 0 )
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_amount = $value->total_amount;
			}
		}

		return $total_amount;
	}
	public function get_actual_paye_expenses($year,$month)
	{
		
		//retrieve all users
		$this->db->from('v_payroll');
		$this->db->select('SUM(total_paye) AS total_amount');
		$this->db->where('payroll_id > 0 AND payroll_year = "'.$year.'" AND month_id ="'.$month.'" ');
		// $this->db->group_by('accountId');
		$total_amount = 0;
		$query = $this->db->get();
		if($query->num_rows() > 0 )
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_amount = $value->total_amount;
			}
		}

		return $total_amount;
	}

	public function get_actual_nssf_expenses($year,$month)
	{
		
		//retrieve all users
		$this->db->from('v_payroll');
		$this->db->select('SUM(total_nssf) AS total_amount');
		$this->db->where('payroll_id > 0 AND payroll_year = "'.$year.'" AND month_id ="'.$month.'" ');
		// $this->db->group_by('accountId');
		$total_amount = 0;
		$query = $this->db->get();
		if($query->num_rows() > 0 )
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_amount = $value->total_amount;
			}
		}

		return $total_amount;
	}
	public function get_actual_nhif_expenses($year,$month)
	{
		
		//retrieve all users
		$this->db->from('v_payroll');
		$this->db->select('SUM(total_nhif) AS total_amount');
		$this->db->where('payroll_id > 0 AND payroll_year = "'.$year.'" AND month_id ="'.$month.'" ');
		// $this->db->group_by('accountId');
		$total_amount = 0;
		$query = $this->db->get();
		if($query->num_rows() > 0 )
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_amount = $value->total_amount;
			}
		}

		return $total_amount;
	}

	public function get_actual_total_salary_expenses($year)
	{
		
		//retrieve all users
		$this->db->from('v_payroll');
		$this->db->select('SUM(total_additions) AS total_amount');
		$this->db->where('payroll_id > 0 AND payroll_year = "'.$year.'"');
		// $this->db->group_by('accountId');
		$total_amount = 0;
		$query = $this->db->get();
		if($query->num_rows() > 0 )
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_amount = $value->total_amount;
			}
		}

		return $total_amount;
	}

	public function get_total_amount_summary_actual($year,$account_id)
	{
		$this->db->select("SUM(dr_amount) AS total_amount");
		$this->db->where("accountId = ".$account_id." AND YEAR(referenceDate) = '".$year."' ");
		$result = $this->db->get("v_general_ledger");
		
		$budget_item_amount = 0;
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $key => $value) {
				# code...
				$budget_item_amount = $value->total_amount;
			}
		}
		if(empty($budget_item_amount))
		{
			$budget_item_amount = 0;
		}
		return $budget_item_amount;
	}
}
?>