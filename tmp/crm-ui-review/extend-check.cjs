const fs=require('fs');const p='tmp/crm-ui-review/check.cjs';let c=fs.readFileSync(p,'utf8');
c=c.replace("  await evaluate(`document.body.className='${portal}-portal crm-ui'`);",`  await evaluate(\`new Promise(resolve=>{document.body.className='\${portal}-portal crm-ui';const link=document.querySelector('link[href*=customAdmin],link[data-portal-style]');link.dataset.portalStyle='1';link.onload=resolve;link.href=\${JSON.stringify('PLACEHOLDER')};})\`);` .replace("${JSON.stringify('PLACEHOLDER')}", "'../../'+({admin:'vendor/custom/customAdmin.css',employee:'vendor/custom/custom.css',lead:'lead/css/style.css'})[portal]"));
// Simpler explicit expression to switch the portal stylesheet without interpolating browser variables.
c=c.replace("link.href='../../'+({admin:'vendor/custom/customAdmin.css',employee:'vendor/custom/custom.css',lead:'lead/css/style.css'})[portal];", "link.href='../../${({admin:'vendor/custom/customAdmin.css',employee:'vendor/custom/custom.css',lead:'lead/css/style.css'})[portal]}';");
c=c.replace(" check('Add to update icon refresh'", " check('Add to update icon refresh'");
c=c.replace(" await cdp('Emulation.setEmulatedMedia'", ` check('Primary input and button share color',await evaluate(\`getComputedStyle(document.querySelector('button.btn-primary')).backgroundColor===getComputedStyle(document.querySelector('input.btn-primary')).backgroundColor\`));
 await evaluate(\`document.querySelector('#editor').insertAdjacentHTML('beforeend','<div class="input-group"><span class="input-group-addon"><i class="fa fa-user"></i></span><input class="form-control"></div><form></form>');document.querySelector('#page-wrapper').insertAdjacentHTML('beforeend','<form class="mini-form"><input class="form-control" name="contact_name" placeholder="Contact name"></form>')\`);
 await new Promise(r=>setTimeout(r,100));
 check('Decorative field icon removed',await evaluate(\`getComputedStyle(document.querySelector('.crm-decorative-addon')).display==='none'\`));
 check('Mini form has persistent label',await evaluate(\`document.querySelector('.mini-form input').labels[0].textContent==='Contact name'\`));
 await cdp('Emulation.setEmulatedMedia'`);
fs.writeFileSync(p,c);
