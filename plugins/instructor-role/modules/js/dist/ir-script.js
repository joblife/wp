!function(e){var r={};function t(n){if(r[n])return r[n].exports;var a=r[n]={i:n,l:!1,exports:{}};return e[n].call(a.exports,a,a.exports,t),a.l=!0,a.exports}t.m=e,t.c=r,t.d=function(e,r,n){t.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:n})},t.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},t.t=function(e,r){if(1&r&&(e=t(e)),8&r)return e;if(4&r&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(t.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&r&&"string"!=typeof e)for(var a in e)t.d(n,a,function(r){return e[r]}.bind(null,a));return n},t.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(r,"a",r),r},t.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},t.p="",t(t.s=0)}([function(e,r,t){"use strict";t(2);var n,a=t(1);var i=new((n=a)&&n.__esModule?n:{default:n}).default;i.irTabs(),i.addReadMore()},function(e,r,t){"use strict";Object.defineProperty(r,"__esModule",{value:!0});var n=function(){function e(e,r){for(var t=0;t<r.length;t++){var n=r[t];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(r,t,n){return t&&e(r.prototype,t),n&&e(r,n),r}}();var a=function(){function e(){!function(e,r){if(!(e instanceof r))throw new TypeError("Cannot call a class as a function")}(this,e)}return n(e,[{key:"irTabs",value:function(){jQuery(".irp-tabs span").click((function(){jQuery(".irp-tabs span").removeClass("irp-active"),jQuery(this).addClass("irp-active");var e=jQuery(this).attr("data-id");jQuery(".irp-tabs-content > div[data-id]").css("display","none"),jQuery('.irp-tabs-content > div[data-id="'+e+'"]').css("display","block")}))}},{key:"addReadMore",value:function(){var e=jQuery(".irp-tab-content > p");if(0!=e.length){var r=e[0].scrollHeight,t=130;jQuery(window).width()<588&&(t=396),r>t&&jQuery('<span class="irp-readmore">Read More</span>').insertAfter(e),this.readMore(e,r),this.readLess(e)}}},{key:"readMore",value:function(e,r){jQuery(document).on("click",".irp-readmore",(function(){jQuery(this).removeClass("irp-readmore"),jQuery(this).addClass("irp-readless"),jQuery(this).text("Read Less"),e.css("max-height",r+"px")}))}},{key:"readLess",value:function(e){var r=130;jQuery(window).width()<588&&(r=396),jQuery(document).on("click",".irp-readless",(function(){jQuery(this).removeClass("irp-readless"),jQuery(this).addClass("irp-readmore"),jQuery(this).text("Read More"),e.css("max-height",r+"px")}))}}]),e}();r.default=a},function(e,r){}]);