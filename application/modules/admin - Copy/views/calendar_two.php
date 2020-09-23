
<script>

$(function() {

	// var currentLocaleCode = 'en';
    // build the locale selector's options
   $('#datepicker').datepicker({

        showOn: "both",
        buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
        buttonImageOnly: true,
        buttonText: " ",
        dateFormat:"yy-mm-dd",
        onSelect: function (dateText, inst) {
            $('#calendar-all').fullCalendar('gotoDate', dateText);
        },

    });
	renderCalendar();
});

$(document).on("submit","form#add_appointment",function(e)
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
				$('#calendar-all').fullCalendar('destroy');
       			renderCalendar();				
				
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
				$('#calendar-all').fullCalendar('destroy');
       			renderCalendar();				
				
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
	var m = $.fullCalendar.moment();
 	var formDate = $.fullCalendar.formatDate(m, 'YYYY-MM-DD');	

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
				// alert(data.message);
				$('#calendar-all').fullCalendar('destroy');
       			renderCalendar();
    // 			addButtons();
				// addInfo();		

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


 function renderCalendar()
 {
 	var config_url = $('#config_url').val();	
 	var m = $.fullCalendar.moment();
 	var formDate = $.fullCalendar.formatDate(m, 'YYYY-MM-DD');

 	// var m2 = $.fullCalendar.formatRange(m, 'yyyy-mm-dd');
	// var m = calendar.moment();
// alert(m); 

	$('#calendar-all').fullCalendar({
			
		    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
		    defaultView: 'agendaDay',
		    timezone : 'local',
	        height:750,
	        // theme: true,
	        businessHours: true,
		    customButtons: {
		      myCustomButton: {
		        text: 'add note',
		        click: function() {
		          // alert("sdhkasjhdak");
		          $('#calendar_note').modal();
		        }
		      },
	          datePickerButton: {
                title:'Calendar',
                click: function () {


                }
            	},
		      
		      printButton: {
		        text: 'Print',
		        click: function() {
		          printPreview();
		           // window.print();
		        }
		      }
		    },
		    header: {
		      // left: 'prev,next today ',
		      // center: 'title',
		      right: 'myCustomButton,prev,agendaDay,next'
		    },
		     // date: '2018-12-13',
       //  	locale: currentLocaleCode,
		    resources: [
		      { id: 'a', title: 'CLINIC 1'},
		      { id: 'b', title: 'CLINIC 2' },
		      { id: 'c', title: 'CLINIC 3' }
		    ],
		    groupByResource: true,
		    showAsSeparateResource: false,
		    editable: false,
		    allDaySlot:false,
		    minTime: "06:00:00",
		    maxTime: "19:00:00",
		    // slotDuration: '00:15:00',   
		    events: {
				        url: config_url+'reception/get_todays_appointments/'+formDate,
				       
				        error: function() {
				          $('#script-warning').show();
				        }
				      },
		    eventClick:  function(event, jsEvent, view) {
		    		// alert(event.id);
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
						    		$("#patient_id"+event.id).customselect();
						            $('#calendarModalNew').modal();

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
		    		
		    	  	jQuery.post(
			            config_url+"reception/create_appointment", 
			            { // re-use event's data
			                // title: title,
			                start: start,
			                end: end,
			                resource: resource.id,

			            }

			        );
			        // $('#calendar-all').fullCalendar( 'refetchEvents',event);
			        // $('#calendar-all').fullCalendar( 'refetchEvents',event);
			       var array = start.split("T");
			       
			       	var start_date = array[0];
			        var url = config_url+'reception/get_todays_appointments/'+start_date;
			         // alert(url);
			        $('#calendar-all').fullCalendar( 'refetchEvents',url);
			        // $('#calendar-all').fullCalendar('unselect');
			  		
			    

			  }

		  });
	addButtons();
	addInfo();
	addBottom();
 }
    

</script>
<style type="text/css">
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
  .borderless td, .borderless th .borderless tr {
    border: none;
	}
	.borderless td, .borderless th
	{
		line-height: 0.6 !important;
		padding: 5px !important;
	}
	/*.table.borderless > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
	{
		border: none !important;
	}*/
</style>
<div class="row" style="margin-top: 20px;">
	<!-- <div class="panel-body">
		<div class="padd"> -->
				<div id="calendar-all">
					
				</div>		<!-- </div>
	</div> -->
	<div class="bottom-head"></div>
	<!-- <div style="margin-left: 40px;">
		<table class="table table-bordered table-condensed" >
			<thead>
				<th>NOTE</th>
				<th>NOTE</th>
				<th>NOTE</th>
			</thead>
			<tbody>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
		</table>
	</div> -->
	
</div>
        <div id="calendarModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4  class="modal-title"> View Appointment </h4>
            </div>
            <div  class="modal-body"> 
            	<div id="body-items">
            		
            	</div>
            	
            </div>
            <div class="modal-footer">
            	<div id="buttons-div"></div>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    </div>

      <div id="calendarModalNew" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4  class="modal-title"> Add New Appointment </h4>
            </div>
            <div  class="modal-body"> 
	            	<div id="new-appointment">
	            		
	            	</div>
	            	
            	
            </div>
            <div class="modal-footer"> 
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    </div>
    <div id="calendar_note" class="modal fade">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
	                <h4  class="modal-title"> Add Note </h4>
	            </div>
	            <div  class="modal-body"> 
	            	<form id="add_note" method="post">
						<div class="row">
							<div class="col-md-8 col-md-offset-2">
								<div class="form-group">
									<label class="col-lg-4 control-label">Schedule: </label>
									<div class="col-lg-8">
										<select name="schedule" id="schedule" class="form-control">
											<option value="">----Select a Schedule----</option>
											<option value="a">CLINIC 1</option>
											<option value="b">CLINIC 2</option>
											<option value="c">CLINIC 3</option>
											
										</select>
									</div>
								 </div>

								 <div class="form-group">
									<label class="col-lg-4 control-label">Note: </label>
									<div class="col-lg-8">
										<textarea id="schedule_note" class="form-control" name="schedule_note"></textarea>
									</div>
								 </div>
								 <div class="form-group">
									<label class="col-lg-4 control-label">Type *: </label>
									<div class="col-lg-8">
										<select name="type" id="type" class="form-control">
											<option value="">---- Select a Type----</option>
											<option value="1">TOP</option>
											<option value="2">BOTTOM</option>
											
										</select>
									</div>
								 </div>
								 <div class="form-group">
									<label class="col-lg-4 control-label">Featured ? </label>
						            <div class="col-lg-4">
						                <div class="radio">
						                    <label>
						                        <input id="optionsRadios1" type="radio" name="featured" value="0" checked="checked">
						                        No
						                    </label>
						                </div>
						            </div>
						            
						            <div class="col-lg-4">
						                <div class="radio">
						                    <label>
						                        <input id="optionsRadios1" type="radio" name="featured" value="1" >
						                        Yes
						                    </label>
						                </div>
						            </div>
								</div>
								<div class="form-group">
									<label class="col-lg-4 control-label">End date: </label>
									
									<div class="col-lg-8">
						                <div class="input-group">
						                    <span class="input-group-addon">
						                        <i class="fa fa-calendar"></i>
						                    </span>
						                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="end_date" placeholder="End Date" value="<?php echo date('Y-m-d');?>">
						                </div>
									</div>
								</div>
								
							</div>
						</div>

						<br/>
						<div class="row">
					        <div class="col-md-12">
					        	<div class=" center-align">
					        		<button type="submit" class="btn btn-sm btn-success ">ADD NOTE DETAIL</button>
					        	</div>
					               
					        </div>
					    </div>
					</form>
	            </div>
	            <div class="modal-footer">
	            	<div id="buttons-div"></div>
	                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	            </div>
	        </div>
	    </div>
	</div>
<script type="text/javascript">
function get_appointment_view(appointment_id)
{
	if(appointment_id == '1')
	{
		$('#patient_appointment').css('display', 'block');
		$('#event_appointment').css('display', 'none');
	}
	else
	{
		$('#patient_appointment').css('display', 'none');
		$('#event_appointment').css('display', 'block');
	}
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
					$('#calendar-all').fullCalendar('destroy');
	       			renderCalendar();								
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
	 	$('#calendar-all').fullCalendar('destroy');
			renderCalendar();								
		$('#calendarModal').modal('hide');
	 }

}

