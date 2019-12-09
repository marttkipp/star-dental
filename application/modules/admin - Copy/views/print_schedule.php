<iframe src="<?php echo site_url().'admin/print_agenda/'.$todays_date?>" name="iframe_a" height="100%" width="100%"></iframe>
<button onclick="print_frame()">print</button>
<script>

function print_frame()
{
  window.frames["iframe_a"].focus();
  window.frames["iframe_a"].print();
}
</script>
