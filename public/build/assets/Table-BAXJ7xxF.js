import{_ as h,D as v}from"./DangerButton-QRHYW_E8.js";import{P as w}from"./PrimaryButton-ByB5uLeL.js";import{f as t,o as e,b as i,g as k,F as d,j as c,t as f,a as u,w as y,e as m}from"./app-CNFM8zkO.js";const C={class:"w-full"},A={class:"bg-gray-800 text-white"},B={key:0,class:"px-6 py-4 text-left"},b={class:"divide-y divide-gray-200"},V={key:0,class:"px-6 py-4 flex gap-2"},E={__name:"Table",props:{columns:Array,data:Array,noActions:Boolean},emits:["show","edit","delete"],setup(a,{emit:x}){const l=x,p=o=>{l("show",o)},_=o=>{l("edit",o)},g=o=>{l("remove",o)};return(o,n)=>(e(),t("table",C,[i("thead",A,[i("tr",null,[(e(!0),t(d,null,c(a.columns,s=>(e(),t("th",{key:s.key,class:"px-6 py-4 text-left"},f(s.title),1))),128)),a.noActions?k("",!0):(e(),t("th",B,"Actions"))])]),i("tbody",b,[(e(!0),t(d,null,c(a.data,s=>(e(),t("tr",{key:s.id},[(e(!0),t(d,null,c(a.columns,r=>(e(),t("td",{key:r.key,class:"px-6 py-4"},f(s[r.key]??""),1))),128)),a.noActions?k("",!0):(e(),t("td",V,[u(h,{onClick:r=>p(s)},{default:y(()=>n[0]||(n[0]=[m(" View ")])),_:2},1032,["onClick"]),u(w,{onClick:r=>_(s)},{default:y(()=>n[1]||(n[1]=[m(" Edit ")])),_:2},1032,["onClick"]),u(v,{onClick:r=>g(s)},{default:y(()=>n[2]||(n[2]=[m(" Remove ")])),_:2},1032,["onClick"])]))]))),128))])]))}};export{E as _};
