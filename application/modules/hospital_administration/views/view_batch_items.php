
<?php
echo form_open("hospital_administration/confirm_payments/".$batch_receipt_id, array("class" => "form-horizontal"));
?>
<?php
        $where = 'batch_receipts.bank_id = account.account_id AND batch_receipts.batch_receipt_id = '.$batch_receipt_id;
        $table = 'batch_receipts,account';
        
    
        $transaction_detail_rs = $this->accounting_model->get_all_unpaid_invoices($table, $where);

        if($transaction_detail_rs->num_rows() > 0)
        {
            foreach ($transaction_detail_rs->result() as $key => $value) {
                # code...
                $account_name = $value->account_name;
                $receipt_number = $value->receipt_number;
                $payment_date = $value->payment_date;
                $total_amount_paid = $value->total_amount_paid;
                $bank_id = $value->bank_id;
                $payment_date = $value->payment_date;
                $receipt_number = $value->receipt_number;

            }
        }
        
        // $where = 'v_statement_of_accounts.dr_amount <> v_statement_of_accounts.cr_amount AND v_statement_of_accounts.payment_type = '.$insurance_id;
        // $table = 'v_statement_of_accounts';

        $where = 'v_transactions_by_date.transactionCategory = "Revenue" AND visit_invoice.visit_invoice_id = v_transactions_by_date.transaction_id AND visit_invoice.visit_invoice_status <> 1 AND patients.patient_id = visit_invoice.patient_id AND visit_invoice.bill_to = '.$insurance_id;
        
        $table = 'v_transactions_by_date,visit_invoice,patients';
        
    
        $query = $this->accounting_model->get_all_unpaid_invoices($table, $where);


        $result = '';
        
        //if users exist display them
        if ($query->num_rows() > 0)
        {
            $count = 0;
            
            $result .= 
            '
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>Name</th>
                        <th>Invoice Date</th>
                        <th>Invoice Number</th>
                        <th>Amount Invoiced</th>
                        <th>Amount Paid</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                  <tbody>
                  
            ';
            
            //get all administrators
           
            
            foreach ($query->result() as $row)
            {
               

                $transaction_id = $row->transaction_id;
                $patient_name = $row->patient_surname.' '.$row->patient_othernames;
                $reference_code = $row->reference_code;
                $invoice_date = $row->invoice_date;



                $dr_amount = $row->dr_amount;
                $total_payments = $this->accounts_model->get_visit_invoice_payments($transaction_id);
                $credit_note = $this->accounts_model->get_visit_invoice_credit_notes($transaction_id);

                $dr_amount = $balance = $dr_amount - ($total_payments+$credit_note);
                // $cr_amount = $row->cr_amount;
                $status = $row->status;
                $patient_id = $row->patient_id;
                $invoice_date = date('jS M Y',strtotime($row->invoice_date));

                if($status == 0)
                {
                	$color ='warning';
                	$status = 'Not Reconcilled';
                }
                else
                {
                	$color = 'success';
                	$status = 'Reconcilled';
                }
                 $checkbox_data = array(
                                        'name'        => 'visit_invoices[]',
                                        'id'          => 'checkbox',
                                        'class'          => 'css-checkbox  lrg ',
                                        // 'checked'=>'checked',
                                        'value'       => $transaction_id,
                                        'onclick'=>'get_values('.$transaction_id.','.$batch_receipt_id.')'
                                      );
                $count++;
                $result .= 
                '
                    <tr>
                        <td>'.form_checkbox($checkbox_data).'<label for="checkbox'.$transaction_id.'" name="checkbox79_lbl" class="css-label lrg klaus"></label>'.'</td>
                        <td >'.$count.'</td>
                        <td >'.$patient_name.'</td>
                        <td>'.$invoice_date.'</td>
                        <td >'.$reference_code.'</td>
                        <td>'.number_format($dr_amount,2).'<input type="hidden" class="form-control" colspan="3" name="invoiced_amount'.$transaction_id.'" id="invoiced_amount'.$transaction_id.'" value="'.$dr_amount.'" />
                        <input type="hidden" class="form-control" colspan="3" name="patient_id'.$transaction_id.'" id="patient_id'.$transaction_id.'" value="'.$patient_id.'"/></td>
                        <td><input type="number" class="form-control" colspan="3" name="amount_paid'.$transaction_id.'" id="amount_paid'.$transaction_id.'"  onkeyup="update_amount_to_pay('.$transaction_id.')"/></td>
                        <td>'.number_format($balance,2).'</td>
                       
                    </tr> 
                ';
            }
            
            $result .= 
            '
                          </tbody>
                        </table>
            ';
        }
        
        else
        {
            $result .= "There are no remitance uploaded";
        }
