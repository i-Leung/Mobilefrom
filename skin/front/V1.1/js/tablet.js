/**
 * 手机信息相关js
 * @author 斌
 */
//加载可见手机列表矩阵展示样式
function loadVisibleTabletByGrid()
{
	if((jQuery('.list_buttom').css('display') == 'none') && (jQuery('.loading').css('display') == 'none')){
		var next = parseInt(jQuery('#view_grid').attr('page')) + 1;
		jQuery.ajax({
			type:'get',
			data:{page:next},
			dataType:'json',
			url:'/tablet/list/ajax-list',
			beforeSend: function(XMLHttpRequest){
				jQuery('.loading').show();
			},
			success:function(data){
				jQuery('.loading').hide();
				jQuery('#view_grid').attr('page', next);
				if(data == '0'){
					jQuery('.list_buttom').show();
					jQuery('.sort_fenye').hide();
				}else{
					jQuery.each(data, function(k, v){
						var item = '<li>\
								            <div class="mb_img_name">\
								                <a target="_blank" href="/tablet/item?cfrom=list&id=' + v.id + '">\
								                    <img alt="' + v.title + '" title="' + v.title + '" src="' + baseSrc + 'thumb/' + v.thumb + '" onerror="loadErrorImg(this);" />\
													<span title="' + v.title + '">[<span class="red" style="display:inline;">' + v.region + '</span>]' + v.title + '</span>\
								                </a>\
								            </div>\
								            <div class="price_seller">\
								                <p class="price"><strong>' + v.price + '</strong></p>\
								                <p class="seller blue">' + v.created_at + '</p>\
								                <div class="clear"></div>\
								            </div>\
								        </li>';
							jQuery('#view_grid ul').append(item);
					});
				}
			},
			error:function(){
				jQuery('.loading').hide();
				jQuery('.list_buttom').show();
				jQuery('.sort_fenye').hide();
			}
		});
	}
}

//加载可见手机列表列表展示样式
function loadVisibleTabletByList()
{
	if((jQuery('.list_buttom').css('display') == 'none') && (jQuery('.loading').css('display') == 'none')){
		var next = parseInt(jQuery('#view_list').attr('page')) + 1;
		jQuery.ajax({
			type:'get',
			data:{page:next},
			dataType:'json',
			url:'/tablet/list/ajax-list',
			beforeSend: function(XMLHttpRequest){
				jQuery('.loading').show();
			},
			success:function(data){
				jQuery('.loading').hide();
				jQuery('#view_list').attr('page', next);
				if(data == '0'){
					jQuery('.list_buttom').show();
					jQuery('.sort_fenye').hide();
				}else{
					jQuery.each(data, function(k, v){
						var item = '<li>\
										<a target="_blank" class="pro_img" title="' + v.title + '" href="/tablet/item?cfrom=list&id=' + v.id + '">\
											<img title="' + v.title + '" alt="' + v.title + '" src="' + baseSrc + 'thumb/' + v.thumb + '" onerror="loadErrorImg(this);" />\
										</a>\
										<div class="pro_info">\
											<a title="' + v.title + '" target="_blank" class="blue" href="/tablet/item?cfrom=list&id=' + v.id + '">' + v.title + '</a>\
											<p class="summary grey">' + v.summary + '</p>\
											<p class="grey">\
												<span>' + v.region + '</span> / \
												<span>' + v.created_at + '</span>\
											</p>\
										</div>\
										<p class="pro_price red">' + v.price + '</p>\
										<p class="seller_kind grey">【' + v.group + '】</p>\
										<div class="clear"></div>\
									</li>';
						jQuery('#view_list ul').append(item);
					});
				}
			},
			error:function(){
				jQuery('.loading').hide();
				jQuery('.list_buttom').show();
				jQuery('.sort_fenye').hide();
			}
		});
	}
}

