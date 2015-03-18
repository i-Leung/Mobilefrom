//商店列表获取出售型号信息
function getStoreModelList(obj, id)
{
	var parent = jQuery(obj).parent().find('.pro_list');
	jQuery.ajax({
		type:'get',
		data:{store:id},
		dataType:'json',
		url:'/store/item/ajax-mobile-list',
		success:function(data){
			if (data != '0') {
				jQuery.each(data, function(k, v){
					parent.prepend(
						'<a href="/store/item/mobile-info?cfrom=store&store=' + id + '&model=' + v.id + '">\
							<img src="/upload/mlib/thumb/' + v.thumb + '" onerror="loadErrorImg(this);"/>\
						</a>'
					);
				});
			}
		},
		error:function(){}
	});
}
