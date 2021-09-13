function selectAboGift(el) {
    $(".related-product-list .buybox--button.btn").addClass('is--disabled').attr('disabled', 'disabled');
    $(".abo-variant--group").find(".variant--option label:eq(0)").click();
    $(el).addClass('active');
}

function selectAboNotGift(el) {
    $(".related-product-list .buybox--button.btn").removeClass('is--disabled').removeAttr('disabled', 'disabled');
    $(".abo-variant--group").find(".variant--option label:eq(1)").click();
    $(el).addClass('active');
}

$(document).ready(function () {
    $(document).on('click', '.abo-buybox--form .buybox--button.btn', function(e) {
        setTimeout(function() {
            if($('.abo-variant--group .variant--option input[type=radio]:eq(0)').prop('checked')) {
                $(".buybox--button.btn").addClass('is--disabled').attr('disabled', 'disabled');
                var inputs = $(".abo-gift--register input");
                for(var i=0; i<inputs.length; i++) {
                    $(inputs[i]).addClass('input--disabled').attr('disabled', 'disabled');
                }
                $(".abo-gift--register select").addClass('is--disabled').attr('disabled', 'disabled');
            }
        }, 500);
    });
});
