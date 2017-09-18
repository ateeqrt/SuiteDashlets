<?php
function populateUserArray(){
	$sql = "SELECT id, name, email, facebook, twitter, generic, google, linkedin,
	 fflag, tflag, gflag, gpflag, lflag FROM rt_cxm_email ORDER BY ";
?>