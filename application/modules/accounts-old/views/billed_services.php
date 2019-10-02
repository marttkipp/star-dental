	<?php

   	$item_invoiced_rs = $this->accounts_model->get_patient_visit_charge_items(7);
   	?>
   	<div id="service_div2" class="form-group">
		<label class="col-lg-4 control-label">Service: </label>
	  
		<div class="col-lg-8">
        	<select name="service_id" class="form-control">
            	<option value="">All services</option>
        	<?php
			if(count($item_invoiced_rs) > 0)
			{
				$s=0;
				foreach ($item_invoiced_rs as $key_items):
					$s++;
					$service_id = $key_items->service_id;
					$service_name = $key_items->service_name;
					?>
                    <option value="<?php echo $service_id;?>"><?php echo $service_name;?></option>
					<?php
				endforeach;
			}
				
			?>
            </select>
		</div>
	</div>