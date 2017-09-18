({
    plugins: ['Dashlet'],
    rt_note: undefined,
    offset: undefined,
    cid: undefined,
    content: undefined,
    username: undefined,
    session_id: '',
    report: '',
    s_status: 'open',
    isValid: false,
    langModule: 'rt_Tracker',
    initialize: function (options) {
        this._super('initialize', [options]);
        this.rt_note = [];
        this.offset = -1;
        this.content = '';
        this.cid = '';
        this.username = '';
		this.setLabels();
    },
	setLabels: function(){
		this.LBL_CHAT_ASSIGNED_TO = app.lang.get('LBL_CHAT_ASSIGNED_TO', this.langModule);
		this.LBL_CHAT_NOT_ASSIGNED = app.lang.get('LBL_CHAT_NOT_ASSIGNED', this.langModule);
		this.LBL_CXM_NOT_VALID = app.lang.get('LBL_CXM_NOT_VALID', this.langModule);
		this.LBL_CHAT_NOT_FOUND = app.lang.get('LBL_CHAT_NOT_FOUND', this.langModule);
	},
    customRefreshClicked: function (evt) {

        var y = $(evt.currentTarget).parent().parent().parent().parent();
        var ch = $(y).parent().children()[0];
        var st = $(ch).children()[0];
        $(st).removeClass('fa-cog');
        $(st).addClass('fa-refresh');
        $(st).addClass('fa-spin');
        $('#rt_cxm_chat_history').text('');
        this.content = '';
        this.render();
        var that = this;

        setTimeout(function () {
            that.bindDataChange();

            $(st).removeClass('fa-refresh');
            $(st).removeClass('fa-spin');
            $(st).addClass('fa-cog');
        }, 1000);
    },
    bindDataChange: function () {
        var that = this;
        that.rt_note = [];
        var ctx = this.context,
            collection = ctx.get('collection');
        if (_.isEmpty(collection)) {  //Collection will be empty in "preview" mode
            return;
        }
        that.model.on('change:full_name', function (param) {
            var ch = $('.chheading').children()[0];
            var name = param.changed.full_name;
            $(ch).text(name);
        });
        this.validateCXMUserforDashlet(collection);
    },
    validateCXMUserforDashlet: function (collection, renderIt) {
        this.content = '';
        if (renderIt)
            this.render();
        var that = this;
        var isValid = window.rtvalidatecxm;
        this.cid = '';
        var name = '';
        var module = collection.models[0].attributes._module;
        var self = this;
        if (module == undefined) {
            setTimeout(function () {
                self.validateCXMUserforDashlet(collection, false);
            }, 1000);
        } else {
            if (typeof collection.models[0].attributes.full_name == 'undefined')
                name = collection.models[0].attributes.first_name + ' ' + collection.models[0].attributes.last_name;
            else
                name = collection.models[0].attributes.full_name;
            if (isValid) {
                this.isValid = true;
                var id = collection.models[0].attributes.id;
                var prefsURL = App.api.buildURL('rtCXM/trackerParent/' + window.btoa(JSON.stringify(id)), null, null, {
                    oauth_token: App.api.getOAuthToken()
                });
                App.api.call('GET', prefsURL, null, {
                    success: _.bind(function (data) {
                        if (typeof data == 'string')
                            data = JSON.parse(data);
                        that.cid = data.cid;
                        that.username = data.user_name;
                        if (that.cid == '')
                            that.cid = 0;
                        that.session_id = '';
                        that.report = '';
                        that.s_status = 'open';
                        that.recurv(that.cid, name);
                    }, this),
					
                    error: _.bind(function (e) {
						if (typeof e.responseText != 'undefined' && !_.isEmpty(e.responseText)) {
							if (typeof e.responseText == 'string')
								e.responseText = JSON.parse(e.responseText);
							var er = e.responseText;
							e.responseText = (typeof er.msg != 'undefined') ? er.msg : er;
							that.isValid = (typeof er.isValid != 'undefined') ? er.isValid : that.isValid;
							that.s_status = '';
							setTimeout(function () {
								that.content += '<div class="chheading" id="' + that.cid + '">';
								that.content += '<div class="convn-info">' + name + '</div></div>';
								that.content += '<div class="rt_cxm_chat_history_conversation">';
								that.ending(true);
							}, 3000);
						}
						console.log(e);
						window.handleDashletCallError(e);
                    }, this)
                });
                this._startAutoRefresh();
            } else {
                if (typeof isValid == "undefined")
                    setTimeout(function () {
                        that.validateCXMUserforDashlet(collection);
                    }, 500);
                else {
                    this.isValid = false;
                    this.s_status = '';
                    setTimeout(function () {
                        that.content += '<div class="chheading" id="' + that.cid + '">';
                        that.content += '<div class="convn-info">' + name + '</div></div>';
                        that.content += '<div class="rt_cxm_chat_history_conversation">';
                        that.ending(true);
                    }, 3000);
                }
            }
        }
    },
    recurv: function (id, name) {
        var that = this;
        var itr = 0;
        var dt = '';
        var inc = parseInt(new Date().toString().split(/\+|\-/)[1].split(" ")[0]) / 100;
        var m_inc = parseInt(new Date().toString().split(/\+|\-/)[1].split(" ")[0]) % 100;
        var data = {module: 'rt_cxm_chat', id: id};
        var prefsURL = App.api.buildURL('rtCXM/fetchCstmData/' + window.btoa(JSON.stringify(data)), null, null, {
            oauth_token: App.api.getOAuthToken()
        });
        App.api.call('GET', prefsURL, null, {
            success: _.bind(function (data) {
                if (typeof data == 'string')
                    data = JSON.parse(data);
                //HEADING
                that.content = '';
                that.content += '<div class="chheading" id="' + that.cid + '">';
                that.content += '<div class="convn-info">' + name + '</div></div>';
                that.content += '<div class="rt_cxm_chat_history_conversation">';
                if (data.length < 1) {
                    that.ending(true);
                    return;
                }
                _.each(data, function (chat) {
                    if (that.session_id != '' && that.session_id !== chat.session_id) {
                        that.content += '<div class="chheading2">';
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
					var report = app.lang.get(chat.report, that.langModule);
                    that.report = report;
                    that.session_id = chat.session_id;
                    that.s_status = chat.session_status;
                    var d = new Date(chat.date_modified);
                    d.setHours(d.getHours() + inc);
                    d.setMinutes(d.getMinutes() + m_inc);
                    var time = d.toLocaleTimeString().replace(/:\d+ /, ' ');
                    var dt2 = d.toLocaleDateString();
                    if (dt != dt2) {
                        dt = dt2;
                        //TIME SEPARATION
                        that.content += '<div class="chheading2">';
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
                            that.content += '<p style="color:#ff0006;font-weight:600;">RtCXM</p>';
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
                that.ending();
            }, this),

            error: _.bind(function (e) {
                console.log(e);
                window.handleDashletCallError(e);
            }, this)
        });
    },
    ending: function (notfound) {
        if (typeof notfound == "undefined") notfound = false;
        var that = this;
        if (this.s_status == 'CLOSED') {
            that.content += '<div class="chheading2">';
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
        if (notfound) {
			var nochatfound = '<h5 class="nochatfound">';
            if (!that.isValid)
                that.content += nochatfound + that.LBL_CXM_NOT_VALID + '</h5>';
            else
                that.content += nochatfound + that.LBL_CHAT_NOT_FOUND + '</h5>';
            that.content += '</div>';
        } else {
            that.content += '</div>';
            that.content += '<div class="chwriter">';
			var h5 = '<h5 style="text-align: -webkit-center;text-align:-moz-center;">';
            if (that.username)
                that.content +=  h5 + '(' + that.LBL_CHAT_ASSIGNED_TO + ' ' + that.username + ')</h5>';
            else
                that.content += h5 + that.LBL_CHAT_NOT_ASSIGNED + '</h5>';
            that.content += '</div>';
        }
        $('#rt_cxm_chat_history').text('');
        $('.modalnchat').hide();
        $('.rt_cxm_chat_history').append(that.content);
        var height = 0;
        $('.rt_cxm_chat_history_conversation div').each(function (i, val) {
            height += parseInt($(this).height());
        });
        height += '';
        $('.rt_cxm_chat_history_conversation').animate({scrollTop: height});
    },
    render: function () {
        if (this.content)
            $('.modalnchat').hide();
        else
            this._super('render');
    },
    _startAutoRefresh: function () {
        var refreshRate = parseInt(this.settings.get('auto_refresh'), 10);
        if (refreshRate) {
            this._stopAutoRefresh();
            this._timerId = setInterval(_.bind(function () {
                $('#rt_cxm_chat_history').text('');
                this.content = '';
                this.render();
                this.bindDataChange();
            }, this), refreshRate * 1000 * 60);
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