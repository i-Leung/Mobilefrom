/**
 * 举报相关JS代码
 */
jQuery(document).ready(function(){
	jQuery('.input_item input').focus(function(){
		var notice = jQuery(this).attr('placeholder');
		if (jQuery(this).val() == notice) {
			jQuery(this).val('');
			jQuery(this).css('color', '');
		}
	});
	jQuery('.input_item input').blur(function(){
		var notice = jQuery(this).attr('placeholder');
		if (jQuery(this).val() == '') {
			jQuery(this).css('color', '#999');
			jQuery(this).val(notice);
		}
	});
	jQuery('.iptwrap input').keydown(function(e){
		if(e.keyCode == 13){
			addContactNumber(this);
		}
	});
	jQuery('.iptwrap input').blur(function(e){
		addContactNumber(this);
	});
});

//获取举报信息进行编辑
function getReport(id)
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		data:{id:id},
		url:'/website/report/get',
		success:function(data){
			if (data != '0') {
				jQuery('input[name=id]').val(data.report.id);
				jQuery('.title_input').val(data.report.title);
				jQuery('input[name=thumb]').val(data.report.thumb);
				if (data.report.gallery != '') {
					var gallery = '';
					jQuery.each(data.report.gallery, function(k, v){
						isThumb = '';
						if (data.report.thumb == v) {
							isThumb = 'isThumb';
						}
						pic = '<img style="margin:5px;border:solid 1px #ddd;" src="' + reportSrc + 'thumb/' + v + '" onerror="loadErrorImg(this);" />';
						pack = '<div class="pack">\
									<div class="pack-img ' + isThumb + '">' + pic + '</div>\
									<div class="pack-link">\
										<a class="setcover" href="javascript:void(0);" onclick="setThumb(this);">设为封面</a>&nbsp;&nbsp;&nbsp;\
										<a href="javascript:void(0);" onclick="removeImg(this);">删除</a>\
									</div>\
								<div>';
						jQuery('#thumbnails').prepend(pack);
						gallery += v + ';';
					});
					jQuery('input[name=gallery]').val(gallery);
				}
				jQuery('.report_edit').html(data.report.content);
				jQuery('select[name=category] option[value=' + data.report.category_id + ']').attr('selected', 'selected');
				if (data.report.informer) {
					jQuery('input[name=informer]').val(data.report.informer);
				}
				var qq = '';
				var tel = '';
				jQuery.each(data.contact, function(k, v){
					switch (v.type) {
						case '2':
							qq += v.number + ';';
							var html = '<p>\
											<span>' + v.number + '</span>\
											<a title="删除" href="javascript:void(0);" onclick="removeContactNumber(this, \'mqq\');"></a>\
										</p>';
							jQuery('.tag_wrap:eq(1)').find('.tag_list').prepend(html).show();
							break;
						case '3':
							tel += v.number + ';';
							var html = '<p>\
											<span>' + v.number + '</span>\
											<a title="删除" href="javascript:void(0);" onclick="removeContactNumber(this, \'mtel\');"></a>\
										</p>';
							jQuery('.tag_wrap:eq(0)').find('.tag_list').prepend(html).show();
							break;
					}
				});
				jQuery('input[name=qq]').val(qq);
				jQuery('input[name=tel]').val(tel);
			} else {
				alert('获取指定举报信息失败，请稍候尝试');
		}
		},
		error:function(){
			alert('获取指定举报信息失败，请稍候尝试');
		}
	});
}

//添加联系方式
function addContactNumber(obj)
{
	var number = trim(jQuery(obj).val());
	if (number == '' || number == '添加多个qq号，回车确认' || number == '添加多个手机，回车确认') {
		return;
	}
	if (!isInt(number)) {
		alert('请输入数字！');
		return false;
	}
	var name = jQuery(obj).attr('name');
	switch (name) {
		case 'mtel':
			jQuery('input[name=tel]').val(jQuery('input[name=tel]').val() + number + ';');
			jQuery(obj).val('添加多个手机，回车确认').css('color', '#999');
			break;
		case 'mqq':
			jQuery('input[name=qq]').val(jQuery('input[name=qq]').val() + number + ';');
			jQuery(obj).val('添加多个qq号，回车确认').css('color', '#999');
			break;
	}
	var html = '<p>\
                	<span>' + number + '</span>\
                    <a title="删除" href="javascript:void(0);" onclick="removeContactNumber(this, \'' + name + '\');"></a>\
                </p>';
	jQuery(obj).parent().parent().find('.tag_list').prepend(html).show();
}

