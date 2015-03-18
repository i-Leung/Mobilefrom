/**
 * 注册页面所用js
 * @author 斌
 */
jQuery(document).ready(function(){
	jQuery('#register input').focus(function(){
		var box = jQuery(this).parent().parent().find('.msg_box');
		box.show();
		box.removeClass('msg_tips')
			.removeClass('msg_empty')
			.removeClass('msg_used')
			.removeClass('msg_error')
			.removeClass('msg_right');
		box.addClass('msg_tips');
		box.find('.msg_cnt span').hide();
		box.find('.msg_cnt_tips').show();
	});
	jQuery('#register input:eq(0)').focus();
	jQuery('#register input:eq(5)').keydown(function(e){
		if (e.keyCode == 13) {
			ajaxRegister(jQuery(this).parent().parent().parent().find('.btn_orange'));
		}
	});
});
//检测用户名合法性
function checkUsername()
{
	var username = trim(jQuery('#register input[name=username]').val());
	var box = jQuery('.msg_box:eq(0)');
	if(username == ''){
		showMsg(box, 'empty');
		return false;
	} else if(username.length < 2 || username.length > 16) {
		showMsg(box, 'error');
		return false;
	} else if(checkExistCustomer(username)) {
		showMsg(box, 'used');
		return false;
	} else {
		showMsg(box, 'right');
		return true;
	}
}
//检测邮箱合法性
function checkEmail()
{
	var email = trim(jQuery('#register input[name=email]').val());
	var box = jQuery('.msg_box:eq(1)');
	box.show();
	var pattern = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
	if(email == ''){
		showMsg(box, 'empty');
		return false;
	} else if(!pattern.test(email)) {
		showMsg(box, 'error');
		return false;
	} else if(checkExistCustomer(email)) {
		showMsg(box, 'used');
		return false;
	} else {
		showMsg(box, 'right');
		return true;
	}
}
//检测密码
function checkPwd()
{
	var password = trim(jQuery('#register input[name=pwd]').val());
	var box = jQuery('.msg_box:eq(3)');
	box.show();
	if(password == ''){
		showMsg(box, 'empty');
		return false;
	} else if(password.length < 6 || password.length > 16) {
		showMsg(box, 'error');
		return false;
	} else {
		showMsg(box, 'right');
		return true;
	}
}
//检测确认密码
function checkRePwd()
{
	var password = trim(jQuery('#register input[name=pwd]').val());
	var repassword = trim(jQuery('#register input[name=repwd]').val());
	var box = jQuery('.msg_box:eq(4)');
	box.show();
	if(repassword == ''){
		showMsg(box, 'empty');
		return false;
	} else if(password != repassword) {
		showMsg(box, 'error');
		return false;
	} else {
		showMsg(box, 'right');
		return true;
	}
}

/**
 * 检测用户存在性
 * @param 用户名或邮箱 customer
 * @return boolean
 */
function checkExistCustomer(customer)
{
	var result = false;
	jQuery.ajax({
		type:'get',
		data:{customer:customer},
		dataType:'json',
		url:'/customer/account/exist',
		async:false,
		success:function(data){
			if(data == '1'){
				result = true;
			}
		},
		error:function(){
			alert('请求连接失败，请刷新页面');
		}
	});
	return result;
}

/**
 * 显示提示信息
 * @param 信息显示对象 obj
 * @param 信息类型 type
 */
function showMsg(obj, type)
{
	obj.removeClass('msg_tips')
		.removeClass('msg_empty')
		.removeClass('msg_used')
		.removeClass('msg_error')
		.removeClass('msg_right');
	obj.addClass('msg_' + type);
	obj.find('.msg_cnt span').hide();
	obj.find('.msg_cnt_' + type).show();
}

/**
 * ajax提交注册数据
 */
function ajaxRegister(obj)
{
	if(checkUsername() && checkEmail() && checkPwd() && checkRePwd()){
		var data = {
			customer:{
				username:jQuery('#register input[name=username]').val(),
				email:jQuery('#register input[name=email]').val(),
				pwd:jQuery('#register input[name=pwd]').val(),
				repwd:jQuery('#register input[name=repwd]').val()
			},
			remember:jQuery('#register input[name=remember]:checked').val()
		};
		jQuery.ajax({
			type:'get',
			data:jQuery.param(data),
			dataType:'json',
			url:'/customer/account/register-post',
			beforeSend:function(){
				jQuery(obj).html('数据正在提交...');
			},
			success:function(data){
				if(data == '0') {
					jQuery(obj).html('同意协议并注册');
					alert('注册失败，请检测您所输入的数据是否合符要求');
				} else {
					window.location.href = '/?cfrom=register';
				}
			},
			error:function(data){
				jQuery(obj).html('同意协议并注册');
				alert('注册失败，请检测您所输入的数据是否合符要求');
			}
		});
	}
}

/**
 * 是否记录使用痕迹
 */
function rememberMe(obj)
{
	var checkbox = jQuery(obj).parent().find('input[type=checkbox]');
	if(checkbox.attr('checked') == 'checked'){
		checkbox.attr('checked', false);
	} else {
		checkbox.attr('checked', true);
	}
}
