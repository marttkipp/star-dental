<?php

$query = $this->dental_model->get_patient_waivers($patient_id);


echo "
<br/>
<table align='center' class='table table-striped table-hover table-condensed'>
	<tr>
		
		<th></th>
		<th>Date</th>
		<th>Amount</th>
		<th>Reason</th>
		<th></th>
	</tr>		
";                     
		$total= 0;  
		if($query->num_rows() > 0)
		{
			$count = 0;
			foreach ($query->result() as $key => $value) {
			# code...
				$amount_paid = $value->amount_paid;
				$date_paid = $value->payment_created;
				$reason = $value->reason;
				$visit_id = $value->visit_id;
				$payment_id = $value->payment_id;
				$patient_id = $value->patient_id;
				if($date_paid == date('Y-m-d'))
				{
					$button = "<a class='btn btn-sm btn-danger' href='#' onclick='delete_waiver_work(".$payment_id.",".$patient_id.")'><i class='fa fa-trash'></i></a>";
				}
				else
				{
					$button = '';
				}
				$count++;
				echo"
						<tr> 
							<td>".$count."</td>	
							<td>".$date_paid."</td>	
							<td>".$amount_paid."</td>
							<td>".$reason."</td>								
							<td>
								".$button."
							</td>
						</tr>	
				";
			}
		}
		
		
echo"
 </table>
";

?>