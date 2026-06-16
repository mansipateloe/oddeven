	<footer>
		<div class="panel-footer">
			<div align="right">&copy; <a href="http://oddeveninfotech.com">Oddeveninfotech</a></div>
		</div>
	</footer>
	<div id="oecrmConfirmModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;">
		<div style="background:#fff;border-radius:8px;max-width:420px;width:92%;box-shadow:0 12px 30px rgba(0,0,0,.25);overflow:hidden;">
			<div style="padding:14px 18px;border-bottom:1px solid #eee;font-weight:600;">Confirm Action</div>
			<div id="oecrmConfirmMessage" style="padding:18px;color:#333;">Are you sure?</div>
			<div style="padding:12px 18px;border-top:1px solid #eee;text-align:right;"><button type="button" data-dialog-close="oecrmConfirmModal" class="btn btn-default">Cancel</button> <button type="button" id="oecrmConfirmOk" class="btn btn-danger">Confirm</button></div>
		</div>
	</div>
	<div id="oecrmAlertModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:10000;align-items:center;justify-content:center;">
		<div style="background:#fff;border-radius:8px;max-width:420px;width:92%;box-shadow:0 12px 30px rgba(0,0,0,.25);overflow:hidden;">
			<div style="padding:14px 18px;border-bottom:1px solid #eee;font-weight:600;">Notification</div>
			<div id="oecrmAlertMessage" style="padding:18px;color:#333;"></div>
			<div style="padding:12px 18px;border-top:1px solid #eee;text-align:right;"><button type="button" data-dialog-close="oecrmAlertModal" class="btn btn-primary">OK</button></div>
		</div>
	</div>
	<script>window.OECRM_CSRF_TOKEN=<?php echo json_encode(oecrm_csrf_token()); ?>;</script>
	<script>if(!window.jQuery)document.write('<script src="../vendor/jquery/jquery.min.js"><\/script>');</script>
	<script>if(!jQuery.fn.modal)document.write('<script src="../vendor/bootstrap/js/bootstrap.min.js"><\/script>');</script>
	<script>if(!jQuery.fn.metisMenu)document.write('<script src="../vendor/metisMenu/metisMenu.min.js"><\/script>');</script>
	<script>if(!jQuery.fn.DataTable)document.write('<script src="../vendor/datatables/js/jquery.dataTables.min.js"><\/script><script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"><\/script><script src="../vendor/datatables-responsive/dataTables.responsive.js"><\/script>');</script>
	<script src="../js/oecrm-dialogs.js"></script>
	<script src="../js/oecrm-ui.js?v=20260612-7"></script>
	<script>
	(function(){
		var toggle=document.querySelector('.admin-menu-toggle');
		var currentPage=document.body.getAttribute('data-current-page')||'';
		var cancelMap={
			'assetEditor.php':'assets.php',
			'expenseEditor.php':'finance.php',
			'subscriptionEditor.php':'subscriptions.php',
			'resourceAllocation.php':'resources.php',
			'projectEditor.php':'projectWorkspace.php',
			'taskEditor.php':'viewTask.php',
			'invoiceEditor.php':'finance.php',
			'clientProfile.php':'clients.php'
		};
		var target=cancelMap[currentPage];
		if(target){
			document.querySelectorAll('form .resource-actions, form .foundation-actions, form').forEach(function(container){
				if(container.querySelector('a.btn[href="'+target+'"]'))return;
				var submit=container.querySelector('button.btn-primary,button[type="submit"],input[type="submit"]');
				if(!submit)return;
				var actionBox=submit.closest('.resource-actions,.foundation-actions')||submit.parentElement;
				if(!actionBox||actionBox.querySelector('a.btn-default[href="'+target+'"]'))return;
				var cancel=document.createElement('a');
				cancel.className='btn btn-default';
				cancel.href=target;
				cancel.textContent='Cancel';
				actionBox.appendChild(document.createTextNode(' '));
				actionBox.appendChild(cancel);
			});
		}
		if('scrollRestoration' in history) history.scrollRestoration='manual';
		window.scrollTo(0,0);
		window.addEventListener('pageshow',function(){window.scrollTo(0,0);});
		if(toggle)toggle.addEventListener('click',function(){document.body.classList.toggle('admin-sidebar-open');});
		document.addEventListener('click',function(event){
			if(window.innerWidth>767||!document.body.classList.contains('admin-sidebar-open'))return;
			if(event.target.closest('.sidebar')||event.target.closest('.admin-menu-toggle'))return;
			document.body.classList.remove('admin-sidebar-open');
		});
	})();
	</script>
	</body>
</html>
