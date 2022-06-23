import $ from "jquery";


$(function () {
    $(".form-container form").submit(function (e) {
        e.preventDefault();
        const form = $(this).serialize();
        $(".form-container form button[type=submit]").html(`<div class="spinner-border" role="status"></div>`)
        $.ajax({
            url: "./contact-us.php",
            method: 'POST',
            dataType: 'json',
            data: form,
            success: function (response) {
                let alertDiv = `<div class="alert alert-primary text-center" role="alert">Votre message a été envoyé! Merci.</div>`;
                $('.alerts-wrapper').html(alertDiv);
                $(".alerts-wrapper").show("slow");
                $(".form-container form").trigger("reset");
                $(".form-container form button[type=submit]").html("Envoyer")
                setTimeout(function () {
                    $(".alerts-wrapper").hide("slow");
                }, 5000)
            },
            error: function (err) {
                console.log(err);
                $(".contact-page form input").val('');
                $(".contact-page form textarea").val('');
                let alertDiv = `<div class="alert alert-danger text-center" role="alert">Ooops! Votre message n'a pas été envoyé. Veuillez réessayer plus tard.</div>`;
                $('.alerts-wrapper').html(alertDiv);
                $(".alerts-wrapper").show("slow");
                $(".form-container form button[type=submit]").html("Envoyer")
                setTimeout(function () {
                    $(".alerts-wrapper").hide("slow");
                }, 5000)
            }
        });

    });
});