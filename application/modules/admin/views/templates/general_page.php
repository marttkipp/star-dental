<?php 
    
    if(!isset($contacts))
    {
        $contacts = $this->site_model->get_contacts();
    }
    $data['contacts'] = $contacts; 

?>
<!doctype html>
<html class="fixed sidebar-left-collapsed">
    <head>
        <?php echo $this->load->view('admin/includes/header', $contacts, TRUE); ?>
          <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery/jquery.js"></script>  
    </head>

    <body>
        <input type="hidden" id="base_url" value="<?php echo site_url();?>">
        <input type="hidden" id="config_url" value="<?php echo site_url();?>">
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
        <![endif]-->
        <section class="body">
            <!-- Top Navigation -->
            <?php echo $this->load->view('admin/includes/top_navigation', $data, TRUE); ?>
            
                
            <div class="inner-wrapper">
                <?php echo $this->load->view('admin/includes/top_level_navigation', '', TRUE); ?>
                
                
                <section role="main" class="content-body">
                    
                    
                    <?php echo $content;?>
                
                </section>
            </div>
             <aside id="sidebar-right" class="sidebar-right" style="display: none;">
                <div class="nano has-scrollbar">
                    <div class="nano-content" tabindex="0" style="right: -17px;">
                        <!-- <a href="#" class="mobile-close d-md-none">
                            Collapse <i class="fa fa-chevron-right"></i>
                        </a> -->
            
                        <div class="sidebar-right-wrapper">
                            <div id="current-sidebar-div"></div>
                            <div id="existing-sidebar-div"></div>  
                            <div id="sidebar-div"></div>                           
            
                        </div>
                    </div>
                <div class="nano-pane" style="opacity: 1; visibility: visible;"><div class="nano-slider" style="height: 189px; transform: translate(0px);"></div></div></div>
            </aside>
            
        </section>
        
        <!-- Vendor -->
           
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>     
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery-cookie/jquery.cookie.js"></script>     
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/js/bootstrap.js"></script>      
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/nanoscroller/nanoscroller.js"></script>       
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>        
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/magnific-popup/magnific-popup.js"></script>       
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>
        
        <!-- Specific Page Vendor -->       
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>        
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery-ui-touch-punch/jquery.ui.touch-punch.js"></script>     
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery-appear/jquery.appear.js"></script>     
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js"></script>     
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery-easypiechart/jquery.easypiechart.js"></script>     
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/flot/jquery.flot.js"></script>        
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/flot-tooltip/jquery.flot.tooltip.js"></script>        
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/flot/jquery.flot.pie.js"></script>        
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/flot/jquery.flot.categories.js"></script>     
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/flot/jquery.flot.resize.js"></script>
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery-sparkline/jquery.sparkline.js"></script>       
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/raphael/raphael.js"></script>     
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/morris/morris.js"></script>       
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/gauge/gauge.js"></script>     
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/snap-svg/snap.svg.js"></script>       
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/liquid-meter/liquid.meter.js"></script>       
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jqvmap/jquery.vmap.js"></script>      
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jqvmap/data/jquery.vmap.sampledata.js"></script>      
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jqvmap/maps/jquery.vmap.world.js"></script>       
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jqvmap/maps/continents/jquery.vmap.africa.js"></script>       
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jqvmap/maps/continents/jquery.vmap.asia.js"></script>     
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jqvmap/maps/continents/jquery.vmap.australia.js"></script>        
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jqvmap/maps/continents/jquery.vmap.europe.js"></script>       
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jqvmap/maps/continents/jquery.vmap.north-america.js"></script>        
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jqvmap/maps/continents/jquery.vmap.south-america.js"></script>    
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>            
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/select2/select2.js"></script>
        
        <!-- Theme Base, Components and Settings -->
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/assets/javascripts/";?>theme.js"></script>
        <script src="<?php echo base_url()."assets/themes/jasny/js/jasny-bootstrap.js";?>"></script>
        
        <!-- Theme Custom -->
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/javascripts/theme.custom.js"></script>
        
        <!-- Theme Initialization Files -->
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/javascripts/theme.init.js"></script>

        <!-- Example -->
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/javascripts/dashboard/examples.dashboard.js"></script>
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/javascripts/charts.js"></script>
        <!-- s -->
        <!-- Full Google Calendar - Calendar -->
        <!-- <script src="<?php echo base_url()."assets/bluish/"?>js/fullcalendar.min.js"></script>  -->
        <script src='<?php echo base_url()."assets/fullcalendar/";?>fullcalendar.min.js'></script>
        <script src='<?php echo base_url()."assets/fullcalendar/";?>scheduler.min.css'></script>
        <script src='<?php echo base_url()."assets/fullcalendar/";?>moment.min.js'></script>
        <script src='<?php echo base_url()."assets/fullcalendar/";?>scheduler.min.js'></script>
        <!-- jQuery Flot -->
        <script src="<?php echo base_url()."assets/bluish/"?>js/excanvas.min.js"></script>
        <script src="<?php echo base_url()."assets/bluish/"?>js/jquery.flot.js"></script>
        <script src="<?php echo base_url()."assets/bluish/"?>js/jquery.flot.resize.js"></script>
        <script src="<?php echo base_url()."assets/bluish/"?>js/jquery.flot.axislabels.js"></script>
        <script src="<?php echo base_url()."assets/bluish/"?>js/jquery.flot.pie.js"></script>
        <script src="<?php echo base_url()."assets/bluish/"?>js/jquery.flot.stack.js"></script>
        <!-- <script src="<?php echo base_url()."assets/"?>js/main.js"></script> -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/bluish";?>/style/jquery.cleditor.css"> 
        <script src="<?php echo base_url()."assets/themes/bluish";?>/js/jquery.cleditor.min.js"></script> <!-- CLEditor -->
        <script type="text/javascript" src="<?php echo base_url();?>assets/themes/tinymce/tinymce.min.js"></script>
         <script src='<?php echo base_url()."assets/bluish/"?>src/jquery-customselect.js'></script>
        <link href='<?php echo base_url()."assets/bluish/"?>src/jquery-customselect.css' rel='stylesheet' />
         <!-- <script src="<?php echo base_url()."assets/bluish/"?>src/owl.carousel.js"></script> -->
        <script type="text/javascript">
            tinymce.init({
                selector: ".cleditor",
                height: "250"
            });
        </script>
        <script>
          $('.owl-carousel').owlCarousel({
            loop:true,
            margin:10,
            nav:true,
            responsive:{
                0:{
                    items:1
                },
                600:{
                    items:1
                },
                1000:{
                    items:1
                }
            }
        })   
           function close_side_bar()
            {
                // $('html').removeClass('sidebar-right-opened');
                document.getElementById("sidebar-right").style.display = "none"; 
                // document.getElementById("current-sidebar-div").style.display = "none"; 
                // document.getElementById("existing-sidebar-div").style.display = "none"; 
                tinymce.remove();
            }

            function open_sidebar()
            {
                document.getElementById("sidebar-right").style.display = "block"; 
                document.getElementById("current-sidebar-div").style.display = "none"; 
            }

        function change_branch(branch_id)
        {
            var current_page = $('#current_page').val();
            var config_url = $('#config_url').val();
            var url = config_url+"site/change_branch/"+branch_id;
            // alert(current_page);
            $.ajax({
            type:'POST',
            url: url,
            data:{branch_id: branch_id},
            dataType: 'text',
            // processData: false,
            // contentType: false,
            success:function(data){
              var data = jQuery.parseJSON(data);

              if(data.message == 'success')  
              {
                window.location.href = current_page;
              }
              else
              {
               
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
