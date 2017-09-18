<?php
// created: 2016-04-21 08:37:11
$dictionary["Contact"]["fields"]["rt_cxm_email_contacts_1"] = array(
    'name' => 'rt_cxm_email_contacts_1',
    'type' => 'link',
    'relationship' => 'rt_cxm_email_contacts_1',
    'source' => 'non-db',
    'module' => 'rt_cxm_email',
    'bean_name' => 'rt_cxm_email',
    'side' => 'right',
    'vname' => 'Contacts',
    'id_name' => 'rt_cxm_email_contacts_1rt_cxm_email_ida',
    'link-type' => 'one',
);
$dictionary["Contact"]["fields"]["rt_cxm_email_contacts_1_name"] = array(
    'name' => 'rt_cxm_email_contacts_1_name',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'cxm_emails',
    'save' => true,
    'id_name' => 'rt_cxm_email_contacts_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_contacts_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'name',
);
$dictionary["Contact"]["fields"]["rt_cxm_email_contacts_1rt_cxm_email_ida"] = array(
    'name' => 'rt_cxm_email_contacts_1rt_cxm_email_ida',
    'type' => 'id',
    'source' => 'non-db',
    'vname' => 'cxm_emails ID',
    'id_name' => 'rt_cxm_email_contacts_1rt_cxm_email_ida',
    'link' => 'rt_cxm_email_contacts_1',
    'table' => 'rt_cxm_email',
    'module' => 'rt_cxm_email',
    'rname' => 'id',
    'reportable' => false,
    'side' => 'right',
    'massupdate' => false,
    'duplicate_merge' => 'disabled',
    'hideacl' => true,
);
