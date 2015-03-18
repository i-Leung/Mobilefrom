/**
 * 新版手机发布JS
 */
//获取用户联系信息
function getContact()
{
	jQuery.ajax({
		type:'get',
		dataType:'json',
		url:'/customer/account/get-contact',
		success:function(data){
			if(data != '0'){
				jQuery('input[name=name]').val(data.name);
				jQuery('input[name=tel]').val(data.tel);
				jQuery('input[name=qq]').val(data.qq);
				jQuery('select[name=region] option[value=' + data.region + ']').attr('selected', 'selected');
				jQuery('input[name=location]').val(data.location);
			}
		},
		error:function(){}
	});
}

//获取指定手机信息内容
function getMobile(id)
{
	jQuery.ajax({
		type:'get',
		data:{id:id},
		dataType:'json',
		url:'/customer/mobile/get',
		async:false,
		success:function(params){
			if(params != '0'){
				var data = params.data;
				var contact = params.contact;
				//手机信息
				jQuery('input[name=title]').val(params.title);
				var gallery = '';
				jQuery.each(params.gallery, function(k, v){
			    	isThumb = '';
					if (params.thumb == v) {
						isThumb = 'isThumb';
					}
					pic = '<img style="margin:5px;border:solid 1px #ddd;" src="' + baseSrc + 'thumb/' + v + '" onerror="loadErrorImg(this);" />';
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
			    jQuery('input[name=thumb]').val(params.thumb);
				jQuery('input[name=cost][value=' + data.cost + ']').attr('checked', 'checked');
				switch(data.cost){
					case '1':
						jQuery('.trade_price').show();
						break;
					case '2':
						jQuery('.trade_exchange').show();
						break;
					case '3':
						jQuery('.trade_price').show();
						jQuery('.trade_exchange').show();
						break;
				}
				jQuery('input[name=price]').val(params.price);
				jQuery('textarea[name=exchange]').val(data.exchange);
				jQuery('input[name=newly][value=' + data.newly + ']').attr('checked', 'checked');
				/**
				jQuery('input[name=newly]').val(data.newly);
				newly_labe = '成色';
				switch (data.newly) {
					case '1':
						newly_label = '全新';
						break;
					case '2':
						newly_label = '95成新';
						break;
					case '3':
						newly_label = '9成新';
						break;
					case '4':
						newly_label = '8成新';
						break;
					case '5':
						newly_label = '7成新或以下';
						break;
				}
				jQuery('#newly-label').html('[' + newly_label + ']');
				*/
				switch (data.newly) {
					case '1':
						jQuery('.new_sale_box input[name=attachment]').val(data.attachment);
						jQuery('.new_sale_box input[name=warranty]').val(data.warranty);
						jQuery('.new_sale_box input[name=warranty]').parent().find('a[v=' + data.warranty + ']').addClass('sale_btn_c');
						if (data.warranty) {
							jQuery('input[name=isnew][value=1]').attr('checked', 'checked');
							jQuery('.new_sale_box').show();
						}
						break;
					default:
						jQuery('.used_sale_box input[name=attachment]').val(data.attachment);
						jQuery('.used_sale_box input[name=warranty]').val(data.warranty);
						jQuery('.used_sale_box input[name=warranty]').parent().find('a[v=' + data.warranty + ']').addClass('sale_btn_c');
						jQuery('.used_sale_box input[name=screen]').val(data.screen);
						jQuery('.used_sale_box input[name=screen]').parent().find('a[v=' + data.screen + ']').addClass('sale_btn_c');
						jQuery('.used_sale_box input[name=pbody]').val(data.pbody);
						jQuery('.used_sale_box input[name=pbody]').parent().find('a[v=' + data.pbody + ']').addClass('sale_btn_c');
						jQuery('.used_sale_box input[name=repair]').val(data.repair);
						jQuery('.used_sale_box input[name=repair]').parent().find('a[v=' + data.repair + ']').addClass('sale_btn_c');
						if (data.trouble != '') {
							if (data.trouble == '完全正常') {
								jQuery('.used_sale_box #trouble_field').prev('.sale_box_item').find('a[v=0]').addClass('sale_btn_c');
								jQuery('textarea[name=trouble]').val(data.trouble);
							} else {
								jQuery('.used_sale_box #trouble_field').prev('.sale_box_item').find('a[v=1]').addClass('sale_btn_c');
								jQuery('textarea[name=trouble]').val(data.trouble);
								jQuery('#trouble_field').show();
							}
						}
						if (data.warranty && data.screen && data.pbody && data.repair) {
							jQuery('input[name=isnew][value=0]').attr('checked', 'checked');
							jQuery('.used_sale_box').show();
						}
						break;
				}
				jQuery('select[name=brand] option[value=' + data.brand + ']').attr('selected', 'selected');
				if (data.brand != '15') {
					loadModelList(data.brand, data.model);
					jQuery('.brand-' + data.brand + ' option[value=' + data.model + ']').attr('selected', 'selected');
				}
				jQuery('input[name=model]').val(data.model);
				jQuery('textarea[name=remark]').val(data.remark);
				//联系信息
				jQuery('input[name=name]').val(contact.name);
				jQuery('input[name=tel]').val(contact.tel);
				jQuery('input[name=qq]').val(contact.qq);
				jQuery('select[name=region] option[value=' + contact.region + ']').attr('selected', 'selected');
				jQuery('input[name=location]').val(contact.location);
			}else{
				alert('载入手机信息失败，请稍候尝试');
			}
		},
		error:function(){
			alert('载入手机信息失败，请稍候尝试');
		}
	});
}

/**
 * 显示提示信息
 * @param 指定对象提示框 box
 * @param 提示信息类型 type
 */
function showMsg(box, type)
{
	if (type == 'empty' || type == 'error') {
		jQuery('input[name=complete]').val('0');
		if (jQuery('#anchor').length == 0) {
			jQuery('.tis_' + box).append('<a id="anchor"></a>');
		}
	}
	if (type == 'right') {
		if (jQuery('.tis_' + box).find('#anchor').length > 0) {
			jQuery('.tis_' + box).find('#anchor').remove();
		}
	}
	jQuery('.tis_' + box).show();
	jQuery('.tis_' + box + ' span').hide();
	jQuery('.tis_' + box + ' .' + type).css('display', 'block');
}

//选择品牌
function selectBrand(obj)
{
	var bid = jQuery('select[name=brand]').val();
	jQuery('.mlist select').hide();
	if (bid != '' && bid != '15') {
		if (jQuery('.brand-' + bid).length == 0) {
			loadModelList(bid, '');
		} else {
			jQuery('.brand-' + bid).show();
			jQuery('.mlist').show();
		}
	} else {
		jQuery('.mlist').hide();
	}
}

//加载手机型号列表
function loadModelList(brand, model)
{
	jQuery.ajax({
		type:'get',
		data:{bid:brand},
		dataType:'json',
		url:'/mobile/item/model-list',
		success:function(data){
			if (data != 0 ){
				var options = '';
				if (model == '') {
					options += '<option value="" selected="selected">请选择</option>';
				} else {
					options += '<option value="">请选择</option>';
				}
				jQuery.each(data, function(k, v){
					if (model == k) {
						options += '<option value="' + k + '" selected="selected">' + v + '</option>';	
					} else {
						options += '<option value="' + k + '">' + v + '</option>';	
					}
				});
				var html = '<select class="default_selt brand-' + brand + '" onchange="selectModel(this);">\
								' + options + '\
							</select>';
				jQuery('.mlist td').append(html);
				jQuery('.mlist').show();
			}
		},
		error:function(){}
	});
}

//检测手机品牌
function checkBrand()
{
	var brand = jQuery('select[name=brand]').val();	
	if (brand == '') {
		showMsg('brand', 'empty');
	} else {
		showMsg('brand', 'right');
		if (jQuery('.brand-' + brand).length > 0) {
			var model = jQuery('.brand-' + brand).val();
			if (model == '') {
				showMsg('xinghao', 'empty');
			} else {
				showMsg('xinghao', 'right');
			}
		}
	}
}

//判断是否上传图片
function checkUpload()
{
	var gallery = trim(jQuery('input[name=gallery]').val());
	if (gallery == '') {
		showMsg('upload_img', 'error');
	} else {
		var thumb = trim(jQuery('input[name=thumb]').val());
		if (thumb == '') {
			showMsg('upload_img', 'error');
		} else {
			showMsg('upload_img', 'right');
		}
	}
}

//选择是否使用过
function isUsed(obj)
{
	checkBrand();
	checkUpload();
	jQuery('.sale_box').hide();
	var isnew = jQuery(obj).find('input[name=isnew]').val();
	jQuery('.sale_box:eq(' + isnew + ')').show();
	/** 完美版发布代码
	jQuery('input[name=newly]').val(isnew);
	if (isnew == '1') {
		jQuery('#newly-label').html('[全新]');
	} else {
		switchNewly();
	}
	*/
	jQuery('input[name=newly]:checked').attr('checked', false);
	if (isnew == '1') {
		jQuery('input[name=newly][value=1]').attr('checked', 'checked');
	} else {
		switchNewly();
	}
}

//检测是否选择机况描述
function checkUsed()
{
	checkBrand();//检测品牌、型号是否选择
	checkUpload();//检测图片是否上传
	var isnew = jQuery('input[name=isnew]:checked').val();	
	if (isnew) {
		showMsg('used', 'right');
	} else {
		showMsg('used', 'focus');
	}
}

//描述屏幕状况
function describeScreen(obj)
{
	jQuery(obj).parent().parent().removeClass('sale_item_c');
	jQuery(obj).parent().find('.sale_btn_c').removeClass('sale_btn_c');
	jQuery(obj).addClass('sale_btn_c');
	var screen = jQuery(obj).attr('v');
	jQuery('input[name=screen]').val(screen);
	switchNewly();
}

//描述机身状况
function describePbody(obj)
{
	jQuery(obj).parent().parent().removeClass('sale_item_c');
	jQuery(obj).parent().find('.sale_btn_c').removeClass('sale_btn_c');
	jQuery(obj).addClass('sale_btn_c');
	var pbody = jQuery(obj).attr('v');
	jQuery('input[name=pbody]').val(pbody);
	switchNewly();
}

//定义新旧程度
function switchNewly()
{
	/**
	var newly = 0;
	var newly_label = '成色';
	var screen = jQuery('input[name=screen]').val();
	var pbody = jQuery('input[name=pbody]').val();
	if (parseInt(screen) > parseInt(pbody)) {
		newly = parseInt(screen);
	} else {
		newly = parseInt(pbody);
	}
	newly += 1;
	switch (newly) {
		case 2:
			newly_label = '95成新';
			break;
		case 3:
			newly_label = '9成新';
			break;
		case 4:
			newly_label = '8成新';
			break;
		case 5:
			newly_label = '7成新或以下';
			break;
	}
	jQuery('input[name=newly]').val(newly);
	jQuery('#newly-label').html('[' + newly_label + ']');
	*/
	var newly = 0;
	var screen = jQuery('input[name=screen]').val();
	var pbody = jQuery('input[name=pbody]').val();
	if (parseInt(screen) > parseInt(pbody)) {
		newly = parseInt(screen);
	} else {
		newly = parseInt(pbody);
	}
	newly += 1;
	jQuery('input[name=newly][value=' + newly + ']').attr('checked', 'checked');
}

//描述功能状况
function describeTrouble(obj)
{
	jQuery(obj).parent().parent().removeClass('sale_item_c');
	jQuery(obj).parent().find('.sale_btn_c').removeClass('sale_btn_c');
	jQuery(obj).addClass('sale_btn_c');
	var trouble = jQuery(obj).attr('v');
	if (trouble == '0') {
		jQuery('textarea[name=trouble]').val('完全正常');
		jQuery('#trouble_field').hide();
	} else {
		jQuery('textarea[name=trouble]').val('');
		jQuery('#trouble_field').show();
	}
}

//描述失效功能
function describeTroubleNotwork(obj)
{
	if (jQuery(obj).val() != '') {
		jQuery(obj).parent().parent().removeClass('sale_item_c');
	}
}

//描述维修史
function describeRepair(obj)
{
	jQuery(obj).parent().parent().removeClass('sale_item_c');
	jQuery(obj).parent().find('.sale_btn_c').removeClass('sale_btn_c');
	jQuery(obj).addClass('sale_btn_c');
	var repair = jQuery(obj).attr('v');
	jQuery('input[name=repair]').val(repair);
}

//描述保修期
function describeWarranty(obj)
{
	jQuery(obj).parent().parent().removeClass('sale_item_c');
	jQuery(obj).parent().find('.sale_btn_c').removeClass('sale_btn_c');
	jQuery(obj).addClass('sale_btn_c');
	var warranty = jQuery(obj).attr('v');
	jQuery(obj).parent().parent().find('input[name=warranty]').val(warranty);
}

//检测手机状况描述情况
function checkMobileDescription()
{
	checkUsed();
	var isnew = jQuery('input[name=isnew]:checked').val();
	switch (isnew) {
		case '0':
			var screen = trim(jQuery('input[name=screen]').val());
			if (screen == '') {
				jQuery('.used_sale_box .sale_box_item:eq(0)').addClass('sale_item_c');
			}
			var pbody = trim(jQuery('input[name=pbody]').val());
			if (pbody == '') {
				jQuery('.used_sale_box .sale_box_item:eq(1)').addClass('sale_item_c');
			}
			var trouble = trim(jQuery('textarea[name=trouble]').val());
			if (trouble == '') {
				if (jQuery('#trouble_field').css('display') == 'block') {
					jQuery('.used_sale_box .sale_box_item:eq(3)').addClass('sale_item_c');
				} else {
					jQuery('.used_sale_box .sale_box_item:eq(2)').addClass('sale_item_c');
				}
			} else if (trouble == '描述长度不能少于6个字，大于200个字') {
				jQuery('.used_sale_box .sale_box_item:eq(3)').addClass('sale_item_c');
			} else if (trouble != '完全正常' && (trouble.length < 6 || trouble.length > 200)){
				jQuery('.used_sale_box .sale_box_item:eq(3)').addClass('sale_item_c');
			} else {}
			var repair = trim(jQuery('input[name=repair]').val());
			if (repair == '') {
				jQuery('.used_sale_box .sale_box_item:eq(4)').addClass('sale_item_c');
			}
			var warranty = trim(jQuery('.used_sale_box input[name=warranty]').val());
			if (warranty == '') {
				jQuery('.used_sale_box .sale_box_item:eq(5)').addClass('sale_item_c');
			}
			if (jQuery('.used_sale_box .sale_item_c').length > 0) {
				jQuery('input[name=complete]').val('0');
			}
			break;
		case '1':
			var warranty = jQuery('.new_sale_box input[name=warranty]').val();
			if (warranty == '') {
				jQuery('.new_sale_box .sale_box_item:eq(0)').addClass('sale_item_c');
			}
			if (jQuery('.new_sale_box .sale_item_c').length > 0) {
				jQuery('input[name=complete]').val('0');
			}
			break;
		default:
			break;
	}
}

//选择交易类型
function chooseCost(obj)
{
	checkMobileDescription();
	jQuery('.trade_box').hide();
	var cost = jQuery(obj).find('input[name=cost]').val();
	switch (cost) {
		case '1':
			jQuery('.tis_tiaojian').hide();
			jQuery('.trade_price').show();
			break;
		case '2':
			jQuery('.tis_price').hide();
			jQuery('.trade_exchange').show();
			break;
		case '3':
			jQuery('.tis_price').css('display', 'block');
			jQuery('.tis_tiaojian').css('display', 'block');
			jQuery('.trade_price').show();
			jQuery('.trade_exchange').show();
			break;
	}
}

//检测是否选择新旧程度
function checkNewly()
{
	var newly = jQuery('input[name=newly]:checked').val();
	if (newly) {
		showMsg('newly', 'right');
	} else {
		showMsg('newly', 'error');
	}
}

//检测交易类型填写
function checkCost()
{
	showMsg('title', 'focus');
	checkMobileDescription();
	checkNewly();
	var cost = jQuery('input[name=cost]:checked').val();
	var price = trim(jQuery('input[name=price]').val());
	var exchange = trim(jQuery('textarea[name=exchange]').val());
	switch (cost) {
		case '1':
			if (price == '') {
				showMsg('price', 'empty');
			} else {
				if (!isInt(price)) {
					showMsg('price', 'error');
				} else {
					showMsg('price', 'right');
				}
			}
			break;
		case '2':
			if (exchange == '') {
				showMsg('tiaojian', 'empty');
			} else {
				var elen = exchange.length;
				if (elen < 6 || elen > 50) {
					showMsg('tiaojian', 'error');
				} else {
					showMsg('tiaojian', 'right');
				}
			}
			break;
		case '3':
			if (price == '') {
				showMsg('price', 'empty');
			} else {
				if (!isInt(price)) {
					showMsg('price', 'error');
				} else {
					showMsg('price', 'right');
				}
			}
			if (exchange == '') {
				showMsg('tiaojian', 'empty');
			} else {
				var elen = exchange.length;
				if (elen < 6 || elen > 50) {
					showMsg('tiaojian', 'error');
				} else {
					showMsg('tiaojian', 'right');
				}
			}
			break;
	}
}

//检测是否填写标题
function checkTitle()
{
	showMsg('miaoshu', 'focus');
	checkCost();
	var title = trim(jQuery('input[name=title]').val());
	if (title == '') {
		showMsg('title', 'empty');
	} else {
		if (title.length < 6 || title.length > 50) {
			showMsg('title', 'error');
		} else {
			showMsg('title', 'right');
		}
	}
}

//检测是否填写备注
function checkRemark()
{
	jQuery('input[name=complete]').val('1');
	checkTitle();
	var remark = trim(jQuery('textarea[name=remark]').val());
	if (remark == '') {
		showMsg('miaoshu', 'empty');
	} else {
		if (remark.length < 10 || remark.length > 2000) {
			showMsg('miaoshu', 'error');
		} else {
			showMsg('miaoshu', 'right');
			if (jQuery('input[name=complete]').val() == '1') {
				jQuery('#base_info').hide();
				jQuery('#contact_info').show();
				jQuery('.step_one').removeClass('step_one').addClass('step_two');
			}
		}
	}
	jQuery("html").animate({scrollTop: jQuery("#anchor").offset().top - 100}, 'fast');
	jQuery("body").animate({scrollTop: jQuery("#anchor").offset().top - 100}, 'fast');
}

//检测联系人是否为空
function checkName()
{
	showMsg('phone', 'focus');
	var name = trim(jQuery('input[name=name]').val());
	if (name == '') {
		showMsg('lxr', 'empty');	
	} else {
		if (name.length < 2 || name.length > 10) {
			showMsg('lxr', 'error');
		} else {
			showMsg('lxr', 'right');
		}
	}
}

//检测联系电话是否为空
function checkTel()
{
	showMsg('qq', 'focus');
	checkName();
	var tel = trim(jQuery('input[name=tel]').val());
	if (tel == '') {
		showMsg('phone', 'empty');	
	} else {
		showMsg('phone', 'right');
	}
}

//检测QQ是否为空
function checkQQ()
{
	checkTel();
	var qq = trim(jQuery('input[name=qq]').val());
	if (qq == '') {
		showMsg('qq', 'empty');	
	} else {
		if (qq.length >= 2 && qq.length <= 13) {
			showMsg('qq', 'right');
		} else {
			showMsg('qq', 'error');
		}
	}
}

//检测交易地区是否选择
function checkRegion()
{
	showMsg('didian', 'focus');
	checkQQ();
	var region = jQuery('select[name=region]').val();
	if (region == '') {
		showMsg('area', 'empty');	
	} else {
		showMsg('area', 'right');
	}
}

//检测交易地点是否为空
function checkLocation(obj)
{
	jQuery('input[name=complete]').val('1');
	checkRegion();
	var location = jQuery('input[name=location]').val();
	if (location == '') {
		showMsg('didian', 'empty');	
	} else {
		if (location.length < 3 || location.length > 30) {
			showMsg('didian', 'error');
		} else {
			showMsg('didian', 'right');
			if (jQuery('input[name=complete]').val() == '1') {
				submitMobile(obj);
			}
		}
	}
	jQuery("body").animate({scrollTop: jQuery("#anchor").offset().top - 100}, 'fast');
}

//返回编辑手机信息
function backToEdit()
{
	jQuery('#base_info').show();
	jQuery('#contact_info').hide();
	jQuery('.step_two').removeClass('step_two').addClass('step_one');
}

//提交手机数据
function submitMobile(obj)
{
	//手机状况
	var id = jQuery('input[name=id]').val();
	var brand = jQuery('select[name=brand]').val();
	if (jQuery('.brand-' + brand).length > 0) {
		var model = jQuery('.brand-' + brand).val();
	} else {
		var model = '';
	}
	var thumb = trim(jQuery('input[name=thumb]').val());
	var gallery = trim(jQuery('input[name=gallery]').val());
	var newly = jQuery('input[name=newly]:checked').val();
	var screen = '';
	var pbody = '';
	var trouble = '';
	var repair = '';
	var warranty = '';
	var attachment = new Array();
	var isnew = jQuery('input[name=isnew]:checked').val();
	switch (isnew) {
		case '1':
			warranty = jQuery('.new_sale_box input[name=warranty]').val();
			jQuery('.new_sale_box input[name=attachment]:checked').each(function(){
				attachment.push(jQuery(this).val());
			});
			break;
		case '0':
			screen = jQuery('input[name=screen]').val();
			pbody = jQuery('input[name=pbody]').val();
			trouble = trim(jQuery('textarea[name=trouble]').val());
			repair = jQuery('input[name=repair]').val();
			warranty = jQuery('.used_sale_box input[name=warranty]').val();
			jQuery('.used_sale_box input[name=attachment]:checked').each(function(){
				attachment.push(jQuery(this).val());
			});
			break;
	}
	/**
	var newly = jQuery('input[name=newly]').val();
	switch (newly) {
		case '1':
			var screen = '';
			var pbody = '';
			var trouble = '';
			var repair = '';
			var warranty = jQuery('.new_sale_box input[name=warranty]').val();
			var attachment = new Array();
			jQuery('.new_sale_box input[name=attachment]:checked').each(function(){
				attachment.push(jQuery(this).val());
			});
			break;
		default:
			var screen = jQuery('input[name=screen]').val();
			var pbody = jQuery('input[name=pbody]').val();
			var trouble = trim(jQuery('textarea[name=trouble]').val());
			var repair = jQuery('input[name=repair]').val();
			var warranty = jQuery('.used_sale_box input[name=warranty]').val();
			var attachment = new Array();
			jQuery('.used_sale_box input[name=attachment]:checked').each(function(){
				attachment.push(jQuery(this).val());
			});
			break;
	}
	*/
	var cost = jQuery('input[name=cost]:checked').val();
	var price = trim(jQuery('input[name=price]').val());
	var exchange = trim(jQuery('textarea[name=exchange]').val());
	var title = trim(jQuery('input[name=title]').val());
	var remark = trim(jQuery('textarea[name=remark]').val());
	//联系信息
	var name = trim(jQuery('input[name=name]').val());
	var tel = trim(jQuery('input[name=tel]').val());
	var qq = trim(jQuery('input[name=qq]').val());
	var region = jQuery('select[name=region]').val();
	var location = trim(jQuery('input[name=location]').val());
	//提交数据
	var params = {
		id:id,
		mobile:{
			brand:brand,
			model:model,
			thumb:thumb,
			gallery:gallery,
			newly:newly,
			screen:screen,
			pbody:pbody,
			trouble:trouble,
			repair:repair,
			warranty:warranty,
			attachment:attachment,
			cost:cost,
			price:price,
			exchange:exchange,
			title:title,
			remark:remark
		},
		contact:{
			name:name,
			tel:tel,
			qq:qq,
			region:region,
			location:location		
		}
	};
	jQuery.ajax({
		type:'get',
		data:jQuery.param(params),
		dataType:'json',
		url:'/customer/mobile/publish-post',
		beforeSend:function(){
			jQuery(obj).html('正在发布...');
		},
		success:function(data){
			if (data != '0') {
				window.location.href = '/customer/mobile/success?id=' + data;
			} else {
				jQuery(obj).html('确认发布');
				jQuery('#publish_fail').show();
			}
		},
		error:function(){
			jQuery(obj).html('确认发布');
			jQuery('#publish_fail').show();
		}
	});
}

//改变手机信息状态
function changeStatus(obj, id, status)
{
	if (status == 3) {
		if (!confirm('是否删除该信息')) {
			return;
		}
	}
	jQuery.ajax({
		type:'get',
		data:{id:id,status:status},
		dataType:'json',
		url:'/customer/mobile/status',
		success:function(data){
			if(data == '1'){
				switch(status){
					case 1:
						alert('手机信息已被激活');
						jQuery(obj).parent().parent().remove();
						break;
					case 2:
						alert('手机信息已被置为过期');
						jQuery(obj).parent().parent().remove();
						break;
					case 3:
						alert('手机信息已被删除');
						jQuery(obj).parent().parent().remove();
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

//刷新手机信息
function refresh(obj, id)
{
	jQuery.ajax({
		type:'get',
		data:{id:id},
		dataType:'json',
		url:'/customer/mobile/refresh',
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
