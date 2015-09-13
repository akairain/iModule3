var AddonYoutube = {
	getYoutubeInfo:function(id,file) {
		if (file.youtube.status == "uploaded" || file.thumbnail == null) {
			if (file.youtube.status == "uploaded") file.status = "PROCESSING";
			
			$(document).triggerHandler("Attachment.change",[id,file]);
			
			$.ajax({
				type:"POST",
				url:ENV.DIR+"/addons/youtube/attachment.php",
				data:{idx:file.idx},
				dataType:"json",
				success:function(result) {
					file.youtube.status = result.status;
					
					if (file.thumbnail == null && result.thumbnail != null) {
						file.thumbnail = result.thumbnail;
						$(document).triggerHandler("Attachment.change",[id,file]);
					}
					
					if (result.status == "uploaded") {
						setTimeout(AddonYoutube.getYoutubeInfo,10000,id,file);
						return;
					} else {
						file.status = "COMPLETE";
						$(document).triggerHandler("Attachment.change",[id,file]);
					}
					
					if (result.thumbnail == null) {
						setTimeout(AddonYoutube.getYoutubeInfo,15000,id,file);
					}
				}
			});
		}
	}
};

$(document).on("Attachment.add Attachment.complete",function(e,id,file) {
	if (file.youtube !== undefined && file.youtube != null) {
		AddonYoutube.getYoutubeInfo(id,file);
	}
});

Attachment.insertWysiwyg.video = function(id,file,editor) {
	if (file.youtube != null) {
		if (file.youtube.status == "processed") {
			if (editor.redactor("selection.getCurrent") === false) {
				editor.redactor("focus.setEnd");
			}
			
			var sHTML = '<iframe data-idx="'+file.idx+'" from="attachment"';
			sHTML+= 'src="http://www.youtube.com/embed/'+file.youtube.id+'" frameborder="0" allowfullscreen="true" ';
			if (editor.width() >= 640) {
				sHTML+= 'width="640" height="360"';
			} else {
				sHTML+= 'width="320" height="180"';
			}
			sHTML+= '></iframe>';
			
			if ($(editor.redactor("selection.getCurrent")).parents("p").length > 0) {
				editor.redactor("insert.htmlWithoutClean",sHTML);
			} else {
				editor.redactor("insert.htmlWithoutClean",'<p>'+sHTML+'</p>');
			}
			editor.redactor("code.sync");
		} else if (file.youtube.status == "uploaded") {
			alert("Please wait for encoding.");
		} else {
			alert("This video can't insert wysiwyg editor.");
		}
	} else {
		alert("This video can't insert wysiwyg editor.");
	}
};