//加载可见手机列表显示为列形式
function loadSearchTabletByList(q)
{
	if((jQuery('.list_buttom').css('display') == 'none') && (jQuery('.loading').css('display') == 'none')){
		var next = parseInt(jQuery('#view_list').attr('page')) + 1;
		jQuery.ajax({
			type:'get',
			data:{q:q, page:next},
			dataType:'json',
			url:'/tablet/list/ajax-search',
			beforeSend: function(XMLHttpRequest){
				jQuery('.loading').show();
			},
			success:function(data){
				jQuery('.loading').hide();
				jQuery('#view_list').attr('page', next);
				if(data == '0'){
					jQuery('.list_buttom').show();
					jQuery('.sort_fenye').hide();
				}else{
					jQuery.each(data, function(k, v){
						var item = '<li>\
										<a target="_blank" class="pro_img" title="' + v.title + '" href="/tablet/item?cfrom=list&id=' + v.id + '">\
											<img title="' + v.title + '" alt="' + v.title + '" src="' + baseSrc + 'thumb/' + v.thumb + '" onerror="loadErrorImg(this);" />\
										</a>\
										<div class="pro_info">\
											<a title="' + v.title + '" target="_blank" class="blue" href="/tablet/item?cfrom=list&id=' + v.id + '">' + v.title + '</a>\
											<p class="summary grey">' + v.summary + '</p>\
											<p class="grey">\
												<span>' + v.region + '</span> / \
												<span>' + v.created_at + '</span>\
											</p>\
										</div>\
										<p class="pro_price red">' + v.price + '</p>\
										<p class="seller_kind grey">【' + v.group + '】</p>\
										<div class="clear"></div>\
									</li>';
						jQuery('#view_list ul').append(item);
					});
				}
			},
			error:function(){
				jQuery('.loading').hide();
				jQuery('.list_buttom').show();
				jQuery('.sort_fenye').hide();
			}
		});
	}
}

//加载可见手机列表显示为块形式
function loadSearchTabletByGrid(q)
{
	if((jQuery('.list_buttom').css('display') == 'none') && (jQuery('.loading').css('display') == 'none')){
		var next = parseInt(jQuery('#view_grid').attr('page')) + 1;
		jQuery.ajax({
			type:'get',
			data:{q:q, page:next},
			dataType:'json',
			url:'/tablet/list/ajax-search',
			beforeSend: function(XMLHttpRequest){
				jQuery('.loading').show();
			},
			success:function(data){
				jQuery('.loading').hide();
				jQuery('#view_grid').attr('page', next);
				if(data == '0'){
					jQuery('.list_buttom').show();
					jQuery('.sort_fenye').hide();
				}else{
					jQuery.each(data, function(k, v){
						var item = '<li>\
								            <div class="mb_img_name">\
								                <a target="_blank" href="/tablet/item?cfrom=search&id=' + v.id + '">\
								                    <img alt="' + v.title + '" title="' + v.title + '" src="' + baseSrc + 'thumb/' + v.thumb + '" onerror="loadErrorImg(this);" />\
								                    <span title="' + v.title + '">[<span class="red" style="display:inline;">' + v.region + '</span>]' + v.title + '</span>\
								                </a>\
								            </div>\
								            <div class="price_seller">\
								                <p class="price"><strong>' + v.price + '</strong></p>\
								                <p class="seller blue">' + v.created_at + '</p>\
								                <div class="clear"></div>\
								            </div>\
								        </li>';
						jQuery('#view_grid ul').append(item);
					});
				}
			},
			error:function(){
				jQuery('.loading').hide();
				jQuery('.list_buttom').show();
				jQuery('.sort_fenye').hide();
			}
		});
	}
}

//添加关注
function addTabletFavor(obj, id)
{
	jQuery.ajax({
		type:'get',
		data:{id:id, type:'1'},
		dataType:'json',
		url:'/customer/favor/add',
		success:function(data){
			if(data == '1') {
				jQuery(obj).html('取消收藏').attr('onclick', 'removeTabletFavor(this, \'' + id + '\')');
			}
		},
		error:function(){
			alert('收藏信息失败，请稍候尝试');
		}
	});
}

