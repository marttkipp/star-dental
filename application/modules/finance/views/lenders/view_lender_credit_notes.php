<?php
      $invoice_where = 'lender_credit_note_item.account_to_id = account.account_id AND lender_credit_note_id = '.$lender_credit_note_id;
      $invoice_table = 'lender_credit_note_item,account';
      $invoice_order = 'lender_credit_note_item_id';

      $invoice_query = $this->lenders_model->get_lenders_list($invoice_table, $invoice_where, $invoice_order);

      $result_payment ='<table class="table table-bordered table-striped table-condensed">
                          <thead>
                            <tr>
                              <th ></th>
                              <th >#</th>
                              <th >Desciption</th>
                              <th >Account</th>
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
          $lender_credit_note_item_id = $value->lender_credit_note_item_id;
          $item_description = $value->description;
          $vat_type_id = $value->vat_type_id;
          $account_name = $value->account_name;
          $vat_amount = $value->credit_note_charged_vat;
          $amount = $value->credit_note_amount;
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
                    'name'        => 'lender_notes_items[]',
                    'id'          => 'checkbox'.$lender_credit_note_item_id,
                    'class'          => 'css-checkbox  lrg ',
                    'checked'=>'checked',
                    'value'       => $lender_credit_note_item_id
                  );
          $x++;
          // $result_payment .= form_open("accounts/update_invoice_item/".$lender_credit_note_item_id."/".$lender_id, array("class" => "form-horizontal"));
          $result_payment .= '<tr>
                                   <td>'.form_checkbox($checkbox_data).'<label for="checkbox'.$lender_credit_note_item_id.'" name="checkbox79_lbl" class="css-label lrg klaus"></label>'.'</td>
                                  <td>'.$x.'</td>

                                  <td>'.$item_description.'</td>
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