function addButtons() {

var m = $.fullCalendar.moment();
 	var formDate = $.fullCalendar.formatDate(m, 'YYYY-MM-DD');

	var config_url = $('#config_url').val();	

	 var url = config_url+"reception/get_featured_notes/"+formDate+"/0";
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
				var toolbar = $("<div class='head-info'>"+data.content+"</div>")
				.addClass("fc-toolbar")
				.addClass("fc-header-toolbar")

				toolbar.append($("<div/>", { "class": "fc-clear"}));
				// insert row before title.
				$(".fc-head").after(toolbar);
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


function addInfo() {
	// create buttons
	

	// create tr with buttons.
	// Please note, if you want the buttons to be placed at the center or right,
	// you will have to append more <td> elements
	var m = $.fullCalendar.moment();
 	var formDate = $.fullCalendar.formatDate(m, 'YYYY-MM-DD');

	var config_url = $('#config_url').val();	

	 var url = config_url+"reception/get_todays_top_notes/"+formDate+"/0";
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
				var toolbar = $("<div style='margin-left: 40px;'><table class='table table-bordered table-condensed' ><tbody>"+data.content+"</tbody></table></div>")
				toolbar.append($("<div/>", { "class": "fc-clear"}));
				// insert row before title.
				$(".head-info").after(toolbar);
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

function addBottom() {
	// create buttons
	

	// create tr with buttons.
	// Please note, if you want the buttons to be placed at the center or right,
	// you will have to append more <td> elements
	var m = $.fullCalendar.moment();
 	var formDate = $.fullCalendar.formatDate(m, 'YYYY-MM-DD');

	var config_url = $('#config_url').val();	

	 var url = config_url+"reception/get_todays_bottom_notes/"+formDate+"/0";
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
				var toolbar = $("<div style='margin-left: 40px;'><table class='table table-bordered table-condensed' ><thead><th style='width: 33%;'>NOTE</th><th style='width: 33%;'>NOTE</th><th style='width: 33%;'>NOTE</th></thead><tbody>"+data.content+"</tbody></table></div>")
				toolbar.append($("<div/>", { "class": "fc-clear"}));
				// insert row before title.
				$(".bottom-head").after(toolbar);
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
	var res = confirm('Are you sure you want to delete this schedule ?');

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
					// alert(data.message);
					$('#calendar-all').fullCalendar('destroy');
	       			renderCalendar();								
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
	 	$('#calendar-all').fullCalendar('destroy');
			renderCalendar();								
		$('#calendarModal').modal('hide');
	 }

}

</script>

