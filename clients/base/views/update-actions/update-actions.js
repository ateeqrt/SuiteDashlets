/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
({
    extendsFrom: 'CreateView',
    conflictNames: undefined,
    initialize: function (options) {
        options.meta = _.extend({}, app.metadata.getView(null, 'update-actions'), options.meta);
        this._super('initialize', [options]);
        var that = this;
        this.conflictNames = [];
        this.model.on('change', function () {
            var attributes = that.model.attributes;
            if (attributes.conflict && attributes.conflictFields && attributes.conflictData && attributes.conflictSugarData) {
                var conflictFieldsCount = attributes.conflictFields.length;
                if (conflictFieldsCount > 0) {
                    var conflictFields = attributes.conflictFields;
                    var itr = 0;
                    $.each(conflictFields, function (i, f) {
                        if (f == 'last_name' || f == 'first_name') {
                            that.customToggle('name');
                        } else if (f.indexOf('address') != -1) {
                            that.customToggle('address');
                        } else if ($('#conflictElement-' + f).length == 0) {
                            var fieldElementArr = $('div.record-cell[data-name=' + f + ']');
                            var fieldElement = $(fieldElementArr)[fieldElementArr.length - 1];
                            var conflictElement = '<a class="rowaction btn btn-primary pull-right" id="conflictElement-' + f + '" href="javascript:void(0);" name="conflict_btn" track="" style="margin-bottom:2%">Sugar</a>';
                            $(fieldElement).prepend(conflictElement);
                            $('#conflictElement-' + f).on('click', function (evt) {
                                that.toggler(evt);
                            });
                        }
                    });
                }
            }
            if (attributes.rtcxm_picture) {
                var toggleelement = '<span class="image_btn pictoggle fa fa-toggle-left "></span>';
                var imagebtn = $('.image_field.image_edit')[0];
                $(imagebtn).append(toggleelement);
                $('a[name=save_button_rtcxm]').on('click', function (evt) {
                    if (attributes.rtcxm_image) {
                        var rtcxm = {
                            url: attributes.rtcxm_image,
                            id: attributes.id,
                            module: attributes._module,
                            field: 'picture'
                        };
                        var prefsURL = App.api.buildURL('rtCXM/SaveFilePut', window.btoa(JSON.stringify(rtcxm)), null, {
                            oauth_token: App.api.getOAuthToken()
                        });
                        App.api.call('GET', prefsURL, null, {
                            success: _.bind(function (result) {
                                console.log(result);
                            })
                        });
                    }
                });
                if ($('.image_field.image_edit img').length > 0)
                    $('.image_field.image_edit img').attr('id', 'primary_img');
                else
                    $('.image_preview .fa-plus').attr('id', 'primary_img');
                $('.image_btn.pictoggle').click(function () {
                    if ($('#primary_img').is(':visible')) {
                        $('#primary_img').css('display', 'none');
                        if ($('#rtcxm_img').length == 0) {
                            var imagelement = '<img id="rtcxm_img" src="' + attributes.rtcxm_picture + '">';
                            $('.image_field.image_edit .image_preview').append(imagelement);
                        } else {
                            $('#rtcxm_img').css('display', 'block');
                        }
                        attributes.rtcxm_image = attributes.rtcxm_picture;
                    } else {
                        attributes.rtcxm_image = '';
                        $('#rtcxm_img').css('display', 'none');
                        $('#primary_img').css('display', 'block');
                    }
                });
            }
        });
        setTimeout(function () {
            var attribute = {};
            attribute["rtcxm_image"] = '';
            that.model.set(attribute);
        }, 200);
    },
    customToggle: function (type) {
        var that = this;
        if (type == 'name')
            var fields = ['first_name', 'last_name'];
        if (type == 'address') {
            var fields = ['primary_address_country', 'primary_address_state', 'primary_address_city', 'primary_address_street'];
            $('div.record-cell[data-name=primary_address]').addClass('custom_form');
        }
        var conflictFields = this.model.attributes.conflictFields;
        $.each(fields, function (i, f) {
            if ($.inArray(f, conflictFields) != -1 && $('#conflictElement-' + f).length == 0) {
                if (type == 'name') {
                    var style1 = '75%';
                    var style2 = 'margin-bottom:-18%;margin-top:12.5px;width:33px';
                    if (f == 'last_name') {
                        style1 = '90%';
                        style2 = 'margin-bottom:-19%;margin-top:12.5px;width:33px;margin-right:-19%;';
                    }
                    $('input[name=' + f + ']').css('width', style1);
                    var fieldElement = $('span.record-cell[data-name=' + f + ']');
                    $('span.record-cell[data-name=' + f + ']').css('width', '44%');
                    var conflictElement = '<a class="rowaction btn btn-primary pull-right" id="conflictElement-' + f + '" href="javascript:void(0);" name="conflict_btn" track="" style="' + style2 + '">Sugar</a>';
                    $(fieldElement).prepend(conflictElement);
                } else {
                    var fieldElement = $('input[name=' + f + ']');
                    var conflictElement = '<a class="rowaction btn btn-primary pull-right" id="conflictElement-' + f + '" href="javascript:void(0);" name="conflict_btn" track="" style="width:33px">Sugar</a>';
                    $(conflictElement).insertBefore(fieldElement);
                }
                $('#conflictElement-' + f).on('click', function (evt) {
                    that.toggler(evt);
                });
            }
        });
    },
    toggler: function (evt) {
        var elementId = evt.currentTarget.attributes['id'].value;
        var buttonText = evt.currentTarget.text;
        var newText = 'Sugar';
        if (buttonText == 'Sugar')
            newText = 'RtCXM';
        $('#' + elementId).text(newText);
        var fieldName = elementId.split('-')[1];
        var conflictFields = this.model.attributes.conflictFields;
        var conflictData = this.model.attributes.conflictData;
        var conflictSugarData = this.model.attributes.conflictSugarData;
        var index = $.inArray(fieldName, conflictFields);
        if (index != -1) {
            var tag = 'input';
            if ($('input[name=' + fieldName + ']').length == 0)
                tag = 'textarea';
            var insert = conflictSugarData[index];
            if (newText == 'Sugar')
                insert = conflictData[index];
            $(tag + '[name=' + fieldName + ']').val(insert);
            this.model.attributes[fieldName] = insert;
        }
    },
})
