<?php

class Ledgers_model extends CI_Model
{

	public function get_child_accounts($parent_account_name)
    {
    	$this->db->from('account');
		$this->db->select('*');
		$this->db->where('account_name = "'.$parent_account_name.'" AND account.account_status = 1');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)  
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$account_id = $value->account_id;
			}
			//retrieve all users
			$this->db->from('account');
			$this->db->select('*');
			$this->db->where('parent_account = '.$account_id.' AND account.account_status = 1');
			$query = $this->db->get();
			
			return $query;    	


		}
		else
		{
			return FALSE;
		}

    }


    public function get_account_ledger_statement($account_id)
    {

    	$account_date_from = $this->session->userdata('account_date_from');
    	$account_date_to = $this->session->userdata('account_date_to');

    	if(!empty($account_date_from) AND !empty($account_date_to))
    	{
    		$add = ' AND v_account_ledger.transactionDate BETWEEN "'.$account_date_from.'"  AND "'.$account_date_to.'"';
    	}
    	else if(!empty($account_date_from) AND empty($account_date_to))
    	{
    		$add = ' AND v_account_ledger.transactionDate = "'.$account_date_from.'"';
    	}
    	else if(empty($account_date_from) AND !empty($account_date_to))
    	{
    		$add = ' AND v_account_ledger.transactionDate = "'.$account_date_to.'"';
    	}
    	else
    	{
    		$add = '';
    	}


    	$this->db->from('v_account_ledger');
		$this->db->select('*');
		$this->db->where('accountId = "'.$account_id.'" '.$add);
		$this->db->order_by('transactionDate','ASC');
		$query = $this->db->get();

		return $query;

    }


    public function export_account_ledger()
    {
    	$this->load->library('excel');
		
		$account = $this->session->userdata('account_id');
		$account_name = $this->session->userdata('account_name');

		$account_date_from = $this->session->userdata('account_date_from');
    	$account_date_to = $this->session->userdata('account_date_to');

    	if(!empty($account_date_from) AND !empty($account_date_to))
    	{
    		$add = ' AND v_account_ledger.transactionDate BETWEEN "'.$account_date_from.'"  AND "'.$account_date_to.'"';
    	}
    	else if(!empty($account_date_from) AND empty($account_date_to))
    	{
    		$add = ' AND v_account_ledger.transactionDate = "'.$account_date_from.'"';
    	}
    	else if(empty($account_date_from) AND !empty($account_date_to))
    	{
    		$add = ' AND v_account_ledger.transactionDate = "'.$account_date_to.'"';
    	}
    	else
    	{
    		$add = '';
    	}


		$this->db->from('v_account_ledger');
		$this->db->select('*');
		$this->db->where('accountId = "'.$account.'" '.$add);
		$this->db->order_by('transactionDate','ASC');
		$visits_query = $this->db->get();

		// var_dump($visits_query); die();
		$search_title = $this->session->userdata('search_title');

		$account_date_from = $this->session->userdata('account_date_from');
	    $account_date_to = $this->session->userdata('account_date_to');
		if(!empty($account_date_from) AND !empty($account_date_to))
		{
			$search_title .= ' FROM PERIOD BETWEEN '.$account_date_from.'  AND '.$account_date_to.'';
		}
		else if(!empty($account_date_from) AND empty($account_date_to))
		{
			$search_title .= ' FOR "'.$account_date_from.'"';
		}
		else if(empty($account_date_from) AND !empty($account_date_to))
		{
			$search_title .= ' FOR "'.$account_date_to.'"';
		}
		else
		{
			$search_title .= '';
		}
		
		$title = $search_title;
		$col_count = 0;
		
		if($visits_query->num_rows() > 0)
		{
			$count = 0;
			/*
				-----------------------------------------------------------------------------------------
				Document Header
				-----------------------------------------------------------------------------------------
			*/

					
			$row_count = 0;
			$report[$row_count][$col_count] = '#';
			$col_count++;
			$report[$row_count][$col_count] = 'Transaction Date';
			$col_count++;
			$report[$row_count][$col_count] = 'Type';
			$col_count++;
			$report[$row_count][$col_count] = 'Description';
			$col_count++;
			$report[$row_count][$col_count] = 'Ref Code';
			$col_count++;
			$report[$row_count][$col_count] = 'Debit';
			$col_count++;
			$report[$row_count][$col_count] = 'Credit';
			$col_count++;
			$report[$row_count][$col_count] = 'Balance';
			$col_count++;
			//display all patient data in the leftmost columns

			$balance = 0;
			$total_dr = 0;
			$total_cr = 0;
			foreach($visits_query->result() as $value)
			{
				$row_count++;
				// $total_invoiced = 0;
				// $visit_date = date('jS M Y',strtotime($row->visit_date));
				// $visit_time = date('H:i a',strtotime($row->visit_time));
				// if($row->visit_time_out != '0000-00-00 00:00:00')
				// {
				// 	$visit_time_out = date('H:i a',strtotime($row->visit_time_out));
				// }
				// else
				// {
				// 	$visit_time_out = '-';
				// }
				
				$transactionId = $value->transactionId;
				$accountName = $value->accountName;
				$transactionDate = $value->transactionDate;
				$dr_amount = $value->dr_amount;
				$cr_amount = $value->cr_amount;
				$transactionDescription = $value->transactionDescription;
				$transactionName = $value->transactionClassification;
				$referenceCode = $value->referenceCode;
				$balance += $dr_amount - $cr_amount;
				$total_dr += $dr_amount;
				$total_cr += $cr_amount;

				//display the patient data
				$report[$row_count][$col_count] = $count;
				$col_count++;
				$report[$row_count][$col_count] = $transactionDate;
				$col_count++;
				$report[$row_count][$col_count] = $transactionName;
				$col_count++;
				$report[$row_count][$col_count] = $transactionDescription;
				$col_count++;
				$report[$row_count][$col_count] = $referenceCode;
				$col_count++;
				$report[$row_count][$col_count] = number_format($dr_amount,2);
				$col_count++;
				$report[$row_count][$col_count] = number_format($cr_amount,2);
				$col_count++;
				$report[$row_count][$col_count] = number_format($balance,2);
				$col_count++;
				
				
				
			}

			$row_count++;
			//display the patient data
			$report[$row_count][$col_count] = '';
			$col_count++;
			$report[$row_count][$col_count] = '';
			$col_count++;
			$report[$row_count][$col_count] = '';
			$col_count++;
			$report[$row_count][$col_count] = '';
			$col_count++;
			$report[$row_count][$col_count] = '';
			$col_count++;
			$report[$row_count][$col_count] = number_format($total_dr,2);
			$col_count++;
			$report[$row_count][$col_count] = number_format($total_cr,2);
			$col_count++;
			$report[$row_count][$col_count] = number_format($balance,2);
			$col_count++;
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	
    }
}

?>