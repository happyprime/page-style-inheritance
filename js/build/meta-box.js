!function(){"use strict";var e=window.wp.element,t=window.wp.components,n=window.wp.coreData,i=window.wp.editPost,o=window.wp.i18n,a=window.wp.plugins;const l=window.pageStyleInheritance;(0,a.registerPlugin)("page-style-inheritance-meta-box",{render:()=>{const a=wp.data.select("core/editor").getCurrentPostType(),[p,s]=(0,n.useEntityProp)("postType",a,"meta"),{psi_page_style:r}=p;return(0,e.createElement)(i.PluginDocumentSettingPanel,{title:(0,o.__)("Page style inheritance","page-style-inheritance")},(0,e.createElement)(t.SelectControl,{label:(0,o.__)("Select page style","page-style-inhertiance"),value:r,onChange:e=>s({...p,psi_page_style:e}),options:l,hideCancelButton:!0}))},icon:"admin-post"})}();