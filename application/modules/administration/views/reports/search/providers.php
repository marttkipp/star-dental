
    <!-- Widget -->
<section class="panel">


    <!-- Widget head -->
    <header class="panel-heading">
		<h2 class="panel-title"><?php echo $title;?></h2>
    </header>             

    <!-- Widget content -->
    <div class="panel-body">
        <?php
        echo form_open("administration/reports/search_provider", array("class" => "form-horizontal"));
        ?>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group " >
				  <label class="col-md-2 control-label">Provider: </label>
				  	<div class="col-md-10">
				        <select id='provider_id_item' name='personnel_id' class='form-control custom-select ' >
				          <option value=''>None - Please Select a provider</option>
				          <?php
						
								if(count($doctor) > 0){
									foreach($doctor as $row):
										$fname = $row->personnel_fname;
										$onames = $row->personnel_onames;
										$personnel_id = $row->personnel_id;
										
										if($personnel_id == set_value('personnel_id'))
										{
											echo "<option value='".$personnel_id."' selected='selected'>".$onames." ".$fname."</option>";
										}
										
										else
										{
											echo "<option value='".$personnel_id."'>".$onames." ".$fname."</option>";
										}
									endforeach;
								}
							?>
				        </select>
				    </div>
				</div>
                
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <label class="col-lg-3 control-label">Report From: </label>
                    
                    <div class="col-lg-9">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_from" placeholder="Report Date From">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="col-lg-3 control-label">Report To: </label>
                    
                    <div class="col-lg-9">
                       <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_to" placeholder="Report Date To">
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <br>
        <div class="center-align">
        	<button type="submit" class="btn btn-info btn-sm">Search</button>
        </div>
        <?php
        echo form_close();
        ?>
	 </div>
</section>

<script type="text/javascript">
	 $(function() {
       $("#provider_id_item").customselect();

   });
</script>