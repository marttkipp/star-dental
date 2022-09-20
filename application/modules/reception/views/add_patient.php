
<?php 
    echo form_open("reception/register_other_patient", array("class" => "form-horizontal"));
    form_hidden('visit_type_id', 3);
    if(isset($dependant_parent))
    {
        form_hidden('dependant_id', $dependant_parent);
    }
    
    else
    {
        form_hidden('dependant_id', 0);
    }
?>
 <section class="panel">
    <header class="panel-heading">
            <h5 class="pull-left"><i class="icon-reorder"></i>Add Patient</h5>
          <div class="widget-icons pull-right">
               <a href="<?php echo site_url();?>patients" class="btn btn-success btn-sm pull-right">  Patients List</a>

          </div>
          <div class="clearfix"></div>
    </header>
      <div class="panel-body">
        <div class="padd">
          <div class="row">
            <div class="col-md-6">
                <div class="form-group">
               
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Name: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_surname" placeholder="Name" value="<?php echo set_value('patient_surname');?>">
                    </div>
                </div>
                
                <div class="form-group" style="display: none;">
                    <label class="col-md-4 control-label">Other Names: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_othernames" placeholder="Other Names">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Gender: </label>
                    
                    <div class="col-md-8">
                        <select class="form-control" name="gender_id">
                            <?php
                                if($genders->num_rows() > 0)
                                {
                                    $gender = $genders->result();
                                    
                                    foreach($gender as $res)
                                    {
                                        $gender_id = $res->gender_id;
                                        $gender_name = $res->gender_name;
                                        
                                        if($gender_id == set_value("gender_id"))
                                        {
                                            echo '<option value="'.$gender_id.'" selected>'.$gender_name.'</option>';
                                        }
                                        
                                        else
                                        {
                                            echo '<option value="'.$gender_id.'">'.$gender_name.'</option>';
                                        }
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                 <div class="form-group"  style="display: none;">
                    <label class="col-md-4 control-label">Patient Number: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="current_patient_number" placeholder="Patient Number"  readonly="readonly">
                        NOTE: For a new patient leave this field blank
                    </div>
                </div>
                <div class="form-group" >
                    <label class="col-lg-4 control-label">An Appointment?</label>
                    <div class="col-lg-8">
                        <div class="radio">
                            <label>
                                <input id="optionsRadios1" type="radio"  value="1" name="appointment_status">
                                Yes
                            </label>
                            <label>
                                <input id="optionsRadios2" type="radio" checked value="0" name="appointment_status">
                                No
                            </label>
                        </div>
                        
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Email Address: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_email" placeholder="Email Address">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Date of Birth : </label>
            
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="patient_dob" placeholder="Date of Birth">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Primary Phone: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_phone1" placeholder="Primary Phone">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Other Phone: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_phone2" placeholder="Other Phone">
                    </div>
                </div>

                
            </div>
            </div>
            <div class="col-md-6">
                
                
                
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Residence: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_town" placeholder="Residence">
                    </div>
                </div>
                
               
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Next of Kin Surname: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_kin_sname" placeholder="Kin Surname">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Next of Kin Other Names: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_kin_othernames" placeholder="Kin Other Names">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Next of Kin Contact: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="next_of_kin_contact" placeholder="">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Relationship To Kin: </label>
                    
                    <div class="col-md-8">
                        <select class="form-control" name="relationship_id">
                            <?php
                                if($relationships->num_rows() > 0)
                                {
                                    $relationship = $relationships->result();
                                    
                                    foreach($relationship as $res)
                                    {
                                        $relationship_id = $res->relationship_id;
                                        $relationship_name = $res->relationship_name;
                                        
                                        if($relationship_id == set_value("relationship_id"))
                                        {
                                            echo '<option value="'.$relationship_id.'" selected>'.$relationship_name.'</option>';
                                        }
                                        
                                        else
                                        {
                                            echo '<option value="'.$relationship_id.'">'.$relationship_name.'</option>';
                                        }
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Insurance : </label>
                    
                    <div class="col-md-8">
                        <select class="form-control" name="insurance_company_id">
                            <option value="0">Select an insurance Company</option>
                            <?php
                                if($insurance->num_rows() > 0)
                                {
                                    $insurance = $insurance->result();
                                    
                                    foreach($insurance as $res)
                                    {
                                        $visit_type_id1 = $res->visit_type_id;
                                        $visit_type_name = $res->visit_type_name;
                                        
                                        if($visit_type_id1 ==  set_value("insurance_company_id"))
                                        {
                                            echo '<option value="'.$visit_type_id1.'" selected>'.$visit_type_name.'</option>';
                                        }
                                        
                                        else
                                        {
                                            echo '<option value="'.$visit_type_id1.'">'.$visit_type_name.'</option>';
                                        }
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-4 control-label">How did you know about us? </label>
                    <div class="col-lg-6">
                        <div id="places-list-view"></div>
                         
                        
                    </div>
                    <div class="col-md-2">
                        <a class="btn btn-warning btn-sm" onclick="add_a_place()"><i class="fa fa-plus"></i> Add a place</a>
                    </div>
                </div>

                    <div class="form-group">
                        <div class="radio">
                            <label class="col-lg-4 control-label">
                                Specifiy
                                
                            </label>
                              <div class="col-md-8">
                                    <input type="text" class="form-control" name="about_us_view" placeholder="Specify the person /location or how you knew about us">
                                </div>
                        </div>
                    </div>
                
            </div>
        </div>
        <br>
        <div class="center-align">
            <button class="btn btn-info btn-sm" type="submit">Add Patient</button>
        </div>
        </div>
    </div>
</section>
<?php echo form_close();?>


<script type="text/javascript">
    $(function() {
        get_all_places_list();
   
    });

    function add_a_place()
    {
        document.getElementById("sidebar-right").style.display = "block"; 
      // document.getElementById("existing-sidebar-div").style.display = "none"; 

      var config_url = $('#config_url').val();
    $.ajax({
            type:'POST',
            url: config_url+"reception/add_a_place",
            cache:false,
            contentType: false,
            processData: false,
            dataType: "text",
            success:function(data){
                 // var data = jQuery.parseJSON(data);
                // alert();
                // var status_event = data.status;

                // alert(data.results);


                 document.getElementById("current-sidebar-div").style.display = "block"; 
                $('#current-sidebar-div').html(data);

                get_all_places();

                $('.datepicker').datepicker({
                        format: 'yyyy-mm-dd'
                    });

                // $('.datepicker').datepicker();
                $('.timepicker').timepicker();

                
            }
        });
    }

    function get_all_places()
    {
        var config_url = $('#config_url').val();
        $.ajax({
            type:'POST',
            url: config_url+"reception/get_all_places",
            cache:false,
            contentType: false,
            processData: false,
            dataType: "text",
            success:function(data){
           
                $('#places-list').html(data);

            }
        });
    }


$(document).on("submit","form#add-place",function(e)
{


    e.preventDefault();
    

    var res = confirm('Are you sure you want to add a place. ?');


    if(res)
    {
        var form_data = new FormData(this);

        // alert(form_data);
        
        var config_url = $('#config_url').val();    

         var url = config_url+"reception/add_place";
         
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
               get_all_places();
              get_all_places_list();
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
     
    
   
    
});


function close_side_bar()
{
    // $('html').removeClass('sidebar-right-opened');
    document.getElementById("sidebar-right").style.display = "none"; 
    document.getElementById("current-sidebar-div").style.display = "none"; 
    // document.getElementById("existing-sidebar-div").style.display = "none"; 
    tinymce.remove();
}


function get_all_places_list()
{
    var config_url = $('#config_url').val();
    $.ajax({
        type:'POST',
        url: config_url+"reception/get_all_places_list",
        cache:false,
        contentType: false,
        processData: false,
        dataType: "text",
        success:function(data){
       
            $('#places-list-view').html(data);

        }
    });
}

function delete_place(place_id)
{

    var res = confirm('Are you sure you want to delete this place. ?');


    if(res)
    {
        var config_url = $('#config_url').val();    

         var url = config_url+"reception/delete_place/"+place_id;
         
           $.ajax({
           type:'POST',
           url: url,
           data:{place_id: place_id},
           dataType: 'text',
           processData: false,
           contentType: false,
           success:function(data){
            var data = jQuery.parseJSON(data);
            
            if(data.message == "success")
            {
               get_all_places();
               get_all_places_list();
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

}

</script>