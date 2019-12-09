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
$inpatient = $patient['inpatient'];
$visit_type_name = $patient['visit_type_name'];

$today = date('jS F Y H:i a',strtotime(date("Y:m:d h:i:s")));

//doctor

//served by
$served_by = $this->accounts_model->get_personnel($this->session->userdata('personnel_id'));

$result = '';
//if users exist display them
//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = 0;
			
			
			$result .= 
			'
				<table class="table table-hover table-bordered ">
				  <thead>
					<tr>
					  <th style="text-align:center" rowspan=2>Date</th>
					  <th rowspan=2>Document Number</th>
					  <th rowspan=2>RX</th>
					  <th colspan=4 style="text-align:center;">Amount</th>
					
					</tr>
					<tr>
					  
					  <th style="text-align:center">Invoice</th>
					  <th style="text-align:center">P.By Patient</th>
					  <th style="text-align:center">P.By Insurance</th>
					  <th style="text-align:center">Paid</th>
					  <th style="text-align:center">Balance</th>
					</tr>
				  </thead>
				  <tbody>
			';
			
			
			$personnel_query = $this->personnel_model->get_all_personnel();
			$total_invoiced_amount = 0;
			$total_paid_amount = 0;
			$total_balance =0;
			$total_payable_by_patient = 0;
			$total_payable_by_insurance = 0;
			foreach ($query->result() as $row)
			{
				$visit_id = $row->visit_id;
				$visit_date = $row->visit_date;
				$visit_date = $row->visit_date;
				$visit_type_name = $row->visit_type_name;
				$visit_type = $row->visit_type;
				$rejected_amount = $row->rejected_amount;
				$total_invoice = $this->accounts_model->total_invoice($visit_id);
				$total_payments = $this->accounts_model->total_payments($visit_id);

				$patient_data = $this->reception_model->patient_names2(NULL, $visit_id);
                $visit_type_preffix = $patient_data['visit_type_preffix'];

				$array_split = explode("-", $visit_date);

				$month = $array_split[1];
				$year = $array_split[0];

				// $invoice_number = 
				$invoice_number = $visit_id; //$visit_type_preffix.'-'.$month.'/'.$year.'-'.sprintf('%03d', $visit_id);
				$total_paid_amount = $total_paid_amount + $total_payments;
				$total_invoiced_amount = $total_invoiced_amount + $total_invoice;


				$payments_rs = $this->accounts_model->payments($visit_id);
               
                $payments_made = '';
                if(count($payments_rs) > 0)
                {
                    foreach ($payments_rs as $key_items):
             
                        $payment_type = $key_items->payment_type;
                         $payment_status = $key_items->payment_status;
                        if($payment_type == 1 && $payment_status == 1)
                        {
                            $payment_method = $key_items->payment_method;
                            $amount_paid = $key_items->amount_paid;
                            $payment_created = $key_items->payment_created;
                            

                            $payments_made .='<tr>
												<td>'.$payment_created.'</td>
												<td>'.$payment_method.'</td>
												<td>'.number_format($amount_paid).'</td>
											</tr>';
                        }


                    endforeach;
                    
                }
                else
                {
                	 $payments_made .='<tr>
											<td colspan=3>No Payments Done</td>
										</tr>';
                }
                $debit_note_amount = $this->accounts_model->get_sum_debit_notes($visit_id);
                $credit_note_amount = $this->accounts_model->get_sum_credit_notes($visit_id);
				$item_invoiced_rs = $this->accounts_model->get_patient_visit_charge_items($visit_id);
				$charged_services = '
									<p><strong>'.strtoupper($visit_type_name).'</strong><p>
									<table class="table">
									  <thead>
										<tr>
										  <th >Name</th>
										  <th >Units</th>
										  <th >Charge</th>
										  <th >Total</th>										
										</tr>
									  <tbody>';

				if(count($item_invoiced_rs) > 0){
					$s=0;
					$total_nhif_days = 0;
					$total = 0;
					
					foreach ($item_invoiced_rs as $key_items):
						$service_charge_id = $key_items->service_charge_id;
						$service_charge_name = $key_items->service_charge_name;
						$visit_charge_amount = $key_items->visit_charge_amount;
						$service_name = $key_items->service_name;
						$units = $key_items->visit_charge_units;
						$service_id = $key_items->service_id;
						$personnel_id = $key_items->personnel_id;
						$total += $units*$visit_charge_amount;

						$charged_services .=  '<tr>
													<td>'.$service_charge_name.'</td>
													<td>'.$units.'</td>
													<td>'.$visit_charge_amount.'</td>
													<td> '.number_format($units*$visit_charge_amount,2).'</td>
												</tr>';
						
					endforeach;
					$charged_services .=  '<tr>
													<td colspan=3>Debit Note</td>
													<td> ('.number_format($debit_note_amount,2).')</td>
												</tr>';
					$charged_services .=  '<tr>
													<td colspan=3>Waiver / Discount</td>
													<td> ('.number_format($credit_note_amount,2).')</td>
												</tr>';
					$charged_services .=  '<tr>
													<td colspan=3>Total Invoice</td>
													<td> '.number_format($total_invoice,2).'</td>
												</tr>';
				}
				$charged_services .= '</tbody>
									</table>
									<p><strong>PAYMENTS</strong><p>';

				$charged_services .= '<table class="table">
									  <thead>
										<tr>
										  <th >Date</th>
										  <th >Amount</th>										
										</tr>
										</thead>
									  <tbody>
									  	'.$payments_made.'
										</tbody>
									</table>';


				$count++;
				$rs_rejection = $this->dental_model->get_visit_rejected_updates_sum($visit_id,$visit_type);
				$total_rejected = 0;
				if(count($rs_rejection) >0){
				  foreach ($rs_rejection as $r2):
				    # code...
				    $total_rejected = $r2->total_rejected;

				  endforeach;
				}


				$rejected_amount += $total_rejected;
				if($total_invoice > 0)
				{
					$balance = $total_invoice - $total_payments;
					$total_balance += $balance;
					if($visit_type > 1 AND $total_rejected > 0)
					{
						$payable_by_patient = $rejected_amount;
						$payable_by_insurance = $total_invoice - $rejected_amount;
					}
					else
					{
						$payable_by_patient = $total_invoice;
						$payable_by_insurance = 0;
					}

					$total_payable_by_patient += $payable_by_patient;
					$total_payable_by_insurance += $payable_by_insurance;
					$result .= 
					'
						<tr>
							<td style="text-align:center">'.$visit_date.'</td>
							<td>'.$invoice_number.'</td>
							<td>'.$charged_services.'</td>
							<td style="text-align:center">'.number_format($total_invoice,2).'</td>
							<td style="text-align:center">'.number_format($payable_by_patient,2).'</td>
							<td style="text-align:center">'.number_format($payable_by_insurance,2).'</td>
							<td style="text-align:center">'.number_format($total_payments,2).'</td>
							<td style="text-align:center">'.number_format($balance,2).'</td>
						</tr> 
					';
				}

				
				
			}

			

				$result .= 
					'
						<tr>
							<td></td>
							<td></td>
							<td style="text-align:center">Totals</td>
							<td style="text-align:center; font-weight:bold;"> '.number_format($total_invoiced_amount,2).'</td>
							<td style="text-align:center; font-weight:bold;">'.number_format($total_payable_by_patient,2).'</td>
							<td style="text-align:center; font-weight:bold;">'.number_format($total_payable_by_insurance,2).'</td>
							<td style="text-align:center; font-weight:bold;">'.number_format($total_paid_amount,2).'</td>
							<td style="text-align:center; font-weight:bold;">'.number_format($total_balance,2).'</td>
						</tr> 
					';
				$Balance =  $total_invoiced_amount -$total_paid_amount;
					$result .= 
					'
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td style="text-align:center; font-weight:bold;">Balance</td>
							<td colspan="3" style="text-align:center; font-weight:bold;">'.number_format($Balance,2).'</td>
						</tr> 
					';
			$result .= 
			'
						  </tbody>
						</table>
			';
		}

