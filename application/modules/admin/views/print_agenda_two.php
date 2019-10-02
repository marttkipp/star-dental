<?php
$dt = new DateTime("@$todays_date");  // convert UNIX timestamp to PHP DateTime
$todays_date =  $dt->format('Y-m-d');
// var_dump($todays_date); die();
?>

<!DOCTYPE html>
<html>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- IE Support -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- Bootstrap -->
<link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css">	

<!-- <script src="<?php echo base_url()."assets/themes/bluish/"?>js/jquery.js"></script>  -->
<!-- jQuery -->

<!-- full calendae -->
 
<link href='<?php echo base_url()."assets/fullcalendar/";?>fullcalendar.min.css' rel='stylesheet'/>
<link href='<?php echo base_url()."assets/fullcalendar/";?>fullcalendar.print.css'  media='print' />
<script src='<?php echo base_url()."assets/fullcalendar/";?>lib/moment.min.js'></script>
<script src='<?php echo base_url()."assets/fullcalendar/";?>lib/jquery.min.js'></script>

<script src="<?php echo base_url()."assets/themes/bluish/"?>js/jquery.js"></script>
<!-- <script src="jquery/html2canvas.js"></script> -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>

<script type="text/javascript" src="<?php echo base_url()."assets/"?>html2canvas/dist/html2canvas.js"></script>
<script type="text/javascript">

$(function() {
   
	renderCalendar();
	// saveAspdf();
});

</script>
<style>
#previewBody{
background-color: #fff;
/*color: #fff;*/
/*padding: 5px 0px 27px 30px;*/
width: 55%;
margin-bottom: 15px;
}

</style>
<script>
function renderCalendar()
{
 	var config_url = $('#config_url').val();	
 	var m = $.fullCalendar.moment();
 	var formDate = $.fullCalendar.formatDate(m, 'YYYY-MM-DD');
 	window.localStorage.setItem('date_set',formDate);
 	var start_date = window.localStorage.getItem('date_set');


 	// alert(start_date);
	$('#calendar-all').fullCalendar({
			
		    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
		    defaultView: 'agendaDay',
		    timezone : 'local',
	        contentHeight: 'auto',
	        theme: false,
	        // businessHours: true,
	        slotLabelFormat: [
							  'H:mm', // top level of text
							],
			timeFormat: 'H:mm',
		    resources: [
		      { id: 'a', title: 'SURGERY 1'},
		      { id: 'b', title: 'SURGERY 2' },
		      { id: 'c', title: 'SURGERY 3' },
		      { id: 'd', title: 'SURGERY 4' },
		      { id: 'e', title: 'SURGERY 5' },
		      { id: 'f', title: 'THE DAYS EVENT' }
		    ],
		    groupByResource: true,
		    showAsSeparateResource: false,
		    editable: false,
		    allDaySlot:false,
		    minTime: "06:00:00",
		    maxTime: "18:45:00",

			events: function(start, end, timezone, callback) {

								window.localStorage.setItem('date_set_old',end.unix());

						        $.ajax({
						          url: config_url+'reception/get_todays_appointments',
						          // type:'POST',
						          dataType: 'json',
						          data: {
						            start: start.unix(),
						            end: end.unix(),
						          },
						          success: function(doc) {
						            var events = [];
						            doc.forEach(function(eventObject) {
						                events.push({
										    id: eventObject.id,
						                    title: eventObject.title,
						                    start: eventObject.start,
						                    end: eventObject.end,
						                    description: eventObject.description,
						                    resourceId: eventObject.resourceId,
						                    backgroundColor: eventObject.backgroundColor,
						                    borderColor: eventObject.borderColor
						                });
						            });
						            $('#calendar-all').fullCalendar('destroyEvents');
						            callback(events);
						        }
						    });
   			},		    
   			eventRender: function(event, element) { 
		            element.find('.fc-title').append("<br/>" + event.description); 
		    },
		   

		  });
	$('.fc-head').after($('.head-info'));
	$('.head-info').after($('.top-items'));
	$('.fc-body').after($('.bottom-items'));
	
	var start_date = window.localStorage.getItem('date_set_old');
	addButtons(start_date);
	addInfo(start_date);
	addBottom(start_date);
	// saveAspdf();
	
 }

function saveAspdf() {   

   //  html2canvas($("#previewBody"), {
   //      onrendered: function(canvas) {         
   //          var imgData = canvas.toDataURL(
   //              'image/png');    
                         
   //          var pdf = new jsPDF('p','pt','a4');
   //          var options = {
		 //         pagesplit: true
		 //    };

			// pdf.addHTML($("#previewBody"), options, function()
			// {
			//     pdf.save("test.pdf");
			// });
   //          // doc.addImage(imgData, 'png', 10, 10);
   //          // doc.save('sample-file.pdf');
   //      }
   //  });


    html2canvas(document.querySelector('#previewBody'), 
								{scale: 4}
						 ).then(canvas => {
			
			let pdf = new jsPDF('p', 'mm', 'a4');
			var options = {
		         pagesplit: true
		    };
		   

			pdf.addHTML($("#previewBody"), options, function()
			{
			    pdf.save("<?php echo $todays_date?>_schedule.pdf");
			});
			// pdf.addImage(canvas.toDataURL('image/png'), 'PNG', 0, 0, 211, 298);
			// pdf.save(filename);
});
}

