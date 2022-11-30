<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_add_pota_columns
 *
 * Add POTA columnds to database to reflect latest ADIF v3.1.x spec changes
 * See http://www.adif.org.uk/313/ADIF_313_annotated.htm
 * 
 */

class Migration_add_pota_columns extends CI_Migration {

        public function up()
        {
                if (!$this->db->field_exists('COL_POTA_REF', 'TABLE_HRD_CONTACTS_V01')) {
                        $fields = array(
                                'COL_POTA_REF VARCHAR(30) DEFAULT NULL',
                                'COL_MY_POTA_REF VARCHAR(50) DEFAULT NULL',
                        );
                        $this->dbforge->add_column('TABLE_HRD_CONTACTS_V01', $fields, 'COL_VUCC_GRIDS');

                        // Now copy over data from SIG_INFO fields and remove COL_SIG and COL_SIG_INFO only if COL_SIG is POTA
                        // This cannot be reverted on downgrade to prevent overwriting of other COL_SIG information
                        $this->db->set('COL_POTA_REF', 'COL_SIG_INFO', FALSE);
                        $this->db->set('COL_SIG_INFO', '');
                        $this->db->set('COL_SIG', '');
                        $this->db->where('COL_SIG', 'POTA');
                        $this->db->update('TABLE_HRD_CONTACTS_V01');

                }
                if (!$this->db->field_exists('station_pota', 'station_profile')) {
                        // Add MY_POTA_REF to station profile
                        $fields = array(
                                'station_pota varchar(50) DEFAULT NULL',
                        );
                        $this->dbforge->add_column('station_profile', $fields);
                }
                if (!$this->db->field_exists('pota', 'bandxuser')) {
                        $fields = array(
                                'pota' => array(
                                        'type' => 'INT',
                                        'constraint' => 20,
                                        'unsigned' => TRUE,
                                ),
                        );
                        $this->dbforge->add_column('bandxuser', $fields);
                        $this->db->query("update bandxuser set pota = 1");
                }
        }

        public function down()
        {
                if ($this->db->field_exists('COL_POTA_REF', 'TABLE_HRD_CONTACTS_V01')) {
                        $this->dbforge->drop_column('TABLE_HRD_CONTACTS_V01', 'COL_POTA_REF');
                }
                if ($this->db->field_exists('COL_MY_POTA_REF', 'TABLE_HRD_CONTACTS_V01')) {
                        $this->dbforge->drop_column('TABLE_HRD_CONTACTS_V01', 'COL_MY_POTA_REF');
                }
                if ($this->db->field_exists('station_pota', 'station_profile')) {
                        $this->dbforge->drop_column('station_profile', 'station_pota');
                }
                if ($this->db->field_exists('pota', 'bandxuser')) {
                        $this->dbforge->drop_column('bandxuser', 'pota');
                }
        }
}
