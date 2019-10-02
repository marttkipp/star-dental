<?php
$rs_pa = $this->nurse_model->get_prescription_notes($patient_id);
$sick_leave_days = 0;
$sick_leave_start_date = date('Y-m-d');
$todays_prescription ='';
$result ='<table class="table table-bordered">
			<th>Date</th>
			<th>Prescription</th>
			<tbody>';
if(count($rs_pa) >0){
	foreach ($rs_pa as $r2):
		# code...
		$visit_prescription = $r2->visit_prescription;
		$visit_date = $r2->visit_date;
		$visit_idd = $r2->visit_id;

		if($visit_date == $sick_leave_start_date)
		{
			$todays_prescription = $visit_prescription;
		}

		// get the visit charge
		$result .= '<tr>
						<td>'.$visit_date.'</td>

						<td>'.$visit_prescription.'</td>
						<td><a href="'.site_url().'print-prescription/'.$visit_idd.'" target="_blank" class="btn btn-sm btn-warning" >Print Prescription</a></td>
						
					</tr>';
	endforeach;


	


}
$result .='</tbody>
			</table>';




echo $result;

?>