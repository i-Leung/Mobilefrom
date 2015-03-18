jQuery(document).ready(function(){
	//操作编辑框样式栏
	jQuery('.rstyle ul li').mouseover(function(){
		jQuery(this).find('.roption').show();
	});
	jQuery('.rstyle ul li').mouseleave(function(){
		jQuery(this).find('.roption').hide();
	});
});

//填写新旧程度
function setNewly(obj)
{
	if (jQuery(obj).val() == '0') {
		jQuery(obj).hide();
		jQuery(obj).parent().find('textarea').show().val('').focus();
	}
}

//手机出售信息获取焦点
function getFocus(obj)
{
	jQuery('.new-field').removeClass('new-field');
	jQuery(obj).parent().parent().addClass('new-field');
}

//添加出售记录
function addMoreSales(obj)
{
	jQuery('.new-field').removeClass('new-field');
	var prev = jQuery(obj).parent().parent().prev();
	var html = '<tr class="new-field">\
					<td><textarea class="vversion" style="width:100px;height:' + prev.find("textarea:eq(0)").height() + 'px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + prev.find("textarea:eq(0)").val() + '</textarea></td>\
					<td><textarea class="vcolor" style="height:' + prev.find("textarea:eq(1)").height() + 'px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + prev.find("textarea:eq(1)").val() + '</textarea></td>\
					<td><textarea class="vstorage" style="height:' + prev.find("textarea:eq(2)").height() + 'px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + prev.find("textarea:eq(2)").val() + '</textarea></td>\
					<td>\
						<select class="visnew" style="width:60px;" onchange="setNewly(this);">\
							<option value="1">全新</option>\
							<option value="0">二手</option>\
						</select>\
						<textarea class="vnewly" style="display:none;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">全新</textarea>\
					</td>\
					<td><textarea class="vremark" style="width:260px;height:' + prev.find("textarea:eq(4)").height() + 'px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + prev.find("textarea:eq(4)").val() + '</textarea></td>\
					<td><textarea class="vprice" style="height:' + prev.find("textarea:eq(5)").height() + 'px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + prev.find("textarea:eq(5)").val() + '</textarea></td>\
					<td><a href="javascript:void(0);" onclick="removeSalesItem(this);">删除</a></td>\
				</tr>';
	jQuery(html).insertBefore(jQuery(obj).parent().parent());
}

//删除出售记录
function removeSalesItem(obj)
{
	if (jQuery(obj).parent().parent().parent().find('tr').length > 2) {
		jQuery(obj).parent().parent().remove();
	}
}

//选择品牌
function selectMobileBrand(obj)
{
	var bid = jQuery('select[name=brand_id]').val();
	jQuery('.mlist select').hide();
	if (bid != '') {
		loadMobileModelList(bid);
	} else {
		jQuery('select[name=model_id]').html('<option value="">请选择手机型号</option>');
	}
}

//加载手机型号列表
function loadMobileModelList(brand)
{
	jQuery.ajax({
		type:'get',
		data:{bid:brand},
		dataType:'json',
		url:'/mobile/item/model-list',
		success:function(data){
			if (data != 0 ){
				var options = '';
				options += '<option value="">请选择手机型号</option>';
				jQuery.each(data, function(k, v){
					if (v != '其它' && v != '其他') {
						options += '<option value="' + k + '">' + v + '</option>';	
					}
				});
				jQuery('select[name=model_id]').html(options);
			}
		},
		error:function(){}
	});
}

//发布出售信息
function submitMobileModel(obj)
{
	var msg = '';
	var store_model_id = jQuery('input[name=store_model_id]').val();
	if (!store_model_id) {
		var brand_id = jQuery('select[name=brand_id]').val();
		if (brand_id == '') {
			msg += '请选择要发布的手机品牌；\n';	
		}
		var model_id = jQuery('select[name=model_id]').val();
		if (model_id == '') {
			msg += '请选择要发布的手机型号；\n';	
		}
	}
	var vversion = new Array();
	jQuery('.vversion').each(function(){
		if (jQuery(this).val() != '') {
			vversion.push(jQuery(this).val());
		} else {
			msg += '请填写手机出售版本；\n';
			return;
		}
	});
	var vcolor = new Array();
	jQuery('.vcolor').each(function(){
		if (jQuery(this).val() != '') {
			vcolor.push(jQuery(this).val());
		} else {
			msg += '请填写出售手机颜色；\n';
			return;
		}
	});
	var vstorage = new Array();
	jQuery('.vstorage').each(function(){
		if (jQuery(this).val() != '' && isInt(jQuery(this).val())) {
			vstorage.push(jQuery(this).val());
		} else {
			msg += '请填写出售手机机身容量；\n';
			return;
		}
	});
	var visnew = new Array();
	jQuery('.visnew').each(function(){
		visnew.push(jQuery(this).val());
	});
	var vnewly = new Array();
	jQuery('.vnewly').each(function(){
		if (jQuery(this).val() != '') {
			vnewly.push(jQuery(this).val());
		} else {
			msg += '请填写出售手机新旧程度；\n';
			return;
		}
	});
	var vremark = new Array();
	jQuery('.vremark').each(function(){
		vremark.push(jQuery(this).val());
	});
	var vprice = new Array();
	jQuery('.vprice').each(function(){
		if (jQuery(this).val() != '' && isInt(jQuery(this).val())) {
			vprice.push(jQuery(this).val());
		} else {
			msg += '请以整数填写手机出售价格；\n';
			return;
		}
	});
	var remark = jQuery('#remark').html();
	var gallery = jQuery('input[name=gallery]').val();
	if (msg != '') {
		alert(msg);
		return;
	}
	if (store_model_id) {
		var params = {
			model:{
				store_model_id:store_model_id,
				remark:remark,
				gallery:gallery		
			},
			mobile:{
				version:vversion,
				color:vcolor,
				storage:vstorage,
				isnew:visnew,
				newly:vnewly,
				remark:vremark,
				price:vprice
			}
		};
	} else {
		var params = {
			model:{
				brand_id:brand_id,
				model_id:model_id,
				remark:remark,
				gallery:gallery		
			},
			mobile:{
				version:vversion,
				color:vcolor,
				storage:vstorage,
				isnew:visnew,
				newly:vnewly,
				remark:vremark,
				price:vprice
			}
		};
	}
	jQuery.ajax({
		type:'post',
		dataType:'json',
		data:jQuery.param(params),
		url:'/customer/store/mobile-submit',
		beforeSend:function(){
			jQuery(obj).html('真正发布...').attr('disabled', 'disabled');
		},
		success:function(data){
			if (data == '1') {
				window.location.href='/customer/store/mobile-list?cfrom=publish';
			} else {
				jQuery(obj).html('发布').removeAttr('disabled');
				alert('发布手机失败，请稍候尝试');
			}
		},
		error:function(){
			jQuery(obj).html('发布').removeAttr('disabled');
			alert('发布手机失败，请稍候尝试');
		}
	});
}

