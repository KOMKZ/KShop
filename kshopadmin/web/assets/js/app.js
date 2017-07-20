var uploader = new plupload.Uploader({
	browse_button: 'browse', // this can be an id of a DOM element or the DOM element itself
	url: 'http://localhost:8011/file/create',
    chunk_size: '100kb',
	multipart_params: {
		file_save_type: "disk",
		file_save_name: "中国地图一亿像素.jpg",
		timestamp: '1500538759',
		file_is_private: 0,
		file_is_tmp: 0,
		access_token: 'abc',
	}
});
uploader.init();
uploader.bind('FilesAdded', function(up, files) {
	var html = '';
	plupload.each(files, function(file) {
		html += '<li id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></li>';
	});
	document.getElementById('filelist').innerHTML += html;
});

uploader.bind('UploadProgress', function(up, file) {
	document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
});

uploader.bind('Error', function(up, err) {
	document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
});


$('#start-upload').click(function(){
    uploader.start();
});