?>

<section class="panel">
    <header class="panel-heading">
       

        <h2 class="panel-title"><?php echo $title;?></h2>
         <div class=" pull-right" style="margin-top:-25px !important;">
             <a  class="btn btn-md btn-success" onclick="add_unallocated_payment_view(<?php echo $batch_receipt_id?>)"> <i class="fa fa-plus"></i> Add unallocated Payment</a>
            <a href="<?php echo site_url().'accounting/remittance-reconcilliations'?>" class="btn btn-md btn-warning" > <i class="fa fa-arrow-left"></i> Back to batch payments</a>
        </div>
    </header>
    <div class="panel-body">
        <div class="col-md-12">
            <div class="table-responsive">
            
                <?php echo $result;?>
        
            </div>
        </div>
         <div class="col-md-12">
            <div class="col-md-4">
            </div>
            <div class="col-md-8">
                <h4>Unallocated Payments</h4>
                <div id="unallocated-payments"></div>
            </div>
        </div>
        <div class="col-md-12">
           
            <div class="col-md-6">
                <input type="hidden" class="form-control" id="total_amount_paid" value="<?php echo $total_amount_paid?>" readonly="readonly" >

                <input type="hidden" class="form-control" id="bank_id" name="bank_id" value="<?php echo $bank_id?>" readonly="readonly" >
                <input type="hidden" class="form-control" id="confirm_number" name="confirm_number" value="<?php echo $receipt_number?>" readonly="readonly" >
                <input type="hidden" class="form-control" id="payment_date" name="payment_date" value="<?php echo $payment_date?>" readonly="readonly" >
                <input type="hidden" class="form-control" id="batch_receipt_id" name="batch_receipt_id" value="<?php echo $batch_receipt_id?>" readonly="readonly" >
            </div>
            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-responsive table-condensed table-striped table-bordered">
                        <thead>
                            <th>Title</th>
                            <th>Description</th>
                        </thead>
                         <tbody>
                             <tr>
                                <td>Bank Account</td>
                                <td><?php echo $account_name;?></td>
                            </tr>
                            <tr>
                                <td>EFT/Transaction Code</td>
                                <td><?php echo $receipt_number;?></td>
                            </tr>
                            <tr>
                                <td>Date Paid</td>
                                <td><?php echo date('jS M Y',strtotime($payment_date))?></td>
                            </tr>
                            <tr>
                                <td>Total Paid</td>
                                <td><?php echo number_format($total_amount_paid,2)?></td>
                            </tr>

                            <tr>
                                
                                <td>Amount Reconcilled</td>
                                <td><input type="text" class="form-control" id="amount_reconcilled" value="" readonly="readonly" ></td>
                            </tr>
                            <tr>
                                
                                <td>Diffence (Total Paid - Amount Reconcilled)</td>
                                <td><span id="difference"></span></td>
                            </tr>

                            <tr>
                                
                                <td colspan="2" >
                                    <button type="submit" id="submit-button" style="display: none;margin: 0 auto;" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to complete this payment ?')"> Complete Payment </button>
                                </td>
                                
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
       


    </div>
    
    <div class="panel-foot">
        
        <?php if(isset($links)){echo $links;}?>
    
        <div class="clearfix"></div> 
    
    </div>
</section>
</form>

