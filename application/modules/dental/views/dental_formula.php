<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title">UPDATE DENTAL</h2>
    </header>
    <div class="panel-body">
		<div class="row"  id="add_item">
			<div class="col-md-12" >
				<div class="col-md-3" >
					<div class="form-group">
			            <div class="col-lg-12">
							<h4 class="center-align">RIGHT TOOTH #<?php echo $teeth_id?></h4>
						</div>
					</div>
				</div>
				<input type="hidden" name="tooth_id" id="tooth_id" value="<?php echo $teeth_id?>">
				<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id?>">
				<input type="hidden" name="visit_id" id="visit_id" value="<?php echo $visit_id?>">

				<?php
				$cavity_status = 0;
				$query = $this->dental_model->get_dentine_item($patient_id,$teeth_id);
				if($query->num_rows() > 0)
				{
					foreach ($query->result() as $key => $value) {
						# code...
						$cavity_status = $value->cavity_status;
					}
				}
				$zero = '';
				$one = '';
				$two = '';
				$three = '';
				$four = '';
				$five = '';
				$six = '';
				$seven = '';

				if($cavity_status == 1)
				{
					$one = 'checked';
				}
				else if($cavity_status == 2)
				{
					$two = 'checked';
				}
				else if($cavity_status == 3)
				{
					$three = 'checked';
				}
				else if($cavity_status == 4)
				{
					$four = 'checked';
				}
				else if($cavity_status == 5)
				{
					$five = 'checked';
				}
				else if($cavity_status == 6)
				{
					$six = 'checked';
				}
				else if($cavity_status == 7)
				{
					$seven = 'checked';
				}

				else
				{
					$zero = 'checked';
				}

				?>
				<div class="col-md-7" >
					<div class="form-group">
			            <div class="col-lg-12">
			                <div class="radio">

			                	<label>
			                        <input id="optionsRadios2" type="radio" name="cavity_status" <?php echo $zero;?>  id="cavity_status" value="0"  >
			                        None
			                    </label>
			                    <label>
			                        <input id="optionsRadios2" type="radio" name="cavity_status" <?php echo $one;?> id="cavity_status" value="1" >
			                        Cavity
			                    </label>
			                    <label>
			                        <input id="optionsRadios2" type="radio" name="cavity_status" <?php echo $two;?> id="cavity_status" value="2">
			                        Bridge Pontic
			                    </label>
			                    <label>
			                        <input id="optionsRadios2" type="radio" name="cavity_status" <?php echo $three;?> id="cavity_status" value="3">
			                        Filling Present
			                    </label>
			                    <label>
			                        <input id="optionsRadios2" type="radio" name="cavity_status" <?php echo $four;?> id="cavity_status" value="4">
			                        Tooth to be
			                    </label>
			                    <label>
			                        <input id="optionsRadios2" type="radio" name="cavity_status" <?php echo $five;?> id="cavity_status" value="5">
			                        Tooth Missing
			                    </label>
			                    <label>
			                        <input id="optionsRadios2" type="radio" name="cavity_status" <?php echo $six;?> id="cavity_status" value="6" >
			                        Crown
			                    </label>
			                    <label>
			                        <input id="optionsRadios2" type="radio" name="cavity_status" <?php echo $seven;?> id="cavity_status" value="7">
			                        Root Present
			                    </label>
			                    
			                </div>
			            </div>
			            
					</div>
				</div>
				<div class="col-md-2" >
					<a class="btn btn-sm btn-success  " onclick="pass_tooth()"> Update Dental</a>					 	
				</div>
			</div>
		</div>
	</div>
</section>