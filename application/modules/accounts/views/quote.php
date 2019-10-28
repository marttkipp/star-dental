<?php

//patient details
$visit_type = $patient['visit_type'];
$patient_type = $patient['patient_type'];
$patient_othernames = $patient['patient_othernames'];
$patient_surname = $patient['patient_surname'];
$patient_surname = $patient['patient_surname'];
$patient_number = $patient['patient_number'];
$gender = $patient['gender'];
$patient_insurance_number = $patient['patient_insurance_number'];
$account_balance = $patient['account_balance'];
$inpatient = $patient['inpatient'];
$visit_type_name = $patient['visit_type_name'];
$visit_status = 0;


$today = date('jS F Y H:i a',strtotime(date("Y:m:d h:i:s")));
$visit_date = date('jS F Y',strtotime($this->accounts_model->get_visit_date($visit_id)));

//doctor
$doctor = $this->accounts_model->get_att_doctor($visit_id);

//served by
$served_by = $this->accounts_model->get_personnel($this->session->userdata('personnel_id'));

//services details
$item_invoiced_rs = $this->accounts_model->get_patient_visit_charge_items($visit_id);
$credit_note_amount = $this->accounts_model->get_sum_credit_notes($visit_id);
$debit_note_amount = $this->accounts_model->get_sum_debit_notes($visit_id);

$visit_rs = $this->accounts_model->get_visit_details($visit_id);
$visit_type_id = 0;
if($visit_rs->num_rows() > 0)
{
	foreach ($visit_rs->result() as $key => $value) {
		# code...
		$close_card = $value->close_card;
		$visit_type_id = $value->visit_type;
		$invoice_number = $value->invoice_number;
		$visit_time_out = $value->visit_time_out;
		$parent_visit = $value->parent_visit;

		$visit_time_out = date('jS F Y',strtotime($visit_time_out));
	}
}
// var_dump($visit_type_id);die();
//payments
$payments_rs = $this->accounts_model->get_all_visit_transactions($visit_id);
$total_payments = 0;
$s = 0;
$total_amount = 0;

//at times credit & debit notes may not be assigned
//to a particular service but still need to be displayed
/*
$display_notes = array();

if($all_notes->num_rows() > 0)
{
	foreach($all_notes->result() as $row)
	{
		$payment_service_name = $row->service_name;
		$payment_service_id = $row->payment_service_id;
		$amount_paid = $row->amount_paid;
		$payment_type = $row->payment_type;
		$found = 0;

		//check if service exist in query from service charge
		if(count($item_invoiced_rs) > 0)
		{
			foreach ($item_invoiced_rs as $key_items):
				$service_id = $key_items->service_id;

				if($service_id == $payment_service_id)
				{
					$found = $service_id;
					break;
				}

			endforeach;
		}

		//if item was not found
		if($found == 0)
		{
			$data['payment_service_name'] = $payment_service_name;
			$data['payment_service_id'] = $payment_service_id;
			$data['amount_paid'] = $amount_paid;
			$data['payment_type'] = $payment_type;

			array_push($display_notes, $data);
		}
	}
}
$total_notes = count($display_notes);*/

