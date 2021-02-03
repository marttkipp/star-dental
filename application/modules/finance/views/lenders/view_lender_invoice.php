 <?php
      $invoice_where = 'lender_invoice_item.lender_invoice_id = '.$lender_invoice_id.' AND lender_invoice_item_status = 1 AND account.account_id = lender_invoice_item.account_to_id';
      $invoice_table = 'lender_invoice_item,account';
      $invoice_order = 'lender_invoice_item_id';

      $invoice_query = $this->lenders_model->get_lenders_list($invoice_table, $invoice_where, $invoice_order);

      $result_payment ='<table class="table table-bordered table-striped table-condensed">
                          <thead>
                            <tr>
                              <th >#</th>
                              <th >Desciption</th>
                              <th >Units</th>
                              <th >Unit Price</th>
                              <th >Expense Account</th>
                              <th >TAX Type</th>
                              <th >TAX Amount</th>
                              <th >Total Amount</th>
                            </tr>
                          </thead>
                            <tbody>';
      $total_amount = 0;
      $total_vat_amount = 0;
      if($invoice_query->num_rows() > 0)
      {
        $x = 0;

        foreach ($invoice_query->result() as $key => $value) {
          // code...
          $account_name = $value->account_name;
          $unit_price = $value->unit_price;
          $lender_invoice_item_id = $value->lender_invoice_item_id;
          $quantity = $value->quantity;
          $account_name = $value->account_name;
          $item_description = $value->item_description;
          $vat_type_id = $value->vat_type_id;
          $vat_amount = $value->vat_amount;
           $lender_id = $value->lender_id;
          $amount = $value->total_amount;
          $total_amount += $amount;
          $total_vat_amount += $vat_amount;
          if($vat_type_id == 0)
          {
            $vat = 'No VAT';
          }
          else if($vat_type_id == 1)
          {
            $vat = '16 % VAT';
          }

          else if($vat_type_id == 2)
          {
            $vat = '5 % Withholding TAX';
          }
          $checkbox_data = array(
                    'name'        => 'lender_invoice_items[]',
                    'id'          => 'checkbox'.$lender_invoice_item_id,
                    'class'          => 'css-checkbox  lrg ',
                    'checked'=>'checked',
                    'value'       => $lender_invoice_item_id
                  );

          $x++;
          // $result_payment .= form_open("accounts/update_invoice_item/".$lender_invoice_item_id."/".$lender_id, array("class" => "form-horizontal"));
          $result_payment .= '<tr>
                                  <td>'.$x.'</td>
                                  <td>'.$item_description.'</td>
                                  <td>'.$quantity.'</td>
                                  <td>'.$unit_price.'</td>
                                  <td>'.$account_name.'</td>
                                  <td>'.$vat.'</td>
                                  <td>'.number_format($vat_amount,2).'</td>
                                  <td>'.number_format($amount,2).'</td>
                              </tr>';
          // $result_payment .=form_close();
        }
        $result_payment .= '<tr>
                                  <td colspan="7">Total Invoice</td>
                                  <td ><strong>'.number_format($total_amount,2).'</strong></td>
                              </tr>';

        // display button

        $display = TRUE;
      }
      else {
        $display = FALSE;
      }

      $result_payment .='</tbody>
                      </table>';
      ?>

      <?php echo $result_payment;?>



       <?php
        

      $where = 'lender_payment.lender_payment_id = lender_payment_item.lender_payment_id AND lender_payment.lender_payment_status = 1 AND lender_payment_item.invoice_type = 3 AND lender_payment.account_from_id = account.account_id AND lender_payment_item.lender_invoice_id ='.$lender_invoice_id;
      $table = 'lender_payment,lender_payment_item,account';
      $select = 'lender_payment.*,SUM(lender_payment_item.amount_paid) AS sum_paid,account.account_name';
      $group_by = 'lender_payment.lender_payment_id';


      $lender_payments = $this->lenders_model->get_content($table, $where,$select,$group_by,$limit=NULL);

      $payment_result ='
                      <h4>Payments</h4>
                      <table class="table table-bordered table-striped table-condensed">
                          <thead>
                            <tr>
                              <th >#</th>
                               <th>Payment Date</th>
                                <th>Payment Account</th>
                                <th>Reference Number</th>
                                <th>Document Number</th>
                                <th>Total Amount</th>
                            </tr>
                          </thead>
                            <tbody>';
      $total_amount = 0;
      $total_vat_amount = 0;
       if($lender_payments->num_rows() > 0)
                {
                  $y = 0;
                  foreach ($lender_payments->result() as $key) {
                    # code...
                    $total_amount = $key->sum_paid;
                    $lender_payment_id = $key->lender_payment_id;
                    $transaction_date = $key->transaction_date;
                    $document_number = $key->document_number;
                    $reference_number = $key->reference_number;
                    $account_name = $key->account_name;
                    $created = $key->created;
                    $created_by = $key->created_by;

                   
                    $payment_explode = explode('-', $transaction_date);

                    $invoice_note_date = date('jS M Y',strtotime($transaction_date));
                    $created = date('jS M Y',strtotime($created));
                    $y++;

                    $payment_result .= '<tr >
                                          <td >'.$y.'</td>
                                          <td >'.$transaction_date.'</td>
                                          <td >'.$account_name.'</td>
                                          <td >'.$reference_number.'</td>
                                          <td >'.$document_number.'</td>
                                          <td >'.number_format($total_amount,2).'</td>
                                        </tr>';

            }
          }

      $payment_result .='</tbody>
                      </table>';
      ?>

      <?php echo $payment_result;?>