
<?php //echo $this->load->view('search/providers', '', TRUE);?>
<!-- search -->
<!-- end search -->

<div class="row" style="margin-top: 5px;">
    <div class="col-md-12">

        <section class="panel">
            <header class="panel-heading">
                
                <h2 class="panel-title"><?php echo $title;?></h2>
                <a href="<?php echo site_url();?>accounting/providers" class="btn btn-sm btn-warning pull-right" style="margin-top: -25px; "><i class="fa fa-arrow-left"></i> Back to providers</a>
                <a href="<?php echo base_url().'accounting/creditors/print_provider_account/'.$provider_id?>" class="btn btn-sm btn-success pull-right"  style="margin-top: -25px;margin-right: 5px;" target="_blank"><i class="fa fa-print"></i> Print</a>
                <button type="button" class="btn btn-sm btn-primary pull-right"  data-toggle="modal" data-target="#record_creditor_account" style="margin-top: -25px; margin-right: 5px;"><i class="fa fa-plus"></i> Record</button>

                    
            </header>
            
            <div class="panel-body">
                <div class="pull-right">
                    
                    <!--<a href="<?php echo base_url().'administration/sync_app_creditor_account';?>" class="btn btn-sm btn-info"><i class="fa fa-sign-out"></i> Sync</a>-->
                </div>
                <!-- Modal -->
                <div class="modal fade" id="record_creditor_account" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Record Payment</h4>
                            </div>
                            <div class="modal-body">
                                <?php echo form_open("accounting/creditors/record_provider_account/".$provider_id."/0", array("class" => "form-horizontal"));?>
                                <input type="hidden" name="account_from_id" value="<?php echo $provider_id;?>">


                                 <div class="form-group">
                                    <label class="col-lg-4 control-label">Account To</label>
                                    <div class="col-lg-8">
                                        <select id="account_to_id" name="account_to_id" class="form-control" required="required">
                                            <option value="">--- Account ---</option>
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
                                    <label class="col-md-4 control-label">Last Day of invoiced Month: </label>
                                    
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="creditor_account_date" placeholder="Month date" required="required">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Payment Date: </label>
                                    
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date" placeholder="Payment date" value="<?php echo date('Y-m-d')?>" required="required">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Description *</label>
                                    
                                    <div class="col-md-8">
                                        <textarea class="form-control" name="creditor_account_description"></textarea>
                                    </div>
                                </div>
                                <input type="hidden" name="redirect_url" id="redirect_url" value="<?php echo $this->uri->uri_string()?>">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Amount *</label>
                                    
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="creditor_account_amount" placeholder="Amount" required="required"/>
                                    </div>
                                </div>

                                 <div class="form-group">
                                    <label class="col-md-4 control-label">Transaction Code *</label>
                                    
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="transaction_code" placeholder="Cheque/Mpesa code" required="required" />
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-8 col-md-offset-4">
                                        <div class="center-align">
                                            <button type="submit" class="btn btn-primary" onclick="return confirm('Do you want to save this record ? ')">Save record</button>
                                        </div>
                                    </div>
                                </div>
                                <?php echo form_close();?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
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
                    
            $search = $this->session->userdata('creditor_payment_search');
            $search_title = $this->session->userdata('creditor_search_title');
        
            if(!empty($search))
            {
                echo '
                <a href="'.site_url().'accounting/creditors/close_creditor_search/'.$provider_id.'" class="btn btn-warning btn-sm ">Close Search</a>
                ';
                echo $search_title;
            }   

                $creditor_result = $this->creditors_model->get_provider_statement($provider_id,$personnel_type_id,$personnel_percentage);
            ?>

                <table class="table table-hover table-bordered ">
                    <thead>
                        <tr>
                          <th>Period Date</th> 
                          <th>Cash</th>
                          <th>Insurance</th>
                          <th>Gross Payable</th>
                          <th>Lab Work</th>
                          <th>Total Payable</th>
                          <th>60 %</th> 
                          <th>40 %</th>
                          <th>Payments</th>   
                          <th>Balance</th>                    
                        </tr>
                     </thead>
                    <tbody>
                        <?php  echo $creditor_result['result'];?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>