<?php

	$creditor_id = $this->session->userdata('payment_creditor_id_searched');
	$all_leases = $this->creditors_model->get_creditor($creditor_id);
  	foreach ($all_leases->result() as $leases_row)
  	{
  		$creditor_id = $leases_row->creditor_id;
  		$creditor_name = $leases_row->creditor_name;
      $opening_balance = $leases_row->opening_balance;
  	}
  $expense_accounts = $this->purchases_model->get_child_accounts("Expense Accounts");

  $creditor_invoices = $this->creditors_model->get_creditor_invoice_number($creditor_id);

  $creditor_payment_details = $this->creditors_model->get_creditor_payment_details($creditor_payment_id);

  if($creditor_payment_details->num_rows() > 0)
  {
  	foreach ($creditor_payment_details->result() as $key => $value) {
  		# code...

  		$total_amount = $value->total_amount;
  		$transaction_date = $value->transaction_date;
  		$reference_number = $value->reference_number;
  		$document_number = $value->document_number;
  		$account_from_id = $value->account_from_id;

  	}
  }

?>
<div class="row">
  <section class="panel">
      <header class="panel-heading">
          <h3 class="panel-title">Edit Payment </h3>
          <div class="widget-tools">
                <a href="<?php echo site_url();?>creditor-statement/<?php echo $creditor_id?>" class="btn btn-sm btn-warning pull-right" style="margin-top:-25px;"><i class="fa fa-arrow-left"></i> Back to creditor statement</a>
            </div>
      </header>
      <div class="panel-body">
        <?php echo form_open("finance/creditors/add_payment_item/".$creditor_id.'/'.$creditor_payment_id, array("class" => "form-horizontal"));?>


              <input type="hidden" name="type_of_account" value="1">
              <input type="hidden" name="creditor_id" id="lease_id" value="<?php echo $creditor_id?>">
              <input type="hidden" name="redirect_url" value="<?php echo $this->uri->uri_string()?>">

              <div class="col-md-12">
                <div class="col-md-5">
                  <div class="form-group">
                    <label class="col-md-3 control-label">Invoice Number: </label>
                    <div class="col-md-8">
                      <select class="form-control  " name="invoice_id" id="invoice_id" required>
                        <option value="0">--- select an invoice - ---</option>
                        <?php

                        if($creditor_invoices->num_rows() > 0)
                        {
                          foreach ($creditor_invoices->result() as $key => $value) {
                            // code...
                            $creditor_invoice_id = $value->creditor_invoice_id;
                            $invoice_number = $value->invoice_number;
                            $creditor_invoice_type = $value->creditor_invoice_type;
                            $balance = $value->balance;



                            if($creditor_invoice_type == "Supplies Invoice")
                            {
                              $invoice_type = 1;
                            }
                            else if($creditor_invoice_type == "Opening Balance")
                            {
                              $invoice_type = 2;
                            }
                            else
                            {
                              $invoice_type = 0;
                            }

                            // var_dump($creditor_invoice_id);die();
                            if($balance > 0)
                            {
                               echo '<option value="'.$creditor_invoice_id.'.'.$invoice_number.'.'.$invoice_type.'"> #'.$invoice_number.' kes.'.number_format($balance,2).'</option>';
                            }
                           
                          }
                        }
                        ?>
                        <option value="0.0.3">--- on account - ---</option>

                      </select>
                      </div>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                    <label class="col-md-3 control-label">Amount: </label>
                    <div class="col-md-8">
                      <input type="text" class="form-control" name="amount_paid" id="amount_paid" placeholder=""  autocomplete="off" required>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <button class="btn btn-info" type="submit">Add Payment Item </button>
                  </div>
                </div>

              </div>



          <?php echo form_close();?>




    <?php echo form_open("finance/creditors/confirm_payment/".$creditor_id.'/'.$creditor_payment_id, array("class" => "form-horizontal"));?>

      <?php
      $invoice_where = 'creditor_payment_item.creditor_id = '.$creditor_id.' AND creditor_payment_id ='.$creditor_payment_id;
      $invoice_table = 'creditor_payment_item';
      $invoice_order = 'creditor_payment_item_id';

      $invoice_query = $this->creditors_model->get_creditors_list($invoice_table, $invoice_where, $invoice_order);

      $result_payment ='<hr><table class="table table-bordered table-striped table-condensed">
                          <thead>
                            <tr>
                              <th >#</th>
                              <th >Type</th>
                              <th >Invoice Number</th>
                              <th >Amount Paid</th>
                              <th colspan="1" >Action</th>
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
                                  <td>'.form_checkbox($checkbox_data).'<label for="checkbox'.$creditor_payment_item_id.'" name="checkbox79_lbl" class="css-label lrg klaus"></label>'.'</td>
                                  <td>'.$x.'</td>
                                  <td>'.$type.'</td>
                                  <td>'.$account_name.'</td>
                                  <td>'.number_format($amount,2).'</td>
                                  <td><a href="'.site_url().'delete-creditor-payment-item/'.$creditor_payment_item_id.'/'.$creditor_id.'/'.$creditor_payment_id.'" onclick="return confirm("Do you want to remove this entry ? ")" type="submit" class="btn btn-sm btn-danger" ><i class="fa fa-trash"></i></a></td>
                              </tr>';
        }

        // display button

        $display = TRUE;
      }
      else {
        $display = TRUE;
      }

      $result_payment .='</tbody>
                      </table>';
      ?>

      <?php echo $result_payment;?>

      <br>
      <?php
      if($display)
      {
        ?>
        <div class="row">
          <div class="col-md-12">
              <div class="col-md-6">
              </div>
              <div class="col-md-6">
                  <!-- <h2 class="pull-right"> KES. <?php echo number_format($total_amount,2);?></h2> -->

                  <input type="hidden" name="type_of_account" value="1">
                  <input type="hidden" name="creditor_id" id="creditor_id" value="<?php echo $creditor_id;?>">
                  <input type="hidden" name="redirect_url" value="<?php echo $this->uri->uri_string()?>">
                  <div class="form-group">
                      <label class="col-md-4 control-label">Payment Account: </label>
                      <div class="col-md-8">
                        <select class="form-control select2" name="account_from_id" id="account_from_id"  required>
                          <option value="">---- select a payment account --- </option>
                          <?php
                            $accounts = $this->purchases_model->get_child_accounts("Bank");
                            if($accounts->num_rows() > 0)
                            {
                              foreach ($accounts->result() as $key => $value) {
                                // code...
                                $account_id = $value->account_id;
                                $account_name = $value->account_name;

                                if($account_from_id == $account_id)
                                {
                                	echo '<option value="'.$account_id.'" selected> '.$account_name.'</option>';
                                }
                                else
                                {
                                	echo '<option value="'.$account_id.'"> '.$account_name.'</option>';
                                }
                              }
                            }
                          ?>
                        </select>
                      </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Reference Number: </label>
                    <div class="col-md-8">
                      <input type="text" class="form-control" name="reference_number" id="reference_number" placeholder=""  autocomplete="off" value="<?php echo $reference_number?>" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Total Amount: </label>

                    <div class="col-md-8">
                      <input type="number" class="form-control" name="amount_paid" placeholder=""  autocomplete="off" value="<?php echo $total_amount;?>" readonly>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Payment Date: </label>

                    <div class="col-md-8">
                       <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date" placeholder="Credit Note Date" id="datepicker" value="<?php echo $transaction_date?>" required>
                        </div>
                    </div>
                  </div>

                <div class="col-md-12">
                    <div class="text-center">
                      <button class="btn btn-info btn-sm " type="submit" onclick="return confirm('Are you sure you want to update payment details ? ')">Update Payment Details </button>
                    </div>
                </div>
              </div>

          </div>
        </div>
        <?php
      }
      ?>

      </div>
      <?php echo form_close();?>
    </section>
</div>