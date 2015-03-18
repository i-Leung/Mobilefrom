function fileQueueError(file, errorCode, message) {
	try {
		var imageName = "error.gif";
		var errorName = "";
		if (errorCode === SWFUpload.errorCode_QUEUE_LIMIT_EXCEEDED) {
			errorName = "You have attempted to queue too many files.";
		}

		if (errorName !== "") {
			alert(errorName);
			return;
		}

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			imageName = "zerobyte.gif";
			break;
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			imageName = "toobig.gif";
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
		default:
			alert(message);
			break;
		}

//		addImage("images/" + imageName);

	} catch (ex) {
		this.debug(ex);
	}
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesQueued > 0) {
			this.startUpload();
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadProgress(file, bytesLoaded) {
	try {
	    //改变flash接收的成功上传图片数量
		var stats = swfu.getStats();
		stats.successful_uploads = parseInt(jQuery('#thumbnails').find('img').length);
		swfu.setStats(stats);
		
		var percent = Math.ceil((bytesLoaded / file.size) * 100);
		var progress = new FileProgress(file,  this.customSettings.upload_target);
		progress.setProgress(percent);
		if (percent === 100) {
			progress.setStatus("正在生成缩略图...");
			progress.toggleCancel(false, this);
		} else {
			progress.setStatus("图片正在上传...");
			progress.toggleCancel(true, this);
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadSuccess(file, serverData) {
	try {
		var progress = new FileProgress(file,  this.customSettings.upload_target);

		if (serverData.substring(0, 7) === "FILEID:") {
			addImage(serverData.substring(7));
			
			var filename = serverData.substring(7);
			
			//图片文件名存入gallery值储存
			jQuery('input[name=gallery]').val(
				jQuery('input[name=gallery]').val() + filename + ';'
			);
			
			progress.setStatus("缩略图生成完毕");
			progress.toggleCancel(false);
		} else {
			addImage("images/error.gif");
			progress.setStatus("Error.");
			progress.toggleCancel(false);
			alert(serverData);

		}


	} catch (ex) {
		this.debug(ex);
	}
}

function uploadComplete(file) {
	try {
		/*  I want the next upload to continue automatically so I'll call startUpload here */
		if (this.getStats().files_queued > 0) {
			this.startUpload();
		} else {
			var progress = new FileProgress(file,  this.customSettings.upload_target);
			progress.setComplete();
			progress.setStatus("图片上传完毕");
			progress.toggleCancel(false);
			jQuery('#upload-process').css('height', '60px');
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadError(file, errorCode, message) {
	var imageName =  "error.gif";
	var progress;
	try {
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Cancelled");
				progress.toggleCancel(false);
			}
			catch (ex1) {
				this.debug(ex1);
			}
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Stopped");
				progress.toggleCancel(true);
			}
			catch (ex2) {
				this.debug(ex2);
			}
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			imageName = "uploadlimit.gif";
			break;
		default:
			alert(message);
			break;
		}

//		addImage("images/" + imageName);

	} catch (ex3) {
		this.debug(ex3);
	}
}


function addImage(src) {
	//添加移除功能用
	var num = jQuery('#thumbnails').find('img').length;
	var img = '<img style="margin:5px;border:solid 1px #ddd;" src="' + baseSrc + 'thumb/' + src + '" />';
	var pack = '<div class="pack">\
					<div class="pack-img">' + img + '</div>\
					<div class="pack-link">\
						<a href="javascript:void(0);" onclick="setThumb(this);">设为封面</a>&nbsp;&nbsp;&nbsp;\
						<a href="javascript:void(0);" onclick="removeImg(this);">删除</a>\
					</div>\
				</div>';
	jQuery('#thumbnails').prepend(pack);
}

function fadeIn(element, opacity) {
	var reduceOpacityBy = 5;
	var rate = 30;	// 15 fps


	if (opacity < 100) {
		opacity += reduceOpacityBy;
		if (opacity > 100) {
			opacity = 100;
		}

		if (element.filters) {
			try {
				element.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
			} catch (e) {
				// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
				element.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')';
			}
		} else {
			element.style.opacity = opacity / 100;
		}
	}

	if (opacity < 100) {
		setTimeout(function () {
			fadeIn(element, opacity);
		}, rate);
	}
}



/* ******************************************
 *	FileProgress Object
 *	Control object for displaying file info
 * ****************************************** */

function FileProgress(file, targetID) {
	this.fileProgressID = "divFileProgress";

	this.fileProgressWrapper = document.getElementById(this.fileProgressID);
	if (!this.fileProgressWrapper) {
		this.fileProgressWrapper = document.createElement("div");
		this.fileProgressWrapper.className = "progressWrapper";
		this.fileProgressWrapper.id = this.fileProgressID;

		this.fileProgressElement = document.createElement("div");
		this.fileProgressElement.className = "progressContainer";

		var progressCancel = document.createElement("a");
		progressCancel.className = "progressCancel";
		progressCancel.href = "#";
		progressCancel.style.visibility = "hidden";
		progressCancel.appendChild(document.createTextNode(" "));

		var progressText = document.createElement("div");
		progressText.className = "progressName";
		progressText.appendChild(document.createTextNode(file.name));

		var progressBar = document.createElement("div");
		progressBar.className = "progressBarInProgress";

		var progressStatus = document.createElement("div");
		progressStatus.className = "progressBarStatus";
		progressStatus.innerHTML = "&nbsp;";

		this.fileProgressElement.appendChild(progressCancel);
		this.fileProgressElement.appendChild(progressText);
		this.fileProgressElement.appendChild(progressStatus);
		this.fileProgressElement.appendChild(progressBar);

		this.fileProgressWrapper.appendChild(this.fileProgressElement);

		document.getElementById(targetID).appendChild(this.fileProgressWrapper);
		fadeIn(this.fileProgressWrapper, 0);

	} else {
		this.fileProgressElement = this.fileProgressWrapper.firstChild;
		this.fileProgressElement.childNodes[1].firstChild.nodeValue = file.name;
	}

	this.height = this.fileProgressWrapper.offsetHeight;

}
FileProgress.prototype.setProgress = function (percentage) {
	this.fileProgressElement.className = "progressContainer green";
	this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
	this.fileProgressElement.childNodes[3].style.width = percentage + "%";
};
FileProgress.prototype.setComplete = function () {
	this.fileProgressElement.className = "progressContainer blue";
	this.fileProgressElement.childNodes[3].className = "progressBarComplete";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setError = function () {
	this.fileProgressElement.className = "progressContainer red";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setCancelled = function () {
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setStatus = function (status) {
	this.fileProgressElement.childNodes[2].innerHTML = status;
};

FileProgress.prototype.toggleCancel = function (show, swfuploadInstance) {
	this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
	if (swfuploadInstance) {
		var fileID = this.fileProgressID;
		this.fileProgressElement.childNodes[0].onclick = function () {
			swfuploadInstance.cancelUpload(fileID);
			return false;
		};
	}
};

/**
 * edit by 斌
 */
function removeImg(obj)
{
	var parent = jQuery(obj).parent().parent();
	var img = parent.find('img').attr('src').replace(baseSrc + 'thumb/' + '', '');
	var gallery = jQuery('input[name=gallery]').val();
	var thumb = jQuery('input[name=thumb]').val();
//	jQuery.ajax({
//		type:'get',
//		data:{img:img},
//		dataType:'json',
//		url:'/plugin/swfupload/remove-img',
//		beforeSend:function(){
//			
//		},
//		success:function(data){
//			if(data != '1'){
//				alert('删除图片失败，请稍候尝试');
//			} else {
				jQuery('input[name=gallery]').val(
					gallery.replace(img + ';', '')
				);
				jQuery('input[name=thumb]').val(
					thumb.replace(img, '')
				);
				parent.remove();
				//改变flash接收的成功上传图片数量
				var stats = swfu.getStats();
				stats.successful_uploads = parseInt(jQuery('#thumbnails').find('img').length);
				swfu.setStats(stats);
//			}
//		},
//		error:function(){
//			alert('删除图片失败，请稍候尝试');
//		}
//	});
}

function setThumb(obj)
{
	var used = jQuery('.isThumb').removeClass('isThumb');
	used.parent().find('.pack-link a:eq(0)').html('设为封面');
	var parent = jQuery(obj).parent().parent();
	var img = parent.find('img').attr('src').replace(baseSrc + 'thumb/' + '', '');
	jQuery('input[name=thumb]').val(img);
	parent.find('.pack-img').addClass('isThumb');
	jQuery(obj).html('此为封面');
}
