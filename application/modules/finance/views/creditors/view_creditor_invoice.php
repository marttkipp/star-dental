 <?php
      $invoice_where = 'creditor_invoice_item.creditor_invoice_id = '.$creditor_invoice_id.' AND creditor_invoice_item_status = 1 AND account.account_id = creditor_invoice_item.account_to_id';
      $invoice_table = 'creditor_invoice_item,account';
      $invoice_order = 'creditor_invoice_item_id';

      $invoice_query = $this->creditors_model->get_creditors_list($invoice_table, $invoice_where, $invoice_order);

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
          $creditor_invoice_item_id = $value->creditor_invoice_item_id;
          $quantity = $value->quantity;
          $account_name = $value->account_name;
          $item_description = $value->item_description;
          $vat_type_id = $value->vat_type_id;
          $vat_amount = $value->vat_amount;
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
                    'name'        => 'creditor_invoice_items[]',
                    'id'          => 'checkbox'.$creditor_invoice_item_id,
                    'class'          => 'css-checkbox  lrg ',
                    'checked'=>'checked',
                    'value'       => $creditor_invoice_item_id
                  );

          $x++;
          // $result_payment .= form_open("accounts/update_invoice_item/".$creditor_invoice_item_id."/".$creditor_id, array("class" => "form-horizontal"));
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