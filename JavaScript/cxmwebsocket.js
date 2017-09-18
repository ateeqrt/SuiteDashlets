var mod = 'rt_Tracker';
var modLang;
var user;
window.getModLang = function(){
	$.ajax({
        type: "GET",
        dataType: 'json',
        crossDomain: false,
        timeout: 5000, 
        url: "index.php?module=rt_Tracker&action=functions&method=getModLang&sugar_body_only=true",

        success: function (result) {
			if (typeof result == 'string')
		    	modLang = JSON.parse(result);
        },
        error: function(e){
	        console.log(e);
	        window.handleDashletCallError(e);
        }
    }); 

}
window.getZFID = function(){
	$.ajax({
        type: "GET",
        dataType: 'json',
        crossDomain: false,
        timeout: 5000, 
        url: "index.php?module=rt_Tracker&action=functions&method=getZFID&sugar_body_only=true",

        success: function (result) {
			if (typeof result == 'string')
		    	window.zfid = result;
        },
        error: function(e){
	        console.log(e);
	        window.handleDashletCallError(e);
	        setTimeout(function() {
	            getZFID();
	    }, 6000);
        }
    }); 

}
window.getUser = function(){

	$.ajax({
        type: "GET",
        dataType: 'json',
        crossDomain: false,
        timeout: 5000, 
        url: "index.php?module=rt_Tracker&action=functions&method=getUser&sugar_body_only=true",

        success: function (result) {
			if (typeof result == 'string')
		    	user = JSON.parse(result);
        },
        error: function(e){
	        console.log(e);
	        window.handleDashletCallError(e);
        }
    }); 

}
window.websocket = function (interval) {
	window.getModLang();
	window.getUser();
	window.getZFID();
	if (typeof window.zfid == 'undefined' || window.zfid == null) {
		if (typeof window.notFound == 'undefined') {
			console.log("zfid is undefined");
			window.storeZFID();
			if (typeof interval == 'undefined')
				interval = 1;
			var ms = 1000 * interval;
			console.log("calling again in " + (ms) + ' m-seconds');
			setTimeout(function () {
				window.websocket(interval + 1);
			}, ms);
		}
	} else {
	
		if ("WebSocket" in window) {
			console.log("WebSocket is supported by your Browser!");

			// Let us open a web socket
			var ws = new WebSocket("wss://rtcxmxeon.rolustech.com:1114");

			ws.onopen = function () {
				// Web Socket is connected, send data using send()
				window.storeZFID();
				console.log("Connecting...");

				var msg = "S-" + window.zfid;
				console.log(msg);
				ws.send(msg);
				console.log("Connected...");
			};

			ws.onmessage = function (evt) {
				var received_msg = evt.data;
				var page_c = '', status_c = '';
				msg = JSON.parse(received_msg);
				//Replace special characterSet with whiteSpace
				spt = msg.message.split('||');
				var go = true;
				if (spt.length > 1) {
					msg.message = spt[1];
					if (user['id'] != spt[0])
						go = false;
				}
				msg.message = msg.message.replace(/\+%/g, ' ');
				msg.page_c = msg.page_c.replace(/\+%/g, ' ');
				msg.name = msg.name.replace('-', ' ');
				if ($('.no-data-notif').length > 0) {
					$('.no-data-notif').hide();
				}
				if (msg.type == 'status') {
					// CHECK IF THIS AN UNKNOWN USER, AND IS A REGULAR AND ARE THERE ANY SPECIAL INTEREST?
					$.ajax({
					    type: "POST",
					    dataType: 'json',
					    crossDomain: false,
					    timeout: 5000, 
					    url: "index.php?module=rt_Tracker&action=functions&method=recurUser&cookie_id=msg.sfrom&sugar_body_only=true",

					   	success: function (result) {
						if (typeof data == 'string')
							data = JSON.parse(data);
						status_c = data.status_c;
						page_c = data.page_c;
						if (data.notify == '' || data.notify === user['id']) {
							if (data.visits > 2) {
								$.notify(modLang['LBL_SUGGEST_CHAT1'] + "'" + msg.name + "'. " + modLang['LBL_SUGGEST_CHAT2'] + "'" + data.potential_interest + "'", {
									position: "top right",
									autoHideDelay: 10000,
									style: 'rtcxmchatstyle'
								});
							}
							else if (data.returning_user)
								$.notify(modLang['LBL_RET_USER'], {
									position: "top right",
									style: 'rtcxmchatstyle'
								});
						}
						if (msg.message == 'online') {
							msg.message = '<i class="fa fa-circle" style="color: green;"></i>';
						}
					    },
					    error: function(e){
					        console.log(e);
					        window.handleDashletCallError(e);
					    }
					}); 


				}
				else {
					var arr = msg.message.split(':::');
					if (arr.length > 1)
						msg.message = arr[1];
					var mesg = msg.message;
					//Notifications Table Entry
					if (msg.message.length > 8)
						mesg = msg.message.substr(0, 8) + '...';

					//Chat Thread
					if ($('#nconvo').is(':visible')) {
						if ($('.heading').attr('id')) {
							var id = $('.heading').attr('id');
							var content = '';
							if (msg.sfrom == id) {
								var d = new Date();
								var time = d.toLocaleTimeString().replace(/:\d+ /, ' ');
								var dt2 = d.toLocaleDateString();
								var dt1 = '';
								if ($('.heading2').length > 0)
									dt1 = $('.heading2')[$('.heading2').length - 1].firstChild.innerText;
								if (dt2 > dt1) {
									content += '<div class="heading2">';
									content += '<div class="convn-info">' + dt2 + '</div></div>';
								}
								//create chat and display it.
								content += '<div class="krm">' + msg.name + ' :';
								content += '<div class="content">' + msg.message + '</div>';
								content += '<div class="message-time">' + time + '</div>';
								content += '</div>';
								$('.conversation').append(content);

								if ($('#cxm-end-chat').hasClass('disable')) {
									$('#cxm-end-chat').removeClass('disable');
								}

								$('.messages').notify({title: '<p>' + modLang['LBL_1_NM'] + '</p>'}, {
									style: 'newmessage',
									autoHide: false,
									position: "top left"
								});
							}
						}
					} else {
						$.notify(modLang['LBL_NEW_MESSAGE'] + msg.name, {
							position: "top right",
							style: 'rtcxmchatstyle'
						});
					}
				}
				//UPDATE THE NOTIFICATION TABLE EITHER CASE

				console.log(msg);
			};

			ws.onclose = function () {
				// websocket is closed.
				console.log("Connection is closed...");
				console.log("Retrying to connect in 60 seconds.");
				setTimeout(function () {
					websocket();
				}, 60000);
			};
		}

		else {
			// The browser doesn't support WebSocket
			console.log("WebSocket NOT supported by your Browser!");
		}
	}
}