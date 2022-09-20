<?php

class Admin_model extends CI_Model 
{
	public function printTree($args,$counting=0){
		$tree = $args["tree"];
		$level = $args["level"];
		$me = $args["me"];
		// $section_id = $args["section_id"];

		$rs_section = $this->get_section_details($me);
		if($rs_section->num_rows() > 0)
		{

			foreach($rs_section->result() as $res_result)
			{

				$section_parent = $res_result->section_parent;
				$section_id = $res_result->section_id;
				$section_name = $res_result->section_name;
				$section_icon = $res_result->section_icon;
			}
		}

		$parent_name = '';
		if($section_parent > 0)
		{
			$rs_section_two = $this->get_section_details($section_parent);
			if($rs_section_two->num_rows() > 0)
			{
				foreach($rs_section_two->result() as $res_parent)
				{
					$parent_name = $res_parent->section_name;
				}
			}


		}
		
		// $section_parent = $res_result->section_parent;

		if(!empty($parent_name))
			$parent_web_name = strtolower($this->site_model->create_web_name($parent_name));
		else
			$parent_web_name = '';


		$web_name = strtolower($this->site_model->create_web_name($section_name));


		$my_string = "";
		$count = count($tree[$me]["children"]);
		if($count == 0)

			if(!empty($parent_name))
					$my_string .= '<li ><a href="'.site_url().$parent_web_name.'/'.$web_name.'" data-hover="'.$tree[$me]["name"].'"> 
																<i class="fa fa-'.$section_icon.'" aria-hidden="true"></i>
																<span>'.$tree[$me]["name"].'</span>
																</a>
													</li>';
			else
					$my_string .= '<li ><a href="'.site_url().$web_name.'" data-hover="'.$tree[$me]["name"].'"> 
															<i class="fa fa-'.$section_icon.'" aria-hidden="true"></i>
															<span>'.$tree[$me]["name"].'</span>
															</a>
												</li>';
		else{
			$my_string .= '<li class="dropdown">';
			$my_string .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="'.$tree[$me]["name"].'"><i class="fa fa-'.$section_icon.'" aria-hidden="true"></i> '.$tree[$me]["name"].' <span data-hover="'.$tree[$me]["name"].'"></span>
												<b class="caret"></b>
										</a>';

			$my_string .= '<ul class="submenu dropdown-menu">';
			
			foreach($tree[$me]["children"] as $child){
				if(array_key_exists($child, $tree))
					$my_string .= $this->printTree(array("tree" => $tree, "me" => $child, "level" => "$level - "),1);
				}

				$my_string .= '</ul>';
			$my_string .= "</li>";

		}
		
		 return $my_string;
	}
	
	public function get_section_details($section_id)
	{
		$this->db->where('section_id',$section_id);
		$query = $this->db->get('section');

		return $query;
	}
	public function printTree_old($args){
		$tree = $args["tree"];
		$level = $args["level"];
		$me = $args["me"];

		//if(is_int($me) and is_array($tree) and array_key_exists($me, $tree) and array_key_exists("name", $tree[$me]) and array_key_exists("children",$tree[$me])){
			echo "<br>$level: " . $tree[$me]["name"];
			foreach($tree[$me]["children"] as $child)
				if(array_key_exists($child, $tree))
					$this->printTree_old(array("tree" => $tree, "me" => $child, "level" => "$level - "));
		//}
	}
	function printArray($arr){
			echo "<pre>";
			echo json_encode($arr, JSON_PRETTY_PRINT);
			echo "</pre>";

	}
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
				$section_idd = $res->section_id;
				
				if($parent == $section_id)
				{
					$section_name = $res->section_name;
					
					$child_array = array
					(
						'section_name' => $section_name,
						'section_id' => $section_idd,
						'link' => site_url().$web_name.'/'.strtolower($this->site_model->create_web_name($section_name)),
					);
					
					array_push($section_children, $child_array);
				}
			}
		}
		
		return $section_children;
	}


	public function check_children_new($childrens, $section_id, $web_name)
	{
		// $section_children = array();
		$section_children = $this->section_children;
		$this->db->where('section_status = 1 AND section_parent = '.$section_id);
		$children = $this->db->get('section');

		if($children->num_rows() > 0)
		{

			foreach($children->result() as $res)
			{
				$parent = $res->section_parent;
				$section_name = $res->section_name;
				$section_idd = $res->section_id;
				
				if($parent == $section_id)
				{
					$section_name = $res->section_name;
					
					$child_array = array
					(
						'section_name' => $section_name,
						'section_id' => $section_idd,
						'link' => site_url().$web_name.'/'.strtolower($this->site_model->create_web_name($section_name)),
					);
					
					array_push($section_children, $child_array);
				}
				$web_name = 'name';//strtolower($this->site_model->create_web_name($section_name));
				$link = site_url().$web_name;
				$this->check_children_new($children, $section_idd, $web_name);

				// var_dump($section_children);die();
			}
		}
		
		return $section_children;
	}
	
	/*
	*	Check if parent has children
	*
	*/
	public function check_children_older($children, $section_id, $web_name)
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
  visit_date, TIME(STR_TO_DATE(appointment_start_time, '%l:%i %p')),patients.*,visit.*,appointments.*
FROM
  visit,patients,appointments
where
".$where."
ORDER BY
  appointments.appointment_date,appointment_start_time ASC");
		// $query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	public function get_all_visits_today($table, $where, $per_page, $page, $order = NULL)
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

	public function get_time_reports($personnel_id)
	{

		// var_dump($date_tomorrow); die();
		$this->db->select('*');
		$this->db->where('MONTH(sign_time_in) = "'.date('m').'" AND personnel_id = '.$personnel_id);
		$query = $this->db->get('personnel_shift');
		return $query;

	}

	public function get_days_schedule($personnel_id)
	{
		$this->db->where('DATE(sign_time_in) = "'.date('Y-m-d').'" AND personnel_id = '.$personnel_id);
		$this->db->order_by('shift_id','DESC');
		$this->db->limit(1);
		$query_old=$this->db->get('personnel_shift');

		return $query_old;
	}
}
?>