$services_billed = array();
$all_notes = $this->accounts_model->get_all_notes($visit_id);
if($all_notes->num_rows() > 0)
{
	foreach($all_notes->result() as $row)
	{
		$payment_service_name = $row->service_name;
		$payment_service_id = $row->payment_service_id;
		$in_array = 0;

		$total_services = count($services_billed);
		if($total_services > 0)
		{
			for($t = 0; $t < $total_services; $t++)
			{
				$saved_service_id = $services_billed[$t]['payment_service_id'];

				if($saved_service_id == $payment_service_id)
				{
					$in_array = 1;
					break;
				}
			}
		}

		if($in_array == 0)
		{
			$data['payment_service_name'] = $payment_service_name;
			$data['payment_service_id'] = $payment_service_id;

			array_push($services_billed, $data);
		}
	}
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $contacts['company_name'];?> | Invoice</title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" media="all"/>
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css" media="all"/>
        <style type="text/css">
			.receipt_spacing{letter-spacing:0px; font-size: 12px;}
			.center-align{margin:0 auto; text-align:center;}

			.receipt_bottom_border{border-bottom: #888888 medium solid;}
			.row .col-md-12 table {
				/*border:solid #000 !important;*/
				/*border-width:1px 0 0 1px !important;*/
				font-size:12px;
				margin-top:10px;
			}
			.col-md-6 {
			    width: 50%;
			 }
			.row .col-md-12 th, .row .col-md-12 td {
				/*border:solid #000 !important;*/
				/*border-width:0 1px 1px 0 !important;*/
			}
			.table thead > tr > th, .table tbody > tr > th, .table tfoot > tr > th, .table thead > tr > td, .table tbody > tr > td, .table tfoot > tr > td
			{
				 /*padding: 2px;*/
				 padding: 5px;
			}
			h3
			{
				font-size: 30px;
			}

			.row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 20px;}
			.title-img{float:left; padding-left:30px;}
			img.logo{ margin:0 auto;}
		</style>
    </head>
    <body class="receipt_spacing">


		<div class="row receipt_bottom_border" >
			<div class="col-md-12">
				<div class="pull-left" style="margin-bottom: 10px;">
		        	<img src="<?php echo base_url().'assets/logo/'.$contacts['logo'];?>" alt="<?php echo $contacts['company_name'];?>" class="img-responsive logo" width=200px; />
		        </div>
		    	<div  class="pull-right" style="margin-right: 10px;">
		    		<h3 style="margin-top: 10px;margin-bottom: 5px;"><strong>QUOTATION</strong></h3>
		        	<p style="font-size: 14px;">
		            	<strong><?php echo $contacts['company_name'];?></strong> <br/>
		                <?php echo $contacts['location'];?>, <?php echo $contacts['building'];?>, <?php echo $contacts['floor'];?><br/>
		                Address : P.O. Box <?php echo $contacts['address'];?> <?php echo $contacts['post_code'];?>, <?php echo $contacts['city'];?> <br/>
		                Office Line:  <?php echo $contacts['phone'];?> <br/>
		                
		            </p>
		        </div>
		    </div>
		</div>

      	
        <!-- Patient Details -->
    	<div class="row receipt_bottom_border" style="margin-bottom: 10px; padding-top: 10px; padding-bottom: 10px; padding-left: 5px; padding-right: 5px;">
	    	<div class="col-md-12">
	        	<div class="pull-left " style="border: 1px solid gray;padding: 6px;">
	        		<div class="row">
	                	<div class="col-md-12">
	                    	<h5 >BILL TO: </h5>
	                    </div>
	                </div>


	        		<div class="row" style="font-size: 15px">
	                	<div class="col-md-12">
	                    	<div >PATIENT NAME: <strong><?php echo strtoupper($patient_surname.' '.$patient_othernames); ?></strong> </div>
	                    </div>
	                </div>

	            	<div class="row" style="font-size: 15px">
	                	<div class="col-md-12">
	                    	<div >CARD NO : <strong><?php echo $patient_number; ?></strong> </div>
	                    </div>
	                </div>


	            </div>

	        	<div class="pull-right" style="font-size: 15px;border: 1px solid gray;padding: 6px;">
	        		<div class="row">
	                	<div class="col-md-12">
	                    	<div >ESTIMATE NUMBER: <strong><?php echo $visit_id; ?></strong> </div>
	                    </div>
	                </div>
	            	<div class="row">
	                	<div class="col-md-12">
	                    	<div >ESTIMATE DATE: <strong><?php echo $visit_date; ?></strong> </div>
	                    </div>
	                </div>


	            </div>
	        </div>

        </div>

    	<div class="row">
        	<div class="col-md-12">
            	<table class="table table-striped table-bordered  col-md-12" >
                    <thead>
                    <tr>
						<th>#</th>
						<th>Services/Items</th>

						<th>Units</th>
						<th >Unit Cost (Ksh)</th>
						<th >Total</th>
					</tr>
                    </thead>
                    <tbody>
						<?php
                        $total = 0;
                        $item_invoiced_rs = $this->accounts_model->get_patient_visit_quote_items_tree($visit_id);
						$total_amount= 0;
						$days = 0;
						$count = 0;
						if($item_invoiced_rs->num_rows() > 0)
						{
							foreach ($item_invoiced_rs->result() as  $value) {
								# code...
								$service_id= $value->service_id;
								$service_name = $value->service_name;

								// var_dump($visit_type_id);die();

								$rs2 = $this->accounts_model->get_visit_quote_charges_per_service($visit_id,$service_id,$visit_type_id);

								// if($service_name == "Bed charge")
								// {
								// 	$days = count($rs2);
								// }

								// var_dump($service_name); die();
								$sub_total= 0;
								$personnel_query = $this->personnel_model->retrieve_personnel();

								if(count($rs2) >0){

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
										$visit_charge_notes = $key1->visit_charge_notes;

										$sub_total= $sub_total +($units * $visit_charge_amount);
										$visit_date = date('l d F Y',strtotime($date));
										$visit_time = date('H:i A',strtotime($visit_charge_timestamp));
										if($visit_date_day != $visit_date)
										{

											$visit_date_day = $visit_date;
										}
										else
										{
											$visit_date_day == $visit_date;
										}



										if($personnel_query->num_rows() > 0)
										{
											$personnel_result = $personnel_query->result();

											foreach($personnel_result as $adm)
											{
												$personnel_id = $adm->personnel_id;


													if($personnel_id == $provider_id)
													{
														$provider_id = ' [ Dr. '.$adm->personnel_fname.' '.$adm->personnel_lname.']';

														$procedure_name = $procedure_name.$provider_id;
													}






											}

										}

										else
										{
											$provider_id = '';

										}
										if($procedure_name == 'General Female Ward' || $procedure_name == 'General Male Ward' || $procedure_name == 'Private Ward' || $procedure_name == 'Semi-Private Female Ward' || $procedure_name == 'High Dependancy Unit Ward'  || $procedure_name == 'Intensive Care Unit Ward' )
										{
											$days++;
										}




										$count++;
										echo"
												<tr>
													<td >".$count."</td>
													<td >".strtoupper($procedure_name)." ".$visit_charge_notes."</td>
													<td>".$units."</td>
													<td>".number_format($visit_charge_amount,2)."</td>
													<td>".number_format($units * $visit_charge_amount,2)."</td>

												</tr>
										";
										$visit_date_day = $visit_date;
										endforeach;


								}

								$total_amount = $total_amount + $sub_total;

							}
						}

						$total_services = count($services_billed);
						$debit_note_pesa = $this->accounts_model->total_debit_note_per_service($payment_service_id, $visit_id);
						$credit_note_pesa = $this->accounts_model->total_credit_note_per_service($payment_service_id, $visit_id);
						// var_dump($payment)
						if(count($payments_rs) > 0)
						{
							$x = $count;
							foreach ($payments_rs as $key_items):
								$x++;
								$payment_method = $key_items->payment_method;

								$amount_paid = $key_items->amount_paid;
								$time = $key_items->time;
								$payment_type = $key_items->payment_type;
								$amount_paidd = number_format($amount_paid,2);
								$payment_service_id = $key_items->payment_service_id;
								$reason = $key_items->reason;

								if($payment_service_id > 0)
								{
								$service_associate = $this->accounts_model->get_service_detail($payment_service_id);
								}
								else
								{
								$service_associate = " ";
								}

								if($payment_type == 3)
								{
									$type = "Debit Note";
									$amount_paidd = $amount_paidd;

									?>
									<tr>
										<td><?php echo $x;?></td>
										<td colspan="2">DEBIT NOTE - [<?php echo $reason;?>]</td>
										<td ><?php echo $amount_paidd;?></td>
										<td ><?php echo $amount_paidd;?></td>
									</tr>
									<?php
								}

								else if($payment_type == 2)
								{
									$type = "Credit Note";
									$amount_paidd = "($amount_paidd)";

									?>
									<tr>
										<td><?php echo $x;?></td>
										<td colspan="2">CREDIT NOTE - [<?php echo $reason;?>]</td>
										<td><?php echo $amount_paidd;?></td>
										<td><?php echo $amount_paidd;?></td>
									</tr>
									<?php
								}

							endforeach;
						}




                      ?>

                    </tbody>
                  </table>
            </div>
        </div>
        <?php
        $rejected_amount = 0;

         $rs_rejection = $this->dental_model->get_rejection_info($visit_id);

		$rejected_reason ='';
		$close_card = 0;
		if(count($rs_rejection) >0){
		  foreach ($rs_rejection as $r2):
		    # code...
		    $rejected_amount = $r2->rejected_amount;
		    $rejected_date = $r2->rejected_date;
		    $rejected_reason = $r2->rejected_reason;
		    $close_card = $r2->close_card;
		    $parent_visit = $r2->parent_visit;

		    // get the visit charge

		  endforeach;
		}


		$rs_rejection = $this->dental_model->get_visit_rejected_updates_sum($visit_id,$visit_type_id);
		$total_rejected = 0;
		if(count($rs_rejection) >0){
		  foreach ($rs_rejection as $r2):
		    # code...
		    $total_rejected = $r2->total_rejected;

		  endforeach;
		}

		$rejected_amount += $total_rejected;



		// $total_amount += $debit_note_pesa;
		// $total_amount -= $credit_note_pesa;

        $payments_value = $this->accounts_model->total_payments($visit_id);

		$invoice_total = $total_amount;//$this->accounts_model->total_invoice($visit_id);

		$balance = $this->accounts_model->balance($payments_value,$invoice_total);

		$invoice_total += $debit_note_pesa;
		$invoice_total -= $credit_note_pesa;
		if($visit_type > 1 AND $rejected_amount > 0)
		{
			$payable_by_patient = $rejected_amount;
			$payable_by_insurance = $invoice_total - $rejected_amount;
		}
		else
		{
			$payable_by_patient = 0;
			$payable_by_insurance = $invoice_total;
		}
		// <tr>
	 //            		<td><strong>PAYABLE BY PATIENT</strong></td><td>Ksh. ('. number_format($payable_by_patient,2).')</td>
	 //            	</tr>
		// var_dump($rejected_amount); die();
		$result = '

	            	<tr>
	            		<td><strong style="font-size:16px; text-align:center">GRAND TOTAL</strong></td><td style="font-size:16px; text-align:center"><strong>Ksh. '.number_format($payable_by_insurance ,2).'</strong> </td>
	            	</tr>
	            	';

        ?>

        <div class="row">
        	<div class="col-md-12">
        		<div class="col-md-6">
	        	</div>
	        	<div class="col-md-6 pull-right">
					<table class="table table-bordered  table-striped col-md-12 ">

		                <tbody>
		                
		                	<?php echo $result;?>


		                </tbody>
		            </table>
				</div>
        		
        	</div>
        	
        </div>

    	

	   
    </body>

</html>
