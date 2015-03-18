/**
 * 求购信息相关js
 * @author 斌
 */
//tabs1选项卡
jQuery(document).ready(function(){
	jQuery('.tabs1_item').hide();
	jQuery('.tabs1 ul li:first').addClass("tabs1_c");
	jQuery('#description').fadeIn();
	jQuery('.tabs1 ul li').click(function(e){
			e.preventDefault();
			jQuery('.tabs1_item').hide();
			jQuery('.tabs1 ul li').removeClass("tabs1_c")
			jQuery(this).addClass("tabs1_c");
			jQuery('#'+jQuery(this).attr('title')).fadeIn();
		});
});

//加载可见求购列表
function loadVisiblePurchase()
{
	if((jQuery('.list_buttom').css('display') == 'none') && (jQuery('.loading').css('display') == 'none')){
		var next = parseInt(jQuery('#buying_list').attr('page')) + 1;
		jQuery.ajax({
			type:'get',
			data:{page:next},
			dataType:'json',
			url:'/purchase/list/ajax-list',
			beforeSend: function(XMLHttpRequest){
				jQuery('.loading').show();
			},
			success:function(data){
				jQuery('.loading').hide();
				jQuery('#buying_list').attr('page', next);
				if(data == '0'){
					jQuery('.list_buttom').show();
					jQuery('.list_bt_tis').show().css('position', 'static');
				}else{
					jQuery.each(data, function(k, v){
						var item = '<li>\
									<p>' + v.created_at + '</p>\
									<a target="_blank" class="fs14 blue" href="/purchase/item?cfrom=list&id=' + v.id + '">' + v.title + '</a>\
									<strong class="red">' + v.price + '元</strong>\
									<span class="area_xingzhi grey1">' + v.region + '<i>/</i>' + v.group + '</span>\
									<div class="clear"></div>\
								</li>';
						jQuery('#buying_list ul').append(item);
						jQuery('.list_bt_tis').show();
					});
				}
			},
			error:function(){
				jQuery('.loading').hide();
				jQuery('.list_buttom').show();
				jQuery('.list_bt_tis').show().css('position', 'static');
			}
		});
	}
}

//加载搜索求购列表
function loadSearchPurchase(q)
{
	if((jQuery('.list_buttom').css('display') == 'none') && (jQuery('.loading').css('display') == 'none')){
		var next = parseInt(jQuery('#buying_list').attr('page')) + 1;
		jQuery.ajax({
			type:'get',
			data:{q:q, page:next},
			dataType:'json',
			url:'/purchase/list/ajax-search',
			beforeSend: function(XMLHttpRequest){
				jQuery('.loading').show();
			},
			success:function(data){
				jQuery('.loading').hide();
				jQuery('#buying_list').attr('page', next);
				if(data == '0'){
					jQuery('.list_buttom').show();
					jQuery('.list_bt_tis').show().css('position', 'static');
				}else{
					jQuery.each(data, function(k, v){
						var item = '<li>\
									<p>' + v.created_at + '</p>\
									<a target="_blank" class="fs14 blue" href="/purchase/item?cfrom=list&id=' + v.id + '">' + v.title + '</a>\
									<strong class="red">' + v.price + '元</strong>\
									<span class="area_xingzhi grey1">' + v.region + '<i>/</i>' + v.group + '</span>\
									<div class="clear"></div>\
								</li>';
						jQuery('#buying_list ul').append(item);
						jQuery('.list_bt_tis').show();
					});
				}
			},
			error:function(){
				jQuery('.loading').hide();
				jQuery('.list_buttom').show();
				jQuery('.list_bt_tis').show().css('position', 'static');
			}
		});
	}
}

//添加关注
function addPurchaseFavor(obj, id)
{
	jQuery.ajax({
		type:'get',
		data:{id:id, type:'2'},
		dataType:'json',
		url:'/customer/favor/add',
		success:function(data){
			if(data == '1') {
				jQuery(obj).html('取消收藏').attr('onclick', 'removePurchaseFavor(this, \'' + id + '\')');
			}
		},
		error:function(){
			alert('收藏信息失败，请稍候尝试');
		}
	});
}

//取消关注
function removePurchaseFavor(obj, id)
{
	jQuery.ajax({
		type:'get',
		data:{type:'2',id:id},
		dataType:'json',
		url:'/customer/favor/remove',
		success:function(data){
			if(data == '1') {
				jQuery(obj).html('加入收藏').attr('onclick', 'addPurchaseFavor(this, \'' + id + '\')');
			}else{
				alert('取消收藏失败，请稍候再尝试');
			}
		},
		error:function(){
			alert('取消收藏失败，请稍候再尝试');
		}
	});
}

