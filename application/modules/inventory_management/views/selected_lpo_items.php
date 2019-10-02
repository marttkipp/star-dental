<?php
$current_query = $this->inventory_management_model->selected_lpo_items($lpo_id, $order_id);
?>
<table class='table table-striped table-hover table-condensed'>
	<tr>
		<th>No.</th>
    	<th>Product Name</th>
		<th>QTY Requested</th>
		<th>Units</th>
		<th colspan="2">Actions</th>
	</tr>
	<tbody>
	<?php
		if($current_query->num_rows() > 0)
		{
			$count = 0;
			foreach ($current_query->result() as $key) {
				$product_id = $key->product_id;
				$product_name = $key->product_name;
				$unit_name = $key->unit_name;
				$product_code = $key->product_code;
				$lpo_item_id = $key->lpo_item_id;
				$quantity_requested = $key->quantity_requested;
				$count++;
				?>
				
					<tr>
			        	<td><?php echo $count;?></td>
						<td><?php echo $product_name;?></td>
						<td><input type="text" class="form-control" id="quantity<?php echo $lpo_item_id;?>" size="2" value="<?php echo $quantity_requested;?>"></td>
						<td><?php echo $unit_name;?></td>
						<td><button id="update_action_point_form" class='btn btn-warning btn-sm fa fa-edit' onclick="change_quantity(<?php echo $lpo_item_id;?>,<?php echo $lpo_id;?>)"> Update quantity</button>
						<button class='btn btn-danger btn-sm fa fa-trash' onclick="remove_from_order(<?php echo $lpo_item_id;?>,<?php echo $lpo_id;?>)"> Remove from LPO</button></td>
						
					</tr>
				
				<?php
			}
		}
	?>
		
	</tbody>
</table>
<div class="row">
	<div class="col-md-12 center-align">
		<a class='btn btn-sm btn-success' onclick='finish_making_request()'> Done Making LPO </a>
	</div>
</div>
