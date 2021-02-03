 <section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title pull-right"></h2>

        <h2 class="panel-title">Search Account Name</h2>

    </header>
    
    <!-- Widget content -->
    <div class="panel-body">
        <div class="padd">
            <?php

            echo form_open("financials/company_financial/search_accounts", array("class" => "form-horizontal"));
            ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" >
                        <label class="col-lg-4 control-label">Account name: </label>
                        
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="account_name" placeholder="Account Name">
                        </div>
                    </div>
                </div>               
                <div class="col-md-6">
                    <div class="form-group" >
                        <div class="center-align">
                            <button type="submit" class="btn btn-info btn-sm">Search</button>
                        </div>
                    </div>
                </div>
            </div>
            
           
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</section>