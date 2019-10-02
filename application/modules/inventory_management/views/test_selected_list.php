<?php
$current_query = $this->inventory_management_model->get_test_costing($lab_test_id);
?>
<table class='table table-striped table-hover table-condensed'>
	<tr>
		<th>No.</th>
    	<th>Product Name</th>
    	<th>Unit Cost</th>
    	<th>Units</th>
    	<th>Total</th>
		<th>Added By</th>
		<th>Date Added</th>
		<th colspan="3">Actions</th>
	</tr>
	<tbody>
	<?php
		//get all administrators
		$personnel_query = $this->personnel_model->get_all_personnel();
		$total_cost = 0;
		if($current_query->num_rows() > 0)
		{
			$count = 0;
			foreach ($current_query->result() as $key) {
				$product_id = $key->product_id;
				$product_name = $key->product_name;
				$product_unitprice = $key->product_unitprice;
				$test_costing_id = $key->test_costing_id;
				$created_by = $key->created_by;
				$created = $key->created;
				$test_costing_units = $key->test_costing_units;
				$total_cost += ($product_unitprice * $test_costing_units);
				
				//creators & editors
				if($personnel_query->num_rows() > 0)
				{
					$personnel_result = $personnel_query->result();
					
					foreach($personnel_result as $adm)
					{
						$personnel_id2 = $adm->personnel_id;
						
						if($created_by == $personnel_id2)
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
				$count++;
				?>
				
					<tr>
			        	<td><?php echo $count;?></td>
						<td><?php echo $product_name;?></td>
						<td><?php echo number_format($product_unitprice, 2);?></td>
						<td><?php echo number_format($test_costing_units);?></td>
						<td><?php echo number_format(($product_unitprice * $test_costing_units), 2);?></td>
						<td><?php echo $created_by;?></td>
						<td><?php echo date('jS M Y H:i a',strtotime($created));?></td>
                        <td><input type="text" class="form-control" id="quantity<?php echo $test_costing_id;?>" size="2" value="<?php echo $test_costing_units;?>"></td>
						<td><a id="update_action_point_form" class='btn btn-warning btn-sm change_quantity' test_costing_id="<?php echo $test_costing_id;?>" lab_test_id="<?php echo $lab_test_id;?>" href="inventory/update-store-order" ><i class=" fa fa-edit"></i> Update quantity</a>
						<td><a class='btn btn-danger btn-sm' href="<?php echo site_url().'inventory_management/remove_costing/'.$test_costing_id.'/'.$lab_test_id;?>" onClick="return confirm('Are you sure you want to remove this charge')"><i class="fa fa-trash"></i> Remove charge</a></td>
					</tr>
				
				<?php
			}
		}
		?>
			<tr>
				<th colspan="2">Total</th>
				<td><?php echo number_format($total_cost, 2);?></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		
		<?php
	?>
		
	</tbody>
</table>