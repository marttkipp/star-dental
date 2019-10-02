<section class="panel">
	<?php //echo $orders_id;die();?>
	<header class="panel-heading">
		<h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
		<div class="widget-icons pull-right">
			<a href="<?php echo site_url().'inventory/manage-store';?>" class="btn btn-sm btn-primary fa fa-back">Back to store management</a>
		</div>
		<div class="clearfix"></div>
	</header>
	<div class="panel-body">
		<div class="padd">
			<div class="clearfix"></div>
			<div class="tabbable" style="margin-bottom: 18px;">
				<ul class="nav nav-tabs nav-justified">
					<li class="active">
						<a href="#purchases" data-toggle="tab">Requested Items</a>
					</li>
					<!--<li>
						<a href="#orders" data-toggle="tab">Allocations Orders</a>
						</li>-->
				</ul>
				<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
					<div class="tab-pane active" id="purchases">
						<br>
						<div class="row">
							<div class="col-sm-12">
								<div id="store_requests"></div>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="orders">
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
 <script>
	var config_url = '<?php echo site_url();?>';
    $(document).ready(function(){
		console.log('here');
		get_requested_items();
    });

    function get_requested_items() {
		var XMLHttpRequestObject = false;
	
		if (window.XMLHttpRequest) {
	
			XMLHttpRequestObject = new XMLHttpRequest();
		} else if (window.ActiveXObject) {
			XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
		}
		var url = config_url + "inventory/store-requests/<?php echo $orders_id;?>/<?php echo $store_parent;?>";
	
		if (XMLHttpRequestObject) {
	
			XMLHttpRequestObject.open("GET", url);
	
			XMLHttpRequestObject.onreadystatechange = function() {
	
				if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
	
					document.getElementById("store_requests").innerHTML = XMLHttpRequestObject.responseText;
					
					$(function() {
						$(".custom-select").customselect();
					});
					
					(function( $ ) {

						'use strict';
					
						if ( $.isFunction($.fn[ 'datepicker' ]) ) {
					
							$(function() {
								$('[data-plugin-datepicker]').each(function() {
									var $this = $( this ),
										opts = {};
					
									var pluginOptions = $this.data('plugin-options');
									if (pluginOptions)
										opts = pluginOptions;
					
									$this.themePluginDatePicker(opts);
								});
							});
					
						}
					
					}).apply(this, [ jQuery ]);
				}
			}
	
			XMLHttpRequestObject.send(null);
		}
	}
	
	function open_window_for_parent_products(store_id) {
		window.open(config_url + "inventory/make-order/" + store_id, "Popup", "height=1200, width=800, , scrollbars=yes, " + "directories=yes,location=yes,menubar=yes," + "resizable=no status=no,history=no top = 50 left = 100");
	}
	
    function receive_quantity(product_deductions_id, store_id, product_id)
	{
		//var product_deductions_id = $(this).attr('href');
		var quantity = $('#quantity_received' + product_deductions_id).val();
		var url = "<?php echo base_url();?>inventory/receive-store-order/" + product_deductions_id + '/' + quantity + '/' + product_id + '/' + store_id;
	
		$.ajax({
			type: 'POST',
			url: url,
			data: {
				quantity: quantity
			},
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function(data) {
	
				$('#quantity_given' + product_deductions_id).html(quantity);
				window.alert(data.result);
				//window.location.href = "inventory_management/recieve_order/"+order_id;
			},
			error: function(xhr, status, error) {
				alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	
			}
		});
		return false;
	}
	
	function receive_purchase(product_deductions_id, product_id, store_parent)
	{
		//var product_deductions_id = $(this).attr('href');
		var purchase_quantity = $('#purchase_quantity' + product_deductions_id).val();
		var purchase_pack_size = $('#purchase_pack_size' + product_deductions_id).val();
		var expiry_date = $('#expiry_date' + product_deductions_id).val();
		var url = "<?php echo base_url();?>inventory/receive-store-purchase/" + product_id + '/' +  product_deductions_id + '/' +  store_parent;
	
		$.ajax({
			type: 'POST',
			url: url,
			data: {
				purchase_quantity: purchase_quantity,
				purchase_pack_size: purchase_pack_size,
				expiry_date: expiry_date
			},
			cache: false,
			dataType: 'json',
			success: function(data) 
			{
				if(data.message == 'success')
				{
					purchase_quantity = parseFloat(purchase_quantity);
					purchase_pack_size = parseFloat(purchase_pack_size);
					
					var total_given = purchase_quantity * purchase_pack_size;
					$('#quantity_given' + product_deductions_id).html(total_given);
				}
				window.alert(data.result);
				//window.location.href = "inventory_management/recieve_order/"+order_id;
			},
			error: function(xhr, status, error) {
				alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	
			}
		});
		return false;
	}
	
	$(document).on("submit","form#search-product",function(e)
	{
		e.preventDefault();
		var form_data = new FormData(this);
		var url = $(this).attr('action');
		$.ajax({
			type: 'POST',
			url: url,
			processData: false,
			contentType: false,
			data: form_data,
			dataType: 'text',
			success: function(data) 
			{
				$("#store_requests").html(data);
				$(function() {
					$(".custom-select").customselect();
				});
				
				(function( $ ) {

					'use strict';
				
					if ( $.isFunction($.fn[ 'datepicker' ]) ) {
				
						$(function() {
							$('[data-plugin-datepicker]').each(function() {
								var $this = $( this ),
									opts = {};
				
								var pluginOptions = $this.data('plugin-options');
								if (pluginOptions)
									opts = pluginOptions;
				
								$this.themePluginDatePicker(opts);
							});
						});
				
					}
				
				}).apply(this, [ jQuery ]);
			},
			error: function(xhr, status, error) {
				alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	
			}
		});
		return false;
	});
	
	$(document).on("click","a.close-request-search",function(e)
	{
		e.preventDefault();
		var url = $(this).attr('href');
		$.ajax({
			type: 'POST',
			url: url,
			dataType: 'text',
			success: function(data) 
			{
				$("#store_requests").html(data);
				$(function() {
					$(".custom-select").customselect();
				});
				
				(function( $ ) {

					'use strict';
				
					if ( $.isFunction($.fn[ 'datepicker' ]) ) {
				
						$(function() {
							$('[data-plugin-datepicker]').each(function() {
								var $this = $( this ),
									opts = {};
				
								var pluginOptions = $this.data('plugin-options');
								if (pluginOptions)
									opts = pluginOptions;
				
								$this.themePluginDatePicker(opts);
							});
						});
				
					}
				
				}).apply(this, [ jQuery ]);
			},
			error: function(xhr, status, error) {
				alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	
			}
		});
		return false;
	});
	
	$(document).on("click","ul.pagination li a",function(e)
	{
		e.preventDefault();
		var url = $(this).attr('href');
		$.ajax({
			type: 'POST',
			url: url,
			dataType: 'text',
			success: function(data) 
			{
				$("#store_requests").html(data);
				$(function() {
					$(".custom-select").customselect();
				});
				
				(function( $ ) {

					'use strict';
				
					if ( $.isFunction($.fn[ 'datepicker' ]) ) {
				
						$(function() {
							$('[data-plugin-datepicker]').each(function() {
								var $this = $( this ),
									opts = {};
				
								var pluginOptions = $this.data('plugin-options');
								if (pluginOptions)
									opts = pluginOptions;
				
								$this.themePluginDatePicker(opts);
							});
						});
				
					}
				
				}).apply(this, [ jQuery ]);
			},
			error: function(xhr, status, error) {
				alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	
			}
		});
		return false;
	});
</script>