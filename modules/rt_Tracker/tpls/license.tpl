{literal}
<style>
	.license_key{
	    width: 30%;
	    margin-right: 2%;
	    margin-bottom: 2%;
	    float: left;
	}

	.notification {
	    width:20%;
	    margin-right:2%;
	    margin-bottom:2%;
	    float: left;
	}

	.hidden-message {
		background-color:transparent;
		color: transparent;
		border-color:
		transparent;
	}
	div.customMainPane
	{
		padding-top: 5%;
	}

	.bold_users {
		font-weight: bold;
	}
	.boost{
		width: 135px !important;
		height: inherit !important;
	}
	.custom_width{
		width: 95% !important;
	}
	.configureUsers{
		height: 300px;
		width: 507px;
	}
	.configureUsers th{
		border: 1px solid;
	}
	.configureUsers td{
		vertical-align: top;
		border: 1px solid;
	}
	#disabled, #enabled {
		float: left;
		list-style-type: none;
		margin: 0 10px 0 0;
		padding: 0 0 2.5em;
		height: 300px;
		width: 100%;
		min-width: 160px !important;
		overflow: scroll;
	}
	.connectedSortable {
		min-height: 50px;
		min-width: 50px;
		font-weight: normal !important;
	}
	#disabled div, #enabled div {
		
		margin: 5px 0px -4px 5px;
		padding: 5px;
		width: 86%;
	}
	.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
		border: 1px solid #D3D3D3;
		color: #555555;
		font-weight: normal;
	}
	.do-not-drag-drop{
		background: repeat-x scroll 50% 50% #E6E6E6;
		border: 1px solid #D3D3D3;
		color: #555555;
		font-weight: bold;
	}
	.divider{
    width:2%;
    height:auto;
    display:inline-block;
}

</style>
<script type="text/javascript">
	var enabled_elements = [];
	var disabled_elements = [];
</script>
{/literal}
<script src="include/javascript/jquery/jquery-min.js"></script>
<script src="include/javascript/jquery/jquery-ui-min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script src="include/javascript/jquery/jquery.cookie.js" type="text/javascript"></script>
<script src="http://yui.yahooapis.com/3.2.0/build/yui/yui-min.js"></script> 

<div>
	<div class="headerpane">
		<h1>
		<div class="record-cell">
			<span class="module-title">{$title}</span>
		</div>
		<div class="btn-toolbar pull-right">
			<span sfuuid="123" class="edit">
			<a class="btn btn-primary"  href="javascript:validation_processing()" name="validate_button id="validate_button">
				{$MOD.LBL_VALIDATE_LABEL}
			</a>
			<a class="btn btn-primary hide" href="javascript:void(0);" name="continue_button" id="continue_button">
				{$MOD.LBL_CONTINUE_LABEL}
			</a>
			<a class="btn btn-primary hide" href="javascript:enable_all_users();" name="boost_button" id="boost_button">
				{$MOD.LBL_BOOST_LABEL}
			</a>
			</span>
		</div>
		</h1>
	</div>
	<!--headerpane ends-->

	<div class="record">
		<div class="row-fluid panel_body">
			<div class="span4 record-cell" data-type="text" data-name="license_key_steps">
				<p>{$MOD.LBL_STEPS_TO_LOCATE_KEY_TITLE}</p>
				<div class="record-label" data-name="">
					{$MOD.LBL_STEPS_TO_LOCATE_KEY1}
				</div>
				<div class="record-label" data-name="">
					 {$MOD.LBL_STEPS_TO_LOCATE_KEY2}
				</div>
				<div class="record-label" data-name="">
					 {$MOD.LBL_STEPS_TO_LOCATE_KEY3}
				</div>
				<div class="record-label" data-name="">
					 {$MOD.LBL_STEPS_TO_LOCATE_KEY4}
				</div>
				<div class="record-label" data-name="">
					 {$MOD.LBL_STEPS_TO_LOCATE_KEY5}
				</div>
			</div>
		</div>
	</div>
	<br/>
	<!--record ends-->

	<div class="row-fluid panel_body">
		<div class="span4 record-cell hide" id="users_count" data-type="text" data-name="users_count">
			<div class="record-label " id="salesmap_users"></div>
			<div class="record-label " id="licensed_users"></div>
		</div>
	</div>

	<div class="row-fluid panel_body">
		<div class="span4 record-cell" data-type="text" data-name="license_key">
			<div class="record-label" data-name="license_key">{$MOD.LBL_LICENSE_KEY} </div>
			<span class="normal index" data-fieldname="license_key" data-index="">
				<span sfuuid="317" class="edit">
					<input type="text" name="license_key" id="license_key" value="{$license_key}" maxlength="100" class="license_key inherit-width" style="height: inherit" placeholder="{$MOD.LBL_LICENSE_KEY}" />
					<span  class="notification" id="notification" name="notification" style="color:orange;"></span>
					<div class="hidden-message"> message </div>

				</span>
			</span>
		</div>
	</div>
	<div class="row-fluid panel_body"><br/></div>