//提交咨询
function consultPurchase()
{
	var textarea = jQuery('textarea');
	if(trim(textarea.val()) == ''){
		alert('请填写咨询内容');
		return;
	}
	if(trim(textarea.val()).length > 240){
		alert('您输入的内容已超过允许长度，请重新编辑');
		return;
	}
	var params = {
			consult:{
				type:2,
				topic_id:textarea.attr('topic'),
				ask:textarea.val(),
				answerer:textarea.attr('to')
			}
	};
	jQuery.ajax({
		type:'get',
		data:jQuery.param(params),
		dataType:'json',
		url:'/customer/message/ask-consult',
		success:function(data){
			if(data == '3'){
				alert('别太贪心喔！每次发表咨询间隔为30秒');
			}else if(data == '2'){
				alert('信息发布者不能对自己发布的信息进行咨询');
			}else if(data == '0'){
				alert('提交咨询失败，请稍候尝试');
			}else{
				var asker = jQuery('.jh_toolbar_l .yellow').html();
				var html = '<li>\
							                <dl class="zx_user">\
						            	<dt style="color:#9C9A9C;">网站会员：</dt>\
						                <dd>\
						                	<div class="user_name_time">\
						                    	<a style="color:#9C9A9C;" href="/space/mobile/list?cfrom=consult&id=' + data.asker + '">' + data.asker + '</a>\
						                        <span style="color:#9C9A9C; margin-left:10px;">' + data.asked_at + '</span>\
						                    </div>\
						                    <div class="answer_btn"></div>\
						                    <div class="clear"></div>\
						                </dd>\
						                <div class="clear"></div>\
						            </dl>\
						            <dl class="zx_ask">\
						            	<dt>咨询内容：</dt>\
						                <dd>' + data.ask + '</dd>\
						                <div class="clear"></div>\
						            </dl>\
						        </li>';
				jQuery('.consult').prepend(html);
				textarea.val('');
				jQuery('.zx_shuru strong').html('240');
			}
		},
		error:function(){
			alert('提交咨询失败，请稍候尝试');
		}
	});
}

//回复咨询
function replyConsult(obj, id, asker)
{
	var answer = trim(jQuery(obj).parent().find('input[name=answer]').val());
	if(answer == ''){
		alert('请填写回复内容');
		return;
	}
	jQuery.ajax({
		type:'get',
		data:{id:id,answer:answer,asker:asker},
		dataType:'json',
		url:'/customer/message/reply-consult',
		success:function(data){
			if(data == '1'){
				var myDate = new Date();
				var html = '<div class="answer_content">' + answer + '</div>\
		                        <div class="answer_time">' + myDate.toLocaleString() + '</div>\
		                        <div class="clear"></div>';
		        jQuery(obj).parent().html(html);
			}else{
				alert('回复失败，请稍候尝试');
			}
		},
		error:function(){
			alert('回复失败，请稍候尝试');
		}
	});
}

//显示咨询回复框
function showReplyBox(obj)
{
	var box = jQuery(obj).parent().parent().parent().parent().find('.zx_answer');
	if(box.css('display') == 'none'){
		box.show();
	}else{
		box.hide();
	}
}

//咨询翻页
function consultPaginator(id, p)
{
	if(jQuery('.consult_' + p).length > 0){
		jQuery('.consult').hide();
		jQuery('.consult_' + p).show();
		return;
	}
	jQuery.ajax({
		type:'get',
		data:{id:id, p:p},
		dataType:'json',
		url:'/purchase/item/consult',
		success:function(data){
			if(data != '0'){
				var publisher = jQuery('.customer-info').attr('customer');
				var html = '';
				jQuery.each(data, function(k, v){
					var answer_btn = '';
					var zx_answer = '';
					if(v.answer != ''){
						var zx_answer = '<dl class="zx_answer">\
								                	<dt>卖家回复：</dt>\
								                    <dd>\
								                    	<div class="answer_content">' + v.answer + '</div>\
								                        <div class="answer_time">' + v.answered_at + '</div>\
								                        <div class="clear"></div>\
								                    </dd>\
								                    <div class="clear"></div>\
								                </dl>';
					}else if(publisher == v.answerer){
						var answer_btn = '<div class="answer_btn"><a href="javascript:void(0);" onclick="showReplyBox(this);">回复</a></div>';
						var zx_answer = '<dl class="zx_answer" style="display:none;">\
									                	<dt>卖家回复：</dt>\
									                    <dd>\
									                    <input name="answer" type="text" style="width:620px;height:20px;" />\
								                    	<button type="button" style="margin-left:5px;width:50px;" onclick="replyConsult(this, \'' + v.id + '\', \'' + v.asker + '\')">提交</button>\
									                    </dd>\
									                    <div class="clear"></div>\
									                </dl>';
					}
					html += '<li>\
					                <dl class="zx_user">\
					                	<dt style="color:#9C9A9C;">网站会员：</dt>\
					                    <dd>\
					                    	<div class="user_name_time">\
					                        	<a style="color:#9C9A9C;" target="_blank" href="/space/purchase/list?cfrom=consult&id=' + v.asker + '">' + v.username + '</a>\
					                            <span style="color:#9C9A9C; margin-left:10px;">' + v.asked_at + '</span>\
					                        </div>\
					                        ' + answer_btn + '<div class="clear"></div>\
					                    </dd>\
					                    <div class="clear"></div>\
					                </dl>\
					                <dl class="zx_ask">\
					                	<dt>咨询内容：</dt>\
					                    <dd>' + v.ask + '</dd>\
					                    <div class="clear"></div>\
					                </dl>' + zx_answer + '\
					            </li>';
				});
				html = '<ul class="consult consult_' + p + '">' + html + '</ul>';
				jQuery('.consult').hide();
				jQuery('.consult-content').append(html);
			}
		},
		error:function(){
			alert('获取咨询信息失败，请稍候尝试');
		}
	});
}

