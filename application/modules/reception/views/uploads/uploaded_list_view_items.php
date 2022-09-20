<?php


$post_gallery = $this->reception_model->get_all_patient_scans($patient_id);


?>

<div class="row" style="margin-top:20px;">
    <?php
	$gallery_images = '<table class="table table-bordered table-striped">';
	
	if($post_gallery->num_rows() > 0)
	{
		$count = 0;
		foreach($post_gallery->result() as $value)
		{
			$patient_scan_id = $value->patient_scan_id;
			$document_name = $value->document_name;
			$document_description = $value->document_description;
			$document_name_thumb = $value->document_name_thumb;
			
			$count++;
			$gallery_images .= '<tr>
									<td>'.$count.'</td>
									<td>'.$document_description.'</td>
									<td><a href="'.site_url().'assets/patient_scans/'.$document_name.'" target="_blank"> View File</a></td>
									<td><a class="btn btn-xs btn-danger" onclick="delete_scan_image('.$patient_id.','.$patient_scan_id.')"><i class="fa fa-trash"></i></a></td>
								</tr>
			
				
			';
		}
	}
	else
	{
		$gallery_images .= '<tr>
									<td> No uploaded Files</td>
								</tr>
			
				
			';

	}
	$gallery_images .= '</table>';
	
	echo $gallery_images;
	?>
</div>