//删除联系方式
function removeContactNumber(obj, name)
{
	var number = jQuery(obj).parent().find('span').html();
	switch (name) {
		case 'mtel':
			jQuery('input[name=tel]').val(jQuery('input[name=tel]').val().replace(number + ';', ''));
			break;
		case 'mqq':
			jQuery('input[name=qq]').val(jQuery('input[name=qq]').val().replace(number + ';', ''));
			break;
	}
	if (jQuery(obj).parent().parent().find('p').length == 1) {
		jQuery(obj).parent().parent().hide();
	}
	jQuery(obj).parent().remove();
}

//检测是否站内用户
function checkInformer(obj)
{
	var username = trim(jQuery(obj).val());
	if (username != '') {
		jQuery.ajax({
			type:'get',
			data:{customer:username},
			dataType:'json',
			url:'/customer/account/exist',
			async:false,
			success:function(data){
				if(data == '1'){
					jQuery('.informer_check').hide();
				} else {
					jQuery('.informer_check').show();
				}
			},
			error:function(){}
		});
	} else {
		jQuery(obj).val('被举报人的机荒网帐号');
	}
}

//检测标题
function checkForm()
{
	var msg = '';
	var title = trim(jQuery('input[name=title]').val());
	if (title == '' || title.length < 5 || title.length > 50) {
		msg += '请按要求填写举报标题;\n';
	}
	var content = trim(jQuery('.report_edit').html());
	if (content == '') {
		msg += '请填写举报事件陈述;\n';
	}
	var category = jQuery('select[name=category]').val();
	if (category == '') {
		msg += '请选择举报信息类型;\n';
	}
	var thumb = trim(jQuery('input[name=thumb]').val());
	var gallery = trim(jQuery('input[name=gallery]').val());
	var informer = trim(jQuery('input[name=informer]').val());
	var tel = trim(jQuery('input[name=tel]').val());
	var qq = trim(jQuery('input[name=qq]').val());
	if (tel == '' && qq == '') {
		msg += '必须填写至少一个联系电话或QQ号码;\n';
	}
	if (msg != '') {
		alert(msg);
		return ;
	}
	var params = {
		'id':trim(jQuery('input[name=id]').val()),
		'report':{
			'category_id':category,
			'title':title,
			'content':content,
			'thumb':thumb,
			'gallery':gallery,
			'informer_id':informer,
			'qq':qq,
			'tel':tel
		}
	};
	jQuery.ajax({
		type:'post',
		dataType:'json',
		data:jQuery.param(params),
		url:'/website/report/ajax-publish',
		success:function(data){
			if (data != '0') {
				window.location.href = '/website/report/item?id=' + data;
			} else {
				alert('举报信息提交失败，请稍候尝试');
			}
		},
		error:function(){
			alert('举报信息提交失败，请稍候尝试'); 
		}
	});
}

//详细页查看大图
function showBigImg(obj)
{
	jQuery('.img_s_c').removeClass('img_s_c');
	jQuery(obj).parent().addClass('img_s_c');
	var src = jQuery(obj).find('img').attr('src');
	jQuery('.img_b img').attr('src', src.replace('thumb/', ''));
	jQuery('.img_b img').parent().attr('href', src.replace('thumb/', ''));
}

//加载大图
function loadBigImg(obj)
{
	jQuery(obj).css('padding-top', (600 - jQuery(obj).height()) / 2);
}

//小图翻页
function switchThumb(obj)
{
	var current = jQuery('.img_s_wp_box ul').css('margin-top');
	if (jQuery(obj).hasClass('img_up')) {
		var next = parseInt(current.replace('px', '')) + 550;
		jQuery('.img_s_wp_box ul').animate({margin:next + 'px 0 0 0'}, 'slow');
		jQuery('.img_down_none').removeClass('img_down_none').addClass('img_down');
		if(next == 0){
			jQuery(obj).removeClass('img_up').addClass('img_up_none');
		}
	}
	if (jQuery(obj).hasClass('img_down')) {
		var next = parseInt(current.replace('px', '')) - 550;
		jQuery('.img_s_wp_box ul').animate({margin:next + 'px 0 0 0'}, 'slow');
		jQuery('.img_up_none').removeClass('img_up_none').addClass('img_up');
		if(next + parseInt(jQuery(obj).attr('limit')) <= 0){
			jQuery(obj).removeClass('img_down').addClass('img_down_none');
		}
	}
}

