$(document).ready(function () {
    "use strict";
    $("#dsubmit").click(function () {

        var amount = $("#deposit").val(), username = $("#dusername").val();

        if ((amount === "")) {
            $("#dmessage").html("<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Please fill in the field.</div>");
        } else {
            $.ajax({
                type: "POST",
                url: "login/deposit.php",
                data: "deposit=" + amount + "&dusername=" + username, 
                dataType: 'JSON',
                success: function (html) {
                    //console.log(html.response + ' ' + html.username);
                    if (html.response === 'true') {
                        //location.assign("../index.php");
                       location.reload();
                        $("#dmessage").html(html.response);
                    } else {
                        $("#dmessage").html(html.response);
                    }
                },
                error: function (textStatus, errorThrown) {
                    console.log(textStatus);
                    console.log(errorThrown);
                },
                beforeSend: function () {
                    $("#dmessage").html("<img src='login/images/ajax-loader.gif'></p>");
                }
            });
        }
        return false;
    });
});