</div>


{if isRepaired == true}
	{if isValidated == true}
<div id="users_edit" class = 'hidden'>
	<div class="headerpane">
		<h1>
			<div class="record-cell">
				<span class="module-title">{$title}</span>
			</div>
			<div class="btn-toolbar pull-right">
				<span sfuuid="900" class="edit">

					<a class="btn btn-primary" href="javascript:handleCancel();" name="cancel_button" id="cancel_button">
						{$MOD.LBL_CANCEL_BUTTON_LABEL}
					</a>
    				<div class="divider"/>
					<a class="btn btn-primary" href="javascript:handleSave();" name="save_button" id="save_button" >
						{$MOD.LBL_SAVE_BUTTON_LABEL}
					</a>
			</span>
			</div>
		</h1>
		<br/>
			<div class="btn-toolbar pull-right hidden-message" >message</div>
			<span  id="notification2" name="notification2" style="color:orange; float: right"></span>

	</div>

	<div class="record">
	{if isAdmin == true}

		<div class="row-fluid panel_body">
			<div class="span4 record-cell" data-type="" data-name="">
				<span class="normal" data-fieldname="" data-index="">
					<span sfuuid="901" class="edit">
							<a class="btn btn-primary" href="javascript:enable_all_users();" name="boost_button" id="boost_button">
								Enable All
							</a>
					</span>
				</span>
			</div>

		</div>
		<div class="row-fluid panel_body">
			<div class="span6 record-cell" data-type="text" data-name="configureUsers">
				<table class="configureUsers"> 
				
				<tr>
				<th class="do-not-drag-drop">Sugar User(s)</th>
				<th class="do-not-drag-drop">Enabled Sugar Support Agents</th>
				</tr>
				<tr>

				<td>
				<ul id="disabled" class="connectedSortable">
				{section name=user loop=$active_users}
					<div id="{$active_users[user].id}" class="ui-state-default" onclick="enable_one(this)">
						{$active_users[user].name}
						<li id="{$active_users[user].name}" class="hidden">{$active_users[user].id}</li>
					</div>
				{/section}
				</ul>
				</td>
				<td>
				<ul id="enabled" class="connectedSortable">
				{section name=user loop=$enabled_users}
					<div id="{$enabled_users[user].id}" class="ui-state-default" onclick="disable_one(this)">
						{$enabled_users[user].name}
						<li id="{$enabled_users[user].name}" class="hidden">{$enabled_users[user].id}</li>
					</div>
				{/section}
				</ul>
				</td>
				</tr>
				</table>

			</div>
			<div class="span4 record-cell" data-type="text" data-name="users_info">
				<span class="normal index" data-fieldname="users_info" data-index="">

					<span class="span4 normal index" data-name="boost_user_count" style="display: none;padding-top: 10px;">
						<span sfuuid="912" class="edit" >
							<input type="text" name="boost_user_count" id="boost_user_count"  value="{$enabled_active_users}" maxlength="100" class="boost" placeholder="Enter User count">
							<a class="btn btn-primary" href="javascript:enable_all_users();" name="boost_button" id="boost_button">
								{$MOD.LBL_BOOST_LABEL}
							</a>
						</span>
					</span>
				</span>

			</div>
		</div>


	</div>
