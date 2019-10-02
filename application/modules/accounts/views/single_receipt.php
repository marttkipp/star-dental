<?php

//patient details
$visit_type = $patient['visit_type'];
$patient_type = $patient['patient_type'];
$patient_othernames = $patient['patient_othernames'];
$patient_surname = $patient['patient_surname'];
$patient_surname = $patient['patient_surname'];
$patient_number = $patient['patient_number'];
$visit_id = $patient['visit_id'];
$gender = $patient['gender'];

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


//payments
$payments_rs = $this->accounts_model->payment_detail($receipt_payment_id);
$total_payments = 0;
$payment_created = $visit_date;	
if(count($payments_rs) > 0)
{
	$x=0;
	
	foreach ($payments_rs as $key_items):
		$x++;
        $payment_type = $key_items->payment_type;
        $payment_created = date('jS F Y',strtotime($key_items->payment_created));
        $payment_method = $key_items->payment_method;
       $receip_date = $key_items->payment_created;


	endforeach;
    
}
$previous_payment = $this->accounts_model->previous_payment($visit_id,$receip_date);
$all_payments_rs = $this->accounts_model->get_all_visit_transactions($visit_id);
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <title><?php echo $contacts['company_name'];?> | Receipt</title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" media="all"/>
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css" media="all"/>
        <style type="text/css">
		body{font-family:"Courier New", Courier, monospace;text-align: left}
        .receipt_spacing{letter-spacing:0px; font-size: 17px;}
        /*.center-align{margin:0 auto; text-align:center;}*/
        
        .receipt_bottom_border{border-bottom: #888888 medium solid;}
        .row .col-md-12 table {
            /*border:solid #000 !important;*/
            /*border-width:1px 0 0 1px !important;*/
            font-size:15px;
        }
        .row .col-md-12 th, .row .col-md-12 td {
            /*border:solid #000 !important;*/
            /*border-width:0 1px 1px 0 !important;*/
        }
        .table thead > tr > th, .table tbody > tr > th, .table tfoot > tr > th, .table thead > tr > td, .table tbody > tr > td, .table tfoot > tr > td
        {
             padding: 5px;
        }
        
        .row .col-md-12 .title-item{
        								float:left;
        								/*width: 130px; */
        								/*font-weight:bold; */
        								/*text-align:right; */
        								padding-right: 20px;
        							}
        .title-img{float:left; padding-left:30px;}
		img.logo{max-height:70px; margin:0 auto;}
		.title-item{font-weight: none !important;}
    </style>
    </head>
    <body class="receipt_spacing">
    	<div class="row">
        	<div class="col-xs-12">
            	<img src="<?php echo base_url().'assets/logo/'.$contacts['logo'];?>" alt="<?php echo $contacts['company_name'];?>" class="img-responsive logo"/>
            </div>
        </div>
    	<div class="row">
        	<div class="col-md-12 center-align ">
            	<strong>
                	<?php echo $contacts['company_name'];?><br/>
                    P.O. Box <?php echo $contacts['address'];?> <?php echo $contacts['post_code'];?>, <?php echo $contacts['city'];?><br/>
                    E-mail: <?php echo $contacts['email'];?>. Tel : <?php echo $contacts['phone'];?><br/>
                    <?php echo $contacts['location'];?><br/>
                </strong>
            </div>
        </div>
        
        
        <!-- Patient Details -->
    	<div class="row receipt_bottom_border" style="margin-bottom: 10px; margin-top: 20px; text-align: left !important;">
        	<div class="col-md-6">
        		<div class="row">
                	<div class="col-md-12">
                    	<div class="title-item">Receipt: </div>                        
                    	 <strong><?php echo $receipt_payment_id; ?></strong>
                    </div>
                </div>
                <div class="row">
                	<div class="col-md-12">
                    	<div class="title-item">Invoice: </div>                        
                    	 <strong><?php echo $visit_id; ?></strong>
                    </div>
                </div>
            	<div class="row">
                	<div class="col-md-12">
                    	
                    	<div class="title-item">Payment for :</div>
                        
                    	<strong><?php echo $patient_surname.' '.$patient_othernames; ?></strong>
                    </div>
                </div>
                <div class="row">
                	<div class="col-md-12">
                    	
                    	<div class="title-item">Date :</div>
                        
                    	<strong><?php echo $payment_created; ?></strong>
                    </div>
                </div>
            	
            </div>
            
        	<div class="col-md-6">
            	<div class="row">
                	<div class="col-md-12">
                    	<div class="title-item">Payment:</div>
                        
                    	<?php echo $payment_method; ?>
                    </div>
                </div>
            </div>
        </div>
        
    	<div class="row receipt_bottom_border">
        	<div class="col-md-12">
            	<table class="table  col-md-12">
                    <thead>
                    <tr>
                      <th>Service</th>
                      <th>Charge</th>
                      <th>QTY</th>
                      <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
						<?php
                        $total = 0;
                        if(count($item_invoiced_rs) > 0){
							$s=0;
							$total_nhif_days = 0;
							
							foreach ($item_invoiced_rs as $key_items):
								$service_charge_id = $key_items->service_charge_id;
								$service_charge_name = $key_items->service_charge_name;
								$visit_charge_amount = $key_items->visit_charge_amount;
								$service_name = $key_items->service_name;
								$units = $key_items->visit_charge_units;
								$service_id = $key_items->service_id;
								$personnel_id = $key_items->personnel_id;

								
								$doctor = '';
								
								if($service_name == 'Bed charge')
								{
									$total_nhif_days = $units;
								}
								
								if($personnel_id > 0)
								{
									$doctor_rs = $this->reception_model->get_personnel($personnel_id);
									if($doctor_rs->num_rows() > 0)
									{
										$key_personnel = $doctor_rs->row();
										$first_name = $key_personnel->personnel_fname;
										$personnel_onames = $key_personnel->personnel_onames;
										$doctor = ' : Dr. '.$personnel_onames.' '.$first_name;
									}
								}
								//var_dump($service_id);
								//if lab check to see if drug is in pres
								if($service_id == 4)
								{
									if($this->accounts_model->in_pres($service_charge_id, $visit_id))
									{
										$visit_total = $visit_charge_amount * $units;
										$s++;
										
										?>
										<tr>
											<td><?php echo $s;?></td>
											<td><?php echo $service_charge_name;?></td>
											<td><?php echo $units;?></td>
											<td><?php echo number_format($visit_charge_amount,2);?></td>
											<td><?php echo number_format($visit_total,2);?></td>
										</tr>
										<?php
										$total = $total + $visit_total;
									}
								}
								
								else
								{
									//$debit_note_pesa = $this->accounts_model->total_debit_note_per_service($service_id,$visit_id);
									
									//$credit_note_pesa = $this->accounts_model->total_credit_note_per_service($service_id,$visit_id);
									
									$visit_total = $visit_charge_amount * $units;
									$s++;
									
									//$visit_total = ($visit_total + $debit_note_pesa) - $credit_note_pesa;
									?>
									<tr>
										<td><?php echo strtoupper($service_charge_name);?></td>
                                        <td><?php echo number_format($visit_charge_amount,2);?></td>
                                        <td><?php echo $units;?></td>
										<td><?php echo number_format($visit_total,2);?></td>
									</tr>
									<?php
									$total = $total + $visit_total;
								}
							endforeach;
							
							//less NHIF Rebate
							if(($inpatient == 1) && (!empty($patient_insurance_number)))
							{
								$rebate = 1600;
								$total_rebate = $rebate * $total_nhif_days;
								$s++;
								?>
                                <tr>
                                    <td><?php echo $s;?></td>
                                    <td colspan="2">Less NHIF Rebate</td>
                                    <td><?php echo $total_nhif_days;?></td>
                                    <td><?php echo number_format($rebate,2);?></td>
                                    <td>(<?php echo number_format($total_rebate,2);?>)</td>
                                </tr>
                                <?php
								$total = $total - $total_rebate;
							}
							$total_amount = $total ;
							
							// $total_amount = ($total + $debit_note_amount) - $credit_note_amount;
                        }
						
						$total_services = count($services_billed);
                    $debit_note_pesa = $this->accounts_model->total_debit_note_per_service(null, $visit_id);
                    $credit_note_pesa = $this->accounts_model->total_credit_note_per_service(null, $visit_id);

                    if(count($all_payments_rs) > 0)
                        {
                        $x = $count;
                        foreach ($all_payments_rs as $key_items):
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
                                </tr>
                                <?php
                            }
                            
                        endforeach;
                        }
                                  
					
					  
					  	if(count($payments_rs) > 0)
						{
							$x=0;
							
							foreach ($payments_rs as $key_items):
								$x++;
								$payment_type = $key_items->payment_type;
								$payment_status = $key_items->payment_status;
								
								if($payment_type == 1 && $payment_status == 1)
								{
									$payment_method = $key_items->payment_method;
									$amount_paid = $key_items->amount_paid;
									
									$total_payments = $total_payments + $amount_paid;
								}
							endforeach;
						}
					  
                      ?>
                        
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row receipt_bottom_border" style="margin-top:10px;">
        	<div class="col-md-6">
        	</div>
        	<div class="col-md-6">
            	<div class="row">
                	<div class="col-md-6">
                		<div class="title-item">Total Bill:</div>
                        
                    </div>
                    <div class="col-md-6">
                    	
                    	<strong> <?php echo number_format(($total_amount+$debit_note_pesa)-$credit_note_pesa ,2);?></strong>
                    </div>
                </div>

                <div class="row">
                	<div class="col-md-6">
                    	<div class="title-item">Previous Payments:</div>
                    </div>
                    <div class="col-md-6">
                    	<strong> <?php echo number_format($previous_payment,2);?></strong>
                    </div>
                </div>
              
                <div class="row">
                	<div class="col-md-6">
                    	<div class="title-item">Balance Due:</div>
                     </div>
                    <div class="col-md-6">   
                    	<strong> <?php echo number_format($total_amount-$previous_payment,2);?></strong>
                    </div>
                </div>
                <div class="row">
                	<div class="col-md-6">
                    	<div class="title-item">Payment Made:</div>
                     </div>
                    <div class="col-md-6">   
                    	<strong> <?php echo number_format($total_payments,2);?></strong>
                    </div>
                </div>
                 <div class="row" style="border-top:2px solid #000">
                	<div class="col-md-6">
                    	<div class="title-item">Balance:</div> 
                    </div>
                    <div class="col-md-6">                       
                    	<strong><?php echo number_format(($total_amount + $debit_note_pesa) - ($total_payments + $previous_payment + $credit_note_pesa),2);?></strong>
                    </div>
                </div>
            	
            </div>
        </div>
        <div class="row receipt_bottom_border" style="margin-top: 10px;">
        	<div class="center-align">
        		<h3>FISCAL RECEIPT</h3>
        		<div class="row" style="font-style:italic; font-size:10px;">
		        	<div >
		            	Served by: <?php echo $served_by; ?>
		            </div>
		        	<div >
		            	<?php echo $today; ?> Thank you
		            </div>
		        </div>
        	</div>
        	
        </div>
        
    	
    </body>
    
</html>