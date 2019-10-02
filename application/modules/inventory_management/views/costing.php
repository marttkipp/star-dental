
		<section class="panel panel-featured panel-featured-info">
			<header class="panel-heading">
            	<a href="<?php echo site_url().'laboratory-setup/tests';?>" class="btn btn-success btn-sm pull-right">Back to tests</a>
				<h2 class="panel-title"><?php echo $title;?></h2>
			</header>

			<div class="panel-body">
            	
                <?php 
					$validation_error = validation_errors();
					
					if(!empty($validation_error))
					{
						echo '<div class="alert alert-danger center-align">'.$validation_error.'</div>';
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
				?>
                <div class="row">
                    <div class="col-md-12">
                        <section class="panel panel-featured panel-featured-info">
                            <header class="panel-heading">
                                <h2 class="panel-title">Selected Products</h2>
                            </header>
                
                            <div class="panel-body">
                                <div id="current_requests"></div>
                            </div>
                        </section>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <section class="panel panel-featured panel-featured-info">
                            <header class="panel-heading">
                                <h2 class="panel-title">Add Products</h2>
                            </header>
                
                            <div class="panel-body">
                            	<?php
                
								echo form_open('inventory_management/make_costing_search/'.$lab_test_id, array('class'=>'form-horizontal'));
								?>
								<div class="row">
									<div class="col-md-5">
										
										<div class="form-group">
											<label class="col-md-5 control-label">Product Item: </label>
											
											<div class="col-md-7">
												<input type="text" class="form-control" name="product_name" placeholder="Product Item">
											</div>
										</div>
									</div>
									
									<div class="col-md-2">
										<?php
										$search = $this->session->userdata('make_costing_search');
										if(!empty($search))
										{
											?>
												<a href="<?php echo site_url().'inventory_management/close_costing_search/'.$lab_test_id;?>" class="btn btn-warning pull-left">Close Search</a>
											<?php 
										}
										?>
										<input type="submit" class="btn btn-info pull-right" value="Search" name="search"/>
										<input type="hidden" value="<?php echo $lab_test_id?>" name="lab_test_id">
									</div>
								</div>
								<?php echo form_close();?>
                
                                <table border="0" class="table table-hover table-condensed">
                                    <thead> 
                                        <th>#</th>
                                        <th> Product Name </th>
                                        <th> Price (Ksh)</th>
                                    </thead>
                        
                                    <?php 
                                    //echo "current - ".$current_item."end - ".$end_item;
                                    $count = $page;
                                    $rs9 = $query->result();
                                    foreach ($rs9 as $rs10) :
                                        $product_name = $rs10->product_name;
                                        $category_name = $rs10->category_name;
                                        $product_id = $rs10->product_id;
                                        $product_unitprice = $rs10->product_unitprice;
                                        $count++;
                                    
                                    ?>
                                    <tr>
                                        <td><?php echo $count?></td>
                                        <td><?php echo $product_name?></td>
                                        <td><?php echo number_format($product_unitprice, 2);?></td>
                                        <td><a class='btn btn-warning btn-sm' href="<?php echo site_url().'inventory_management/add_costing/'.$product_id.'/'.$lab_test_id;?>" onClick="return confirm('Are you sure you want to add this charge')"><i class="fa fa-plus"></i> Add charge</a></td>
                                    </tr>
                                    <?php endforeach;?>
                                </table>
                            </div>
                        </section>
                    </div>
                </div>
				<?php
                if(isset($links)){echo $links;}
                ?>
            
            </div>

     </section>
                 
<script type="text/javascript">
   var config_url = '<?php echo site_url();?>';
    $(document).ready(function(){
      get_requested_items_check(<?php echo $lab_test_id;?>);
    });

    function get_requested_items_check(lab_test_id)
    {
         var XMLHttpRequestObject = false;
            
        if (window.XMLHttpRequest) {
        
            XMLHttpRequestObject = new XMLHttpRequest();
        } 
            
        else if (window.ActiveXObject) {
            XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
        }
        var url = config_url+"inventory_management/test_costings/"+lab_test_id;
        
        if(XMLHttpRequestObject) {
                    
            XMLHttpRequestObject.open("GET", url);
                    
            XMLHttpRequestObject.onreadystatechange = function(){
                
                if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
                    
                    document.getElementById("current_requests").innerHTML = XMLHttpRequestObject.responseText;
                }
            }
            
            XMLHttpRequestObject.send(null);
        }
    }
	
	function save_product(product_id, lab_test_id){
		
		var XMLHttpRequestObject = false;
			
		if (window.XMLHttpRequest) {
		
			XMLHttpRequestObject = new XMLHttpRequest();
		} 
			
		else if (window.ActiveXObject) {
			XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
		}
		var url = "<?php echo site_url();?>inventory/save-product-request/"+product_id+"/"+lab_test_id;
		// window.alert(url);
		if(XMLHttpRequestObject) {
					
			XMLHttpRequestObject.open("GET", url);
					
			XMLHttpRequestObject.onreadystatechange = function(){
				
				if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
					
				   document.getElementById("current_requests").innerHTML = XMLHttpRequestObject.responseText;
				   get_requested_items_check(lab_test_id);
				}
			}
			
			XMLHttpRequestObject.send(null);
		}
	}
	
	$(document).on("click","a.change_quantity",function(e)
	{
		e.preventDefault();
		
		var test_costing_id = $( this ).attr('test_costing_id');
		var lab_test_id = $( this ).attr('lab_test_id');
		var quantity = $( '#quantity'+test_costing_id ).val();
		var url = "<?php echo base_url();?>inventory_management/update_test_costing/"+test_costing_id+'/'+quantity;
		$.ajax({
			type:'POST',
			url: url,
			data:{quantity: quantity},
			cache:false,
			contentType: false,
			processData: false,
			dataType: 'json',
			success:function(data){
				
				//get_requested_items();
				get_requested_items_check(lab_test_id);
				window.alert(data.result);
			},
			error: function(xhr, status, error) {
				alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
			
			}
		});
		return false;
	});

</script>

