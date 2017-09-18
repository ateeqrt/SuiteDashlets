<?php

//Loop through the groupings to find grouping file you want to append to

foreach ($js_groupings as $key => $groupings) {
    foreach ($groupings as $file => $target) {
        //if the target grouping is found
        if ($target == 'include/javascript/sugar_grp1.js') {
            //append the custom JavaScript file
            $js_groupings[$key]['custom/JavaScript/customFile.js'] = 'include/javascript/sugar_grp1.js';
        }
        break;
    }
}
