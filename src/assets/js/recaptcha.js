var cptFirst = $('.jsCpt').first();
var cptSiteKey = cptFirst.data('cpt-key');
var cptAction = cptFirst.data('cpt-action');

function resetRecaptchaToken() {
    if (typeof siteKey !== 'undefined' && typeof cptAction !== 'undefined') {
        grecaptcha.execute(cptSiteKey, {action: cptAction}).then(function (token) {
            $('.jsCpt').each(function () {
                $(this).val(token);
            })
        });
    }
}

grecaptcha.ready(function () {
    resetRecaptchaToken();
});

$(document).ajaxComplete(function() {
    resetRecaptchaToken();
});