//取消关注
function removeTabletFavor(obj, id)
{
	jQuery.ajax({
		type:'get',
		data:{type:'1',id:id},
		dataType:'json',
		url:'/customer/favor/remove',
		success:function(data){
			if(data == '1') {
				jQuery(obj).html('加入收藏').attr('onclick', 'addTabletFavor(this, \'' + id + '\')');
			}else{
				alert('取消收藏失败，请稍候再尝试');
			}
		},
		error:function(){
			alert('取消收藏失败，请稍候再尝试');
		}
	});
}

//添加手机对比项
function addCompare(obj, id, title, thumb, from)
{
	jQuery.ajax({
		type:'get',
		data:{id:id},
		dataType:'json',
		url:'/tablet/list/add-compare',
		success:function(data){
			jQuery('.compare').show();
			if(data == '0'){
				alert('已选对比手机数量达到4个，请先减速在选数量，再重新添加');
			}
			if(data == '1'){
				if(from == 'list'){
					var parent = jQuery(obj).parent().parent();
					var html = '<li>\
					            		<div class="mobile_img_c">\
					            			<a href="/tablet/item?cfrom=sidebox&id=' + id + '" target="_blank">\
												<img alt="' + title + '" title="' + title + '" src="' + thumb + '" onerror="loadErrorImg(this);" />\
											</a>\
							            </div>\
							            <div class="mobile_name_c">' + title + '</div>\
							            <i onclick="removeCompare(this, \'' + id + '\', \'side\')"></i>\
							            <div class="clear"></div>\
							        </li>';
					jQuery('.compare_bd ul').append(html);
				}else{
					var html = '<li>\
					            		<div class="mobile_img_c">\
					            			<a href="/tablet/item?cfrom=sidebox&id=' + id + '" target="_blank">\
												<img alt="' + title + '" title="' + title + '" src="' + thumb + '" onerror="loadErrorImg(this);" />\
											</a>\
							            </div>\
							            <div class="mobile_name_c">\
							            	<a href="/tablet/item?cfrom=sidebox&id=' + id + '" target="_blank">' + title + '</a>\
							            </div>\
							            <i onclick="removeCompare(this, \'' + id + '\', \'side\')"></i>\
							            <div class="clear"></div>\
							        </li>';
					jQuery('.compare_bd ul').append(html);
				}
			}
			if(data == '2'){
				alert('该手机信息已于对比列表中，请重新选择');
			}
		},
		error:function(){
			alert('添加对比手机失败，请稍候尝试');
		}
	});
}

//移除手机对比项
function removeCompare(obj, id, from)
{
	jQuery.ajax({
		type:'get',
		data:{id:id},
		dataType:'json',
		url:'/tablet/list/remove-compare',
		success:function(data){
			if(data == '1'){
				if(from == 'list'){
					var index = jQuery('.mobile_item').index(jQuery(obj).parent());
					jQuery('tr').each(function(){
						jQuery(this).find('td:eq(' + (index + 1) + ')').html('');
					});
				}else{
					jQuery(obj).parent().remove();
				}
			}else{
				alert('删除对比手机失败，请稍候尝试');
			}
		},
		error:function(){
			alert('删除对比手机失败，请稍候尝试');
		}
	});
}

//加载手机对比列表
function loadCompare()
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		url:'/tablet/list/loadCompare',
		success:function(data){
			if(data != '0'){
				jQuery('.none-compare').remove();
				jQuery.each(data, function(k,v){
					var html = '<li>\
	            		<div class="mobile_img_c">\
	            			<a href="/tablet/item?cfrom=sidebox&id=' + v.id + '" target="_blank">\
	            				<img alt="' + v.title + '" title="' + v.title + '" src="' + baseSrc + 'thumb/' + v.thumb + '" onerror="loadErrorImg(this);" />\
	            			</a>\
			            </div>\
			            <div class="mobile_name_c">\
			            	<a href="/tablet/item?cfrom=sidebox&id=' + v.id + '" target="_blank">' + v.title + '</a>\
			            </div>\
			            <i onclick="removeCompare(this, \'' + v.id + '\', \'side\')"></i>\
			            <div class="clear"></div>\
			        </li>';
			        jQuery('.compare_bd ul').append(html);
				});
				jQuery('.compare').show();
			}else{
				jQuery('.compare').hide();
			}
		},
		error:function(){}
	});
}

