"use strict";

let webtools = function (){
    function __getCookie(name) {
        var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    return {
        getSession : function () {
            return __getCookie("sid");
        },
        setCurrencyFormat : function (n) {
            var _n = parseFloat(n).toFixed(2);
            var _nparts = _n.split('.');
            return _nparts[0]+' руб. '+_nparts[1]+' коп.';
        },
        nextStep :function (pos, sid = null) {
            var redirect_table = {
                "user-init":"grab",
                "grab":"pcheck",
                "pcheck":"egrul",
                "egrul":"docs",
                "docs":"user-init",
            };

            var url = window.location.origin+'/'+redirect_table[pos];

            if (sid) {
                url = url+'?sid='+sid;
            }

            window.location.replace(url);
        }
    };

}();

let pcheck = function () {
    return {
        check : function () {
            var is_validate = false;

            $("input[name=pserial]").parent().removeClass("has-error");
            $("input[name=pnumber]").parent().removeClass("has-error");
            $("input[name=captcha_str]").parent().removeClass("has-error");

            if (!is_validate && (! $("input[name=pserial]").val() || $("input[name=pserial]").inputmask('unmaskedvalue').length != 4)){$("input[name=pserial]").parent().addClass("has-error"); is_validate = true;}
            if (!is_validate && (! $("input[name=pnumber]").val() || $("input[name=pnumber]").inputmask('unmaskedvalue').length != 6)){$("input[name=pnumber]").parent().addClass("has-error"); is_validate = true;}
            if (!is_validate && (! $("input[name=captcha_str]").val() || $("input[name=captcha_str]").inputmask('unmaskedvalue').length != 6)){$("input[name=captcha_str]").parent().addClass("has-error"); is_validate = true;}

            if (!is_validate) {
                $("[name=request]").prop("disabled", true);
                $("[name=request]").addClass("disabled");
                $("#log_result").fadeOut("slow");

                $.post(
                    window.location.origin+window.location.pathname+"/check", {sid:webtools.getSession(), s:$("input[name=pserial]").val(), n:$("input[name=pnumber]").val(),c:$("input[name=captcha_str]").val(), uid:$("#captcha").data("uid"), jid:$("#captcha").data("jid")},
                    function (answer) {
                        if (answer) {
                            if (parseInt(answer.error) === 200) {
                                if (answer.validate) {
                                    $("#log_result").text(answer.data);
                                } else {
                                    $("#log_result").text(answer.data);
                                }
                            }
                            if (parseInt(answer.error) === 500) {
                                $("#log_result").text("Сервис временно недоступен");
                            }
                            if (parseInt(answer.error) === 400) {
                                $("#log_result").text("Не веррно введен код с картинки");
                            }
                        } else {
                            $("#log_result").text("Сервис временно недоступен");
                        }
                    }
                ).fail(function(r) {
                    $("#log_result").text("Сервис временно недоступен");
                    console.log(r.responseText);
                }).always(function () {
                    $("[name=request]").prop("disabled", false);
                    $("[name=request]").removeClass("disabled");
                    $("#log_result").fadeIn("slow");
                    pcheck.getcaptcha();
                });
            }
        },
        getcaptcha : function () {
            $("#captcha").attr("src", "");
            $("#captcha").data("jid", "");
            $("#captcha").data("uid", "");
            $("[name=captcha_str]").val('');
            $("[name=request]").prop("disabled", true);
            $("[name=request]").addClass("disabled");

            $.post(
                window.location.origin+window.location.pathname+"/getcaptcha", {},
                function (answer) {
                    if (parseInt(answer.error) === 200) {
                        $("#captcha").attr("src", answer.captcha);
                        $("#captcha").data("jid", answer.jid);
                        $("#captcha").data("uid", answer.uid);
                    }
                    if (parseInt(answer.error) === 500) {
                        $("#log_result").text("Сервис временно недоступен");
                    }
                }
            ).fail(function(r) {
                $("#log_result").text("Сервис временно недоступен");
                console.log(r.responseText);
            }).always(function () {
                $("[name=request]").prop("disabled", false);
                $("[name=request]").removeClass("disabled");
            });
        }
    };
}();

let grab = function () {
    return {
        getcapcha : function () {
            $("#captcha").attr("src", "");
            $("[name=captcha_str]").data("sid", "");
            $("[name=request]").prop("disabled", true);
            $("[name=request]").addClass("disabled");
            $("[name=captcha_str]").val('');

            $.post(
                window.location.origin+window.location.pathname+"/getcapcha", {},
                function (answer) {
                    if (parseInt(answer.error) === 200) {
                        $("#captcha").attr("src", answer.captcha);
                        $("[name=captcha_str]").data("sid", answer.cookies);
                        $("[name=request]").prop("disabled", false);
                        $("[name=request]").removeClass("disabled");
                    }
                    if (parseInt(answer.error) === 500) {
                        $("#captcha").attr("alt", "Сервис временно недоступен");
                    }
                }
            ).fail(function(r) {
                $("#captcha").attr("alt", "Сервис временно недоступен");
                $("#log_result").text(r.responseText);
            });
        },
        send_grab : function () {
            $("#log_result").fadeOut("slow", function () {
                $("#log_result").empty();
            });
            $("[name=request]").prop("disabled", true);
            $("[name=request]").addClass("disabled");
            var is_validate = false;

            if (!is_validate && ! $("input[name=last_name]").val() ){$("input[name=last_name]").parent().addClass("has-error"); is_validate = true;}
            if (!is_validate && ! $("input[name=first_name]").val() ){$("input[name=first_name]").parent().addClass("has-error"); is_validate = true;}
            if (!is_validate && ! $("input[name=patronymic]").val() ){$("input[name=patronymic]").parent().addClass("has-error"); is_validate = true;}
            if (!is_validate && ! $("input[name=date]").val() ){$("input[name=date]").parent().addClass("has-error"); is_validate = true;}
            if (!is_validate && ! $("[name=captcha_str]").val() ){$("input[name=captcha_str]").parent().addClass("has-error"); is_validate = true;}

            if (!is_validate) {
                $.post(
                    window.location.origin+window.location.pathname + "/sendgrab",
                    {
                        last_name: $("input[name=last_name]").val(),
                        first_name: $("input[name=first_name]").val(),
                        patronymic: $("input[name=patronymic]").val(),
                        date: $("input[name=date]").val(),
                        sid: $("[name=captcha_str]").data("sid"),
                        code: $("[name=captcha_str]").val(),
                        s: webtools.getSession()
                    },
                    function (answer) {
                        $("[name=request]").prop("disabled", false);
                        $("[name=request]").removeClass("disabled");

                        if (null !== answer && parseInt(answer.error) === 200) {
                            if ( answer.data.hasOwnProperty("cnt") ) {
                                $("#log_result").append("Найдено задолжностей:" + answer.data.cnt + " на общую сумму:" + webtools.setCurrencyFormat(answer.data.sum));
                            } else {
                                $("#log_result").append("Задолжностей не найдено");
                            }
                        } else if (null !== answer && parseInt(answer.error) === 400){
                            $("#log_result").append(answer.data);
                            grab.getcapcha();
                        } else {
                            $("#log_result").append("Сервис временно недоступен");
                        }

                        $("#log_result").fadeIn();
                    }
                ).fail(function (r) {
                    $("#log_result").text(r.responseText);
                });
            } else {
                $("[name=request]").prop("disabled", false);
                $("[name=request]").removeClass("disabled");
            }
        },
    };
}();

let userinit = function () {
    var timerId = null;

    return {
        start : function () {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            $("#container").removeClass("has-error");
            if ( regex.test($("input[name=email]").val()) ) {
                $.post(
                    window.location.origin + "/user-init/start", {
                        email: $("input[name=email]").val(),
                        sid: $("[name=email]").data("sid")
                    },
                    function (result) {
                        if (result.hasOwnProperty('redirect')){
                            document.cookie = 'sid='+$("[name=email]").data("sid");
                            webtools.nextStep('user-init');
                        }
                    }
                ).fail(function (r) {
                    console.log(r.responseText);
                });
            } else {
                $("#container").addClass("has-error");
            }
        },
        search : function () {
            if (timerId) {clearTimeout(timerId);}
            $(".saved-session-holder").fadeOut("slow");
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if ( regex.test($("input[name=email]").val()) ) {
                timerId = setTimeout(function () {
                    $.post(
                        window.location.origin + "/user-init/getsession", {
                            email: $("input[name=email]").val(),
                        },
                        function (sessions) {
                            $("#saved_session").empty();
                            if (sessions.length > 0) {
                                $(sessions).each(function (i, o) {
                                    $("#saved_session").append("<div class='saved-session' data-sid='" + o.sid + "'>" + o.rdate + "</div>");
                                    if (i === sessions.length-1){
                                        $(".saved-session-holder").fadeIn("slow");
                                        $(".saved-session").click(function () {
                                            webtools.nextStep('user-init', $(this).data("sid"));
                                        });
                                    }
                                });
                            }
                        }
                    ).fail(function (r) {
                        console.log(r.responseText);
                    });
                }, 1000);
            }
        },
    };
}();

let egrul = function () {
    return {
        check: function () {
            $("#container").removeClass("has-error");
            if ( $("input[name=inn]").inputmask('unmaskedvalue') ) {
                $.post(
                    window.location.origin+window.location.pathname + "/check", {
                        inn: $("input[name=inn]").inputmask('unmaskedvalue')
                    },
                    function (result) {
                        if (result.hasOwnProperty("n")){
                            $("#egrul_name").text(result.n);
                            $("#egrul_attr").text("ОГРНИП: "+result.o+" , ИНН: "+result.i+" , Дата присвоения ОГРНИП: "+result.r);
                        }
                    }
                ).fail(function (r) {
                    console.log(r.responseText);
                });
            } else {
                $("#container").addClass("has-error");
            }
        }
    };
}();


$(document).ready(function () {
    if (window.location.pathname === '/grab'){
        grab.getcapcha();
        if ($("input[name=patronymic]").data('summ') && $("input[name=patronymic]").data('bdate')){
            $("input[name=date]").val($("input[name=patronymic]").data('bdate'));
            $("#log_result").append("Найдены задолжноси на общую сумму:" + $("input[name=patronymic]").data('summ'));
            $("#log_result").fadeIn("slow");
        }
    }

    if (window.location.pathname === '/pcheck'){
        pcheck.getcaptcha();
        if ($("input[name=pserial]").val() && $("input[name=pnumber]").val()){
            $("#log_result").text("По Вашему запросу о действительности паспорта РФ "+$("input[name=pserial]").inputmask("unmaskedvalue")+" № "+$("input[name=pnumber]").inputmask("unmaskedvalue")+" получен ответ о том, что данный паспорт «Cреди недействительных не значится».");
            $("#log_result").fadeIn("slow");
        }
    }
});
