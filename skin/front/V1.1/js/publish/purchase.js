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

//获取指定求购信息内容
function getPurchase(id)
{
	jQuery.ajax({
		type:'get',
		data:{id:id},
		dataType:'json',
		url:'/customer/purchase/get',
		async:false,
		success:function(data){
			if(data != '0'){
				jQuery('input[name=title]').val(data.title);
				jQuery('select[name=brand] option[value=' + data.brand + ']').attr('selected', 'selected');
				jQuery('input[name=amount]').val(data.amount);
				jQuery('input[name=min-price]').val(data.min);
				jQuery('input[name=max-price]').val(data.max);
				jQuery('textarea[name=remark]').val(data.remark);
				jQuery('input[name=name]').val(data.contact.name);
				jQuery('input[name=tel]').val(data.contact.tel);
				jQuery('input[name=qq]').val(data.contact.qq);
				jQuery('select[name=region] option[value=' + data.contact.region + ']').attr('selected', 'selected');
				jQuery('input[name=location]').val(data.contact.location);
			}else{
				alert('载入求购信息失败，请稍候尝试');
			}
		},
		error:function(){
			alert('载入求购信息失败，请稍候尝试');
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
	if (type == 'right'){
		if (jQuery('.tis_' + box).find('#anchor').length > 0) {
			jQuery('.tis_' + box).find('#anchor').remove();
		}
	}
	jQuery('.tis_' + box).show();
	jQuery('.tis_' + box + ' span').hide();
	jQuery('.tis_' + box + ' .' + type).css('display', 'block');
}

/**
 * 检测标题是否为空
 */
function checkTitle()
{
	var title = trim(jQuery('input[name=title]').val());
	if (title == '') {
		showMsg('pur_title', 'empty');
	} else {
		if (title.length < 6 || title.length > 50) {
			showMsg('pur_title', 'error');
		} else {
			showMsg('pur_title', 'right');
		}
	}
}

/**
 * 检测品牌
 */
function checkBrand()
{
	showMsg('pur_price', 'focus');
	checkTitle();
	var brand = jQuery('select[name=brand]').val();
	if (brand == '') {
		showMsg('pur_brand', 'empty');
	} else {
		showMsg('pur_brand', 'right');
	}
}

/**
 * 检测价格
 */
function checkPrice()
{
	showMsg('pur_num', 'focus');
	checkBrand();
	var min = trim(jQuery('input[name=min-price]').val());
	var max = trim(jQuery('input[name=max-price]').val());
	if (min == '' || max == '') {
		showMsg('pur_price', 'empty');
	} else {
		if (isInt(min) && isInt(max) && parseInt(min) < parseInt(max)) {
			showMsg('pur_price', 'right');
		} else {
			showMsg('pur_price', 'error');
		}
	}
}

/**
 * 检测数量
 */
function checkAmount()
{
	showMsg('pur_miaoshu', 'focus');
	checkPrice();
	var amount = trim(jQuery('input[name=amount]').val());
	if (amount == '' || !isInt(amount) || amount == '0') {
		showMsg('pur_num', 'error');
	} else {
		showMsg('pur_num', 'right');
	}
}

/**
 * 检测备注
 */
function checkRemark()
{
	jQuery('input[name=complete]').val('1');
	checkAmount();
	var remark = jQuery('textarea[name=remark]').val();
	if (remark == '') {
		showMsg('pur_miaoshu', 'empty');
	} else {
		if (remark.length < 10 || remark.length > 500) {
			showMsg('pur_miaoshu', 'error');
		} else {
			showMsg('pur_miaoshu', 'right');
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

//返回编辑手机信息
function backToEdit()
{
	jQuery('#base_info').show();
	jQuery('#contact_info').hide();
	jQuery('.step_two').removeClass('step_two').addClass('step_one');
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
				submitPurchase(obj);
			}
		}
	}
	jQuery("body").animate({scrollTop: jQuery("#anchor").offset().top - 100}, 'fast');
}

//提交求购数据
function submitPurchase(obj)
{
	//手机状况
	var id = jQuery('input[name=id]').val();
	var title = trim(jQuery('input[name=title]').val());
	var brand = jQuery('select[name=brand]').val();
	var amount = trim(jQuery('input[name=amount]').val());
	var min = trim(jQuery('input[name=min-price]').val());
	var max = trim(jQuery('input[name=max-price]').val());
	var price = min + '-' + max;
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
			purchase:{
				title:title,
				brand:brand,
				amount:amount,
				price:price,
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
		url:'/customer/purchase/publish-post',
		beforeSend:function(){
			jQuery(obj).html('正在发布...');
		},
		success:function(data){
			if (data != '0') {
				window.location.href = '/customer/purchase/success?id=' + data;
			} else {
				jQuery(obj).html('确认发布');
				alert('发布求购失败，请稍候尝试');
			}
		},
		error:function(){
			jQuery(obj).html('确认发布');
			alert('发布求购失败，请稍候尝试');
		}
	});
}

//改变求购信息状态
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
		url:'/customer/purchase/status',
		success:function(data){
			if(data == '1'){
				switch(status){
					case 1:
						alert('求购信息已被激活');
						jQuery(obj).parent().parent().remove();
						break;
					case 2:
						alert('求购信息已被置为过期');
						jQuery(obj).parent().parent().remove();
						break;
					case 3:
						alert('求购信息已被删除');
						jQuery(obj).parent().parent().remove();
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
