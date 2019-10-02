<!-- search -->
<?php //echo $this->load->view('search/search_petty_cash', '', TRUE);

    $properties = $this->property_model->get_active_property();
    $rs8 = $properties->result();
    $property_list = '';
    foreach ($rs8 as $property_rs) :
        $property_id = $property_rs->property_id;
        $property_name = $property_rs->property_name;
        $property_location = $property_rs->property_location;

        $property_list .="<option value='".$property_id."'>".$property_name." Location: ".$property_location."</option>";

    endforeach;


    $invoice_type_order = 'invoice_type.invoice_type_id';
    $invoice_type_table = 'invoice_type';
    $invoice_type_where = 'invoice_type.invoice_type_status = 1 AND generaljournalaccountid = 6';

    $invoice_type_query = $this->tenants_model->get_tenant_list($invoice_type_table, $invoice_type_where, $invoice_type_order);
    $rs8 = $invoice_type_query->result();
    $invoice_type_list = '';
    foreach ($rs8 as $invoice_rs) :
      $invoice_type_id = $invoice_rs->invoice_type_id;
      $invoice_type_name = $invoice_rs->invoice_type_name;


        $invoice_type_list .="<option value='".$invoice_type_id."'>".$invoice_type_name."</option>";

    endforeach;

    $v_data['invoice_type_list'] = $invoice_type_list;

