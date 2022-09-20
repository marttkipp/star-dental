<section class="panel">
    <!-- Widget head -->
    <header class="panel-heading">
      <h4 class="pull-left"><i class="icon-reorder"></i>Search Remittance Payments</h4>
      
      <div class="clearfix"></div>
    </header>             

    <!-- Widget content -->
     <div class="panel-body">
      <div class="padd">
        <?php echo form_open('administration/search-batch-payments', array("class" => "form-horizontal", "role" => "form"));?>
       
        
        <div class="row">
            <div class="col-md-12">
                 <div class="col-md-3">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Date From: </label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date_from" placeholder="Payment Date From" value=""  autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label">Date To: </label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date_to" placeholder="Payment Date to" value=""  autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="col-lg-4 control-label"> EFT/Code: </label>
                        <div class="col-lg-8">
                            <input type="text"  class="form-control" name="receipt_number" placeholder="Code" value="" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                     <div class="form-group">
                        <label class="col-lg-4 control-label">Bank: </label>
                        <div class="col-lg-8">
                            <select class="form-control" name="bank_id" >
                                <option value=""> ------ SELECT A BANK ------</option>
                                <?php
                                    $accounts = $this->hospital_administration_model->get_transacting_accounts("Bank");

                                    if($accounts->num_rows() > 0)
                                    {
                                        foreach ($accounts->result() as $key => $value) {
                                            # code...
                                            $account_id = $value->account_id;
                                            $account_name = $value->account_name;
                                            echo ' <option value="'.$account_id.'"> '.strtoupper($account_name).'</option>';
                                        }
                                    }
                                ?>
                                
                               
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Paid By: </label>
                        <div class="col-lg-8">
                            <select class="form-control" name="insurance_id" >
                                <option value=""> ------ SELECT AN INSURANCE ------</option>
                                <?php
                                    $visit_types_rs = $this->reception_model->get_visit_types();

                                    if($visit_types_rs->num_rows() > 0)
                                    {
                                        foreach ($visit_types_rs->result() as $key => $value) {
                                            # code...
                                            $visit_type_id = $value->visit_type_id;
                                            $visit_type_name = $value->visit_type_name;


                                            echo ' <option value="'.$visit_type_id.'"> '.strtoupper($visit_type_name).'</option>';
                                        }
                                    }
                                ?>
                                
                               
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="center-align">
                      <button type="submit" class="btn btn-info" > Search</button>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close();?>
    </div>
  </div>
</section>
<section class="panel">
    <!-- Widget head -->
    <header class="panel-heading">
      <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
      
      <div class="clearfix"></div>
    </header>             

    <!-- Widget content -->
     <div class="panel-body">
      <div class="padd">
        
        <div class="row">
            <div class="col-md-12">
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
            if(isset($import_response))
            {
                if(!empty($import_response))
                {
                    echo $import_response;
                }
            }
            
            if(isset($import_response_error))
            {
                if(!empty($import_response_error))
                {
                    echo '<div class="center-align alert alert-danger">'.$import_response_error.'</div>';
                }
            }


        ?>
            </div>
        </div>
        <?php echo form_open_multipart('administration/import-payments-values', array("class" => "form-horizontal", "role" => "form"));?>
       
        <div class="row">
            <div class="col-md-12">
                <ul>
                    <li>Download the import template <a href="<?php echo site_url().'administration/import-payments-template';?>">here.</a></li>
                    
                    <li>Save your file as a <strong>csv</strong> file before importing</li>
                    <li>After adding your patients to the import template please import them using the button below</li>
                </ul>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                 <div class="col-md-3">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Date: </label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date" placeholder="Visit Date" value="" required="required" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label"> EFT/Code: </label>
                        <div class="col-lg-8">
                            <input type="text"  class="form-control" name="receipt_number" placeholder="Code" value="" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Paid By: </label>
                        <div class="col-lg-8">
                            <select class="form-control" name="insurance_id" required="required">
                                <option value=""> ------ SELECT AN INSURANCE ------</option>
                                <?php
                                    $visit_types_rs = $this->reception_model->get_visit_types();

                                    if($visit_types_rs->num_rows() > 0)
                                    {
                                        foreach ($visit_types_rs->result() as $key => $value) {
                                            # code...
                                            $visit_type_id = $value->visit_type_id;
                                            $visit_type_name = $value->visit_type_name;


                                            echo ' <option value="'.$visit_type_id.'"> '.strtoupper($visit_type_name).'</option>';
                                        }
                                    }
                                ?>
                                
                               
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label"> Total Paid: </label>
                        <div class="col-lg-8">
                            <input type="text"  class="form-control" name="total_amount_paid" placeholder="Amount Paid" value="" autocomplete="off">
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-3">
                     <div class="form-group">
                        <label class="col-lg-4 control-label">Bank: </label>
                        
                        <div class="col-lg-8">
                            <select class="form-control" name="bank_id" required="required">
                                <option value=""> ------ SELECT A BANK ------</option>
                                <?php
                                    $accounts = $this->hospital_administration_model->get_transacting_accounts("Bank");

                                    if($accounts->num_rows() > 0)
                                    {
                                        foreach ($accounts->result() as $key => $value) {
                                            # code...
                                            $account_id = $value->account_id;
                                            $account_name = $value->account_name;
                                            echo ' <option value="'.$account_id.'"> '.strtoupper($account_name).'</option>';
                                        }
                                    }
                                ?>
                                
                               
                            </select>
                        </div>
                    </div>
                </div>
                <?php
                /*$data = array(
                      'class'       => 'custom-file-input btn-red btn-width',
                      'name'        => 'import_csv',
                      'onchange'    => 'this.form.submit();',
                      'type'        => 'file'
                    );
            
                echo form_input($data);*/
                ?>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="col-md-4 control-label">File: </label>
                        
                        <div class="col-md-8">
                            <input type="file" name="document_scan">
                            
                           
                        </div>
                    </div> 
                    <div class="form-group">
                        <div class="center-align">
                          <button type="submit" class="btn btn-info" onclick="return confirm('Are you sure you want to add this remittance ?')"> Add remitance</button>
                        </div>
                    </div>

                    <!-- <div class="fileUpload btn btn-info">
                        <span>Import payments</span>
                        <input type="file" class="upload"  name="import_csv" required="required" />
                    </div> -->
                </div>

                <div class="col-md-12">
                    
                </div>
            </div>
        </div>
        <?php echo form_close();?>
    </div>
  </div>
