<?php

class Calendar_model extends CI_Model 
{
	/*
	*	Check if parent has children
	*
	*/

	public function get_branch_doctors($branch_id=NULL)
	{

		$add = '';
		// if(!empty($branch_id))
		// {
		// 	$add = ' AND personnel.branch_id = '.$branch_id;
		// }
		$table = "personnel, personnel_job,job_title";
		$where = "personnel_job.personnel_id = personnel.personnel_id AND personnel.personnel_status = 1 AND personnel_job.job_title_id = job_title.job_title_id AND job_title.job_title_name = 'Dentist' ".$add;
		$items = "personnel.personnel_onames, personnel.personnel_fname, personnel.personnel_id,personnel.branch_id,personnel.authorize_invoice_changes";
		$order = "personnel_onames";
		


		$this->db->where($where);
		$this->db->select($items);
		$query = $this->db->get($table);
		
		return $query;
	}
}
?>