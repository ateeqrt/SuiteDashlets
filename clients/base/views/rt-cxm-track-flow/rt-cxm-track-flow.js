({
    plugins: ['Dashlet'],
    flows: undefined,
    tData: undefined,
    tab_form: false,
    person_form: false,
    row: false,
    e_id: '',
    autorefresh_flag: false,
    ga_data: undefined,
    trec: undefined,
    fwd_elem: undefined,
    back_elem: undefined,
    offset: undefined,
    prev_fwd: undefined,
    back_offset: undefined,
    back_length: undefined,
    chartdata: undefined,
    keys: undefined,
    cid: undefined,
    user_cookie_id: undefined,
    isValid: false,
    torender: undefined,
    ga_lbls: [],
    langModule: 'rt_Tracker',
    events: {
        'click [data-action=reset_main]': '_reset',
    },
    _defaultSettings: function () {
        if (!this.settings.get('ga_options')) {
            var fields = SUGAR.App.lang.getAppListStrings('rtcxm_sugar7_ga_options');
            var selected = [], i = 0;
            $.each(fields, function (name, label) {
                selected[i++] = name;
            });
            this.settings.set('ga_options', selected);
        }
    },
    initDashlet: function (view) {
        this._defaultSettings();
    },
    initialize: function (options) {
        this._super('initialize', [options]);
        this.flows = [];
        this.ga_data = [];
        this.trec = [];
        this.tData = '';
        this.fwd_elem = false;
        this.back_elem = false;
        this.torender = false;
        this.offset = -1;
        this.prev_fwd = -1;
        this.back_offset = -1;
        this.back_length = 0;
        this.keys = [];
        this.chartdata = [];
        this.cid = -1;
		this.chart_div_notice = '<h5 style="color: #555;text-align: -webkit-center;margin-top: 6%;">';
		this.table_div_notice = '<h5 style="color: #555;text-align: -webkit-center;margin-top: 33%;">'
        this.ga_lbls = ['LBL_TRACK_NAME', 'LBL_GNO_OF_VISITS', 'LBL_GUSER_TYPE', 'LBL_GFULL_REFERRER', 'LBL_GSOCIAL_NETWORK', 'LBL_GHAS_SOCIAL_SOURCE_REFERRAL', 'LBL_GCITY', 'LBL_GBROWSER', 'LBL_GBROWSER_VERSION', 'LBL_GOS', 'LBL_GOS_VERSION', 'LBL_DEVICE', 'LBL_LANGUAGE', 'LBL_USER_AGE_BRACKET', 'LBL_GUSER_GENDER'];
        this.LBL_TRACK_NAME = app.lang.get('LBL_TRACK_NAME', this.langModule);
        this.LBL_WEBPAGE_VISITED = app.lang.get('LBL_WEBPAGE_VISITED', this.langModule);
        this.LBL_DATE_VISITED = app.lang.get('LBL_DATE_VISITED', this.langModule);
        this.LBL_RELATED_MODULE = app.lang.get('LBL_RELATED_MODULE', this.langModule);
        this.LBL_NEXT_PAGE = app.lang.get('LBL_NEXT_PAGE', this.langModule);
        this.LBL_PREV_PAGE = app.lang.get('LBL_PREV_PAGE', this.langModule);
        this.LBL_SERVER_CNC_ER = app.lang.get('LBL_SERVER_CNC_ER', this.langModule);
        this.LBL_NO_VISITS = app.lang.get('LBL_NO_VISITS', this.langModule);
        this.LBL_CXM_NOT_VALID = app.lang.get('LBL_CXM_NOT_VALID', this.langModule);
        this.LBL_NO_RECORDS = app.lang.get('LBL_NO_RECORDS', this.langModule);
        this.LBL_COMPLETE_TRACK = app.lang.get('LBL_COMPLETE_TRACK', this.langModule);
        this.LBL_MIS_FILTS_DEFAULT = app.lang.get('LBL_MIS_FILTS_DEFAULT', this.langModule);
        this.LBL_FORM_LABEL = app.lang.get('LBL_FORM_LABEL', this.langModule);
        this.LBL_CART_LABEL = app.lang.get('LBL_CART_LABEL', this.langModule);
        this.LBL_HOMEPAGE_LABEL = app.lang.get('LBL_HOMEPAGE_LABEL', this.langModule);

    },
    customRefreshClicked: function (evt) {

        var y = $(evt.currentTarget).parent().parent().parent().parent();
        var ch = $(y).parent().children()[0];
        var st = $(ch).children()[0];
        $(st).removeClass('fa-cog');
        $(st).addClass('fa-refresh');
        $(st).addClass('fa-spin');
        this.view1();
        var that = this;

        setTimeout(function () {
            var bindD = true;
            if (that.person_form) {
                if (!that.row) {
                    bindD = false;
                }
                // that.autorefresh_flag = true;
            }
            if (bindD)
                that.bindDataChange();
            else {
                that.person_view_fn();
            }

            $(st).removeClass('fa-refresh');
            $(st).removeClass('fa-spin');
            $(st).addClass('fa-cog');
        }, 1000);
    },
    bindDataChange: function () {
        var module = this.model.attributes._module;
        var that = this;
        that.tab_form = true;
        if ((module) && module != "Dashboards") {
            that.tab_form = false;
            that.row = true;
        }
        var isValid = window.rtvalidatecxm;
        if (isValid) {
            this.isValid = true;
            this.initBind(module);
        } else {
            this.render();
            if (typeof isValid == "undefined") {
                setTimeout(function () {
                    that.bindDataChange();
                }, 1000);
            } else {
                this.isValid = false;
                setTimeout(function () {
                    that._initiateBinding(module);
                }, 1000);
            }
        }
        this._startAutoRefresh();
    },
    initBind: function (module) {
        var self = this;
        if (module == undefined) {
            setTimeout(function () {
                self.initBind(self.model.attributes._module);
            }, 1000);
        } else {
            this._initiateBinding(module);
        }
    },
    _initiateBinding: function (module) {
        if ((module) && module != "Dashboards") {
            this.tab_form = false;
            this.row = true;
            //get the ID and fill in the name
            var id = this.model.attributes.id;
            this.flows[0] = [];
            if (typeof this.model.attributes.full_name == 'undefined')
                this.flows[0]['name'] = this.model.attributes.first_name + ' ' + this.model.attributes.last_name;
            else
                this.flows[0]['name'] = this.model.attributes.full_name;
            //add name change event
            this.model.on('change:full_name', function (param) {
                $('.tname').text(param.changed.full_name);
            });
            if (this.isValid) {
                //get related cookie id
                this.rec_view_fn(module, id);
            } else {
                this.cid = 0;
                this.person_form = true;
                this.torender = true;
                this.render();
            }
        } else {
            this.list = true;
            this.row = false;
            if (this.isValid)
                this.fetch_recs(-1);
            else {
                this.torender = true;
                this.render();
            }
        }
    },
    fetch_recs: function (offset) {
        var that = this;
        var date = new Date();
        date.setDate(date.getDate() - 3);
        var str = SUGAR.App.date(date);
        var date_str = str.format("YYYY-MM-DD hh:mm:ss");
        var count = 0;
        var data = {action: 'extraction', date: date_str};
        var prefsURL = App.api.buildURL('rtCXM/decodeTrackRec/' + window.btoa(JSON.stringify(data)), null, null, {
            oauth_token: App.api.getOAuthToken()
        });
        App.api.call('GET', prefsURL, null, {
            success: _.bind(function (records) {
                if (typeof records == 'string')
                    records = JSON.parse(records);
                $.each(records, function (id, track) {
                    var start = (location.href.split('#'))[0];
                    var plink = start + '#' + track.parent_type + '/' + track.parent_id;
                    var link = true;
                    if (_.isEmpty(track.parent_type)) {
                        track.parent_type = '-';
                        link = false;
                    }
                    that.flows[count] = {
                        id: count + 1,
                        name: track.name,
                        cookie_id_c: track.cookie_id_c,
                        link: link,
                        parent_type: track.parent_type,
                        parent_link: plink,
                        parent_name: track.parent_name,
                        webpage: track.page_visited,
                        date_visited: track.date_visited,
                        track_rec: track.track_record_c,
                        history_btn_value: that.LBL_COMPLETE_TRACK
                    };
                    count++;
                });
                that.torender = true;
                that.render();
                if (that.flows.length < 1 && $('.no-data-track').length == 0) {
                    // $('#rtr-data-table').empty();
                    $('#rtr-data-table').append('<h5 class="no-data-track">' + that.LBL_NO_VISITS + '</h5>');
                }
            }, this),

            error: _.bind(function (e) {
				if (typeof e.responseText != 'undefined' && !_.isEmpty(e.responseText)) {
					if (typeof e.responseText == 'string')
						e.responseText = JSON.parse(e.responseText);
					var er = e.responseText;
					e.responseText = (typeof er.msg != 'undefined') ? er.msg : er;
					that.isValid = (typeof er.isValid != 'undefined') ? er.isValid : that.isValid;
					that.torender = true;
					that.render();
				}
                console.log(e);
                window.handleDashletCallError(e);
            }, this)
        });
    },
    ga_report: function (callback, id) {
        var self = this;
        if (!window.zfid)
            window.storeZFID();
		var ga_options = self.settings.get('ga_options');
        if (!window.zfid) {
            var Promise = $.Deferred();
            var arr = [];
            Promise.resolve(arr);
        } else {
            var data = {
                zfid: window.zfid,
                cid: self.user_cookie_id,
                filters: JSON.stringify(ga_options)
            };
            var prefsURL = App.api.buildURL('rtCXM/googleAnalyticsReport/' + window.btoa(JSON.stringify(data)), null, null, {
                oauth_token: App.api.getOAuthToken()
            });
            var _call = App.api.call('GET', prefsURL, null, {
                success: _.bind(function (records) {
					if (typeof self.options.meta.ga_options == "undefined") {
						var msg = {autoClose: true, level: 'warning'};
						msg.messages = self.LBL_MIS_FILTS_DEFAULT;
						App.alert.show('rtCXM_config', msg);
					}
                }, this),
                error: _.bind(function (e) {
                }, this)
            });
            var Promise = _call.xhr;
        }
        Promise.done(function (data) {
            self.ga_data = data;
            if (self.ga_data) {
                var filters = ga_options;
                $.each(filters, function (i, filter) {
                    self.flows[id][filter] = self.ga_data[filter];
                });
            }
        }).done(function () {
            callback();
        });
        Promise.fail(function (msg) {
            window.handleDashletCallError(msg);
            console.log(msg);
            $('.gcharts').notify(app.lang.get(msg.responseText, self.langModule), {position: "top right"});
            if (self.tab_form) {
                $('#rtr-data-table').empty();
                $('#rtr-data-table').append('<h5 class="no-data-notif" style="color: #e61718;">' + self.LBL_SERVER_CNC_ER + '</h5>');
            } else {
                // self.cid = -1;
                callback();
            }
        });
    },
    person_view_fn: function (evt) {
        if (typeof evt == "undefined") evt = '';
        if (this.autorefresh_flag == false)
            this.view1();
        this.autorefresh_flag = false;
        this.tab_form = false;
        this.person_form = true;
        this.isValid = window.rtvalidatecxm;
        if (this.isValid) {
            if (evt != '')
                this.e_id = evt.currentTarget.attributes['id'].value;
            var e_id = this.e_id.split("-")[1];
            this.trec = this.flows[e_id - 1].track_rec;
            var cid = this.flows[e_id - 1].cookie_id_c;
            this.user_cookie_id = cid;
            var that = this;
            var callback = function () {
                var data = {action: 'tracking', cookie_id_c: cid};
                var prefsURL = App.api.buildURL('rtCXM/decodeTrackRec/' + window.btoa(JSON.stringify(data)), null, null, {
                    oauth_token: App.api.getOAuthToken()
                });
                App.api.call('GET', prefsURL, null, {
                    success: _.bind(function (data) {
                        if (typeof data == 'string')
                            data = JSON.parse(data);
                        that.trec = data;
                        that.cid = e_id - 1;
                        that.torender = true;
                        that.render();
                        $('.back-arrow-flow').css('display', 'inline-block');
                        if ($('.gcharts').hasClass('screenChange') == false)
                            $('.gcharts').addClass('screenChange');
                    }, this),

                    error: _.bind(function (e) {
                        console.log(e);
                        window.handleDashletCallError(e);
                    }, this)
                });
            }
            this.ga_report(callback, e_id - 1);
        } else {
            this.cid = 0;
            this.torender = true;
            this.render();
            $('.back-arrow-flow').css('display', 'inline-block');
        }
    },
    rec_view_fn: function (module, id) {
        if (this.autorefresh_flag == false)
            this.view1();
        this.autorefresh_flag = false;
        this.tab_form = false;
        this.person_form = true;
        var that = this;
        var data = {action: 'gaRecView', id: id, module: module};
        var prefsURL = App.api.buildURL('rtCXM/decodeTrackRec/' + window.btoa(JSON.stringify(data)), null, null, {
            oauth_token: App.api.getOAuthToken()
        });
        App.api.call('GET', prefsURL, null, {
            success: _.bind(function (data) {
                if (typeof data == 'string')
                    data = JSON.parse(data);
                that.trec = data.tracks;
                that.cid = 0;
                that.user_cookie_id = data.cookie_id_c;
                var callback = function () {
                    that.torender = true;
                    that.render();
                }
                this.ga_report(callback, 0);
            }, this),

            error: _.bind(function (e) {
				if (typeof e.responseText != 'undefined' && !_.isEmpty(e.responseText)) {
					if (typeof e.responseText == 'string')
						e.responseText = JSON.parse(e.responseText);
					var er = e.responseText;
					e.responseText = (typeof er.msg != 'undefined') ? er.msg : er;
					that.isValid = (typeof er.isValid != 'undefined') ? er.isValid : that.isValid;
					that.cid = 0;
					that.person_form = true;
					that.torender = true;
					that.render();
				}
                console.log(e);
                window.handleDashletCallError(e);
            }, this)
        });
    },
    _reset: function (evt) {
        this.tData = '';
        this.tab_form = true;
        this.person_form = false;
        this.torender = true;
        this.render();
        $('.visitor_ga_data').text('');
        if ($('.gcharts').hasClass('screenChange'))
            $('.gcharts').removeClass('screenChange');
    },
    view1: function (tab) {
        this.torender = false;
        this.render();
    },
    render: function () {
        this._super('render');
        if (this.torender)
            $('.modal2').hide();
        var that = this;
        if (typeof google != "undefined" && this.person_form) {
            if (this.row !== true)
                $('.back-arrow-flow').css('display', 'inline-block');
            else {
                $('.gcharts').css('height', '366px');
                if ($('.gcharts').hasClass('screenChange') == false)
                    $('.gcharts').addClass('screenChange');
            }
            if (that.isValid)
                google.load('visualization', '1', {
                    packages: ['timeline'], callback: function () {
                        that.drawChart(that.trec)
                    }
                });
            else
                $('#chart_div').append(that.chart_div_notice + that.LBL_CXM_NOT_VALID + '</h5>');
            if (that.cid != -1) {
                var content = '';
                var toggle = true;
				var ga_options = that.settings.get('ga_options');
                var selectedFields = ga_options;
                var defs = SUGAR.App.lang.getAppListStrings('rtcxm_sugar7_ga_options');
                var fields = $.extend({name: "LBL_TRACK_NAME"}, defs);
                $.each(fields, function (name, label) {
                    if (name == "name" || selectedFields.indexOf(name) != -1) {
                        if (that.flows[that.cid][name]) {
                            if (toggle) {
                                content += '<div class="list-item">';
                            }
                            content += '<div class="span6">';
                            var lbl = label;
                            if (name == 'name')
                                lbl = app.lang.get(label, that.langModule);
                            content += '<ul><li><span>' + lbl;
                            content += '</span></li><li';
                            if (name == 'name')
                                content += ' class="tname" ';
                            content += '>' + that.flows[that.cid][name] + '</li></ul></div>';
                            if (!toggle) {
                                content += '</div>';
                                toggle = true;
                            } else
                                toggle = false;
                        }
                    }
                });
                $('.visitor_ga_data').append(content);
            }
        } else if (this.isValid) {
            $.each($('.track_history'), function (i, ele) {
                $(ele).on('click', function (evt) {
                    that.person_view_fn(evt);
                });
            });
        } else {
            $('#rtr-data-table .data-row').remove();
            $('#rtr-data-table').append(that.table_div_notice + that.LBL_CXM_NOT_VALID + '</h5>');
        }
    },
    drawChart: function (trec) {
        //get data from ajax
        var data = new google.visualization.DataTable();
        var that = this;
        data.addColumn('string', 'Title');
        data.addColumn('string', 'Label');
        data.addColumn('datetime', 'Session Start');
        data.addColumn('datetime', 'Session End');
        var d = '', i = 0, ki = 0;
        that.chartdata = [];
        that.keys = [];
        var inc = parseInt(new Date().toString().split(/\+|\-/)[1].split(" ")[0]) / 100;
        var m_inc = parseInt(new Date().toString().split(/\+|\-/)[1].split(" ")[0]) % 100;
        $.each(trec, function (index, val) {
            var f = val.flow;
            if (typeof f != "undefined" && f) {
                var idx = val.flow.indexOf("^*");
                if (idx != -1) {
                    // val.flow = f.split("^*")[0];
                    // f = val.flow + '* (submitted form) ';
					f = f.replace('^*', that.LBL_FORM_LABEL);
                }
				var idc = val.flow.indexOf("-*");
				if(idc != -1){
					// val.flow = f.split("-*")[0];
					// f = val.flow + '* (Added to Cart)';
					f = f.replace('-*', that.LBL_CART_LABEL);
				}
				if(val.flow.indexOf("|HomePage|") != -1){
					val.flow = f.split("|HomePage|")[0];
					val.flow = val.flow.trim();
					if(idx != -1 || idc != -1)
						f = f.replace('|HomePage|', '');
					else
						f = '' + val.flow ;
					f += that.LBL_HOMEPAGE_LABLE;
				}
                var d1 = (val.start.split(" "))[0];
                if (d1 != d) {
                    i = 0;
                    d = d1;
                    that.chartdata[d] = [];
                    that.keys[ki++] = d;
                }
                var start = new Date(val.start);
                start.setHours(start.getHours() + inc);
                start.setMinutes(start.getMinutes() + m_inc);
                var end = new Date(val.end);
                end.setHours(end.getHours() + inc);
                end.setMinutes(end.getMinutes() + m_inc);
                that.chartdata[d][i++] = [val.flow, f, start, end];
            }
        });
        var l = Object.keys(that.chartdata).length - 1;
        if (l < 0) {
            $('#chart_div').append(that.chart_div_notice + that.LBL_NO_RECORDS + '</h5>');
        } else {
            $.each(that.chartdata[that.keys[l]], function (index, val) {
                data.addRows([
                    [val[0], val[1], val[2], val[3]],
                ]);
            });
            // if(this.row){
            // var options = {
            // height: 500,
            // width: 371,
            // timeline: {
            // groupByRowLabel: true,
            // },
            // avoidOverlappingGridLines: false,
            // hAxis: {
            // format: 'h:m a'
            // },
            // };
            // }else
            var options = {
                height: 500,
                width: $('.gcharts').innerWidth() - 17,
                timeline: {
                    groupByRowLabel: true,
                },
                avoidOverlappingGridLines: false,
                hAxis: {
                    format: 'h:m a'
                },
            };
            var showDate = new Date(that.keys[l]);
            $('.chart-date').text(showDate.toDateString());
            if (that.keys.length > 0) {
                $(".prev_day").unbind('click');
                $(".next_day").unbind('click');
                $(".prev_day").button().click(function () {
                    that.prev_day_fn();
                });
                $(".next_day").button().click(function () {
                    that.next_day_fn();
                });
                if (that.keys.length < 2)
                    $('.prev_day').hide();
                else
                    $('.prev_day').show();
                $('.next_day').hide();
            }
            var chart = new google.visualization.Timeline(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    },
    prev_day_fn: function () {
        var date = new Date($('.chart-date').text());
        var dt = '' + date.getFullYear() + '-' + ("0" + (date.getMonth() + 1)).slice(-2) + '-' + ("0" + date.getDate()).slice(-2);
        var data = new google.visualization.DataTable();
        var that = this;
        data.addColumn('string', 'Title');
        data.addColumn('string', 'Label');
        data.addColumn('datetime', 'Session Start');
        data.addColumn('datetime', 'Session End');
        var l = $.inArray(dt, that.keys);
        l = l - 1;
        if (l - 1 < 0) {
            $('.prev_day').hide();
        }
        if (l < 0)
            return;
        $.each(that.chartdata[that.keys[l]], function (index, val) {
            data.addRows([
                [val[0], val[1], val[2], val[3]],
            ]);
        });
        // if(this.row){
        // var options = {
        // height: 500,
        // width: 371,
        // timeline: {
        // groupByRowLabel: true,
        // },
        // avoidOverlappingGridLines: false,
        // hAxis: {
        // format: 'h:m a'
        // },
        // };
        // }else
        var options = {
            height: 500,
            width: $('.gcharts').innerWidth() - 17,//805,
            timeline: {
                groupByRowLabel: true,
            },
            avoidOverlappingGridLines: false,
            hAxis: {
                format: 'h:m a'
            },
        };
        var showDate = new Date(that.keys[l]);
        $('.chart-date').text(showDate.toDateString());
        $('.next_day').show();
        var chart = new google.visualization.Timeline(document.getElementById('chart_div'));
        chart.draw(data, options);
    },
    next_day_fn: function () {
        var date = new Date($('.chart-date').text());
        var dt = '' + date.getFullYear() + '-' + ("0" + (date.getMonth() + 1)).slice(-2) + '-' + ("0" + date.getDate()).slice(-2);
        var data = new google.visualization.DataTable();
        var that = this;
        data.addColumn('string', 'Title');
        data.addColumn('string', 'Label');
        data.addColumn('datetime', 'Session Start');
        data.addColumn('datetime', 'Session End');
        var l = $.inArray(dt, that.keys);
        if (l > that.keys.length)
            return;
        l = l + 1;
        if (l + 1 == that.keys.length) {
            $('.next_day').hide();
        }
        $.each(that.chartdata[that.keys[l]], function (index, val) {
            data.addRows([
                [val[0], val[1], val[2], val[3]],
            ]);
        });
        // if(this.row){
        // var options = {
        // height: 500,
        // width: 371,
        // timeline: {
        // groupByRowLabel: true,
        // },
        // avoidOverlappingGridLines: false,
        // hAxis: {
        // format: 'h:m a'
        // },
        // };
        // }else
        var options = {
            height: 500,
            width: $('.gcharts').innerWidth() - 17,
            timeline: {
                groupByRowLabel: true,
            },
            avoidOverlappingGridLines: false,
            hAxis: {
                format: 'h:m a'
            },
        };
        $('.prev_day').show();
        var showDate = new Date(that.keys[l]);
        $('.chart-date').text(showDate.toDateString());
        var chart = new google.visualization.Timeline(document.getElementById('chart_div'));
        chart.draw(data, options);
    },
    _startAutoRefresh: function () {
        var refreshRate = parseInt(this.settings.get('auto_refresh'), 10);
        if (refreshRate) {
            this._stopAutoRefresh();
            this._timerId = setInterval(_.bind(function () {
                var bindD = true;
                if (this.person_form) {
                    if (!this.row) {
                        bindD = false;
                    }
                    this.autorefresh_flag = true;
                }
                if (bindD)
                    this.bindDataChange();
                else {
                    this.person_view_fn();
                }
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