//大图翻页
function switchBigImg(obj)
{
	var current = jQuery('.img_s_wp_box ul li').index(jQuery('.img_s_c'));
	if (jQuery(obj).hasClass('img_l')) {
		if (current != 0) {
			var next = current - 1;
			var page = (Math.ceil((next + 1) / 5) - 1) * 550;
			jQuery('.img_s_wp_box ul li:eq(' + next + ') a').click();
			jQuery('.img_s_wp_box ul').animate({margin:'-' + page + 'px 0 0 0'}, 'slow');
		}
	}
	if (jQuery(obj).hasClass('img_r')) {
		if (current != parseInt(jQuery(obj).attr('limit'))) {
			var next = current + 1;
			var page = (Math.ceil((next + 1) / 5) - 1) * 550;
			jQuery('.img_s_wp_box ul li:eq(' + next + ') a').click();
			jQuery('.img_s_wp_box ul').animate({margin:'-' + page + 'px 0 0 0'}, 'slow');
		}
	}
}

/**
 * 用户投票
 */
function voteReport(obj, id, who)
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		data:{id:id, who:who},
		url:'/website/report/vote',
		success:function(data){
			if (data == '1') {
				var rpoint = parseInt(jQuery('.pk_result span:eq(0) strong').html());
				var ipoint = parseInt(jQuery('.pk_result span:eq(1) strong').html());
				switch (who) {
					case 'reporter':
						rpoint ++;
						break;
					case 'informer':
						ipoint ++;
						break;
				}
				var rpercent = Math.ceil(rpoint / (rpoint + ipoint) * 100);
				var ipercent = 100 - rpercent;
				jQuery('.pk_result span:eq(0)').css('width', rpercent + '%');
				jQuery('.pk_result span:eq(0) strong').html(rpoint);
				jQuery('.pk_result span:eq(1)').css('width', ipercent + '%');
				jQuery('.pk_result span:eq(1) strong').html(ipoint);
				jQuery('.btn_support a').attr('onclick', '').addClass('disabled');
			} else {
				alert('投票失败，请稍候尝试！');
			}
		},
		error:function(){
			alert('投票失败，请稍候尝试！');
		}
	});
}

/**
 * 评论举报
 * @param 触发对象 obj
 * @param 举报信息ID rid
 * @param 父评论ID id
 * @param 接收人ID to
 * @param 是否父评论 isp
 */
function commentReport(obj, rid, id, to, isp)
{ 
	if (jQuery('.after_login').length == 0) {
		popOpen('login');
		return;
	}
	var content = '';
	if (id == '0') {
		content = jQuery('.post_box textarea').val();	
	} else {
		content = jQuery(obj).parent().parent().find('textarea').val();
	}
	if (content == '') {
		alert('请输入回复内容');
		return;
	}
	if (content.length > 140) {
		alert('输入内容不能超过140个字符');
		return;
	}
	var params = {
		comment:{
			rid:rid,
			id:id,
			to:to,
			content:content		
		}
	};
	jQuery.ajax({
		type:'get',
		dataType:'json',
		data:jQuery.param(params),
		url:'/website/report/comment',
		success:function(data){
			if (data == '1') {
				switch (isp) {
					case '1':
						var html = '<li>\
										<div class="l">\
											<img src="/skin/front/V1.1/img/user1.png" />\
										</div>\
										<div class="r">\
											<div class="com_text">\
												<a class="blue" href="javascript:void(0);">我自己</a>\
												<span>：' + content + '</span>\
											</div>\
											<div class="reply_op">\
												<span class="grey1">刚刚</span>\
											</div>\
										</div>\
										<div class="clear"></div>\
									</li>';
						jQuery('.com_lsit ul').prepend(html);
						jQuery('.post_box textarea').val('');
						jQuery('.no_comment').hide();
						break;
					case '0':
						var item = '<div class="reply_item">\
										<div class="l">\
											<img src="/skin/front/V1.1/img/user2.png" />\
										</div>\
										<div class="r">\
											<div class="reply_text">\
												<a class="blue" href="javascript:void(0);">我自己</a>\
												<span>：' + content + '</span>\
											</div>\
											<div class="reply_op">\
												<span class="grey1">刚刚</span>\
											</div>\
										</div>\
										<div class="clear"></div>\
									</div>';
						jQuery(obj).parent().parent().parent().find('.reply_list').prepend(item).show();
						jQuery(obj).parent().parent().html('');
						break;
				}
			} else if (data == '-1') {
				alert('每30秒只能评论一次喔！');
			} else {
				alert('评论失败，请稍候尝试');
			}
		},
		error:function(){
			alert('评论失败，请稍候尝试');	  
		}
	});
}

