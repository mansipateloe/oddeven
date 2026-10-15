<style>
.shift-page .shift-assignment-form .shift-option-search{width:100%;margin-bottom:8px}
.shift-page .shift-assignment-form select[data-oecrm-native="1"]{display:block!important;width:100%;height:42px;min-height:42px;padding:8px 10px;border:1px solid #dfe4ee;border-radius:8px;background:#fff}
.shift-page .shift-search-status{display:block;min-height:18px;margin:5px 0 0;color:#667085;font-size:12px}
.shift-page .shift-assignment-form>input[type="hidden"]{display:none}
</style>
<script>
(function(){
    function init(){
        document.querySelectorAll('.shift-option-search').forEach(function(search){
            var select=document.getElementById(search.getAttribute('data-filter-select'));
            if(!select)return;
            var status=document.getElementById(select.id+'-status');
            var kind=select.name==='employee_id'?'employees':'shifts';
            var placeholder=select.options[0]?select.options[0].text:'Select an option';
            var options=Array.prototype.slice.call(select.options).filter(function(option){return option.value!==''&&!option.disabled;}).map(function(option){return {value:option.value,text:option.text};});
            var matches=options.slice();
            function changed(){select.dispatchEvent(new Event('change',{bubbles:true}));}
            function filter(){
                var previous=select.value;
                var terms=search.value.trim().toLocaleLowerCase().split(/\s+/).filter(Boolean);
                matches=options.filter(function(option){
                    var text=option.text.toLocaleLowerCase();
                    return terms.every(function(term){return text.indexOf(term)!==-1;});
                });
                select.replaceChildren(new Option(matches.length?placeholder:'No matching '+kind,''));
                matches.forEach(function(option){select.add(new Option(option.text,option.value));});
                select.value=matches.some(function(option){return option.value===previous;})?previous:'';
                if(select.value!==previous)changed();
                status.textContent=matches.length
                    ?matches.length+(terms.length?' matching ':' available ')+kind
                    :'No matching '+kind+'. Clear the search to see all options.';
            }
            search.addEventListener('input',filter);
            search.addEventListener('keydown',function(event){
                if(event.key==='Enter'){
                    event.preventDefault();
                    if(matches.length===1){
                        select.value=matches[0].value;
                        changed();
                        select.focus({preventScroll:true});
                    }
                }else if(event.key==='ArrowDown'){
                    event.preventDefault();
                    select.focus({preventScroll:true});
                }else if(event.key==='Escape'){
                    event.preventDefault();
                    search.value='';
                    filter();
                }
            });
            var form=select.closest('form');
            if(form)form.addEventListener('reset',function(){setTimeout(filter,0);});
            filter();
        });
    }
    if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);
    else init();
})();
</script>