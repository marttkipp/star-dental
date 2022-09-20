<?php

class Sections_model extends CI_Model 
{	
	/*
	*	Retrieve all sections
	*
	*/
	public function all_sections()
	{
		$this->db->where('section_status = 1');
		$query = $this->db->get('section');
		
		return $query;
	}
	
	/*
	*	Retrieve all parent sections
	*
	*/
	public function all_parent_sections($order = 'section_name')
	{
		$this->db->where('section_status = 1');
		$this->db->order_by($order, 'ASC');
		$query = $this->db->get('section');
		
		return $query;
	}



	public function all_parent_sections_list($order = 'section_name')
	{
		$select_sql = "SELECT
						* 

						FROM 
						(

							SELECT 
							1 AS part, section.* 
							
							FROM 
								section
							WHERE
								section.section_status = 1
							
									
							UNION ALL

							SELECT
								2 AS part, s2.* 
							FROM
								section s1
								INNER JOIN section s2 ON s2.section_sequence LIKE CONCAT( s1.section_sequence, '.%' ) 
							WHERE
								s1.section_status = 1 
								AND s2.section_parent > 0 
								AND s2.section_status = 1 
								
							-- SELECT 

							-- section.* 
							
							-- FROM 
							-- 	section
							-- WHERE
							-- 	section.section_status = 1
								-- AND section.section_parent = 0
									
						) AS data ";


		$query = $this->db->query($select_sql);

		return $query;
	}

	public function all_parent_sections_new($order = 'section_parent')
	{
		$this->db->where('section_status = 1');
		// $this->db->order_by($order, 'ASC');
		$this->db->order_by('section_parent','ASC');
		$query = $this->db->get('section');
		
		return $query;
	}
	/*
	*	Retrieve all children sections
	*
	*/
	public function all_child_sections($order = 'section_position')
	{
		$this->db->where('section_status = 1 ');
		$this->db->order_by($order, 'ASC');
		$query = $this->db->get('section');
		
		return $query;
	}
	
