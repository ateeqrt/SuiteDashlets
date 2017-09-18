<?php
//  Append new View to Footer layout's list of components
$viewdefs['base']['layout']['footer']['components'] = array(
    'type' => 'simple',
    array(
        'view' => 'footer-actions',
    ),
    array(
        'view' => 'cxm-chat-action',
    ),
);