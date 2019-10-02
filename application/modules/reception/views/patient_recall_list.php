<?php 
$recall_list = $this->reception_model->get_patient_recall_list($visit_id,$patient_id);

$result = "<table class='table table-bordered table-condensed'>
				<thead>
					<th>#</th>
					<th>Created</th>
					<th>Recall</th>
					<th>Tentative Date</th>
					<th>Created by</th>

				</thead>

				<tbody>";
if($recall_list->num_rows() > 0)
{
	$count = 0;
	foreach ($recall_list->result() as $key => $value) {
		# code...
		$list_name = $value->list_name;
		$created = $value->date_created;
		$period_date = $value->period_date;
		$duration = $value->duration;
		$personnel_fname = $value->personnel_fname;
		$personnel_onames = $value->personnel_onames;
		$count++;
		$result .= '<tr>
						<td>'.$count.'</td>
						<td>'.$created.'</td>
						<td>'.$list_name.'</td>
						<td>'.$period_date.'</td>
						<td>'.$personnel_onames.' '.$personnel_fname.'</td>
					</tr>';

	}

}

$result .='</tbody>
			</table>';


echo $result;



?>