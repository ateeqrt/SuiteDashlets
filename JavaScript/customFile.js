
var modListLang = "";
var modLang;
var authenticated = false;
$(document).ready(function() {
    getModListLang();
    getModLang();
    window.storeZFID = function() {
        var res = false;
        $.ajax({
            type: "GET",
            dataType: 'json',
            crossDomain: false,
            timeout: 5000, 
            url: "index.php?module=rt_Tracker&action=functions&method=getZFID&sugar_body_only=true",

            success: function (result) {
                window.zfid = result;
            },
            error: function(e){

            if (e.errorThrown == 'Not Found') {
                e.message = 'RT CXM is not validated';
                e.status = (e.status) ? e.status : '404';
            }
            window.handleDashletCallError(e);
            window.notFound = true;
            }
        });

    }
    if (typeof google == "undefined") {
        $.getScript('cache/include/javascript/newGroupingRt.js', function() {
            callWebSocket();
            $.notify.addStyle('rtcxmchatstyle', {
                html: "<div><span data-notify-text/></div>",
                classes: {
                    base: {
                        "white-space": "pre",
                        "color": "#468847",
                        "background-color": "#DFF0D8",
                        "border-color": "#D6E9C6",
                        "font-weight": "bold",
                        "padding": "7px 10px",
                        "border-radius": "5px",
                        "white-space": "no-wrap",
                    },
                    newmessage: {
                        "font-size": "small",
                        "white-space": "normal",
                        "width": "100px",
                    },
                }
            });
            $.notify.addStyle('smtmsg', {
                html: "<div>" + "<div class='clearfix'>" + "<div class='title' data-notify-html='title' style='white-space:pre-wrap;font-size:12px'/>" + "<div class='buttons'>" + "<button class='yes' data-notify-text='button'></button>" + "</div>" + "</div>" + "</div>",
                classes: {
                    base: {
                        "white-space": "pre",
                        "background-color": "aliceblue",
                        "padding": "5px",
                        "width": "300px",
                    }
                }
            });
            $.notify.addStyle('newmessage', {
                html: "<div>" + "<div class='clearfix'>" + "<div class='title' data-notify-html='title' style='white-space:pre-wrap;font-size:12px'/>" + "</div>" + "</div>",
                classes: {
                    base: {
                        "font-size": "small",
                        "white-space": "normal",
                        "width": "100px",
                        "white-space": "pre",
                        "color": "#468847",
                        "background-color": "#DFF0D8",
                        "border-color": "#D6E9C6",
                        "font-weight": "bold",
                        "padding": "7px 10px",
                        "border-radius": "5px",
                        "white-space": "no-wrap",
                    }
                }
            });
        });
    }
    loadrtcxm();

    function isAuthenticated() {
        $.ajax({
            type: "GET",
            dataType: 'json',
            crossDomain: false,
            timeout: 5000, 
            url: "index.php?module=rt_Tracker&action=functions&method=dbFieldExists&table=rt_tracker&field=license_key&sugar_body_only=true",

            success: function (result) {
                if (result !== null && result == true) {
                    authenticated = true;
                    return true;
                }
                else {
                    authenticated = false;
                    return false;
                }
            },
            error: function(e){
                console.log(e);
                return false;
            }
        });
    }

    function getModListLang() {
        $.ajax({
            type: "GET",
            dataType: 'json',
            crossDomain: false,
            timeout: 5000, 
            url: "index.php?module=rt_Tracker&action=functions&method=getModListLang&sugar_body_only=true",

            success: function (result) {
                if (typeof result == 'string') {
                    modListLang = result;
                }

            },
            error: function(e){
                modListLang = "unfinished";
                console.log(e);
                window.handleDashletCallError(e);
            }
        });        

    }

    function getModLang() {
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

    function loadrtcxm() {
        if (authenticated == true) {
            loadCss();
            validateCxmUser();
            if (window.rtvalidatecxm)
                window.websocket();
        } else {
            setTimeout(function() {
                isAuthenticated();
                loadrtcxm();
            }, 500);
        }
    }

    function callWebSocket(interval) {
        if (window.rtvalidatecxm)
            window.websocket();
        else {
            if (typeof interval == 'undefined')
                interval = 1;
            var ms = 1000 * interval;
            setTimeout(function() {
                callWebSocket(interval + 2);
            }, ms);
        }
    }
});
window.handleDashletCallError = function(error) {
    var msg = {
        autoClose: false,
        level: 'error'
    };
    if (error && typeof error.message == 'string') {
        if (error.message.indexOf('LBL_') != -1)
            error.message = modLang[error.message];
        msg.messages = error.message;
    } else {
        msg.messages = 'Unknown Error';
    }
    if (error.status == 412 && !error.request.metadataRetry) {
        return;
        msg.messages = 'If this page does not reload automatically, please try to reload manually';
    }
    console.log('Failed: ' + error);
    if (typeof error.status != 'undefined') {
        if (error.status == 400) {
            if (typeof error.responseText != 'undefined') {
                if (error.responseText.indexOf('LBL_') != -1)
                    error.responseText = modLang[error.responseText];
                msg.messages = error.responseText;
            }
        }
        $.notify('rtCXM_config', msg);
    }
}, readCookie = function(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function validateCxmUser() {
    _apivalidate();
}

function validatePackage(callback, c) {
    if (typeof c == 'undefined')
        c = 1;
    var ms = 500 * c;
    setTimeout(function() {
        if (modListLang == "") {
            validatePackage(callback, c + 1);
        } else {

            if (modListLang == "unfinished") {
                $.notify("rtCXM_Package", {
                    level: 'warning',
                    title: 'Rebuild Cache',
                    messages: 'Please perform a <b>Repair and Rebuild</b> to Completely Uninstall <b>RT CXM</b>. Otherwise you will keep seeing this warning.'
                });
            } else {
                callback();
            }
        }
    }, ms);
}

function _apivalidate() {
    var callback = function() {
    $.ajax({
        type: "GET",
        dataType: 'json',
        crossDomain: false,
        timeout: 5000, 
        url: "index.php?module=rt_Tracker&action=functions&method=validateCXMUser&sugar_body_only=true",

        success: function (result) {
            if (typeof result == 'string')
                result = JSON.parse(result);
            if (result !== null && result["isValid"] !== null && result["cxmValid"] !== null) {
                window.rtvalidatecxm = (result.isValid && result.cxmValid) ? true : false;
            }
        },
        error: function(e){
            window.handleDashletCallError(e);
            window.rtvalidatecxm = undefined;
            setTimeout(function() {
                _apivalidate();
            }, 6000);
        }
    });  

    };
    validatePackage(callback);
}

function loadCss() {
    var callback = function() {
    $.ajax({
        type: "POST",
        dataType: 'json',
        crossDomain: false,
        timeout: 5000, 
        url: "index.php?module=rt_Tracker&action=functions&method=getCustomCSS&sugar_body_only=true",

        success: function (result) {
            $('head').append('<style type="text/css" id="rtcxm-style">' + result.data + '</style>');

        },
        error: function(e){
            console.log(e);
            window.handleDashletCallError(e);
            setTimeout(function() {
                loadCss();
            }, 6000);
        }
    });  

    };
    validatePackage(callback);
}
$(document).one('focus.textarea', '.autoExpand', function() {
    var savedValue = this.value;
    this.value = '';
    if (this.baseScrollHeight == undefined)
        this.baseScrollHeight = this.scrollHeight;
    this.value = savedValue;
}).on('input.textarea', '.autoExpand', function() {
    var minRows = this.getAttribute('data-min-rows') | 0,
        rows;
    this.rows = minRows;
    rows = (this.scrollHeight - this.baseScrollHeight) / 17;
    this.rows = minRows + rows;
    if (rows < 0) {
        this.rows = 1;
    }
    if (this.rows === 1) {
        $('.conversation').css('height', '241px');
        $('.btb').css('margin-top', '-3px');
    } else if (this.rows === 2) {
        $('.conversation').css('height', '230px');
        $('.btb').css('margin-top', '3px');
        updateScroll();
    } else if (this.rows >= 3) {
        $('.conversation').css('height', '212px');
        $('.btb').css('margin-top', '13px');
        updateScroll();
    }
});

function updateScroll() {
    var height = 0;
    $('.conversation div').each(function(i, val) {
        height += parseInt($(this).height());
    });
    height += '';
    $('.conversation').animate({
        scrollTop: height
    });
} 