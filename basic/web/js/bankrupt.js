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
        nextStep :function (pos) {
            var redirect_table = {
                "user-init":"grab",
                "grab":"pcheck",
                "pcheck":"user-init"
            };

            window.location.replace(window.location.origin+'/'+redirect_table[pos]);
        }
    };

}();

let pcheck = function () {
    return {
        check : function () {
            var is_validate = false;

            $("input[name=pserial]").parent().removeClass("has-error");
            $("input[name=pnumber]").parent().removeClass("has-error");

            if (!is_validate && (! $("input[name=pserial]").val() || $("input[name=pserial]").inputmask('unmaskedvalue').length != 4)){$("input[name=pserial]").parent().addClass("has-error"); is_validate = true;}
            if (!is_validate && (! $("input[name=pnumber]").val() || $("input[name=pnumber]").inputmask('unmaskedvalue').length != 6)){$("input[name=pnumber]").parent().addClass("has-error"); is_validate = true;}

            if (!is_validate) {
                $("[name=request]").prop("disabled", true);
                $("[name=request]").addClass("disabled");

                $("#log_result").fadeOut("slow", function () {
//                    $("#log_result").empty();
                });

                $.post(
                    window.location.href+"/check", {sid:webtools.getSession(), s:$("input[name=pserial]").val(), n:$("input[name=pnumber]").val()},
                    function (answer) {
                        if (parseInt(answer.error) === 200) {
                            if (answer.validate) {
                                $("#log_result").text("Паспорт действителен");
                            }else{
                                $("#log_result").text("Паспорт аннулирован");
                            }
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
                    $("#log_result").fadeIn("slow");
                });
            }
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
                window.location.href+"/getcapcha", {},
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
                    window.location.href + "/sendgrab",
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

$(document).ready(function () {
    if (window.location.pathname === '/grab'){
        grab.getcapcha();
    }
});

let userinit = function () {
    return {
        start : function () {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            $("#container").removeClass("has-error");
            if ( regex.test($("input[name=email]").val()) ) {
                $.post(
                    window.location.href + "/user-init/start", {
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
        }
    };
}();