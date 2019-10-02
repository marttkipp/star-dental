
<section class="panel">
    <header class="panel-heading">
      <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
      <div class="widget-icons pull-right">
        <a href="<?php echo site_url().'inventory/products';?>" class="btn btn-sm btn-primary fa fa-back">Back to inventory</a>
        </div>
      <div class="clearfix"></div>
    </header>  
     <div class="panel-body">
        <div class="padd">

        	 <div class="clearfix"></div>

			     <div class="tabbable" style="margin-bottom: 18px;">
              <ul class="nav nav-tabs nav-justified">
                <li class="active">
                	<a href="#purchases" data-toggle="tab">Requests</a>
                </li>
                <!--<li class="active">
                	<a href="#products" data-toggle="tab">Products</a>
                </li>-->
              </ul>
              <div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
                <div class="tab-pane active" id="purchases">
                	
                    <div class="row">
                        <div class="col-sm-12 center-align">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#orders_modal">
                            	Create New Order
                            </button>
                            
                            <div class="modal fade" id="orders_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Create New Order</h4>
                                        </div>
                                        <div class="modal-body">
                                        	
                                        	<?php echo form_open('inventory_management/create_new_order/', array('class' => 'form-horizontal'));?>
                                            	<div class="form-group">
                                                    <label for="exampleInputEmail1" class="col-md-5">Order Date</label>
                                                    <div class="col-md-7">
                                                    	<div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </span>
                                                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="orders_date" placeholder="Order Date">
                                                        </div>
                                                    </div>
                                                </div>
                                            	<div class="form-group">
                                                    <label for="exampleInputEmail1" class="col-md-5">Store</label>
                                                    <div class="col-md-7">
                                                    	<select name="store_id" id="store_id" class="form-control" onchange="check_department_type()">
                                                        <option value="0">--Select Store--</option>
                                                    	 <?php
															if($store_priviledges->num_rows() > 0)
															{
																foreach ($store_priviledges->result() as $key)
																{
																	# code...
																	$store_parent = $key->store_parent;
																	$store_id = $key->store_id;
																	$store_name = $key->store_name;
																	
																	?>
																	<option value="<?php echo $store_id;?>"><?php echo $store_name;?></option>
																	<?php
																}
															}
														?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="supplier_id">
                                                    <label for="exampleInputEmail1" class="col-lg-5 control-label">Select Supplier: </label>
                                                    
                                                    <div class="col-lg-7">
                                                        <select name="supplier_id" class="form-control">
															<option value="">--Select Supplier</option>
                                                            <?php
                                                                if($suppliers->num_rows() > 0)
                                                                {
                                                                    foreach ($suppliers->result() as $key)
                                                                    {
                                                                        # code...
                                                                        $supplier_name = $key->supplier_name;
                                                                        $supplier_id = $key->supplier_id;
                                                                        
                                                                        ?>
                                                                        <option value="<?php echo $supplier_id;?>"><?php echo $supplier_name;?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                            ?> 
                                                        </select>
                                                    </div>
                                                </div>
                                            	<!--<div class="form-group">
                                                    <label for="exampleInputEmail1" class="col-md-5">Order Date</label>
                                                    <div class="col-md-7">
                                                    	<input type="text" class="form-control" id="exampleInputEmail1" placeholder="Email">
                                                    </div>
                                                </div>-->
                                                <div class="form-group">
                                                	<div class="col-sm-offset-2 col-sm-10">
                                                		<button type="submit" class="btn btn-primary">Create Order</button>
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
                            
                        </div>
                    </div>
                    
                	<div class="row">
                	 	<div class="col-sm-12">
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
							$validation_errors = validation_errors();
							if(!empty($validation_errors))
							{
								echo '<div class="alert alert-danger">'.$validation_errors.'</div>';
							}
							
							$errors = $this->session->userdata('error_message');
							if(!empty($errors))
							{
								echo '<div class="alert alert-danger">'.$errors.'</div>';
							}
							
							$success = $this->session->userdata('success_message');
							if(!empty($success))
							{
								echo '<div class="alert alert-danger">'.$success.'</div>';
							}
							?>
                	 		<table border="0" class="table table-hover table-condensed">
                                <thead> 
                                    <th>#</th>
                                    <th>Order Date</th>
                                    <th>Store Name</th>
                                    <th>Order Number</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </thead>
                    
                                <?php 
                                //echo "current - ".$current_item."end - ".$end_item;
                                
                                $rs9 = $query->result();
								$count = $page;
                                foreach ($rs9 as $rs10) :
                                    $store_parent = $rs10->store_parent;
                                    $orders_id = $rs10->order_id;
                                    $orders_date = $rs10->orders_date;
                                    $orders_number = $rs10->orders_number;
									$orders_approval_status = $rs10->orders_approval_status;
                                    $store_id = $rs10->store_id;
                                    $store_name = $rs10->store_name;
                                    $personnel_fname = $rs10->personnel_fname;
                                    $personnel_onames = $rs10->personnel_onames;
									$order_approval_status = $rs10->orders_approval_status;
									$status = $this->inventory_management_model->get_status($order_approval_status);
									$count++;
                                ?>
                                <tr>
                                    <td><?php echo $count;?></td>
                                    <td><?php echo date('jS M Y',strtotime($orders_date));?></td>
                                    <td><?php echo $store_name;?></td>
                                    <td><?php echo $orders_number;?></td>
                                    <td><?php echo $personnel_fname;?> <?php echo $personnel_onames;?></td>
                                    <td><?php echo $status;?></td>
                                    <?php
									if($orders_approval_status == 0)
									{
										?>
                                        <td><a href="#" class="btn btn-sm btn-info" onclick="open_window_for_parent_products(<?php echo $store_id;?>, <?php echo $orders_id;?>)">Add Products To <?php echo $store_name;?> order</a></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-success" data-toggle="modal" data-target="#import_orders_modal<?php echo $orders_id?>">Import Products To <?php echo $store_name;?> order</a>
                                            
                                            <div class="modal fade" id="import_orders_modal<?php echo $orders_id?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">Import Products To <?php echo $store_name;?> order</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            
                                                            <?php echo form_open_multipart('inventory/manage-store/', array('class' => 'form-horizontal'));?>
                                                                <input type="hidden" name="store_id" value="<?php echo $store_id?>" />
                                                                <input type="hidden" name="orders_id" value="<?php echo $orders_id?>" />
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <ul>
                                                                            <li>Download the import template <a href="<?php echo site_url().'inventory_management/order_items_import_template';?>">here.</a></li>
                                                                            <!--<li>Please categorise your products <strong>ONLY</strong> according to one of the categories <a href="<?php echo site_url().'vendor/import-categories';?>">here.</a></li>-->
                                                                            <li>Save your file as a <strong>csv</strong> file before importing</li>
                                                                            <li>After adding your products to the import template please import them using the button below</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <?php
                                                                        /*$data = array(
                                                                              'class'       => 'custom-file-input btn-red btn-width',
                                                                              'name'        => 'import_csv',
                                                                              'onchange'    => 'this.form.submit();',
                                                                              'type'       	=> 'file'
                                                                            );
                                                                    
                                                                        echo form_input($data);*/
                                                                        ?>
                                                                        <div class="fileUpload btn btn-primary">
                                                                            <span>Import products</span>
                                                                            <input type="file" class="upload" onChange="this.form.submit();" name="import_csv" />
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
                                        </td>
                                        <?php
									}
									else
									{
										?>
                                        <td></td>
                                        <td></td>
                                        <?php
									}
									
									?>
										
									<td><a href="<?php echo site_url().'receive-order/'.$orders_id.'/'.$store_parent;?>" class="btn btn-sm btn-warning">Recieve order</a></td>
                                </tr>
                                <?php endforeach;?>
                            </table>
                	 	</div>
                        <?php echo $links;?>
                	</div>
                    
                	<div class="row">
                	 	<div class="col-sm-12">
                	 		<div id="store_requests"></div>
                	 	</div>
                	</div>
                </div>
                <div class="tab-pane" id="products">
                	
                </div>

                
              </div>
            </div>
        </div>
     </div>
 </section> 

 <script>
    $('#myTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // store the currently selected tab in the hash value
    $("ul.nav-tabs > li > a").on("shown.bs.tab", function (e) {
        var id = $(e.target).attr("href").substr(1);
        window.location.hash = id;
    });

    // on load of the page: switch to the currently selected tab
    var hash = window.location.hash;
    $('#myTab a[href="' + hash + '"]').tab('show');
</script>
<script type="text/javascript">
	var config_url = '<?php echo site_url();?>';
    $(document).ready(function(){
      //get_requested_items();
    });
	function check_department_type()
	{
		var myTarget = document.getElementById("store_id").value;
		var store_id = myTarget; //alert(store_id);
		
		var url = "<?php echo site_url();?>inventory_management/check_store_parent/"+store_id;
		//alert(url);
		//get department services
		$.get( "<?php echo site_url();?>inventory_management/check_store_parent/"+store_id, function( data ) 
		{
			//alert(data);
			$( "#department_services" ).html( data );
		});
	}

    function get_requested_items()
    {
    	 var XMLHttpRequestObject = false;
            
        if (window.XMLHttpRequest) {
        
            XMLHttpRequestObject = new XMLHttpRequest();
        } 
            
        else if (window.ActiveXObject) {
            XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
        }
        var url = config_url+"inventory/store-requests";
       
        if(XMLHttpRequestObject) {
                    
            XMLHttpRequestObject.open("GET", url);
                    
            XMLHttpRequestObject.onreadystatechange = function(){
                
                if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
                    
                    document.getElementById("store_requests").innerHTML = XMLHttpRequestObject.responseText;
                }
            }
            
            XMLHttpRequestObject.send(null);
        }
    }
    function open_window_for_parent_products(store_id, order_id)
	{
	  window.open(config_url+"inventory/make-order/"+store_id+"/"+order_id,"Popup","height=600, width=800, , scrollbars=yes, "+ "directories=yes,location=yes,menubar=yes," + "resizable=no status=no,history=no top = 50 left = 100");
	  win.focus();
	}
</script>
<script type="text/javascript">
    function receive_quantity(product_deductions_id,store_id,product_id)
    {
      
       //var product_deductions_id = $(this).attr('href');
       var quantity = $('#quantity_received'+product_deductions_id).val();
       var url = "<?php echo base_url();?>inventory/receive-store-order/"+product_deductions_id+'/'+quantity+'/'+product_id+'/'+store_id;
  
        $.ajax({
           type:'POST',
           url: url,
           data:{quantity: quantity},
           cache:false,
           contentType: false,
           processData: false,
           dataType: 'json',
           success:function(data){
            
            window.alert(data.result);
            window.location.href = "<?php echo base_url();?>inventory/manage-store";
           },
           error: function(xhr, status, error) {
            alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
           
           }
        });
        return false;
     }
	 function check_department_type()
	 {
		var store = document.getElementById("store_id").value;
		var myTarget2 = document.getElementById("supplier_id");
		
		//check to see if store selected is parent
		var url = "<?php echo base_url();?>inventory_management/is_store_parent/"+store;
		
		$.ajax({
           type:'POST',
           url: url,
           data:{parent: parent},
           cache:false,
           contentType: false,
           processData: false,
           dataType: 'json',
           success:function(data)
		   {
			  if( data.Error )
			  {
			  }
			  else
			  {
				  show_supplier(data);
			  }
				
           },
		   error: function( e )
		   {
			  alert( JSON.stringify( e ) ); // This usually relates to server side errors, 404 not founds etc... so this function will not magically know that your validation failed... Although you could perhaps throw a PHP fatal error... 
		   }
        }); 

	}
	function show_supplier(data)
	{
	}
</script>