//坚持手机型号出售信息是否存在
function checkMobileModelExist()
{
	var brand_id = jQuery('select[name=brand_id]').val();
	var model_id = jQuery('select[name=model_id]').val();
	if (brand_id && model_id) {
		jQuery.ajax({
			type:'get',
			dataType:'json',
			data:{brand_id:brand_id, model_id:model_id},
			url:'/customer/store/check-mobile-model-exist',
			success:function(data){
				if (data != '0') {
					getMobileModelInfo(data, 'new');
				}
			},
			error:function(){}
		});
	}
}

//加载手机型号出售信息
function getMobileModelInfo(id, from)
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		data:{id:id},
		url:'/customer/store/mobile-info',
		success:function(data){
			if (data != '0') {
				jQuery('#mobile-publish tbody:eq(0)').html(
					'<tr>\
						<th>\
							<label>当前型号：</label>\
						</th>\
						<td>\
							<span style="font-size:14px;">' + data.label + '</span>\
							<input type="hidden" name="store_model_id" value="' + data.store_model_id + '" />\
						</td>\
					</tr>'
				);
				jQuery('#remark').prepend(data.remark);
				jQuery.each(data.gallery, function(k, v){
					if (v) {
						jQuery('input[name=gallery]').val(
							jQuery('input[name=gallery]').val() + v + ';'
						);
						var pack = '<div class="pack">\
										<div class="pack-img" onmousedown="tarselect(this, event);" onmousemove="tarmove(event);" onmouseup="tarlocate();">\
											<img style="margin:5px;border:solid 1px #ddd;" src="/upload/store/thumb/' + v + '" ondragstart="return false"/>\
										</div>\
										<div class="pack-link" style="text-align:center;">\
											<a href="javascript:void(0);" onclick="removeImg(this);">删除</a>\
										</div>\
									</div>';
						jQuery('#store-thumbnails').prepend(pack);
					}
				});
				//手机出售信息加载
				switch (from) {
					case 'edit':
						jQuery('.sales-field tbody tr:eq(0)').remove();
						break;
					default:
						break;
				}
				jQuery.each(data.mobile, function(k, v){
					var newly = '';
					if (v.isnew == '1') {
						newly = '<select class="visnew" style="width:60px;" onchange="setNewly(this);">\
									<option value="1">全新</option>\
									<option value="0">二手</option>\
								</select>\
								<textarea class="vnewly" style="display:none;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + v.newly + '</textarea>';
					} else {
					newly = '<select class="visnew" style="display:none;width:60px;" onchange="setNewly(this);">\
								<option value="1">全新</option>\
								<option value="0" selected="selected">二手</option>\
							</select>\
							<textarea class="vnewly" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + v.newly + '</textarea>';
					}
					var html = '<tr>\
									<td><textarea class="vversion" style="width:100px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + v.version + '</textarea></td>\
									<td><textarea class="vcolor" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + v.color + '</textarea></td>\
									<td><textarea class="vstorage" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + v.storage + '</textarea></td>\
									<td>' + newly + '</td>\
									<td><textarea class="vremark" style="width:260px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + v.remark + '</textarea></td>\
									<td><textarea class="vprice" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + v.price + '</textarea></td>\
									<td><a href="javascript:void(0);" onclick="removeSalesItem(this);">删除</a></td>\
								</tr>';
					jQuery('.sales-field tbody').prepend(html);
				});
			} else {
				alert('加载手机信息失败，请稍候尝试');
			}
		},
		error:function(){
			alert('加载手机信息失败，请稍候尝试');
		}
	});
}

