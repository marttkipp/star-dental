<?php

$row = $query->row();
$invoice_date = date('jS M Y H:i a',strtotime($row->debtor_invoice_created));
$debtor_invoice_id = $row->debtor_invoice_id;
// $visit_type_name = $row->visit_type_name;
$visit_type_id = $row->visit_type_id;
//$patient_insurance_number = $row->patient_insurance_number;
$batch_no = $row->batch_no;
$status = $row->debtor_invoice_status;
$personnel_id = $row->debtor_invoice_created_by;
$date_from = date('jS M Y',strtotime($row->date_from));
$date_to = date('jS M Y',strtotime($row->date_to));
$total_invoiced = number_format($this->reports_model->calculate_debt_total($debtor_invoice_id, $where, $table,$visit_type_id), 2);
// var_dump($total_invoiced); die();

//creators and editors
if($personnel_query->num_rows() > 0)
{
	$personnel_result = $personnel_query->result();
	
	foreach($personnel_result as $adm)
	{
		$personnel_id2 = $adm->personnel_id;
		
		if($personnel_id == $personnel_id2)
		{
			$created_by = $adm->personnel_onames.' '.$adm->personnel_fname;
			break;
		}
		
		else
		{
			$created_by = '-';
		}
	}
}

else
{
	$created_by = '-';
}
?>
<div class="row">
    <div class="col-md-12">
        <section class="panel panel-featured">
            <header class="panel-heading">
            	<h2 class="panel-title">Debtor's reports for </h2>
            </header>             
        
            <!-- Widget content -->
            <div class="panel-body">
            	
                <div class="row">
                    <div class="col-md-12"> 
                        <h5 class="center-align">Invoice for services rendered between <?php echo $date_from;?> and <?php echo $date_to;?> as per the attached invoices</h5>
                    </div>
                </div>
            	
                <div class="row">
                    <div class="col-md-12"> 
                        <a href="<?php echo site_url('accounts/insurance-invoices/'.$visit_type_id)?>" class="btn btn-sm btn-info pull-right">Back to debtors</a>
                        <a href="<?php echo site_url('administration/reports/invoice/'.$debtor_invoice_id)?>" class="btn btn-sm btn-success pull-right" target="_blank" style="margin-right:10px;">Print</a>
                    </div>
                </div>
            	
                <div class="row">
                    <div class="col-md-12"> 
                        <table class="table table-hover table-bordered col-md-12">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice Date</th>
                                    <th>Member Number</th>
                                    <th>Patient</th>
                                    <th>Invoice Number</th>
                                    <th>Total Cost</th>
                                    <th colspan="2">Actions</th>
                                </tr>
                            </thead>
                            
                            <tbody> 
                                <?php
                                $total_amount = 0;
                                $total_waiver = 0;
                                $total_payments = 0;
                                $total_invoice = 0;
                                $total_balance = 0;
                                $total_rejected_amount = 0;
                                $total_cash_balance = 0;
                                $total_insurance_payments =0;
                                $total_insurance_invoice =0;
                                $total_payable_by_patient = 0;
                                $total_payable_by_insurance = 0;
                                if($debtor_invoice_items->num_rows() > 0)
                                {
                                    $count = 0;
                                    foreach($debtor_invoice_items->result() as $res)
                                    {
                                        $count++;
                                        // $invoice_amount = $res->invoice_amount;
                                        $patient_surname = $res->patient_surname;
                                        $patient_othernames = $res->patient_othernames;
                                        $patient_number = $res->patient_number;
                                        $patient_insurance_number = $res->patient_insurance_number;
                                        $current_patient_number = $res->current_patient_number;
										$debtor_invoice_item_status = $res->debtor_invoice_item_status;
										$debtor_invoice_item_id = $res->debtor_invoice_item_id;
                                        $rejected_amount = $res->rejected_amount;
                                        $invoice_number = $res->invoice_number;
                                        $visit_type_id  = $visit_type = $res->visit_type;
                                        $visit_id = $res->visit_id;
                                         $parent_visit = $res->parent_visit;
                                         $invoice_amount = $res->invoice_amount;
                                        $visit_date = date('jS F Y',strtotime($res->visit_date));
                              
										
										if($debtor_invoice_item_status == 1)
										{
											$buttons = '<a href="'.site_url().'administration/reports/activate_debtor_invoice_item/'.$debtor_invoice_item_id.'/'.$debtor_invoice_id.'" class="btn btn-sm btn-success"onclick="return confirm(\'Do you want to activate invoice '.$patient_insurance_number.'?\');">Activate</a>';
										}
										else if($debtor_invoice_item_status == 0)
										{
											$buttons = '<a href="'.site_url().'administration/reports/deactivate_debtor_invoice_item/'.$debtor_invoice_item_id.'/'.$debtor_invoice_id.'" class="btn btn-sm btn-danger"onclick="return confirm(\'Do you want to deactivate invoice '.$patient_insurance_number.'?\');">Deactivate</a>';
										}
										else
										{
											$buttons = '';
										}


                                            // $payments_value = $this->accounts_model->total_payments($visit_id);

                                            // $invoice_total = $amount_payment = $this->accounts_model->total_invoice($visit_id);


                                            // $rs_rejection = $this->dental_model->get_visit_rejected_updates_sum($visit_id,$visit_type);
                                            // $total_rejected = 0;
                                            // if(count($rs_rejection) >0){
                                            //   foreach ($rs_rejection as $r2):
                                            //     # code...
                                            //     $total_rejected = $r2->total_rejected;

                                            //   endforeach;
                                            // }

                                            // $rejected_amount += $total_rejected;



                                            // if($visit_type > 1 AND $total_rejected > 0)
                                            // {
                                            //     $payable_by_patient = $rejected_amount;
                                            //     $payable_by_insurance = $invoice_total - $payments_value;
                                            // }
                                            // else if($visit_type > 1 AND $total_rejected == 0)
                                            // {
                                            //     $payable_by_patient = $invoice_total;
                                            //     $payable_by_insurance = $invoice_total - $payments_value;
                                            // }
                                            // else
                                            // {
                                            //     $payable_by_patient = $invoice_total - $payments_value;
                                            //     $payable_by_insurance = 0;
                                            // }
                                            // $balance  = $this->accounts_model->balance($payments_value,$invoice_total);
                                            // $total_insurance_payments += $payments_value;
                                            // $total_balance += $payable_by_insurance;
                                            // // $total_rejected_amount += $billed_amount;               
                                            // $total_invoice += $invoice_total;
                                            // $total_payable_by_insurance += $payable_by_insurance;
                                            // $total_payable_by_patient += $payable_by_patient;
                                            
                                            // $balance = ($invoice_total) - ($payments_value);

                                            if($invoice_amount > 0)
                                            {
                                                
                                            $total_balance += $invoice_amount;
                                            }

                                            
                                            $count++;
                                            
                                            //payment data
                                            $charges = '';
                                            
                                            


                                        $print_invoice = '<a href="'.site_url().'accounts/print_invoice_new/'.$visit_id.'" class="btn btn-sm btn-success" target="_blank">Print Invoice</a>';
                                        ?>
                                        <tr>
                                            <td><?php echo $count;?></td>
                                            <td><?php echo $visit_date;?></td>
                                            <td><?php echo $patient_insurance_number;?></td>
                                            <td><?php echo $patient_surname;?> <?php echo $patient_othernames;?></td>
                                            <td><?php echo $invoice_number; ?></td>
                                            <td><?php echo number_format($invoice_amount, 2);?></td>
                                            <td><?php echo $buttons;?></td>
                                            <td><?php echo $print_invoice;?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                <tr>
                                    <th colspan="5" align="right">Total</th>
                                    <th><?php echo number_format($total_balance, 2);?></th>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        
            <div class="widget-foot">
                    
				<?php if(isset($links)){echo $links;}?>
            
        	</div>
        </section>
        <!-- Widget ends -->
    
    </div>
</div>