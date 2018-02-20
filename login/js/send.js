$(document).ready(function () {
    "use strict";
    $("#submit").click(function () {

        var from = $("#from").val(), email = $("#email").val(), amount = $("#amount").val();

        if ((email === "") || (amount === "")) {
            $("#message").html("<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Please fill in both fields.</div>");
        } else {
            $.ajax({
                type: "POST",
                url: "login/send.php",
                data: "email=" + email + "&amount=" + amount + "&from=" + from, 
                dataType: 'JSON',
                success: function (html) {
                    //console.log(html.response + ' ' + html.username);
                    if (html.response === 'true') {
                        //location.assign("../index.php");
                       location.reload();
                        $("#message").html(html.response);
                    } else {
                        $("#message").html(html.response);
                    }
                },
                error: function (textStatus, errorThrown) {
                    console.log(textStatus);
                    console.log(errorThrown);
                },
                beforeSend: function () {
                    $("#message").html("<img src='login/images/ajax-loader.gif'></p>");
                }
            });
        }
        return false;
    });
});
