<?php
$creditor_id = $this->session->userdata('payment_creditor_id_searched');
if(empty($creditor_id))
{

  $creditor_where = 'creditor_id > 0' ;
  $creditor_table = 'creditor';
  $creditor_order = 'creditor.creditor_name';

  $creditor_query = $this->creditors_model->get_creditors_list($creditor_table, $creditor_where, $creditor_order);
  $rs8 = $creditor_query->result();
  $creditor_list = '';
  foreach ($rs8 as $creditor_rs) :
    $creditor_id = $creditor_rs->creditor_id;
    $creditor_name = $creditor_rs->creditor_name;

      $creditor_list .="<option value='".$creditor_id."'> ".$creditor_name."</option>";

  endforeach;

  ?>
  <div class="col-md-12">
      <section class="panel">
          <header class="panel-heading">
              <h3 class="panel-title">Search Creditor </h3>
          </header>
          <div class="panel-body">
              <!-- select a tenant  -->
              <div class="row" style="margin-bottom:20px;">
                <?php echo form_open("search-creditor-payments", array("class" => "form-horizontal", "role" => "form"));?>
                    <div class="row">
                      <div class="col-md-12">
                            <div class="col-md-8">
                                  <div class="form-group center-align">
                                    <label class="col-md-4 control-label">Creditor Name: </label>

                                    <div class="col-md-8">
                                      <select id='creditor_id' name='creditor_id' class='form-control select2'>
                                          <!-- <select class="form-control custom-select " id='procedure_id' name='procedure_id'> -->
                                            <option value=''>None - Please Select a creditor</option>
                                            <?php echo $creditor_list;?>
                                          </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                            <div class="form-actions center-align">
                                <button class="submit btn btn-primary btn-sm" type="submit">
                                    Search Creditor
                                </button>
                            </div>
                          </div>

                      </div>
                    </div>
                  <?php echo form_close();?>
              </div>
          </div>
      </section>
    </div>


  <?php
}
?>
<?php
$creditor_id = $lease_id = $this->session->userdata('payment_creditor_id_searched');
if($creditor_id > 0)
{

  $all_leases = $this->creditors_model->get_creditor($creditor_id);
  	foreach ($all_leases->result() as $leases_row)
  	{
  		$creditor_id = $leases_row->creditor_id;
  		$creditor_name = $leases_row->creditor_name;
      $opening_balance = $leases_row->opening_balance;
  	}
  $expense_accounts = $this->purchases_model->get_child_accounts("Expense Accounts");

  $creditor_invoices = $this->creditors_model->get_creditor_invoice_number($creditor_id);
?>

<div class="row">
  <section class="panel">
      <header class="panel-heading">
          <h3 class="panel-title">Add Payment </h3>
          <div class="widget-tools">
                <a href="<?php echo site_url();?>creditor-statement/<?php echo $creditor_id?>" class="btn btn-sm btn-warning pull-right" style="margin-top:-25px;"><i class="fa fa-arrow-left"></i> Back to creditor statement</a>
            </div>
      </header>
      <div class="panel-body">
        <?php echo form_open("finance/creditors/add_payment_item/".$creditor_id, array("class" => "form-horizontal"));?>


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
                            $dr_amount = $value->dr_amount;
                            $cr_amount = $value->cr_amount;
                            $invoice_date = $value->invoice_date;



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

                            if($cr_amount > 0)
                            {
                              $color_checked = 'orange';
                            }
                            else if($cr_amount == 0)
                            {
                              $color_checked = 'red';
                            }
                            else
                            {
                              $color_checked = 'white';
                            }

                            // var_dump($creditor_invoice_id);die();
                            if($balance > 0)
                            {
                               echo '<option value="'.$creditor_invoice_id.'.'.$invoice_number.'.'.$invoice_type.'" style="background:'.$color_checked.';color:white;"> '.$invoice_date.' # '.$invoice_number.' Bill .'.number_format($dr_amount,2).' Payments.('.number_format($cr_amount,2).') Bal.'.number_format($balance,2).'</option>';
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




    <?php echo form_open("finance/creditors/confirm_payment/".$creditor_id, array("class" => "form-horizontal"));?>

      <?php
      $invoice_where = 'creditor_payment_item.creditor_id = '.$creditor_id.' AND creditor_payment_item_status = 0';
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
                                  <td><a href="'.site_url().'delete-creditor-payment-item/'.$creditor_payment_item_id.'/'.$creditor_id.'" onclick="return confirm("Do you want to remove this entry ? ")" type="submit" class="btn btn-sm btn-danger" ><i class="fa fa-trash"></i></a></td>
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
                                echo '<option value="'.$account_id.'"> '.$account_name.'</option>';
                              }
                            }
                          ?>
                        </select>
                      </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Reference Number: </label>
                    <div class="col-md-8">
                      <input type="text" class="form-control" name="reference_number" id="reference_number" placeholder=""  autocomplete="off" required>
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
                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date" placeholder="Credit Note Date" id="datepicker" value="<?php echo date('Y-m-d')?>" required>
                        </div>
                    </div>
                  </div>

                <div class="col-md-12">
                    <div class="text-center">
                      <button class="btn btn-info btn-sm " type="submit">Complete Payment </button>
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
    <section class="panel">
        <header class="panel-heading">
            <h3 class="panel-title">Creditor Payments </h3>
        </header>
        <div class="panel-body">
            <table class="table  table-condensed table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Payment Date</th>
                  <th>Payment Account</th>
                  <th>Reference Number</th>
                  <th>Document Number</th>
                  <th>Status</th>
                  <th>Total Amount</th>
                  <th colspan="2">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                	// $creditor_payments = $this->creditors_model->get_creditor_payments($creditor_id,10);
                  // var_dump($creditor_payments);die();
                if($creditor_payments->num_rows() > 0)
                {
                  $y = $page;
                  foreach ($creditor_payments->result() as $key) {
                    # code...
                    $total_amount = $key->total_amount;
                    $creditor_payment_id = $key->creditor_payment_id;
                    $transaction_date = $key->transaction_date;
                    $document_number = $key->document_number;
                    $reference_number = $key->reference_number;
                    $account_name = $key->account_name;
                    $created = $key->created;
                    $created_by = $key->created_by;

                    $checker = $this->creditors_model->check_on_account($creditor_payment_id);

                    if($checker == TRUE)
                    {
                      $plung = 'Incomplete';
                      $color =  'warning';
                    }
                    else
                    {
                      $plung = 'Complete';
                      $color =  'success';
                    }

                    $payment_explode = explode('-', $transaction_date);

                    $invoice_note_date = date('jS M Y',strtotime($transaction_date));
                    $created = date('jS M Y',strtotime($created));
                    $y++;

                    ?>
                    <tr >
                      <td class="<?php echo $color?>"><?php echo $y?></td>
                      <td class="<?php echo $color?>"><?php echo $transaction_date;?></td>
                      <td class="<?php echo $color?>"><?php echo $account_name?></td>
                      <td class="<?php echo $color?>"><?php echo $reference_number?></td>
                      <td class="<?php echo $color?>"><?php echo $document_number?></td>
                      <td class="<?php echo $color?>"><?php echo number_format($total_amount,2);?></td>
                      <td class="<?php echo $color?>"><?php echo $plung?></td>
                      <td><a href="<?php echo site_url().'edit-creditor-payment/'.$creditor_payment_id;?>" class="btn btn-xs btn-success" ><i class="fa fa-pencil"></i></a></td>
                      <td><a href="<?php echo site_url().'delete-creditor-payment/'.$creditor_payment_id;?>" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want ot delete this payment detail ? ')"> <i class="fa fa-trash"></i></a></td>

                    </tr>
                    <?php

                  }
                }
                ?>

              </tbody>
            </table>
            <div class="widget-foot">
                                
                <?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>

        </div>
    </section>
  
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

  var url = "<?php echo base_url();?>finance/creditors/calculate_value";
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
