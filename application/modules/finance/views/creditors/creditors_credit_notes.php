<?php

$creditor_id = $this->session->userdata('credit_note_creditor_id_searched');
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
            <h3 class="panel-title">Creditor </h3>
        </header>
        <div class="panel-body">
              <!-- select a tenant  -->
              <div class="row" style="margin-bottom:20px;">
                <?php echo form_open("search-creditor-credit-notes", array("class" => "form-horizontal", "role" => "form"));?>
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
$creditor_id = $lease_id = $this->session->userdata('credit_note_creditor_id_searched');
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
?>
<div class="col-md-3">
  <section class="panel">
      <header class="panel-heading">
          <h3 class="panel-title">Add Payment </h3>
      </header>
      <div class="panel-body">
          <div class="box-body box-profile">
      <!-- <img class="profile-user-img img-responsive img-circle" src="../../dist/img/user4-128x128.jpg" alt="User profile picture"> -->

      <h3 class="profile-username text-center"><?php echo strtoupper($creditor_name);?></h3>

      <p class="text-muted text-center"><?php echo strtoupper('Creditor Credit Notes');?></p>

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

      <a href="<?php echo site_url().'close-search-creditors-credit-notes'?>" class="btn btn-warning btn-block"><b>CLOSE SEACH</b></a>
    </div>
    <!-- /.box-body -->
      </div>
    </section>
</div>
<div class="col-md-9">
  <section class="panel">
      <header class="panel-heading">
          <h3 class="panel-title">Add Credit Note </h3>
      </header>
      <div class="panel-body">
        <?php echo form_open("finance/creditors/add_credit_note_item/".$creditor_id, array("class" => "form-horizontal"));?>
          <div class="modal-body">

              <input type="hidden" name="type_of_account" value="1">
              <input type="hidden" name="creditor_id" id="lease_id" value="<?php echo $creditor_id?>">
              <input type="hidden" name="redirect_url" value="<?php echo $this->uri->uri_string()?>">

              <div class="col-md-12">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="col-md-3 control-label">Invoice Number: </label>
                    <div class="col-md-8">
                      <select class="form-control  select2" name="invoice_id" id="invoice_id" required>
                        <option value="0">--- select an invoice - ---</option>
                        <?php
                        $creditor_invoices = $this->creditors_model->get_creditor_invoice($creditor_id);
                        if($creditor_invoices->num_rows() > 0)
                        {
                          foreach ($creditor_invoices->result() as $key => $value) {
                            // code...
                            $creditor_invoice_id = $value->creditor_invoice_id;
                            $invoice_number = $value->invoice_number;
                            echo '<option value="'.$creditor_invoice_id.'"> '.$invoice_number.'</option>';
                          }
                        }
                        ?>
                      </select>
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
                      <label class="col-md-3 control-label">Description: </label>
                      <div class="col-md-8">
                        <textarea class="form-control" name="description" autocomplete="off"></textarea>
                      </div>
                    </div>

                  </div>

              </div>

                <button class="btn btn-info" type="submit">Add Credit Note Item </button>

        </div>


          <?php echo form_close();?>


      <?php
      $invoice_where = 'creditor_credit_note_item.creditor_id = '.$creditor_id.' AND creditor_credit_note_item_status = 0 AND creditor_invoice.creditor_invoice_id = creditor_credit_note_item.creditor_invoice_id';
      $invoice_table = 'creditor_credit_note_item,creditor_invoice';
      $invoice_order = 'creditor_credit_note_item_id';

      $invoice_query = $this->creditors_model->get_creditors_list($invoice_table, $invoice_where, $invoice_order);

      $result_payment ='<table class="table table-bordered table-striped table-condensed">
                          <thead>
                            <tr>
                              <th >#</th>
                              <th >Desciption</th>
                              <th >Invoice Number</th>
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
          $account_name = $value->invoice_number;
          $item_description = $value->description;
          $vat_type_id = $value->vat_type_id;
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

          $x++;
          $result_payment .= form_open("accounts/update_invoice_item/".$creditor_credit_note_item_id."/".$creditor_id, array("class" => "form-horizontal"));
          $result_payment .= '<tr>
                                  <td>'.$x.'</td>
                                  <td>'.$item_description.'</td>
                                  <td>'.$account_name.'</td>
                                  <td>'.$vat.'</td>
                                  <td>'.number_format($vat_amount,2).'</td>
                                  <td>'.number_format($amount,2).'</td>
                                  <td><a href="'.site_url().'delete-invoice-item/'.$creditor_credit_note_item_id.'" type="submit" class="btn btn-sm btn-danger" ><i class="fa fa-trash"></i></a></td>
                              </tr>';
          $result_payment .=form_close();
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
            <?php echo form_open("finance/creditors/confirm_credit_note/".$creditor_id, array("class" => "form-horizontal"));?>
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
                      <input type="text" class="form-control" name="credit_note_number" placeholder=""  autocomplete="off" value="">
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
                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="credit_note_date" placeholder="Credit Note Date" id="datepicker" value="<?php echo date('Y-m-d')?>" required>
                        </div>
                    </div>
                  </div>

                <div class="col-md-12">
                    <div class="text-center">
                      <button class="btn btn-info btn-sm " type="submit">Complete Credit Note </button>
                    </div>
                </div>
              </div>
            <?php echo form_close();?>
          </div>
        </div>
        <?php
      }
      ?>

    </div>
  </section>
</div>


<div class="row">
  <div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <h3 class="panel-title">Credit Notes </h3>
        </header>
        <div class="panel-body">
            <table class="table table-hover table-bordered col-md-12">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Credit Note Date</th>
                  <th>Credit Note Number</th>
                  <th>Document Number</th>
                  <th>Tax Charged</th>
                  <th>Toatl Bill</th>
                  <th colspan="1">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                	$creditor_invoices = $this->creditors_model->get_creditor_credit_notes($creditor_id,10);
                  // var_dump($tenant_query);die();
                if($creditor_invoices->num_rows() > 0)
                {
                  $y = 0;
                  foreach ($creditor_invoices->result() as $key) {
                    # code...
                    $total_amount = $key->total_amount;
                    $creditor_invoice_id = $key->creditor_invoice_id;
                    $transaction_date = $key->transaction_date;
                    $document_number = $key->document_number;
                    $invoice_number = $key->invoice_number;
                    $vat_charged = $key->vat_charged;
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
                      <td><?php echo $invoice_number?></td>
                      <td><?php echo $document_number?></td>
                      <td><?php echo number_format($vat_charged,2);?></td>
                      <td><?php echo number_format($total_amount,2);?></td>
                      <td><a href="<?php echo site_url().'cash-office/print-invoice-note/'.$creditor_invoice_id.'/'.$creditor_id;?>" class="btn btn-sm btn-primary" target="_blank">Credit Note</a></td>

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
