<?php
ob_start();
session_start();
include("process/config.php");
include("functions.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo get_company_name(); ?>">
    <meta name="author" content="<?php echo get_company_name(); ?>">
    <meta name="keyword" content="<?php echo get_company_name(); ?>">
    <link rel="shortcut icon" href="img/favicon.ico">
    <title><?php echo get_company_name(); ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-reset.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
	<link href="css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="assets/bootstrap-datepicker/css/datepicker.css" />
    <link rel="stylesheet" type="text/css" href="assets/bootstrap-timepicker/compiled/timepicker.css" />
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />
    <link href="css/table-responsive.css" rel="stylesheet" />
    
    <link href="assets/fullcalendar/fullcalendar/bootstrap-fullcalendar.css" rel="stylesheet" />
	
    <!--dynamic table-->
     <link href="assets/advanced-datatable/media/css/demo_page.css" rel="stylesheet" />
     <link href="assets/advanced-datatable/media/css/demo_table.css" rel="stylesheet" />
     <link rel="stylesheet" href="assets/data-tables/DT_bootstrap.css" />
	 <script src="js/jquery.js"></script>
     <script src="js/ajax.js"></script>
	 
	 <!--toastr-->
    <link href="assets/toastr-master/toastr.css" rel="stylesheet" type="text/css" />
	
	<!--  summernote -->
    <link href="assets/summernote/dist/summernote.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="assets/jquery-multi-select/css/multi-select.css" />
	
	<!-- Jquery filer css -->
    <link href="assets/jquery.filer/src/jquery.fileuploader.css" media="all" rel="stylesheet">	
    <script type="text/javascript" src="tinymce/tinymce.min.js"></script>
    <script type="text/javascript">
        tinymce.init({
            selector: ".tinytext",
 		    plugins: 'fontawesome noneditable',
			plugins: "importcss",
			theme: "modern",
			valid_elements : '*[*]',
			width: 600,
			height:300,
			
		    plugins: [
                 "advlist autolink lists charmap print preview hr anchor pagebreak",
                 "searchreplace wordcount visualblocks visualchars insertdatetime nonbreaking",
                 "table contextmenu directionality emoticons paste textcolor responsivefilemanager codemirror fontawesome"
           ],
           toolbar2: "| responsivefilemanager | link unlink anchor | forecolor backcolor  | print preview code",
           image_advtab: true ,
		   file_browser_callback_types: 'file image media',
		   file_browser_callback: function(field_name, url, type, win) { win.document.getElementById(field_name).value = "my browser value"; },
           external_filemanager_path:"tinymce/plugins/filemanager/",
           filemanager_title:"Responsive Filemanager" ,
           external_plugins: {"filemanager" : "plugins/filemanager/plugin.min.js"},
           codemirror: {
            indentOnInit: true, // Whether or not to indent code on init. 
            path: '../codemirror', // Path to CodeMirror distribution
            config: {           // CodeMirror config object
               mode: 'application/x-httpd-php',
               lineNumbers: false
            },

            jsFiles: [          // Additional JS files to load
               'mode/clike/clike.js',
               'mode/php/php.js'
            ]

          },
             force_br_newlines : false,
             force_p_newlines : false,
             forced_root_block : '',

         }); 
	
        </script>
</head>
<body>
  <section id="container" class="">
	<?php include("topbar.php"); ?>
    <?php include("sidebar.php"); ?>
	<section id="main-content">
		<?php include("content.php"); ?>
	</section>
  </section>
  
  <script src="js/bootstrap.min.js"></script>
  <script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
  <script src="js/jquery.sparkline.js" type="text/javascript"></script>
  <script src="js/owl.carousel.js" ></script>
  <script src="js/jquery.scrollTo.min.js"></script>
  <script src="js/jquery.nicescroll.js" type="text/javascript"></script>
  
  <script type="text/javascript" language="javascript" src="assets/advanced-datatable/media/js/jquery.dataTables.js"></script>
  <script type="text/javascript" src="assets/data-tables/DT_bootstrap.js"></script>
  <!--dynamic table initialization -->
  <script src="js/dynamic_table_init.js"></script>
  
  <script src="js/respond.min.js" ></script>
  <script type="text/javascript" src="assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
  <script type="text/javascript" src="assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
  <script type="text/javascript" src="assets/bootstrap-daterangepicker/moment.min.js"></script>
  <script type="text/javascript" src="assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
  <script src="js/common-scripts.js"></script>
  <script type="text/javascript" src="js/selects/select2.min.js"></script>
  <script type="text/javascript" src="assets/jquery-multi-select/js/jquery.multi-select.js"></script>
   <script type="text/javascript" src="assets/jquery-multi-select/js/jquery.quicksearch.js"></script>
  <script src="js/count.js"></script>
  <script src="js/advanced-form-components.js"></script>
  <script src="assets/jquery-knob/js/jquery.knob.js"></script>
  <!--toastr-->
  <script src="assets/toastr-master/toastr.js"></script>
  <script>
      //knob
      $(".knob").knob();
	  function delete_confirm(){
		if($('.checked_id:checked').length > 0){
				var result = confirm("Are you sure to delete selected data?");
				if(result){
					return true;
				}else{
					return false;
				}
			}else{
				alert('Select at least 1 record to delete.');
				return false;
			}
	  }
	  $(document).ready(function(){
			$('#select_all').on('click',function(){
				if(this.checked){
					$('.checked_id').each(function(){
						this.checked = true;
					});
				}else{
					 $('.checked_id').each(function(){
						this.checked = false;
					});
				}
			});
			
			$('.checked_id').on('click',function(){
				if($('.checked_id:checked').length == $('.checked_id').length){
					$('#select_all').prop('checked',true);
				}else{
					$('#select_all').prop('checked',false);
				}
			});
		});
		function delete_confirm1()
		{
			if($('.checked_id1:checked').length > 0){
				var result = confirm("Are you sure to delete selected data?");
				if(result){
					return true;
				}else{
					return false;
				}
			}else{
				alert('Select at least 1 record to delete.');
				return false;
			}
	  }
	  $(document).ready(function(){
			$('#select_all1').on('click',function(){
				if(this.checked){
					$('.checked_id1').each(function(){
						this.checked = true;
					});
				}else{
					 $('.checked_id1').each(function(){
						this.checked = false;
					});
				}
			});
			
			$('.checked_id1').on('click',function(){
				if($('.checked_id1:checked').length == $('.checked_id1').length){
					$('#select_all1').prop('checked',true);
				}else{
					$('#select_all1').prop('checked',false);
				}
			});
		});
  </script>
  <script src="assets/ckeditor/ckeditor.js"></script>
  
   <!--summernote-->
  <script src="assets/summernote/dist/summernote.min.js"></script>
  <script>

      jQuery(document).ready(function(){

          $('.summernote').summernote({
              height: 200,                 // set editor height
			  toolbar: [
					[ 'style', [ 'style' ] ],
					[ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
					[ 'color', [ 'color' ] ],
					[ 'para', [ 'ol','paragraph', 'height' ] ],
					[ 'table', [ 'table' ] ],
					[ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
				],

              minHeight: null,             // set minimum height of editor
              maxHeight: null,             // set maximum height of editor

              focus: true                 // set focus to editable area after initializing summernote
          });
      });

  </script>
  <!-- Jquery filer js -->
  <script src="assets/jquery.filer/src/jquery.fileuploader.min.js" type="text/javascript"></script>
  <script src="assets/jquery.filer/js/custom.js" type="text/javascript"></script>
  <!--script for this page only-->
  <?php if($_REQUEST['pid'] == "home"){ include("calendar.php"); } ?>
  <script type="text/javascript">
	   $('.decimal').keyup(function(){
			var val = $(this).val();
			if(isNaN(val)){
				 val = val.replace(/[^0-9\.]/g,'');
				 if(val.split('.').length>2) 
					 val =val.replace(/\.+$/,"");
			}
			$(this).val(val); 
		});
		$(".decimal1").on("change", function() {
				var val = this.value;
				if (val.match(/\./g)) { //See if it already has a dot
					if (val.replace(/\./g, "").match(/^\d+$/) !== null) { //Check if it's number after removing the dot
						this.value = parseFloat(val, 10).toFixed(2); //Yes, so format number
					} else {
						//Not a number, so reset the input value
						//Log a msg if need be: "Only numbers allowed!";
						this.value = "";
						this.focus();
					}
				} else {
					//No dot found, so, see if it's a number        
					if (val.match(/^\d+$/) !== null) {
						this.value = parseFloat(val, 10).toFixed(2); //Yes, so format number
					} else {
						//Not a number, so reset the input value
						//Log a msg if need be: "Only numbers allowed!";
						this.value = "";
						this.focus();
					}
				}
			});

  </script>
</body>
</html>
