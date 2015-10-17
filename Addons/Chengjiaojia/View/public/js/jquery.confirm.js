/**
 * Created by yf on 15/9/4.
 */
/*!
 * jquery.confirm.js
 * @author ydr.me
 * @version  1.0
 */




/**
 * v1.0 2013年11月23日20:45:36
 * 		构造
 * 		2014年2月18日23:06:27
 * 		更新CSS布局
 */


(function($, undefined) {
    var _,
        prefix = "jquery_confirm____",
    // 是否加载了css
        hasLoadCss = 0,
    // 空函数
        emptyFn = function() {},
    // 确认框队列
    // confirmQueue = [],
        defaults = {
            // 样式
            css: "http://festatic.aliapp.com/css/jquery.confirm/default.min.css?v=" + Math.ceil(new Date() / 86400000),
            // 确认框内容
            content: "确认吗？",
            // 确认按钮文字
            sureButton: "确认",
            // 取消按钮文字
            cancelButton: "取消",
            // 位置
            position: {},
            // 自动打开
            autoOpen: false,
            // 动画持续时间
            duration: 123,
            // 打开确认框回调
            onopen: emptyFn,
            // 单击了确认或者取消回调
            onclick: emptyFn,
            // 确认回调
            onsure: emptyFn,
            // 取消回调
            oncancel: emptyFn,
            // 关闭确认框回调
            onclose: emptyFn
        },
        $bg, $confirm, $body, $sure, $cancel, callback = emptyFn,
        options;


    $bg = $('<div class="' + prefix + 'bg"></div>').appendTo("body");
    $confirm = $('<div class="' + prefix + '" style="display:none"><div class="' + prefix + 'body"></div><div class="' + prefix + 'footer"><button class="' + prefix + 'sure">确认</button><button class="' + prefix + 'cancel">取消</button></div></div>').appendTo("body");
    $body = $("." + prefix + "body", $confirm);
    $sure = $("." + prefix + "sure", $confirm);
    $cancel = $("." + prefix + "cancel", $confirm);


    $.confirm = function() {
        var args = arguments,
            argL = args.length;

        // ""
        if (_isStrOrNum(args[0])) {
            options = $.extend({}, defaults, {
                content: args[0]
            });
            options.onclick = $.isFunction(args[1]) ? args[1] : emptyFn;
            // 加载样式
            if (!hasLoadCss) {
                $('<link rel="stylesheet" href="' + options.css + '" />').appendTo("head").load(function() {
                    hasLoadCss = 1;
                    _open();
                });
            } else {
                _open();
            }
        }
        // object
        else if ($.type(args[0]) == "object") {
            options = $.extend({}, defaults, args[0]);
            // 加载样式
            if (!hasLoadCss) {
                $('<link rel="stylesheet" href="' + options.css + '" />').appendTo("head").load(function() {
                    hasLoadCss = 1;
                    if (options.autoOpen) {
                        _open();
                    }
                });
            } else {
                if (options.autoOpen) {
                    _open();
                }
            }
        }
    }

    // 单击确认
    $sure.click(function() {
        options.onsure();
        _close(function() {
            options.onclick(true);
        });
    });


    // 单击取消
    $cancel.click(function() {
        options.oncancel();
        _close(function() {
            options.onclick(false);
        });
    });


    /**
     * 打开确认框
     * @return {undefined}
     * @version 1.0
     * 2013年10月23日15:49:35
     */

    function _open() {
        $bg.fadeIn(options.duration, function() {
            var theW = $confirm.width(),
                theH = $confirm.height(),
                winW = $(window).width(),
                winH = $(window).height(),
                theL = (winW - theW) / 2,
                theT = (winH - theH) / 3;

            $body.html(options.content);
            $sure.html(options.sureButton);
            $cancel.html(options.cancelButton);

            if (options.position.left !== undefined) theL = options.position.left;
            if (options.position.top !== undefined) theT = options.position.top;

            $confirm.css({
                left: theL,
                top: theT
            }).fadeIn(options.duration, function() {
                options.onopen();
            });
        });
    }

    /**
     * 关闭确认框
     * @param {Function} callback 关闭回调
     * @return {undefined}
     * @version 1.0
     * 2013年10月23日15:48:50
     */

    function _close(callback) {
        $confirm.fadeOut(options.duration, function() {
            $bg.fadeOut(options.duration, function() {
                options.onclose();
                callback();
            });
        });
    }


    /**
     * 判断值是否为字符串或者数值
     * @param  {String/Number} 字符串或数值
     * @return {Boolean}
     * @version 1.0
     * 2013年9月23日15:23:04
     */

    function _isStrOrNum(val) {
        return $.type(val) == "string" || $.type(val) == "number";
    }

    $.confirm.defaults = defaults;
})($);
