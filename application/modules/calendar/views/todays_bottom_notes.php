<?php
$query_one = $this->reception_model->get_todays_calendar_notes($todays_date,2,'a',0,$branch_id);
$notes_one = '';
// var_dump($query_one); die();
if($query_one->num_rows() > 0)
{
	foreach ($query_one->result() as $key => $value) {
		# code...
		$note = $value->note;
		$calendar_note_id = $value->calendar_note_id;
		$notes_one .= '<tr>
						<td><span onclick="get_note_details('.$calendar_note_id.')"> '.$note.'</span>  <a class="pull-right" onclick="delete_note_details('.$calendar_note_id.',1)"><i class="fa fa-trash"></i></a></td>
					  </tr>';
	}
}


$query_one_patients = $this->reception_model->get_todays_rescheduled_patients($todays_date,2,'a',0,$branch_id);

// var_dump($query_one); die();
if($query_one_patients->num_rows() > 0)
{
	foreach ($query_one_patients->result() as $key => $value) {
		# code...
		$event_name = $value->event_name;
		$event_description = $value->event_description;
		$appointment_id = $value->appointment_id;
		$notes_one .= '<tr>
							<td><span> '.$event_name.' '.$event_description.'</span>  <a class="pull-right" onclick="delete_event_details('.$appointment_id.',1)"><i class="fa fa-trash"></i></a>
							</td>
					  	</tr>';
	}
}


$query_two = $this->reception_model->get_todays_calendar_notes($todays_date,2,'b',0,$branch_id);
$notes_two = '';
// var_dump($query_two); die();
if($query_two->num_rows() > 0)
{
	foreach ($query_two->result() as $key => $value) {
		# code...
		$note = $value->note;
		$calendar_note_id = $value->calendar_note_id;
		$notes_two .= '<tr>
						<td><span onclick="get_note_details('.$calendar_note_id.')"> '.$note.'</span> <a class="pull-right" onclick="delete_note_details('.$calendar_note_id.',1)"><i class="fa fa-trash"></i></a></td>
					  </tr>';
	}
}

$query_two_patients = $this->reception_model->get_todays_rescheduled_patients($todays_date,2,'b',0,$branch_id);

// var_dump($query_two_patients); die();
if($query_two_patients->num_rows() > 0)
{
	foreach ($query_two_patients->result() as $key => $value) {
		# code...
		$event_name = $value->event_name;
		$event_description = $value->event_description;
		$appointment_id = $value->appointment_id;
		$notes_two .= '<tr>
							<td><span> '.$event_name.' '.$event_description.'</span>  <a class="pull-right" onclick="delete_event_details('.$appointment_id.',1)"><i class="fa fa-trash"></i></a>
							</td>
					  	</tr>';
	}
}

$query_three = $this->reception_model->get_todays_calendar_notes($todays_date,2,'c',0,$branch_id);
$notes_three = '';
// var_dump($query_three); die();
if($query_three->num_rows() > 0)
{
	foreach ($query_three->result() as $key => $value) {
		# code...
		$note = $value->note;
		$calendar_note_id = $value->calendar_note_id;
		$notes_three .= '<tr>
						<td><span onclick="get_note_details('.$calendar_note_id.')"> '.$note.'</span>  <a class="pull-right" onclick="delete_note_details('.$calendar_note_id.',1)"><i class="fa fa-trash"></i></a></td>
					  </tr>';
	}
}



$query_three_patients = $this->reception_model->get_todays_rescheduled_patients($todays_date,2,'c',0,$branch_id);

// var_dump($query_three); die();
if($query_three_patients->num_rows() > 0)
{
	foreach ($query_three_patients->result() as $key => $value) {
		# code...
		$event_name = $value->event_name;
		$event_description = $value->event_description;
		$appointment_id = $value->appointment_id;
		$notes_three .= '<tr>
							<td><span> '.$event_name.' '.$event_description.'</span>  <a class="pull-right" onclick="delete_event_details('.$appointment_id.',1)"><i class="fa fa-trash"></i></a>
							</td>
					  	</tr>';
	}
}




$query_four = $this->reception_model->get_todays_calendar_notes($todays_date,2,'d',0,$branch_id);
$notes_four = '';
// var_dump($query_four); die();
if($query_four->num_rows() > 0)
{
	foreach ($query_four->result() as $key => $value) {
		# code...
		$note = $value->note;
		$calendar_note_id = $value->calendar_note_id;
		$notes_four .= '<tr>
						<td><span onclick="get_note_details('.$calendar_note_id.')"> '.$note.'</span>  <a class="pull-right" onclick="delete_note_details('.$calendar_note_id.',1)"><i class="fa fa-trash"></i></a></td>
					  </tr>';
	}
}



$query_four_patients = $this->reception_model->get_todays_rescheduled_patients($todays_date,2,'d',0,$branch_id);

