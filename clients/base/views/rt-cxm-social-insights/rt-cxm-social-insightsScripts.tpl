{literal}
<script type="text/javascript">
    function SocialDashlet(){
        this.tabData        = undefined;
        this.row_elem       = false;
        this.list_elem      = false;
        this.goto_list      = false;
        this.social_status  = undefined;
        this.sData          = undefined;
        this.email          = undefined;
        this.loc            = undefined;
        this.p1             = undefined;
        this.flags          = [];
        this.tabs           = [];
        this.torender       = false;
        this.bean_id        = undefined;
        this.bean_type      = undefined;
        this.isValid        = false;
        this.langModule     = 'rt_Tracker';
        this.cxmnotvalid    = '<h5 style="text-align = -webkit-center;    margin-top = 6%;">';
        this._defaultSettings = {
            limit: 5,
            filter_id: 'all_records',
            intelligent: '0',
        };
        DashablelistView.call(this);
    }
    SocialDashlet.prototype = Object.create(DashablelistView.prototype);
    extendsFrom: 'DashablelistView',
    events: {
        'click [data-action=change_tab]': 'change_tab_fn',
        'click [data-action=reset_main]': '_reset',
        'click [data-action=cxm_auto_forms]': 'auto_forms',
    }

    function initialize(options) {
        this._super('initialize', [options]);

        //var initialize
        this.sData = [];
        this.email = '';
        this.social_status = '';
        this.p1 = false;
        this.loc = false;

        //collection initialize
        this.collection = App.data.createMixedBeanCollection();
        this.collection.module_list = ["Contacts", "Leads"];
        var obj = {field: 'date_modified', direction: 'desc'};
        this.collection.orderBy = obj;
        this.context.set('collection', this.collection);
        this.flags = ['general', 'facebook', 'twitter', 'google', 'linkedin'];
        var that = this;
        this.tabs = [];
        $.each(this.flags, function (i, f) {
            that.tabs[f] = false;
        });
        this.bean_id = '';
        this.bean_type = '';
        this.isValid = window.rtvalidatecxm;
        this.initPagination();
        this.setCxmLabels();
    }
    function setCxmLabels() {
        this.LBL_HOME = app.lang.get('LBL_HOME', this.langModule);
        this.LBL_TWITTER = app.lang.get('LBL_TWITTER', this.langModule);
        this.LBL_FACEBOOK = app.lang.get('LBL_FACEBOOK', this.langModule);
        this.LBL_GOOGLE_PLUS = app.lang.get('LBL_GOOGLE_PLUS', this.langModule);
        this.LBL_LINKEDIN = app.lang.get('LBL_LINKEDIN', this.langModule);
        this.LBL_FORM = app.lang.get('LBL_FORM', this.langModule);
        this.LBL_NAME = app.lang.get('LBL_NAME', this.langModule);
        this.LBL_LOCATION = app.lang.get('LBL_LOCATION', this.langModule);
        this.LBL_PROFILE_LINK = app.lang.get('LBL_PROFILE_LINK', this.langModule);
        this.LBL_INTRODUCTION = app.lang.get('LBL_INTRODUCTION', this.langModule);
        this.LBL_CXM_NOT_VALID = app.lang.get('LBL_CXM_NOT_VALID', this.langModule);
        this.LBL_NOT_EN_DATA = app.lang.get('LBL_NOT_EN_DATA', this.langModule);
        this.LBL_NO_VALID_DATA = app.lang.get('LBL_NO_VALID_DATA', this.langModule);
        this.LBL_SOCIAL_ERR_MSG = app.lang.get('LBL_SOCIAL_ERR_MSG', this.langModule);
        this.LBL_SHOW_MORE = app.lang.get('LBL_SHOW_MORE', this.langModule);
    }
    inc = 1;
    function initPagination() {
        this._initPaginationBottom = function () {
            if (!this.layout) {
                return;
            }
            if (!this.isValid)
                return;
            if (this.row_elem)
                return;
            var pageComponent = this.layout.getComponent('list-bottom');
            if (pageComponent) {
                return;
            }
            var prefsURL = App.api.buildURL('rtCXM/version', null, null, {
                oauth_token: App.api.getOAuthToken()
            });
            if (this.inc + 1 > 2)
                return;
            this.inc++;
            App.api.call('GET', prefsURL, null, {
                success: _.bind(function (result) {
                    if (typeof result == 'string')
                        result = JSON.parse(result);
                    if (result !== null && result.className !== null) {
                        pageComponent = app.view.createView({
                            context: this.context,
                            name: 'list-bottom',
                            className: result.className,
                            meta: {
                                template: 'list-bottom.dashlet-bottom'
                            },
                            module: this.module,
                            primary: false,
                            layout: this.layout
                        });
                        var self = this;
                        pageComponent.setShowMoreLabel = function () {
                            this.showMoreLabel = self.LBL_SHOW_MORE;
                        };
                        this.layout.addComponent(pageComponent);
                    }
                }, this),

                error: _.bind(function (e) {
                    window.handleDashletCallError(e);
                }, this)
            });
        };
    }
    function initDashlet(view) {
        if (this.meta.config) {
            this.settings.on('change:module', function (model, moduleName) {
                var label = (model.get('filter_id') === 'assigned_to_me') ? 'TPL_DASHLET_MY_MODULE' : 'LBL_MODULE_NAME';
                this.dashModel.set('module', moduleName);
                this.dashModel.set('filter_id', 'all_records');
                this.layout.trigger('dashlet:filter:reinitialize');
                this._updateDisplayColumns();
                this.updateLinkedFields(moduleName);
            }, this);
            this.settings.on('change:intelligent', function (model, intelligent) {
                this.setLinkedFieldVisibility('1', intelligent);
            }, this);
            this.on('render', function () {
                var isVisible = !_.isEmpty(this.settings.get('linked_fields')) ? '1' : '0';
                this.setLinkedFieldVisibility(isVisible, this.settings.get('intelligent'));
            }, this);
        }
        this._initializeSettings();
        this.metaFields = this._getColumnsForDisplay();
        if (this.settings.get('intelligent') == '1') {
            var link = this.settings.get('linked_fields'),
                model = app.controller.context.get('model'),
                module = this.settings.get('module'),
                options = {
                    link: {
                        name: link,
                        bean: model
                    },
                    relate: true
                };
            this.collection = app.data.createBeanCollection(module, null, options);
            this.context.set('collection', this.collection);
            this.context.set('link', link);
        } else {
            this.context.unset('link');
        }
        this.before('render', function () {
            if (!this.moduleIsAvailable) {
                this.$el.html(this._noAccessTemplate());
                return false;
            }
        });
        if (this.meta.config) {
            this._configureDashlet();
        } else if (this.moduleIsAvailable) {
            var filterId = this.settings.get('filter_id');
            if (!filterId || this.meta.preview) {
                this._displayDashlet();
                return;
            }
            var filters = app.data.createBeanCollection('Filters');
            filters.setModuleName(this.settings.get('module'));
            filters.load({
                success: _.bind(function () {
                    var filter = filters.collection.get(filterId);
                    var filterDef = filter && filter.get('filter_definition');
                    this._displayDashlet(filterDef);
                }, this),
                error: _.bind(function (err) {
                    this._displayDashlet();
                }, this)
            });
        }
    }
    function _setDefaultModule() {
        var module = 'Leads';
        if (this.context.get('module') != 'Home' && this.context.get('module') != '')
            module = this.context.get('module');
        this.settings.set('module', module);
    }
    function _getColumnsForDisplay() {
        var columns = [],
            fields = this.getFieldMetaForView(this._getListMeta(this.settings.get('module')));
        if (!this.settings.get('display_columns')) {
            this._updateDisplayColumns();
        }
        if (!this.settings.get('linked_fields')) {
            this.updateLinkedFields(this.model.module);
        }
        _.each(this.settings.get('display_columns'), function (name) {
            var field = _.find(fields, function (field) {
                return field.name === name;
            }, this);
            var column = _.extend({
                name: name,
                sortable: true
            }, field || {});
            if (name == '_module')
                column.label = "Module";
            column.sortable = false;
            columns.push(column);
        }, this);
        columns = app.metadata._patchFields(this.module, app.metadata.getModule(this.module), columns);
        return columns;
    }
    function _getAvailableColumns() {
        var columns = {},
            module = this.settings.get('module');
        if (!module) {
            return columns;
        }
        var keys = ['full_name', 'email', '_module', 'generic_flag', 'twitter_flag', 'google_flag', 'facebook_flag', 'linkedin_flag'];
        var labels = ['Name', 'Email', 'Module', 'General', 'Twitter', 'Google', 'Facebook', 'LinkedIn'];
        $.each(keys, function (index, key) {
            columns[key] = labels[index];
        });
        return columns;
    }
    function _displayDashlet(filterDef) {
        if (typeof filterDef == "undefined") filterDef = '';
        var module = this.model.attributes._module;
        if (!module) {
            if (location.href.indexOf('#Leads/') != -1)
                module = 'Leads';
            else if (location.href.indexOf('#Contacts/') != -1)
                module = 'Contacts';
        }
        var that = this;
        if ((module) && module != "Dashboards") {

            that.row_elem = true;
            that.list_elem = false;
            var evt = [];
            evt.toElement = [];
            evt.toElement.id = module + '/' + that.model.attributes.id;
            evt.toElement.innerText = '';

            //ON EMAIL CHANGE
            that.model.on('change:email', function (param) {
                var emails = param.changed.email;
                $.each(emails, function (i, e) {
                    if (e.primary_address) {
                        evt.toElement.innerText = e.email_address;
                        return;
                    }
                });
                that.person_view_fn(evt, 'rec');
            });

            if ((that.model.attributes.email) && that.model.attributes.email.length > 0) {
                evt.toElement.innerText = that.model.attributes.email[0].email_address;
            }
            that.person_view_fn(evt, 'rec');
        } else {
            that.row_elem = false;
            that.list_elem = true;
            that.torender = true;
            var columns = this._getColumnsForDisplay();
            this.meta.panels = [{
                fields: columns
            }];
            this.context.set('skipFetch', false);
            this.context.set('limit', this.settings.get('limit'));
            this.context.set('fields', this.getFieldNames());

            if (filterDef) {
                this._applyFilterDef(filterDef);
                this.context.reloadData({
                    'recursive': false
                });
            }
            setTimeout(function () {
                that.render();
            }, 1000);
        }
        this._startAutoRefresh();
    }
    function _reset(evt) {
        this.content = '';
        this.row_elem = false;
        this.list_elem = true;
        this.torender = true;
        if (!this.collection.dataFetched) {
            if (this.collection.models.length > 0) {
                this.collection.dataFetched = true;
            }
        }
        this.render();
        $('div.list-view').css('overflow-y', 'auto');
        if (this.collection.next_offset > -1)
            $('.ft1').show();
    }
    function auto_forms() {
        var modelPrefil = app.data.createBean(this.bean_type, {id: this.bean_id});
        var fields = ['Phone', 'Bio', 'Introduction', 'AboutMe', 'dp', 'Name', 'Location'];
        var sugarAttributes = {
            'Phone': 'phone_mobile', 'Bio': 'description', 'Introduction': 'description', 'AboutMe': 'description',
            'dp': 'rtcxm_picture'
        }
        var conflict = false;
        var conflictFields = [];
        var itr = 0;
        var conflictData = [];
        var conflictSugarData = [];
        var change = {};
        var that = this;
        app.alert.dismissAll();
        app.alert.show('rtCXM_config', {level: 'process', title: 'Loading'});
        setTimeout(function () {
            app.alert.dismissAll();
        }, 4000);
        modelPrefil.fetch().xhr.done(function (data) {
            var itr = 0;
            $.each(fields, function (i, f) {
                if (that.tabData[f]) {
                    var attributeName = sugarAttributes[f];
                    if (f == 'Name') {
                        var names = that.tabData[f].split(' ');
                        if (names.length > 1) {
                            var lastIndex = names.length - 1;
                            var nameConflict = false;
                            if (data['first_name'] != names[0]) {
                                if (data['first_name'] != '') {
                                    conflict = true;
                                    nameConflict = true;
                                    conflictData[itr] = names[0] + ' (RtCXM Field)';
                                    conflictSugarData[itr] = data['first_name'];
                                    conflictFields[itr++] = 'first_name';
                                }
                                modelPrefil.set({
                                    first_name: names[0] + ' (RtCXM Field)',
                                });
                            }
                            if (data['last_name'] != names[lastIndex]) {
                                if (data['last_name'] != '') {
                                    conflict = true;
                                    nameConflict = true;
                                    conflictData[itr] = names[lastIndex] + ' (RtCXM Field)';
                                    conflictSugarData[itr] = data['last_name'];
                                    conflictFields[itr++] = 'last_name';
                                }
                                modelPrefil.set({
                                    last_name: names[lastIndex] + ' (RtCXM Field)',
                                });
                            }
                            if (nameConflict) {
                                modelPrefil.set({
                                    conflict: conflict,
                                    conflictData: conflictData,
                                    conflictSugarData: conflictSugarData,
                                    conflictFields: conflictFields,
                                });
                            }
                        }
                    } else if (f == 'Location') {
                        var addressArray = that.tabData[f].split(',');
                        if (addressArray.length == 0)
                            addressArray = that.tabData[f].split(' ');
                        var pr = ['country', 'state', 'city', 'street'];
                        var id = 0;
                        var cFlag = false;
                        if (addressArray.length >= 1) {
                            var len = addressArray.length - 1;
                            for (i = addressArray.length - 1; i >= 0; i--) {
                                var key = 'primary_address_' + pr[id];
                                addressArray[i] = addressArray[i].replace(',', '');
                                addressArray[i] = addressArray[i].trim();
                                if (data[key] != addressArray[i]) {
                                    if (data[key] != '') {
                                        conflict = true;
                                        cFlag = true;
                                        conflictData[itr] = addressArray[i] + ' (RtCXM Field)';
                                        conflictSugarData[itr] = data[key];
                                        conflictFields[itr++] = key;
                                    }
                                    var attribute = {};
                                    attribute[key] = addressArray[i] + ' (RtCXM Field)';
                                    modelPrefil.set(attribute);
                                }
                                id++;
                            }
                            if (cFlag) {
                                modelPrefil.set({
                                    conflict: conflict,
                                    conflictData: conflictData,
                                    conflictSugarData: conflictSugarData,
                                    conflictFields: conflictFields,
                                });
                            }
                        }
                    } else if (data[attributeName] != that.tabData[f]) {
                        if (data[attributeName] != '') {
                            conflict = true;
                            conflictData[itr] = that.tabData[f] + ' (RtCXM Field)';
                            conflictSugarData[itr] = data[attributeName];
                            conflictFields[itr++] = attributeName;
                            modelPrefil.set({
                                conflict: conflict,
                                conflictData: conflictData,
                                conflictSugarData: conflictSugarData,
                                conflictFields: conflictFields,
                            });
                        }
                        var attribute = {};
                        attribute[attributeName] = that.tabData[f];
                        if (f != 'dp')
                            attribute[attributeName] += ' (RtCXM Field)';
                        modelPrefil.set(attribute);
                    }
                }
            });
            app.alert.dismissAll();
            app.drawer.open({
                layout: 'update-actions',
                context: {
                    create: true,
                    select: true,
                    module: that.bean_type,
                    model: modelPrefil,
                    conflict: conflict,
                    conflictFields: conflictFields
                }
            }, function (selectedModel) {
                if (!_.isEmpty(selectedModel)) {
                    SUGAR.App.controller.context.reloadData({});
                }
            });
        });
    }
    function view1(tab) {
        this.torender = false;
        this.render();
        $('.ui-social-item').each(function (index) {
            var i = 'cxm-' + (index + 1);
            var name = $('#' + i).attr('name');
            if (name == tab) {
                if (!$('#' + i).hasClass('active')) {
                    $('#' + i).addClass('active');
                }
            } else {
                if ($('#' + i).hasClass('active')) {
                    $('#' + i).removeClass('active');
                }
            }
        });
        if (this.goto_list)
            $('.back-arrow').css('display', 'inline-block');
    }
    function person_view_fn(evt, view) {
        if (typeof view == "undefined") view = '';
        var tab = 'cxm-general';
        if (view == '')
            this.goto_list = true;
        this.view1(tab);
        tab = tab.split('-')[1];
        this.email = evt.toElement.innerText;
        var bean = evt.toElement.id.split('/');
        this.bean_id = '';
        this.bean_type = '';
        this.bean_id = bean[1];
        this.bean_type = bean[0];
        this.list_elem = false;
        this.row_elem = true;
        var that = this;
        var callback = function () {
            that.torender = true;
            that.render();
            if (that.goto_list)
                $('.back-arrow').css('display', 'inline-block');
        };
        $('.content-box').text('');
        $.each(this.flags, function (i, f) {
            that.tabs[f] = false;
        });
        if (this.isValid) {
            this.tabs['cform'] = true;
            this.setData(tab, callback);
        } else {
            this.torender = true;
            setTimeout(function () {
                that.render();
            }, 1000);
        }
    }
    function go_back_fn(evt) {
        this.view1();
        if (this.offset != -1 && this.back_offset > 0)
            this.back_offset -= 20;
        this.fetch_recs(this.back_offset, 'back');
    }
    function change_tab_fn(evt) {
        var tab = evt.currentTarget.attributes['name'].value;
        this.view1(tab);
        tab = tab.split('-')[1];
        var id = evt.currentTarget.attributes['id'].value,
            that = this;
        var callback = function (id) {
            that.render();
            that.torender = true;
            $('#' + id).addClass('active');
            $('.ui-social-item').each(function (index) {
                var i = 'cxm-' + (index + 1);
                if (id != i) {
                    if ($('#' + i).hasClass('active')) {
                        $('#' + i).removeClass('active');
                    }
                }
            });
            if (that.goto_list)
                $('.back-arrow').css('display', 'inline-block');
            $('.modal1').hide();
        };
        this.tabs['cform'] = true;
        this.setData(tab, callback, id);
    }
    function render() {
        if (!this.isValid) {
            if (window.rtvalidatecxm) {
                this.isValid = window.rtvalidatecxm;
                this.collection.dataFetched = true;
            } else {
                this.collection.dataFetched = false;
                if (this.row_elem)
                    this.social_status = this.cxmnotvalid + this.LBL_CXM_NOT_VALID + '</h5>';
            }
        }
        this._super('render');
        if (this.list_elem && !this.isValid) {
            $('#not-valid').append(this.cxmnotvalid + this.LBL_CXM_NOT_VALID + '</h5>');
        }
        var that = this;
        if (that.row_elem) {
            if (that.goto_list)
                $('.back-arrow').css('display', 'inline-block');
            $('.rt_cxm_social_table').hide();
            $('.ft1').hide();
            $('div.list-view').css('overflow-y', 'hidden');
            $('.ui-social-item').each(function (index) {
                var name = $(this).attr('name').split('-')[1];
                if (that.isValid == false || that.tabs[name] == false) {
                    $(this).removeAttr('data-action');
                    var child = $(this).children()[0];
                    $(child).addClass('tblock');
                    var gchild = $(child).children()[0];
                    $(gchild).css('color', '#AFAFAF');
                }
            });
        }
        if (that.torender) {
            $('.modal1').hide();
        }
        if (that.isValid && that.list_elem) {
            var keys = ['generic_flag', 'twitter_flag', 'google_flag', 'facebook_flag', 'linkedin_flag'];
            var flags = [
				'<i class="fa fa-male"></i>', 
				'<i class="fa fa-twitter"></i>', 
				'<i class="fa fa-google-plus"></i>', 
				'<i class="fa fa-facebook"></i>', 
				'<i class="fa fa-linkedin"></i>'
			];

            $('td.rt_cxm_social.full_name a').addClass('link-x');

            var indices = [], id = 0, emailid = -1;
            $.each($("th.rt_cxm_social"), function (i, k) {
                var field = $(k).attr('data-fieldname');
                if ($.inArray(field, keys) > -1) {
                    indices[field] = i;
                    $(k).hide();
                } else if (field == 'email') {
                    emailid = i;
                }
            });
            var that = this;
            $.each($('tr.rt_cxm_social'), function (i, k) {
                $.each(Object.keys(indices), function (ic, kc) {
                    var flag = that.collection.models[i].attributes[kc];
                    var idc = indices[kc];
                    var child = $(k.children)[idc];
                    if (flag == 'true') {
                        $(child).text('');
                        $(child).append(flags[ic]);
                    }
                });
                if (emailid != -1) {
                    var email = $(k.children)[emailid];
                    var namefield = $('td.rt_cxm_social.full_name a')[i];
                    var grandChild = $('td.rt_cxm_social.email a')[i];
                    $(grandChild).removeAttr('data-action');
                    $(grandChild).addClass('link-x');
                    $(grandChild).attr('id', ($(namefield).attr('href').split('#'))[1]);
                    $(grandChild).button().click(function (evt) {
                        that.person_view_fn(evt);
                    });
                }
            });
        }
    }
    function getFieldNames() {
        var fields = [
			"full_name", 
			"email", 
			"salutation", 
			"first_name", 
			"last_name", 
			"title", 
			"generic_flag",
			"twitter_flag",
			"google_flag", 
			"facebook_flag", 
			"linkedin_flag"
		];
        return fields;
    }
    function setData(tab, callback, id) {
        this.social_status = '';
        var that = this;
        that.p1 = false;
        that.loc = false;
        //if email is empty show error message
        if (that.email == undefined || that.email == '' || that.email == null) {//window
            this.social_status = that.LBL_NOT_EN_DATA;
            this.tabs['cform'] = false;
            if (callback && typeof callback == 'function') {
                if (typeof id != 'undefined')
                    callback(id);
                else
                    setTimeout(function () {
                        callback();
                    }, 500);
            }
            return;
        }
        var found = false;
        var parsed = null;
        var dtype = tab;
        if (tab == 'general')
            dtype = "generic";
        var tosend = dtype;// + '_c';
        if (that.sData[that.email] != undefined) {
            var emailPromise = $.Deferred();
            emailPromise.resolve(that.sData[that.email]);
        } else {
            if (typeof that.bean_id == 'undefined')
                that.bean_id = '';
            if (!window.zfid)
                window.storeZFID();
            var data = {email: that.email, lead_id: that.bean_id, type: that.bean_type, zfid: window.zfid};
            var prefsURL = App.api.buildURL('rtCXM/fetchEmailBean/' + window.btoa(JSON.stringify(data)), null, null, {
                oauth_token: App.api.getOAuthToken()
            });
            var _call = App.api.call('GET', prefsURL, null, {
                success: _.bind(function (records) {
                }, this),
                error: _.bind(function (e) {
                }, this)
            });
            var emailPromise = _call.xhr;
        }
        emailPromise.done(function (data) {
            if (typeof data == "string")
                data = JSON.parse(data);
            that.sData[that.email] = data;
            if (data != null) {
                $.each(data, function (k, v) {
                    if (k == 'generic')
                        k = 'general';
                    if (k != 'email') {
                        that.tabs[k] = true;
                    }
                });
            }
        }).done(function () {
            if (that.sData[that.email]) {
                var data = {type: tab, data: tosend};
                var prefsURL = App.api.buildURL('rtCXM/trackDecode/' + window.btoa(JSON.stringify(data)), null, null, {
                    oauth_token: App.api.getOAuthToken()
                });
                var _call = App.api.call('GET', prefsURL, null, {
                    success: _.bind(function (records) {
                    }, this),
                    error: _.bind(function (e) {
                        console.log(e);
                        window.handleDashletCallError(e);
                    }, this)
                });
                var decodePromise = _call.xhr;
            } else {
                var decodePromise = $.Deferred();
                decodePromise.resolve('');
            }
            decodePromise.done(function (data) {
                if (data && typeof data == "string")
                    data = JSON.parse(data);
                parsed = data;
                that.tabData = data;
            }).done(function () {
                if (parsed && (parsed.length > 0 || parsed.length == undefined)) {
                    var data = '';
                    var im = '', imflag = false;
                    if (tab == 'twitter' || tab == 'google') {
                        var image = parsed.dp;
                        if (image != '') {
                            im = '<img src="' + image + '" class="avatar avatar-md small-pic">';
                            that.tabData.Image = image;
                            parsed.Image = '';
                            imflag = true;
                        }
                    }
                    if (im == '') {
                        im = '<span class="myLabel label label-module label-module-md label-' + that.bean_type + ' span2" rel="tooltip" data-title="' + that.bean_type + '">' + that.bean_type.slice(0, 2) + '</span>'
                    }
                    data = that._appd(parsed, im, imflag);
                    that.social_status = data;
                } else {
                    that.social_status = that.LBL_NO_VALID_DATA;
                }
                if (callback && typeof callback == 'function') {
                    if (typeof id != 'undefined')
                        callback(id);
                    else
                        callback();
                }


            });
            decodePromise.fail(function (msg) {
                console.log(msg);
                window.handleDashletCallError(msg);
                that.social_status = that.LBL_SOCIAL_ERR_MSG;
                if (callback && typeof callback == 'function') {
                    if (typeof id != 'undefined')
                        callback(id);
                    else
                        callback();
                }
            });
        });emailPromise.fail(function (e) {
			if (typeof e.responseText != 'undefined' && !_.isEmpty(e.responseText)) {
				if (typeof e.responseText == 'string')
					e.responseText = JSON.parse(e.responseText);
				var er = e.responseText;
				e.responseText = (typeof er.msg != 'undefined') ? er.msg : er;
				// that.isValid = (typeof er.isValid != 'undefined') ? er.isValid : that.isValid;
				that.social_status = that.cxmnotvalid;
				that.social_status += (e.responseText.indexOf('LBL_') != -1) ? app.lang.get(e.responseText, that.langModule) : e.responseText;
				that.social_status += '</h5>';
				if (callback && typeof callback == 'function') {
					if (typeof id != 'undefined')
						callback(id);
					else
						callback();
				}
			}
			console.log("click here");
            // msg.responseText = app.lang.get(msg.responseText, that.langModule);
            window.handleDashletCallError(e);
        });
    }
    function _appd(parsed, im, imflag) {
        var that = this;
        var data = '';
        var pdone = false;
        _.each(parsed, function (obj, key) {
            if (obj != '' && obj != undefined && obj != null) {
                if (key == 'Is_verified' || key == 'Verified' || key == 'Id' || key == 'Other_details'
                    || key == 'Kind' || key == 'ObjectType' || key == 'dp' || key == 'Sname')
                    return;
                if (key == 'Name' || key == 'displayName') {
                    data += that.p1_check();
                    data += '<div class="list-item">';
                    data += '<div class="span5"><ul><li><span>' + that.LBL_NAME;
                    data += '</span></li><li>' + obj;
                    //check for verification
                    if (parsed.verified || parsed.is_verified)
                        data += '  <i class=\"fa-check-circle\"></i>';
                    if (parsed.hasOwnProperty('Sname')) {
                        data += '</li><li>(<a target="_blank" href="https://twitter.com/' + parsed.Sname + '">@' + parsed.Sname + '</a>)';
                    }
                    data += '</li></ul></div>';
                    pdone = true;
                    that.loc = true;
                }
                if (key.search('Loc') != -1) {
                    data += that.p1_check();
                    data += '<div class="span7">';
                    if (imflag) {
                        data += '<span>' + im + '</span>';
                    } else {
                        data += im;
                    }
                    data += '<ul><li><span>' + that.LBL_LOCATION;
                    data += '</span></li><li>' + obj;
                    data += '</li></ul></div></div>';
                    pdone = true;
                    that.loc = false;
                }
                if (key == 'Bio' || key.search('Intro') != -1 || key == 'AboutMe') {
                    data += that.loc_check(imflag, im);
                    data += that.p1_check();
                    data += '<h3>' + that.LBL_INTRODUCTION + '</h3>';
                    data += '<p class="rt-p">' + obj + '</p>';
                    pdone = true;
                }
                if (key == 'Url' || key == 'Link') {
                    data += that.loc_check(imflag, im);
                    data += that.p1_check();
                    data += '<h3>' + that.LBL_PROFILE_LINK + '</h3>';
                    data += '<p class="rt-p"><a target="_blank" href="' + obj + '">' + obj + '</a></p>';
                    pdone = true;
                }
                if (!pdone) {
                    data += that.loc_check(imflag, im);
                    if (!that.p1) {
                        data += '<div class="list-item">';
                        that.p1 = true;
                    } else {
                        that.p1 = false;
                    }
                    if (key == "Trends")
                        data += '<div class="span12">';
                    else
                        data += '<div class="span6">';
                    if (obj instanceof Array) {
                        data += '<h3>' + app.lang.get('LBL_' + key, that.langModule) + '</h3>';
                        data += '<ol>';
                        _.each(obj, function (entity) {
                            data += '<li>' + entity + '</li>';
                        });
                        data += '</ol>';
                    } else {
                        data += '<ul><li><span>' + app.lang.get('LBL_' + key, that.langModule);
                        data += '</span></li><li>';
                        data += obj + '</li></ul>';
                    }
                    data += '</div>';
                    if (!that.p1) {
                        data += '</div>';
                    }
                }
                pdone = false;
            }
        });
        return data;
    }
    function p1_check() {
        if (this.p1) {
            this.p1 = false;
            return '</div>';
        }
        return '';
    }
    function loc_check(imflag, im) {
        var data = '';
        var that = this;
        if (that.loc) {
            data += '<div class="span7">';
            if (imflag) {
                data += '<span>' + im + '</span>';
            } else {
                data += im;
            }
            data += '</div></div>';
            that.loc = false;
        }
        return data;
    }
</script>
{/literal}