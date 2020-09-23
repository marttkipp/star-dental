
<script>

$(function() {

	// $("#schedule").customselect();

   $('#date_picker').datepicker({
   	// alert("sfksjhfkjs");
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
	 $(".fc-prevButton-button").after('<input  id="txtdate" type="text">');

	 $("#txtdate").datepicker({
        showOn: "button",
        buttonText: "Select date",
        dateFormat:"yy-mm-dd",
        // onSelect: function(dateText, inst) {
        //     alert(dateText);
        // }
    });

	 $("#txtdate").datepicker({
	    onSelect: function(dateText) {
	      // alert("Selected date: " + dateText + ", Current Selected Value= " + this.value);
	      $(this).change(this.value);
	    }
	  }).on("change", function(dateText) {
	    var bla = $('#txtdate').val();
	   	var date_time = new Date(bla).getTime() / 1000;
	    window.localStorage.setItem('date_set_old',date_time);
	    $('#calendar-all').fullCalendar('gotoDate', bla);
	    // alert("Change event"+bla);
	    addButtons(date_time);
		addInfo(date_time);
		addBottom(date_time);
	  });

	  function display(msg) {
	    $("<p>").html(msg).appendTo(document.body);
	  }
});


function renderCalendar()
 {
 	// document.getElementById("loader").style.display = "block";
 	var config_url = $('#config_url').val();	
 	var m = $.fullCalendar.moment();
 	var formDate = $.fullCalendar.formatDate(m, 'YYYY-MM-DD');
 	window.localStorage.setItem('date_set',formDate);
 	var start_date = window.localStorage.getItem('date_set');

	$('#calendar-all').fullCalendar({
			
		    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
		    defaultView: 'agendaDay',
		    timezone : 'local',
	        contentHeight: 'auto',
	        // theme: true,
	        // businessHours: true,
	        slotLabelFormat: [
							  'H:mm', // top level of text
							],
		    customButtons: {
		      myCustomButton: {
		        text: 'add note',
		        click: function() {
		          // alert("sdhkasjhdak");
		          $('#calendar_note').modal();
		        }
		      },
		      printButton: {
		        text: 'print',
		        click: function() {
		          // alert("sdhkasjhdak");
		          // $('#calendar_note').modal();
		          var start_date = window.localStorage.getItem('date_set_old');
		          // alert(start_date);
		          window.open(config_url+'uhdc-diary/print-uhdc-schedule/'+start_date, '_blank');
		        }
		      },
	          nextButton: {
	                text:'next',
	                click: function () {
						$('#calendar-all').fullCalendar('next');
						var moment = $('#calendar-all').fullCalendar('getDate');
						var start = moment.format();
						var array = start.split("T");

						var start_date = array[0];
						window.localStorage.setItem('date_set',start_date);

						var start_date = window.localStorage.getItem('date_set_old');

						addButtons(start_date);
						addInfo(start_date);
						addBottom(start_date);
						$('#calendar-all').fullCalendar('destroyEvents');
            			callback(events);

	                }
            	},prevButton: {
	                text:'prev',
	                click: function () {
						$('#calendar-all').fullCalendar('prev');
						var moment = $('#calendar-all').fullCalendar('getDate');
						var start = moment.format();
						var array = start.split("T");

						var start_date = array[0];
						window.localStorage.setItem('date_set',start_date);

						 var start_date = window.localStorage.getItem('date_set_old');
						 addButtons(start_date);
						 addInfo(start_date);
						 addBottom(start_date);
						 $('#calendar-all').fullCalendar('destroyEvents');
            			callback(events);
						

	                }
            	},
            	  datePickerButton: {
                themeIcon:'circle-triangle-s',
                click: function () {


                    var $btnCustom = $('.fc-datePickerButton-button'); // name of custom  button in the generated code
                    $btnCustom.after('<input type="hidden" id="hiddenDate" class="datepicker"/>');

                    $("#hiddenDate").datepicker({
                        showOn: "button",

                        dateFormat:"yy-mm-dd",
                        onSelect: function (dateText, inst) {
                            $('#calendar-all').fullCalendar('gotoDate', dateText);
                        },
                    });

                    var $btnDatepicker = $(".ui-datepicker-trigger"); // name of the generated datepicker UI 
                    //Below are required for manipulating dynamically created datepicker on custom button click
                    $("#hiddenDate").show().focus().hide();
                    $btnDatepicker.trigger("click"); //dynamically generated button for datepicker when clicked on input textbox
                    $btnDatepicker.hide();
                    $btnDatepicker.remove();
                    $("input.datepicker").not(":first").remove();//dynamically appended every time on custom button click

                }
            }
		    },
		    header: {
		      // left: 'prev,next today ',
		      // center: 'title',
		      right: 'myCustomButton,prevButton,nextButton,printButton,agendaDay,month,basicWeek,basicDay'
		    },
		     // date: '2018-12-13',
       //  	locale: currentLocaleCode,
		    resources: [
		      { id: 'a', title: 'SURGERY 1'},
		      { id: 'b', title: 'SURGERY 2' },
		      { id: 'c', title: 'SURGERY 3' },
		    ],
		    groupByResource: true,
		    showAsSeparateResource: false,
		    editable: false,
		    allDaySlot:false,
		    minTime: "06:30:00",
		    maxTime: "18:45:00",
		    slotDuration: '00:15:00',   
		    // timeFormat: 'h(:mm)',
		    titleFormat: 'dddd D MMMM YYYY',


			events: function(start, end, timezone, callback) {

								window.localStorage.setItem('date_set_old',end.unix());
						        $.ajax({
						          url: config_url+'reception/get_todays_appointments',
						          // type:'POST',
						          dataType: 'json',
						          data: {
						            start: start.unix(),
						            end: end.unix()
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
								  tinymce.init({
									                selector: ".cleditor",
									               	height: "150"
										            });
								if(status_event == 0)
								{
									
						            $('#new-appointment').html(data.results);
						   //          $('#eventUrl').attr('href',event.url);
						    		
						            $('#calendarModalNew').modal();
						            $("#patient_id"+event.id).customselect();
						            $("#service_charge_id"+event.id).customselect();

								}
								else
								{
									// $('#modalTitle').html(event.title);
						            $('#body-items').html(data.results);
						            $('#buttons-div').html(data.buttons);
						            
						            // $('#eventUrl').attr('href',event.url);
						           
						             
						            var appointment_type = data.appointment_type;
						            if(appointment_type == 1)
						            {
						            	get_recall_visit(data.visit_id,data.patient_id);
						            }
						            $('#calendarModal').modal();
								}
							}
						});
		           
		        },
		    eventRender: function(event, element) { 
		            element.find('.fc-title').append("<br/>" + event.description); 
		    },
		    dayClick: function(date, jsEvent, view, resource,event) {
		    		// document.getElementById("loader").style.display = "block";
		    		var config_url = $('#config_url').val();
		    		var start_date =  date.format();
		    		var end_date =  date.format();
		    		
		    	  	// jQuery.post(
			       //      config_url+"reception/create_appointment", 
			       //      { 
			       //          start: start,
			       //          end: end,
			       //          resource: resource.id,

			       //      }

			       //  );
			       // alert(start_date);
			       var url = config_url+"reception/create_appointment";
			       $.ajax({
			       type:'POST',
			       url: url,
			       data: { start: start_date, end: end_date,resource: resource.id },
			       dataType: 'text',
			       // processData: false,
			       // contentType: false,
			       success:function(data){
			          var data = jQuery.parseJSON(data);
			        
				          	if(data.message == "success")
							{
								// alert(data.message);
								$('#new-appointment').html(data.appointment_detail.results);
						    	$('#calendarModalNew').modal();
						        $("#patient_id"+data.appointment_id).customselect();
							}
							else
							{
								alert('Please ensure you have added included all the items');
							}
							// document.getElementById("loader").style.display = "none";
				       
				       },
				       error: function(xhr, status, error) {
				       alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
				       // document.getElementById("loader").style.display = "none";
				       
				       }
			       });

			       
			        $('#calendar-all').fullCalendar('unselect');
			       	var array = start_date.split("T");			       
			       	var start_date = array[0];			        
			       	refetch_events(start_date);	
			       	var url = config_url+'reception/get_todays_appointments/'+start_date;

				    $("#calendar-all").fullCalendar('addEventSource', url);
					$('#calendar-all').fullCalendar('rerenderEvents');
					$('#calendar-all').fullCalendar( 'refetchEvents',url );

					 $('#calendar-all').fullCalendar('destroyEvents');
					callback(events);
					document.getElementById("loader").style.display = "none";

			  }

		  });
	$('.fc-head').after($('.head-info'));
	$('.head-info').after($('.top-items'));
	$('.fc-body').after($('.bottom-items'));
	
	var start_date = window.localStorage.getItem('date_set_old');
	addButtons(start_date);
	addInfo(start_date);
	addBottom(start_date);

	document.getElementById("loader").style.display = "none";
	
 }

$("#calendarModalNew").on("hidden.bs.modal", function () {
    // put your default event here
    alert("shkajshkja");
});

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
	 // var values = $('#visit_type_id').val();
	 
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

	 var url = config_url+"reception/add_note/"+formDate+"/1";
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


$(document).on("submit","form#edit_note",function(e)
{
	e.preventDefault();
	// myApp.showIndicator();
	
	var form_data = new FormData(this);

	// alert(form_data);

	var config_url = $('#config_url').val();
	// var m = $.fullCalendar.moment();
 // 	var formDate = $.fullCalendar.formatDate(m, 'YYYY-MM-DD');	
	var formDate = window.localStorage.getItem('date_set_old');

	 var url = config_url+"reception/edit_note/"+formDate+"/0";
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
				
				$('#calendarNoteModal').modal('hide'); 
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
		/*line-height: 1 !important;*/
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

</style>

<div id="loader" style="display: none;"></div>
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<div id="calendar-all">
			<div class="fc-datePickerButton-button"></div>
			<div class="top-items" id="top-items"></div>
			<div class="head-info" id="head-info"></div>
			
		</div>		
		<div class="bottom-items" id="bottom-items"></div>
		<div class="bottom-head"></div>
	</div>
	
</div>
        <div id="calendarModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4  class="modal-title"> View Appointment </h4>
            </div>
            <div  class="modal-body"> 
            	<div id="body-items"></div>            	
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

    <div id="calendarNoteModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4  class="modal-title"> Edit Note </h4>
            </div>
            <div  class="modal-body"> 
	            <div id="note-info"></div>   
            </div>
            <div class="modal-footer"> 
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    </div>
    <div id="calendarPatientAppointmentModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4  class="modal-title"> Edit Appointment </h4>
            </div>
            <div  class="modal-body"> 
	            <div id="patient-info"></div>   
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
											<option value="a">SURGERY 1</option>
											<option value="b">SURGERY 2</option>
											<option value="c">SURGERY 3</option>
											
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
				$('#calendar-all').fullCalendar('destroyEvents');
				var formDate = window.localStorage.getItem('date_set_old');
    			var url = config_url+'reception/get_todays_appointments/'+formDate;

				$('#calendar-all').fullCalendar( 'refetchEvents',url );

				addButtons(formDate);
				addInfo(formDate);
				addBottom(formDate);				
					
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


function resheduled_appointments(appointment_id,status)
{
	var config_url = $('#config_url').val();	
	var res = confirm('Are you sure you want to reschedule this appointment ?');

	if(res)
	{

		 var url = config_url+"reception/reschedule_event_details/"+appointment_id+"/"+status;
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

	 var url = config_url+"reception/get_featured_notes/"+start_date+"/1";
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

	 var url = config_url+"reception/get_todays_top_notes/"+formDate+"/1";
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
				var toolbar = $("<div style='margin-left: 40px;'><table class='table table-bordered table-condensed' ><tbody>"+data.content+"</tbody></table></div>")
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
				var toolbar = $("<div style='margin-left: 40px;'><table class='table table-bordered table-condensed' id='bottom-table'><thead><th style='width: 33%;'>NOTE</th><th style='width: 33%;'>NOTE</th><th style='width: 33%;'>NOTE</th></thead><tbody>"+data.content+"</tbody></table></div>")
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

function get_note_details(calendar_note_id)
{	

		var config_url = $('#config_url').val();
	 	var url = config_url+"reception/get_note_detail/"+calendar_note_id;
		$.ajax({
		type:'POST',
		url: url,
		data:{calendar_note_id: calendar_note_id},
		dataType: 'text',
		processData: false,
		contentType: false,
		success:function(data){
		  var data = jQuery.parseJSON(data);
		  // alert(data.content);
		  if(data.message == "success")
			{
				$('#note-info').html(data.content);
				$('#calendarNoteModal').modal();
				
				
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
function edit_patient_appointment(appointment_id,type)
{
		$('#calendarModal').modal('hide');
	var config_url = $('#config_url').val();
	 	var url = config_url+"reception/get_edit_appointment_details/"+appointment_id;
		$.ajax({
		type:'POST',
		url: url,
		data:{appointment_id: appointment_id,type: type},
		dataType: 'text',
		processData: false,
		contentType: false,
		success:function(data){
		  var data = jQuery.parseJSON(data);
		  // alert(data.content);
		  if(data.message == "success")
			{
				$('#patient-info').html(data.results);
				$('#calendarPatientAppointmentModal').modal();
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

function send_message_note(appointment_id,page)
{
	var res = confirm('Do you want to send a thank you note ?');

	if(res)
	{

		var config_url = $('#config_url').val();
	 	var url = config_url+"reception/send_thank_you_note/"+appointment_id;
		$.ajax({
		type:'POST',
		url: url,
		data:{appointment_id: appointment_id},
		dataType: 'text',
		processData: false,
		contentType: false,
		success:function(data){
		  var data = jQuery.parseJSON(data);
		 
		  $('#calendarModal').modal('hide');

		},
		error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);

		}
		});
	}
}

function submit_patient_recall(appointment_id)
{

	var config_url = $('#config_url').val();
 	var url = config_url+"reception/send_recall_list/"+appointment_id;

 	var notes = tinymce.get('summary_notes'+appointment_id).getContent();
	var period = $('#period_id'+appointment_id).val();
	var list = $('#list_id'+appointment_id).val();
	var visit_id = $('#visit_id'+appointment_id).val();
	var patient_id = $('#patient_id'+appointment_id).val();
	var doctor_id = $('#doctor_id'+appointment_id).val();
	// alert(notes);
	$.ajax({
	type:'POST',
	url: url,
	data:{summary_notes: notes,period_id: period,list_id: list,patient_id: patient_id,visit_id: visit_id,doctor_id: doctor_id},
	dataType: 'text',
	// processData: false,
	// contentType: false,
	success:function(data){
	  var data = jQuery.parseJSON(data);
	  // alert(data.message);
	  if(data.message == 'success')	 
	  {
	  	alert('You have successfully added the patient to the recall list.');
	  	$('#calendar-all').fullCalendar('destroyEvents');
			var formDate = window.localStorage.getItem('date_set_old');
			var url = config_url+'reception/get_todays_appointments/'+formDate;

			$('#calendar-all').fullCalendar( 'refetchEvents',url );
			
			addButtons(formDate);
			addInfo(formDate);
			addBottom(formDate);
	  	 $('#calendarModal').modal('hide');


	  }
	  else
	  {
	  	alert(data.result);
	  }
	 

	},
	error: function(xhr, status, error) {
	alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);

	}
	});
	get_recall_visit(visit_id,patient_id);
}
function get_recall_visit(visit_id,patient_id) {
	// body...

	var config_url = $('#config_url').val();
 	var url = config_url+"reception/get_recall_list/"+visit_id+"/"+patient_id;
	// alert(summary_notes);
	$.ajax({
	type:'POST',
	url: url,
	data:{patient_id: patient_id,visit_id: visit_id},
	dataType: 'text',
	// processData: false,
	// contentType: false,
	success:function(data){
	  var data = jQuery.parseJSON(data);

	  if(data.message == 'success')	 
	  {
	  	$('#recall-view').html(data.results);
	  }
	  else
	  {
	  	// alert(data.result);
	  }
	 

	},
	error: function(xhr, status, error) {
	alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);

	}
	});
}

function send_patient_note(appointment_id)
{

	// document.getElementById("loader").style.display = "block";
	var config_url = $('#config_url').val();
 	var url = config_url+"reception/send_patient_message/"+appointment_id;
	// alert(summary_notes);
	var message = tinymce.get('message'+appointment_id).getContent();
	var visit_id = $('#visit_id'+appointment_id).val();
	var patient_id = $('#patient_id'+appointment_id).val();
	var subject = $('#subject'+appointment_id).val();
	var email = $('#email'+appointment_id).val();
	var option = document.getElementById("optionsRadios1"+appointment_id).value; 
	$.ajax({
	type:'POST',
	url: url,
	data:{patient_id: patient_id,visit_id: visit_id,message: message,option: option,subject: subject,email: email},
	dataType: 'text',
	// processData: false,
	// contentType: false,
	success:function(data){
	  var data = jQuery.parseJSON(data);
	  $('#calendarModal').modal('hide');
	  if(data.message == 'success')	 
	  {
	  	// $('#message-view').html(data.results);
	  	alert('You have successfully sent the message to the patient.');
	  	// $('#calendarModal').modal('hide');
	  }
	  else
	  {
	  	alert(data.result);
	  }

	  tinymce.get('message'+appointment_id).setContent('');
	  $('#calendarModal').modal('hide');
	  // document.getElementById("loader").style.display = "none";
	},
	error: function(xhr, status, error) {
	alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	// document.getElementById("loader").style.display = "none";
	}
	});
}

function initialize_editor()
{

	 tinymce.init({
	                selector: ".cleditor",
	               	height: "150"
		            });
}
$(document).on("submit","form#edit_event",function(e)
{
	e.preventDefault();
	// myApp.showIndicator();
	
	var form_data = new FormData(this);

	// alert(form_data);

	var config_url = $('#config_url').val();	

	 var url = config_url+"reception/edit_event";
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
							
				$('#calendarPatientAppointmentModal').modal('hide');
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
$(document).on("submit","form#edit_appointment",function(e)
{
	e.preventDefault();
	// myApp.showIndicator();
	
	var form_data = new FormData(this);

	// alert(form_data);

	var config_url = $('#config_url').val();	

	 var url = config_url+"reception/edit_appointment_detail";
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
							
				$('#calendarPatientAppointmentModal').modal('hide');
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

