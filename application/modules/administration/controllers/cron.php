<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron  extends MX_Controller
{	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('administration/sync_model');
	}
	
	public function sync_visits()
	{
		
		$date = date('Y-m-d');
		//Sync OSE
		$this->session->set_userdata('branch_code', 'OSE');
		$this->db->where('branch_code = "'.$this->session->userdata('branch_code').'" AND visit_date = "'.$date.'"');
		$query = $this->db->get('visit');
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $res)
			{
				$visit_id = $res->visit_id;
				
				if($this->sync_model->syn_up_on_closing_visit($visit_id))
				{
				}
			}
		}
	}
	public function backup()
    {
        // Load the DB utility class
        $this->load->dbutil();
        $date = date('YmdHis');
        $prefs = array(
            'ignore'        => array('table','diseases','icd10'),                     // List of tables to omit from the backup
            'format'        => 'txt',                       // gzip, zip, txt
            'filename'      => $date.'_backup.sql',              // File name - NEEDED ONLY WITH ZIP FILES
            'newline'       => "\n"                         // Newline character used in backup file
        );

        // Backup your entire database and assign it to a variable
        $backup = $this->dbutil->backup($prefs);

        // Load the file helper and write the file to your server
        $this->load->helper('file');
        write_file('G://backups/'.$date.'_backup.sql', $backup);

        // Load the download helper and send the file to your desktop
        // $this->load->helper('download');
        // force_download('mybackup.gz', $backup);



    }
}
?>