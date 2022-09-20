<?php
$date_today = $todays_date;
$dt = new DateTime("@$todays_date");  // convert UNIX timestamp to PHP DateTime
$todays_date =  $dt->format('Y-m-d');
// var_dump($todays_date); die();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Schedule || <?php echo $todays_date;?></title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css">	

		<!-- <script src="<?php echo base_url()."assets/themes/bluish/"?>js/jquery.js"></script>  -->
		<!-- jQuery -->

		<!-- full calendae -->
		<!-- <link href='<?php echo base_url()."assets/fullcalendar/";?>fullcalendar.print.min.css' rel='stylesheet' media='print' />  -->
		<link href='<?php echo base_url()."assets/fullcalendar/";?>fullcalendar.min.css' rel='stylesheet'/>
		
		<script src='<?php echo base_url()."assets/fullcalendar/";?>lib/moment.min.js'></script>
		<script src='<?php echo base_url()."assets/fullcalendar/";?>lib/jquery.min.js'></script>
		<script>

		$(function() {
		   
			renderCalendar();
			var days_view = $('#days_view').val();
			var todays_date = $('#todays_date').val();	
			window.localStorage.setItem('date_set_old',days_view);
 			// alert(days_view);
			$('#calendar-all').fullCalendar('gotoDate', todays_date);
			addButtons(days_view);
			addInfo(days_view);
			addBottom(days_view);
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
	        businessHours: true,
	        slotLabelFormat: [
							  'H:mm', // top level of text
							],
			timeFormat: 'H:mm',
		    resources: [
		      { id: 'd', title: 'SURGERY 4' },
		      { id: 'e', title: 'SURGERY 5' },
		      { id: 'f', title: 'THE DAYS EVENT' }
		    ],
		    groupByResource: true,
		    showAsSeparateResource: false,
		    editable: false,
		    allDaySlot:false,
		    minTime: "06:30:00",
		    maxTime: "18:30:00",
		    slotLabelInterval : '00:15:00',
		    viewSubSlotLabel : true,
		    slotLabelFormat:"HH:mm",
		    businessHours: {
						        start: '06:30',
						        end: '18:30',
						        dow: [7]
						    },  
		    titleFormat: 'dddd D MMMM YYYY',
		    timeFormat: "HH:mm",


			events: function(start, end, timezone, callback) {
								var days_view = $('#date_today').val();	
								window.localStorage.setItem('date_set_old',days_view);

						        $.ajax({
						          url: config_url+'reception/get_todays_appointments',
						          // type:'POST',
						          dataType: 'json',
						          data: {
						            start: start.unix(),
						            end: days_view,
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
		    eventClick:  function(event, jsEvent, view) {
		    		 $.ajax({
							type:'POST',
							url: config_url+"reception/get_event_details/"+event.id,
							cache:false,
							contentType: false,
							processData: false,
							dataType: "json",
							success:function(data){
								// alert();
								var status_event = data.status;
								// alert(status_event);
								if(status_event == 0)
								{
									
						            $('#new-appointment').html(data.results);
						   //          $('#eventUrl').attr('href',event.url);
						    		
						            $('#calendarModalNew').modal();
						            $("#patient_id"+event.id).customselect();

								}
								else
								{
									// $('#modalTitle').html(event.title);
						            $('#body-items').html(data.results);
						            $('#buttons-div').html(data.buttons);
						            // $('#eventUrl').attr('href',event.url);
						            $('#calendarModal').modal();
								}
							}
						});
		           
		        },
		    eventRender: function(event, element) { 
		            element.find('.fc-title').append("<br/>" + event.description); 
		    },
		    dayClick: function(date, jsEvent, view, resource,event) {

		    		var config_url = $('#config_url').val();
		    		var start =  date.format();
		    		var end =  date.format();

		    		// alert(date);
		    		
		    	  	jQuery.post(
			            config_url+"reception/create_appointment", 
			            { // re-use event's data
			                // title: title,
			                start: start,
			                end: end,
			                resource: resource.id,

			            }

			        );

			       
			        $('#calendar-all').fullCalendar('unselect');
			       	var array = start.split("T");			       
			       	var start_date = array[0];			        
			       	refetch_events(start_date);	
			       	var url = config_url+'reception/get_todays_appointments/'+start_date;

				    $("#calendar-all").fullCalendar('addEventSource', url);
					$('#calendar-all').fullCalendar('rerenderEvents');
					$('#calendar-all').fullCalendar( 'refetchEvents',url );

					 $('#calendar-all').fullCalendar('destroyEvents');
					callback(events);

			  }

		  });
	$('.fc-head').after($('.head-info'));
	$('.head-info').after($('.top-items'));
	$('.fc-body').after($('.bottom-items'));
	
	var start_date = window.localStorage.getItem('date_set_old');
	addButtons(start_date);
	addInfo(start_date);
	addBottom(start_date);
	
 }

 function refetch_events(start_date)
 {
 	var url = config_url+'reception/get_todays_appointments/'+start_date;

    $('#calendar-all').fullCalendar('removeEvents');

	$('#calendar-all').fullCalendar( 'refetchEvents',url );
	//getting latest Resources
	$('#calendar-all').fullCalendar( 'refetchResources' );
 }

$(document).on("submit","form#add_appointment",function(e)
{
	e.preventDefault();
	
	var form_data = new FormData(this);

	// alert(form_data);
	
	var config_url = $('#config_url').val();	

	 var url = config_url+"reception/add_appointment/0";
       $.ajax({
       type:'POST',
       url: url,
       data:form_data,
       dataType: 'text',
       processData: false,
       contentType: false,
       success:function(data){
          var data = jQuery.parseJSON(data);
        
          if(data.message == "success")
			{
    			
				$('#calendar-all').fullCalendar('destroyEvents');
				var formDate = window.localStorage.getItem('date_set_old');
    			var url = config_url+'reception/get_todays_appointments/'+formDate;

				$('#calendar-all').fullCalendar( 'refetchEvents',url );

				addButtons(formDate);
				addInfo(formDate);
				addBottom(formDate);				
				
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
	 
	
   
	
});
$(document).on("submit","form#add_appointment2",function(e)
{
	e.preventDefault();
	
	var form_data = new FormData(this);

	// alert(form_data);
	
	var config_url = $('#config_url').val();	

	 var url = config_url+"reception/add_appointment/1";
       $.ajax({
       type:'POST',
       url: url,
       data:form_data,
       dataType: 'text',
       processData: false,
       contentType: false,
       success:function(data){
          var data = jQuery.parseJSON(data);
        
          if(data.message == "success")
			{
    			
				$('#calendar-all').fullCalendar('destroyEvents');
				var formDate = window.localStorage.getItem('date_set_old');
    			var url = config_url+'reception/get_todays_appointments/'+formDate;

				$('#calendar-all').fullCalendar( 'refetchEvents',url );

				addButtons(formDate);
				addInfo(formDate);
				addBottom(formDate);				
				
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
	 
	
   
	
});


$(document).on("submit","form#add_event",function(e)
{
	e.preventDefault();
	// myApp.showIndicator();
	
	var form_data = new FormData(this);

	// alert(form_data);

	var config_url = $('#config_url').val();	

	 var url = config_url+"reception/add_appointment";
       $.ajax({
       type:'POST',
       url: url,
       data:form_data,
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
	 
	
   
	
});


$(document).on("submit","form#add_note",function(e)
{
	e.preventDefault();
	// myApp.showIndicator();
	
	var form_data = new FormData(this);

	// alert(form_data);

	var config_url = $('#config_url').val();
	// var m = $.fullCalendar.moment();
 // 	var formDate = $.fullCalendar.formatDate(m, 'YYYY-MM-DD');	
	var formDate = window.localStorage.getItem('date_set_old');

	 var url = config_url+"reception/add_note/"+formDate+"/0";
       $.ajax({
       type:'POST',
       url: url,
       data:form_data,
       dataType: 'text',
       processData: false,
       contentType: false,
       success:function(data){
          var data = jQuery.parseJSON(data);
        
          if(data.message == "success")
			{
						

			   $('#calendar-all').fullCalendar('destroyEvents');
				var formDate = window.localStorage.getItem('date_set_old');
    			var url = config_url+'reception/get_todays_appointments/'+formDate;

				$('#calendar-all').fullCalendar( 'refetchEvents',url );
				
				addButtons(formDate);
				addInfo(formDate);
				addBottom(formDate);				
				

				document.getElementById("add_note").reset();
				$('#calendar_note').modal('hide'); 
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
	 
	
   
	
});


 
    

</script>
		
		<style type="text/css">
            .receipt_spacing{letter-spacing:0px; font-size: 12px;}
            .center-align{margin:0 auto; text-align:center;}
            
            .receipt_bottom_border{border-bottom: #888888 medium solid; margin-bottom: 5px;}
            .row .col-md-12 table {
                /*border:solid #000 !important;*/
                /*border-width:1px 0 0 1px !important;*/
                font-size:10px;
            }
            .row .col-md-12 th, .row .col-md-12 td {
                /*border:solid #000 !important;*/
                /*border-width:0 1px 1px 0 !important;*/
            }
            
            .row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 20px;}
            .title-img{float:left; padding-left:30px;}
            img.logo{max-height:70px;}
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
		  table.borderless td, .borderless th .borderless tr {
		    border: none !important;
		    color: black !important;

			}


			.borderless td, .borderless th
			{
				line-height: 1 !important;
				padding: 1px !important;
				/*padding: 5px;*/
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

		
			.fc-bgevent
			{
				/*border: 1px solid grey !important;*/
			}
			.fc-business-container
			{
				/*border: 1px solid grey !important;*/
			}

			table {
					    border:solid grey !important;
					    border-width:1px 0 0 1px !important;
					}
					th, td {
					    border:solid lightgrey !important;
					    border-width:0 1px 1px 0 !important;
					    z-index: 1;
					    /*background-color: #fff;*/
					    /*border-bottom: none !important;*/
					}
			table.borderless {
		    	border: none !important;
			}
			.fc-toolbar h2
			{
				font-size: 18px;
			}
			.fc-right
			{
				display: none;
			}
			.fc-time-grid .fc-slats td {
			    height: 3.9em !important;
			}
			#calendar-all .fc-scroller {
			  overflow-x: hidden !important;
			  overflow-y: hidden !important;
			}

			.fc-event .fc-bg {
				z-index: 10;
				background-color: #fff;
				/*opacity: 0.35;*/
				position: absolute !important;
			}
			.fc-time-grid .fc-event-container
			{
				background-color: #000 !important;
			}
        </style>
    </head>
    <body class="receipt_spacing" onLoad="">
    	<input type="hidden" id="date_today" value="<?php echo $date_today;?>">
    	<input type="hidden" id="todays_date" value="<?php echo $todays_date;?>">
    	<input type="hidden" id="config_url" value="<?php echo site_url();?>">
    	<!-- <div class="col-md-12 left-align ">
			<div class="row">
	        	<div class="col-xs-12 " style="padding-bottom: 5px;">
	            	<img src="<?php echo base_url().'assets/logo/'.$contacts['logo'];?>" alt="<?php echo $contacts['company_name'];?>" class="img-responsive logo"/>
	            </div>
	        </div>
	        
        </div> -->
   
        <div class="row receipt_bottom_border" >
        	<div class="col-md-12">
            	<div  style="padding-left: 20px;padding-right: 20px;" id="wrapper">
					<div id="calendar-all">
						<div class="fc-datePickerButton-button"></div>
						<div class="top-items" id="top-items"></div>
						<div class="head-info" id="head-info"></div>
						
					</div>		
					<div class="bottom-items" id="bottom-items"></div>
					<div class="bottom-head"></div>
					
				</div>
            </div>
        	
        </div>
		<!-- Full Google Calendar - Calendar -->
		<script src='<?php echo base_url()."assets/fullcalendar/";?>fullcalendar.min.js'></script>
		<script src='<?php echo base_url()."assets/fullcalendar/";?>scheduler.min.css'></script>
		<script src='<?php echo base_url()."assets/fullcalendar/";?>moment.min.js'></script>
		<script src='<?php echo base_url()."assets/fullcalendar/";?>scheduler.min.js'></script>

    </body>
</html>
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
	 var formDate = window.localStorage.getItem('date_set_old');
	 var url = config_url+"reception/get_featured_notes/"+formDate+"/2";
	 // var url = config_url+"reception/get_featured_notes/"+start_date+"/2";
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

	 var url = config_url+"reception/get_todays_top_notes/"+formDate+"/2";
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

	 var url = config_url+"reception/get_todays_bottom_notes/"+formDate+"/2";

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
				var toolbar = $("<div style='margin-left: 35px;'><table class='table table-bordered table-condensed' id='bottom-table'><thead><th style='width: 33%;'>NOTE</th><th style='width: 33%;'>NOTE</th><th style='width: 33%;'>NOTE</th></thead><tbody>"+data.content+"</tbody></table></div>")
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

