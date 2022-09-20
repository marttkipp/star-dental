
<div class="col-md-12">
	 <section class="panel">
	    <header class="panel-heading">
	            <h5 class="pull-left"><i class="icon-reorder"></i>Add Place</h5>
	          <div class="widget-icons pull-right">
	               <!-- <a href="<?php echo site_url();?>patients" class="btn btn-success btn-sm pull-right">  Patients List</a> -->

	          </div>
	          <div class="clearfix"></div>
	    </header>
	      <div class="panel-body">
	        <div class="padd" style="height:80vh;">
	        	
				<div class="col-md-6">
					<form id="add-place" method="post">
						<div class="col-md-12">		

							<div class="form-group">
								<label class="col-lg-4 control-label">Place Name: </label>
								<div class="col-lg-8">
									<input type="text" name="place_name" class="form-control">
								</div>
							</div>

								
						</div>
						<br/>
						<br/>
						<div class="row" >
					        <div class="col-md-12">
					        	<div class=" center-align" style="margin-top: 10px;">
					        		<button type="submit" class="btn btn-sm btn-success ">ADD PLACE</button>
					        		
					        	</div>
					               
					        </div>
					    </div>
					</form>
				</div>
				<div class="col-md-6">
	        		<div id="places-list" style="overflow-y: scroll;"></div>
	        	</div>
			</div>
		</div>
	</section>
</div>
<div class="row" style="margin-top: 5px;">
		<ul>
			<li style="margin-bottom: 5px;">
				<div class="row">
			        <div class="col-md-12 center-align">
				        	<!-- <div id="old-patient-button" style="display:none">
				        				        		
				        		
				        	</div> -->
				        	<!-- <div> -->
				        		<a  class="btn btn-sm btn-info" onclick="close_side_bar()"><i class="fa fa-folder-closed"></i> CLOSE SIDEBAR</a>
				        	<!-- </div> -->
				        		
			               
			        </div>
			    </div>
				
			</li>
		</ul>
	</div>
