<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_blog extends CI_Migration 
{
	public function up()
	{
		/*$this->load->dbforge();
		//personnel
		$fields = array(
                        'bank_branch_id' => array('type' => 'int', 'null' => TRUE),
                        'bank_account_name' => array('type' => 'varchar', 'constraint' => '100', 'null' => TRUE)
					);
		$this->dbforge->add_column('personnel', $fields);
		
		$fields = array(
                        'bank_account_name' => array(
							'name' => 'bank_account_number',
							'type' => 'varchar',
							'constraint' => '100', 
							'null' => TRUE
						),
					);
		$this->dbforge->modify_column('personnel', $fields);*/
	}

	public function down()
	{
		//$this->dbforge->drop_table('blog');
	}
}