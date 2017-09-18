<?php
function populateUserArray(){

    $sql = "SELECT id, name, email, facebook, twitter, generic, google, linkedin,
    fflag, tflag, gflag, gpflag, lflag FROM rt_cxm_email ORDER BY date_modified DESC";
    $result = $GLOBALS['db']->query($sql);
    $user = array();
	if ($result->num_rows > 0) {
		$i = 0;
	    while ($row = $db->fetchByAssoc($result)) {
	    	$user[$i]['id'] 		= $row['id'];
	    	$user[$i]['name'] 		= $row['name'];
	    	$user[$i]['email'] 		= $row['email'];

	    	$user[$i]['socialFlags'] = array(
		    	$row['fflag'],
		    	$row['tflag'],
		    	$row['gflag'],
		    	$row['gpflag'],
		    	$row['lflag'],
	    	);

	    	$user[$i]['socialList'] = array(
		    	$row['facebook'],
		    	$row['twitter'],
		    	$row['generic'],
		    	$row['google'],
		    	$row['linkedin'],
	    	);

	    	$i++;
	    }
	}
	return $user;
}
?>