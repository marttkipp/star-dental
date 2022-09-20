

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
                        <th>Status</th>
                        <th>Account Name</th>
                        <th>Ref Number</th>
                        <th>Date</th>
                        <th>Amount Paid</th>
                        <th>Amount Reconcilled</th>
                        <th>Invoice Number</th>
                        <th>Receipt No.</th>
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
                $amount = $row->amount;
                $visit_invoice_number = $row->visit_invoice_number;
                $confirm_number = $row->confirm_number;
                $current_payment_status = $row->current_payment_status;
                $payment_date = date('jS M Y',strtotime($row->payment_date));

                if($current_payment_status == 0)
                {
                	$color ='warning';
                	$status = 'Not Reconcilled';
                }
                else
                {
                	$color = 'success';
                	$status = 'Reconcilled';
                }
                
                $count++;
                $result .= 
                '
                    <tr>
                        <td class="'.$color.'">'.$count.'</td>
                        <td class="'.$color.'">'.$status.'</td>
                        <td>'.$account_name.'</td>
                        <td>'.$receipt_number.'</td>
                        <td>'.$payment_date.'</td>
                        <td>'.number_format($amount,2).'</td>
                        <td>'.number_format($amount,2).'</td>
                        <td>'.$visit_invoice_number.'</td>
                        <td>'.$confirm_number.'</td>
                       
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
            <a href="<?php echo site_url().'accounting/remittance-reconcilliations'?>" class="btn btn-md btn-warning" > <i class="fa fa-arrow-left"></i> Back to batch payments</a>
        </div>
    </header>
    <div class="panel-body">
       
        <div class="table-responsive">
            
            <?php echo $result;?>
    
        </div>
    </div>
    
    <div class="panel-foot">
        
        <?php if(isset($links)){echo $links;}?>
    
        <div class="clearfix"></div> 
    
    </div>
</section>