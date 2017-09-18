({
    plugins: ['Dashlet'],
    rt_note: undefined,
    forward_element: false,
    offset: undefined,
	row_elem: undefined,
	list_elem: undefined,
	ROW_LABEL: undefined,
    cid: undefined,
    content: undefined,
    username: undefined,
    torender: undefined,
    lock: undefined,
    names: undefined,
    itr: undefined,
    count: undefined,
    session_id: '',
    report: '',
    s_status: 'open',
    valid_agent: '0',
    isValid: false,
    langModule: 'rt_Tracker',
    session_time: 0,
    refreshing: false, 
	badge: 0,
    events: {
        'click [data-action=pop_chat]': 'pop_chat_fn',
        'click [data-action=show-more]': 'show_more_fn',
        'click [data-action=end-chat]': 'endChat',
        'click [data-action=view_history]': 'view_history_fn',
    },
    initialize: function (options) {
        this._super('initialize', [options]);
        this.rt_note = [];
        this.offset = -1;
        this.content = '';
        this.cid = '';
        this.username = '';
        this.torender = false;
        this.lock = false;
        this.names = [];
        this.itr = 0;
        this.count = 0;
		this.row_elem = false;
		this.list_elem = false;
        this.setCxmLabels();
    },
    setCxmLabels: function () {
        this.LBL_TRACK_NAME = app.lang.get('LBL_TRACK_NAME', this.langModule);
        this.LBL_MESSAGE = app.lang.get('LBL_MESSAGE', this.langModule);
        this.LBL_STATUS = app.lang.get('LBL_STATUS', this.langModule);
        this.LBL_CURRENTLY_ON = app.lang.get('LBL_CURRENTLY_ON', this.langModule);
        this.LBL_MORE_NOTIFICATIONS = app.lang.get('LBL_MORE_NOTIFICATIONS', this.langModule);
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
        this.LBL_COMPLETE_TRACK = app.lang.get('LBL_COMPLETE_TRACK', this.langModule);
        this.LBL_NO_TABLE_ENTRY = app.lang.get('LBL_NO_TABLE_ENTRY', this.langModule);
    },
    bindDataChange: function () {
        var that = this;
        this.render();
        that.rt_note = [];
        this.validateCXMUserforDashlet();
    },
    validateCXMUserforDashlet: function () {
        var that = this;
        var isValid = window.rtvalidatecxm;
        if (isValid) {
            this.isValid = true;
            if (this.session_time > 0) {
                this.nbinding(-1);
                this._startAutoRefresh();
                return;
            }
            //call session time
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
                that.nbinding(-1);
                that._startAutoRefresh();
            });
            Promise.fail(function (msg) {
                window.handleDashletCallError(msg);
                that.session_time = 1;
                that.nbinding(-1);
                that._startAutoRefresh();
            });
        } else {
            if (typeof isValid == "undefined")
                setTimeout(function () {
                    that.validateCXMUserforDashlet();
                }, 500);
            else {
                this.forward_element = false;
                this.isValid = false;
                setTimeout(function () {
                    that.end();
                }, 1000);
            }
        }
    },
    customRefreshClicked: function (evt) {

        var y = $(evt.currentTarget).parent().parent().parent().parent();
        var ch = $(y).parent().children()[0];
        var st = $(ch).children()[0];
        $(st).removeClass('fa-cog');
        $(st).addClass('fa-refresh');
        $(st).addClass('fa-spin');
        this.rend_view();
        this.refreshing = true;
        var that = this;

        setTimeout(function () {
            //reinitialize all
            that.names = [];
            that.itr = 0;
            that.count = 0;
            that.rt_note = [];
            // that.nbinding(-1);
            that.validateCXMUserforDashlet();

            $(st).removeClass('fa-refresh');
            $(st).removeClass('fa-spin');
            $(st).addClass('fa-cog');
        }, 1000);
    },
    show_more_fn: function () {
        this.rend_view();
        this.nbinding(this.offset);
    },
    nbinding: function (offset) {
        var that = this;
        var date = new Date();
        date.setDate(date.getDate() - 1);
        var str = SUGAR.App.date(date);
        var date_str = str.format("YYYY-MM-DD hh:mm:ss");
        var filter = 'filter[0][date_modified][$gt]=' + date_str + '&order_by=date_entered:desc';
        filter += '&offset=' + offset;
        App.api.call('GET', App.api.buildURL('rt_cxm_notif/filter?' + filter), null, {
            success: function (data) {
                that.offset = data.next_offset;
                var count = 0;
                _.each(data.records, function (note) {
                    var name = note.about_c.split(" ")[0];
                    if ($.inArray(name, that.names) == -1) {
                        that.names[this.itr++] = name;
                        // note.name == 'status' && ( )
                        if (!that.validDate(note.date_entered))
                            return;
                        var cid = note.about_c.split(" ")[2];
                        if (name != '' && name != ' ' && name != 'undefined') {
                            var facircle = '<i class="fa fa-circle" style="color: green;"></i>';
                            var msg = facircle + ' ' + note.message_c;
                            if (note.message_c.length > 8)
                                msg = facircle + ' ' + note.message_c.substr(0, 8) + '...';
                            //if(count > 0 && name == that.rt_note[count-1].name){}
                            //else{
                            name = (name.split("-")).join(" ");
                            var namelink = name;
                            if (note.link_id != '') {
                                namelink = '<a href="#' + note.link_module + '/' + note.link_id + '" class="link-x">' + name + '</a>';
                            }
                            if (note.message_c == 'online') {
                                msg = facircle;
                            }
                            that.rt_note[that.count] = {
                                c: that.count + 1,
                                cid: cid,
                                id: note.id,
                                name: name,
                                namelink: namelink,
                                message: msg,
                                status: note.status_c,
                                page: note.page_c,
                                history_btn_value: that.LBL_COMPLETE_TRACK
                            };
                            /* if(note.status_c == 'waiting'){
                             that.rt_note[that.count].btn_value = 'Start Chat';
                             }else  */
                            if (note.status_c == 'Chat Started') {
                                that.rt_note[that.count].btn_value = that.LBL_VIEW_CHAT;
                            } else {
                                that.rt_note[that.count].btn_value = that.LBL_START_CHAT;
                            }
                            that.count++;
                            count++;
                            //}
                        }
                    }
                }, that);
                if (data.next_offset == -1)// || count == 0
                    that.forward_element = false;
                else
                    that.forward_element = true;
                that.end();
            }
        });
    },
    end: function () {
        var that = this;
		that.row_elem = false;
		that.list_elem = false;
		that.ROW_LABEL = '';
        that.refreshing = false;
        that.badge = that.rt_note.length;
        that.torender = true;
        if (that.rt_note.length < 1) {
			that.row_elem = true;
            that.forward_element = false;
			if (that.isValid == false)
				that.ROW_LABEL = that.LBL_CXM_NOT_VALID;
			else
				that.ROW_LABEL = that.LBL_NO_NOTIFICATIONS;
        } else {
			that.list_elem = true;
		}
        that.render();
        if (that.forward_element)
            $('.note-more').show();
        else
            $('.note-more').hide();
    },
    validDate: function (notifTime) {

        var startDate = new Date(notifTime);
        var endDate = new Date();
        var timeDiff = Math.abs(startDate - endDate);

        // var hh = Math.floor(timeDiff / 1000 / 60 / 60);
        var mm = Math.floor(timeDiff / 1000 / 60);

        // if(hh > 1)
        if (mm > this.session_time)
            return false;
        return true;
    },
    render: function () {
        this._super('render');
        if (this.torender)
            $('.modaln').hide();
    },
    rend_view: function (tab) {
        this.torender = false
        this.render();
    },
    view_history_fn: function (evt) {
        var id = evt.currentTarget.attributes['id'].value;
        var element_selector = '.track_history[name=' + id + ']';
        if ($(element_selector).length > 0) {
            app.alert.dismissAll();
            $(element_selector).click();
        } else {
            app.alert.show('cxm-chat-alert', {
                level: 'error',
                messages: this.LBL_NO_TABLE_ENTRY,
                autoClose: false
            });
        }
    },
    pop_chat_fn: function (evt) {
        evt.stopImmediatePropagation();
        var id = evt.currentTarget.attributes['id'].value;
        this.cid = id;
        var name = evt.currentTarget.attributes['name'].value;
        this.session_id = '';
        this.report = '';
        this.s_status = 'open';
        var that = this;
        // var user = App.data.createBean('Users',{'id':App.user.id});
        // user.fetch().xhr.done(function(model){
        // that.valid_agent = model.cxm_agent;
        // // if(model.active_requests == '1'){
        // // that.isActive = true;
        // // that.nbinding();
        that.lock = false;
        that.recurv(id, name, -1, '');
        // // }
        // });
    },
    recurv: function (id, name, offset, dt) {
        var that = this;
        var filter = 'filter[0][client_no_c][$equals]=' + id;
        filter += '&order_by=date_entered:asc';
        filter += '&offset=' + offset + '&max_num=200';
        App.api.call('GET', App.api.buildURL('rt_cxm_chat/filter?' + filter), null, {
            success: function (data) {
                if (offset == -1) {
                    //HEADING
                    that.content = '';
                    that.content += '<div class="heading" id="' + that.cid + '">';
                    that.content += '<a class="btn btn-primary" id="cxm-end-chat" data-action="end-chat">End Chat</a>';
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
                        that.content += '>Session Report: ' + rp[1] + '</div></div>';
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
            that.content += '>Session Report: ' + rp[1] + '</div></div>';
        }
        that.content += '</div>';
        if (that.lock) {// || (that.valid_agent == '0')
            disableButton = true;
            that.content += '<div class="writer">';
            that.content += '<h5 style="text-align: -webkit-center;text-align:-moz-center;">';
            // if(that.valid_agent == '0')
            // that.content += that.LBL_CXM_NOT_AUTH;
            // else
            that.content += '<i class="fa fa-lock"></i> ' + that.LBL_CHAT_LOCKED + ' (' + that.LBL_CHAT_ASSIGNED_TO + that.username + ')';
            that.content += '</h5></div>';
        } else {
            that.content += '<div class="writer"><span class="span3 m3"><textarea class="messages autoExpand" rows="1" data-min-rows="1"></textarea></span>';
            that.content += '<span class="span1"><a class="btb btn btn-primary" style="width:37px;">' + that.LBL_SEND + '</a></span>';
            that.content += '</div>';
        }
        $('#nconvo').text('');
        var len = that.rt_note.length;
        if (that.refreshing)
            len = that.badge;
        if (len > 10000)
            len = '10000+';
        $('span.rt_cxm_chat.badge').text(len);
        $('#nrt-data-table').hide();
        $('.no-data-notif').hide();
        $('.headerTable').hide();
        $('#nconvo').show();
        $('#nconvo').append(that.content);
        $('#popupc').removeClass('hide');
        $('#popup1').removeClass('hide');
        $('#popupc').addClass('show');
        $(".close").button().click(function () {
            that.cid = '';
        });
        $(".btb").unbind('click');
        $(".btb").button().click(function () {
            that.send_message_cxm_fn();
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
            // var promise = $.ajax({
            // url: 'index.php?module=rt_Tracker&entryPoint=smartMessage',
            // data: {cid: that.cid, name: $('.heading.convn-info').text(), uname: app.user.attributes.full_name},
            // type: 'POST'
            // });
            var data = {cid: that.cid, name: $('.heading .convn-info').text(), uname: app.user.attributes.full_name};
            var prefsURL = App.api.buildURL('rtCXM/smartMessage/' + window.btoa(JSON.stringify(data)), null, null, {
                oauth_token: App.api.getOAuthToken()
            });
            App.api.call('GET', prefsURL, null, {
                success: _.bind(function (data) {
                    // promise.done(function(data){
                    if (data) {
                        //data = data.replace(/[\\n\\t\r]/g,"");
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
                        //data = data.replace(/\n/g,"<br>");
                    }
                    // });
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
                //$('.notifyjs-container').fadeOut();
                $('.notifyjs-wrapper').trigger('notify-hide');
            } else {
                // upscroll code
            }
            lastScrollTop = st;
        });
    },
    send_message_cxm_fn: function (evt) {
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
        if (!window.zfid)    window.storeZFID();
        var promise = $.ajax({
            url: 'https://rtcxmneon.rolustech.com/customerService/sendMessage',
            data: {vsid: that.cid, message: messagetosend},
            type: 'GET',
        });
        app.alert.show('cxm-chat-alert', {
            level: 'info',
            messages: that.LBL_SENDING,
            autoClose: true
        });
        promise.done(function (data) {
            console.log(data);
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
                    var dt1 = $('.heading2')[$('.heading2').length - 1].firstChild.innerText;
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
                // $.ajax({
                // url: 'index.php?module=rt_Tracker&entryPoint=createChatMessage',//EmailFetcher
                // type: 'POST',
                // data: {client_no_c: that.cid, message_c: message, name: name, sender: App.user.attributes.user_name, sender_id: App.user.id, first: first},
                // dataType: 'json',
                // });
                var data = {
                    client_no_c: that.cid,
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
                autoClose: false
            });
        });
    },
    endChat: function () {
        //go back to table view
        this.notifier();
        //mark the chat session end

        var prefsURL = App.api.buildURL('rtCXM/endChat/' + window.btoa(JSON.stringify(this.cid)), null, null, {
            oauth_token: App.api.getOAuthToken()
        });
        App.api.call('GET', prefsURL, null, {
            success: _.bind(function (result) {
                console.log(result);
            }, this),

            error: _.bind(function (e) {
                console.log(e);
                window.handleDashletCallError(e);
            }, this)
        });
    },
    _startAutoRefresh: function () {
        var refreshRate = parseInt(this.settings.get('auto_refresh'), 10);
        if (refreshRate) {
            this._stopAutoRefresh();
            this._timerId = setInterval(_.bind(function () {
                this.names = [];
                this.itr = 0;
                this.count = 0;
                this.rt_note = [];
                this.refreshing = true;
                this.validateCXMUserforDashlet();
            }, this), refreshRate * 1000);
        }
    },
    _stopAutoRefresh: function () {
        if (this._timerId) {
            clearInterval(this._timerId);
        }
    },
    dispose: function () {
        this._stopAutoRefresh();
        this._super('dispose');
    },
})
