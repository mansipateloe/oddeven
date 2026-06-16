$(document).ready(function() {
	
	// enable fileuploader plugin
	$('#inputfile').fileuploader({
        addMore: true,
	    extensions: ['jpg', 'jpeg', 'png'] // allowed extensions or types {Array}
    });
	$('#inputfile_1').fileuploader({
        addMore: true,
	//    extensions: ['jpg', 'jpeg', 'png', 'gif'] // allowed extensions or types {Array}
    });
	
	
	$('#inputfile1').fileuploader({
        addMore: true,
		extensions: ['jpg', 'jpeg', 'png', 'pdf'] // allowed extensions or types {Array}
    });
	
});