function printHtmldiv()
{
    var pdf = new jsPDF('p', 'pt', 'a4')

    // source can be HTML-formatted string, or a reference
    // to an actual DOM element from which the text will be scraped.
    , source = $('#previewBody')[0]

    // we support special element handlers. Register them with jQuery-style
    // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
    // There is no support for any other type of selectors
    // (class, of compound) at this time.
    , specialElementHandlers = {
         // element with id of "bypass" - jQuery style selector
        '#bypassme': function(element, renderer)
        {
            // true = "handled elsewhere, bypass text extraction"
            return true
        }
    }

    margins = {
        top: 80,
        bottom: 60,
        left: 40,
        width: 522
    };
    // all coords and widths are in jsPDF instance's declared units
    // 'inches' in this case
    pdf.fromHTML
    (
        source // HTML string or DOM elem ref.
      , margins.left // x coord
      , margins.top // y coord
      , {'width': margins.width // max width of content on PDF
         , 'elementHandlers': specialElementHandlers
        }
      , function (dispose) 
        {
           // dispose: object with X, Y of the last line add to the PDF
           // this allow the insertion of new lines after html
           pdf.save('Mypdf.pdf');
        }
      , margins
    )
}
</script>
<style type="text/css">
    .receipt_spacing{letter-spacing:0px; font-size: 12px;}
    .center-align{margin:0 auto; text-align:center;}
    
    .receipt_bottom_border{border-bottom: #888888 medium solid;}
    .row .col-md-12 table {
        border:solid #000 !important;
        border-width:1px 0 0 1px !important;
        font-size:10px;
    }
    .row .col-md-12 th, .row .col-md-12 td {
        border:solid #000 !important;
        border-width:0 1px 1px 0 !important;
    }
    
    .row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 20px;}
    .title-img{float:left; padding-left:30px;}
    img.logo{max-height:70px; margin:0 auto;}
    /*.table {margin-bottom: 0;}*/
    .fc .fc-widget-header
	{
		font-size: 1.3rem !important;
		font-weight: 500 !important;
		padding: 0px 0 !important;
	}
	.fc-time-area .fc-event-container {
	  padding-bottom: 0 !important;
	}
	#datepicker {
    display: inline-block;
  }
  .fc-toolbar.fc-header-toolbar
  {
  	margin-bottom: 0.1em !important;
  }
  .ui-widget-content span [text='all-day']
  {
  	display: none !important;
  }
  .table
  {
  	margin-bottom: 0px !important;
  }
  .bg-info
  {
  	margin-bottom:5px !important;
  }
  table #bottom-table th
  {
  	color: black !important;
  }
  .borderless td, .borderless th .borderless tr {
    border: none;
    color: black !important;
	}
	.borderless td, .borderless th
	{
		line-height: 1 !important;
		padding: auto !important;
	}
	
	table.borderless td
	{
		width: 35% !important;
	}
	
	.head-info
	{
		text-align: center !important;
	}
	.bold
	{
	    font-weight: bold !important;
	}

	.fc table th
	{
		color: #000 !important;
	}

	table {
			    border:solid #000 !important;
			    border-width:1px 0 0 1px !important;
			}
			th, td {
			    border:solid #000 !important;
			    border-width:0 1px 1px 0 !important;
			}
	.fc-bgevent
	{
		border: 1px solid #000 !important;
	}
	.fc-business-container
	{
		border: 1px solid #000 !important;
	}


