<!-- search -->
<?php //echo $this->load->view('invoices/search_custom_invoices', '', TRUE);?>
<!-- end search -->
<style type="text/css">
.bootstrap-datetimepicker-widget{z-index:2000;}
</style>

<div class="row">
    <div class="col-md-12">

      <!-- Widget -->
      <div class="widget boxed">
        <!-- Widget head -->
        <div class="widget-head">
          <h4 class="pull-left"><i class="icon-reorder"></i>Custom invoices</h4>
          <div class="widget-icons pull-right">
            <a href="#" class="wminimize"><i class="icon-chevron-up"></i></a> 
            <a href="#" class="wclose"><i class="icon-remove"></i></a>
          </div>
          <div class="clearfix"></div>
        </div>             

        <!-- Widget content -->
        <div class="widget-content">
          <div class="padd">
          <h5 class="center-align"><?php echo $this->session->userdata('search_title');?></h5>
	<?php
				
		$search = $this->session->userdata('custom_invoice_search');
		
		if(!empty($search))
		{
			$search_result = '<a href="'.site_url().'administration/invoices/close_custom_invoice_search" class="btn btn-danger">Close Search</a>';
		}
		
		$result = 
		'
		
		<!-- Button to trigger modal -->
		<a href="#new_invoice" class="btn btn-primary" data-toggle="modal"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create new invoice</a>
		
		<!-- Modal -->
		<div id="new_invoice" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title">Add new invoice</h4>
					</div>
					
					<div class="modal-body">
					'.form_open("administration/invoices/add_invoice/", array("class" => "form-horizontal")).'
                    <div class="row">
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Debtor: </label>
                                
                                <div class="col-lg-8">
                                    <input class="form-control" type="text" name="custom_invoice_debtor" placeholder="Debtor" value="'.set_value('custom_invoice_debtor').'">
                                </div>
                            </div>
							
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Debtor contacts: </label>
                                
                                <div class="col-lg-8">
                                    <textarea class="form-control" name="custom_invoice_debtor_contacts" placeholder="Debtor contacts">'.set_value('custom_invoice_debtor_contacts').'</textarea>
                                </div>
                            </div>
							
							<div class="form-group">
                                <label class="col-lg-4 control-label">Payable by: </label>
                                
                                <div class="col-lg-8">
                                    <div id="datetimepicker4" class="input-append">
                                        <input data-format="yyyy-MM-dd" class="form-control" type="text" name="payable_by" placeholder="Payable by">
                                        <span class="add-on" style="cursor:pointer;">
                                            &nbsp;<i data-time-icon="icon-time" data-date-icon="icon-calendar">
                                            </i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                    	</div>
                    </div>   
                    
                    <div class="center-align">
                        <button type="submit" class="btn btn-info btn-lg">Create new invoice</button>
                    </div>

                    '.form_close().'
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
					</div>
				</div>
			</div>
		</div>
		';
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			$result .= 
			'
				<table class="table table-condensed table-striped table-hover">
					<tr>
						<th>#</th>
						<th>Invoice number</th>
						<th>Debtor</th>
						<th>Created on</th>
						<th>Created by</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
			';
			
			foreach ($query->result() as $row)
			{
				$created =  date('jS M Y H:i a',strtotime($row->custom_invoice_created));
				$personnel_id = $row->custom_invoice_created_by;
				$invoice_number = $row->custom_invoice_number;
				$debtor = $row->custom_invoice_debtor;
				$contacts = $row->custom_invoice_debtor_contacts;	
				$status = $row->custom_invoice_status;			
				$last_modified = $row->custom_invoice_last_modified;
				$modified_by = $row->custom_invoice_modified_by;	
				$custom_invoice_id = $row->custom_invoice_id;		
				
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
				
				$total_price = 0;
				$total_items = 0;
				
				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$invoice_number.'</td>
						<td>'.$debtor.'</td>
						<td>'.$created.'</td>
						<td>'.$created_by.'</td>
						<td>'.$status.'</td>
						<td><a href="'.site_url().'administration/invoices/add_invoice_items/'.$custom_invoice_id.'" class="btn btn-info">View invoice</a></td>
					</tr> 
				';
			}
			
			$result .= 
			'
						  </tbody>
						</table>
					</div>
			';
		}
		
		else
		{
			$result .= "There are no custom invoices";
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
		echo $result;
?>
          </div>
          
          <div class="widget-foot">
                                
				<?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        </div>
        <!-- Widget ends -->

      </div>
    </div>
  </div>