//展示手机对比列表
function showCompare()
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		url:'/tablet/list/showCompare',
		success:function(data){
			if(data != '0'){
				jQuery.each(data, function(k, v){
					jQuery('.mobile_item:eq(' + k + ')').html(
						'<a class="cancal_btn btn_blue" href="javascript:void(0);" onclick="removeCompare(this, \'' + v.id + '\', \'list\')">取消对比</a>'
					);
					var wp = '<a href="/tablet/item?cfrom=compare&id=' + v.id + '" target="_blank">\
				                		<img src="' + baseSrc + 'thumb/' + v.thumb + '" onerror="loadErrorImg(this);"/>\
				                    </a>\
				                    <div class="mobile_name">\
				                    	<a href="/tablet/item?cfrom=compare&id=' + v.id + '">' + v.title + '</a>\
				                    </div>\
				                    <a class="guanzhu btn_red" style="display:none;" href="javascript:void(0)" onclick="addTabletFavor(this, ' + v.id + ')">+加入关注</a>';
				    jQuery('.mobile_wp:eq(' + k + ')').html(wp);
				    jQuery('.mobile_cost:eq(' + k + ')').html(v.data.cost);
					jQuery('.price:eq(' + k + ')').html('￥' + v.price);
					jQuery('.mobile_newly:eq(' + k + ')').html(v.data.newly);
					jQuery('.mobile_trouble:eq(' + k + ')').html(v.data.trouble);
					jQuery('.mobile_remark:eq(' + k + ')').html(v.data.remark);
					jQuery('.contact_name:eq(' + k + ')').html(v.contact.name);
					jQuery('.contact_tel:eq(' + k + ')').html(v.contact.tel);
					jQuery('.contact_qq:eq(' + k + ')').html(v.contact.qq);
					jQuery('.contact_region:eq(' + k + ')').html(v.contact.region);
					jQuery('.contact_location:eq(' + k + ')').html(v.contact.location);
					jQuery('.contact_group:eq(' + k + ')').html(v.group);
					jQuery('.to_see:eq(' + k + ')').html('<a href="/tablet/item?cfrom=compare&id=' + v.id + '" target="_blank" class="btn_red wanted">我要这部</a>');
				});
			}
		},
		error:function(){}
	});
}

//提交咨询
function consultTablet()
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
				type:1,
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
						                    	<a style="color:#9C9A9C;" href="/space/tablet/list?cfrom=consult&id=' + data.asker + '">' + data.asker + '</a>\
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

//点击查看大图
function showGalleryPic(obj)
{
	var src = jQuery(obj).find('img').attr('src');
	jQuery('.img_b a').attr('href', '#' + src.replace('/upload/tablet/thumb/', ''));
	jQuery('.img_b img').attr('src', src);
}

