<?php

echo "
<table align='center' class='table table-striped table-hover table-condensed'>
	<tr>
		<th>#</th>
		<th>Date</th>
		<th>Time</th>
		<th>Services/Items</th>
		<th>Units</th>
		<th>Unit Cost (Ksh)</th>
		<th>Total</th>
		<th></th>
		<th></th>
	</tr>		
"; 

$item_invoiced_rs = $this->accounts_model->get_patient_visit_charge_items_tree($visit_id);
$total= 0; 
if($item_invoiced_rs->num_rows() > 0)
{
	foreach ($item_invoiced_rs->result() as  $value) {
		# code...
		$service_id= $value->service_id;
		$service_name = $value->service_name;
		echo"
				<tr > 
					<td colspan='9'><strong>".$service_name."</strong></td>
				</tr> 
			";

		$rs2 = $this->accounts_model->get_visit_procedure_charges_per_service($visit_id,$service_id);   

		$sub_total= 0; 
		$personnel_query = $this->personnel_model->retrieve_personnel();
			
		if(count($rs2) >0){
			$count = 0;
			$visit_date_day = '';
			foreach ($rs2 as $key1):
				$v_procedure_id = $key1->visit_charge_id;
				$procedure_id = $key1->service_charge_id;
				$date = $key1->date;
				$time = $key1->time;
				$visit_charge_timestamp = $key1->visit_charge_timestamp;
				$visit_charge_amount = $key1->visit_charge_amount;
				$units = $key1->visit_charge_units;
				$procedure_name = $key1->service_charge_name;
				$service_id = $key1->service_id;
				$provider_id = $key1->provider_id;
				$charge_creator = $key1->charge_creator;
				$sub_total= $sub_total +($units * $visit_charge_amount);
				$visit_date = date('l d F Y',strtotime($date));
				$visit_time = date('H:i A',strtotime($visit_charge_timestamp));
				
				if($visit_date_day != $visit_date)
				{
					
					$visit_date_day = $visit_date;
				}
				else
				{
					$visit_date_day = '';
				}

				// echo 'asdadsa'.$visit_date_day;

				if($personnel_query->num_rows() > 0)
				{
					$personnel_result = $personnel_query->result();
					
					foreach($personnel_result as $adm)
					{
						$personnel_id = $adm->personnel_id;
						

						if($personnel_id == $provider_id)
						{
							$provider_id = '[ Dr. '.$adm->personnel_fname.' '.$adm->personnel_lname.']';


							$procedure_name = $procedure_name.$provider_id;
						}
						
						
						
					}
				}
				
				else
				{
					$provider_id = '';
				}


				$personnel_id = $this->session->userdata('personnel_id');
				$is_admin = $this->reception_model->check_if_admin($personnel_id,1);

				if($is_admin OR $personnel_id == 0 OR ($personnel_id == $charge_creator))
				{

					$buttons = "<td align='center'>
									<input type='text' id='units".$v_procedure_id."' class='form-control' value='".$units."' size='3' />
								</td>
								<td align='center'>".number_format($visit_charge_amount)."</td>
								<td align='center'><input type='text' id='billed_amount".$v_procedure_id."' class='form-control'  size='5' value='".$units * $visit_charge_amount."' id='total".$v_procedure_id."'></div></td>
								<td>
								<a class='btn btn-sm btn-primary'  onclick='calculatetotal(".$visit_charge_amount.",".$v_procedure_id.", ".$procedure_id.",".$visit_id.")'><i class='fa fa-pencil'></i></a>
								</td>
								<td>
									<a class='btn btn-sm btn-danger'  onclick='delete_service(".$v_procedure_id.", ".$visit_id.")'><i class='fa fa-trash'></i></a>
								</td>";

				}
				else
				{
					$buttons = "
								<td align='center'>
									<input type='text' id='units".$v_procedure_id."' class='form-control' value='".$units."' size='3' readonly/>
								</td>
								<td align='center'>".number_format($visit_charge_amount)."</td>
								<td align='center'>
									<input type='text' id='billed_amount".$v_procedure_id."' class='form-control'  size='5' value='".$units * $visit_charge_amount."' id='total".$v_procedure_id."' readonly></div>
								</td>
								<td>
									Please contact an administrator
								</td>
								
								";
				}

				$count++;
				echo"
						<tr> 
							<td align='center'>".$count."</td>
							<td align='center'>".$visit_date_day."</td>
							<td align='center'>".$visit_time."</td>
							<td align='center'>".$procedure_name."</td>

							".$buttons."
							
						</tr>	
				";

				$visit_date_day = $visit_date;
				endforeach;
				

		}
		echo"
			<tr >
				<td colspan='6'><strong>Sub Total: ".$service_name." </strong></td>
				<td colspan='3'><strong>Ksh. ".number_format($sub_total)." </strong></td>
			</tr>
			";
		$total = $total + $sub_total;

	}
}



		
echo"
	<tr bgcolor='#D9EDF7'>
	<td colspan='6'><strong>Grand Total: </strong></td>
	<td colspan='3'><strong>Ksh. ".number_format($total)."</strong></td>
	</tr>
 </table>
";
?>