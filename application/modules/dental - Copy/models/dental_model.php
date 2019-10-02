<?php
class Dental_model extends CI_Model 
{
	function submitvisitbilling($procedure_id,$visit_id,$suck){
		$visit_data = array('procedure_id'=>$procedure_id,'visit_id'=>$visit_id,'units'=>$suck);
		$this->db->insert('visit_procedure', $visit_data);
	}

	 function get_payment_info($visit_id)
	{
		$table = "visit";
		$where = "visit_id = '$visit_id'";
		$items = "payment_info,sick_leave_note,sick_leave_start_date,sick_leave_days";
		$order = "visit_id";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	function get_rejection_info($visit_id)
	{
		$table = "visit";
		$where = "visit_id = '$visit_id'";
		$items = "*";
		$order = "visit_id";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}

	function get_visit_rejected_updates_sum($visit_id,$visit_type_id)
	{
		$table = "visit_bill,visit";
		$where = "visit_parent = '$visit_id' AND  visit.visit_id = visit_bill.visit_id AND visit.visit_delete = 0  ";
		$items = "SUM(visit_bill_amount) AS total_rejected";
		$order = "visit.visit_id";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}


	function get_current_visit_rejected_updates_sum($visit_id,$visit_type_id)
	{
		$table = "visit_bill,visit";
		$where = "visit_bill.visit_id = '$visit_id' AND  visit.visit_id = visit_bill.visit_id AND visit.visit_delete = 0 ";
		$items = "SUM(visit_bill_amount) AS total_rejected";
		$order = "visit.visit_id";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}


	function get_visit_rejected_updates($visit_id)
	{
		$table = "visit,visit_bill,visit_type";
		$where = "visit_parent = '$visit_id' AND visit.visit_delete = 0 AND visit.visit_id = visit_bill.visit_parent AND visit_type.visit_type_id = visit_bill.visit_type_id";
		$items = "*";
		$order = "visit.visit_id";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}

	
	public function all_document_types()
	{
		$this->db->order_by('document_type_name');
		$query = $this->db->get('document_type');
		
		return $query;
	}
	
	public function upload_any_file($path, $location, $name, $upload, $edit = NULL)
	{
		if(!empty($_FILES[$upload]['tmp_name']))
		{
			$image = $this->session->userdata($name);
			
			if((!empty($image)) || ($edit != NULL))
			{
				if($edit != NULL)
				{
					$image = $edit;
				}
				
				//delete any other uploaded image
				if($this->file_model->delete_file($path."\\".$image, $location))
				{
					//delete any other uploaded thumbnail
					$this->file_model->delete_file($path."\\thumbnail_".$image, $location);
				}
				
				else
				{
					$this->file_model->delete_file($path."/".$image, $location);
					$this->file_model->delete_file($path."/thumbnail_".$image, $location);
				}
			}
			//Upload image
			$response = $this->file_model->upload_any_file($path, $upload);
			if($response['check'])
			{
				$file_name = $response['file_name'];
					
				//Set sessions for the image details
				$this->session->set_userdata($name, $file_name);
			
				return TRUE;
			}
		
			else
			{
				$this->session->set_userdata('upload_error_message', $response['error']);
				
				return FALSE;
			}
		}
		
		else
		{
			$this->session->set_userdata('upload_error_message', '');
			return FALSE;
		}
	}

	function upload_personnel_documents($patient_id, $document)
	{
		$data = array(
			'document_type_id'=> $this->input->post('document_type_id'),
			'document_name'=> $this->input->post('document_item_name'),
			'document_upload_name'=> $document,
			'created_by'=> $this->session->userdata('personnel_id'),
			'modified_by'=> $this->session->userdata('personnel_id'),
			'created'=> date('Y-m-d H:i:s'),
			'patient_id'=>$patient_id
		);
		
		if($this->db->insert('patient_document_uploads', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	function get_document_uploads($patient_id)
	{
		$this->db->from('patient_document_uploads, document_type');
		$this->db->select('*');
		$this->db->where('patient_document_uploads.document_type_id = document_type.document_type_id AND patient_id = '.$patient_id);
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Delete an existing personnel
	*	@param int $personnel_id
	*
	*/
	public function delete_document_scan($document_upload_id)
	{
		//delete parent
		if($this->db->delete('patient_document_uploads', array('document_upload_id' => $document_upload_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	public function get_dentine_item($patient_id,$teeth_id)
	{
		# code...
		$this->db->where('patient_id = '.$patient_id.' AND teeth_id = '.$teeth_id);
		$query = $this->db->get('dentine');		
		return $query;
	}
	public function get_dentine_value($patient_id,$teeth_id)
	{
		# code...
		$this->db->where('patient_id = '.$patient_id.' AND teeth_id = '.$teeth_id);
		$query = $this->db->get('dentine');		
		
		$cavity_status = 0;
		$query = $this->dental_model->get_dentine_item($patient_id,$teeth_id);
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$cavity_status = $value->cavity_status;
			}
		}


		if($cavity_status == 1)
		{
			$one = 'O';
		}
		else if($cavity_status == 2)
		{
			$one = 'P';
		}
		else if($cavity_status == 3)
		{
			$one = '<span>&#x25cf;</span>';
		}
		else if($cavity_status == 4)
		{
			$one = '/';
		}
		else if($cavity_status == 5)
		{
			$one = '--';
		}
		else if($cavity_status == 6)
		{
			$one = 'C';
		}
		else if($cavity_status == 7)
		{
			$one = 'X';
		}else
		{
			$one = '';
		}

		return $one;
	}

	public function get_visit_lab_work($v_id){
		$table = "visit_lab_work";
		$where = "visit_id = $v_id";
		$items = "*";
		$order = "visit_id";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		return $result;
	}

	public function get_patient_waivers($patient_id){
		
		$this->db->where('visit.visit_id = payments.visit_id AND payments.cancel = 0 AND payments.payment_type = 2 AND visit.patient_id = '.$patient_id);
		$query = $this->db->get('visit,payments');

		return $query;

	}
}
?>