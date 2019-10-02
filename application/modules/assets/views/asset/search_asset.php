 <section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title pull-right"></h2>

        <h2 class="panel-title">Search Asset</h2>

    </header>
    
    <!-- Widget content -->
    <div class="panel-body">
        <div class="padd">
            <?php

            echo form_open("assets/assets/search_asset", array("class" => "form-horizontal"));
            ?>
            <div class="row">
            	<div class="col-md-12">
            		<div class="col-md-6">
	                  	<div class="form-group" style="margin:0 auto;">
	                        <label class="col-lg-4 control-label">Asset name: </label>
	                        
	                        <div class="col-lg-8">
	                            <input type="text" class="form-control" name="asset_name" placeholder="Asset Name">
	                        </div>
	                    </div>
	                </div>
	                <div class="col-md-6">
	                	<div class="form-group" style="margin:0 auto;">
	                        <label class="col-lg-4 control-label">Asset Category </label>
                            <div class="col-lg-8">
                                <select id="asset_category_id" name="asset_category_id" class="form-control">
                                    <option value="">--- None ---</option>
                                    <?php
                                    if($all_categories->num_rows() > 0)
                                    {	
                                        foreach($all_categories->result() as $row):
											// $company_name = $row->company_name;
											$asset_category_name = $row->asset_category_name;
											$asset_category_id = $row->asset_category_id;
											
											if($asset_category_id == set_value('asset_category_id'))
											{
                                        		echo "<option value=".$asset_category_id." selected='selected'> ".$asset_category_name."</option>";
											}
											
											else
											{
                                        		echo "<option value=".$asset_category_id."> ".$asset_category_name."</option>";
											}
                                        endforeach;	
                                    } 
                                    ?>
                                </select>
                            </div>
	                    </div>
	                </div>
                </div>
            </div>

            <br/>
            <div class="center-align">
                <button type="submit" class="btn btn-info btn-sm">Search</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</section>