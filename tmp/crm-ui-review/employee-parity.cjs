const fs=require('fs'),{execFileSync}=require('child_process');
const old=fs.readFileSync('tmp/crm-ui-review/employee-before.php','utf8');const updated=fs.readFileSync('admin/addEmployee.php','utf8');
const formNames=t=>[...t.match(/<form id="employeeForm"[\s\S]*?<\/form>/)[0].matchAll(/\bname="([^"]+)"/g)].map(x=>x[1]).sort();
const before=formNames(old),after=formNames(updated);console.log(JSON.stringify({preservedFields:JSON.stringify(before)===JSON.stringify(after),count:after.length,removed:before.filter(x=>!after.includes(x)),added:after.filter(x=>!before.includes(x))},null,2));

