<script type="text/javascript" src="/js/swfupload.js"></script>
<script type="text/javascript" src="/js/jquery.swfupload.js"></script>
<script type="text/javascript">

$(function(){
	$('#swfupload-control').swfupload({
		upload_url: "upload.php",
		file_size_limit : "10240",
		file_types : "*.*",
		file_types_description : "All Files",
		file_upload_limit : "0",
		flash_url : "/js/swfupload.swf",
		button_image_url : '../vendor/swfupload/XPButtonUploadText_61x22.png',
		button_width : 61,
		button_height : 22,
		button_placeholder : $('#button')[0],
		debug: true,
		custom_settings : {something : "here"}
	})
		.bind('swfuploadLoaded', function(event){
			$('#log').append('<li>Loaded</li>');
		})
		.bind('fileQueued', function(event, file){
			$('#log').append('<li>File queued - '+file.name+'</li>');
			// start the upload since it's queued
			$(this).swfupload('startUpload');
		})
		.bind('fileQueueError', function(event, file, errorCode, message){
			$('#log').append('<li>File queue error - '+message+'</li>');
		})
		.bind('fileDialogStart', function(event){
			$('#log').append('<li>File dialog start</li>');
		})
		.bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
			$('#log').append('<li>File dialog complete</li>');
		})
		.bind('uploadStart', function(event, file){
			$('#log').append('<li>Upload start - '+file.name+'</li>');
		})
		.bind('uploadProgress', function(event, file, bytesLoaded){
			$('#log').append('<li>Upload progress - '+bytesLoaded+'</li>');
		})
		.bind('uploadSuccess', function(event, file, serverData){
			$('#log').append('<li>Upload success - '+file.name+'</li>');
		})
		.bind('uploadComplete', function(event, file){
			$('#log').append('<li>Upload complete - '+file.name+'</li>');
			// upload has completed, lets try the next one in the queue
			$(this).swfupload('startUpload');
		})
		.bind('uploadError', function(event, file, errorCode, message){
			$('#log').append('<li>Upload error - '+message+'</li>');
		});
	
});	

</script>
<div id="swfupload-control">
	<ol id="log"></ol>
	<input type="button" id="button" />
</div>