//改变手机型号出售信息状态
function changeMobileModelStatus(obj, id, status)
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		data:{id:id, status:status},
		url:'/customer/store/change-mobile-model-status',
		success:function(data){
			if (data == '1') {
				switch (status) {
					case '0':
						jQuery(obj).parent().parent().remove();
						jQuery('.sales_model_' + id).remove();
						break;
					case '1':
						jQuery(obj).html('下架').attr('onclick', 'changeMobileModelStatus(this, \'' + id + '\', \'2\');');
						jQuery(obj).parent().parent().removeClass('inactive');
						jQuery('.sales_model_' + id).each(function(){
							if (jQuery(this).attr('status') == '1') {
								jQuery(this).removeClass('inactive');
							}
						});
						break;
					case '2':
						jQuery(obj).html('上架').attr('onclick', 'changeMobileModelStatus(this, \'' + id + '\', \'1\');');
						jQuery(obj).parent().parent().addClass('inactive');
						jQuery('.sales_model_' + id).addClass('inactive');
						break;
				}
			} else {
				alert('更新信息状态失败，请稍候尝试');
			}
		},
		error:function(){
			alert('更新信息状态失败，请稍候尝试');
		}
	});
}

//改变手机型号的热销状态
function changeMobileModelHot(obj, id)
{
	var type = jQuery(obj).attr('v');
	jQuery.ajax({
		type:'get',
		dataType:'json',
		data:{id:id, type:type},
		url:'/customer/store/change-mobile-model-hot',
		success:function(data){
			if (data == '1') {
				switch (type) {
					case '0':
						jQuery(obj).html('热销').attr('v', '1');
						break;
					case '1':
						jQuery(obj).html('取消热销').attr('v', '0');
						break;
				}
			} else if (data == '-1') {
				alert('您可设置的热销商品数量已达到上限，请减少后再重新设置');
			} else {
				alert('设置商品为热销商品失败，请稍候尝试');
			}
		},
		error:function(){
			alert('设置商品为热销商品失败，请稍候尝试');
		}
	});
}

//改变手机型号的推荐状态
function changeMobileModelRecommend(obj, id)
{
	var type = jQuery(obj).attr('v');
	jQuery.ajax({
		type:'get',
		dataType:'json',
		data:{id:id, type:type},
		url:'/customer/store/change-mobile-model-recommend',
		success:function(data){
			if (data == '1') {
				switch (type) {
					case '0':
						jQuery(obj).html('推荐').attr('v', '1');
						break;
					case '1':
						jQuery('.recommend').html('推荐').attr('v', '1');
						jQuery(obj).html('取消推荐').attr('v', '0');
						break;
				}
			} else {
				alert('设置商品为推荐商品失败，请稍候尝试');
			}
		},
		error:function(){
			alert('设置商品为推荐商品失败，请稍候尝试');
		}
	});
}

