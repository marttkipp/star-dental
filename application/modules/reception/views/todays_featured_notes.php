<?php
if(empty($branch_id))
{

	$query_one = $this->reception_model->get_todays_featured_notes($todays_date,1,null,1,null);
	$notes_one = '';
	// var_dump($query_one); die();
	if($query_one->num_rows() > 0)
	{
		foreach ($query_one->result() as $key => $value) {
			# code...
			$note = $value->note;
			$calendar_note_id = $value->calendar_note_id;
			$notes_one .= '<div class="bg-danger">'.$note.' <a class="pull-right" onclick="delete_note_details('.$calendar_note_id.',1)"><i class="fa fa-trash"></i></a></div>';
		}
	}


	echo $notes_one;

}
else
{
	$query_one = $this->reception_model->get_todays_featured_notes($todays_date,1,null,1,$branch_id);
	$notes_one = '';
	// var_dump($query_one); die();
	if($query_one->num_rows() > 0)
	{
		foreach ($query_one->result() as $key => $value) {
			# code...
			$note = $value->note;
			$calendar_note_id = $value->calendar_note_id;
			$notes_one .= '<div class="bg-danger">'.$note.' <a class="pull-right" onclick="delete_note_details('.$calendar_note_id.',1)"><i class="fa fa-trash"></i></a></div>';
		}
	}
	echo $notes_one;
}

?>