// var_dump($query_four); die();
if($query_four_patients->num_rows() > 0)
{
	foreach ($query_four_patients->result() as $key => $value) {
		# code...
		$event_name = $value->event_name;
		$event_description = $value->event_description;
		$appointment_id = $value->appointment_id;
		$notes_four .= '<tr>
							<td><span> '.$event_name.' '.$event_description.'</span>  <a class="pull-right" onclick="delete_event_details('.$appointment_id.',1)"><i class="fa fa-trash"></i></a>
							</td>
					  	</tr>';
	}
}





$query_five = $this->reception_model->get_todays_calendar_notes($todays_date,2,'e',0,$branch_id);
$notes_five = '';
// var_dump($query_five); die();
if($query_five->num_rows() > 0)
{
	foreach ($query_five->result() as $key => $value) {
		# code...
		$note = $value->note;
		$calendar_note_id = $value->calendar_note_id;
		$notes_five .= '<tr>
						<td><span onclick="get_note_details('.$calendar_note_id.')"> '.$note.'</span>  <a class="pull-right" onclick="delete_note_details('.$calendar_note_id.',1)"><i class="fa fa-trash"></i></a></td>
					  </tr>';
	}
}


$query_five_patients = $this->reception_model->get_todays_rescheduled_patients($todays_date,2,'e',0,$branch_id);

// var_dump($query_five); die();
if($query_five_patients->num_rows() > 0)
{
	foreach ($query_five_patients->result() as $key => $value) {
		# code...
		$event_name = $value->event_name;
		$event_description = $value->event_description;
		$appointment_id = $value->appointment_id;
		$notes_five .= '<tr>
							<td><span> '.$event_name.' '.$event_description.'</span>  <a class="pull-right" onclick="delete_event_details('.$appointment_id.',1)"><i class="fa fa-trash"></i></a>
							</td>
					  	</tr>';
	}
}



$query_six = $this->reception_model->get_todays_calendar_notes($todays_date,2,'f',0,$branch_id);
$notes_six = '';
// var_dump($query_six); die();
if($query_six->num_rows() > 0)
{
	foreach ($query_six->result() as $key => $value) {
		# code...
		$note = $value->note;
		$calendar_note_id = $value->calendar_note_id;
		$notes_six .= '<tr>
						<td><span onclick="get_note_details('.$calendar_note_id.')"> '.$note.'</span>  <a class="pull-right" onclick="delete_note_details('.$calendar_note_id.',1)"><i class="fa fa-trash"></i></a></td>
					  </tr>';
	}
}


$query_six_patients = $this->reception_model->get_todays_rescheduled_patients($todays_date,2,'f',0,$branch_id);

// var_dump($query_six); die();
if($query_six_patients->num_rows() > 0)
{
	foreach ($query_six_patients->result() as $key => $value) {
		# code...
		$event_name = $value->event_name;
		$event_description = $value->event_description;
		$appointment_id = $value->appointment_id;
		$notes_six .= '<tr>
							<td><span> '.$event_name.' '.$event_description.'</span>  <a class="pull-right" onclick="delete_event_details('.$appointment_id.',1)"><i class="fa fa-trash"></i></a>
							</td>
					  	</tr>';
	}
}
// echo $notes_one;
if($branch_id == 1)
{


?>
<td>
	<table class="table borderless">
		<tbody>
			<?php echo $notes_one?>
		</tbody>
	</table>
</td>
<td>
	<table class="table borderless">
		<tbody>
			<?php echo $notes_two?>
		</tbody>
	</table>
</td>
<td>
	<table class="table borderless">
		<tbody>
			<?php echo $notes_three?>
		</tbody>
	</table>
</td>
<?php
}
else if($branch_id == 2)
{
?>

<td>
	<table class="table borderless">
		<tbody>
			<?php echo $notes_four?>
		</tbody>
	</table>
</td>
<td>
	<table class="table borderless">
		<tbody>
			<?php echo $notes_five?>
		</tbody>
	</table>
</td>
<td>
	<table class="table borderless">
		<tbody>
			<?php echo $notes_six?>
		</tbody>
	</table>
</td>
<?php
}
else
{
	?>
	<td>
		<table class="table borderless">
			<tbody>
				<?php echo $notes_one?>
			</tbody>
		</table>
	</td>
	<td>
		<table class="table borderless">
			<tbody>
				<?php echo $notes_two?>
			</tbody>
		</table>
	</td>
	<td>
		<table class="table borderless">
			<tbody>
				<?php echo $notes_three?>
			</tbody>
		</table>
	</td>
	<td>
		<table class="table borderless">
			<tbody>
				<?php echo $notes_four?>
			</tbody>
		</table>
	</td>
	<td>
		<table class="table borderless">
			<tbody>
				<?php echo $notes_five?>
			</tbody>
		</table>
	</td>
	<td>
		<table class="table borderless">
			<tbody>
				<?php echo $notes_six?>
			</tbody>
		</table>
	</td>
	<?php
}
?>