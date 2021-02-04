<!-- search -->
<?php echo $this->load->view('search/search_cheques', '', TRUE);?>

<!-- end search -->
<section class="panel">
    <header class="panel-heading">
          <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
          <div class="widget-icons pull-right">
               
          </div>
          <div class="clearfix"></div>
    </header>
    <div class="panel-body">
        <div class="padd">
         <?php echo form_open_multipart($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Parent Account</label>
                        <div class="col-lg-8">
                            <select id="account_from_id" name="account_from_id" class="form-control">
                                <option value="0">--- Account ---</option>
                                <?php
                                if($accounts->num_rows() > 0)
                                {   
                                    foreach($accounts->result() as $row):
                                        // $company_name = $row->company_name;
                                        $account_name = $row->account_name;
                                        $account_id = $row->account_id;
                                        
                                        echo "<option value=".$account_id."> ".$account_name."</option>";
                                        
                                    endforeach; 
                                } 
                                ?>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Amount *</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="amount" placeholder="Amount" value="<?php echo set_value('amount');?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Cheque *</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="cheque_number" placeholder="cheque_number" value="<?php echo set_value('cheque_number');?>" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label">Description *</label>
                        <div class="col-lg-8">
                            <textarea class="form-control" name="description" placeholder="Payment For"><?php echo set_value('description');?></textarea>
                        </div>
                    </div>
                   
                </div>
                <div class="col-md-6">        
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Payment date: </label>
                        
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date" placeholder="Payment Date" value="<?php echo date('Y-m-d');?>">
                            </div>
                        </div>
                    </div>       
                    <!-- Activate checkbox -->
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Accout to ?</label>
                        <div class="col-lg-8">
                            <div class="radio">
                                <label>
                                    <input  type="radio" checked value="0" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list('account_to_type')">
                                    None
                                </label>
                                <label>
                                    <input  type="radio"  value="4" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list('account_to_type')">
                                    Direct Purchase
                                </label>
                               <!--  <label>
                                    <input  type="radio" value="2" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list('account_to_type')">
                                    Creditor
                                </label>
                                
                                <label>
                                    <input  type="radio" value="1" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list('account_to_type')">
                                    Transfer
                                </label> -->
                               <!--  <label>
                                    <input  type="radio" value="3" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list('account_to_type')">
                                    Doctor
                                </label> -->
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Charging to: </label>
                        
                        <div class="col-lg-8">
                            <select name="account_to_id" class="form-control custom-select" id="charge_to_id">
                                
                            </select>
                        </div>
                    </div>
                        <div class="form-group" style="display: none;" id="payment_to_div">
                        <label class="col-lg-4 control-label">Accout for ?</label>
                        <div class="col-lg-8">
                            <div class="radio">
                                <label>
                                    <input  type="radio" checked value="1" name="payment_to" id="payment_to" >
                                    Week Payment
                                </label>
                                <label>
                                    <input  type="radio"  value="0" name="payment_to" id="payment_to" >
                                    Month Payment
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions center-align">
                        <button class="submit btn btn-primary btn-sm" type="submit">
                            Add payment detail
                        </button>
                    </div>
                </div>

            </div>
            <?php echo form_close();?>
        </div>
    </div>
</section>

 <section class="panel">
    <header class="panel-heading">
          <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
          <div class="widget-icons pull-right">
               
          </div>
          <div class="clearfix"></div>
    </header>
    <div class="panel-body">
        <div class="padd">
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

            <?php
            $search = $this->session->userdata('accounts_cheques_search');

            if(!empty($search))
            {
                echo '<a href="'.site_url().'accounting/petty_cash/close_cheques_search" class="btn btn-warning btn-sm">Close Search</a>';
            }
            ?>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-hover table-bordered ">
                        <thead>
                            <tr>
                              <th>#</th>   
                              <th>Date</th>      
                              <th>Document No.</th>              
                              <th>Account From</th>
                              <th>Payment To</th>
                              <th>Description</th>
                              <th >Amount</th>  
                              <th colspan="2"></th>                    
                            </tr>
                         </thead>
                        <tbody>
                            <?php
                                $result = '';
                                // var_dump($query); die();
                               if($query->num_rows() > 0)
                               {
                                 $x=$page;
                                    foreach ($query->result() as $key => $value) {
                                        # code...
                                        $account_from_id = $value->account_from_id;
                                        $account_to_type = $value->account_to_type;
                                        $account_to_id = $value->account_to_id;
                                        $receipt_number = $value->receipt_number;
                                        $account_payment_id = $value->account_payment_id;
                                         $payment_date = $value->payment_date;
                                         $created = $value->created;
                                        $amount_paid = $value->amount_paid;
                                        $account_payment_description = $value->account_payment_description;

                                        $account_from_name = $this->petty_cash_model->get_account_name($account_from_id);
                                        if($account_to_type == 1)
                                        {
                                            $payment_type = 'Transfer';
                                            $account_to_name = $this->petty_cash_model->get_account_name($account_to_id);
                                        }
                                        else if($account_to_type == 3)
                                        {
                                            // doctor payments
                                            $payment_type = "Doctor Payment";
                                            $account_to_name = $this->petty_cash_model->get_doctor_name($account_to_id);
                                        }
                                        else if($account_to_type == 2)
                                        {
                                            // creditor
                                            $payment_type = "Creditor Payment";
                                            $account_to_name = $this->petty_cash_model->get_creditor_name($account_to_id);
                                        }
                                        else if($account_to_type == 4)
                                        {
                                            // expense account
                                            $payment_type = "Direct Expense Payment";
                                            $account_to_name = $this->petty_cash_model->get_account_name($account_to_id);
                                        }


                                        // if($created == date('Y-m-d'))
                                        // {
                                            $add_invoice = '<td><a onclick="edit_direct_payment('.$account_payment_id.')"   class="btn btn-sm btn-success fa fa-pencil"></a></td>
                                                            <td><a href="'.site_url().'delete-payment-ledger-entry/'.$account_payment_id.'"  onclick="return confirm(\'Are you sure you want to delete this payment ? \')" class="btn btn-sm btn-danger fa fa-trash" ></a></td>
                                                            ';
                                        // }
                                        // else
                                        // {
                                        //     $add_invoice = '';
                                        // }

                                        $x++;

                                        $result .= '<tr>
                                                        <td>'.$x.'</td>
                                                        <td>'.$payment_date.'</td>
                                                        <td>'.strtoupper($receipt_number).'</td>
                                                        <td>'.$account_from_name.'</td>
                                                        <td>'.$account_to_name.'</td>
                                                        <td>'.$account_payment_description.'</td>
                                                        <td>'.number_format($amount_paid,2).'</td>
                                                        '.$add_invoice.'
                                                    </tr>';

                                    }
                               }
                               echo $result;
                            ?>
                        </tbody>
                    </table>
                </div>
                
            </div>
            <div class="widget-foot">
                                
                <?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        </div>
    </div>
</section>

    <script type="text/javascript">

    	
    	
    	function get_accounty_type_list()
    	{
    		 var type = $("input[name='account_to_type']:checked").val();

           // var type = getRadioCheckedValue(radio_name);
            // $("#charge_to_id").customselect()="";
            if(type == 3)
            {
                $('#payment_to_div').css('display', 'block');
            }
            else
            {
                $('#payment_to_div').css('display', 'none');
            }

            var url = "<?php echo site_url();?>accounting/petty_cash/get_list_type/"+type;  
            
            //get department services
            $.get( url, function( data ) 
            {
                $( "#charge_to_id" ).html( data );
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

        function edit_direct_payment(account_payment_id)
        {

            document.getElementById("sidebar-right").style.display = "block"; 
            // document.getElementById("existing-sidebar-div").style.display = "none"; 

            var config_url = $('#config_url').val();
            var data_url = config_url+"accounting/petty_cash/edit_account_payment/"+account_payment_id;
            //window.alert(data_url);
            $.ajax({
            type:'POST',
            url: data_url,
            data:{account_payment_id: account_payment_id},
            dataType: 'text',
            success:function(data){

                document.getElementById("current-sidebar-div").style.display = "block"; 
                $("#current-sidebar-div").html(data);

            },
            error: function(xhr, status, error) {
            //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
            alert(error);
            }

            });
        }

        function close_side_bar()
        {
            // $('html').removeClass('sidebar-right-opened');
            document.getElementById("sidebar-right").style.display = "none"; 
            document.getElementById("current-sidebar-div").style.display = "none"; 
            // document.getElementById("existing-sidebar-div").style.display = "none"; 
            tinymce.remove();
        }


        function calendar_sidebar(appointment_id)
        {
         
          
        }


        $(document).on("submit","form#edit-direct-payment",function(e)
        {
            // alert('dasdajksdhakjh');
            e.preventDefault();
            // myApp.showIndicator();
            
            var form_data = new FormData(this);

            // alert(form_data);

            var config_url = $('#config_url').val();    

             var url = config_url+"accounting/petty_cash/edit_direct_payment_data";
            $.ajax({
            type:'POST',
            url: url,
            data:form_data,
            dataType: 'text',
            processData: false,
            contentType: false,
            success:function(data)
            {
              var data = jQuery.parseJSON(data);

                if(data.message == "success")
                {
                  
                  var redirect_url = $('#redirect_url').val();

                  window.location.href = config_url+'accounting/direct-purchases';
                
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
             
            
        });


    </script>