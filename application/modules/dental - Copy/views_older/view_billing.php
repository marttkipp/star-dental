<?php

//  check if visit exists

$rs = $this->nurse_model->check_visit_type($visit_id);

if(count($rs) >0){
	foreach ($rs as $rs1):
		# code...
		$visit_t = $rs1->visit_type;

		// get the visit charge

	endforeach;

	$rs2 = $this->nurse_model->visit_charge($visit_id);

	


}



echo "
	<div class='navbar-inner'><p style='text-align:center; color:#0e0efe;'><input type='button' class='btn btn-primary' value='Add Billing Info' onclick='open_window_billing(".$visit_id.")'/></p></div>
<table align='center' class='table table-striped table-hover table-condensed'>
	<tr>
		
		<th align='center'>Service Charge</th>
		<th align='center'>Units</th>
		<th align='center'>Unit Cost</th>
		<th align='center'>Total</th>
	<th></th>
	</tr>		
";                     
		$total= 0;  
		if(count($rs2) >0){
			foreach ($rs2 as $rs3):
				# code...
				$v_procedure_id = $rs3->visit_charge_id;
				$procedure_id = $rs3->service_charge_id;
				$visit_charge_amount = $rs3->visit_charge_amount;
				$units = $rs3->visit_charge_units;

				//  get the service charge

				$rs4 = $this->nurse_model->get_service_charge($procedure_id);
				if(count($rs4) >0){
					foreach ($rs4 as $rs5):
		
						$procedure_name = $rs5->service_charge_name;
						$service_id = $rs5->service_id;
						if($service_id==9){
							$total= $total +($units * $visit_charge_amount);
							echo"
									<tr> 
							 			<td align='center'>".$procedure_name."</td>
										<td align='center'><input type='text' class='form-control' id='units".$v_procedure_id."' value='".$units."' size='3' onkeyup='calculatebillingtotal(".$visit_charge_amount.",".$v_procedure_id.", ".$procedure_id.",".$visit_id.")'/></td>
										<td align='center'>".$visit_charge_amount."</td>
										<td align='center'><input type='text' class='form-control' readonly='readonly' size='5' value='".$units * $visit_charge_amount."' id='total".$v_procedure_id."'></div></td>
										<td>
											<div class='btn-toolbar'>
												<div class='btn-group'>
													<a class='btn' href='#' onclick='delete_billing(".$v_procedure_id.", ".$visit_id.")'><i class='icon-remove'></i></a>
												</div>
											</div>
										</td>
									</tr>	
							";					
						} 
						endforeach;
				}
				endforeach;

		}
echo"
<tr bgcolor='#59B3D5'>
<td></td>
<td></td>
<td></td>
<td>Grand Total: </td>
<td align='center'><div id='grand_total'>".$total."</div></td>
</tr>
 </table>
 <div class='clear'></div>
 <br>
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
      				<textarea id="deductions_and_other" rows="5" cols="50" class="form-control col-md-12" > '.$payment_info.' </textarea>
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