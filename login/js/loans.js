$(document).ready(function () {
    "use strict";
    $("#submit").click(function () {

        var amount = $("#famount").val(), reason = $("#reason").val();

            $.ajax({
                type: "POST",
                url: "login/loans.php",
                data: "amount=" + amount + "&reason=" + reason,
                dataType: 'JSON',
                success: function (html) {
                        $("#message").html(html.response);
                        $('#request').modal('hide');
                        $('#sent').modal('show');
                },
                error: function (textStatus, errorThrown) {
                    console.log(textStatus);
                    console.log(errorThrown);
                },
            });
        return false;
    });

    $("#pay").click(function () {

            $.ajax({
                type: "POST",
                url: "login/loanpay.php",
                dataType: 'JSON',
                success: function (html) {
                        $("#paymessage").html(html.response);
                },
                error: function (textStatus, errorThrown) {
                    console.log(textStatus);
                    console.log(errorThrown);
                },
                beforeSend: function () {
                    $("#paymessage").html("<p style=\"text-align: center;\"><img src='login/images/ajax-loader.gif'></p>");
                },
            });
        return false;
    });
});
