({
    // className: 'list-view',
    extendsFrom: 'ListView',
    isValid: false,
    initialize: function (options) {
        this._super('initialize', [options]);
    },


    _render: function () {
        var self = this;
        var prefsURL = app.api.buildURL('validateCXMModuleLicense/prefs/', null, null, {
            oauth_token: app.api.getOAuthToken()
        });
        app.api.call('POST', prefsURL, null, {
            success: _.bind(function (result) {
                if (result == true) {
                    self._super('_render');
                }
                else {
                    var msg = {autoClose: false, level: 'error'};
                    msg.messages = result;
                    app.alert.show('rtCXM_config', msg);
                }
            }, this),

            error: _.bind(function (e) {
                console.log(e);
                this.configError(e);
            }, this)
        });
    },
    configError: function (error) {
        var msg = {autoClose: false, level: 'error'};
        if (error && _.isString(error.message)) {
            msg.messages = error.message;
        }
        if (error.status == 412 && !error.request.metadataRetry) {
            msg.messages = 'If this page does not reload automatically, please try to reload manually';
        } else {
            app.alert.show('rtCXM_config', msg);
        }
        if (error && _.isString(error.message)) {
            msg.messages = error.message;
        }
        app.alert.dismiss('rtCXM_config');
        app.logger.error('Failed: ' + error);
        if (typeof error.status != 'undefined') {
            if (error.status == 400) {
                if (typeof error.responseText != 'undefined') {
                    var msg = {autoClose: false, level: 'error'};
                    msg.messages = error.responseText;
                    app.alert.show('rtCXM_config', msg);
                }
            }
        }
    },
})
