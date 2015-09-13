Lms.video = {
	uploadUrl:"https://www.googleapis.com/upload/youtube/v3/videos?uploadType=resumable&part=snippet%2Cstatus",
	resumableUrl:null,
	uploadFile:null,
	chunkSize:10*1024*1024,
	timer:null,
	getFileSize:function(size,isKIB) {
		var depthSize = isKIB === true ? 1024 : 1000;
		if (size / depthSize / depthSize / depthSize > 1) return (size / depthSize / depthSize / depthSize).toFixed(2)+(isKIB === true ? 'GiB' : 'GB');
		else if (size / depthSize / depthSize > 1) return (size / depthSize / depthSize).toFixed(2)+(isKIB === true ? 'MiB' : 'MB');
		else if (size / depthSize > 1) return (size / depthSize).toFixed(2)+(isKIB === true ? 'KiB' : 'KB');
		return size+"B";
	},
	getUploadUrl:function() {
		if (Lms.video.uploadFile == null) return;
		var form = $("form[name=ModuleLmsWriteForm]");
		var helpBlock = form.find("input[name=file]").parents(".inputBlock, .inputInline").find(".helpBlock");
		helpBlock.html(helpBlock.attr("data-wait"));
		
		var metadata = {
			snippet:{
				title:form.find("input[name=title]").val() ? form.find("input[name=title]").val() : "UbiTube Video",
				description:"http://ubitube.moimz.com",
				categoryId:22
			},
			status:{
				privacyStatus:"public" // or public
			}
		};
		
		$.ajax({
			url:Lms.video.uploadUrl,
			method:"POST",
			contentType:"application/json",
			headers:{
				"Authorization":"Bearer "+form.find("input[name=access_token]").val(),
				"x-upload-content-length":Lms.video.uploadFile.size,
				"x-upload-content-type":Lms.video.uploadFile.type
			},
			data:JSON.stringify(metadata)
		}).complete(function(xhr) {
			if (xhr.status == 200) {
				Lms.video.resumableUrl = xhr.getResponseHeader("Location");
				$("#ModuleLmsUploadProgress > div.bar").width(0);
				$("#ModuleLmsUploadProgress").show();
				Lms.video.upload();
			} else {
				helpBlock.html(helpBlock.attr("data-error"));
				helpBlock.parents(".inputBlock, .inputInline").addClass("hasError");
			}
		});
	},
	upload:function() {
		var form = $("form[name=ModuleLmsWriteForm]");
		var helpBlock = form.find("input[name=file]").parents(".inputBlock, .inputInline").find(".helpBlock");
		Lms.video.uploadFile.chunkStart = Lms.video.uploadFile.chunkStart ? Lms.video.uploadFile.chunkStart : 0;
		Lms.video.uploadFile.chunkEnd = Lms.video.uploadFile.size > Lms.video.uploadFile.chunkStart + Lms.video.chunkSize ? Lms.video.uploadFile.chunkStart + Lms.video.chunkSize : Lms.video.uploadFile.size;
		
		$.ajax({
			url:Lms.video.resumableUrl,
			method:"PUT",
			contentType:Lms.video.uploadFile.type,
			headers:{
				"Content-Range":"bytes " + Lms.video.uploadFile.chunkStart + "-" + (Lms.video.uploadFile.chunkEnd - 1) + "/" + Lms.video.uploadFile.size
			},
			xhr:function() {
				var xhr = $.ajaxSettings.xhr();

				if (xhr.upload) {
					xhr.upload.addEventListener("progress",function(e) {
						if (e.lengthComputable) {
							var loaded = Lms.video.uploadFile.loaded + e.loaded;
							helpBlock.html(Lms.video.getFileSize(loaded)+" / "+Lms.video.getFileSize(Lms.video.uploadFile.size));
							$("#ModuleLmsUploadProgress > div.bar").width((loaded / Lms.video.uploadFile.size * 100)+"%");
						}
					},false);
				}

				return xhr;
			},
			processData:false,
			data:Lms.video.uploadFile.slice(Lms.video.uploadFile.chunkStart,Lms.video.uploadFile.chunkEnd)
		}).complete(function(xhr,status) {
			console.log("complete",xhr.status,xhr);
			if (xhr.status == 308) {
				Lms.video.uploadFile.loaded = Lms.video.uploadFile.chunkEnd;
			
				Lms.video.uploadFile.chunkStart = Lms.video.uploadFile.chunkEnd;
				Lms.video.upload();
			} else if (xhr.status == 200) {
				var data = xhr.responseJSON;
				form.find("input[name=id]").val(data.id);
				
				helpBlock.html(helpBlock.attr("data-complete"));
				helpBlock.parents(".inputBlock, .inputInline").addClass("hasSuccess");
				$("#ModuleLmsUploadProgress").hide();
				
				form.find("button[type=submit]").attr("disabled",false);
			} else {
				setTimeout(Lms.video.upload,1000);
			}
		});
	},
	waitingTimer:function(mode) {
		var form = $("form[name=ModuleLmsWriteForm]");
		var timer = $("#ModuleLmsWaitingTime");
		
		if (Lms.video.timer != null) {
			clearTimeout(Lms.video.timer);
			Lms.video.timer = null;
		}
		
		if (mode == "start") {
			timer.data("time",10);
			timer.html("10");
			form.find("button[type=submit]").attr("disabled",true);
			Lms.video.timer = setTimeout(Lms.video.waitingTimer,1000,"interval");
			return;
		}
		
		if (mode == "stop") {
			form.find("button[data-role=select]").show();
			$("#ModuleLmsSelectedFile").hide();
			form.find("input[name=file]").val("");
			form.find("button[data-role=upload]").hide();
			form.find("button[data-role=cancel]").hide();
			form.find("button[type=submit]").attr("disabled",false);
			return;
		}
		
		if (mode == "run") {
			form.find("button[data-role=select]").hide();
			form.find("button[data-role=upload]").hide();
			form.find("button[data-role=cancel]").hide();
			$("#ModuleLmsSelectedFile").hide();
			
			Lms.video.getUploadUrl();
			return;
		}
		
		if (timer.data("time") == 0) {
			Lms.video.waitingTimer("run");
		} else {
			timer.data("time",timer.data("time") - 1);
			timer.html(timer.data("time"));
			Lms.video.timer = setTimeout(Lms.video.waitingTimer,1000,"interval");
		}
	}
};

$(document).on("Lms.post.init",function(e,form) {
	console.log("init");
	
	$("#ModuleLmsUploadProgress").hide();
	
	form.find("button[data-role=select]").on("click",function() {
		form.find("input[name=file]").trigger("click");
	});
	
	form.find("input[name=file]").on("change",function(e) {
		if (e.target.files.length == 1) {
			Lms.video.uploadFile = e.target.files[0];
			Lms.video.uploadFile.loaded = 0;
			form.find("button[data-role=select]").hide();
			$("#ModuleLmsSelectedFileName").html(Lms.video.uploadFile.name);
			$("#ModuleLmsSelectedFile").show();
			form.find("button[data-role=upload]").show();
			form.find("button[data-role=cancel]").show();
			
			form.find("input[name=file]").parents(".inputBlock, .inputInline").find(".helpBlock").html("");
			
			Lms.video.waitingTimer("start");
			
//			Lms.video.getUploadUrl();
		} else {
			Lms.video.uploadFile = null;
		}
	});
});