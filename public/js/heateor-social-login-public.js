function heateorSlStrReplace(e,t,o){for(var a=0;a<e.length;a++)o=o.replace(new RegExp(e[a],"g"),t[a]);return o}function heateorSlCallAjax(e){"undefined"!=typeof jQuery&&e()}function heateorSlGetScript(e,t){var o=document.createElement("script");o.src=e;var a=document.getElementsByTagName("head")[0],r=!1;o.onload=o.onreadystatechange=function(){r||this.readyState&&"loaded"!=this.readyState&&"complete"!=this.readyState||(r=!0,t(),o.onload=o.onreadystatechange=null,a.removeChild(o))},a.appendChild(o)}function heateorSlGetElementsByClass(e,t){return e.getElementsByClassName?e.getElementsByClassName(t):function(e,t){null==t&&(t=document);for(var o=[],a=t.getElementsByTagName("*"),r=a.length,l=new RegExp("(^|\\s)"+e+"(\\s|$)"),n=0,i=0;n<r;n++)l.test(a[n].className)&&(o[i]=a[n],i++);return o}(t,e)}function heateorSlPopup(e){"undefined"!=typeof heateorSlSameTabLogin&&"1"==heateorSlSameTabLogin?location.href=e:window.open(e,"_blank","height=520,width=770,left=315,top=80,resizable,scrollbars,toolbar=0,personalbar=0,menubar=no,location=no,directories=no,status")}function heateorSlLoadingIcon(){jQuery(".heateor_sl_login_container").html('<img id="heateor_sl_loading_image" src="'+heateorSlLoadingImgPath+'" />')}function heateorSlInitiateLogin(e,t){var o=jQuery(e).parents("div.heateor_sl_login_container").find(".heateor_sl_social_login_optin");0==o.length||jQuery(o).is(":checked")?"facebook"==t?heateorSlPopup(heateorSlFacebookAuthUrl):"spotify"==t?heateorSlPopup(heateorSlSpotifyAuthUrl):"discord"==t?heateorSlPopup(heateorSlDiscordAuthUrl):"dropbox"==t?heateorSlPopup(heateorSlDropboxAuthUrl):"foursquare"==t?heateorSlPopup(heateorSlFoursquareAuthUrl):"disqus"==t?heateorSlPopup(heateorSlDisqusAuthUrl):"stackoverflow"==t?heateorSlPopup(heateorSlStackoverflowAuthUrl):"amazon"==t?heateorSlPopup(heateorSlAmazonAuthUrl):"reddit"==t?heateorSlPopup(heateorSlRedditAuthUrl):"github"==t?heateorSlPopup(heateorSlGithubAuthUrl):"kakao"==t?heateorSlPopup(heateorSlKakaoAuthUrl):"line"==t?heateorSlPopup(heateorSlLineAuthUrl):"microsoft"==t?heateorSlPopup(heateorSlLiveAuthUrl):"instagram"==t?heateorSlPopup(heateorSlInstagramAuthUrl):"mailru"==t?heateorSlPopup(heateorSlMailruAuthUrl):"steam"==t?heateorSlPopup(heateorSlSteamAuthUrl):"wordpress"==t?heateorSlPopup(heateorSlWordpressAuthUrl):"twitter"==t?heateorSlPopup(heateorSlTwitterAuthUrl):"github"==t?heateorSlPopup(heateorSlGithubAuthUrl):"kakao"==t?heateorSlPopup(heateorSlKakaoAuthUrl):"spotify"==t?heateorSlPopup(heateorSlSpotifyAuthUrl):"yahoo"==t?heateorSlPopup(heateorSlYahooAuthUrl):"dribbble"==t?heateorSlPopup(heateorSlDribbbleAuthUrl):("linkedin"==t&&heateorSlPopup(heateorSlLinkedinAuthUrl),"google"==t?heateorSlPopup(heateorSlGoogleAuthUrl):"twitch"==t?heateorSlPopup(heateorSlTwitchAuthUrl):"vkontakte"==t?heateorSlPopup(heateorSlVkontakteAuthUrl):"dribbble"==t&&heateorSlPopup(heateorSlDribbbleAuthUrl)):0<o.length&&jQuery(o).parent().css("color","red")}function heateorSlDisplayLoginIcon(e,t){if("undefined"!=typeof jQuery)for(var o=0;o<t.length;o++)jQuery("."+t[o]).css("display","block");else for(o=0;o<t.length;o++)for(var a=heateorSlGetElementsByClass(e,t[o]),r=0;r<a.length;r++)a[r].style.display="block"}function heateorSlValidateEmail(e){return/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(e)}function heateor_sl_save_email(e){var t=document.getElementById("heateor_sl_email").value.trim(),o=document.getElementById("heateor_sl_confirm_email").value.trim();return"save"!=e.id||heateorSlValidateEmail(t)?t!=o?(document.getElementById("heateor_sl_error").innerHTML="Email addresses do not match",void jQuery("#TB_ajaxContent").css("height","auto")):void heateorSlCallAjax(function(){heateorSlSaveEmail(e.id,t)}):(document.getElementById("heateor_sl_error").innerHTML=heateorSlEmailPopupErrorMsg,void jQuery("#TB_ajaxContent").css("height","auto"))}function heateorSlSaveEmail(e,t){document.getElementById("heateor_sl_error").innerHTML='<img src="'+heateorSlLoadingImgPath+'" />',jQuery.ajax({type:"POST",dataType:"json",url:heateorSlAjaxUrl,data:{action:"heateor_sl_save_email",elemId:e,email:t,id:heateorSlEmailPopupUniqueId},success:function(e){window.history.pushState({html:"html",pageTitle:"page title"},"","?done=1"),1==e.status&&e.message.response&&"success"==e.message.response?location.href=e.message.url:1==e.status&&"success"==e.message?location.href=heateorSlRegRedirectionUrl:1==e.status&&"cancelled"==e.message?tb_remove():1==e.status&&"verify"==e.message?document.getElementById("TB_ajaxContent").innerHTML="<strong>"+heateorSlEmailPopupVerifyMessage+"</strong>":0==e.status&&(document.getElementById("heateor_sl_error").innerHTML=e.message,jQuery("#TB_ajaxContent").css("height","auto"))},error:function(e,t,o){location.href=decodeURIComponent(heateorSlRedirectionUrl)}})}function heateorSlCapitaliseFirstLetter2(e){return e.charAt(0).toUpperCase()+e.slice(1)}var heateorSlLinkingRedirection;heateorSlFBKey="undefined"!=typeof heateorSlFBKey?heateorSlFBKey:"",heateorSlFBLang="undefined"!=typeof heateorSlFBLang?heateorSlFBLang:heateorSlDefaultLang,heateorSlFbIosLogin="undefined"!=typeof heateorSlFbIosLogin?heateorSlFbIosLogin:0,"function"!=typeof String.prototype.trim&&(String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g,"")}),void 0===heateorSlLinkingRedirection&&(heateorSlLinkingRedirection=""),heateorSlVerified&&heateorSlLoadEvent(function(){tb_show(heateorSlPopupTitle,heateorSlAjaxUrl)}),heateorSlEmailPopup&&heateorSlLoadEvent(function(){tb_show(heateorSlEmailPopupTitle,heateorSlEmailAjaxUrl)});var heateorSlCommentFormLogin=!1;