/**
 * 显示评论回复框
 * @param 触发对象 obj
 * @param 举报信息ID rid
 * @param 评论信息ID id
 * @param 评论对象 to
 * @param 是否父评论 isp
 */
function showCommentReply(obj, rid, id)
{
	var target = jQuery(obj).parent().parent().find('.reply_list');
	if (target.html() == '') {
		jQuery.ajax({
			type:'get',
			data:{pid:id},
			dataType:'json',
			url:'/website/report/load-comment-children',
			success:function(data){
				if (data != '0') {
					jQuery.each(data, function(k, v){
						var item = '<div class="reply_item">\
										<div class="l">\
											<img src="/upload/thumbnail/' + v.customer_id + '.jpg" onerror="loadErrorThumbnail(this);" />\
										</div>\
										<div class="r">\
											<div class="reply_text">\
												<a class="blue" href="/space/mobile/list?id=' + v.customer_id + '">' + v.username + '</a>\
												<span>：' + v.content + '</span>\
											</div>\
											<div class="reply_op">\
												<span class="grey1">' + v.created_at + '</span>\
												<a class="blue reply_js" href="javascript:void(0);" onclick="showCommentField(this, \'' + rid + '\', \'' + id + '\', \'' + v.customer_id + '\', \'0\');">回复</a>\
											</div>\
										</div>\
										<div class="clear"></div>\
									</div>';
						target.append(item);
					});
				}
			},
			error:function(){
				alert('加载评论回复失败，请稍候尝试');	  
			}
		});
	} else {
		target.show();
	}
}

//隐藏回复
function hideCommentReply(obj)
{
	jQuery(obj).hide();
	jQuery(obj).parent().parent().find('.reply_list').hide();
}

/**
 * 显示评论回复框
 * @param 触发对象 obj
 * @param 举报信息ID rid
 * @param 评论信息ID id
 * @param 评论对象 to
 * @param 是否父评论 isp
 */
function showCommentField(obj, rid, id, to, isp)
{
	if (jQuery(obj).find('span').length > 0) {
		showCommentReply(obj, rid, id);
	}
	jQuery(obj).parent().find('.hide_reply_list').show();
	switch (isp) {
		case '0':
			var who = jQuery(obj).parent().parent().find('.reply_text a').html();
			var field = '<p style="padding-bottom:7px;">对 <span class="blue">' + who + '</span> 说：</p>\
						<textarea maxlength="140" onfocus="focusCommentField();"></textarea>\
						<div class="bottom">\
							<span class="grey1">您还可以输入<strong>140</strong>个字</span>\
							<button type="button" onclick="commentReport(this, \'' + rid + '\', \'' + id + '\', \'' + to + '\', \'0\');">回复</button>\
							<button type="button" onclick="hideCommentField(this);" style="margin-right:5px;">取消</button>\
							<div class="clear"></div>\
						</div>';
			var target = jQuery(obj).parent().parent().parent().parent().parent().find('.reply');
			break;
		case '1':
			var who = jQuery(obj).parent().parent().find('.com_text a').html();
			var field = '<p style="padding-bottom:7px;">对 <span class="blue">' + who + '</span> 说：</p>\
						<textarea maxlength="140" onfocus="focusCommentField();"></textarea>\
						<div class="bottom">\
							<span class="grey1">您还可以输入<strong>140</strong>个字</span>\
							<button type="button" onclick="commentReport(this, \'' + rid + '\', \'' + id + '\', \'' + to + '\', \'0\');">回复</button>\
							<button type="button" onclick="hideCommentField(this);" style="margin-right:5px;">取消</button>\
							<div class="clear"></div>\
						</div>';
			var target = jQuery(obj).parent().parent().find('.reply');
			break;
	}
	target.html(field).show();
	jQuery(obj).parent().parent().find('.reply_box').show();
	jQuery(obj).parent().parent().find('.reply_list').show();
}

