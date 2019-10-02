<?php
$query_one = $this->reception_model->get_todays_calendar_notes($todays_date,1,'a',0,$branch_id);
$notes_one = '';
// var_dump($query_one); die();
if($query_one->num_rows() > 0)
{
	foreach ($query_one->result() as $key => $value) {
		# code...
		$note = $value->note;
		$calendar_note_id = $value->calendar_note_id;
		$notes_one .= '<tr>
						<td>'.$note.' <a class="pull-right" onclick="delete_note_details('.$calendar_note_id.',1)"><i class="fa fa-trash"></i></a></td>
					  </tr>';
	}
}


$query_two = $this->reception_model->get_todays_calendar_notes($todays_date,1,'b',0,$branch_id);
$notes_two = '';
// var_dump($query_two); die();
if($query_two->num_rows() > 0)
{
	foreach ($query_two->result() as $key => $value) {
		# code...
		$note = $value->note;
		$calendar_note_id = $value->calendar_note_id;
		$notes_two .= '<tr>
						<td>'.$note.' <a class="pull-right" onclick="delete_note_details('.$calendar_note_id.',1)"><i class="fa fa-trash"></i></a></td>
					  </tr>';
	}
}

$query_three = $this->reception_model->get_todays_calendar_notes($todays_date,1,'c',0,$branch_id);
$notes_three = '';
// var_dump($query_three); die();
if($query_three->num_rows() > 0)
{
	foreach ($query_three->result() as $key => $value) {
		# code...
		$note = $value->note;
		$calendar_note_id = $value->calendar_note_id;
		$notes_three .= '<tr>
						<td>'.$note.' <a class="pull-right" onclick="delete_note_details('.$calendar_note_id.',1)"><i class="fa fa-trash"></i></a></td>
					  </tr>';
	}
}
$query_four = $this->reception_model->get_todays_calendar_notes($todays_date,1,'d',0,$branch_id);
$notes_four = '';
// var_dump($query_four); die();
if($query_four->num_rows() > 0)
{
	foreach ($query_four->result() as $key => $value) {
		# code...
		$note = $value->note;
		$calendar_note_id = $value->calendar_note_id;
		$notes_four .= '<tr>
						<td>'.$note.' <a class="pull-right" onclick="delete_note_details('.$calendar_note_id.',1)"><i class="fa fa-trash"></i></a></td>
					  </tr>';
	}
}


$query_five = $this->reception_model->get_todays_calendar_notes($todays_date,1,'e',0,$branch_id);
$notes_five = '';
// var_dump($query_five); die();
if($query_five->num_rows() > 0)
{
	foreach ($query_five->result() as $key => $value) {
		# code...
		$note = $value->note;
		$calendar_note_id = $value->calendar_note_id;
		$notes_five .= '<tr>
						<td>'.$note.' <a class="pull-right" onclick="delete_note_details('.$calendar_note_id.',1)"><i class="fa fa-trash"></i></a></td>
					  </tr>';
	}
}

$query_six = $this->reception_model->get_todays_calendar_notes($todays_date,1,'f',0,$branch_id);
$notes_six = '';
// var_dump($query_six); die();
if($query_six->num_rows() > 0)
{
	foreach ($query_six->result() as $key => $value) {
		# code...
		$note = $value->note;
		$calendar_note_id = $value->calendar_note_id;
		$notes_six .= '<tr>
						<td>'.$note.' <a class="pull-right" onclick="delete_note_details('.$calendar_note_id.',1)"><i class="fa fa-trash"></i></a></td>
					  </tr>';
	}
}
// echo $notes_one;

if($branch_id == 1)
{
	?>
	<td style="padding:none !important;">
		<table class="table borderless">
			<tbody>
				<?php echo $notes_one?>
			</tbody>
		</table>
	</td>
	<td style="padding:none !important;">
		<table class="table borderless">
			<tbody>
				<?php echo $notes_two?>
			</tbody>
		</table>
	</td>
	<td style="padding:none !important;">
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
	<td style="padding:none !important;">
		<table class="table borderless">
			<tbody>
				<?php echo $notes_four?>
			</tbody>
		</table>
	</td>
	<td style="padding:none !important;">
		<table class="table borderless">
			<tbody>
				<?php echo $notes_five?>
			</tbody>
		</table>
	</td>
	<td style="padding:none !important;">
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
	<td style="padding:none !important;">
		<table class="table borderless">
			<tbody>
				<?php echo $notes_one?>
			</tbody>
		</table>
	</td>
	<td style="padding:none !important;">
		<table class="table borderless">
			<tbody>
				<?php echo $notes_two?>
			</tbody>
		</table>
	</td>
	<td style="padding:none !important;">
		<table class="table borderless">
			<tbody>
				<?php echo $notes_three?>
			</tbody>
		</table>
	</td>
	<td style="padding:none !important;">
		<table class="table borderless">
			<tbody>
				<?php echo $notes_four?>
			</tbody>
		</table>
	</td>
	<td style="padding:none !important;">
		<table class="table borderless">
			<tbody>
				<?php echo $notes_five?>
			</tbody>
		</table>
	</td>
	
	<?php
}
?>
