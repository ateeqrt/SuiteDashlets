<?php
// created: 2016-03-15 11:32:26
$dictionary["rt_cxm_email_leads_1"] = array(
    'true_relationship_type' => 'one-to-many',
    'from_studio' => true,
    'relationships' =>
        array(
            'rt_cxm_email_leads_1' =>
                array(
                    'lhs_module' => 'rt_cxm_email',
                    'lhs_table' => 'rt_cxm_email',
                    'lhs_key' => 'id',
                    'rhs_module' => 'Leads',
                    'rhs_table' => 'leads',
                    'rhs_key' => 'id',
                    'relationship_type' => 'many-to-many',
                    'join_table' => 'rt_cxm_email_leads_1_c',
                    'join_key_lhs' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
                    'join_key_rhs' => 'rt_cxm_email_leads_1leads_idb',
                ),
        ),
    'table' => 'rt_cxm_email_leads_1_c',
    'fields' =>
        array(
            0 =>
                array(
                    'name' => 'id',
                    'type' => 'varchar',
                    'len' => 36,
                ),
            1 =>
                array(
                    'name' => 'date_modified',
                    'type' => 'datetime',
                ),
            2 =>
                array(
                    'name' => 'deleted',
                    'type' => 'bool',
                    'len' => '1',
                    'default' => '0',
                    'required' => true,
                ),
            3 =>
                array(
                    'name' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
                    'type' => 'varchar',
                    'len' => 36,
                ),
            4 =>
                array(
                    'name' => 'rt_cxm_email_leads_1leads_idb',
                    'type' => 'varchar',
                    'len' => 36,
                ),
        ),
    'indices' =>
        array(
            0 =>
                array(
                    'name' => 'rt_cxm_email_leads_1spk',
                    'type' => 'primary',
                    'fields' =>
                        array(
                            0 => 'id',
                        ),
                ),
            1 =>
                array(
                    'name' => 'rt_cxm_email_leads_1_ida1',
                    'type' => 'index',
                    'fields' =>
                        array(
                            0 => 'rt_cxm_email_leads_1rt_cxm_email_ida',
                        ),
                ),
            2 =>
                array(
                    'name' => 'rt_cxm_email_leads_1_alt',
                    'type' => 'alternate_key',
                    'fields' =>
                        array(
                            0 => 'rt_cxm_email_leads_1leads_idb',
                        ),
                ),
        ),
);