//改变求购信息状态
function changeStatus(obj, id, status)
{
	jQuery.ajax({
		type:'get',
		data:{id:id,status:status},
		dataType:'json',
		url:'/customer/purchase/status',
		success:function(data){
			if(data == '1'){
				switch(status){
					case '1':
						alert('求购信息已被激活');
						window.location.reload();
						break;
					case '2':
						alert('求购信息已被置为过期');
						window.location.href = '/purchase/list';
						break;
					case '3':
						alert('求购信息已被删除');
						window.location.href = '/purchase/list';
						break;
				}
			}else{
				alert('求购信息更改状态失败，请稍候尝试');
			}
		},
		error:function(){
			alert('求购信息更改状态失败，请稍候尝试');
		}
	});
}

//刷新手机信息
function refresh(obj, id)
{
	jQuery.ajax({
		type:'get',
		data:{id:id},
		dataType:'json',
		url:'/customer/purchase/refresh',
		success:function(data){
			if(data == '1'){
				alert('该求购信息刷新成功');
				if(navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.match(/7./i)=="7."){
					window.location.reload();
				}
				jQuery(obj).removeClass('blue').addClass('grey')
						.attr('onclick', 'refreshNotice();');
			}else{
				alert('求购信息刷新失败，请稍候尝试');
			}
		},
		error:function(){
			alert('求购信息刷新失败，请稍候尝试');
		}
	});
}

//刷新提示
function refreshNotice()
{
	alert('每天信息刷新间隔为24小时，请稍候刷新');
}

//获取用户发布的其它求购信息
function loadOtherPurchase(customer_id, purchase_id)
{
	jQuery.ajax({
		type:'get',
		data:{cid:customer_id,pid:purchase_id},
		dataType:'json',
		url:'/space/purchase/other',
		success:function(data){
			if(data != '0') {
				var html = '<li>\
				            	<div class="buying_info">\
				                	<div class="buying_name">\
				                    	<a href="/purchase/item?cfrom=item_other&id=' + data.id + '">\
				                        	<span class="blue"></span>' + data.title + '\
				                        </a>\
				                    </div>\
				                    <div class="buying_price">\
				                    	价格范围：<span class="price">' + data.price + '元</span>\
				                    </div>\
				                </div>\
				            </li>';
				jQuery('#publish_other .info_list').find('.no-more').remove();
				jQuery('#publish_other .info_list').append(html);
				jQuery('#publish_other').show();
			}
		},
		error:function(){}
	});
}

//获取用户浏览记录
function loadHistoryPurchase()
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		url:'/purchase/list/history',
		success:function(data){
			if(data != 0){
				jQuery.each(data, function(k, v){
					var html = '<li>\
									<div class="buying_info">\
										<div class="buying_name">\
											<a href="/purchase/item?cfrom=history&id=' + v.id + '">' + v.title + '</a>\
										</div>\
										<div class="buying_price">\
											价格范围：<span class="price">' + v.price + '元</span>\
										</div>\
									</div>\
								</li>';
					jQuery('#history .info_list').append(html);
				});
				jQuery('#history').show();
			}
		}
	});
}

//清空用户浏览记录
function clearHistoryPurchase()
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		url:'/purchase/list/clear-history',
		success:function(data){
			if(data == '1'){
				jQuery('#history').hide();
			}else{
				alert('清空浏览记录失败，请稍候尝试');
			}
		},
		error:function(){
			alert('清空浏览记录失败，请稍候尝试');
	  	}
	});
}
