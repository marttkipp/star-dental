
    <div class="row">
        <div class="col-md-12">
            <section class="panel panel-featured panel-featured-info">
                <header class="panel-heading">
                    <h2 class="panel-title">Search Products</h2>
                </header>
    
                <div class="panel-body">
                	<div class="container">
						<?php
                    
                        $validation_error = validation_errors();
                        
                        if(!empty($validation_error))
                        {
                            echo '<div class="alert alert-danger">'.$validation_error.'</div>';
                        }
                        echo form_open('inventory_management/search_lpo_products/'.$lpo_id.'/'.$order_id, array('class'=>'form-horizontal'));
                        ?>
                        <div class="row">
                            <div class="col-sm-6">
                                
                                <div class="form-group">
                                    <label class="col-md-5 control-label">Product code </label>
                                    
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" name="product_code" placeholder="Product code">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-md-5 control-label">Product Item: </label>
                                    
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" name="product_name" placeholder="Product Item">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                
                                <div class="form-group">
                                    <label class="col-md-5 control-label">Product Category: </label>
                                    
                                    <div class="col-md-7">
                                         <select name="category_id" id="category_id" class="form-control">
                                            <?php
                                            echo '<option value="0">No Category</option>';
                                            if($all_categories->num_rows() > 0)
                                            {
                                                $result = $all_categories->result();
                                                
                                                foreach($result as $res)
                                                {
                                                    if($res->category_id == set_value('category_id'))
                                                    {
                                                        echo '<option value="'.$res->category_id.'" selected>'.$res->category_name.'</option>';
                                                    }
                                                    else
                                                    {
                                                        echo '<option value="'.$res->category_id.'">'.$res->category_name.'</option>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                	<div class="col-md-offset-5">
										<?php
                                        $search = $this->session->userdata('lpo_product_search');
                                        if(!empty($search))
                                        {
                                            ?>
                                                <a href="<?php echo site_url().'inventory_management/close_lpo_products_search/'.$lpo_id.'/'.$order_id;?>" class="btn btn-warning pull-left btn-sm">Close Search</a>
                                            <?php 
                                        }
                                        ?>
                                        <input type="submit" class="btn btn-info pull-right" value="Search" name="search"/>
                                        <input type="hidden" value="<?php echo $lpo_id?>" name="lpo_id">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php echo form_close();?>
                	</div>
                </div>
            </section>
        </div>
    </div>
    
		<section class="panel panel-featured panel-featured-info">
			<header class="panel-heading">
				<h2 class="panel-title">Product List</h2>
			</header>

			<div class="panel-body">
            
                <div class="row">
                    <div class="col-md-12">
                        
                        <a href="<?php echo site_url().'inventory/save-all-lpo-products/'.$lpo_id.'/'.$order_id;?>" class="btn btn-primary">Add All</a>
                    </div>
                </div>
				
                <div class="row">
                    <div class="col-md-12">
                        <table border="0" class="table table-hover table-condensed">
                            <thead> 
                                <th>#</th>
                                <th>Product code</th>
                                <th>Product Name </th>
                                <th>Category</th>
                                <th></th>
                            </thead>
                
                            <?php 
                            //echo "current - ".$current_item."end - ".$end_item;
                            
                            $rs9 = $query->result();
							$count = $page;
                            foreach ($rs9 as $rs10) :
                            	$product_code = $rs10->product_code;
                                $product_name = $rs10->product_name;
                                $category_name = $rs10->category_name;
                                $product_id = $rs10->product_id;
								$count++;
								?>
								<tr>
									<td><?php echo $count?></td>
									<td><?php echo $product_code?></td>
									<td><?php echo $product_name?></td>
									<td><?php echo $category_name?></td>
									<td><button class="btn btn-sm btn-info"  onclick="save_product(<?php echo $product_id?>,<?php echo $lpo_id?>,<?php echo $order_id?>)"><i class="fa fa-plus"></i></button></td>
								</tr>
                            <?php endforeach;?>
                        </table>
                    </div>
                </div>
				<?php
                if(isset($links)){echo $links;}
                ?>
            
            </div>

     </section>
                 

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

        
<script type="text/javascript">
	var config_url = '<?php echo site_url();?>';
	$(document).ready(function()
	{
		console.log('ready');
		get_requested_items_check(<?php echo $lpo_id;?>, <?php echo $order_id;?>);
	});

    function get_requested_items_check(lpo_id, order_id)
    {
        var url = config_url+"inventory/selected-lpo-items/"+lpo_id+"/"+order_id;
		$.get( url, function( data ) {
			$( "#current_requests" ).html( data );
		});
    }
	function save_product(product_id, lpo_id, order_id)
	{
    	var url = "<?php echo site_url();?>inventory/save-lpo-item/"+product_id+"/"+lpo_id+"/"+order_id;
		$.get( url, function( data ) 
		{
			alert(data);
			get_requested_items_check(lpo_id, order_id);
		});
	}
   
   function save_all_products(lpo_id, order_id)
   {
    	var url = "<?php echo site_url();?>inventory/save-all-product-request/"+lpo_id;
		$.get( url, function( data )
		{
			alert(data);
			get_requested_items_check(lpo_id, order_id);
		});
	}
	
	function change_quantity(lpo_item_id, lpo_id)
	{
		//var lpo_item_id = $(this).attr('href');
		var quantity = $('#quantity' + lpo_item_id).val();
		var url = "<?php echo base_url();?>inventory/update-lpo-items/" + lpo_item_id + '/' + quantity;
		$.ajax({
			type: 'POST',
			url: url,
			data: {
				quantity: quantity
			},
			dataType: 'json',
			success: function(data) {
	
				window.alert(data.result);
	
				get_requested_items_check(lpo_id, <?php echo $order_id;?>);
			},
			error: function(xhr, status, error) {
				alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	
			}
		});
		return false;
	}
	
	function remove_from_order(lpo_item_id, lpo_id) {
		//var lpo_item_id = $(this).attr('href');
		var url = "<?php echo base_url();?>inventory/remove-from-lpo/" + lpo_item_id;
		$.ajax({
			type: 'POST',
			url: url,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function(data) {
	
				window.alert(data.result);
	
				get_requested_items_check(lpo_id, <?php echo $order_id;?>);
			},
			error: function(xhr, status, error) {
				alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	
			}
		});
		return false;
	}
	
	function finish_making_request()
	{
		/*window.onunload = refreshParent;
	
		function refreshParent() {
			window.opener.location.reload();
		}*/
		window.close();
	}

  
</script>