/**
 * 焦点于评论框时触发
 */
function focusCommentField()
{
	if (jQuery('.after_login').length == 0) {
		popOpen('login');
	}
}

/**
 * 隐藏评论框
 */
function hideCommentField(obj)
{
	jQuery(obj).parent().parent().hide();
}

//加载更多举报评论
function loadCommentList(obj, rid)
{
	var p = jQuery(obj).attr('p');
	jQuery(obj).attr('p', parseInt(p) + 1);
	jQuery.ajax({
		type:'get',
		data:{rid:rid, p:p},
		dataType:'json',
		url:'/website/report/load-comment-list',
		success:function(data){
			if (data != '0') {
				var amount = 0;
				jQuery.each(data, function(k, v){
					if (v.children == '0') {
						var children = '';
					} else {
						var children = '<a class="blue" href="javascript:void(0);" onclick="showCommentReply(this, \'' + rid + '\', \'' + v.id + '\');">显示回复<span class="grey1">（' + v.children + '）</span></a>';
					}
					var html = '<li>\
									<div class="l">\
										<img src="/upload/thumbnail/' + v.customer_id + '.jpg" onerror="loadErrorThumbnail(this);" />\
									</div>\
									<div class="r">\
										<div class="com_text">\
											<a class="blue" href="/space/mobile/list?id=' + v.customer_id + '" target="_blank">' + v.username + '</a>\
											<span>：' + v.content + '</span>\
										</div>\
										<div class="reply_op">\
											<span class="grey1">' + v.created_at + '</span>\
											' + children + '\
											<a class="blue reply_js" href="javascript:void(0);" onclick="showCommentField(this, \'' + rid + '\', \'' + v.id + '\', \'' + v.customer_id + '\', \'1\');">回复</a>\
										</div>\
										<div class="reply_box" style="display:none;">\
											<i style="display:none;"></i>\
											<div class="reply_list" style="display:none;"></div>\
											<div class="reply" style="display:none;"></div>\
										</div>\
									</div>\
									<div class="clear"></div>\
								</li>';
					jQuery('.com_lsit ul').append(html);
					amount ++;
				});
				if (amount != 10) {
					jQuery('.load_more').hide();
				}
			} else {
				jQuery('.load_more').hide();
			}
		},
		error:function(){
			alert('加载举报评论失败，请稍候尝试');	  
		}
	});
}

//显示对象相关言论
function showTargetCommentList(obj)
{
	switch (obj) {
		case 'reporter':
			if (jQuery('.bianlun_list:eq(0)').css('display') == 'none') {
				jQuery('.bianlun_list:eq(0)').slideDown('normal');
			} else {
				jQuery('.bianlun_list:eq(0)').slideUp('normal');
			}
			break;
		case 'informer':
			if (jQuery('.bianlun_list:eq(0)').css('display') == 'none') {
				jQuery('.bianlun_list:eq(1)').slideDown('normal');
			} else {
				jQuery('.bianlun_list:eq(0)').slideUp('normal');
			}
			break;
	}
}