</style>
</head>
 <body class="receipt_spacing" onLoad="">
	<input type="hidden" id="base_url" value="<?php echo site_url();?>">
	<input type="hidden" id="config_url" value="<?php echo site_url();?>">
	<button id="downloadItem" href="#" onclick="saveAspdf()">Download</button>
	<div id="previewBody">
		<div class="col-md-12 center-align receipt_bottom_border">
			<div class="row">
	        	<div class="col-xs-12">
	            	<img src="<?php echo base_url().'assets/logo/'.$contacts['logo'];?>" alt="<?php echo $contacts['company_name'];?>" class="img-responsive logo"/>
	            </div>
	        </div>
	        <br>
	    	<div class="row">
	        	<div class="col-md-12 center-align receipt_bottom_border">
	            	  <strong>
	                    <?php echo $contacts['company_name'];?><br/>
	                    P.O. Box <?php echo $contacts['address'];?> <?php echo $contacts['post_code'];?>, <?php echo $contacts['city'];?><br/>
	                    E-Mail:<?php echo $contacts['email'];?>.<br> Tel : <?php echo $contacts['phone'];?><br/>
	                </strong>
	            </div>
	        </div>
	    </div>

	    <div class="row receipt_bottom_border" >
	    	<div class="col-md-12">
	        	<div  style="padding-top: 20px;padding-left: 20px;padding-right: 20px;" id="wrapper">
					<div id="calendar-all">
						<div class="fc-datePickerButton-button"></div>
						<div class="top-items" id="top-items"></div>
						<div class="head-info" id="head-info"></div>
						
					</div>		
					<div class="bottom-items" id="bottom-items"></div>
					<div class="bottom-head"></div>
					
				</div>
	        </div>
	    	<div class="col-md-12 center-align">
	        	<?php echo 'Prepared By:	 '.date('jS M Y H:i:s',strtotime(date('Y-m-d H:i:s')));?>
	        </div>
	    </div>
		
	</div>
	
	
</div>

<script src='<?php echo base_url()."assets/fullcalendar/";?>fullcalendar.min.js'></script>
<script src='<?php echo base_url()."assets/fullcalendar/";?>scheduler.min.css'></script>
<script src='<?php echo base_url()."assets/fullcalendar/";?>moment.min.js'></script>
<script src='<?php echo base_url()."assets/fullcalendar/";?>scheduler.min.js'></script>

<script type="text/javascript">


function addButtons(start_date) {

var m = $.fullCalendar.moment();

	var config_url = $('#config_url').val();	

	 var url = config_url+"reception/get_featured_notes/"+start_date+"/0";
	 // alert(url);
	   $.ajax({
	   type:'POST',
	   url: url,
	   data:{appointment_id: 1},
	   dataType: 'text',
	   processData: false,
	   contentType: false,
	   success:function(data){
	      var data = jQuery.parseJSON(data);
	    
	      if(data.message == "success")
			{

				$('.head-info').html(data.content);
			}
			else
			{
				alert('Please ensure you have added included all the items');
			}
	   
	   },
	   error: function(xhr, status, error) {
	   alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	   
	   }
	   });
	
	
}


function addInfo(start_date) {
	// create buttons
	
 	var formDate = window.localStorage.getItem('date_set_old');

 	// alert(end_date);

	var config_url = $('#config_url').val();	

	 var url = config_url+"reception/get_todays_top_notes/"+formDate+"/0";
	  // alert(url);
	   $.ajax({
	   type:'POST',
	   url: url,
	   data:{appointment_id: 1},
	   dataType: 'text',
	   processData: false,
	   contentType: false,
	   success:function(data){
	      var data = jQuery.parseJSON(data);
	    
	      if(data.message == "success")
			{
				// alert(data.message);
				 // document.getElementByClass('').innerHTML = "";
				var toolbar = $("<div style='margin-left: 35px;'><table class='table table-bordered table-condensed' ><tbody>"+data.content+"</tbody></table></div>")
				// toolbar.append($("<div/>", { "class": "fc-clear"}));
				// insert row before title.

				$('.top-items').html(toolbar);
				// $(".head-info").after(toolbar);
			}
			else
			{
				alert('Please ensure you have added included all the items');
			}
	   
	   },
	   error: function(xhr, status, error) {
	   alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	   
	   }
	   });
	

	
}

function addBottom(start_date) {
	// create buttons
	var m = $.fullCalendar.moment();
 	var formDate = start_date;
 	var formDate = window.localStorage.getItem('date_set_old');

	var config_url = $('#config_url').val();	

	 var url = config_url+"reception/get_todays_bottom_notes/"+formDate+"/0";

	 // alert(url);
	   $.ajax({
	   type:'POST',
	   url: url,
	   data:{appointment_id: 1},
	   dataType: 'text',
	   processData: false,
	   contentType: false,
	   success:function(data){
	      var data = jQuery.parseJSON(data);
	    
	      if(data.message == "success")
			{
				// alert(data.message);
				var toolbar = $("<div style='margin-left: 35px;'><table class='table table-bordered table-condensed' id='bottom-table'><thead><th style='width: 11%;'>NOTE</th><th style='width: 11%;'>NOTE</th><th style='width: 11%;'>NOTE</th><th style='width: 11%;'>NOTE</th><th style='width: 11%;'>NOTE</th><th style='width: 11%;'>NOTE</th></thead><tbody>"+data.content+"</tbody></table></div>")
				// toolbar.append($("<div/>", { "class": "fc-clear"}));
				// insert row before title.
				// document.getElementById("MyDiv").innerHTML = "";
				$(".bottom-items").html(toolbar);
			}
			else
			{
				alert('Please ensure you have added included all the items');
			}
	   
	   },
	   error: function(xhr, status, error) {
	   alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	   
	   }
	   });
	

	
}


</script>
</body>
</html>