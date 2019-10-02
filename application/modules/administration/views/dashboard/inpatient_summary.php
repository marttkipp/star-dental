<?php 
	//$daily_balance = number_format($this->reports_model->get_daily_balance(), 0, '.', ',');
	$main_queue_total = number_format($this->reports_model->get_totals_inpatient_items('AND visit.inpatient = 1'), 0, '.', ',');
	$main_patients_today = number_format($this->reports_model->get_totals_inpatient_items('AND visit.inpatient = 1 AND visit.close_card = 1'), 0, '.', ',');
	$main2_queue_total = number_format($this->reports_model->get_totals_inpatient_items('AND visit.inpatient = 1 AND visit.close_card = 2'), 0, '.', ',');
	$main2_patients_today = number_format($this->reports_model->get_totals_inpatient_items('AND patients.rip_status = 1 '), 0, '.', ',');
    $response2 = $this->reports_model->calculate_distict_inpatient(1);
?>
<section class="panel panel-featured panel-featured-info">
    <header class="panel-heading">
        <h2 class="panel-title">Patients Today</h2>
    </header>   

    <!-- Widget content -->
    <div class="panel-body">
        <div class="row">
            <div class="col-md-3 col-lg-3 col-xl-3">
                <section class="panel panel-featured-left panel-featured-tertiary">
                    <div class="panel-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-tertiary">
                                    <i class="fa fa-users"></i>
                                </div>
                            </div>
                            <div class="widget-summary-col">
                                <div class="summary">
                                    <h4 class="title">Total Inpatients</h4>
                                    <div class="info">
                                        <strong class="amount"><?php echo $main_queue_total-$response2['rip_number']- $main_patients_today-$main2_queue_total;?></strong>
                                    </div>
                                </div>
                                <div class="summary-footer">
                                    <!--<a class="text-muted text-uppercase">(statement)</a>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-md-3 col-lg-3 col-xl-3">
                <section class="panel panel-featured-left panel-featured-quartenary">
                    <div class="panel-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-quartenary">
                                    <i class="fa fa-users"></i>
                                </div>
                            </div>
                            <div class="widget-summary-col">
                                <div class="summary">
                                    <h4 class="title">Discharged Out</h4>
                                    <div class="info">
                                        <strong class="amount"><?php echo $main_patients_today;?></strong>
                                    </div>
                                </div>
                                <div class="summary-footer">
                                    <!--<a class="text-muted text-uppercase" href="<?php echo base_url()."data/reports/patients.php";?>">(report)</a>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-md-3 col-lg-3 col-xl-3">
                <section class="panel panel-featured-left panel-featured-secondary">
                    <div class="panel-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-secondary">
                                    <i class="fa fa-users"></i>
                                </div>
                            </div>
                            <div class="widget-summary-col">
                                <div class="summary">
                                    <h4 class="title">Discharged In</h4>
                                    <div class="info">
                                        <strong class="amount"><?php echo $main2_queue_total;?></strong>
                                    </div>
                                </div>
                                <div class="summary-footer">
                                    <!--<a class="text-muted text-uppercase">(withdraw)</a>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-md-3 col-lg-3 col-xl-3">
                <section class="panel panel-featured-left panel-featured-primary">
                    <div class="panel-body">
                        <div class="widget-summary">
                            <div class="widget-summary-col widget-summary-col-icon">
                                <div class="summary-icon bg-primary">
                                    <i class="fa fa-users"></i>
                                </div>
                            </div>
                            <div class="widget-summary-col">
                                <div class="summary">
                                    <h4 class="title">RIP's</h4>
                                    <div class="info">
                                        <strong class="amount"><?php echo $response2['rip_number'];?></strong>
                                    </div>
                                </div>
                                <div class="summary-footer">
                                    <!--<a class="text-muted text-uppercase">(withdraw)</a>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div class="row">
            <div class="center-align">
                <?php
                    $search_title = $this->session->userdata('visit_title_search');
                    if(!empty($search_title))
                    {
                        $title_ext = $search_title;
                    }
                    else
                    {
                        $title_ext = 'Visit Report for '.date('Y-m-d');
                    }
                    echo $title_ext;
                ?>
            </div>
        </div>
    </div>

</section>
          
        <script type="text/javascript">
			var config_url = $('#config_url').val();

//Get patients per day for the last 7 days
$.ajax({
	type:'POST',
	url: config_url+"administration/charts/latest_patient_totals",
	cache:false,
	contentType: false,
	processData: false,
	dataType: "json",
	success:function(data){
		
		var bars = data.bars;
		var days_total = bars.split(',').map(function(item) {
			return parseInt(item, 10);
		});
		
		$("#patients_per_day").sparkline(days_total, {
			type: 'bar',
			height: data.highest_bar,
			barWidth: 4,
			barColor: '#fff'});
	},
	error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	}
});

//Get Revenue for the individual revenue types
$.ajax({
	type:'POST',
	url: config_url+"administration/charts/queue_total",
	cache:false,
	contentType: false,
	processData: false,
	dataType: "json",
	success:function(data){
		
		var bars = data.bars;
		var queue_total = bars.split(',').map(function(item) {
			return parseInt(item, 10);
		});
		
		$("#queue_total").sparkline(queue_total, {
			type: 'bar',
			height: data.highest_bar,
			barWidth: 4,
    		barColor: '#E25856'});
	},
	error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	}
});

//Get payment methods
$.ajax({
	type:'POST',
	url: config_url+"administration/charts/payment_methods",
	cache:false,
	contentType: false,
	processData: false,
	dataType: "json",
	success:function(data){
		
		var bars = data.bars;
		var queue_total = bars.split(',').map(function(item) {
			return parseInt(item, 10);
		});
		
		$("#payment_methods").sparkline(queue_total, {
			type: 'bar',
			height: data.highest_bar,
			barWidth: 4,
    		barColor: '#94B86E'});
	},
	error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	}
});
		</script>