?>
<!-- end search -->
<!--begin the reports section-->
<?php
//unset the sessions set\
?>
<!--end reports -->
<div class="row">
    <div class="col-md-5">
      <div class="box">
               <div class="box-header with-border">
                 <h3 class="box-title">Search List</h3>

                 <div class="box-tools pull-right">
                 </div>
               </div>
               <div class="box-body">
               <div class="pull-right">
                 <!-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#record_petty_cash"><i class="fa fa-plus"></i> Record</button>
                 <a href="<?php echo base_url().'accounts/petty_cash/print_petty_cash/';?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-print"></i> Print</a>
                 <a href="<?php echo base_url().'administration/sync_app_petty_cash';?>" class="btn btn-sm btn-info"><i class="fa fa-sign-out"></i> Sync</a> -->
               </div>

             <?php echo form_open("finance/purchases/search_purchases", array("class" => "form-horizontal"));?>
               <div class="row">
                 <div class="col-md-12">
                 <div class="col-md-6">
                       <div class="form-group">
                           <label class="col-md-4 control-label">Date From: </label>

                           <div class="col-md-8">
                               <div class="input-group">
                                   <span class="input-group-addon">
                                       <i class="fa fa-calendar"></i>
                                   </span>
                                   <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_from" placeholder="Transaction date" value="<?php echo date('Y-m-d');?>" id="datepicker" >
                               </div>
                           </div>
                       </div>
                       <div class="form-group">
                           <label class="col-md-4 control-label">Ref No *</label>

                           <div class="col-md-8">
                               <input type="text" class="form-control" name="transaction_number" placeholder="Transaction Number" />
                           </div>
                       </div>



                 </div>
                 <div class="col-md-6">

                         <div class="form-group">
                             <label class="col-md-4 control-label">Date To: </label>

                             <div class="col-md-8">
                                 <div class="input-group">
                                     <span class="input-group-addon">
                                         <i class="fa fa-calendar"></i>
                                     </span>
                                     <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_ro" placeholder="Transaction date" value="<?php echo date('Y-m-d');?>" id="datepicker1" >
                                 </div>
                             </div>
                         </div>
                         <div class="form-group">
                           <div class="text-center">
                               <button type="submit" class="btn btn-sm btn-primary">Search record</button>
                           </div>
                         </div>

                 </div>
                 </div>


               </div>
               <?php echo form_close();?>
              <hr>

            </div>
        </div>
    </div>
    <div class="col-md-7">
      <div class="box">
               <div class="box-header with-border">
                 <h3 class="box-title">Add <?php echo $title;?></h3>

                 <div class="box-tools pull-right">
                 </div>
               </div>
               <div class="box-body">
               <div class="pull-right">
                 <!-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#record_petty_cash"><i class="fa fa-plus"></i> Record</button>
                 <a href="<?php echo base_url().'accounts/petty_cash/print_petty_cash/';?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-print"></i> Print</a>
                 <a href="<?php echo base_url().'administration/sync_app_petty_cash';?>" class="btn btn-sm btn-info"><i class="fa fa-sign-out"></i> Sync</a> -->
               </div>

             <?php echo form_open("finance/landlord/record_landlord_transaction", array("class" => "form-horizontal"));?>
               <div class="row">
                 <div class="col-md-12">
                 <div class="col-md-6">
                       <div class="form-group">
                           <label class="col-md-4 control-label">Transaction date: </label>

                           <div class="col-md-8">
                               <div class="input-group">
                                   <span class="input-group-addon">
                                       <i class="fa fa-calendar"></i>
                                   </span>
                                   <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="transaction_date" placeholder="Transaction date" value="<?php echo date('Y-m-d');?>" id="datepicker2" required>
                               </div>
                           </div>
                       </div>
                       <div class="form-group">
                           <label class="col-lg-4 control-label">Property From</label>

                           <div class="col-lg-8">
                              <select  name='property_id' class='form-control select2' >
                                 <option value=''>None - Please Select a property</option>
                                 <?php echo $property_list;?>
                               </select>
                           </div>
                       </div>

                       <div class="form-group">
                           <label class="col-md-4 control-label">Type *</label>

                           <div class="col-md-8">
                               <div class="radio">
                                   <label>
                                       <input  type="radio"  value="2" name="transaction_type_id" id="account_to_type" onclick="get_transaction_type_list(this.value)">
                                       Income
                                   </label>
                                   <label>
                                       <input  type="radio"  value="3" name="transaction_type_id" id="account_to_type" onclick="get_transaction_type_list(this.value)">
                                       Payments
                                   </label>
                                   <label>
                                       <input  type="radio"  value="4" name="transaction_type_id" id="account_to_type" onclick="get_transaction_type_list(this.value)">
                                       Landlord Receipts
                                   </label>
                               </div>
                           </div>
                       </div>


                       <div class="form-group" id="property_div" style="display:none;">
                           <label class="col-lg-4 control-label">Property To</label>

                           <div class="col-lg-8">
                              <select  name='property_to_id' class='form-control select2' >
                                 <option value=''>None - Please Select a property</option>
                                 <?php echo $property_list;?>
                               </select>
                           </div>
                       </div>

                       <div class="form-group" >
                           <label class="col-md-4 control-label">Expense Account *</label>

                           <div class="col-md-8">
                               <select class="form-control select2" name="account_to_id" id="account_to_id" required>
                                 <option value="0">--- select an  account - ---</option>

                               </select>
                           </div>
                       </div>

                       <div class="form-group" >
                           <label class="col-md-4 control-label">Type Account *</label>

                           <div class="col-md-8">
                                <select id='invoice_type_id' name='invoice_type_id' class='form-control select2 '>
                                 <option value=''>None - Please Select an invoice type</option>
                                 <?php echo $invoice_type_list;?>
                               </select>
                           </div>
                       </div>



                 </div>
                 <div class="col-md-6">

                   <div class="form-group" id="payment_method">
                     <label class="col-md-4 control-label">Payment Method: </label>

                     <div class="col-md-7">
                       <select class="form-control" name="payment_method" onchange="check_payment_type(this.value)" required>
                         <option value="0">Select a payment method</option>
                                               <?php
                           $method_rs = $this->accounts_model->get_payment_methods();

                           foreach($method_rs->result() as $res)
                           {
                             $payment_method_id = $res->payment_method_id;
                             $payment_method = $res->payment_method;

                             echo '<option value="'.$payment_method_id.'">'.$payment_method.'</option>';

                           }

                         ?>
                       </select>
                       </div>
                   </div>
                   <div id="cheque_div" class="form-group" style="display:none;" >
                     <div class="form-group" >
                       <label class="col-md-4 control-label"> Bank: </label>

                       <div class="col-md-7">
                         <select class="form-control " name="bank_id"  required>
                           <option value="0">Select a bank</option>
                                                 <?php
                             $bank_rs = $this->accounts_model->get_bank_accounts();

                             foreach($bank_rs->result() as $res)
                             {
                               $id = $res->id;
                               $name = $res->name;

                               echo '<option value="'.$id.'">'.$name.'</option>';

                             }

                           ?>
                         </select>
                       </div>
                     </div>
                   </div>
                   <div class="form-group">
                       <label class="col-md-4 control-label">Transaction Number *</label>

                       <div class="col-md-8">
                           <input type="text" class="form-control" name="transaction_number" placeholder="Transaction Number" required/>
                       </div>
                   </div>
                   <div class="form-group">
                       <label class="col-md-4 control-label">Description *</label>

                       <div class="col-md-8">
                           <textarea class="form-control" name="description" required></textarea>
                       </div>
                   </div>

                   <div class="form-group">
                       <label class="col-md-4 control-label">Amount *</label>

                       <div class="col-md-8">
                           <input type="text" class="form-control" name="transacted_amount" placeholder="Amount" required/>
                       </div>
                   </div>
                 </div>
                 </div>
                 <br>
                 <div class="row">
                       <div class="col-md-12">
                           <div class="text-center">
                               <button type="submit" class="btn btn-sm btn-primary">Save record</button>
                           </div>
                       </div>
                   </div>

               </div>
               <?php echo form_close();?>


            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">


			<?php
      $search = $this->session->userdata('search_purchases');
			if(!empty($search))
			{
				?>
                <a href="<?php echo base_url().'finance/purchases/close_search';?>" class="btn btn-sm btn-success"><i class="fa fa-print"></i> Close Search</a>
                <?php
			}
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

			$result =  '';

			// echo $result;

      $result = '';