	/*
	*	Retrieve all sections
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_sections($table, $where, $per_page, $page, $order = 'section_name', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Add a new section
	*	@param string $image_name
	*
	*/
	public function add_section()
	{
		$data = array(
				'section_name'=>ucwords(strtolower($this->input->post('section_name'))),
				'section_parent'=>$this->input->post('section_id'),
				'section_position'=>$this->input->post('section_position'),
				'section_status'=>$this->input->post('section_status'),
				'section_icon'=>$this->input->post('section_icon'),
				'created'=>date('Y-m-d H:i:s'),
				'created_by'=>$this->session->userdata('personnel_id'),
				'modified_by'=>$this->session->userdata('personnel_id')
			);
			
		if($this->db->insert('section', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Update an existing section
	*	@param string $image_name
	*	@param int $section_id
	*
	*/
	public function update_section($section_id)
	{

		$data = array(
				'section_name'=>ucwords(strtolower($this->input->post('section_name'))),
				'section_parent'=>$this->input->post('section_id'),
				'section_position'=>$this->input->post('section_position'),
				'section_status'=>$this->input->post('section_status'),
				'section_icon'=>$this->input->post('section_icon'),
				'modified_by'=>$this->session->userdata('personnel_id')
			);
			
		$this->db->where('section_id', $section_id);
		if($this->db->update('section', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	get a single section's children
	*	@param int $section_id
	*
	*/
	public function get_sub_sections($section_id)
	{
		//retrieve all users
		$this->db->from('section');
		$this->db->select('*');
		$this->db->where('section_parent = '.$section_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	get a single section's details
	*	@param int $section_id
	*
	*/
	public function get_section($section_id)
	{
		//retrieve all users
		$this->db->from('section');
		$this->db->select('*');
		$this->db->where('section_id = '.$section_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Delete an existing section
	*	@param int $section_id
	*
	*/
	public function delete_section($section_id)
	{
		//delete children
		if($this->db->delete('section', array('section_parent' => $section_id)))
		{
			//delete parent
			if($this->db->delete('section', array('section_id' => $section_id)))
			{
				return TRUE;
			}
			else{
				return FALSE;
			}
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Activate a deactivated section
	*	@param int $section_id
	*
	*/
	public function activate_section($section_id)
	{
		$data = array(
				'section_status' => 1
			);
		$this->db->where('section_id', $section_id);
		

		if($this->db->update('section', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated section
	*	@param int $section_id
	*
	*/
	public function deactivate_section($section_id)
	{
		$data = array(
				'section_status' => 0
			);
		$this->db->where('section_id', $section_id);
		
		if($this->db->update('section', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	public function get_personnel_roles($personnel_id)
	{
		$select_sql = "SELECT
						* 

						FROM 
						(

							SELECT 
							1 AS part, section.* 
							
							FROM 
								section,personnel_section
							WHERE
								section.section_status = 1
								AND section.section_id = personnel_section.section_id 
								AND personnel_section.personnel_id = $personnel_id
									
							UNION ALL
							
							
							SELECT
								2 AS part, s2.* 
							FROM
								section s1
								INNER JOIN section s2 ON s2.section_sequence LIKE CONCAT( s1.section_sequence, '.%' ) 
								INNER JOIN personnel_section ON  s1.section_id = personnel_section.section_id 
							WHERE
								s1.section_status = 1 
								AND s2.section_parent > 0 
								AND s2.section_status = 1 
								
								AND personnel_section.personnel_id = $personnel_id


									
									
									
									
						) AS data ";

		// echo $select_sql;die();			
		$query = $this->db->query($select_sql);

		return $query;
	}

	public function printTree_old($args){
		$tree = $args["tree"];
		$level = $args["level"];
		$me = $args["me"];

		$rs_section = $this->admin_model->get_section_details($me);
		if($rs_section->num_rows() > 0)
		{

			foreach($rs_section->result() as $res_result)
			{

				$section_parent = $res_result->section_parent;
				$section_id = $res_result->section_id;
				$section_name = $res_result->section_name;
				$section_icon = $res_result->section_icon;


				$section_id = $res_result->section_id;
				$section_position = $res_result->section_position;
				$section_name = $res_result->section_name;
				$parent = $res_result->section_parent;
				$section_status = $res_result->section_status;
				$created_by = $res_result->created_by;
				$modified_by = $res_result->modified_by;
				$icon = $res_result->section_icon;
				$section_sequence = $res_result->section_sequence;
				
				if($section_parent == 0)
				{
					$class="warning";
				}
				else
				{
					$class = '';
				}
				//status
				if($section_status == 1)
				{
					$status = 'Active';
				}
				else
				{
					$status = 'Disabled';
				}
			
				
			
				
				//create deactivated status display
				if($section_status == 0)
				{
					$status = '<span class="label label-important">Deactivated</span>';
					$button = '<a class="btn btn-info" href="'.site_url().'administration/activate-section/'.$section_id.'" onclick="return confirm(\'Do you want to activate '.$section_name.'?\');" title="Activate '.$section_name.'"><i class="fa fa-thumbs-up"></i></a>';
				}
				//create activated status display
				else if($section_status == 1)
				{
					$status = '<span class="label label-success">Active</span>';
					$button = '<a class="btn btn-default" href="'.site_url().'administration/deactivate-section/'.$section_id.'" onclick="return confirm(\'Do you want to deactivate '.$section_name.'?\');" title="Deactivate '.$section_name.'"><i class="fa fa-thumbs-down"></i></a>';
				}
			}
		}

		//if(is_int($me) and is_array($tree) and array_key_exists($me, $tree) and array_key_exists("name", $tree[$me]) and array_key_exists("children",$tree[$me])){
			echo "<tr class='".$class."'>
						<td> ".$section_sequence." </td>
						<td> $level: " . $tree[$me]["name"].' </td>
						<td>'.date('jS M Y H:i a',strtotime($res_result->created)).'</td>
						<td>'.date('jS M Y H:i a',strtotime($res_result->last_modified)).'</td>
						<td><input type="number" class="form-control" id="section_position'.$section_id.'" value="'.$section_position.'" onkeyup="update_section_position('.$section_id.')" size="3"></td>

						<td>
							
							<!-- Button to trigger modal -->
							<a href="#user'.$section_id.'" class="btn btn-primary" data-toggle="modal" title="Expand '.$section_name.'"><i class="fa fa-plus"></i></a>
							
							<!-- Modal -->
							<div id="user'.$section_id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
											<h4 class="modal-title">'.$section_name.'</h4>
										</div>
										
										<div class="modal-body">
											<table class="table table-stripped table-condensed table-hover">
												<tr>
													<th>Section name</th>
													<td>'.$section_name.'</td>
												</tr>
												<tr>
													<th>Section parent</th>
													<td>'.$section_parent.'</td>
												</tr>
												<tr>
													<th>Status</th>
													<td>'.$status.'</td>
												</tr>
												
												<tr>
													<th>Created by</th>
													<td>'.$created_by.'</td>
												</tr>
												
												<tr>
													<th>Modified by</th>
													<td>'.$modified_by.'</td>
												</tr>
												<tr>
													<th>Section icon</th>
													<td><i class="fa fa-'.$icon.' fa-3x"></i></td>
												</tr>
											</table>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
											<a href="'.site_url().'administration/edit-section/'.$section_id.'" class="btn btn-sm btn-success" title="Edit '.$section_name.'"><i class="fa fa-pencil"></i></a>
											'.$button.'
											<a href="'.site_url().'administration/delete-section/'.$section_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$section_name.'?\');" title="Delete '.$section_name.'"><i class="fa fa-trash"></i></a>
										</div>
									</div>
								</div>
							</div>
						
						</td>
						<td><a href="'.site_url().'administration/edit-section/'.$section_id.'" class="btn btn-sm btn-success" title="Edit '.$section_name.'"><i class="fa fa-pencil"></i></a></td>
						<td>'.$button.'</td>
						<td><a href="'.site_url().'administration/delete-section/'.$section_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$section_name.'?\');" title="Delete '.$section_name.'"><i class="fa fa-trash"></i></a></td>
					</tr>';
			foreach($tree[$me]["children"] as $child)
				if(array_key_exists($child, $tree))
					$this->printTree_old(array("tree" => $tree, "me" => $child, "level" => "$level - "));
		//}
	}

	public function cboMenu ($selected_id=NULL)
	{
		$select = "SELECT section_id, section_parent, section_sequence, section_name
				FROM section
				WHERE section_status = 1
				ORDER BY section_parent, section_position";
		$query = $this->db->query($select);
		$this->arrMenu = array("parents" => array(),
			"children" => array());
		foreach($query->result() AS $key => $value){ //Looping throught the sQL
			$section_id = $value->section_id;
			$section_parent = $value->section_parent;
			$section_sequence = $value->section_sequence;
			$section_name = $value->section_name;

			$num_dots = substr_count($section_sequence, ".");
			for($i = 0; $i<$num_dots; $i++)
				$section_name = " - " . $section_name;

			if(!array_key_exists($section_parent, $this->arrMenu["parents"]))
				$this->arrMenu["parents"][$section_parent] = array();
			array_push($this->arrMenu["parents"][$section_parent],$section_id);
			$this->arrMenu["children"][$section_id] = $section_name;

		}
		// var_dump($this->arrMenu);die();
		// echo "<pre>Printing arrmenu";
		// echo json_encode($this->arrMenu, JSON_PRETTY_PRINT);
		// echo "</pre>";

		$strMenu = "<select name='section_id' class='form-control'>";

		if(empty($selected_id))
		{
			$strMenu .= "<option value='0'>--- Parent Account ---- </option>";
		}
		foreach($this->arrMenu["parents"][0] as $cainandabel)
			$strMenu = $this->strMenu($strMenu, $cainandabel,$selected_id); 
			if(!empty($selected_id))
			{
				$strMenu .= "<option value='0'>--- Parent Account ---- </option>";
			}
		$strMenu .= "</select>";
		unset($this->arrMenu);

		return $strMenu;
	}

	public function strMenu($strMenu, $index,$selected_id=null){
		// echo "<br>Checking for $index: $strMenu";

		if(is_array($this->arrMenu) and array_key_exists($index, $this->arrMenu["children"])){
			// echo "<br>Loaidng the child $index";
				if($selected_id == $index)
					$strMenu .= "<option value='$index' selected>".$this->arrMenu["children"][$index]."</option>";
				else
					$strMenu .= "<option value='$index'>".$this->arrMenu["children"][$index]."</option>";
				if(array_key_exists($index, $this->arrMenu["parents"]))
					foreach($this->arrMenu["parents"][$index] as $mychild)
						$strMenu = $this->strMenu($strMenu, $mychild,$selected_id);
			}
			// else
				// echo "<br>It is not an array. Index $index";

			return $strMenu;
			
	}


	public function update_sequences($args)
	{
		
		$tree = $args["tree"];
		$level = $args["level"];
		$me = $args["me"];

		
			echo "<br>$level: " . $tree[$me]["name"];
			foreach($tree[$me]["children"] as $child)
				if(array_key_exists($child, $tree))
					$this->update_sequences(array("tree" => $tree, "me" => $child, "level" => "$level - "));
	
		
	}


	public function update_sections_sequence()
	{

		//  run sequence update
		$array_seq['section_sequence'] = NULL;
		$this->db->update('section',$array_seq);

		// update all parent sequence
		$this->db->where('section_id > 0 AND section_sequence IS NULL AND section_parent = 0');
		$this->db->order_by('section.section_position','ASC');
		$query_sequence = $this->db->get('section');


		if($query_sequence->num_rows() > 0)
		{
			foreach ($query_sequence->result() as $key => $value_parent) {
				// code...
				$section_id = $value_parent->section_id;
				$section_parent_seq = $value_parent->section_sequence;
				$section_parent = $value_parent->section_parent;
				$section_position = $value_parent->section_position;
				$section_sequence = $value_parent->section_sequence;

				$sequ_one = $section_position;
				
				$section_update_new['section_sequence'] = $sequ_one;
				
				$this->db->where('section_id',$section_id);
				$this->db->update('section',$section_update_new);

			}
		}


		//  update all children sections

		$this->db->where('section_id > 0 AND section_sequence IS NULL AND section_parent > 0');
		$this->db->order_by('section.section_parent','ASC');
		$query = $this->db->get('section');


		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				// code...
				$section_id = $value->section_id;
				$section_parent_seq = $value->section_sequence;
				$section_parent = $value->section_parent;
				$section_position = $value->section_position;
				$section_sequence = $value->section_sequence;

				

					if($section_parent > 0 )
					{

						$this->db->select('section_sequence');
						$this->db->where('section_id',$section_parent);
						$query_three = $this->db->get('section');

						if($query_three->num_rows() > 0)
						{
							foreach ($query_three->result() as $key => $value_two) {
								// code...
								$section_sequence_one = $value_two->section_sequence;
							}
						}
						else
						{
							$number = 1;
						}
						$sequ_one = $section_sequence_one.'.'.$section_position;
					}
					else
					{
						
						$sequ_one = $section_position;
					}
					
					
					$section_update_new['section_sequence'] = $sequ_one;
					
					$this->db->where('section_id',$section_id);
					$this->db->update('section',$section_update_new);
				
				



			}
		}
		
	}

	public function get_all_orphans($arrBranches)
	{
		$sql = "SELECT tparents.section_sequence AS parent_sequence, tparents.section_id AS parent_id,tparents.section_name AS parent_name, tparents.section_parent AS grandparent,
					 tchildren.section_sequence AS child_sequence, tchildren.section_id as child_id, tchildren.section_name as child_name,tparents.section_icon
		 		FROM section tparents
		  		INNER JOIN section tchildren ON tchildren.section_parent = tparents.section_id
		   		WHERE tparents.section_sequence in ('".implode("','", $arrBranches)."') 
		   		AND tparents.section_status = 1  ORDER BY tparents.section_id ASC";

		$queryOrphans = $this->db->query($sql);

		return $queryOrphans;
	}

}
?>