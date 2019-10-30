<?php 
// var_dump($deduction_type_id);die();
if($deduction_type_id == 0)
{
	echo $this->load->view('search_suppliers_credit_notes', '', TRUE);
}
else
{

}

?>
<section class="panel panel-featured panel-featured-info">
    <header class="panel-heading">
         <h2 class="panel-title pull-left"><?php echo $title;?></h2>
         <div class="widget-icons pull-right">
            	<a class="btn btn-success btn-sm" data-toggle='modal' data-target='#add_provider_items'>Add Credit Note</a>
          </div>
          <div class="clearfix"></div>
    </header>
    <div class="panel-body">
    	<div class="padd">
		<?php
			$error = $this->session->userdata('error_message');
			$success = $this->session->userdata('success_message');
			$search_result ='';
			$search_result2  ='';
			if(!empty($error))
			{
				$search_result2 = '<div class="alert alert-danger">'.$error.'</div>';
				$this->session->unset_userdata('error_message');
			}

			if(!empty($success))
			{
				$search_result2 ='<div class="alert alert-success">'.$success.'</div>';
				$this->session->unset_userdata('success_message');
			}

			$search = $this->session->userdata('supplier_credit_note_search');

			if(!empty($search))
			{
				$search_result = '<a href="'.site_url().'inventory/close-note-search" class="btn btn-danger">Close Search</a>';
			}


			$result = '<div class="padd">';
			$result .= ''.$search_result.'';
			$result .= '
					';

			//if users exist display them
			if ($query->num_rows() > 0)
			{
				$count = $page;

				$result .=
				'
				<div class="row">
					<div class="col-md-12">
						<table class="example table-autosort:0 table-stripeclass:alternate table  table-bordered " id="TABLE_2">
						  <thead>
							<tr>
							  <th >#</th>
							  <th class="table-sortable:default table-sortable" title="Click to sort">Order Date</th>
							  <th class="table-sortable:default table-sortable" title="Click to sort">Order Number</th>
							  <th class="table-sortable:default table-sortable" title="Click to sort">Credit Number</th>
							  <th class="table-sortable:default table-sortable" title="Click to sort">Transfer Store</th>
							  <th class="table-sortable:default table-sortable" title="Click to sort">Ordering Store</th>
							  <th class="table-sortable:default table-sortable" title="Click to sort">Created By</th>
							  <th class="table-sortable:default table-sortable" title="Click to sort">Status</th>
							  <th colspan="3">Actions</th>
							</tr>
						  </thead>
						  <tbody>
						';

							//get all administrators
							$personnel_query = $this->personnel_model->get_all_personnel();

							foreach ($query->result() as $row)
							{
								$order_id = $row->order_id;
								$order_number = $row->order_number;
								$order_status = $row->order_status_id;
								$order_instructions = $row->order_instructions;
								$order_status_name = $row->order_status_name;
								$created_by = $row->created_by;
								$creditor_name = $row->creditor_name;
								$created = $row->created;
								$modified_by = $row->modified_by;
								$store_id = $row->store_id;
								$store_name = $row->store_name;
								$last_modified = $row->last_modified;
								$order_approval_status = $row->order_approval_status;
								$created = $row->created;
								$supplier_invoice_number = $row->supplier_invoice_number;

								if(!empty($created))
								{
									$invoice_date = ''.date('jS M Y H:i a',strtotime($created)).'';
								}
								else
								{
									$invoice_date = '-';
								}

								// var_dump($order_approval_status); die();

								$order_details = $this->orders_model->get_order_items($order_id);
								$total_price = 0;
								$total_items = 0;
								//creators & editors

								if($personnel_query->num_rows() > 0)
								{
									$personnel_result = $personnel_query->result();

									foreach($personnel_result as $adm)
									{
										$personnel_id2 = $adm->personnel_id;

										if($created_by == $personnel_id2 ||  $modified_by == $personnel_id2 )
										{
											$created_by = $adm->personnel_fname;
											break;
										}

										else
										{
											$created_by = '-';
										}
									}
								}

								else
								{
									$created_by = '-';
								}


								$button = '';




								$approval_levels = $this->orders_model->check_if_can_access($order_approval_status,$order_id);

								$personnel_id = $this->session->userdata('personnel_id');

								$is_hod = $this->reception_model->check_if_admin($personnel_id,30);
								$is_admin = $this->reception_model->check_if_admin($personnel_id,1);



								// if($approval_levels == TRUE OR $personnel_id == 0 )
								// {

									$next_order_status = $order_approval_status+1;

									$status_name = $this->orders_model->get_next_approval_status_name($next_order_status);
									$is_hod = $this->reception_model->check_if_admin($personnel_id,30);
									$is_admin = $this->reception_model->check_if_admin($personnel_id,1);


									//pending order
									if($order_approval_status == 7 )
									{
										$status = '<span class="label label-success">Credit note has been closed</span>';
										$button = '';
										$button2 = '';
									}
									else
									{
										$status = '<span class="label label-default">Credit note is open</span>';
										$button = '';
										$button2 = '';
									}



									// just to mark for the next two stages


									$count++;
									$result .=
									'
										<tr>
											<td>'.$count.'</td>
											<td>'.$invoice_date.'</td>
											<td>'.$order_number.'</td>
											<td>'.$supplier_invoice_number.'</td>
											<td>'.$creditor_name.'</td>
											<td>'.$store_name.'</td>
											<td>'.$created_by.'</td>
											<td>'.$status.'</td>
											<td><a href="'.site_url().'procurement/credit-note-detail/'.$order_id.'" class="btn btn-info  btn-sm fa fa-eye"> VIEW DETAIL </a></td>
											<td><a href="'.base_url().'print-suppliers-credit-note/'.$order_id.'" target="_blank" class="btn btn-warning btn-sm"><i class="fa fa-print"></i> PRINT </a></td>



										</tr>
									';
								// }
							}

					$result .=
					'
							  </tbody>
							</table>
						</div>
					</div>
					';
			}
			// <td><a href="'.site_url().'procurement/delete-invoices/'.$order_id.'" class="btn btn-sm btn-danger  btn-sm fa fa-trash"> Delete </a></td>

			else
			{
				$result .= "There are no credit notes for suppliers created";
			}
			$result .= '</div>';
			echo $result;
		?>
	</div>

         <div class="widget-foot">

				<?php if(isset($links)){echo $links;}?>

                <div class="clearfix"></div>

            </div>
        </div>



     <div class="modal fade bs-example-modal-lg" id="add_provider_items" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add New Item</h4>
                </div>
                <?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
                <div class="modal-body">
			        <div class="row">
			        	<div class="col-md-12">
			                <div class="form-group">
			                	<label class="col-lg-2 control-label">Ordering Store</label>
			                    <div class="col-lg-10">
			                    	 <select name="store_id" id="store_id" class="form-control" required="">
	                                        <?php
	                                        $personnel_id = $this->session->userdata('personnel_id');
	                                        $all_stores = $this->stores_model->get_parent_stores($personnel_id);
	                                        echo '<option value="">No Store</option>';
	                                        if($all_stores->num_rows() > 0)
	                                        {
	                                            $result = $all_stores->result();

	                                            foreach($result as $res)
	                                            {
	                                                if($res->store_id == set_value('store_id'))
	                                                {
	                                                    echo '<option value="'.$res->store_id.'" selected>'.$res->store_name.'</option>';
	                                                }
	                                                else
	                                                {
	                                                    echo '<option value="'.$res->store_id.'">'.$res->store_name.'</option>';
	                                                }
	                                            }
	                                        }
	                                        ?>
	                                </select>

			                    </div>
			                </div>
			            </div>
			            <div class="col-md-12" style="margin-top: 20px;">
		                  	<div class="form-group">
		                        <label class="col-lg-2 control-label">CREDIT NOTE: </label>	                        
		                        <div class="col-lg-10">
		                            <input type="text" class="form-control" name="credit_note_number" placeholder="Credit Note Number" autocomplete="off" required="required">
		                        </div>
		                    </div>
		                </div>
		                 <div class="col-md-12" style="margin-top: 20px;">
			                <div class="form-group">
		                        <label class="col-lg-2 control-label">Date: </label>
		                        
		                        <div class="col-lg-10">
		                        	<div class="input-group">
		                                <span class="input-group-addon">
		                                    <i class="fa fa-calendar"></i>
		                                </span>
		                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="supplier_invoice_date" placeholder="Date" autocomplete="off" required="required">
		                            </div>
		                        </div>
		                    </div>
		                 </div>
		             	<div class="col-md-12" style="margin-top: 20px;">
		              		<div class="form-group">
			                	<label class="col-lg-2 control-label">Supplier</label>
			                    <div class="col-lg-10">
			                    	<select class="form-control" name="supplier_id" id="supplier_id" onchange="get_suppliers_invoices(this.value)" required="required">
			                    		<option>SELECT A SUPPLIER</option>
			                    		<?php
			                    		if($suppliers_query->num_rows() > 0)
			                    		{
			                    			foreach ($suppliers_query->result() as $key_supplier_items ) {
			                    				# code...
			                    				$creditor_id = $key_supplier_items->creditor_id;
			                    				$creditor_name = $key_supplier_items->creditor_name;

			                    				echo '<option value="'.$creditor_id.'">'.$creditor_name.'</option>';
			                    			}
			                    		}
			                    		?>
			                    	</select>
			                    </div>
			                </div>
			            </div>
			            <div class="col-md-12" style="margin-top: 20px;">
		              		<div class="form-group">
			                	<label class="col-lg-2 control-label">Invoice</label>
			                    <div class="col-lg-10">
			                    	<select class="form-control" name="reference_id" id="reference_id">


			                    	</select>
			                    </div>
			                </div>
			            </div>
			            <div class="col-md-12" style="margin-top: 20px;">
			            	<div class="form-group">
				                <label class="col-lg-2 control-label">Note Instructions</label>
				                <div class="col-lg-10">
				                	<textarea class="form-control" name="order_instructions"><?php echo set_value('order_instructions');?></textarea>
				                </div>
				            </div>
			            </div>
			        </div>
                </div>
                <div class="modal-footer">
                	<button type="submit" class='btn btn-info btn-sm' type='submit' >Add Credit Note</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
                <?php echo form_close();?>
            </div>
        </div>
</section>

<script type="text/javascript">
	function get_suppliers_invoices(creditor_id)
	{
		// alert(department_id);

        var url = "<?php echo site_url();?>inventory/orders/get_creditors_invoices/"+creditor_id;
        // alert(url);
        //get department services
        $.get( url, function( data )
        {
            $( "#reference_id" ).html( data );
            // $(".custom-select").customselect();
        });
	}
</script>