</div>
				{else}
				<div class='headerpane'>
		<h1>
		<p class="record-cell">
		Do <a href='#rt_Tracker/layout/license' target='_blank'><span class='alert-danger' >Validate License Key</span></a>
		</p>
		</h1>
		</div>	
				{/if}
				</div>
			</div>
			{else}
			<div class='headerpane'>
			<h1>
			<p class="record-cell">
			Do <a href='#rt_Tracker/layout/license' target='_blank'><span class='alert-danger' >Validate </span></a>first
			</p>
			</h1>
			</div>
		{/if}

	
{/if}
{literal}
<script type="text/javascript">
	var licenseKey = '';
	var license_validator= false;
	var rt_Tracker_key = '';
	var title;
	var	loaded;
	var repaired;
	var validated;
	var active_users;
	var enabled_active_users;
	var licensed_user_count;
	var showBoostButton;
	var license_key;
	var continueURL;
	var users_mulitiselect;
	var selected = false;
	var select2Onchange = true;
	var userConfig = false;
	var className = 'customMainPane';
	var license_key;
	var update = false; //true if previously license is saved
	var continueURL;
	var user_count;
	var licensed_user_count;

	var uobjeCt = {};
	uobjeCt['active'] = {};
	uobjeCt['enabled'] = {};
	uobjeCt['disabled'] = {};

function validation_processing() {
    if ($('input[name=license_key]').val().trim() == "") {
        $("#notification").notify("Key should not be empty", "error");
    }
    else {
		$('#notification').notify("validating license key", "info");
        rt_Tracker_key = '594d6d25c592dc91fcc69ccafaa1cc0c';
        var user_key = $("#license_key").val().trim();
        licenseKey = user_key;
        $.ajax({
	        type: "GET",
	        dataType: 'jsonp',
            crossDomain: true,
      		timeout: 5000, //work around for jsonp not returning errors
	        url: "https://www.sugaroutfitters.com/api/v1/key/validate",
	        data: {

                format: 'jsonp',
                public_key: rt_Tracker_key,
                key: licenseKey	        	
	        },
	        success: function (result) {
		       	$('#users_edit').removeClass('hidden');
	        	$('#validate_button').addClass('hidden');
	            $("#validate_button").addClass('hide');
	            $("#license_validator").val(true);
	            validateLicenseSuccess(result,licenseKey);
	        },
	        error: function(e){
            $("#license_validator").val(false);

	        }
		});

    }
}

function validateLicenseSuccess(response,licenseKey) {
    $("#notification").css({
        color: "LightGreen"
    });
	$('#notification').notify("validation successful", "success");
    var msg = {};
    if (response) {
            if (response.validated && response.validated == true) {
                if (response.validated_users && response.validated_users == true) {
                    user_count = response.user_count;
                    licensed_user_count = response.licensed_user_count;

                    userConfig = true;
                    getUserConfig();
                }
                else {
                    user_count = response.user_count;
                    licensed_user_count = response.licensed_user_count;

                }
                $('#validate_button').hide();

                $('#licensed_users').html( function(){
                	 "{$MOD.LBL_LICENSED_USERS}"  + ': ' + licensed_user_count });
                $('#users_count').removeClass('hide');
            }

        else {
            msg = {
                autoClose: false,
                level: 'error',
                messages: 'Unkown error'
            };
        }
    }
    else {
        msg = {
            autoClose: false,
            level: 'error',
            messages: 'No response received from server.'
        };
    }
	YAHOO.util.Connect.asyncRequest(
		'POST',
		'index.php',
		{
			//'success':window.alert("about to go to that action"),
			'failure':null
		},
		'module=rt_Tracker&action=functions&method=validate&key='+licenseKey+'&sugar_body_only=true'
		);
}

function getUserConfig() {
		/*YAHOO.util.Connect.asyncRequest(
		'GET',
		'index.php',
		{
			//'success':window.alert("about to go to that action"),
			'failure':null
		},
		'module=rt_Tracker&action=get_users&sugar_body_only=true'
		);*/
		$.ajax({
	        type: "GET",
	        dataType: 'json',
            crossDomain: false,
      		timeout: 5000, 
	        url: "index.php?module=rt_Tracker&action=functions&method=getUserConfig&sugar_body_only=true",
	        success: function (result) { 
        		getUserConfigSuccess(result);
	        },
	        error: function(e){
		        console.log(e);
	        	configError(e);
	        }
		});

}

