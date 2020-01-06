<?php
 $creditor_id = $this->session->userdata('credit_note_creditor_id_searched');
 $expense_accounts = $this->purchases_model->get_child_accounts("Expense Accounts");

  $creditor_invoice_details = $this->creditors_model->get_creditor_credit_note_details($creditor_credit_note_id);


   if($creditor_invoice_details->num_rows() > 0)
   {
   		foreach ($creditor_invoice_details->result() as $key => $value) {
   			# code...
   			$amount = $value->amount;
   			$vat_charged = $value->vat_charged;
   			$total_amount = $value->total_amount;
   			$transaction_date = $value->transaction_date;
   			$invoice_number = $value->invoice_number;
   			$document_number = $value->document_number;
   			$creditor_invoice_id = $value->creditor_invoice_id;

   		}


   }
?>
<div class="row">
  <section class="panel">
      <header class="panel-heading">
          <h3 class="panel-title">Add Credit Note </h3>
          <div class="pull-right">
             <a href="<?php echo site_url().'creditor-statement/'.$creditor_id?>" style="margin-top:-40px;" class="btn btn-sm btn-warning "><i class="fa fa-arrow-left"></i> Back to creditor statement </a>
          </div>
      </header>
      <div class="panel-body">
        <?php echo form_open("finance/creditors/add_credit_note_item/".$creditor_id.'/'.$creditor_credit_note_id, array("class" => "form-horizontal"));?>
          <div class="modal-body">

              <input type="hidden" name="type_of_account" value="1">
              <input type="hidden" name="creditor_id" id="lease_id" value="<?php echo $creditor_id?>">
              <input type="hidden" name="redirect_url" value="<?php echo $this->uri->uri_string()?>">

              <div class="col-md-12">
                <div class="col-md-6">
                     <div class="form-group">
                      <label class="col-md-3 control-label">Description: </label>
                      <div class="col-md-8">
                        <textarea class="form-control" name="description" autocomplete="off"></textarea>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-md-3 control-label">Amount: </label>
                      <div class="col-md-8">
                        <input type="number" class="form-control" name="amount" id="amount" placeholder=""  autocomplete="off" required>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">

                    <div class="form-group">
                      <label class="col-md-3 control-label">TAX: </label>
                      <div class="col-md-8">
                        <select class="form-control " name="tax_type_id" id="tax_type_id"  required>
                          <option value="0">No VAT</option>
                          <option value="1">16% VAT</option>
                          <option value="2">5% Withholding</option>
                        </select>
                        </div>
                    </div>
                    <div class="form-group">
                      <label class="col-md-3 control-label">Account: </label>
                      <div class="col-md-8">
                        <select class="form-control  select2" name="account_to_id" id="account_to_id" required>
                          <option value="">--- select an expense account - ---</option>
                          <?php
                          if($expense_accounts->num_rows() > 0)
                          {
                            foreach ($expense_accounts->result() as $key => $value) {
                              // code...
                              $account_id = $value->account_id;
                              $account_name = $value->account_name;
                              echo '<option value="'.$account_id.'"> '.$account_name.'</option>';
                            }
                          }
                          ?>
                        </select>
                        </div>
                    </div>


                   

                  </div>

              </div>
              <br>
              <div class="center-align">
                <button class="btn btn-sm btn-info center-align" type="submit">Add Credit Note Item </button>
              </div>
                

        </div>


          <?php echo form_close();?>

      <?php echo form_open("finance/creditors/confirm_credit_note/".$creditor_id.'/'.$creditor_credit_note_id, array("class" => "form-horizontal"));?>
      <?php
      $invoice_where = 'creditor_credit_note_item.creditor_id = '.$creditor_id.' AND creditor_credit_note_item.account_to_id = account.account_id AND creditor_credit_note_id = '.$creditor_credit_note_id;
      $invoice_table = 'creditor_credit_note_item,account';
      $invoice_order = 'creditor_credit_note_item_id';

      $invoice_query = $this->creditors_model->get_creditors_list($invoice_table, $invoice_where, $invoice_order);

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
          $creditor_credit_note_item_id = $value->creditor_credit_note_item_id;
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
                    'name'        => 'creditor_notes_items[]',
                    'id'          => 'checkbox'.$creditor_credit_note_item_id,
                    'class'          => 'css-checkbox  lrg ',
                    'checked'=>'checked',
                    'value'       => $creditor_credit_note_item_id
                  );
          $x++;
          // $result_payment .= form_open("accounts/update_invoice_item/".$creditor_credit_note_item_id."/".$creditor_id, array("class" => "form-horizontal"));
          $result_payment .= '<tr>
                                   <td>'.form_checkbox($checkbox_data).'<label for="checkbox'.$creditor_credit_note_item_id.'" name="checkbox79_lbl" class="css-label lrg klaus"></label>'.'</td>
                                  <td>'.$x.'</td>

                                  <td>'.$item_description.'</td>
                                  <td>'.$account_name.'</td>
                                  <td>'.$vat.'</td>
                                  <td>'.number_format($vat_amount,2).'</td>
                                  <td>'.number_format($amount,2).'</td>
                                  <td><a href="'.site_url().'delete-credit-note-item/'.$creditor_credit_note_item_id.'/'.$creditor_credit_note_id.'" type="submit" class="btn btn-sm btn-danger" ><i class="fa fa-trash"></i></a></td>
                              </tr>';
          // $result_payment .=form_close();
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
                    <label class="col-md-4 control-label">Credit Note Number: </label>

                    <div class="col-md-7">
                      <input type="text" class="form-control" name="credit_note_number" placeholder="" value="<?php echo $invoice_number?>"  autocomplete="off" value="">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Invoice Number: </label>
                    <div class="col-md-7">
                      <select class="form-control  select2" name="invoice_id" id="invoice_id" required>
                        <option value="0">--- select an invoice - ---</option>
                        <?php
                        $creditor_invoices = $this->creditors_model->get_creditor_invoice($creditor_id);
                        if($creditor_invoices->num_rows() > 0)
                        {
                          foreach ($creditor_invoices->result() as $key => $value) {
                            // code...
                            $invoice_id = $value->creditor_invoice_id;
                            $invoice_number = $value->invoice_number;

                            if($creditor_invoice_id == $invoice_id)
                            {
                            	echo '<option value="'.$creditor_invoice_id.'" selected> '.$invoice_number.'</option>';	
                            }
                            else
                            {
                            	echo '<option value="'.$creditor_invoice_id.'"> '.$invoice_number.'</option>';
                            }
                            
                          }
                        }
                        ?>
                      </select>
                      </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Total Amount: </label>

                    <div class="col-md-7">
                      <input type="number" class="form-control" name="amount" placeholder=""  autocomplete="off" value="<?php echo $total_amount - $total_vat_amount;?>" readonly>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Total Tax: </label>

                    <div class="col-md-7">
                      <input type="number" class="form-control" name="vat_charged" placeholder=""  autocomplete="off" value="<?php echo $total_vat_amount;?>" readonly>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Total Amount: </label>

                    <div class="col-md-7">
                      <input type="number" class="form-control" name="amount_charged" placeholder=""  autocomplete="off" value="<?php echo $total_amount;?>" readonly>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Credit Note Date: </label>

                    <div class="col-md-7">
                       <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="credit_note_date" placeholder="Credit Note Date" id="datepicker" value="<?php echo $transaction_date?>" required>
                        </div>
                    </div>
                  </div>

                <div class="col-md-12">
                    <div class="text-center">
                      <button class="btn btn-info btn-sm " type="submit" onclick="return confirm('Are you sure you want to update the credit note ? ')">Update Credit Note </button>
                    </div>
                </div>
              </div>
            
          </div>
        </div>
        <?php
      }
      ?>

      <?php echo form_close();?>

    </div>
  </section>
</div>