<script type="text/javascript">
    $(function() {

        var batch_receipt_id = $("#batch_receipt_id").val(); 

        // alert(batch_receipt_id);
        get_unreconcilled_payments(batch_receipt_id);
    });

    function get_values(transaction_id,batch_receipt_id)
    {
        var config_url = $('#config_url').val();
        var invoiced_value = $("#invoiced_amount"+transaction_id).val(); 
        document.getElementById("amount_paid"+transaction_id).value = invoiced_value;
        var value_item = $("#amount_paid"+transaction_id).val();   


        var url = config_url+"hospital_administration/update_visit_invoice/"+transaction_id;

        // alert(value_item);
        $.ajax({
        type:'POST',
        url: url,
        data:{amount_payable: value_item,batch_receipt_id: batch_receipt_id},
        dataType: 'text',
        // processData: false,
        // contentType: false,
        success:function(data){
            // alert(data);
                 var data = jQuery.parseJSON(data);
          
                if(data.message == 'success')  
                {

                }

                else
                {
                    alert(data.result);
                }
            },
            error: function(xhr, status, error) {
            alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);

            }
        });
    }
    function get_values_old(transaction_id)
    {
        var favorite = [];
        $.each($("input[id='checkbox']:checked"), function(){    

            var invoiced_value = $("#invoiced_amount"+$(this).val()).val(); 
            document.getElementById("amount_paid"+$(this).val()).value = invoiced_value;
            var value_item = $("#amount_paid"+$(this).val()).val();     

            // favorite.push($(this).val());

            favorite.push(value_item);
        });
        // alert("My favourite sports are: " + favorite.join(", "));
        var total_bill = favorite.join(", ");

        var total_amount_paid = $("#total_amount_paid").val(); 
        var batch_receipt_id = $("#batch_receipt_id").val(); 

        var config_url = $('#config_url').val();
        var url = config_url+"hospital_administration/calculate_billed_items";

        // alert(total_amount_paid);
        $.ajax({
        type:'POST',
        url: url,
        data:{billed: total_bill,total_paid: total_amount_paid, batch_receipt_id: batch_receipt_id},
        dataType: 'text',
        // processData: false,
        // contentType: false,
        success:function(data){
            // alert(data);
          var data = jQuery.parseJSON(data);
          
          if(data.message == 'success')  
          {
                
                document.getElementById("amount_reconcilled").value = data.billing;
                // document.getElementById("difference").value = data.balance;
                $("#difference").html(data.balance);
                // alert(data.billing);

                if(data.balance == "0")
                {
                    // alert('herer');
                    $('#submit-button').css('display', 'block');
                }
                else
                {
                    $('#submit-button').css('display', 'none');
                }
            

          }
          else
          {
            alert(data.result);
          }
         

        },
        error: function(xhr, status, error) {
        alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);

        }
        });
    }

    function update_amount_to_pay(visit_invoice_id)
    {

        var total_bill = $("#amount_paid"+visit_invoice_id).val(); 
        // alert(total_bill);
         // var total_bill = favorite.join(", ");

        var total_amount_paid = $("#total_amount_paid").val(); 
         var batch_receipt_id = $("#batch_receipt_id").val(); 
        var config_url = $('#config_url').val();
        var url = config_url+"hospital_administration/calculate_billed_items";

        // alert(total_amount_paid);
        $.ajax({
        type:'POST',
        url: url,
        data:{billed: total_bill,total_paid: total_amount_paid,batch_receipt_id: batch_receipt_id},
        dataType: 'text',
        // processData: false,
        // contentType: false,
        success:function(data){
            // alert(data);
          var data = jQuery.parseJSON(data);
          
          if(data.message == 'success')  
          {
                
                document.getElementById("amount_reconcilled").value = data.billing;
                // document.getElementById("difference").value = data.balance;
                $("#difference").html(data.balance);
                // alert(data.billing);

                if(data.balance == "0")
                {
                    // alert('herer');
                    $('#submit-button').css('display', 'block');
                }
                else
                {
                    $('#submit-button').css('display', 'none');
                }
            

          }
          else
          {
            alert(data.result);
          }
         

        },
        error: function(xhr, status, error) {
        alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);

        }
        });

    }

    function add_unallocated_payment_view(batch_receipt_id)
    {

      // $('html').toggleClass('sidebar-right-opened');

      // $('#sidebar-right').trigger('click');

      document.getElementById("sidebar-right").style.display = "block"; 
      // document.getElementById("existing-sidebar-div").style.display = "none"; 
      // document.getElementById("sidebar-right").style.width = "300px";
      // document.getElementById("sidebar-right").style.marginLeft = "-250px";
      
      var config_url = $('#config_url').val();
      var data_url = config_url+"hospital_administration/add_unallocated_payment_view/"+batch_receipt_id;
      //window.alert(data_url);
      $.ajax({
      type:'POST',
      url: data_url,
      data:{batch_receipt_id: batch_receipt_id},
      dataType: 'text',
      success:function(data){
      //window.alert("You have successfully updated the symptoms");
      //obj.innerHTML = XMLHttpRequestObject.responseText;
       // alert(data);
       document.getElementById("current-sidebar-div").style.display = "block"; 
       $("#current-sidebar-div").html(data);
       tinymce.init({
                    selector: ".cleditor",
                    height: "150"
                    });
       
      },
      error: function(xhr, status, error) {
      //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
      alert(error);
      }

      });
    }

    $(document).on("submit","form#add-unallocated-payment",function(e)
    {
        
        alert('sdasdasda');
        // myApp.showIndicator();
        
        var res = confirm('Are you sure you want to add this payment item ?');

        if(res)
        {
            e.preventDefault();

            var form_data = new FormData(this);

            alert(form_data);

            var config_url = $('#config_url').val();    

            var url = config_url+"hospital_administration/add_unallocated_payment";
            $.ajax({
               type:'POST',
               url: url,
               data:form_data,
               dataType: 'text',
               processData: false,
               contentType: false,
               success:function(data){
                  var data = jQuery.parseJSON(data);
                
                  if(data.message == "success")
                    {
                        // alert(data.message);
                        
                        close_side_bar();
                    }
                    else
                    {
                        alert('Please ensure you have added included all the items');
                    }
               
               },
               error: function(xhr, status, error) {
               alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
               
               }
             });
        }
         
        
       
        
    });


    function add_payment_item()
    {

        // myApp.showIndicator();
        
        var res = confirm('Are you sure you want to add this payment item ?');

        if(res)
        {
           

            var batch_receipt_id = $('#batch_receipt_idd').val(); 
            var payment_description = tinymce.get('payment_description').getContent();
            var amount_paid_unreconcilled = $('#amount_paid_unreconcilled').val(); 
            var invoice_referenced = $('#invoice_referenced').val(); 

            var config_url = $('#config_url').val();    

            var url = config_url+"hospital_administration/add_unallocated_payment";
            $.ajax({
               type:'POST',
               url: url,
               data:{batch_receipt_id: batch_receipt_id,payment_description: payment_description,amount_paid_unreconcilled: amount_paid_unreconcilled,invoice_referenced: invoice_referenced},
               dataType: 'text',
               // processData: false,
               // contentType: false,
               success:function(data){
                  var data = jQuery.parseJSON(data);
                
                  if(data.message == "success")
                    {
                        // alert(data.message);
                        get_unreconcilled_payments(batch_receipt_id);
                        close_side_bar();
                    }
                    else
                    {
                        alert('Please ensure you have added included all the items');
                    }
               
               },
               error: function(xhr, status, error) {
               alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
               
               }
             });
        }
    }

    function get_unreconcilled_payments(batch_receipt_id) 
    {
        // body...

        var config_url = $('#config_url').val();
        var url = config_url+"hospital_administration/get_unreconcilled_payments/"+batch_receipt_id;
        // alert(summary_notes);
        $.ajax({
        type:'POST',
        url: url,
        data:{batch_receipt_id: batch_receipt_id},
        dataType: 'text',
        // processData: false,
        // contentType: false,
        success:function(data){
          var data = jQuery.parseJSON(data);
          // alert(data.results);
          if(data.message == 'success')  
          {
            $('#unallocated-payments').html(data.results);
          }
          else
          {
            // alert(data.result);
          }
         

        },
        error: function(xhr, status, error) {
        alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);

        }
        });
    }

    function delete_unallocated_payment(unallocated_payment_id,batch_receipt_id)
    {


        var res = confirm('Are you sure you want to remove this allocation ?');

        if(res)
        {


            var config_url = $('#config_url').val();
            var url = config_url+"hospital_administration/delete_unallocated_payment/"+unallocated_payment_id;
            // alert(summary_notes);
            $.ajax({
            type:'POST',
            url: url,
            data:{batch_receipt_id: batch_receipt_id},
            dataType: 'text',
            // processData: false,
            // contentType: false,
            success:function(data){
              var data = jQuery.parseJSON(data);

              if(data.message == 'success')  
              {
                get_unreconcilled_payments(batch_receipt_id);
              }
              else
              {
                // alert(data.result);
              }
             

            },
            error: function(xhr, status, error) {
            alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);

            }
            });
        }
    }


    
</script>