</section>


<?php
        

        $result = '';
        
        //if users exist display them
        if ($query->num_rows() > 0)
        {
            $count = $page;
            
            $result .= 
            '
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Company</th>
                        <th>Bank</th>
                        <th>Ref Number</th>
                        <th>Date</th>
                        <th>Amount Paid</th>
                        <th>Amount Reconcilled</th>
                        <th colspan="2">Actions</th>
                    </tr>
                </thead>
                  <tbody>
                  
            ';
            
            //get all administrators
            $administrators = $this->personnel_model->retrieve_personnel();
            if ($administrators->num_rows() > 0)
            {
                $admins = $administrators->result();
            }
            
            else
            {
                $admins = NULL;
            }
            
            foreach ($query->result() as $row)
            {
                $batch_receipt_id = $row->batch_receipt_id;
                $account_name = $row->account_name;
                $receipt_number = $row->receipt_number;
                $payment_date = $row->payment_date;
                $visit_type_name = $row->visit_type_name;
                $insurance_id = $row->insurance_id;
                $total_amount_paid = $row->total_amount_paid;
                $payment_date = date('jS M Y',strtotime($row->payment_date));

                $total_payments = $this->hospital_administration_model->get_receipt_amount($batch_receipt_id);
                $total_reconcilled = $this->hospital_administration_model->get_receipt_amount_paid($batch_receipt_id);
                $total_unallocated = $this->hospital_administration_model->get_all_unallocated_payments($batch_receipt_id);

                $total_reconcilled += $total_unallocated;
                //  <td><a href="'.site_url().'hospital_administration/update_payments/'.$batch_receipt_id.'" onclick="return confirm(\' Are you sure you want to reconcile this batch of payments ? \')" class="btn btn-xs btn-info" title=""><i class="fa fa-recycle"></i> reconcile payment</a></td>
                
                $count++;
                $result .= 
                '
                    <tr>
                        <td>'.$count.'</td>
                        <td>'.$visit_type_name.'</td>
                        <td>'.$account_name.'</td>
                        <td>'.$receipt_number.'</td>
                        <td>'.$payment_date.'</td>
                        <td>'.number_format($total_amount_paid,2).'</td>
                        <td>'.number_format($total_reconcilled,2).'</td>
                       
                         <td><a href="'.site_url().'hospital_administration/view_batch_items/'.$batch_receipt_id.'/'.$insurance_id.'" class="btn btn-xs btn-success" title=""><i class="fa fa-folder-open"></i> View Statement of accounts</a></td>
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
        <div class="panel-actions">
            <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
        </div>

        <h2 class="panel-title"><?php echo $title;?></h2>
    </header>
    <div class="panel-body">
        <?php
        
            $search_item = $this->session->userdata('batch_payments_search');

            if(!empty($search_item))
            {
                echo '<a href="'.site_url().'hospital_administration/close_batch_search" class="btn btn-warning btn-sm ">Close Search</a>';
            }
        ?>
       
        <div class="table-responsive">
            
            <?php echo $result;?>
    
        </div>
    </div>
    
    <div class="panel-foot">
        
        <?php if(isset($links)){echo $links;}?>
    
        <div class="clearfix"></div> 
    
    </div>
</section>