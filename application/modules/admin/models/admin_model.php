<?php

class Admin_model extends CI_Model 
{
	/*
	*	Check if parent has children
	*
	*/
	public function check_children($children, $section_id, $web_name)
	{
		$section_children = array();
		
		if($children->num_rows() > 0)
		{
			foreach($children->result() as $res)
			{
				$parent = $res->section_parent;
				
				if($parent == $section_id)
				{
					$section_name = $res->section_name;
					
					$child_array = array
					(
						'section_name' => $section_name,
						'link' => site_url().$web_name.'/'.strtolower($this->site_model->create_web_name($section_name)),
					);
					
					array_push($section_children, $child_array);
				}
			}
		}
		
		return $section_children;
	}
	
	public function get_breadcrumbs()
	{
		$page = explode("/",uri_string());
		$total = count($page);
		$last = $total - 1;
		$crumbs = '<li><a href="'.site_url().'dashboard"><i class="fa fa-home"></i></a></li>';
		
		for($r = 0; $r < $total; $r++)
		{
			$name = $this->site_model->decode_web_name($page[$r]);
			if($r == $last)
			{
				$crumbs .= '<li><span>'.strtoupper($name).'</span></li>';
			}
			else
			{
				if($total == 3)
				{
					if($r == 1)
					{
						$crumbs .= '<li><a href="'.site_url().$page[$r-1].'/'.strtolower($name).'">'.strtoupper($name).'</a></li>';
					}
					else
					{
						$crumbs .= '<li><a href="'.site_url().strtolower($name).'">'.strtoupper($name).'</a></li>';
					}
				}
				else
				{
					$crumbs .= '<li><a href="'.site_url().strtolower($name).'">'.strtoupper($name).'</a></li>';
				}
			}
		}
		
		return $crumbs;
	}
	
	public function create_breadcrumbs($title)
	{
		$crumbs = '<li><a href="'.site_url().'dashboard"><i class="fa fa-home"></i></a></li>';
		$crumbs .= '<li><span>'.strtoupper($title).'</span></li>';
		
		return $crumbs;
	}
	
	public function get_configuration()
	{
		return $this->db->get('configuration');
	}
	
	public function edit_configuration($configuration_id)
	{
		$data = array(
			'mandrill' => $this->input->post('mandrill'),
			'sms_key' => $this->input->post('sms_key'),
			'sms_user' => $this->input->post('sms_user')
		);
		
		if($configuration_id > 0)
		{
			$this->db->where('configuration_id', $configuration_id);
			if($this->db->update('configuration', $data))
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
			if($this->db->insert('configuration', $data))
			{
				return TRUE;
			}
			
			else
			{
				return FALSE;
			}
		}
	}
	
	public function create_preffix($yourString)
	{
		$vowels = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", " ");
		$yourString = str_replace($vowels, "", $yourString);
		$trimed = substr($yourString, 0, 3);
		$preffix = strtoupper($trimed);
		return $preffix;
	}
	public function get_all_visits_parent_old($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit.*,patients.*');
		$this->db->where($where);
		$this->db->order_by('visit.time_start','ASC');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}



	public function get_all_visits_parent($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		// $this->db->from($table);
		// $this->db->select('visit.*,patients.*');
		// $this->db->where($where);
		// $this->db->order_by('visit.time_start','ASC');

		$query = $this->db->query("SELECT
  visit_date, TIME(STR_TO_DATE(time_start, '%l:%i %p')),patients.*,visit.*
FROM
  visit,patients
where
".$where."
ORDER BY
  STR_TO_DATE(time_start, '%l:%i %p')");
		// $query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	public function check_if_admin($personnel_id)
	{
		$this->db->where('job_title_id = 1 AND personnel_id ='.$personnel_id);
		$query=$this->db->get('personnel_job');
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	public function get_total_unsent_appointments()
	{
		
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
             $date_to_send = 'tomorrow';
        }


        // var_dump($date_tomorrow); die();
		$this->db->select('*');
		$this->db->where('visit.visit_date = "'.$date_tomorrow.'" AND visit.patient_id = patients.patient_id AND visit.visit_delete = 0 AND schedule_id = 0');
		$query = $this->db->get('visit,patients');

		return $query->num_rows();
	}
}
?>