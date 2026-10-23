const fs = require('fs');
const paths = ['admin', 'employee'];
const changed = [];
const inventory = [];
for (const dir of paths) {
 for (const entry of fs.readdirSync(dir).filter(x => x.endsWith('.php'))) {
  const file = dir + '/' + entry;
  const original = fs.readFileSync(file, 'utf8');
  if (!/include\s*(?:\(\s*)?['"]header\.php/.test(original)) continue;
  inventory.push({file, forms:(original.match(/<form\b/g)||[]).length, titleBars:(original.match(/foundation-titlebar/g)||[]).length});
  const revised = original.replace(/(<div class="foundation-titlebar">)\s*(<a\b[^>]*>[\s\S]*?<\/a>|<span>\s*<\/span>)\s*(<h2>[\s\S]*?<\/h2>)/g, (_, open, back, title) => open + title + (back.startsWith('<a') ? '\n    ' + back : ''));
  if (revised !== original) { fs.writeFileSync(file, revised); changed.push(file); }
 }
}
fs.writeFileSync('tmp/crm-ui-review/inventory.json', JSON.stringify({sharedPages:inventory.length, formPages:inventory.filter(x=>x.forms).length, pages:inventory, correctedTitleOrder:changed},null,2));
console.log(JSON.stringify({sharedPages:inventory.length, formPages:inventory.filter(x=>x.forms).length, correctedTitleOrder:changed},null,2));
