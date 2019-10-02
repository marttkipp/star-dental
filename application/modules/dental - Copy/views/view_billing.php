<?php

$rs2 = $this->nurse_model->get_visit_procedure_charges($visit_id);


echo "
<table align='center' class='table table-striped table-hover table-condensed'>
	<tr>

		<th>Visit Charge</th>
		<th>Tooth Number</th>
		<th>Units</th>
		<th>Unit Cost</th>
		<th>Total</th>
		<th></th>
		<th></th>
	</tr>
";
		$total= 0;
		if(count($rs2) >0){
			foreach ($rs2 as $key1):
				$v_procedure_id = $key1->visit_charge_id;
				$procedure_id = $key1->service_charge_id;
				$visit_charge_amount = $key1->visit_charge_amount;
				$visit_charge_notes = $key1->visit_charge_notes;
				$units = $key1->visit_charge_units;
				$procedure_name = $key1->service_charge_name;
				$service_id = $key1->service_id;

				$total= $total +($units * $visit_charge_amount);
				$checked="";
				// $personnel_check = TRUE;
				// if($personnel_check)
				// {
					$checked = "<td>
								<a class='btn btn-sm btn-primary'  onclick='calculatetotal(".$visit_charge_amount.",".$v_procedure_id.", ".$procedure_id.",".$visit_id.")'><i class='fa fa-pencil'></i></a>
								</td>
								<td>
									<a class='btn btn-sm btn-danger' href='#' onclick='delete_procedure(".$v_procedure_id.", ".$visit_id.")'><i class='fa fa-trash'></i></a>
								</td>";
				// }

				echo"
						<tr>
							<td align='center'>".$procedure_name."</td>
							<td align='center'>
								<input type='text' id='notes".$v_procedure_id."' class='form-control' value='".$visit_charge_notes."'  />
							</td>
							<td align='center'>
								<input type='text' id='units".$v_procedure_id."' class='form-control' value='".$units."' size='3' />
							</td>

							<td align='center'><input type='text' class='form-control' size='5' value='".$visit_charge_amount."' id='billed_amount".$v_procedure_id."'></div></td>

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

echo '<div class="row">
      	<div class="col-md-12">
  			<div class="form-group">
   				<label class="col-lg-4 control-label">Deductions and/or additions:  </label>
    		<div class="col-lg-8">
      				<textarea id="deductions_and_other_info" rows="5" cols="50" class="form-control col-md-12" > '.$payment_info.' </textarea>
      		</div>
      	</div>
    </div>
  </div>';

  echo '
  <br>
	<div class="row">
        <div class="form-group">
            <div class="col-lg-12">
                <div class="center-align">
                      <a hred="#" class="btn btn-large btn-info" onclick="save_other_deductions('.$visit_id.')">Save other payment information</a>
                  </div>
            </div>
        </div>
    </div>';
?>