function getUserConfigSuccess(response) {
        if (response ) {
            if (response.data.isRepaired) {
                repaired  = response.data.isRepaired;
                if (response.data.isValidated) {
                    isvalidated  = response.data.isValidated;
                } else {
                    // app.alert.show('rtCXM_config', {autoClose: false, level: 'error',messages:'Do validate first'});
                }
            }
            }
            if (response.data.active_users) {
                active_users  = Object.keys(response.data.active_users).length;
                uobjeCt['active']  = response.data.active_users;
            }
            if (response.data.enabled_active_users) {
                enabled_active_users  = Object.keys(response.data.enabled_active_users).length;
                uobjeCt['enabled']  = response.data.enabled_active_users;
            }
            if (response.data.licensed_user_count) {
                licensed_user_count  = response.data.licensed_user_count;
            }
            if (response.data.license_key) {
                license_key  = response.data.license_key;
            }
            if (response.data.select2Onchange) {
                select2Onchange  = response.data.select2Onchange;
            }
        loaded  = true;

}
function configError(error) {
	console.log(error);
        var msg  = {autoClose: false, level: 'error'};
        if (error && (typeof error.message == 'string')) {
            msg.messages  = error.message;
        }
        if (error.status  == 412 && !error.request.metadataRetry) {
            msg.messages  = 'If this page does not reload automatically, please try to reload manually';
        } else {
        }
        if (error && (typeof error.message == 'string')) {
            msg.messages  = error.message;
        }
        if (typeof error.status != 'undefined') {
            if (error.status  == 400) {
                if (typeof error.responseText != 'undefined') {
                    var msg  = {autoClose: false, level: 'error'};
                    msg.messages  = error.responseText;
                }
            }
        }
    }
 function handleCancel(){
 	location.reload();
 }
 function handleSave()
    {
    	if(selected == true){
	        var selections  = $('#enabled div');
	        var selectedUserIDS = [];

			selections.each(function(idx, div) {
				var text = $(div).clone().children().text();
	    			selectedUserIDS.push(text.trim().replace(/\s+/g, ' '));
			});


	        console.log(selectedUserIDS);
			YAHOO.util.Connect.asyncRequest(
			'POST',
			'index.php',
			{
				'success':$("#notification2").notify("Changes have been saved", "success"),
				'failure':null//$("#notification2").notify("Changes not saved!", "error"),
			},
			'module=rt_Tracker&action=functions&method=setUserConfig&selectedUserIDS='+ window.btoa(JSON.stringify(selectedUserIDS))+'&sugar_body_only=true'
			);
			selected = false;
		}
		else{
			$("#notification2").notify("No changes has been made!");
		}
    }
    function enable_one(el){
        console.log(el.innerText);
		var li_id = '<li id=' + el.innerText + ' class="hidden">' + el.id + '</li>' + ' </div>';
		var div_name = '<div id=' + el.id + ' class="ui-state-default" onclick="disable_one(this)"> ' + el.innerText;
		var ul_main = div_name + li_id;
        $('#enabled').append(ul_main);
        $(el).detach();
		//$('#disabled li').detach();
		selectionChanged();
	}
	function disable_one(el){
		var div_name = '<div id=' + el.id + ' class="ui-state-default" onclick="enable_one(this)"> ' + el.innerText;
		var li_id = '<li id=' + el.innerText + ' class="hidden">' + el.id + '</li>' + ' </div>';
		var ul_main = div_name + li_id ;
        $('#disabled').append(ul_main);
        $(el).detach();
        var selector1 = "\'" + el.innerText + "\'";
        selector1 = "\"" + "[id="+selector1+"]" + "\"";
        console.log(selector1);
		//$(selector1).whatever().detach();
		selectionChanged();
	}
    function enable_all_users() {
        var newUsers_div = $('#disabled div');
        for(var i = 0; i < newUsers_div.length; i++){
        	enable_one(newUsers_div[i]);
		}
    }
    function selectionChanged() {
    		selected = true;
        var enabled_active_users  = $('#enabled div').length;
        $('#enabled_active_users').html(enabled_active_users);
        $('#boost_user_count').val(enabled_active_users);
        if (isNumber(enabled_active_users) && enabled_active_users > licensed_user_count) {
            showBoostButton  = true;
            $("span[data-name = boost_user_count]").show();
        }
        else {
            showBoostButton  = false;
            $("span[data-name = boost_user_count]").hide();
        }

    }

    function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }

</script>
{/literal}