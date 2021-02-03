<?php
if($lender_id > 0)
{

  $all_leases = $this->debtors_model->get_lender($lender_id);
  	foreach ($all_leases->result() as $leases_row)
  	{
  		$lender_id = $leases_row->lender_id;
  		$lender_name = $leases_row->lender_name;
      $opening_balance = $leases_row->opening_balance;
  	}
  $expense_accounts = $this->purchases_model->get_child_accounts("Expense Accounts");

  $lender_invoices = $this->debtors_model->get_lender_invoice_number($lender_id);
?>
<div class="col-md-3">
  <section class="panel">

      <div class="panel-body">
          <div class="box-body box-profile">
      <!-- <img class="profile-user-img img-responsive img-circle" src="../../dist/img/user4-128x128.jpg" alt="User profile picture"> -->

      <h3 class="profile-username text-center"><?php echo strtoupper($lender_name);?></h3>

      <p class="text-muted text-center"><?php echo strtoupper('Debtor Credit Notes');?></p>

      <ul class="list-group list-group-unbordered">

        <li class="list-group-item">
          <b>Opening balance</b> <a class="pull-right">0</a>
        </li>
        <li class="list-group-item">
          <b>Total Invoices</b> <a class="pull-right">0</a>
        </li>
        <li class="list-group-item">
          <b>Total Payments</b> <a class="pull-right">0</a>
        </li>
        <li class="list-group-item">
          <b>Account Balance</b> <a class="pull-right">0</a>
        </li>
      </ul>

      <a href="<?php echo site_url().'lender-statement/'.$lender_id;?>" class="btn btn-warning btn-block"><b><i class="fa fa-arrow-left"></i> Back to statement</b></a>
    </div>
    <!-- /.box-body -->
      </div>
  </section>
</div>
<div class="col-md-9">
  <section class="panel">
      <header class="panel-heading">
          <h3 class="panel-title">Add Payment </h3>
      </header>
      <div class="panel-body">
        <?php echo form_open("finance/debtors/add_payment_item/".$lender_id."/".$lender_payment_id, array("class" => "form-horizontal"));?>


              <input type="hidden" name="type_of_account" value="1">
              <input type="hidden" name="lender_id" id="lease_id" value="<?php echo $lender_id?>">
              <input type="hidden" name="redirect_url" value="<?php echo $this->uri->uri_string()?>">

              <div class="col-md-12">
                <div class="col-md-5">
                  <div class="form-group">
                    <label class="col-md-3 control-label">Invoice Number: </label>
                    <div class="col-md-8">
                      <select class="form-control  " name="invoice_id" id="invoice_id" required>
                        <option value="0">--- select an invoice - ---</option>
                        <?php

                        if($lender_invoices->num_rows() > 0)
                        {
                          foreach ($lender_invoices->result() as $key => $value) {
                            // code...
                            $lender_invoice_id = $value->lender_invoice_id;
                            $invoice_number = $value->invoice_number;
                            $lender_invoice_type = $value->lender_invoice_type;
                            $balance = $value->balance;



                            if($lender_invoice_type == "Supplies Invoice")
                            {
                              $invoice_type = 1;
                            }
                            else if($lender_invoice_type == "Opening Balance")
                            {
                              $invoice_type = 2;
                            }
                            else
                            {
                              $invoice_type = 0;
                            }

                            // var_dump($lender_invoice_id);die();
                            if($balance > 0)
                            {
                               echo '<option value="'.$lender_invoice_id.'.'.$invoice_number.'.'.$invoice_type.'"> #'.$invoice_number.' kes.'.number_format($balance,2).'</option>';
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




    <?php echo form_open("finance/debtors/confirm_payment/".$lender_id."/".$lender_payment_id, array("class" => "form-horizontal"));?>

      <?php
      $invoice_where = 'lender_payment_item.lender_id = '.$lender_id.' AND lender_payment_item.lender_payment_id = '.$lender_payment_id;
      $invoice_table = 'lender_payment_item';
      $invoice_order = 'lender_payment_item_id';

      $invoice_query = $this->debtors_model->get_debtors_list($invoice_table, $invoice_where, $invoice_order);

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
          $lender_payment_item_id_db = $value->lender_payment_item_id;
          $invoice_type = $value->invoice_type;

          $color = "default";

          if($invoice_type == 0)
          {
            $type = "lender Bill";
            // lender invoice
            $lender_invoice_id = $value->lender_invoice_id;
            $invoice_where = 'lender_invoice.lender_id = '.$lender_id.' AND lender_invoice_id = '.$lender_invoice_id;
            $invoice_table = 'lender_invoice';
            $invoice_order = 'lender_invoice_id';

            $invoice_items = $this->debtors_model->get_debtors_list($invoice_table, $invoice_where, $invoice_order);
            $invoice_things = $invoice_items->row();

            $account_name = $invoice_things->invoice_number;
          }
          else if($invoice_type == 1)
          {
            $type = "Supplies Invoice";
              // lender invoice
              $lender_invoice_id = $value->lender_invoice_id;
              $invoice_where = 'orders.supplier_id = '.$lender_id.' AND order_id = '.$lender_invoice_id;
              $invoice_table = 'orders';
              $invoice_order = 'order_id';

              $invoice_items = $this->debtors_model->get_debtors_list($invoice_table, $invoice_where, $invoice_order);
              $invoice_things = $invoice_items->row();
              $account_name = $invoice_things->supplier_invoice_number;

          }

          else if($invoice_type == 2)
          {
            $type = "On opening balance";
              // lender invoice
              $lender_invoice_id = $value->lender_invoice_id;

              $account_name = '';

          }

          else if($invoice_type == 3)
          {
            $type = "On account";
             $color = "warning";
              // lender invoice
              $lender_invoice_id = $value->lender_invoice_id;

              $account_name = '';

          }
          $amount = $value->amount_paid;
          $total_amount += $amount;
          $checkbox_data = array(
                    'name'        => 'lender_payments_items[]',
                    'id'          => 'checkbox'.$lender_payment_item_id,
                    'class'          => 'css-checkbox  lrg ',
                    'checked'=>'checked',
                    'value'       => $lender_payment_item_id
                  );

          $x++;
          $result_payment .= '<tr class="'.$color.'">
                                  <td>'.form_checkbox($checkbox_data).'<label for="checkbox'.$lender_payment_item_id.'" name="checkbox79_lbl" class="css-label lrg klaus"></label>'.'</td>
                                  <td>'.$x.'</td>
                                  <td>'.$type.'</td>
                                  <td>'.$account_name.'</td>
                                  <td>'.number_format($amount,2).'</td>
                                  <td><a href="'.site_url().'delete-payment-item/'.$lender_payment_id.'/'.$lender_payment_item_id.'/'.$lender_payment_item_id_db.'/'.$lender_id.'" onclick="return confirm("Do you want to remove this entry ? ")" type="submit" class="btn btn-sm btn-danger" ><i class="fa fa-trash"></i></a></td>
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

      <br>
      <?php
      if($display)
      {

      	$lender_payment_where = 'lender_payment_id = '.$lender_payment_id;
		$lender_payment_table = 'lender_payment';
		$lender_payment_order = 'lender_payment_id';

		$lender_payment_query = $this->debtors_model->get_debtors_list($lender_payment_table, $lender_payment_where, $lender_payment_order);

		$lender_payment_rs = $lender_payment_query->row();
		$account_from_id = $lender_payment_rs->account_from_id;
		$reference_number = $lender_payment_rs->reference_number;
		$total_amount = $lender_payment_rs->total_amount;
		$transaction_date = $lender_payment_rs->transaction_date;

		// var_dump($account_from_id);die();
        ?>
        <div class="row">
          <div class="col-md-12">
              <div class="col-md-6">
              </div>
              <div class="col-md-6">
                  <!-- <h2 class="pull-right"> KES. <?php echo number_format($total_amount,2);?></h2> -->

                  <input type="hidden" name="type_of_account" value="1">
                  <input type="hidden" name="lender_id" id="lender_id" value="<?php echo $lender_id;?>">
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

                                if($account_id === $account_from_id)
                                {
                                	echo '<option value="'.$account_id.'" selected="selected"> '.$account_name.'</option>';
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
                      <input type="text" class="form-control" name="reference_number" id="reference_number" placeholder="" value="<?php echo $reference_number;?>" autocomplete="off" required>
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
                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date" placeholder="Transaction Date" id="datepicker" value="<?php echo $transaction_date?>" required>
                        </div>
                    </div>
                  </div>

                <div class="col-md-12">
                    <div class="text-center">
                      <button class="btn btn-info btn-sm " type="submit" onclick="return confirm('Are you sure you want to complete this transaction ? ')">Complete Payment </button>
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
<?php
$error = $this->session->userdata('error_message');
$success = $this->session->userdata('success_message');

if(!empty($error))
{
  echo '<div class="alert alert-danger">'.$error.'</div>';
  $this->session->unset_userdata('error_message');
}

if(!empty($success))
{
  echo '<div class="alert alert-success">'.$success.'</div>';
  $this->session->unset_userdata('success_message');
}

?>

<div class="row">
  <div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <h3 class="panel-title">lender Payments </h3>
        </header>
        <div class="panel-body">
            <table class="table table-hover table-bordered col-md-12">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Payment Date</th>
                  <th>Payment Account</th>
                  <th>Reference Number</th>
                  <th>Document Number</th>
                  <th>Total Amount</th>
                  <!-- <th colspan="1">Actions</th> -->
                </tr>
              </thead>
              <tbody>
                <?php
                	$lender_invoices = $this->debtors_model->get_lender_payments($lender_id,10);
                  // var_dump($tenant_query);die();
                if($lender_invoices->num_rows() > 0)
                {
                  $y = 0;
                  foreach ($lender_invoices->result() as $key) {
                    # code...
                    $total_amount = $key->total_amount;
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

                    ?>
                    <tr>
                      <td><?php echo $y?></td>
                      <td><?php echo $transaction_date;?></td>
                      <td><?php echo $account_name?></td>
                      <td><?php echo $reference_number?></td>
                      <td><?php echo $document_number?></td>
                      <td><?php echo number_format($total_amount,2);?></td>
                      <!-- <td><a href="<?php echo site_url().'cash-office/print-invoice-note/'.$lender_payment_id.'/'.$lender_id;?>" class="btn btn-sm btn-primary" target="_blank">Credit Note</a></td> -->

                    </tr>
                    <?php

                  }
                }
                ?>

              </tbody>
            </table>

        </div>
      </section>
    </div>
  </div>
<?php



}

?>

<script>
function display_payment_model()
{
  $('#modal-defaults').modal('show');
  $('#datepicker').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
  })
  // var quantity = document.getElementById("quantity");
}
function get_value()
{
  var quantity = document.getElementById("quantity").value;
  var unit_price = document.getElementById("unit_price").value;
  var tax_type_id = document.getElementById("tax_type_id").value;

  var url = "<?php echo base_url();?>finance/debtors/calculate_value";
   $.ajax({
   type:'POST',
   url: url,
   data:{quantity: quantity,unit_price : unit_price, tax_type_id : tax_type_id},
   dataType: 'text',
   success:function(data){
     var data = jQuery.parseJSON(data);
     var amount = data.amount;
      var vat = data.vat;
      $( "#vat_amount" ).html("<h4>TAX KES. "+ vat +" </h4>");
      $( "#total_units" ).html("<h3>TOTAL : KES. "+ amount +" </h3>");
     document.getElementById("input-total-value").value = amount;
     document.getElementById("vat-amount").value = vat;

   },
   error: function(xhr, status, error) {
   alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);

   }
   });

}
function check_payment_type(payment_type_id){

  var myTarget1 = document.getElementById("cheque_div");

  var myTarget2 = document.getElementById("mpesa_div");

  var myTarget3 = document.getElementById("insuarance_div");

  if(payment_type_id == 1)
  {
    // this is a check

    myTarget1.style.display = 'block';
    myTarget2.style.display = 'none';
    myTarget3.style.display = 'none';
  }
  else if(payment_type_id == 2)
  {
    myTarget1.style.display = 'none';
    myTarget2.style.display = 'none';
    myTarget3.style.display = 'none';
  }
  else if(payment_type_id == 3)
  {
    myTarget1.style.display = 'none';
    myTarget2.style.display = 'none';
    myTarget3.style.display = 'block';
  }
  else if(payment_type_id == 4)
  {
    myTarget1.style.display = 'none';
    myTarget2.style.display = 'none';
    myTarget3.style.display = 'none';
  }
  else if(payment_type_id == 5)
  {
    myTarget1.style.display = 'none';
    myTarget2.style.display = 'block';
    myTarget3.style.display = 'none';
  }
  else
  {
    myTarget2.style.display = 'none';
    myTarget3.style.display = 'block';
  }

}
</script>
