<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

error_reporting(E_ALL);
class S3  extends MX_Controller
{
	var $document_upload_path;

	var $document_upload_location;

		
	function __construct()
	{
		parent:: __construct();

		$this->load->library('image_lib');

		$this->document_upload_path = realpath(APPPATH . '../assets');
		$this->load->model('s3_model');
	}



	public function upload_existing_file_to_digital_ocean_s3()
	{
		// we need an object of the tables we are going to use and the field that has current file
		$tables = array(

			// 'personnel_document_uploads' => array(
			// 	'field' => 'document_upload_name',
			// 	'path' => 'document_uploads',
			// 	'field_id' => 'document_upload_id',
			// 	'loc' => 'personnel_documents',
			// 	'file_type' => ''
			// ),
			// 'payroll' => array(
			// 	'field' => 'file_data',
			// 	'path' => 'payroll',
			// 	'field_id' => 'payroll_id',
			// 	'loc' => 'payroll',
			// 	'file_type' => 'txt'
			// ),
			// 'personnel' => array(
			// 	'field' => 'image',
			// 	'path' => 'personnel',
			// 	'field_id' => 'personnel_id',
			// 	'loc' => 'personnel',
			// 	'file_type' => ''
			// ),
			// 'patient_document_uploads' => array(
			// 	'field' => 'document_upload_name',
			// 	'path' => 'document_uploads',
			// 	'field_id' => 'document_upload_id',
			// 	'loc' => 'scans',
			// 	'file_type' => ''
			// ),
			'branch' => array(
				'field' => 'branch_image_name',
				'path' => 'logo',
				'field_id' => 'branch_id',
				'loc' => 'logo',
				'file_type' => ''
			),
		);
		// var_dump($tables);die();
		// loop through the tables
		foreach($tables as $table => $data)
		{
			// var_dump($data['field']);
			// get the data
			var_dump('Uploading files for '.$table);
			$field_name = $data['field'];
			$field_id = $data['field_id'];
			$loc = $data['loc'];
			$extension_name = $data['file_type'] ? '.'.$data['file_type'].'' : '';
			$this->db->where($data['field'].' IS NOT NULL OR '.$data['field'].' <> 0');
			
			$id = $field_id;//$table.'_id';
			// get only the field that has the file
			if($loc == 'scans'){
				$this->db->limit(1000);
				$query = $this->db->select([$id, $data['field'],'patient_id'])->get($table);
			}
			else{
				$query = $this->db->select([$id, $data['field']])->get($table);
			}


			// var_dump($query->num_rows());die();
			if($query->num_rows() > 0)
			{
				foreach($query->result() as $key => $value)
				{
					$file_id = $value->$id;

					$patient_id = '';
					if($loc == 'scans')
						$patient_id = $value->patient_id;

					var_dump($patient_id);die();
					if ($value->$field_name == '')
					{
						var_dump('No file to upload');
						continue;
					}
					if ($this->check_if_digital_ocean_link($value->$field_name))
					{
						var_dump($value->$field_name);
						var_dump('File already uploaded');
						continue;
					}
					$concatenated_name = $value->$field_name.$extension_name;
					$path = $data['path'].'/'.$concatenated_name;
					$full_path = $this->document_upload_path.'/'.$path;
					// var_dump($full_path);die();
					// check if the file exists
					if (!file_exists($full_path))
					{
						var_dump('File does not exist');
						continue;
					}
					
					$response = $this->s3_model->upload_file_to_digital_ocean_using_path($full_path, $loc, $concatenated_name,$patient_id);
					// var dump uploading file to digital ocean
					var_dump('Uploading '. $concatenated_name .' to digital ocean ... ');
					var_dump($response);
					if ($response['check'])
					{
						// update the table with the new file name
						$update_data = array(
							$field_name => $response['download_url']
						);
						$this->db->where($id, $file_id);
						$this->db->update($table, $update_data);
						var_dump('File uploaded successfully');
					}
					else
					{
						var_dump('Error uploading file');
					}
					// die();
				}
			}
		}
	}

	public function check_if_digital_ocean_link($string)
	{
		$url = trim($string);
		$protocol = 'https://';

		if (substr($url, 0, strlen($protocol)) === $protocol)
		{
			return true;
		}
		return false;
	}


}
?>