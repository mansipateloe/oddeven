function get_event_data(eve)
{
	var id=$(eve).data('id');
	var user_name=$(eve).data('user');
	var task_type=$(eve).data('task_type');
	var date=$(eve).data('dt_y')+'-'+$(eve).data('dt_m')+'-'+$(eve).data('dt_d');
	if(!task_type)
	{
		var task_type=" ";
	}
	if(id!="add_task")
	{	
		 jQuery.ajax({
			'async': false,
			'type': "POST",
			'global': false,
			'url': "get_user_task.php",
			'data': {id:id,date:date,task_type:task_type},
			'success': function(data){
				
				
				
				data=$.parseJSON(data);
				//console.log(data);
				$("#event_lbl").html(user_name+" details");
				
				if(data.table_task!=" ")
				{	
					$('#modal_task_display').html(data.table_task);
					$('#modal_task_display_panel').show();
				}else
				{
					
					$('#modal_task_display_panel').hide();
				}
				
				if(data.table_user!=" ")
				{	
					$('#modal_user_display').html(data.table_user);
					$('#modal_user_display_panel').show();
				}else
				{
					
					$('#modal_user_display_panel').hide();
				}
		   }
		});
	}
}
function add_task_user_init(eve)
{	
	$('#modal_add_task').modal('show');
	var date=$(eve).data('date');
	$("#user_date").val(date);
	$('#user_name').select2();
	//console.log(date);
}

