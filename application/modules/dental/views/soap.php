<?php echo form_open("nurse/submit-doctors-notes", array("class" => "form-horizontal"));?>

  <div id="doctor_notes"></div>
                        
<?php echo form_close();?>




<script type="text/javascript">

  $(document).ready(function(){
      doctor_notes(<?php echo $visit_id;?>);
      
  });









function doctor_notes(visit_id){
    var XMLHttpRequestObject = false;
    
  if (window.XMLHttpRequest) {
  
    XMLHttpRequestObject = new XMLHttpRequest();
  } 
    
  else if (window.ActiveXObject) {
    XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
  }
  
  var config_url = $('#config_url').val();
  var url = config_url+"/nurse/doctor_notes/"+visit_id;
  
  if(XMLHttpRequestObject) {
    
    var obj = document.getElementById("doctor_notes");
        
    XMLHttpRequestObject.open("GET", url);
        
    XMLHttpRequestObject.onreadystatechange = function(){
      
      if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
        obj.innerHTML = XMLHttpRequestObject.responseText;
        // window.alert("Dotors notes are saved");
        
      }
    }
        
    XMLHttpRequestObject.send(null);
  }
}

</script>