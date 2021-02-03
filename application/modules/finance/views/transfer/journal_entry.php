<?php
 $document_number_two = $this->transfer_model->create_journal_number();
?>
<div class="row">
  <div class="col-md-12">

    <section class="panel">
        <header class="panel-heading">
            <h2 class="panel-title">Search Journal Entry
            </h2>
        </header>
        <div class="panel-body">

           <?php echo form_open("finance/transfer/search_journal_entry", array("class" => "form-horizontal"));?>
             <div class="row">
               <div class="col-md-12">
               	<div class="col-md-3">
                 <div class="form-group">
                     <label class="col-md-4 control-label">Account</label>

                     <div class="col-md-8">
                          <select id="account" name="account" class="form-control">                                    
                                <?php
                                	$changed = '<option value="">--- Account ---</option>';
                                 if($accounts->num_rows() > 0)
                                 {
                                     foreach($accounts->result() as $row):
                                         // $company_name = $row->company_name;
                                         $account_name = $row->account_name;
                                         $account_id = $row->account_id;
                                         $parent_account = $row->parent_account;

                                         if($parent_account != $current_parent)
                                         {
                                         	  $account_from_name = $this->transfer_model->get_account_name($parent_account);
                                         	$changed .= '<optgroup label="'.$account_from_name.'">';
                                         }

                                       	 $changed .= "<option value=".$account_id."> ".$account_name."</option>";
                                       	 $current_parent = $parent_account;
                                       	 if($parent_account != $current_parent)
                                         {
                                         	$changed .= '</optgroup>';
                                         }

                                     	 
                                     	
                                     endforeach;
                                 }
                                 echo $changed;
                                 ?>
                                  
								    
                            </select>
                     </div>
                 </div>
               </div>
               <div class="col-md-3">
                     <div class="form-group">
                         <label class="col-md-4 control-label">Date From: </label>

                         <div class="col-md-8">
                             <div class="input-group">
                                 <span class="input-group-addon">
                                     <i class="fa fa-calendar"></i>
                                 </span>
                                 <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_from" placeholder="Transaction date" value="" id="datepicker" >
                             </div>
                         </div>
                     </div>
               </div>
               <div class="col-md-3">

                       <div class="form-group">
                           <label class="col-md-4 control-label">Date To: </label>

                           <div class="col-md-8">
                               <div class="input-group">
                                   <span class="input-group-addon">
                                       <i class="fa fa-calendar"></i>
                                   </span>
                                   <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_ro" placeholder="Transaction date" value="" id="datepicker1" >
                               </div>
                           </div>
                       </div>


               </div>
               <div class="col-md-2">
                 <div class="form-group">
                     <label class="col-md-4 control-label">Ref No *</label>

                     <div class="col-md-8">
                         <input type="text" class="form-control" name="transaction_number" placeholder="Transaction Number" />
                     </div>
                 </div>
               </div>
               <div class="col-md-1">
                 <div class="form-group">
                   <div class="text-center">
                       <button type="submit" class="btn btn-sm btn-primary">Search</button>
                   </div>
                 </div>
              </div>
               </div>


             </div>
             <?php echo form_close();?>
            <hr>

          </div>
      </section>
  </div>
  <div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <h2 class="panel-title">Make Transfer
            </h2>
        </header>
        <div class="panel-body">
            <?php echo form_open_multipart($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Account to Credit</label>
                            <div class="col-lg-8">
                                <select id="account_from_id" name="account_from_id" class="form-control" onchange="get_accounty_type_list(this.value)" required>
                                    
                                    <?php
                                    	$changed = '<option value="">--- Account ---</option>';
                                     if($accounts->num_rows() > 0)
                                     {
                                         foreach($accounts->result() as $row):
                                             // $company_name = $row->company_name;
                                             $account_name = $row->account_name;
                                             $account_id = $row->account_id;
                                             $parent_account = $row->parent_account;

                                             if($parent_account != $current_parent)
                                             {
                                             	  $account_from_name = $this->transfer_model->get_account_name($parent_account);
                                             	$changed .= '<optgroup label="'.$account_from_name.'">';
                                             }

                                           	 $changed .= "<option value=".$account_id."> ".$account_name."</option>";
                                           	 $current_parent = $parent_account;
                                           	 if($parent_account != $current_parent)
                                             {
                                             	$changed .= '</optgroup>';
                                             }

                                         	 
                                         	
                                         endforeach;
                                     }
                                     echo $changed;
                                     ?>
                                      
									    
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-4 control-label">Reference No *</label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="reference_number" placeholder="Reference Number" value="<?php echo $document_number_two;?>" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-4 control-label">Transfer date: </label>

                            <div class="col-lg-8">
                                <div class="input-group">
                                    <span class="input-group-addon">
                               <i class="fa fa-calendar"></i>
                           </span>
                                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date" placeholder="Transfer Date" value="<?php echo date('Y-m-d');?>" id="datepicker2" required>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <!-- Activate checkbox -->


                        <div class="form-group">
                            <label class="col-lg-4 control-label">Account to Debit: </label>

                            <div class="col-lg-8">
                                <select name="account_to_id" class="form-control select2" id="charge_to_id" required>
                                  <option value="">---- select an account to Account to ------</option>

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
                            <label class="col-lg-4 control-label">Description *</label>
                            <div class="col-lg-8">
                                <textarea class="form-control" name="description" placeholder="Transfer Description" required="required"></textarea>
                            </div>
                        </div>
                        <div class="form-actions center-align">
                            <button class="submit btn btn-primary btn-sm" type="submit" onclick="return confirm('Are you sure you want to perform this entry ? ')">
                                Transfer
                            </button>
                        </div>
                    </div>

                </div>
            <?php echo form_close();?>
        </div>
      </section>
  </div>
</div>

 <section class="panel">
    <header class="panel-heading">
          <h4 class="pull-left"><i class="icon-reorder"></i>Journal Entry Form</h4>
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
    		$search = $this->session->userdata('search_journal');
            if(!empty($search))
            {
              ?>
              <a href="<?php echo base_url().'finance/transfer/close_journal_search';?>" class="btn btn-sm btn-success"><i class="fa fa-print"></i> Close Search</a>
              <?php
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
                              <th>Credited Account</th>
                              <th>Debited Account</th>
                              <th>Transacted Amount</th> 
                              <th>Entry Description</th>                     
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
                                        $receipt_number = $value->document_number;
                                        $journal_entry_id = $value->journal_entry_id;
                                         $payment_date = $value->payment_date;
                                         $created = $value->created;
                                        $amount_paid = $value->amount_paid;
                                        $payment_to = $value->payment_to;
                                        $journal_entry_description = $value->journal_entry_description;

                                        $account_from_name = $this->transfer_model->get_account_name($account_from_id);
                                        $account_to_name = $this->transfer_model->get_account_name($account_to_id);
                                        


                                        if($created == date('Y-m-d'))
                                        {
                                            $add_invoice = '<td><a href="'.site_url().'delete-journal-entry/'.$journal_entry_id.'" class="btn btn-xs btn-danger fa fa-trash" onclick="return confirm(\'Do you really want delete this entry?\');"></a></td>';
                                        }
                                        else
                                        {
                                            $add_invoice = '';
                                        }

                                        $x++;

                                        $result .= '<tr>
                                                        <td>'.$x.'</td>
                                                        <td>'.$payment_date.'</td>
                                                        <td>'.strtoupper($receipt_number).'</td>
                                                        <td>'.$account_from_name.'</td>
                                                        <td>'.$account_to_name.'</td>
                                                        <td>'.number_format($amount_paid,2).'</td>
                                                        <td>'.$journal_entry_description.'</td>
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
    function get_accounty_type_list(radio_name) {
        var type = radio_name;
        // $("#charge_to_id").customselect()="";
        // alert(radio_name);
        var url = "<?php echo site_url();?>finance/transfer/get_other_accounts/" + type;
        // alert(url);
        //get department services
        $.get(url, function(data) {
            $("#charge_to_id").html(data);
            // $(".custom-select").customselect();
        });

    }

    function getRadioCheckedValue(radio_name) {
        var oRadio = document.forms[0].elements[radio_name];

        for (var i = 0; i < oRadio.length; i++) {
            if (oRadio[i].checked) {
                return oRadio[i].value;
            }
        }

        return '';
    }
</script>