$count = 0;
  if($query_purchases->num_rows() > 0)
  {
    foreach ($query_purchases->result() as $key => $value) {
      // code...
      $document_number = $value->document_number;
      $transaction_number = $value->transaction_number;
      $transaction_date = $value->transaction_date;
      $property_name = $value->property_owner_name;
      $property_id = $value->property_id;
      $account_name = $value->account_name;
      $landlord_transaction_id = $value->landlord_transaction_id;
      $landlord_transaction_description = $value->landlord_transaction_description;
      $landlord_transaction_amount = $value->landlord_transaction_amount;
      $checkbox_data = array(
                'name'        => 'visit[]',
                'id'          => 'checkbox'.$landlord_transaction_id,
                'class'          => 'css-checkbox lrg',
                'value'       => $landlord_transaction_id
              );



      $balance = $landlord_transaction_amount;
      $count++;
      $result .='
                <tr>
                  <td>'.$count.'</td>
                  <td>'.form_checkbox($checkbox_data).'<label for="checkbox'.$landlord_transaction_id.'" name="checkbox79_lbl" class="css-label lrg klaus"></label>'.'</td>
                  <td>'.$transaction_date.'</td>
                  <td>'.$transaction_number.'</td>
                  <td>'.$landlord_transaction_description.'</td>
                  <td>'.number_format($landlord_transaction_amount,2).'</td>
                  <td>'.$property_name.'</td>
                  <td>'.$property_name.'</td>';
    }
  }
?>
<div class="box">
 <div class="box-header with-border">
   <h3 class="box-title">All Landlord Transactions</h3>

   <div class="box-tools pull-right">
   </div>
 </div>
 <div class="box-body">
      <table class="table table-hover table-bordered ">
			 	<thead>
					<tr>
            <th>#</th>
            <th></th>
					  <th>Date</th>
					  <th>Ref Number</th>
					  <th>Description</th>
					  <th>Invoice Amount</th>
					  <th>Owner</th>
            <th>Property</th>
					</tr>
				 </thead>
			  	<tbody>
            <?php echo $result;?>
			  	</tbody>
			</table>

    </div>
</div>
</div>
</div>

 <script type="text/javascript">

        function get_transaction_type_list(type)
        {
            var myTarget1 = document.getElementById("property_div");

            if(type == 4)
            {
                myTarget1.style.display = 'block';
                $('#property_id_to').addClass('select2');
            }

            var url = "<?php echo site_url();?>accounting/petty_cash/get_list_type_petty_cash/"+type;
            // alert(url);
            //get department services
            $.get( url, function( data )
            {
                $( "#account_to_id" ).html( data );
                // $(".custom-select").customselect();
            });

        }

        function getRadioCheckedValue(radio_name)
        {
           var oRadio = document.forms[0].elements[radio_name];

           for(var i = 0; i < oRadio.length; i++)
           {
              if(oRadio[i].checked)
              {
                 return oRadio[i].value;
              }
           }

           return '';
        }

        function display_payment_model(modal_id)
      	{
      		$('#modal-defaults'+modal_id).modal('show');
      		$('#datepicker1'+modal_id).datepicker({
    	      autoclose: true,
    	      format: 'yyyy-mm-dd',
    	    })
      	}

        function check_payment_type(payment_type_id){

          var myTarget1 = document.getElementById("cheque_div");

          var myTarget2 = document.getElementById("mpesa_div");

          var myTarget3 = document.getElementById("insuarance_div");

          if(payment_type_id == 1)
          {
            // this is a cash

            myTarget1.style.display = 'none';
            myTarget2.style.display = 'none';
            myTarget3.style.display = 'none';
          }
          else if(payment_type_id == 2 || payment_type_id == 3 || payment_type_id == 5)
          {
            // cheque
            myTarget1.style.display = 'block';
            myTarget2.style.display = 'none';
            myTarget3.style.display = 'none';
          }

          else
          {
            myTarget1.style.display = 'none';
            myTarget2.style.display = 'none';
            myTarget3.style.display = 'none';
          }

        }


    </script>
