const fs = require('fs');
const {spawn} = require('child_process');
const path = require('path');
const fixture = `<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="../../vendor/bootstrap/css/bootstrap.min.css"><link rel="stylesheet" href="../../vendor/font-awesome/css/font-awesome.min.css"><link rel="stylesheet" href="../../vendor/custom/customAdmin.css"><link rel="stylesheet" href="../../dist/css/erp-typography.css"><link rel="stylesheet" href="../../dist/css/crm-ui.css"><script defer src="../../js/crm-ui.js"></script>
<style>body{margin:0!important}body #page-wrapper{margin:0!important;min-height:100vh!important;padding:24px!important} .resource-form{display:grid;grid-template-columns:repeat(2,minmax(0,1fr))}@media(max-width:767px){.resource-form{grid-template-columns:1fr}} </style></head>
<body class="admin-portal crm-ui"><main id="page-wrapper" class="compact-admin-page">
<div class="foundation-titlebar"><a href="#">Back to Assets</a><h2>New Asset</h2></div>
<div class="panel panel-default"><div class="panel-heading"><h4><i class="fa fa-building"></i> Asset Profile</h4><a class="btn btn-default" href="#">View Assets</a></div><div class="panel-body"><form id="editor"><div class="resource-form"><div class="form-group"><label><i class="fa fa-user"></i> Asset Name <span class="text-danger">*</span></label><input class="form-control" name="asset_name" required></div><div class="form-group"><label>Asset Type</label><select class="form-control" name="asset_type"><option>Laptop</option><option>Desktop</option></select></div><div class="form-group"><label>Asset Code</label><input class="form-control" readonly value="AST-001"></div><div class="form-group"><label>Purchase Cost</label><input class="form-control is-invalid" type="number" value="0"><span class="field-error">Enter a valid amount.</span></div><div class="form-group"><label>Notes</label><textarea class="form-control" rows="3"></textarea></div></div><div class="foundation-actions"><button class="btn btn-primary" name="save" value="1"><i class="fa fa-save"></i> Save Asset</button><input class="btn btn-danger" type="reset" value="Cancel"></div></form></div></div>
<div class="panel panel-default"><div class="panel-heading">Lead Sources</div><div class="panel-body"><form id="master"><input type="hidden" name="source_id" value="0"><div class="col-lg-4"><div class="form-group"><label>Lead Source</label><input class="form-control" name="name" value="Referral"></div></div><div class="col-lg-2"><br><div class="form-group" align="right"><input id="dynamic-submit" class="btn btn-primary" type="submit" name="addLeadSource" value="Add Lead Source" style="margin-top:7px"></div></div><div class="col-lg-2"><br><div class="form-group"><input class="btn btn-danger" type="reset" value="Cancel"></div></div></form><table class="table"><thead><tr><th>Title</th><th>Action</th></tr></thead><tbody><tr><td>Referral</td><td><button type="button" class="btn btn-link"><i class="fa fa-pencil"></i> Edit</button> <button type="button" class="btn btn-link" id="delete"><i class="fa fa-trash-o"></i></button></td></tr></tbody></table></div></div></main></body></html>`;
fs.writeFileSync('tmp/crm-ui-review/components.html', fixture);
const browser = spawn('C:/Program Files/Google/Chrome/Application/chrome.exe', ['--headless=new','--disable-gpu','--no-first-run','--no-default-browser-check','--remote-debugging-pipe','--user-data-dir='+path.resolve('tmp/crm-ui-review/chrome-profile'),'about:blank'], {stdio:['ignore','ignore','pipe','pipe','pipe'],windowsHide:true});
let id=0, buffer='', pending=new Map();
browser.stdio[4].on('data',chunk=>{buffer+=chunk; let end; while((end=buffer.indexOf('\0'))>=0){let raw=buffer.slice(0,end);buffer=buffer.slice(end+1);if(!raw)continue;let msg=JSON.parse(raw);if(msg.id&&pending.has(msg.id)){let p=pending.get(msg.id);pending.delete(msg.id);msg.error?p.reject(msg.error):p.resolve(msg.result)}}});
function cdp(method,params={},sessionId){return new Promise((resolve,reject)=>{const n=++id;pending.set(n,{resolve,reject});browser.stdio[3].write(JSON.stringify({id:n,method,params,...(sessionId?{sessionId}:{})})+'\0');setTimeout(()=>{if(pending.has(n)){pending.delete(n);reject(new Error('Timeout: '+method));}},20000).unref();});}
const checks=[];
function check(name,ok,details){checks.push({name,ok,details});if(!ok)console.error('FAIL',name,details);}
(async()=>{
 const {targetId}=await cdp('Target.createTarget',{url:'about:blank'});const {sessionId:s}=await cdp('Target.attachToTarget',{targetId,flatten:true});
 const evaluate=async expression=>(await cdp('Runtime.evaluate',{expression,returnByValue:true,awaitPromise:true},s)).result.value;
 await cdp('Page.enable',{},s);
 await cdp('Page.navigate',{url:'http://localhost/oddeven/tmp/crm-ui-review/components.html'},s);
 for(let i=0;i<60;i++){if(await evaluate('document.readyState === "complete" && !!document.querySelector(".crm-action")'))break;await new Promise(r=>setTimeout(r,100));}
 for(const portal of ['admin','employee','lead']){
  await evaluate(`(()=>{document.body.className='${portal}-portal crm-ui';const link=document.querySelector('link[href*=customAdmin],link[data-portal-style]');link.dataset.portalStyle='1';link.href='../../${({admin:'vendor/custom/customAdmin.css',employee:'vendor/custom/custom.css',lead:'lead/css/style.css'})[portal]}';})()`);
  for(const width of [1440,390]){
   await cdp('Emulation.setDeviceMetricsOverride',{width,height:1000,deviceScaleFactor:1,mobile:false},s);
   await new Promise(r=>setTimeout(r,250));
   const result=await evaluate(`new Promise(resolve=>requestAnimationFrame(()=>{const root=document.querySelector('#page-wrapper'),h=document.querySelector('.foundation-titlebar h2'),b=document.querySelector('.foundation-titlebar a'),field=document.querySelector('input.form-control'),save=document.querySelector('button.btn-primary'),cancel=document.querySelector('input[type=reset]');resolve({titleLeft:h.getBoundingClientRect().left,backLeft:b.getBoundingClientRect().left,titleTop:h.getBoundingClientRect().top,backTop:b.getBoundingClientRect().top,fieldHeight:field.getBoundingClientRect().height,saveHeight:save.getBoundingClientRect().height,cancelHeight:cancel.getBoundingClientRect().height,cancelColor:getComputedStyle(cancel).backgroundColor,overflow:document.documentElement.scrollWidth>innerWidth,labelLinked:document.querySelector('label').control===field,iconLabel:document.querySelector('#delete').getAttribute('aria-label'),required:field.required,submitValue:save.value,masterFlex:getComputedStyle(document.querySelector('#master')).display,invalid:getComputedStyle(document.querySelector('.is-invalid')).borderTopColor})}))`);
   check(`${portal} ${width}px layout`,result.fieldHeight===40&&result.saveHeight===40&&result.cancelHeight===40&&!result.overflow&&result.labelLinked&&result.iconLabel==='Delete'&&result.required&&result.submitValue==='1'&&result.masterFlex==='flex'&&result.cancelColor==='rgb(255, 255, 255)'&&(width===390?result.titleTop<result.backTop:result.titleLeft<result.backLeft),result);
   if(portal==='admin'){const {data}=await cdp('Page.captureScreenshot',{format:'png',captureBeyondViewport:true},s);fs.writeFileSync(`tmp/crm-ui-review/${portal}-${width}.png`,Buffer.from(data,'base64'));}
  }
 }
 await evaluate(`document.querySelector('#dynamic-submit').value='Update Lead Source'`);
 await new Promise(r=>setTimeout(r,100));
 check('Add to update icon refresh',await evaluate(`document.querySelector('#dynamic-submit').dataset.crmAction==='save'`));
 await evaluate(`document.querySelector('#master').insertAdjacentHTML('beforeend','<button class="btn btn-default" type="button">Download Report</button>')`);
 await new Promise(r=>setTimeout(r,100));
 check('Dynamic controls enhanced',await evaluate(`document.querySelector('#master > button').dataset.crmAction==='download'`));
 check('Primary input and button share color',await evaluate(`getComputedStyle(document.querySelector('button.btn-primary')).backgroundColor===getComputedStyle(document.querySelector('input.btn-primary')).backgroundColor`));
 await evaluate(`document.querySelector('#editor').insertAdjacentHTML('beforeend','<div class="input-group"><span class="input-group-addon"><i class="fa fa-user"></i></span><input class="form-control"></div><form></form>');document.querySelector('#page-wrapper').insertAdjacentHTML('beforeend','<form class="mini-form"><input class="form-control" name="contact_name" placeholder="Contact name"></form>')`);
 await new Promise(r=>setTimeout(r,100));
 check('Decorative field icon removed',await evaluate(`getComputedStyle(document.querySelector('.crm-decorative-addon')).display==='none'`));
 check('Mini form has persistent label',await evaluate(`document.querySelector('.mini-form input').labels[0].textContent==='Contact name'`));
 await cdp('Emulation.setEmulatedMedia',{media:'print'},s);
 check('Screen-only styles excluded from print',await evaluate(`getComputedStyle(document.querySelector('#master')).display!=='flex'`));
 fs.writeFileSync('tmp/crm-ui-review/checks.json',JSON.stringify(checks,null,2));console.log(JSON.stringify({passed:checks.filter(x=>x.ok).length,total:checks.length,checks},null,2));
 if(checks.some(x=>!x.ok))process.exitCode=1;
})().catch(e=>{console.error(e);process.exitCode=1;}).finally(()=>{browser.kill();});
