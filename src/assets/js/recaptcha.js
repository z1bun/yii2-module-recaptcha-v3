var cptFirst = $('.jsCpt').first();
var cptSiteKey = cptFirst.data('cpt-key');
var cptAction = cptFirst.data('cpt-action');

function resetRecaptchaToken() {
    if (typeof cptSiteKey !== 'undefined' && typeof cptAction !== 'undefined') {
        setTimeout(function(){
            grecaptcha.execute(cptSiteKey, {action: cptAction}).then(function (token) {
                $('.jsCpt').each(function () {
                    $(this).val(token);
                })
            });
        }, 10000);
    }
}

grecaptcha.ready(function () {
    resetRecaptchaToken();
});

$(document).ajaxComplete(function() {
    resetRecaptchaToken();
});

