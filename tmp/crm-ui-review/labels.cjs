const fs=require('fs');
for(const file of ['admin/addEmployee.php','employee/userinfo.php']){
 const text=fs.readFileSync(file,'utf8');
 fs.writeFileSync(file,text.replace(/<span>(\*?)([^<>]+?)\s*:<\/span>/g,(_,required,label)=>'<label>'+label.trim()+(required?' <span class="text-danger">*</span>':'')+'</label>'));
}
