$(document).ready(function () {
    "use strict";
    $("#distribute").click(function () {
            $.ajax({
                type: "POST",
                url: "login/interest.php",
                dataType: 'JSON',
                success: function (html) {
                        $("#modal").html(html.response);
                        $("#sent").modal('show');
                },
                error: function (textStatus, errorThrown) {
                    console.log(textStatus);
                    console.log(errorThrown);
                },
                beforeSend: function () {
                    $("#modal").html("<p style=\"text-align: center;\"><img src='login/images/ajax-loader.gif'></p>");
                },
            });
        return false;
    });
});
