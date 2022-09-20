 <section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title pull-right"></h2>

        <h2 class="panel-title">Search Debtor</h2>

    </header>
    
    <!-- Widget content -->
    <div class="panel-body">
        <div class="padd">
            <?php

            echo form_open("accounting/debtors/search_hospital_creditors", array("class" => "form-horizontal"));
            ?>
            <div class="row">
                  <div class="form-group" style="width:800px; margin:0 auto;">
                        <label class="col-lg-4 control-label">Debtor name: </label>
                        
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="visit_type_name" placeholder="Debtor Name">
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