//详细页缩略图翻页
function nextPic(obj)
{
	var parent = jQuery(obj).parent();
	var current = jQuery('.img_s ul').css('margin-left');
	var limit = parseInt(jQuery('.img_s').attr('num'));
	if(parent.hasClass('img_up')){
		var next = parseInt(current.replace('px', '')) + 72;
		jQuery('.img_s ul').animate({margin:'0 0 0 ' + next + 'px'}, 'slow');
		jQuery('.img_down_none').removeClass('img_down_none').addClass('img_down');
		if(next == 0){
			parent.removeClass('img_up').addClass('img_up_none');
		}
	}
	if(parent.hasClass('img_down')){
		var next = parseInt(current.replace('px', '')) - 72;
		jQuery('.img_s ul').animate({margin:'0 0 0 ' + next + 'px'}, 'slow');
		jQuery('.img_up_none').removeClass('img_up_none').addClass('img_up');
		if(limit == ((parseInt(current.replace('px', '').replace('-', '')) / 72) + 1)){
			parent.removeClass('img_down').addClass('img_down_none');
		}
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
		url:'/tablet/item/consult',
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
					                        	<a style="color:#9C9A9C;" target="_blank" href="/space/tablet/list?cfrom=consult&id=' + v.asker + '">' + v.username + '</a>\
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

//刷新手机信息
function refresh(obj, id)
{
	jQuery.ajax({
		type:'get',
		data:{id:id},
		dataType:'json',
		url:'/customer/tablet/refresh',
		success:function(data){
			if(data == '1'){
				alert('该手机信息刷新成功');
				if(navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.match(/7./i)=="7."){
					window.location.reload();
				}
				jQuery(obj).removeClass('blue').addClass('grey')
						.attr('onclick', 'refreshNotice();');
			}else if (data == '2'){
				alert('个人用户发布转让信息最多只为2条，请先删除多余信息，再尝试刷新！');
			} else {
				alert('手机信息刷新失败，请稍候尝试');
			}
		},
		error:function(){
			alert('手机信息刷新失败，请稍候尝试');
		}
	});
}

//刷新提示
function refreshNotice()
{
	alert('每天信息刷新间隔为24小时，请稍候刷新');
}

//改变手机信息状态
function changeStatus(obj, id, status)
{
	jQuery.ajax({
		type:'get',
		data:{id:id,status:status},
		dataType:'json',
		url:'/customer/tablet/status',
		success:function(data){
			if(data == '1'){
				switch(status){
					case '1':
						alert('手机信息已被激活');
						window.location.reload();
						break;
					case '2':
						alert('手机信息已被置为过期');
						window.location.href = '/tablet/list';
						break;
					case '3':
						alert('手机信息已被删除');
						window.location.href = '/tablet/list';
						break;
				}
			}else if (data == '2'){
				alert('个人用户发布转让信息最多只为2条，请先删除多余信息，再尝试激活！');
			} else {
				alert('手机信息更改状态失败，请稍候尝试');
			}
		},
		error:function(){
			alert('手机信息更改状态失败，请稍候尝试');
		}
	});
}

//获取用户发布的其它手机信息
function loadOtherTablet(customer_id, tablet_id)
{
	jQuery.ajax({
		type:'get',
		data:{cid:customer_id,mid:tablet_id},
		dataType:'json',
		url:'/space/tablet/other',
		success:function(data){
			if(data != '0') {
				var html = '<li>\
				            	<div class="mobile_info">\
				                	<div class="mb_img_s">\
				                    	<a href="/tablet/item?cfrom=item_other&id=' + data.id + '">\
				                        	<img src="' + baseSrc + 'thumb/' + data.thumb + '" onerror="loadErrorImg(this);"/>\
				                        </a>\
				                    </div>\
				                    <div class="mb_name_price">\
				                    	<div class="mb_name">\
				                        	<a href="/tablet/item?cfrom=item_other&id=' + data.id + '">\
				                            	<span class="blue">【出售】</span>' + data.title+ '\
				                            </a>\
				                        </div>\
				                    	<div class="mb_price price">￥' + data.price + '</div>\
				                    </div>\
				                    <div class="clear"></div>\
				                </div>\
				            </li>';
				jQuery('#publish_other .info_list').append(html);
				jQuery('#publish_other').show();
			}
		},
		error:function(){}
	});
}

//获取用户浏览记录
function loadHistoryTablet()
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		url:'/tablet/list/history',
		success:function(data){
			if(data != 0){
				jQuery.each(data, function(k, v){
					var html = '<li>\
									<div class="mobile_info">\
										<div class="mb_img_s">\
											<a href="/tablet/item?cfrom=history&id=' + v.id + '">\
												<img src="' + tabletSrc + 'thumb/' + v.thumb + '" onerror="loadErrorImg(this);"/>\
											</a>\
										</div>\
										<div class="mb_name_price">\
											<div class="mb_name">\
												<a href="/tablet/item?cfrom=history&id=' + v.id + '">' + v.title+ '</a>\
											</div>\
											<div class="mb_price price">￥' + v.price + '</div>\
										</div>\
										<div class="clear"></div>\
									</div>\
								</li>';
					jQuery('#history .list').append(html);
				});
				jQuery('#history').show();
			}
		}
	});
}

//清空用户浏览记录
function clearHistoryTablet()
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		url:'/tablet/list/clear-history',
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
