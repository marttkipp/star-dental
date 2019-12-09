<?php
$dt = new DateTime("@$todays_date");  // convert UNIX timestamp to PHP DateTime
$todays_date =  $dt->format('Y-m-d');
// var_dump($todays_date); die();
?>
<!doctype>
<html>
<head>
    <title>jsPDF</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- IE Support -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- Bootstrap -->
	

	<style>
		@CHARSET "UTF-8";
		.page-break {
			page-break-after: always;
			page-break-inside: avoid;
			clear:both;
		}
		.page-break-before {
			page-break-before: always;
			page-break-inside: avoid;
			clear:both;
		}
		#color-red
		{
			color: red;
		}
	</style>
	<link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" />
	<link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css">	
	<link href='<?php echo base_url()."assets/fullcalendar/";?>fullcalendar.print.css'  media="print"/>
	<link href='<?php echo base_url()."assets/fullcalendar/";?>fullcalendar.min.css' rel='stylesheet' media="screen"/>

	<script src='<?php echo base_url()."assets/fullcalendar/";?>lib/moment.min.js'></script>
	<script src='<?php echo base_url()."assets/fullcalendar/";?>lib/jquery.min.js'></script>
	<style type="text/css">
	    .receipt_spacing{letter-spacing:0px; font-size: 12px;}
	    .center-align{margin:0 auto; text-align:center;}
	    
	    .receipt_bottom_border{border-bottom: #888888 medium solid;}
	    .row .col-md-12 table {
	        border:solid #000 !important;
	        border-width:2px 2px 2px 2px !important;
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
				     border-width:2px 2px 2px 2px !important;
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
		.fc-toolbar h2 {
			font-size: 15px !important;
			font-weight: bold !important;
		}

	</style>
	<script type="text/javascript">

	$(function() {
	   
		renderCalendar();
		// saveAspdf();
	});

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
			    businessHours: {
						        start: '06:00',
						        end: '18:45',
						        dow: [7]
						    },

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

	</script>

 </head>
<body>
	<button onclick="generate()">Generate PDF</button>
<div id="html-2-pdfwrapper" style='position: absolute; left: 20px; top: 50px; bottom: 0; overflow: auto; width: 600px'>

		<h1>Html2Pdf</h1>
		<p>
			This demo uses Html2Canvas.js to render HTML. <br />Instead of using an HTML canvas however, a canvas wrapper using jsPDF is substituted. The <em>context2d</em> provided by the wrapper calls native PDF rendering methods.
		</p>
		<p>A PDF of this page will be inserted into the right margin.</p>

		<h2>Colors</h2>
		<p>
			<span id="color-red" >red</span> <span style='color: rgb(0, 255, 0)'>rgb(0,255,0)</span> <span style='color: rgba(0, 0, 0, .5)'>rgba(0,0,0,.5)</span> <span style='color: #0000FF'>#0000FF</span> <span style='color: #0FF'>#0FF</span>
		</p>
		<input type="hidden" id="base_url" value="<?php echo site_url();?>">
		<input type="hidden" id="config_url" value="<?php echo site_url();?>">
		<div class="row">
			<div class="col-md-12 center-align receipt_bottom_border">
				
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
		        	<div  style="margin-top: 20px;padding-left: 20px;padding-right: 20px;" id="wrapper">
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

<script src='<?php echo base_url()."assets/"?>jspdf/jspdf.min.js'></script>
<script src='<?php echo base_url()."assets/fullcalendar/";?>fullcalendar.min.js'></script>
<script src='<?php echo base_url()."assets/fullcalendar/";?>scheduler.min.css'></script>
<script src='<?php echo base_url()."assets/fullcalendar/";?>moment.min.js'></script>
<script src='<?php echo base_url()."assets/fullcalendar/";?>scheduler.min.js'></script>

<script type="text/javascript">
function get_appointment_view(appointment_id)
{
	if(appointment_id == '1')
	{
		
		$('#old-patient-view').css('display', 'block');
		$('#new-patient-view').css('display', 'none');
		$('#patient_appointment').css('display', 'block');
		$('#event_appointment').css('display', 'none');
	}
	else
	{
		$('#old-patient-view').css('display', 'none');
		$('#new-patient-view').css('display', 'none');
		$('#patient_appointment').css('display', 'none');
		$('#event_appointment').css('display', 'block');
	}
}

function get_new_patient_view(appointment_id)
{
	// $('#old-patient-button').css('display', 'block');
	// $('#new-patient-button').css('display', 'none');
	$('#old-patient-view').css('display', 'none');
	$('#new-patient-view').css('display', 'block');
}
function get_old_patient_view(appointment_id)
{
	// $('#old-patient-button').css('display', 'none');
	// $('#new-patient-button').css('display', 'block');
	$('#old-patient-view').css('display', 'block');
	$('#new-patient-view').css('display', 'none');
}
function update_event_status(appointment_id,status)
{
	var config_url = $('#config_url').val();	

	 var url = config_url+"reception/update_appointment_details/"+appointment_id+"/"+status;
       $.ajax({
       type:'POST',
       url: url,
       data:{appointment_id: appointment_id,status: status},
       dataType: 'text',
       processData: false,
       contentType: false,
       success:function(data){
          var data = jQuery.parseJSON(data);
        
          if(data.message == "success")
			{
				// alert(data.message);
				$('#calendar-all').fullCalendar('destroy');
       			renderCalendar();								
				$('#calendarModal').modal('hide');
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


function delete_event_details(appointment_id,status)
{
	var config_url = $('#config_url').val();	
	var res = confirm('Are you sure you want to delete this schedule ?');

	if(res)
	{

		 var url = config_url+"reception/delete_event_details/"+appointment_id+"/"+status;
	       $.ajax({
	       type:'POST',
	       url: url,
	       data:{appointment_id: appointment_id,status: status},
	       dataType: 'text',
	       processData: false,
	       contentType: false,
	       success:function(data){
	          var data = jQuery.parseJSON(data);
	        
	          if(data.message == "success")
				{
					// alert(data.message);
					$('#calendar-all').fullCalendar('destroyEvents');
					var formDate = window.localStorage.getItem('date_set_old');
	    			var url = config_url+'reception/get_todays_appointments/'+formDate;
					$('#calendar-all').fullCalendar( 'refetchEvents',url );
					addButtons(formDate);
					addInfo(formDate);
					addBottom(formDate);											
					$('#calendarModal').modal('hide');
					$('#calendarModalNew').modal('hide');
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
	 else
	 {
	 	var formDate = window.localStorage.getItem('date_set_old');
		var url = config_url+'reception/get_todays_appointments/'+formDate;
		$('#calendar-all').fullCalendar( 'refetchEvents',url );
		addButtons(formDate);
		addInfo(formDate);
		addBottom(formDate);						
		$('#calendarModal').modal('hide');
	 }

}

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

function delete_note_details(calendar_note_id,status)
{
	var config_url = $('#config_url').val();	
	var res = confirm('Are you sure you want to delete this note ?');

	if(res)
	{

		 var url = config_url+"reception/delete_note_details/"+calendar_note_id+"/"+status;
	       $.ajax({
	       type:'POST',
	       url: url,
	       data:{calendar_note_id: calendar_note_id,status: status},
	       dataType: 'text',
	       processData: false,
	       contentType: false,
	       success:function(data){
	          var data = jQuery.parseJSON(data);
	        
	          if(data.message == "success")
				{
	    			var start_date = window.localStorage.getItem('date_set_old');

	    			var url = config_url+'reception/get_todays_appointments/'+start_date;
					$('#calendar-all').fullCalendar( 'refetchEvents',url );
					addButtons(start_date);
					addInfo(start_date);
					addBottom(start_date);	

					$('#calendarModal').modal('hide');
					$('#calendarModalNew').modal('hide');
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
	 else
	 {
	 	var start_date = window.localStorage.getItem('date_set_old');

		var url = config_url+'reception/get_todays_appointments/'+start_date;
		$('#calendar-all').fullCalendar( 'refetchEvents',url );
		addButtons(start_date);
		addInfo(start_date);
		addBottom(start_date);									
		$('#calendarModal').modal('hide');
	 }

}

</script>

<script>
var base64Img = null;
// imgToBase64('octocat.jpg', function(base64) {
//     base64Img = base64; 
// });

margins = {
  top: 70,
  bottom: 40,
  left: 30,
  width: 550
};

generate = function()
{
	var pdf = new jsPDF('p', 'pt', 'a4');
	pdf.setFontSize(18);
	pdf.fromHTML(document.getElementById('html-2-pdfwrapper'), 
		margins.left, // x coord
		margins.top,
		{
			// y coord
			width: margins.width// max width of content on PDF
		},function(dispose) {
			headerFooterFormatting(pdf, pdf.internal.getNumberOfPages());
		}, 
		margins);
		
	var iframe = document.createElement('iframe');
	iframe.setAttribute('style','position:absolute;right:0; top:0; bottom:0; height:100%; width:650px; padding:20px;');
	document.body.appendChild(iframe);
	
	iframe.src = pdf.output('datauristring');
};
function headerFooterFormatting(doc, totalPages)
{
    for(var i = totalPages; i >= 1; i--)
    {
        doc.setPage(i);                            
        //header
        header(doc);
        
        footer(doc, i, totalPages);
        doc.page++;
    }
};

function header(doc)
{
    doc.setFontSize(30);
    doc.setTextColor(40);
    doc.setFontStyle('normal');
    doc.addCSS('<?php echo base_url()."assets/fullcalendar/";?>fullcalendar.print.min.css');
	
    if (base64Img) {
       doc.addImage(base64Img, 'JPEG', margins.left, 10, 40,40);        
    }
	    
    doc.text("Report Header Template", margins.left + 50, 40 );
	doc.setLineCap(2);
	doc.line(3, 70, margins.width + 43,70); // horizontal line
};

// You could either use a function similar to this or pre convert an image with for example http://dopiaza.org/tools/datauri
// http://stackoverflow.com/questions/6150289/how-to-convert-image-into-base64-string-using-javascript
function imgToBase64(url, callback, imgVariable) {
 
    if (!window.FileReader) {
        callback(null);
        return;
    }
    var xhr = new XMLHttpRequest();
    xhr.responseType = 'blob';
    xhr.onload = function() {
        var reader = new FileReader();
        reader.onloadend = function() {
			imgVariable = reader.result.replace('text/xml', 'image/jpeg');
            callback(imgVariable);
        };
        reader.readAsDataURL(xhr.response);
    };
    xhr.open('GET', url);
    xhr.send();
};

function footer(doc, pageNumber, totalPages){

    var str = "Page " + pageNumber + " of " + totalPages
   
    doc.setFontSize(10);
    doc.text(str, margins.left, doc.internal.pageSize.height - 20);
    
};

 </script>
</body>
</html>
