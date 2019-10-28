<?php

$rs2 = $this->nurse_model->get_visit_procedure_quotation_list($visit_id);


echo "
<table align='center' class='table table-striped table-hover table-condensed'>
	<tr>
		
		<th>Visit Charge</th>
		<th>Units</th>
		<th>Unit Cost</th>
		<th>Total</th>
		<th></th>
		<th></th>
		<th></th>
	</tr>		
";                     
		$total= 0;  
		if($rs2->num_rows() >0){
			foreach ($rs2->result() as $key1):
				$v_procedure_id = $key1->visit_charge_id;
				$procedure_id = $key1->service_charge_id;
				$visit_charge_amount = $key1->visit_charge_amount;
				$units = $key1->visit_charge_units;
				$procedure_name = $key1->service_charge_name;
				$service_id = $key1->service_id;
				$visit_type_id = $key1->visit_type_id;

				if($visit_type_id == 1)
				{
					$visit = 'SELF';
				}
				else
				{
					$visit = 'INSURANCE';
				}
			
				$total= $total +($units * $visit_charge_amount);
				$checked="";
				$personnel_check = TRUE;
				if($personnel_check)
				{
					$checked = "<td>
								<a class='btn btn-sm btn-primary'  onclick='calculatetotalquotation(".$visit_charge_amount.",".$v_procedure_id.", ".$procedure_id.",".$visit_id.")'><i class='fa fa-pencil'></i></a>
								</td>
								<td>
									<a class='btn btn-sm btn-danger' href='#' onclick='delete_quote(".$v_procedure_id.", ".$visit_id.")'><i class='fa fa-trash'></i></a>
								</td>";
				}
				
				echo"
						<tr> 
							<td align='center'>".$procedure_name."</td>
							<td align='center'>
								<input type='text' id='quote_units".$v_procedure_id."' class='form-control' value='".$units."' size='3' />
							</td>
							<td align='center'><input type='text' class='form-control' size='5' value='".$visit_charge_amount."' id='quote_amount".$v_procedure_id."'></div></td>

							<td align='center'>".number_format($units*$visit_charge_amount)."</td>
							".$checked."
						</tr>	
				";
				endforeach;

		}
echo"
<tr bgcolor='#D9EDF7'>
<td></td>
<td></td>
<th>Grand Total: </th>
<th colspan='3'><div id='grand_total'>".number_format($total)."</div></th>
<td></td>
<td></td>
</tr>
 </table>
";

$rs_pa = $this->dental_model->get_payment_info($visit_id);

if(count($rs_pa) >0){
	foreach ($rs_pa as $r2):
		# code...
		$payment_info = $r2->payment_info;

		// get the visit charge

	endforeach;


	


}


?>