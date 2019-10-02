<?php

class Dashboard_model extends CI_Model 
{
	/*
	*	Count all items from a table
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function count_items($table, $where, $limit = NULL)
	{
		if($limit != NULL)
		{
			$this->db->limit($limit);
		}
		$this->db->from($table);
		$this->db->where($where);
		return $this->db->count_all_results();
	}
	public function count_items_group($table, $where, $select = NULL ,$cpm_group = NULL,$limit = NULL)
	{
		if($limit != NULL)
		{
			$this->db->limit($limit);
		}
		if($select != NULL)
		{
			$this->db->select($select);
		}
		if($cpm_group != NULL)
		{
			$this->db->group_by($cpm_group);
		}
		
		$this->db->where($where);
		if($select != NULL)
		{
			$query = $this->db->get($table);

			$row = $query->result();

			//var_dump($row); die();
			$number= $row[0]->number;

			if(empty($number)) 
			{
				$number = 0;
			}
			
			

			return $number;
		}
		else
		{
			$this->db->from($table);
			return $this->db->count_all_results();
		}
	}


	public function get_content($table, $where,$select,$group_by=NULL,$limit=NULL)
	{
		$this->db->from($table);
		$this->db->select($select);
		$this->db->where($where);
		if($group_by != NULL)
		{
			$this->db->group_by($group_by);
		}
		$query = $this->db->get('');
		
		return $query;
	}
}
?>