//编辑手机出售信息
function editMobileItem(obj, id)
{
	var parent = jQuery(obj).parent().parent();
	parent.find('td:eq(0)').html('<textarea onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" name="version" style="width:80px;">' + parent.find('td:eq(0)').html() + '</textarea>');
	parent.find('td:eq(1)').html('<textarea name="color" style="width:60px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'">' + parent.find('td:eq(1)').html() + '</textarea>');
	parent.find('td:eq(2)').html('<textarea name="storage" style="width:40px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'">' + parent.find('td:eq(2)').html() + '</textarea>');
	if (parent.find('td:eq(3)').attr('isnew') == '1') {
		parent.find('td:eq(3)').html(
			'<select name="isnew" onchange="setNewly(this);" style="width:80px;">\
				<option value="1">全新</option>\
				<option value="0">二手</option>\
			</select>\
			<textarea name="newly" style="display:none;width:80px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'">全新</textarea>'
		);
	} else {
		parent.find('td:eq(3)').html('<textarea name="newly" style="width:80px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'">' + parent.find('td:eq(3)').html() + '</textarea>');
	}
	parent.find('td:eq(4)').html('<textarea name="remark" style="width:320px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'">' + parent.find('td:eq(4)').html() + '</textarea>');
	parent.find('td:eq(5)').html('<textarea name="price" style="width:60px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'">' + parent.find('td:eq(5)').html() + '</textarea>');
	parent.find('td:eq(6)').html(
		'<a class="blue" href="javascript:void(0);" onclick="saveMobileItem(this, \'' + id + '\');">保存</a>\
		<a class="blue" href="javascript:void(0);" onclick="cancleEditMobileItem(this, \'' + id + '\');">取消</a>'
	);
}

//取消编辑手机出售信息
function cancleEditMobileItem(obj, id)
{
	var parent = jQuery(obj).parent().parent();
	parent.find('td:eq(0)').html(parent.find('td:eq(0)').attr('v'));
	parent.find('td:eq(1)').html(parent.find('td:eq(1)').attr('v'));
	parent.find('td:eq(2)').html(parent.find('td:eq(2)').attr('v'));
	parent.find('td:eq(3)').html(parent.find('td:eq(3)').attr('v'));
	parent.find('td:eq(4)').html(parent.find('td:eq(4)').attr('v'));
	parent.find('td:eq(5)').html(parent.find('td:eq(5)').attr('v'));
	if (parent.find('td:eq(6)').attr('status') == '1') {
		parent.find('td:eq(6)').html(
			'<a class="blue" href="javascript:void(0);" onclick="editMobileItem(this, \'' + id + '\');">编辑</a>\
			<a class="blue" href="javascript:void(0);">下架</a>\
			<a class="red" href="javascript:void(0);">删除</a>'
		);
	} else {
		parent.find('td:eq(6)').html(
			'<a class="blue" href="javascript:void(0);" onclick="editMobileItem(this, \'' + id + '\');">编辑</a>\
			<a class="blue" href="javascript:void(0);">上架</a>\
			<a class="red" href="javascript:void(0);">删除</a>'
		);
	}
}

//保存手机出售信息
function saveMobileItem(obj, id)
{
	var msg = '';
	var parent = jQuery(obj).parent().parent();
	var version = parent.find('textarea[name=version]').val();
	if (version == '') {
		msg += '请填写手机出售版本；\n';	
	}
	var color = parent.find('textarea[name=color]').val();
	if (color == '') {
		msg += '请填写出售手机颜色；\n';	
	}
	var storage = parent.find('textarea[name=storage]').val();
	if (storage == '' && isInt(storage)) {
		msg += '请填写出售手机机身容量；\n';	
	}
	var isnew = parent.find('select[name=isnew]').val();
	var newly = parent.find('textarea[name=newly]').val();
	if (isnew == '0' && newly == '') {
		msg += '请填写出售手机新旧程度；\n';	
	}
	var remark = parent.find('textarea[name=remark]').val();
	var price = parent.find('textarea[name=price]').val();
	if (price == '' || !isInt(price)) {
		msg += '请整数形式填写出售手机价格；\n';	
	}
	if (msg != '') {
		alert(msg);
		return;
	}
	var params = {
		id:id,
		mobile:{
			version:version,
			color:color,
			storage:storage,
			isnew:isnew,
			newly:newly,
			remark:remark,
			price:price
		}
	};
	jQuery.ajax({
		type:'post',
		data:jQuery.param(params),
		dataType:'json',
		url:'/customer/store/save-mobile-item',
		success:function(data){
			if (data == '1') {
				parent.find('td:eq(0)').html(version).attr('v', version);
				parent.find('td:eq(1)').html(color).attr('v', color);
				parent.find('td:eq(2)').html(storage).attr('v', storage);
				parent.find('td:eq(3)').html(newly).attr('v', newly).attr('isnew', isnew);
				parent.find('td:eq(4)').html(remark).attr('v', remark);
				parent.find('td:eq(5)').html(price).attr('v', price);
				if (parent.find('td:eq(6)').attr('status') == '1') {
					parent.find('td:eq(6)').html(
						'<a class="blue" href="javascript:void(0);" onclick="editMobileItem(this, \'' + id + '\');">编辑</a>\
						<a class="blue" href="javascript:void(0);">下架</a>\
						<a class="red" href="javascript:void(0);">删除</a>'
					);
				} else {
					parent.find('td:eq(6)').html(
						'<a class="blue" href="javascript:void(0);" onclick="editMobileItem(this, \'' + id + '\');">编辑</a>\
						<a class="blue" href="javascript:void(0);">上架</a>\
						<a class="red" href="javascript:void(0);">删除</a>'
					);
				}
			} else {
				alert('修改手机出售信息失败，请稍候尝试');
			}
		},
		error:function(){
			alert('修改手机出售信息失败，请稍候尝试');
		}
	});
}

//改变手机出售信息状态
function changeMobileItemStatus(obj, id, status)
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		data:{id:id, status:status},
		url:'/customer/store/change-mobile-item-status',
		success:function(data){
			if (data == '1') {
				switch (status) {
					case '0':
						jQuery(obj).parent().parent().remove();
						break;
					case '1':
						jQuery(obj).html('下架').attr('onclick', 'changeMobileItemStatus(this, \'' + id + '\', \'2\');');
						jQuery(obj).parent().parent().removeClass('inactive');
						break;
					case '2':
						jQuery(obj).html('上架').attr('onclick', 'changeMobileItemStatus(this, \'' + id + '\', \'1\');');
						jQuery(obj).parent().parent().addClass('inactive');
						break;
				}
			} else {
				alert('更新信息状态失败，请稍候尝试');
			}
		},
		error:function(){
			alert('更新信息状态失败，请稍候尝试');
		}
	});
}

//选择品牌
function selectTabletBrand(obj)
{
	var bid = jQuery('select[name=brand_id]').val();
	jQuery('.mlist select').hide();
	if (bid != '') {
		loadTabletModelList(bid);
	} else {
		jQuery('select[name=model_id]').html('<option value="">请选择平板型号</option>');
	}
}

//加载平板型号列表
function loadTabletModelList(brand)
{
	jQuery.ajax({
		type:'get',
		data:{bid:brand},
		dataType:'json',
		url:'/tablet/item/model-list',
		success:function(data){
			if (data != 0 ){
				var options = '';
				options += '<option value="">请选择平板型号</option>';
				jQuery.each(data, function(k, v){
					if (v != '其它' && v != '其他') {
						options += '<option value="' + k + '">' + v + '</option>';	
					}
				});
				jQuery('select[name=model_id]').html(options);
			}
		},
		error:function(){}
	});
}

//发布出售信息
function submitTabletModel(obj)
{
	var msg = '';
	var store_model_id = jQuery('input[name=store_model_id]').val();
	if (!store_model_id) {
		var brand_id = jQuery('select[name=brand_id]').val();
		if (brand_id == '') {
			msg += '请选择要发布的平板品牌；\n';	
		}
		var model_id = jQuery('select[name=model_id]').val();
		if (model_id == '') {
			msg += '请选择要发布的平板型号；\n';	
		}
	}
	var vversion = new Array();
	jQuery('.vversion').each(function(){
		if (jQuery(this).val() != '') {
			vversion.push(jQuery(this).val());
		} else {
			msg += '请填写平板出售版本；\n';
			return;
		}
	});
	var vcolor = new Array();
	jQuery('.vcolor').each(function(){
		if (jQuery(this).val() != '') {
			vcolor.push(jQuery(this).val());
		} else {
			msg += '请填写出售平板颜色；\n';
			return;
		}
	});
	var vstorage = new Array();
	jQuery('.vstorage').each(function(){
		if (jQuery(this).val() != '' && isInt(jQuery(this).val())) {
			vstorage.push(jQuery(this).val());
		} else {
			msg += '请填写出售平板机身容量；\n';
			return;
		}
	});
	var visnew = new Array();
	jQuery('.visnew').each(function(){
		visnew.push(jQuery(this).val());
	});
	var vnewly = new Array();
	jQuery('.vnewly').each(function(){
		if (jQuery(this).val() != '') {
			vnewly.push(jQuery(this).val());
		} else {
			msg += '请填写出售平板新旧程度；\n';
			return;
		}
	});
	var vremark = new Array();
	jQuery('.vremark').each(function(){
		vremark.push(jQuery(this).val());
	});
	var vprice = new Array();
	jQuery('.vprice').each(function(){
		if (jQuery(this).val() != '' && isInt(jQuery(this).val())) {
			vprice.push(jQuery(this).val());
		} else {
			msg += '请以整数填写平板出售价格；\n';
			return;
		}
	});
	var remark = jQuery('#remark').html();
	var gallery = jQuery('input[name=gallery]').val();
	if (msg != '') {
		alert(msg);
		return;
	}
	if (store_model_id) {
		var params = {
			model:{
				store_model_id:store_model_id,
				remark:remark,
				gallery:gallery		
			},
			tablet:{
				version:vversion,
				color:vcolor,
				storage:vstorage,
				isnew:visnew,
				newly:vnewly,
				remark:vremark,
				price:vprice
			}
		};
	} else {
		var params = {
			model:{
				brand_id:brand_id,
				model_id:model_id,
				remark:remark,
				gallery:gallery		
			},
			tablet:{
				version:vversion,
				color:vcolor,
				storage:vstorage,
				isnew:visnew,
				newly:vnewly,
				remark:vremark,
				price:vprice
			}
		};
	}
	jQuery.ajax({
		type:'post',
		dataType:'json',
		data:jQuery.param(params),
		url:'/customer/store/tablet-submit',
		beforeSend:function(){
			jQuery(obj).html('真正发布...').attr('disabled', 'disabled');
		},
		success:function(data){
			if (data == '1') {
				window.location.href='/customer/store/tablet-list?cfrom=publish';
			} else {
				jQuery(obj).html('发布').removeAttr('disabled');
				alert('发布平板失败，请稍候尝试');
			}
		},
		error:function(){
			jQuery(obj).html('发布').removeAttr('disabled');
			alert('发布平板失败，请稍候尝试');
		}
	});
}

//坚持平板型号出售信息是否存在
function checkTabletModelExist()
{
	var brand_id = jQuery('select[name=brand_id]').val();
	var model_id = jQuery('select[name=model_id]').val();
	if (brand_id && model_id) {
		jQuery.ajax({
			type:'get',
			dataType:'json',
			data:{brand_id:brand_id, model_id:model_id},
			url:'/customer/store/check-tablet-model-exist',
			success:function(data){
				if (data != '0') {
					getTabletModelInfo(data, 'new');
				}
			},
			error:function(){}
		});
	}
}

//加载平板型号出售信息
function getTabletModelInfo(id, from)
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		data:{id:id},
		url:'/customer/store/tablet-info',
		success:function(data){
			if (data != '0') {
				jQuery('#mobile-publish tbody:eq(0)').html(
					'<tr>\
						<th>\
							<label>当前型号：</label>\
						</th>\
						<td>\
							<span style="font-size:14px;">' + data.label + '</span>\
							<input type="hidden" name="store_model_id" value="' + data.store_model_id + '" />\
						</td>\
					</tr>'
				);
				jQuery('#remark').prepend(data.remark);
				jQuery.each(data.gallery, function(k, v){
					if (v) {
						jQuery('input[name=gallery]').val(
							jQuery('input[name=gallery]').val() + v + ';'
						);
						var pack = '<div class="pack">\
										<div class="pack-img" onmousedown="tarselect(this, event);" onmousemove="tarmove(event);" onmouseup="tarlocate();">\
											<img style="margin:5px;border:solid 1px #ddd;" src="/upload/store/thumb/' + v + '" ondragstart="return false"/>\
										</div>\
										<div class="pack-link" style="text-align:center;">\
											<a href="javascript:void(0);" onclick="removeImg(this);">删除</a>\
										</div>\
									</div>';
						jQuery('#store-thumbnails').prepend(pack);
					}
				});
				//手机出售信息加载
				switch (from) {
					case 'edit':
						jQuery('.sales-field tbody tr:eq(0)').remove();
						break;
					default:
						break;
				}
				jQuery.each(data.tablet, function(k, v){
					var newly = '';
					if (v.isnew == '1') {
						newly = '<select class="visnew" style="width:60px;" onchange="setNewly(this);">\
									<option value="1">全新</option>\
									<option value="0">二手</option>\
								</select>\
								<textarea class="vnewly" style="display:none;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + v.newly + '</textarea>';
					} else {
					newly = '<select class="visnew" style="display:none;width:60px;" onchange="setNewly(this);">\
								<option value="1">全新</option>\
								<option value="0" selected="selected">二手</option>\
							</select>\
							<textarea class="vnewly" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + v.newly + '</textarea>';
					}
					var html = '<tr>\
									<td><textarea class="vversion" style="width:100px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + v.version + '</textarea></td>\
									<td><textarea class="vcolor" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + v.color + '</textarea></td>\
									<td><textarea class="vstorage" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + v.storage + '</textarea></td>\
									<td>' + newly + '</td>\
									<td><textarea class="vremark" style="width:260px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + v.remark + '</textarea></td>\
									<td><textarea class="vprice" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" onfocus="getFocus(this);">' + v.price + '</textarea></td>\
									<td><a href="javascript:void(0);" onclick="removeSalesItem(this);">删除</a></td>\
								</tr>';
					jQuery('.sales-field tbody').prepend(html);
				});
			} else {
				alert('加载平板信息失败，请稍候尝试');
			}
		},
		error:function(){
			alert('加载平板信息失败，请稍候尝试');
		}
	});
}

