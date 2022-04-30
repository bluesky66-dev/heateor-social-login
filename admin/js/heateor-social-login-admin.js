var heateorSlReferrer = null,
    heateorSlReferrerVal = "",
    heateorSlReferrerTabId = "";

function heateorSlEmailPopupOptions(e) {
    jQuery(e).is(":checked") ? jQuery("#heateor_sl_email_popup_options").css("display", "block") : jQuery("#heateor_sl_email_popup_options").css("display", "none")
}

function heateorSlCommentingOptions(e) {
    jQuery(e).is(":checked") ? jQuery("#heateor_sl_commenting_extra").css("display", "none") : jQuery("#heateor_sl_commenting_extra").css("display", "table-row-group")
}

function heateorSlSetReferrer(e) {
    jQuery(heateorSlReferrer).val(heateorSlReferrerVal.substring(0, heateorSlReferrerVal.indexOf("#") > 0 ? heateorSlReferrerVal.indexOf("#") : heateorSlReferrerVal.length) + e)
}
jQuery(document).ready(function() {
    jQuery("#tabs").tabs(), heateorSlReferrer = jQuery("input[name=_wp_http_referer]"), heateorSlReferrerVal = jQuery("input[name=_wp_http_referer]").val(), (heateorSlReferrerTabId = location.href.indexOf("#") > 0 ? location.href.substring(location.href.indexOf("#"), location.href.length) : "") && heateorSlSetReferrer(heateorSlReferrerTabId), jQuery("#tabs ul a").click(function() {
        heateorSlSetReferrer(jQuery(this).attr("href"))
    }), jQuery("#heateor_sl_gdpr_enable").click(function() {
        jQuery(this).is(":checked") ? jQuery("#heateor_sl_gdpr_options").css("display", "table-row-group") : jQuery("#heateor_sl_gdpr_options").css("display", "none")
    }), jQuery("#heateor_sl_redirection_column").find("input[type=radio]").click(function() {
        jQuery(this).attr("id") && "heateor_sl_redirection_custom" == jQuery(this).attr("id") ? jQuery("#heateor_sl_redirection_url").css("display", "block") : jQuery("#heateor_sl_redirection_url").css("display", "none")
    }), jQuery("#heateor_sl_redirection_custom").is(":checked") ? jQuery("#heateor_sl_redirection_url").css("display", "block") : jQuery("#heateor_sl_redirection_url").css("display", "none"), jQuery("#heateor_sl_register_redirection_column").find("input[type=radio]").click(function() {
        jQuery(this).attr("id") && "heateor_sl_register_redirection_custom" == jQuery(this).attr("id") ? jQuery("#heateor_sl_register_redirection_url").css("display", "block") : jQuery("#heateor_sl_register_redirection_url").css("display", "none");
    }), jQuery("#heateor_sl_register_redirection_custom").is(":checked") ? jQuery("#heateor_sl_register_redirection_url").css("display", "block") : jQuery("#heateor_sl_register_redirection_url").css("display", "none"), jQuery(".heateor_sl_help_bubble").attr("title", heateorSlHelpBubbleTitle),jQuery(".heateor_sl_help_bubble").click(function(){jQuery("#"+jQuery(this).attr("id")+"_cont").toggle(500)}),jQuery("#heateor_sl_fb_comment_switch_wp").keyup(function() {
        jQuery(this).prev("span").remove(), "" == jQuery(this).val().trim() ? jQuery(this).before( '<span style="color:red">This cannot be blank</span>' ) : jQuery(this).val().trim() == jQuery("#heateor_sl_fb_comment_switch_fb").val().trim() && jQuery(this).before( '<span style="color:red">This cannot be same as text on "Switch to Facebook Commenting" button</span>' )
    }), jQuery("input#heateor_sl_enable_commenting, input#heateor_sl_login_enable, input#heateor_sl_counter_enable").click(function() {
        jQuery(this).is(":checked") ? jQuery("div#tabs").css("display", "block") : jQuery("div#tabs").css("display", "none")
    }), jQuery("#heateor_sl_fb_comment_switch_fb").keyup(function() {
        jQuery(this).prev("span").remove(), "" == jQuery(this).val().trim() ? jQuery(this).before( '<span style="color:red">This cannot be blank</span>' ) : jQuery(this).val().trim() == jQuery("#heateor_sl_fb_comment_switch_wp").val().trim() && jQuery(this).before( '<span style="color:red">This cannot be same as text on "Switch to WordPress Commenting" button</span>' )
    })
}), jQuery("html, body").animate({
    scrollTop: 0
});