this.wc=this.wc||{},this.wc.navigationOptOut=function(t){var e={};function n(o){if(e[o])return e[o].exports;var r=e[o]={i:o,l:!1,exports:{}};return t[o].call(r.exports,r,r.exports,n),r.l=!0,r.exports}return n.m=t,n.c=e,n.d=function(t,e,o){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:o})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var r in t)n.d(o,r,function(e){return t[e]}.bind(null,r));return o},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="",n(n.s=482)}({0:function(t,e){!function(){t.exports=this.wp.element}()},11:function(t,e){t.exports=function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}},12:function(t,e){function n(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}t.exports=function(t,e,o){return e&&n(t.prototype,e),o&&n(t,o),t}},13:function(t,e,n){var o=n(75);t.exports=function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&o(t,e)}},14:function(t,e,n){var o=n(30),r=n(8);t.exports=function(t,e){return!e||"object"!==o(e)&&"function"!=typeof e?r(t):e}},2:function(t,e){!function(){t.exports=this.wp.i18n}()},30:function(t,e){function n(e){return"function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?t.exports=n=function(t){return typeof t}:t.exports=n=function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},n(e)}t.exports=n},4:function(t,e){!function(){t.exports=this.wp.components}()},468:function(t,e,n){},482:function(t,e,n){"use strict";n.r(e);var o=n(0),r=n(11),c=n.n(r),i=n(12),u=n.n(i),a=n(13),f=n.n(a),l=n(14),s=n.n(l),p=n(6),m=n.n(p),b=n(2),y=n(4);function d(t){var e=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(t){return!1}}();return function(){var n,o=m()(t);if(e){var r=m()(this).constructor;n=Reflect.construct(o,arguments,r)}else n=o.apply(this,arguments);return s()(this,n)}}var v=function(t){f()(n,t);var e=d(n);function n(t){var o;return c()(this,n),(o=e.call(this,t)).state={isModalOpen:!0},o}return u()(n,[{key:"render",value:function(){var t=this;return this.state.isModalOpen?Object(o.createElement)(y.Modal,{title:Object(b.__)("Help us improve",'woocommerce'),onRequestClose:function(){return t.setState({isModalOpen:!1})},className:"woocommerce-navigation-opt-out-modal"},Object(o.createElement)("p",null,Object(b.__)("Take this 2-minute survey to share why you're opting out of the new navigation",'woocommerce')),Object(o.createElement)("div",{className:"woocommerce-navigation-opt-out-modal__actions"},Object(o.createElement)(y.Button,{isDefault:!0,onClick:function(){return t.setState({isModalOpen:!1})}},Object(b.__)("No thanks",'woocommerce')),Object(o.createElement)(y.Button,{isPrimary:!0,target:"_blank",href:"https://automattic.survey.fm/new-navigation-opt-out",onClick:function(){return t.setState({isModalOpen:!1})}},Object(b.__)("Share feedback",'woocommerce')))):null}}]),n}(o.Component),O=(n(468),document.createElement("div"));O.setAttribute("id","navigation-opt-out-root"),Object(o.render)(Object(o.createElement)(v,null),document.body.appendChild(O))},6:function(t,e){function n(e){return t.exports=n=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)},n(e)}t.exports=n},75:function(t,e){function n(e,o){return t.exports=n=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t},n(e,o)}t.exports=n},8:function(t,e){t.exports=function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}}});