(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-card-web-view-web-view"],{"3daa":function(t,n,e){"use strict";e.r(n);var u=e("a183"),a=e.n(u);for(var i in u)"default"!==i&&function(t){e.d(n,t,function(){return u[t]})}(i);n["default"]=a.a},"4d06":function(t,n,e){"use strict";e.r(n);var u=e("7575"),a=e("3daa");for(var i in a)"default"!==i&&function(t){e.d(n,t,function(){return a[t]})}(i);var r=e("2877"),o=Object(r["a"])(a["default"],u["a"],u["b"],!1,null,"31e6e714",null);n["default"]=o.exports},7575:function(t,n,e){"use strict";var u=function(){var t=this,n=t.$createElement,e=t._self._c||n;return e("v-uni-view",[e("v-uni-web-view",{attrs:{src:t.url}})],1)},a=[];e.d(n,"a",function(){return u}),e.d(n,"b",function(){return a})},a183:function(t,n,e){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var u={data:function(){return{url:""}},onLoad:function(t){t.url&&(this.url=t.url,uni.setNavigationBarTitle({title:t.title}))},onNavigationBarButtonTap:function(t){uni.switchTab({url:"/pages/tabBar/index/index"})}};n.default=u}}]);