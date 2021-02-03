<?php
	  $invoice_where = 'creditor_payment_id = '.$creditor_payment_id;
      $invoice_table = 'creditor_payment_item';
      $invoice_order = 'creditor_payment_item_id';

      $invoice_query = $this->creditors_model->get_creditors_list($invoice_table, $invoice_where, $invoice_order);

      $result_payment ='<table class="table table-bordered table-striped table-condensed">
                          <thead>
                            <tr>
                              <th >#</th>
                              <th >Type</th>
                              <th >Invoice Number</th>
                              <th >Amount Paid</th>
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
          $creditor_payment_item_id = $value->creditor_payment_item_id;
          $invoice_type = $value->invoice_type;
          $creditor_id = $value->creditor_id;

          if($invoice_type == 0)
          {
            $type = "Creditor Bill";
            // creditor invoice
            $creditor_invoice_id = $value->creditor_invoice_id;
            $invoice_where = 'creditor_invoice.creditor_id = '.$creditor_id.' AND creditor_invoice_id = '.$creditor_invoice_id;
            $invoice_table = 'creditor_invoice';
            $invoice_order = 'creditor_invoice_id';

            $invoice_items = $this->creditors_model->get_creditors_list($invoice_table, $invoice_where, $invoice_order);
            $invoice_things = $invoice_items->row();

            $account_name = $invoice_things->invoice_number;
          }
          else if($invoice_type == 1)
          {
            $type = "Supplies Invoice";
              // creditor invoice
              $creditor_invoice_id = $value->creditor_invoice_id;
              $invoice_where = 'orders.supplier_id = '.$creditor_id.' AND order_id = '.$creditor_invoice_id;
              $invoice_table = 'orders';
              $invoice_order = 'order_id';

              $invoice_items = $this->creditors_model->get_creditors_list($invoice_table, $invoice_where, $invoice_order);
              $invoice_things = $invoice_items->row();
              $account_name = $invoice_things->supplier_invoice_number;

          }

          else if($invoice_type == 2)
          {
            $type = "On opening balance";
              // creditor invoice
              $creditor_invoice_id = $value->creditor_invoice_id;

              $account_name = '';

          }

          else if($invoice_type == 3)
          {
            $type = "On account";
              // creditor invoice
              $creditor_invoice_id = $value->creditor_invoice_id;

              $account_name = '';

          }
          $amount = $value->amount_paid;
          $total_amount += $amount;
          $checkbox_data = array(
                    'name'        => 'creditor_payments_items[]',
                    'id'          => 'checkbox'.$creditor_payment_item_id,
                    'class'          => 'css-checkbox  lrg ',
                    'checked'=>'checked',
                    'value'       => $creditor_payment_item_id
                  );

          $x++;
          $result_payment .= '<tr>
                                  <td>'.$x.'</td>
                                  <td>'.$type.'</td>
                                  <td>'.$account_name.'</td>
                                  <td>'.number_format($amount,2).'</td>
                              </tr>';
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