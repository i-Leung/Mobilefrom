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

function uploadMajorProgress(file, bytesLoaded) {
	try {
	    //改变flash接收的成功上传图片数量
		var stats = majorswfu.getStats();
		stats.successful_uploads = parseInt(jQuery('#major-thumbnails').find('img').length);
		majorswfu.setStats(stats);
		
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

function uploadMajorSuccess(file, serverData) {
	try {
		var progress = new FileProgress(file,  this.customSettings.upload_target);

		if (serverData.substring(0, 7) === "FILEID:") {
			addMajorImage(serverData.substring(7));
			
			var filename = serverData.substring(7);
			
			//储存图片
			if (jQuery('input[name=thumb]').val() == '') {
				jQuery('input[name=thumb]').val(filename);
			}
			var gallery = jQuery('input[name=gallery]').val();
			jQuery('input[name=gallery]').val(
				gallery + filename + ';'
			);
			
			progress.setStatus("缩略图生成完毕");
			progress.toggleCancel(false);
		} else {
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


function addMajorImage(src) {
	//添加移除功能用
	var num = jQuery('#major-thumbnails').find('img').length;
	var img = '<img style="margin:5px;border:solid 1px #ddd;" src="/upload/mlib/thumb/' + src + '" ondragstart="return false"/>';
	var pack = '<div class="pack">\
					<div class="pack-img" onmousedown="tarselect(this, event);" onmousemove="tarmove(event);" onmouseup="tarlocate();">' + img + '</div>\
					<div class="pack-link" style="text-align:center;">\
						<a href="javascript:void(0);" onclick="removeImg(this);">删除</a>\
					</div>\
				</div>';
	jQuery('#major-thumbnails').prepend(pack);
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
	var img = parent.find('img').attr('src').replace('/upload/mlib/thumb/', '');
	jQuery(obj).parent().parent().remove();
	renewImg();
}

//图片拖曳排序
var range = { x: 0, y: 0 };//鼠标元素偏移量
var lastPos = { x: 0, y: 0, x1: 0, y1: 0 }; //拖拽对象的四个坐标
var tarPos = { x: 0, y: 0, x1: 0, y1: 0 }; //目标元素对象的坐标初始化
var theDiv = null;
var move = false;//拖拽对象 拖拽状态
var theDivId = 0;
var theDivWidth = 0;
var theDivHeight = 0;
var theDivHalfWidth = 0;
var theDivHalfHeight = 0;
var tarFirstX = 0; //拖拽对象的索引、高度、的初始化。
var tarDiv = null;
var tarFirst;
var tempDiv = null; 
function tarselect(obj, event)
{
	//拖拽对象
	theDiv = jQuery(obj).parent();
	//鼠标元素相对偏移量
	if (document.all) {
		range.x = event.x - theDiv.offset().left;
		range.y = event.y - theDiv.offset().top;
	} else {
		range.x = event.pageX - theDiv.offset().left;
		range.y = event.pageY - theDiv.offset().top;
	}
	theDivId = theDiv.index();
	theDivWidth = theDiv.width();
	theDivHalfWidth = theDivWidth / 2;
	theDivHeight = theDiv.height();
	theDivHalfHeight = theDivHeight / 2;
	move = true;
	theDiv.removeClass('pack').addClass('mvpack');
	theDiv.css('position', 'absolute');
	// 创建新元素 插入拖拽元素之前的位置(虚线框)
	jQuery('<div class="tmpack"></div>').insertAfter(theDiv);
}

function tarmove(event)
{
	if (!move) return false;
	if (document.all) {
		lastPos.x = event.x - range.x;
		lastPos.y = event.y - range.y;
	} else {
		lastPos.x = event.pageX - range.x;
		lastPos.y = event.pageY - range.y;
	}
	lastPos.x1 = lastPos.x + theDivWidth;
	// 拖拽元素随鼠标移动
	theDiv.css({left: lastPos.x + 'px',top: lastPos.y + 'px'});
	// 拖拽元素随鼠标移动 查找插入目标元素
	var packs = jQuery('.pack'); // 局部变量：按照重新排列过的顺序  再次获取 各个元素的坐标，
	tempDiv = jQuery('.tmpack'); //获得临时 虚线框的对象
	packs.each(function () {
		tarDiv = jQuery(this);
		tarPos.x = tarDiv.offset().left;
		tarPos.x1 = tarPos.x + tarDiv.width() / 2;
		tarPos.y = tarDiv.offset().top;
		tarPos.y1 = tarPos.y + tarDiv.height() / 2;
		tarFirst = packs.eq(0); // 获得第一个元素
		tarFirstX = tarFirst.offset().left + theDivHalfWidth; // 第一个元素对象的中心纵坐标
		tarFirstY = tarFirst.offset().top + theDivHalfHeight; // 第一个元素对象的中心纵坐标
		//拖拽对象 移动到第一个位置
		if (lastPos.x <= tarFirstX) {
				tempDiv.insertBefore(tarFirst);
		}
		//判断要插入目标元素的坐标后，直接插入
		/**
		if (lastPos.x >= tarPos.x && lastPos.x1 >= tarPos.x1) {
			tempDiv.insertAfter(tarDiv);
		}
		*/
		if (lastPos.x >= (tarPos.x - theDivHalfWidth) && lastPos.x1 <= (tarPos.x1 + theDivHalfWidth) && lastPos.y >= (tarPos.y - theDivHalfHeight) && lastPos.y1 <= (tarPos.y1 + theDivHalfHeight)) {
			tempDiv.insertAfter(tarDiv);
		}
	});
}

function tarlocate()
{
	theDiv.insertAfter(tempDiv);  // 拖拽元素插入到 虚线div的位置上
	theDiv.css('position', 'static');
	jQuery('.tmpack').remove(); // 删除新建的虚线div
	theDiv.removeClass('mvpack').addClass('pack');
	move = false;
	//储存图片
	jQuery('input[name=thumb]').val('');
	jQuery('input[name=gallery]').val('');
	jQuery('.pack-img img').each(function(){
		var img = jQuery(this).attr('src').replace('/upload/mlib/thumb/', '');
		if (jQuery('input[name=thumb]').val() == '') {
			jQuery('input[name=thumb]').val(img);
		}
		jQuery('input[name=gallery]').val(
			jQuery('input[name=gallery]').val() + img + ';'
		);
	});
}

function renewImg()
{
	jQuery('input[name=thumb]').val('');
	jQuery('input[name=gallery]').val('');
	jQuery('.pack-img img').each(function(){
		var img = jQuery(this).attr('src').replace('/upload/mlib/thumb/', '');
		if (jQuery('input[name=thumb]').val() == '') {
			jQuery('input[name=thumb]').val(img);
		}
		jQuery('input[name=gallery]').val(
			jQuery('input[name=gallery]').val() + img + ';'
		);
	});
}