//改变平板型号出售信息状态
function changeTabletModelStatus(obj, id, status)
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		data:{id:id, status:status},
		url:'/customer/store/change-tablet-model-status',
		success:function(data){
			if (data == '1') {
				switch (status) {
					case '0':
						jQuery(obj).parent().parent().remove();
						jQuery('.sales_model_' + id).remove();
						break;
					case '1':
						jQuery(obj).html('下架').attr('onclick', 'changeTabletModelStatus(this, \'' + id + '\', \'2\');');
						jQuery(obj).parent().parent().removeClass('inactive');
						jQuery('.sales_model_' + id).each(function(){
							if (jQuery(this).attr('status') == '1') {
								jQuery(this).removeClass('inactive');
							}
						});
						break;
					case '2':
						jQuery(obj).html('上架').attr('onclick', 'changeTabletModelStatus(this, \'' + id + '\', \'1\');');
						jQuery(obj).parent().parent().addClass('inactive');
						jQuery('.sales_model_' + id).addClass('inactive');
						break;
				}
			} else {
				alert('更新信息状态失败，请稍候尝试');
			}
		},
		error:function(){
			alert('更新信息状态失败，请稍候尝试');
		}
	});
}

//改变平板型号的热销状态
function changeTabletModelHot(obj, id)
{
	var type = jQuery(obj).attr('v');
	jQuery.ajax({
		type:'get',
		dataType:'json',
		data:{id:id, type:type},
		url:'/customer/store/change-tablet-model-hot',
		success:function(data){
			if (data == '1') {
				switch (type) {
					case '0':
						jQuery(obj).html('热销').attr('v', '1');
						break;
					case '1':
						jQuery(obj).html('取消热销').attr('v', '0');
						break;
				}
			} else if (data == '-1') {
				alert('您可设置的热销商品数量已达到上限，请减少后再重新设置');
			} else {
				alert('设置商品为热销商品失败，请稍候尝试');
			}
		},
		error:function(){
			alert('设置商品为热销商品失败，请稍候尝试');
		}
	});
}