//加载对象相关言论
function loadTargetCommentList(rid, customer, who)
{
	jQuery.ajax({
		type:'get',
		data:{rid:rid, customer:customer},
		dataType:'json',
		url:'/website/report/load-target-comment-list',
		success:function(data){
			if (data != '0') {
				jQuery.each(data, function(k, v){
					var children = '';
					jQuery.each(v.children, function(l, w){
						var child = '<div class="space">\
										<p>\
											<a class="blue" href="/space/mobile/list?cfrom=report&id=' + w.customer_id + '">' + w.username + '</a>\
											<span class="grey1" style="margin-left:10px;">' + w.created_at + '</span>\
										</p>\
										<p style="margin-top:10px;">' + w.content + '</p>\
									</div>';
						children += child;
					});
					var html = '<div class="bianlun_item">\
									<div class="white_space space">\
										<p>\
											<a class="blue" href="/space/mobile/list?cfrom=report&id=' + v.self.customer_id + '">' + v.self.username + '</a>\
											<span class="grey1" style="margin-left:10px;">' + v.self.created_at + '</span>\
										</p>\
										<p style="margin-top:10px;">' + v.self.content + '</p>\
									</div>\
									' + children + '\
								</div>';
					switch (who) {
						case 'reporter':
							jQuery('.bianlun_list:eq(0)').append(html);
							break;
						case 'informer':
							jQuery('.bianlun_list:eq(1)').append(html);
							break;
					}
				});
			}
		},
		error:function(){}
	});
}

/**
 * 用户中心查看举报评论对话
 */
function showCommentDialog(rid, id)
{
	jQuery.ajax({
		type:'get',
		data:{id:id},
		dataType:'json',
		url:'/website/report/show-comment-dialog',
		success:function(data){
			if (data != '0') {
				var fid = fname = fcontent = fcreated = ritem = '';
				jQuery.each(data, function(k, v){
					if (k == 0) {
						fid = v.customer_id;
						fname = v.username;
						fcontent = v.content;
						fcreated = v.created_at;
					} else {
						ritem += '<div class="reply_item">\
									<div class="l">\
										<img src="/upload/thumbnail/' + v.customer_id + '.jpg" onerror="loadErrorThumbnail(this);" />\
									</div>\
									<div class="r">\
										<div class="reply_text">\
											<a class="blue" href="/space/mobile/list?id=' + v.customer_id + '">' + v.username + '</a>\
											<span>：' + v.content + '</span>\
										</div>\
										<div class="reply_op">\
											<span class="grey1">' + v.created_at + '</span>\
											<a class="blue reply_js" href="javascript:void(0);" onclick="showCommentField(this, \'' + rid + '\', \'' + id + '\', \'' + v.customer_id + '\', \'0\');">回复</a>\
										</div>\
									</div>\
									<div class="clear"></div>\
								</div>';
					}
				});
				var rbox = '<div class="reply">\
								<p style="padding-bottom:7px;">对 <span class="blue">' + fname + '</span> 说：</p>\
								<textarea maxlength="140" onfocus="focusCommentField();"></textarea>\
								<div class="bottom">\
									<span class="grey1">您还可以输入<strong>140</strong>个字</span>\
									<button type="button" onclick="commentReport(this, \'' + rid + '\', \'' + id + '\', \'' + fid + '\', \'0\');">回复</button>\
									<button type="button" onclick="hideCommentField(this);" style="margin-right:5px;">取消</button>\
									<div class="clear"></div>\
								</div>\
							</div>';
				var html = '<div class="l">\
								<img src="/upload/thumbnail/' + fid + '.jpg" onerror="loadErrorThumbnail(this);" />\
							</div>\
							<div class="r">\
								<div class="com_text">\
									<a class="blue" href="/space/mobile/list?id=' + fid + '" target="_blank">' + fname + '</a>\
									<span>：' + fcontent + '</span>\
								</div>\
								<div class="reply_op">\
									<span class="grey1">' + fcreated + '</span>\
									<a class="blue" href="javascript:void(0);" onclick="showCommentField(this, \'' + rid + '\', \'' + id + '\', \'' + fid + '\', \'0\');">回复</a>\
								</div>\
								<div class="reply_box">\
									<i></i>\
									<div class="reply_list">' + ritem + '</div>\
									' + rbox + '\
								</div>\
							</div>\
							<div class="clear"></div>';
				jQuery('.comment_wp').html(html);
				jQuery('.pop_window').show();
				jQuery('.dialogue_pop').css('margin-top', '140px');
			} else {
				alert('加载对话记录失败，请稍候尝试');
			}
		},
		error:function(){
			alert('加载对话记录失败，请稍候尝试');
		}
	});
}

//隐藏举报信息评论对话框
function hideCommentDialog()
{
	jQuery('.pop_window').hide();
}
