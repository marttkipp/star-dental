<?php



$item_invoiced_rs = $this->accounts_model->get_patient_visit_charge_items($visit_id);
$credit_note_amount = $this->accounts_model->get_sum_credit_notes($visit_id);
$debit_note_amount = $this->accounts_model->get_sum_debit_notes($visit_id);
$payments_rs = $this->accounts_model->get_all_visit_transactions($visit_id);

?>
<div class="row">
   <table class="table table-hover table-bordered col-md-12">
     <thead>
       <tr>
         <th>#</th>
         <th>Service</th>
         <th>Item Name</th>
         <th>Units</th>
         <th>Unit Cost</th>
         <th>Total</th>
       </tr>
     </thead>
     <tbody>
       <?php
       $total = 0;
       $s=0;
       if(count($item_invoiced_rs) > 0)
       {
         foreach ($item_invoiced_rs as $key_items):
           $s++;
           $service_charge_name = $key_items->service_charge_name;
           $visit_charge_amount = $key_items->visit_charge_amount;
           $service_name = $key_items->service_name;
           $units = $key_items->visit_charge_units;
           $visit_total = $visit_charge_amount * $units;
           $personnel_id = $key_items->personnel_id;
           $doctor = '';

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
           ?>
           <tr>
             <td><?php echo $s;?></td>
             <td><?php echo $service_name;?></td>
             <td><?php echo $service_charge_name.$doctor;?></td>
             <td><?php echo $units;?></td>
             <td><?php echo number_format($visit_charge_amount,2);?></td>
             <td><?php echo number_format($visit_total,2);?></td>
           </tr>
           <?php
           $total = $total + $visit_total;
         endforeach;
       }
       $total_amount = $total ;
       // enterring the payment stuff
       $total_payments = 0;
       $total_amount = ($total + $debit_note_amount) - $credit_note_amount;

       if(count($payments_rs) > 0)
       {
         $x = $s;
         foreach ($payments_rs as $key_items):
           $x++;
           $payment_method = $key_items->payment_method;

           $amount_paid = $key_items->amount_paid;
           $time = $key_items->time;
           $payment_type = $key_items->payment_type;
           $amount_paidd = number_format($amount_paid,2);
           $payment_service_id = $key_items->payment_service_id;

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
               <td colspan="3">Debit Note</td>
               <td><?php echo $amount_paidd;?></td>
               <td><?php echo $amount_paidd;?></td>
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
               <td colspan="3">Credit Note</td>
               <td><?php echo $amount_paidd;?></td>
               <td><?php echo $amount_paidd;?></td>
             </tr>
             <?php
           }

         endforeach;
       }
       // end of the payments
       $total_amount = ($total + $debit_note_amount) - $credit_note_amount;
       $total_payments = 0;

       if(count($payments_rs) > 0)
       {
         foreach ($payments_rs as $key_items):
           $payment_method = $key_items->payment_method;

           $time = $key_items->time;
           $payment_type = $key_items->payment_type;
           $payment_id = $key_items->payment_id;
           $payment_status = $key_items->payment_status;
           $payment_service_id = $key_items->payment_service_id;
           $service_name = '';

           if($payment_type == 1 && $payment_status == 1)
           {
             $amount_paid = $key_items->amount_paid;
             $total_payments += $amount_paid;
           }
         endforeach;
       }
       $total_invoice = $total_amount - $total_payments;
       ?>
       <tr>
         <td colspan="5" align="right"><strong>Total Payments:</strong></td>
         <td><strong> <?php echo number_format($total_payments,2);?></strong></td>
       </tr>
       <tr>
         <td colspan="5" align="right"><strong>Total Invoice:</strong></td>
         <td><strong> <?php echo number_format($total_invoice,2);?></strong></td>
       </tr>
       <?php
       /*}
       else
       {
         ?>
         <tr>
           <td colspan="4"> No Charges</td>
         </tr>
         <?php
       }*/

       ?>
     </tbody>
   </table>
 </div>