else
{
	$result .= "There are no items";
}



                   
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $contacts['company_name'];?> | Patient Statement</title>
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
				border:solid #000 !important;
				border-width:1px 0 0 1px !important;
				font-size:9px;
			}
			.row .col-md-12 th, .row .col-md-12 td {
				border:solid #000 !important;
				border-width:0 1px 1px 0 !important;
			}
			.table thead > tr > th, .table tbody > tr > th, .table tfoot > tr > th, .table thead > tr > td, .table tbody > tr > td, .table tfoot > tr > td
			{
				 padding: 2px;
			}
			
			.row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 20px;}
			.title-img{float:left; padding-left:30px;}
			img.logo{max-height:70px; margin:0 auto;}
		</style>
    </head>
    <body class="receipt_spacing">
    	<div class="row">
        	<div class="col-xs-12">
            	<img src="<?php echo base_url().'assets/logo/'.$contacts['logo'];?>" alt="<?php echo $contacts['company_name'];?>" class="img-responsive logo"/>
            </div>
        </div>
    	<div class="row">
        	<div class="col-md-12 center-align receipt_bottom_border">
            	  <strong>
                    <?php echo $contacts['company_name'];?><br/>
                    P.O. Box <?php echo $contacts['address'];?> <?php echo $contacts['post_code'];?>, <?php echo $contacts['city'];?><br/>
                    E-Mail:<?php echo $contacts['email'];?>.<br> Tel : <?php echo $contacts['phone'];?><br/>
                </strong>
            </div>
        </div>
        
      <div class="row receipt_bottom_border" >
        	<div class="col-md-12 center-align">
            	<strong>PATIENT STATEMENT DETAILS</strong>
            </div>
        </div>
        
        <!-- Patient Details -->
    	<div class="row receipt_bottom_border" style="margin-bottom: 10px;">
        	<div class="col-md-4 pull-left">
            	<div class="row">
                	<div class="col-md-12">
                    	
                    	<div class="title-item">NAME:</div>
                        
                    	<?php echo $patient_surname.' '.$patient_othernames; ?>
                    </div>
                </div>
            	
            
            </div>
            
        	<div class="col-md-4">
            	
            </div>
            
        	<div class="col-md-4 pull-right">
            	<div class="row">
                	<div class="col-md-12">
                    	<div class="title-item">Date:</div>
                        
                    	<?php echo $visit_date; ?>
                    </div>
                </div>
              
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
            	<?php echo $result;?>
            </div>
        </div>
        
		
    	<div class="row" style="font-style:italic; font-size:11px;">
        	<div class="col-md-12 ">
                <div class="col-md-6 pull-left">
                    	Prepared by: <?php echo $served_by;?> 
                </div>
                <div class="col-md-6 pull-right">
            			<?php echo date('jS M Y H:i a'); ?> Thank you
            	</div>
          	</div>
        	
        </div>
    </body>
    
</html>