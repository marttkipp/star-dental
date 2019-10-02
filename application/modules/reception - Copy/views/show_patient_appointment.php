<?php

$rs= $this->reception_model->patients_schedule($patient_id,$visit_date);

if (count($rs) == 0)
{

echo '<h5 style="font-family:Palatino Linotype;">Appointment List from '.$visit_date.' </h5>';

echo 'No appointments scheduled on '.$visit_date;
}
else{

echo '<h5 style="font-family:Palatino Linotype;">Appointment List from '.$visit_date.'  </h5>';
echo '<table  class="table table-hover table-bordered "> <tr> <th>Visit Date</th> <th> Room</th> <th>Start Time</th> <th> End Time</th> <th> Doctor</th> </tr>';
foreach ($rs as $key) {
	# code...

	$time_end=$key->time_end;
	$time_start=$key->time_start;
	$visit_date=$key->visit_date;
	$personnel_id=$key->personnel_id;
	$room_id=$key->room_id;
	$room_name = '';
	$drname2 ='';
	$rs1= $this->reception_model->doctors_names($personnel_id);
	if (count($rs1) > 0)
	{
		foreach ($rs1 as $key2) {
			$drname= $key2->personnel_fname;
			$drname2= $key2->personnel_onames;
		}
	}
	
	$rs2= $this->reception_model->rooms_names($room_id);
	if (count($rs2) > 0)
	{
		foreach ($rs2 as $key3) {
			$room_name= $key3->room_name;
		}
	}

echo '<tr> <td>'.$visit_date.' </td> <td>'.$room_name.'</td> <td>'.$time_start.' <td>'.$time_end.'</td> <td>'.$drname2.'</td>  </tr> ';

}
echo '</table>';

	
}

?>