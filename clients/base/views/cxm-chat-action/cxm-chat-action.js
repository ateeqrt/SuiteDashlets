({
    events: {
        //On click of our "button" element
        'click [data-action=chat]': 'chat',
        'click [data-action=min]': 'min',
        'click [data-action=start_chat]': 'start_chat',
        'click [data-action=reset_main]': 'reset_main',
        'click [data-action=notifier]': 'notifier',
        'click [data-action=end-chat]': 'endChat',
		'click [data-action=toggle_active_requests]':'toggleActiveRequests',
    },
    tagName: "span",
    rt_note_c: [],
    content: '',
    cxm_cid: '',
    lock: false,
    username: '',
    refreshflag: false,
    isActive: false,
    session_id: '',
    report: '',
    s_status: 'open',
    renderflag: false,
    isValid: false,
    langModule: 'rt_Tracker',
    LBL_CXM_CHAT: 'RtCXM Chat',
    session_time: 0,
    chat: function () {
        this.setCxmLabels();
        $('.modaln2').removeClass('hide');
        if ($('#popupc').hasClass('hide')) {
            this._startAutoRefresh();
            var that = this;
            this.validateCXMUserforDashlet();
        }
        else {
            this.min();
        }
    },
    setCxmLabels: function () {
        this.LBL_NOTIFICATIONS = app.lang.get('LBL_NOTIFICATIONS', this.langModule);
        this.LBL_TRACK_NAME = app.lang.get('LBL_TRACK_NAME', this.langModule);
        this.LBL_MESSAGE = app.lang.get('LBL_MESSAGE', this.langModule);
        this.LBL_ACCEPT_REQUESTS = app.lang.get('LBL_ACCEPT_REQUESTS', this.langModule);
        this.LBL_CXM_CHAT = app.lang.get('LBL_CXM_CHAT', this.langModule);
        this.LBL_MESSAGE = app.lang.get('LBL_MESSAGE', this.langModule);
        this.LBL_NO_NOTIFICATIONS = app.lang.get('LBL_NO_NOTIFICATIONS', this.langModule);
        this.LBL_START_CHAT = app.lang.get('LBL_START_CHAT', this.langModule);
        this.LBL_VIEW_CHAT = app.lang.get('LBL_VIEW_CHAT', this.langModule);
        this.LBL_CXM_NOT_VALID = app.lang.get('LBL_CXM_NOT_VALID', this.langModule);
        this.LBL_CHAT_LOCKED = app.lang.get('LBL_CHAT_LOCKED', this.langModule);
        this.LBL_CHAT_ASSIGNED_TO = app.lang.get('LBL_CHAT_ASSIGNED_TO', this.langModule);
        this.LBL_CXM_NOT_AUTH = app.lang.get('LBL_CXM_NOT_AUTH', this.langModule);
        this.LBL_SEND = app.lang.get('LBL_SEND', this.langModule);
        this.LBL_USE = app.lang.get('LBL_USE', this.langModule);
        this.LBL_SENDING = app.lang.get('LBL_SENDING', this.langModule);
        this.LBL_SENT = app.lang.get('LBL_SENT', this.langModule);
        this.LBL_CLIENT_NF = app.lang.get('LBL_CLIENT_NF', this.langModule);
        this.LBL_MSG_NS = app.lang.get('LBL_MSG_NS', this.langModule);
        this.LBL_END_CHAT = app.lang.get('LBL_END_CHAT', this.langModule);
        this.LBL_SESSION_REPORT = app.lang.get('LBL_SESSION_REPORT', this.langModule);
    },
    validateCXMUserforDashlet: function () {
        var that = this;
        var isValid = window.rtvalidatecxm;
        if (isValid) {
            this.isValid = true;
        } else {
            this.isValid = false;
            this.rt_note_c = [];
        }
        var self = this;
        if (typeof isValid == "undefined")
            setTimeout(function () {
                self._initiateBinding();
            }, 100);
        else
            self._initiateBinding();
    },
    _initiateBinding: function () {
        var that = this;
        if (App.user.id) {
            var user = App.data.createBean('Users', {'id': App.user.id});
            user.fetch().xhr.done(function (model) {
                if (model.active_requests == '1') {
                    that.isActive = true;
                }
                if (that.isValid)
                    that._beforebind();
                else
                    that.afterRenderTable();
            });
        }
    },
    _beforebind: function () {
        if (this.session_time > 0) {
            this.nbinding();
            return;
        }
        var that = this;
        if (!window.zfid)
            window.storeZFID();
        if (!window.zfid) {
            var Promise = $.Deferred();
            var arr = [];
            Promise.resolve(arr);
        } else {
            var Promise = jQuery.ajax({
                url: 'https://rtcxmneon.rolustech.com/customerService/getSessionTime',
                type: 'GET',
                crossDomain: true,
                dataType: 'json',
                data: {zfid: window.zfid}
            });
        }
        Promise.done(function (data) {
            if (typeof data == 'string' && data.trim() != '')
                data = JSON.parse(data);
            if (typeof data.session != 'undefined')
                that.session_time = data.session;
            else
                that.session_time = 1;
            that.nbinding();
        });
        Promise.fail(function (msg) {
            window.handleDashletCallError(msg);
            that.session_time = 1;
            that.nbinding();
        });
    },
    min: function () {
        this.render();
        $('#nconvo').text('')
        $('#popupc').removeClass('show');
        $('#popupc').addClass('hide');
        $('#popup1').addClass('hide');
        $('.modaln2').addClass('hide');
    },
    reset_main: function () {
        this.render();
        this.nbinding();
    },
    start_chat: function (evt) {
        var id = evt.currentTarget.attributes['id'].value;
        this.cxm_cid = id;
        var name = evt.currentTarget.attributes['name'].value;
        this.session_id = '';
        this.report = '';
        this.s_status = 'open';
        this.lock = false;
        this.recurv(id, name, -1, '');
    },
    notifier: function () {
        if (this.rt_note_c.length > 0) {
            $('.headerTable').show();
            $('#nrt-data-table').show();
            $('#nconvo').text('');
            $('#nconvo').hide();
        } else {
            if (!this.chat_ace) {
                var that = this;
                if (App.user.id) {
                    var user = App.data.createBean('Users', {'id': App.user.id});
                    user.fetch().xhr.done(function (model) {
                        if (model.active_requests == '1') {
                            that.isActive = true;
                        }
                        that.setCxmLabels();
                        that._beforebind();
                    });
                }
            } else
                this.nbinding();
        }
    },
    nbinding: function () {
        var that = this;
        that.rt_note_c = [];
        var prefsURL = App.api.buildURL('rtCXM/populateNotifications/', null, null, {
            oauth_token: App.api.getOAuthToken()
        });
        App.api.call('GET', prefsURL, null, {
            success: _.bind(function (data) {
                if (typeof data == 'string')
                    data = JSON.parse(data);
                var count = 0;
                _.each(data, function (note) {
                    // note.name == 'status' && ( )
                    if (!that.validDate(note.date_entered))
                        return;
                    var name = note.about_c.split(" ")[0];
                    var cid = note.about_c.split(" ")[2];
                    if (name != '' && name != ' ' && name != 'undefined') {
                        name = (name.split("-")).join(" ");
                        var facircle = '<i class="fa fa-circle" style="color: green;"></i>';
                        var msg = facircle + note.message_c;
                        if (note.message_c.length > 8)
                            msg = facircle + note.message_c.substr(0, 8) + '...';
                        if (note.message_c == 'online') {
                            msg = facircle;
                        }
                        var namelink = name;
                        if (note.link_id != '') {
                            namelink = '<a href="#' + note.link_module + '/' + note.link_id + '" class="link-x">' + name + '</a>';
                        }
                        that.rt_note_c[count] = {
                            c: count + 1,
                            id: note.id,
                            namelink: namelink,
                            cid: cid,
                            name: name,
                            message: msg,
                        };
                        if (note.status_c == 'Chat Started') {
                            that.rt_note_c[count].btn_value = that.LBL_VIEW_CHAT;
                        } else {
                            that.rt_note_c[count].btn_value = that.LBL_START_CHAT;
                        }
                        count++;
                    }
                }, that);
                that.afterRenderTable();
            }, this),

            error: _.bind(function (e) {
				if (typeof e.responseText != 'undefined' && !_.isEmpty(e.responseText)) {
					if (typeof e.responseText == 'string')
						e.responseText = JSON.parse(e.responseText);
					var er = e.responseText;
					e.responseText = (typeof er.msg != 'undefined') ? er.msg : er;
					that.isValid = (typeof er.isValid != 'undefined') ? er.isValid : that.isValid;
					that.afterRenderTable();
				}
                console.log(e);
                window.handleDashletCallError(e);
            }, this)
        });
    },
    afterRenderTable: function () {
        this.render();
        $('#popupc').removeClass('hide');
        $('#popup1').removeClass('hide');
        $('#popupc').addClass('show');
        $('.headerTable').show();
        $('#nconvo').hide();
        $('.modaln2').hide();
        if (this.rt_note_c.length > 0) {
            $('#nrt-data-table').show();
        } else {
            $('#nrt-data-table').hide();
			var nodatanotif = '<h5 class="no-data-notif">';
            if (!this.isValid)
                $(nodatanotif + this.LBL_CXM_NOT_VALID + '</h5>').insertAfter($('.headerTable'));
            else
                $(nodatanotif + this.LBL_NO_NOTIFICATIONS + '</h5>').insertAfter($('.headerTable'));
        }
        var len = this.rt_note_c.length;
        if (len > 10000)
            len = '10000+';
        $('span.rt_cxm_chat.badge').text(len);
    },
    validDate: function (notifTime) {

        var startDate = new Date(notifTime);
        var dt = new Date().toLocaleString('en-US', {timeZone: 'UTC'});
        var eDate = new Date(dt);
        var timeDiff = Math.abs(startDate - eDate);

        // var hh = Math.floor(timeDiff / 1000 / 60 / 60);
        var mm = Math.floor(timeDiff / 1000 / 60);

        // if(hh > 1)
        if (mm > this.session_time)
            return false;
        return true;
    },
    recurv: function (id, name, offset, dt) {
        var that = this;
        var filter = 'filter[0][client_no_c][$equals]=' + id;
        filter += '&order_by=date_entered:asc';
        filter += '&offset=' + offset;
        App.api.call('GET', App.api.buildURL('rt_cxm_chat/filter?' + filter), null, {
            success: function (data) {
                if (offset == -1) {
                    //HEADING
                    that.content = '';
                    that.content += '<div class="heading" id="' + that.cxm_cid + '">';
                    that.content += '<a class="btn btn-primary" id="cxm-end-chat" data-action="end-chat">' + that.LBL_END_CHAT + '</a>';
                    that.content += '<div class="convn-info">' + name + '</div></div>';
                    that.content += '<div class="conversation">';
                }
                if (data.records.length < 1) {
                    that.ending();
                    return;
                }
                _.each(data.records, function (chat) {
                    if ((chat.assigned_user_id) && chat.assigned_user_id !== App.user.id)
                        that.lock = true;
                    if (that.session_id != '' && that.session_id !== chat.session_id) {
                        that.content += '<div class="heading2">';
                        var rp = that.report.split(':');
                        that.content += '<div class="convn-info session_report ' + rp[0].toLowerCase() + '"';
                        that.content += '>' + that.LBL_SESSION_REPORT + ': ' + rp[1] + '</div></div>';
                    }
					var report = app.lang.get(chat.report, that.langModule);
                    that.report = report;
                    that.session_id = chat.session_id;
                    that.s_status = chat.session_status;
                    var d = new Date(chat.date_modified);
                    var time = d.toLocaleTimeString().replace(/:\d+ /, ' ');
                    var dt2 = d.toLocaleDateString();
                    if (dt != dt2) {
                        dt = dt2;
                        //TIME SEPARATION
                        that.content += '<div class="heading2">';
                        that.content += '<div class="convn-info">' + dt + '</div></div>';
                    }
                    if (chat.sender_c == 'visitor') {
                        //NAME
                        that.content += '<div class="krm">' + name + ' :';
                    } else {
                        //NAME
                        var user = chat.sender_c.split('user:')[1];
                        that.content += '<div class="luky">';
                        if (user.indexOf('RtCXMOperator2227') !== -1)
                            that.content += '<p style="color:#ff0006;font-weight:600;">RtCXM :</p>';
                        else {
                            that.username = user;
                            that.content += user + ' :';
                        }
                    }
                    //CONTENT
                    that.content += '<div class="content"><pre class="chatpre">' + chat.message_c + '</pre></div>';
                    //TIME
                    that.content += '<div class="message-time">' + time + '</div>';
                    that.content += '</div>';
                });
                if (data.next_offset == -1) {
                    that.ending();
                } else {
                    that.recurv(id, name, data.next_offset, dt);
                }
            }
        });
    },
    ending: function () {
        var that = this;
        var disableButton = false;
        if (this.s_status == 'CLOSED') {
            disableButton = true;
            that.content += '<div class="heading2">';
            var style = "background: #ecf0f1;border: 1px solid #ddd;";
            var rp = that.report.split(':');
            if (rp[0] == 'GOOD') {
                style += 'color:#008000;"';
            } else if (rp[0] == 'POOR') {
                style += 'color:#176de5;"';
            } else if (rp[0] == 'BAD') {
                style += 'color:#e61718;"';
            }
            that.content += '<div class="convn-info" style="' + style + '"';
            that.content += '>' + that.LBL_SESSION_REPORT + ': ' + rp[1] + '</div></div>';
        }
        that.content += '</div>';
        if (that.lock) {
            disableButton = true;
            that.content += '<div class="writer">';
            that.content += '<h5 style="text-align: -webkit-center;text-align:-moz-center;">';
            that.content += '<i class="fa fa-lock"></i> ' + that.LBL_CHAT_LOCKED + ' (' + that.LBL_CHAT_ASSIGNED_TO + that.username + ')';
            that.content += '</h5></div>';
        } else {
            that.content += '<div class="writer"><span class="span3 m3"><textarea class="messages autoExpand" rows="1" data-min-rows="1"></textarea></span>';
            that.content += '<span class="span1"><a class="btb btn btn-primary" style="width:37px;">' + that.LBL_SEND + '</a></span>';
            that.content += '</div>';
        }
        $('#nrt-data-table').hide();
        $('.no-data-notif').hide();
        $('.headerTable').hide();
        $('#nconvo').show();
        $('#nconvo').append(that.content);
        $('#popupc').removeClass('hide');
        $('#popup1').removeClass('hide');
        $('#popupc').addClass('show');
        $(".btb").unbind('click');
        $(".btb").button().click(function () {
            that.send_message_cxm();
        });
        if (disableButton) {
            $('#cxm-end-chat').addClass('disable');
            $('#cxm-end-chat').removeAttr('data-action');
        }
        var height = 0;
        $('.conversation div').each(function (i, val) {
            height += parseInt($(this).height());
        });
        height += '';
        $('.conversation').animate({scrollTop: height});
        $(".messages").keydown(function (event) {
            if (event.keyCode == 13 && !event.shiftKey) {
				event.preventDefault();
                $(".btb").click();
            }
        });
        if (!that.lock && $('.conversation').text().length < 1) {
            $('#cxm-end-chat').addClass('disable');
            $('#cxm-end-chat').removeAttr('data-action');
            var data = {cid: that.cxm_cid, name: $('.heading .convn-info').text(), uname: app.user.attributes.full_name};
            var prefsURL = App.api.buildURL('rtCXM/smartMessage/' + window.btoa(JSON.stringify(data)), null, null, {
                oauth_token: App.api.getOAuthToken()
            });
            App.api.call('GET', prefsURL, null, {
                success: _.bind(function (data) {
                    if (data) {
                        var h5 = $("<h5/>").append(data);

                        $(document).on('click', '.notifyjs-smtmsg-base .yes', function () {
                            //show button text
                            $('.messages').val(data);
                            //hide notification
                            $(this).trigger('notify-hide');
                            $('.messages').attr('rows', '3');
                            $('.conversation').css('height', '212px');
                            $('.btb').css('margin-top', '13px');
                        });


                        if ($('.writer').is(":visible")) {
                            $('.messages').notify({
                                title: '<p>' + data + '</p>',
                                button: that.LBL_USE
                            }, {style: 'smtmsg', autoHide: false, position: "top left"});
                        }
                    }
                }, this),

                error: _.bind(function (e) {
                    console.log(e);
                    window.handleDashletCallError(e);
                }, this)
            });
        }

        var lastScrollTop = 0;
        $('.conversation').scroll(function (event) {
            var st = $(this).scrollTop();
            if (st > lastScrollTop) {
                // downscroll code
                $('.notifyjs-wrapper').trigger('notify-hide');
            } else {
                // upscroll code
            }
            lastScrollTop = st;
        });

    },
    send_message_cxm: function (evt) {
        if ($('.messages').val().trim() == '')
            return;
        var first = false;
        if ($('.conversation').text().length < 1)
            first = true;
        var message = ($('.messages').val().split(" ")).join("+");
        var messagetosend = message + '-/*' + App.user.id + '-/*';
        $('.messages').val('');
        //Back to 1 row
        $('.messages').attr('rows', '1');
        $('.conversation').css('height', '241px');
        $('.btb').css('margin-top', '-3px');
        var that = this;
        var content = '';
        var promise = $.ajax({
            url: 'https://rtcxmneon.rolustech.com/customerService/sendMessage',
            data: {vsid: that.cxm_cid, message: messagetosend},
            type: 'GET',
        });
        app.alert.show('cxm-chat-alert', {
            level: 'info',
            messages: that.LBL_SENDING,
            autoClose: true
        });
        promise.done(function (data) {
            if (data == "message sent!") {
                app.alert.show('cxm-chat-alert', {
                    level: 'success',
                    messages: that.LBL_SENT,
                    autoClose: true
                });
                message = (message.split("+")).join(" ");
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
                content += '<div class="luky">' + App.user.attributes.user_name + ' :';
                content += '<div class="content"><pre class="chatpre">' + message + '</pre></div>';
                content += '<div class="message-time">' + time + '</div>';
                content += '</div>';
                $('.conversation').append(content);
                var height = 0;
                $('.conversation div').each(function (i, val) {
                    height += parseInt($(this).height());
                });
                height += '';
                $('.conversation').animate({scrollTop: height});
                if ($('#cxm-end-chat').hasClass('disable')) {
                    $('#cxm-end-chat').removeClass('disable');
                    $('#cxm-end-chat').attr('data-action', 'end-chat');
                }
                //CREATE CHAT RECORD
                var name = $($('.convn-info')[0]).text();
                var data = {
                    client_no_c: that.cxm_cid,
                    message_c: message,
                    name: name,
                    sender: App.user.attributes.user_name,
                    sender_id: App.user.id,
                    first: first
                };
                var prefsURL = App.api.buildURL('rtCXM/createChatMessage/' + window.btoa(JSON.stringify(data)), null, null, {
                    oauth_token: App.api.getOAuthToken()
                });
                App.api.call('POST', prefsURL, null, {
                    success: _.bind(function (records) {
                    }, this),
                    error: _.bind(function (e) {
                        console.log(e);
                        window.handleDashletCallError(e);
                    }, this)
                });
            } else if (data == "client not found") {
                app.alert.show('cxm-chat-alert', {
                    level: 'error',
                    messages: that.LBL_CLIENT_NF,
                    autoClose: false
                });
            }
        });
        promise.fail(function () {
            app.alert.show('cxm-chat-alert', {
                level: 'error',
                messages: that.LBL_MSG_NS,
                autoClose: true
            });
        });
    },
    _renderHtml: function () {
        this.isAuthenticated = app.api.isAuthenticated();
        app.view.View.prototype._renderHtml.call(this);
    },
    render: function () {
        var safe = true;
        if (this.renderflag) {
            if ($('.headerTable').is(':visible') == false)
                safe = false;
            this.renderflag = false;
        }
        if (safe)
            this._super('render');
    },
	toggleActiveRequests: function () {
		//GET TOGGLE VALUE
		var check = $('#cxmcheckbox:checked').length;

		//SET TIME PARAMETER FOR COOKIE
		var d = new Date();
		d.setTime(d.getTime() + (60 * 1000));
		var expires = "expires=" + d.toUTCString();

		//CREATE COOKIE
		document.cookie = '_cxmagentstatus=' + check + ';' + expires + 'path=/';

		//SET THE ACTIVE DATA ELEMENT
		if (check == '0')
			this.isActive = false;
		else if (check == '1')
			this.isActive = true;

		//SET UP API CALL URL
		var prefsURL = App.api.buildURL('rtCXMConfigurations/setAgentStatus/' + window.btoa(JSON.stringify(App.user.id)), null, null, {
			oauth_token: App.api.getOAuthToken()
		});

		//CALL THE API TO UPDATE USER'S STATUS
		App.api.call('POST', prefsURL, null, {
			success: _.bind(function (result) {
			}, this),

			error: _.bind(function (e) {
				console.log(e);
				window.handleDashletCallError(e);
			}, this)
		});
	},
    endChat: function () {
        //go back to table view
        this.notifier();
        //mark the chat session end
        var self = this;
		if(_.isEmpty(this.cxm_cid))
			this.cxm_cid = $('#nconvo .heading').attr('id');

        var prefsURL = App.api.buildURL('rtCXM/endChat/' + window.btoa(JSON.stringify(this.cxm_cid)), null, null, {
            oauth_token: App.api.getOAuthToken()
        });
        App.api.call('GET', prefsURL, null, {
            success: _.bind(function (result) {
                // console.log(result);

                if (!window.zfid)    window.storeZFID();
                var messagetosend = 'session_end' + '-/*' + App.user.id + '-/*';
                var promise = $.ajax({
                    url: 'https://rtcxmneon.rolustech.com/customerService/sendMessage',
                    data: {vsid: self.cxm_cid, message: messagetosend},
                    type: 'GET',
                });
                promise.done(function (data) {
                    // console.log(data);
                });
            }, this),

            error: _.bind(function (e) {
                console.log(e);
                window.handleDashletCallError(e);
            }, this)
        });
    },
    _startAutoRefresh: function () {
        var refreshRate = 1;
        if (refreshRate) {
            this._stopAutoRefresh();
            this._timerId = setInterval(_.bind(function () {
                if ($('.headerTable').is(':visible')) {
                    this.refreshflag = true;
                    this.nbinding();
                }
            }, this), refreshRate * 1000 * 10);
        }
    },
    _stopAutoRefresh: function () {
        if (this._timerId) {
            clearInterval(this._timerId);
        }
    },
})
