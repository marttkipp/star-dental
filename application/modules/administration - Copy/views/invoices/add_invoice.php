<div class="row">
	<div class="col-md-12">
		<div class="pull-right">
		 <a href="<?php echo site_url()?>/administration/invoices/custom_invoices" class="btn btn-sm btn-primary"> Back to invoices list </a>

		</div>
	</div>
</div>
<div class="row">
    <div class="col-md-12">

      <!-- Widget -->
      <div class="widget boxed">
        <!-- Widget head -->
        <div class="widget-head">

          <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?> </h4>
          <div class="widget-icons pull-right">
         
            <a href="#" class="wminimize"><i class="icon-chevron-up"></i></a> 
            <a href="#" class="wclose"><i class="icon-remove"></i></a>
          </div>
          <div class="clearfix"></div>
        </div>             

        <!-- Widget content -->
        <div class="widget-content">
          <div class="padd">
            <div class="center-align">
              <?php
                $error = $this->session->userdata('error_message');
                $validation_error = validation_errors();
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
            </div>
			
            <div class="row">
            	<div class="col-md-6">
                	<?php
                    	if($custom_invoice_query->num_rows() > 0)
						{
							$row = $custom_invoice_query->row();
							
							$created =  date('jS M Y H:i a',strtotime($row->custom_invoice_created));
							$personnel_id = $row->custom_invoice_created_by;
							$invoice_number = $row->custom_invoice_number;
							$debtor = $row->custom_invoice_debtor;
							$contacts = $row->custom_invoice_debtor_contacts;	
							$status = $row->custom_invoice_status;	
							$payable_by = $row->payable_by;		
							
							//get status
							if($status == 0)
							{
								$status = '<span class="label label-danger">Unpaid</span>';
							}
							
							else
							{
								$status = '<span class="label label-success">Paid</span>';
							}
							
							if($personnel_query->num_rows() > 0)
							{
								$personnel_result = $personnel_query->result();
								
								foreach($personnel_result as $adm)
								{
									$personnel_id2 = $adm->personnel_id;
									
									if($personnel_id == $personnel_id2)
									{
										$created_by = $adm->personnel_onames.' '.$adm->personnel_fname;
										break;
									}
									
									else
									{
										$created_by = '-';
									}
								}
							}
							
							$attributes = array('class'=>'form-horizontal', 'role'=>'form');
							 echo form_open('administration/invoices/update_invoice/'.$custom_invoice_id, $attributes);
							?>
                            <table class="table table-condensed table-striped table-hover">
                            	<tr>
                                	<th>Invoice number: </th>
                                    <td><?php echo $invoice_number;?></td>
                                </tr>
                            	<tr>
                                	<th>Created on: </th>
                                    <td><?php echo $created;?></td>
                                </tr>
                            	<tr>
                                	<th>Created by: </th>
                                    <td><?php echo $created_by;?></td>
                                </tr>
                            	<tr>
                                	<th>Status: </th>
                                    <td><?php echo $status;?></td>
                                </tr>
                            	<tr>
                                	<th>Debtor: </th>
                                    <td><input type="text" name="custom_invoice_debtor" class="form-control" value="<?php echo $debtor;?>" /></td>
                                </tr>
                            	<tr>
                                	<th>Debtor's contacts: </th>
                                    <td><input type="text" name="custom_invoice_debtor_contacts" class="form-control" value="<?php echo $contacts;?>" /></td>
                                </tr>
                            	<tr>
                                	<th>Payable by: </th>
                                    <td>
                                        <div id="datetimepicker4" class="input-append">
                                            <input data-format="yyyy-MM-dd" class="form-control" type="text" name="payable_by" placeholder="Payable by" value="<?php echo $payable_by;?>">
                                            <span class="add-on" style="cursor:pointer;">
                                                &nbsp;<i data-time-icon="icon-time" data-date-icon="icon-calendar">
                                                </i>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="center-align">
                            	<button type="submit" class="btn btn-info">Update</button>
                            </div>
                            <?php
							 echo form_close();
						}
						
					?>
                </div>
                
                <div class="col-md-6">
					<?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal'));?>
                         <div class="row">
                            <div class="col-md-12">
                                  <div class="form-group">
                                      <label class="col-lg-4 control-label">Item description</label>
                                      <div class="col-lg-8">
                                          <textarea class="form-control" name="custom_invoice_item_description" placeholder="Item description"><?php echo set_value('custom_invoice_item_description');?></textarea>
                                      </div>
                                  </div>
                                 
                                  <div class="form-group">
                                      <label class="col-lg-4 control-label">Item cost</label>
                                      <div class="col-lg-8">
                                          <input type="number" class="form-control" name="custom_invoice_item_cost" placeholder="Item cost" value="<?php echo set_value('custom_invoice_item_cost');?>">
                                      </div>
                                  </div>
                                 
                                  <div class="form-group">
                                      <label class="col-lg-4 control-label">Item quantity</label>
                                      <div class="col-lg-8">
                                          <input type="number" class="form-control" name="custom_invoice_item_quantity" placeholder="Item quantity" value="<?php echo set_value('custom_invoice_item_quantity');?>">
                                      </div>
                                  </div>
                                 
                              </div>
                             
                        </div>
        
                        <div class="center-align">
                          <button type="submit" class="btn btn-info btn-lg"> Add item</button>
                        </div>
                        <?php echo form_close();
                    ?>
                </div>
			</div>
            
            <div class="row">
            	<div class="col-md-12">
                	<a href="<?php echo site_url().'administration/invoices/print_invoice/'.$custom_invoice_id;?>" class="btn btn-success pull-right" target="_blank">Print invoice</a>
                	<h4 class="center-align">Invoice items</h4>
                	<?php
                    //invoice items
					if($query->num_rows() > 0)
					{
						?>
						<table class="table table-condensed table-striped table-hover">
							<tr>
								<th>#</th>
								<th>Description</th>
								<th>Quantity</th>
								<th>Cost (Ksh)</th>
								<th>Total (Ksh)</th>
								<th>Actions</th>
							</tr>
						<?php
						$total = $count = 0;
						foreach($query->result() as $res)
						{
							$custom_invoice_item_id = $res->custom_invoice_item_id;
							$description = $res->custom_invoice_item_description;
							$cost = $res->custom_invoice_item_cost;
							$quantity = $res->custom_invoice_item_quantity;
							$total += ($cost*$quantity);
							$count++;
							
							?>
							<tr>
								<td><?php echo $count;?></td>
								<td><?php echo $description;?></td>
								<td><?php echo $quantity;?></td>
								<td><?php echo number_format($cost, 2);?></td>
								<td><?php echo number_format(($cost*$quantity), 2);?></td>
                                <td><a href="<?php echo site_url().'administration/invoices/delete_invoice_item/'.$custom_invoice_item_id.'/'.$custom_invoice_id;?>" class="btn btn-sm btn-danger" onclick="return confirm('Do you really want to delete this item?');">Delete</a></td>
							</tr>
							<?php
						}
						?>
							<tr>
								<th colspan="4">Total</th>
								<td><?php echo number_format($total, 2);?></td>
							</tr>
						</table>
						<?php
					}
					
					else
					{
						echo '<p>This invoice does not contain particulars</p>';
					}
				?>
                    
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
</div>