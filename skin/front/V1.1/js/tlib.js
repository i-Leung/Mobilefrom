//获取平板报价信息列表
function loadModelQuote(data)
{
	jQuery.ajax({
		type:'get',
		data:{data:data},
		dataType:'json',
		url:'/tlib/list/load-model-quote',
		success:function(data){
			if (data != '0') {
				jQuery.each(data, function(k, v){
					if (v.news) {
						jQuery('#m_l_i_' + k + ' .m_l_i_a_left:eq(0)').html('<span>新机：<span class="red">￥' + v.news + '</span></span>');
						jQuery('#m_l_i_' + k + ' .m_i_r_i_lprice:eq(0)').html('<span>新机：<span class="red">￥' + v.news + '</span></span>');
					}
					if (v.used) {
						jQuery('#m_l_i_' + k + ' .m_l_i_a_right:eq(0)').html('<span>二手：<span class="red">￥' + v.used + '</span></span>');
						jQuery('#m_l_i_' + k + ' .m_i_r_i_rprice:eq(0)').html('<span>二手：<span class="red">￥' + v.used + '</span></span>');
					}
				});
			}
		},
		error:function(){}
	});
}

//加载更多平板型号信息
function loadMoreModelList(obj)
{
	var p = jQuery(obj).attr('p');
	jQuery.ajax({
		type:'get',
		data:{p:p},
		dataType:'json',
		url:'/tlib/list/more-model',
		beforeSend:function(){
			jQuery(obj).html('正在加载...');
		},
		success:function(data){
			if (data != '0') {
				if (data.length == 16) {
					jQuery(obj).html('点击加载更多').attr('p', (parseInt(p) + 1));
				} else {
					jQuery(obj).hide();
				}
				jQuery.each(data, function(k, v){
					var style = '';
					if ((k + 1) % 4 == 0) {
						style = 'class="m_l_i_a_wrap"';
					}
					var news = '<span>新机：暂无出售</span>';
					if (v.news) {
						news = '<span>新机：<span class="red">￥' + v.news + '</span></span>';
					}
					var used = '<span>二手：暂无出售</span>';
					if (v.used) {
						used = '<span>二手：<span class="red">￥' + v.used + '</span></span>';
					}
					var offers = '0';
					if (v.offers != '0') {
						offers = '<span class="red">' + v.offers + '</span>';
					}
					var html = '<li ' + style + '>\
									<div class="m_l_i_a_thumb m_l_i_a_item_layout">\
										<a href="/tlib/item?id=' + v.id + '" target="_blank">\
											<img src="' + v.thumb + '" onerror="loadErrorImg(this);" />\
										</a>\
									</div>\
									<div class="m_l_i_a_title m_l_i_a_item_layout">\
										<a href="/tlib/item?id=' + v.id + '" target="_blank" title="' + v.brand + ' ' + v.model + '">' + v.brand + ' ' + v.model + '</a>\
									</div>\
									<div class="m_l_i_a_price m_l_i_a_item_layout">\
										<div class="m_l_i_a_left">' + news + '</div>\
										<div class="m_l_i_a_right">' + used + '</div>\
										<div class="clear"></div>\
									</div>\
									<div class="m_l_i_a_sells m_l_i_a_item_layout">\
										<div class="m_l_i_a_left">\
											<span>' + offers + '条出售信息</span>\
										</div>\
										<div class="m_l_i_a_right">\
											<span>' + v.clicks + '次点阅</span>\
										</div>\
										<div class="clear"></div>\
									</div>\
								</li>';
					jQuery('.tlib_list_item_area ul').append(html);
				});
			} else {
				jQuery(obj).hide();
			}
		},
		error:function(){
			jQuery(obj).hide();
		}
	});
}
