<?php

$creditor_id = $this->session->userdata('invoice_creditor_id_searched');
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
            <h2 class="panel-title">Transfers</h2>
            <div class="widget-tools pull-right">
                <a href="<?php echo site_url();?>finance/add-creditor" class="btn btn-sm btn-primary" ><i class="fa fa-plus"></i> Add creditor</a>
            </div>
        </header>
        <div class="panel-body">

              <!-- select a tenant  -->
              <div class="row" style="margin-bottom:20px;">
                <?php echo form_open("search-creditor-invoices", array("class" => "form-horizontal", "role" => "form"));?>
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
$creditor_id = $lease_id = $this->session->userdata('invoice_creditor_id_searched');
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
  $bills = $this->creditors_model->get_creditor_account($creditor_id);
  $total_invoice = $bills['total_invoice'];
  $total_paid_amount = $bills['total_paid_amount'];
  $total_credit_note = $bills['total_credit_note'];

  $account_balance = ($opening_balance+$total_invoice) - ($total_paid_amount+$total_credit_note);

?>

<div class="row">
  <section class="panel">
      <header class="panel-heading">
          <h3 class="panel-title">Add an invoice </h3>
          <div class="pull-right">
             <a href="<?php echo site_url().'creditor-statement/'.$creditor_id?>" style="margin-top:-40px;" class="btn btn-sm btn-warning "> Back to creditor statement </a>
          </div>
      </header>
      <div class="panel-body">

        <?php echo form_open("finance/creditors/add_invoice_item/".$creditor_id, array("class" => "form-horizontal"));?>


              <input type="hidden" name="type_of_account" value="1">
              <input type="hidden" name="lease_id" id="lease_id" value="<?php echo $creditor_id?>">
              <input type="hidden" name="redirect_url" value="<?php echo $this->uri->uri_string()?>">

              <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                      <label class="col-md-3 control-label">Description: </label>
                      <div class="col-md-8">
                        <textarea class="form-control" name="item_description" required></textarea>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-md-3 control-label">Quantity: </label>
                      <div class="col-md-8">
                        <input type="number" class="form-control" name="quantity" id="quantity" placeholder="1"  autocomplete="off" onkeyup="get_value()" required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-md-3 control-label">Unit Price: </label>
                      <div class="col-md-8">
                        <input type="text" class="form-control" name="unit_price" id="unit_price" placeholder=""  autocomplete="off" onkeyup="get_value()" required>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
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
                    <input type="hidden" id="input-total-value" name="total_amount">
                    <input type="hidden" id="vat-amount" name="vat_amount">
                    <div class="form-group">
                      <label class="col-md-3 control-label">TAX: </label>

                      <div class="col-md-8">
                        <select class="form-control " name="tax_type_id" id="tax_type_id" onchange="get_value()" required>
                          <option value="0">No VAT</option>
                          <option value="1">16% VAT</option>
                          <option value="2">5% Withholding</option>

                        </select>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                      <div id="vat_amount"></div>
                      <div id="total_units"></div>
                    </div>

                  </div>

              </div>
              <br>
              <div class="row text-center">
                <button class="btn btn-info" type="submit">Add Item </button>
              </div>


      <?php echo form_close();?>

      <?php echo form_open("finance/creditors/confirm_invoice_note/".$creditor_id, array("class" => "form-horizontal"));?>
      <?php
      $invoice_where = 'creditor_invoice_item.creditor_id = '.$creditor_id.' AND creditor_invoice_item_status = 0 AND account.account_id = creditor_invoice_item.account_to_id';
      $invoice_table = 'creditor_invoice_item,account';
      $invoice_order = 'creditor_invoice_item_id';

      $invoice_query = $this->creditors_model->get_creditors_list($invoice_table, $invoice_where, $invoice_order);

      $result_payment ='<table class="table table-bordered table-striped table-condensed">
                          <thead>
                            <tr>
                              <th >#</th>
                              <th ></th>
                              <th >Desciption</th>
                              <th >Units</th>
                              <th >Unit Price</th>
                              <th >Expense Account</th>
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
                                    <td>'.form_checkbox($checkbox_data).'<label for="checkbox'.$creditor_invoice_item_id.'" name="checkbox79_lbl" class="css-label lrg klaus"></label>'.'</td>
                                  <td>'.$item_description.'</td>
                                  <td>'.$quantity.'</td>
                                  <td>'.$unit_price.'</td>
                                  <td>'.$account_name.'</td>
                                  <td>'.$vat.'</td>
                                  <td>'.number_format($vat_amount,2).'</td>
                                  <td>'.number_format($amount,2).'</td>
                                  <td><a href="'.site_url().'delete-creditor-invoice-item/'.$creditor_invoice_item_id.'/'.$creditor_id.'" type="submit" class="btn btn-sm btn-danger" ><i class="fa fa-trash"></i></a></td>
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
                    <label class="col-md-4 control-label">Invoice Number: </label>

                    <div class="col-md-7">
                      <input type="text" class="form-control" name="invoice_number" placeholder=""  autocomplete="off"  required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Total Amount: </label>

                    <div class="col-md-7">
                      <input type="number" class="form-control" name="amount" placeholder=""  autocomplete="off" value="<?php echo $total_amount - $total_vat_amount;?>" readonly="readonly" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Total Tax: </label>

                    <div class="col-md-7">
                      <input type="number" class="form-control" name="vat_charged" placeholder=""  autocomplete="off" value="<?php echo $total_vat_amount;?>" readonly="readonly" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Total Amount: </label>

                    <div class="col-md-7">
                      <input type="number" class="form-control" name="amount_charged" placeholder=""  autocomplete="off" value="<?php echo $total_amount;?>" readonly="readonly" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Invoice Date: </label>

                    <div class="col-md-7">
                       <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="invoice_date" placeholder="Payment Date" id="datepicker" value="<?php echo date('Y-m-d')?>" required>
                        </div>
                    </div>
                  </div>

                <div class="col-md-12">
                    <div class="text-center">
                      <button class="btn btn-info btn-sm " type="submit">Complete Invoice </button>
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
            <h3 class="panel-title">Invoices </h3>
        </header>
        <div class="panel-body">
            <table class="table table-hover table-bordered col-md-12">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Invoice Date</th>
                  <th>Invoice Number</th>
                  <th>Document Number</th>
                  <th>Tax Charged</th>
                  <th>Toatl Bill</th>
                  <!-- <th colspan="1">Actions</th> -->
                </tr>
              </thead>
              <tbody>
                <?php
                	// $creditor_invoices = $this->creditors_model->get_creditor_invoice($lease_id,10);
                  // var_dump($tenant_query);die();
                if($creditor_invoices->num_rows() > 0)
                {
                  $y = $page;
                  // $count = $page;
                  foreach ($creditor_invoices->result() as $key) {
                    # code...
                    $total_amount = $key->total_amount;
                    $creditor_invoice_id = $key->creditor_invoice_id;
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
                      <td><a href="<?php echo site_url().'creditor-invoice/edit-creditor-invoice/'.$creditor_invoice_id;?>" class="btn btn-sm btn-success" ><i class="fa fa-pencil"></i></a></td>
                      <td><a href="<?php echo site_url().'creditor-invoice/delete-creditor-invoice/'.$creditor_invoice_id;?>" class="btn btn-sm btn-danger" onclick="return confirm('Do you want to delete this invoice ?')"><i class="fa fa-trash"></i></a></td>

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