//改变手机型号的推荐状态
function changeTabletModelRecommend(obj, id)
{
	var type = jQuery(obj).attr('v');
	jQuery.ajax({
		type:'get',
		dataType:'json',
		data:{id:id, type:type},
		url:'/customer/store/change-tablet-model-recommend',
		success:function(data){
			if (data == '1') {
				switch (type) {
					case '0':
						jQuery(obj).html('推荐').attr('v', '1');
						break;
					case '1':
						jQuery('.recommend').html('推荐').attr('v', '1');
						jQuery(obj).html('取消推荐').attr('v', '0');
						break;
				}
			} else {
				alert('设置商品为推荐商品失败，请稍候尝试');
			}
		},
		error:function(){
			alert('设置商品为推荐商品失败，请稍候尝试');
		}
	});
}

//编辑平板出售信息
function editTabletItem(obj, id)
{
	var parent = jQuery(obj).parent().parent();
	parent.find('td:eq(0)').html('<textarea onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'" name="version" style="width:80px;">' + parent.find('td:eq(0)').html() + '</textarea>');
	parent.find('td:eq(1)').html('<textarea name="color" style="width:60px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'">' + parent.find('td:eq(1)').html() + '</textarea>');
	parent.find('td:eq(2)').html('<textarea name="storage" style="width:40px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'">' + parent.find('td:eq(2)').html() + '</textarea>');
	if (parent.find('td:eq(3)').attr('isnew') == '1') {
		parent.find('td:eq(3)').html(
			'<select name="isnew" onchange="setNewly(this);" style="width:80px;">\
				<option value="1">全新</option>\
				<option value="0">二手</option>\
			</select>\
			<textarea name="newly" style="display:none;width:80px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'">全新</textarea>'
		);
	} else {
		parent.find('td:eq(3)').html('<textarea name="newly" style="width:80px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'">' + parent.find('td:eq(3)').html() + '</textarea>');
	}
	parent.find('td:eq(4)').html('<textarea name="remark" style="width:320px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'">' + parent.find('td:eq(4)').html() + '</textarea>');
	parent.find('td:eq(5)').html('<textarea name="price" style="width:60px;" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'">' + parent.find('td:eq(5)').html() + '</textarea>');
	parent.find('td:eq(6)').html(
		'<a class="blue" href="javascript:void(0);" onclick="saveTabletItem(this, \'' + id + '\');">保存</a>\
		<a class="blue" href="javascript:void(0);" onclick="cancleEditTabletItem(this, \'' + id + '\');">取消</a>'
	);
}

//取消编辑平板出售信息
function cancleEditTabletItem(obj, id)
{
	var parent = jQuery(obj).parent().parent();
	parent.find('td:eq(0)').html(parent.find('td:eq(0)').attr('v'));
	parent.find('td:eq(1)').html(parent.find('td:eq(1)').attr('v'));
	parent.find('td:eq(2)').html(parent.find('td:eq(2)').attr('v'));
	parent.find('td:eq(3)').html(parent.find('td:eq(3)').attr('v'));
	parent.find('td:eq(4)').html(parent.find('td:eq(4)').attr('v'));
	parent.find('td:eq(5)').html(parent.find('td:eq(5)').attr('v'));
	if (parent.find('td:eq(6)').attr('status') == '1') {
		parent.find('td:eq(6)').html(
			'<a class="blue" href="javascript:void(0);" onclick="editTabletItem(this, \'' + id + '\');">编辑</a>\
			<a class="blue" href="javascript:void(0);">下架</a>\
			<a class="red" href="javascript:void(0);">删除</a>'
		);
	} else {
		parent.find('td:eq(6)').html(
			'<a class="blue" href="javascript:void(0);" onclick="editTabletItem(this, \'' + id + '\');">编辑</a>\
			<a class="blue" href="javascript:void(0);">上架</a>\
			<a class="red" href="javascript:void(0);">删除</a>'
		);
	}
}

//保存平板出售信息
function saveTabletItem(obj, id)
{
	var msg = '';
	var parent = jQuery(obj).parent().parent();
	var version = parent.find('textarea[name=version]').val();
	if (version == '') {
		msg += '请填写平板出售版本；\n';	
	}
	var color = parent.find('textarea[name=color]').val();
	if (color == '') {
		msg += '请填写出售平板颜色；\n';	
	}
	var storage = parent.find('textarea[name=storage]').val();
	if (storage == '' && isInt(storage)) {
		msg += '请填写出售平板机身容量；\n';	
	}
	var isnew = parent.find('select[name=isnew]').val();
	var newly = parent.find('textarea[name=newly]').val();
	if (isnew == '0' && newly == '') {
		msg += '请填写出售平板新旧程度；\n';	
	}
	var remark = parent.find('textarea[name=remark]').val();
	var price = parent.find('textarea[name=price]').val();
	if (price == '' || !isInt(price)) {
		msg += '请整数形式填写出售平板价格；\n';	
	}
	if (msg != '') {
		alert(msg);
		return;
	}
	var params = {
		id:id,
		tablet:{
			version:version,
			color:color,
			storage:storage,
			isnew:isnew,
			newly:newly,
			remark:remark,
			price:price
		}
	};
	jQuery.ajax({
		type:'post',
		data:jQuery.param(params),
		dataType:'json',
		url:'/customer/store/save-tablet-item',
		success:function(data){
			if (data == '1') {
				parent.find('td:eq(0)').html(version).attr('v', version);
				parent.find('td:eq(1)').html(color).attr('v', color);
				parent.find('td:eq(2)').html(storage).attr('v', storage);
				parent.find('td:eq(3)').html(newly).attr('v', newly).attr('isnew', isnew);
				parent.find('td:eq(4)').html(remark).attr('v', remark);
				parent.find('td:eq(5)').html(price).attr('v', price);
				if (parent.find('td:eq(6)').attr('status') == '1') {
					parent.find('td:eq(6)').html(
						'<a class="blue" href="javascript:void(0);" onclick="editTabletItem(this, \'' + id + '\');">编辑</a>\
						<a class="blue" href="javascript:void(0);">下架</a>\
						<a class="red" href="javascript:void(0);">删除</a>'
					);
				} else {
					parent.find('td:eq(6)').html(
						'<a class="blue" href="javascript:void(0);" onclick="editTabletItem(this, \'' + id + '\');">编辑</a>\
						<a class="blue" href="javascript:void(0);">上架</a>\
						<a class="red" href="javascript:void(0);">删除</a>'
					);
				}
			} else {
				alert('修改平板出售信息失败，请稍候尝试');
			}
		},
		error:function(){
			alert('修改平板出售信息失败，请稍候尝试');
		}
	});
}

//改变平板出售信息状态
function changeTabletItemStatus(obj, id, status)
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		data:{id:id, status:status},
		url:'/customer/store/change-tablet-item-status',
		success:function(data){
			if (data == '1') {
				switch (status) {
					case '0':
						jQuery(obj).parent().parent().remove();
						break;
					case '1':
						jQuery(obj).html('下架').attr('onclick', 'changeTabletItemStatus(this, \'' + id + '\', \'2\');');
						jQuery(obj).parent().parent().removeClass('inactive');
						break;
					case '2':
						jQuery(obj).html('上架').attr('onclick', 'changeTabletItemStatus(this, \'' + id + '\', \'1\');');
						jQuery(obj).parent().parent().addClass('inactive');
						break;
				}
			} else {
				alert('更新信息状态失败，请稍候尝试');
			}
		},
		error:function(){
			alert('更新信息状态失败，请稍候尝试');
		}
	});
}

//保存活动信息信息
function submitActivity(obj)
{
	var msg = '';
	var parent = jQuery(obj).parent().parent().parent();
	var id = jQuery('input[name=id]').val();
	var type = parent.find('select[name=type]').val();
	if (type == '') {
		msg += '请选择活动类型；\n';	
	}
	if (id && parent.find('select[name=limit]').css('display') == 'none') {
	} else {
		var limit = parent.find('select[name=limit]').val();
		if (limit == '') {
			msg += '请选择活动有效期；\n';	
		}
	}
	var title = parent.find('input[name=title]').val();
	if (title == '' || title.length > 40) {
		msg += '请按要求填写活动标题；\n';
	}
	var description = parent.find('#remark').html();
	if (description == '') {
		msg += '请填写活动详情；\n';	
	}
	var thumb = parent.find('input[name=thumb]').val();
	var gallery = parent.find('input[name=gallery]').val();
	if (gallery == '') {
		msg += '请上传活动相关图片；\n';	
	}
	if (msg != '') {
		alert(msg);
		return;
	}
	if (id && parent.find('select[name=limit]').css('display') == 'none') {
		var params = {
			id:id,
			activity:{
				type:type,
				title:title,
				description:description,
				thumb:thumb,
				gallery:gallery
			}
		};
	} else {
		var params = {
			id:id,
			activity:{
				type:type,
				limit:limit,
				title:title,
				description:description,
				thumb:thumb,
				gallery:gallery
			}
		};
	}
	jQuery.ajax({
		type:'post',
		data:jQuery.param(params),
		dataType:'json',
		url:'/customer/store/submit-activity',
		beforeSend:function(){
			jQuery(obj).html('正在提交...').attr('disabled', 'disabled');
		},
		success:function(data){
			if (data == '1') {
				window.location.href = '/customer/store/activity-list?cfrom=activity-apply';
			} else {
				jQuery(obj).html('发布').removeAttr('disabled');
				alert('修改手机出售信息失败，请稍候尝试');
			}
		},
		error:function(){
			jQuery(obj).html('发布').removeAttr('disabled');
			alert('修改手机出售信息失败，请稍候尝试');
		}
	});
}

//获取指定活动信息
function getActivityInfo(id)
{
	jQuery.ajax({
		type:'get',
		data:{id:id},
		dataType:'json',
		url:'/customer/store/get-activity',
		success:function(data){
			if (data != '0') {
				jQuery('input[name=id]').val(data.id);
				jQuery('select[name=type] option[value=' + data.type + ']').attr('selected', 'selected');
				if (data.limit != '已过期') {
					jQuery('#limit').prepend('<span>' + data.limit + '</span><a class="red" href="javascript:void(0);" onclick="editLimit();" style="padding-left:5px;">修改活动有效期</a>');
					jQuery('select[name=limit]').hide();
				}
				jQuery('input[name=title]').val(data.title);
				jQuery('#remark').html(data.description);
				jQuery('input[name=thumb]').val(data.thumb);
				jQuery.each(data.gallery, function(k, v){
					if (v) {
						jQuery('input[name=gallery]').val(
							jQuery('input[name=gallery]').val() + v + ';'
						);
						var pack = '<div class="pack">\
										<div class="pack-img" onmousedown="tarselect(this, event);" onmousemove="tarmove(event);" onmouseup="tarlocate();">\
											<img style="margin:5px;border:solid 1px #ddd;" src="/upload/store/thumb/' + v + '" ondragstart="return false"/>\
										</div>\
										<div class="pack-link" style="text-align:center;">\
											<a href="javascript:void(0);" onclick="removeImg(this);">删除</a>\
										</div>\
									</div>';
						jQuery('#store-thumbnails').prepend(pack);
					}
				});
			} else {
				alert('获取活动资料失败，请稍候尝试');
			}
		},
		error:function(){
			alert('获取活动资料失败，请稍候尝试');
		}
	});
}

//设置为可编辑模式
function execCommandOnElement(obj, commandName, value)
{
	jQuery(obj).parent().parent().hide();
    if (typeof value == "undefined") {
        value = null;
    }

    if (typeof window.getSelection != "undefined") {
        // Non-IE case
        var sel = window.getSelection();

        // Save the current selection
        var savedRanges = [];
        for (var i = 0, len = sel.rangeCount; i < len; ++i) {
            savedRanges[i] = sel.getRangeAt(i).cloneRange();
        }

        // Temporarily enable designMode so that
        // document.execCommand() will work
        document.designMode = "on";

        // Execute the command
        document.execCommand(commandName, false, value);

        // Disable designMode
        document.designMode = "off";

        // Restore the previous selection
        sel = window.getSelection();
        sel.removeAllRanges();
        for (var i = 0, len = savedRanges.length; i < len; ++i) {
            sel.addRange(savedRanges[i]);
        }
    } else if (typeof document.body.createTextRange != "undefined") {
        // IE case
		if (commandName == 'foreColor') {
			document.execCommand("ForeColor", "false", value);
		} else {
			document.selection.createRange().execCommand(commandName, value);
		}
    }
}
