<?php
// created: 2016-03-15 11:32:27
$dictionary["Lead"]["fields"]["rt_cxm_email_leads_1"] = array(
    'name' => 'rt_cxm_email_leads_1',
    'type' => 'link',
    'relationship' => 'rt_cxm_email_leads_1',
    'source' => 'non-db',
    'module' => 'rt_cxm_email',
    'bean_name' => 'rt_cxm_email',
    'side' => 'right',
    'vname' => 'Leads',
    'id_name' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
    'link-type' => 'one',
);
$dictionary["Lead"]["fields"]["rt_cxm_email_leads_1_name"] = array(
    'name' => 'rt_cxm_email_leads_1_name',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'cxm_emails',
    'save' => true,
    'id_name' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_leads_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'name',
);
$dictionary["Lead"]["fields"]["rt_cxm_email_leads_1rt_cxm_email_ida"] = array(
    'name' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
    'type' => 'id',
    'source' => 'non-db',
    'vname' => 'cxm_emails ID',
    'id_name' => 'rt_cxm_email_leads_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_leads_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'id',
    'reportable' => false,
    'side' => 'right',
    'massupdate' => false,
    'duplicate_merge' => 'disabled',
    'hideacl' => true,
);
