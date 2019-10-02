 <section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title">Search</h2>
    </header>
    
    <!-- Widget content -->
    <div class="panel-body">
        <div class="padd">
            <?php
            echo form_open("finance/creditors/search_creditors", array("class" => "form-horizontal"));
            ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Creditor: </label>
                        
                        <div class="col-md-8">
                            <input  type="text"  class="form-control" name="creditor_name" placeholder="Creditor" autocomplete="off">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="redirect_url" value="<?php echo $this->uri->uri_string()?>">
                

                <div class="col-md-4">
                	<div class="center-align">
		                <button type="submit" class="btn btn-info btn-sm">Search</button>
		            </div>
                </div>
            </div>
            
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</section>