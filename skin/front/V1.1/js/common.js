jQuery(document).ready(function(){
	jQuery('#ls_password').keydown(function(e){
		if(e.keyCode==13){
			jQuery('.login_r_top button').click();
		}
	});
	//保持脚部总在页面最底部
	jQuery('.main').css('min-height', jQuery(window).height() - 138 - 105 - 20);
	//顶部是否显示隐藏下拉列表
	jQuery('.ismore').mouseover(function(){
		jQuery(this).parent().addClass('pub_more_on');
	});
	jQuery('.top_user_c').mouseleave(function(){
		jQuery(this).removeClass('pub_more_on');
	});
	jQuery('.top_user_news').mouseleave(function(){
		jQuery(this).removeClass('pub_more_on');
	});
});

//去除字符串手尾空格
function trim(str)
{ 
	return str.replace(/(^\s*)|(\s*$)/g, "");
}

//判断是否为整数
function isInt(str)
{
	var regu = /^[0-9]{1,}$/;
	return regu.test(str);
}

//关闭警告窗
function closeAlter(obj)
{
	jQuery(obj).parent().parent().hide();
}

//图片加载失败时提供默认图片
function loadErrorImg(obj)
{
	jQuery(obj).attr('src', base + '/skin/front/V1.1/img/mf.png');
}

//头像加载失败时提供默认图片
function loadErrorThumbnail(obj)
{
	jQuery(obj).attr('src', base + '/skin/front/V1.1/img/customer/user.png');
}

//头像加载失败时提供默认图片
function loadErrorStoreThumbnail(obj)
{
	jQuery(obj).attr('src', base + '/skin/front/V1.1/img/customer/store.png');
}

//发表首页建议
function makeSuggestion(obj)
{
	var qq = trim(jQuery(obj).parent().parent().find('input[name=qq]').val());
	if(qq == ''){
		alert('请填写联系QQ，我们会专人联系您了解情况！');
		return;
	}
	var content = trim(jQuery(obj).parent().parent().find('textarea').val());
	if(content == ''){
		alert('请填写建议内容');
		return;
	}
	if(content.length >= 240){
		alert('建议的内容太多，请精简一下内容，谢谢！');
		return;
	}
	jQuery.ajax({
		type:'get',
		data:{qq:qq,content:content},
		dataType:'json',
		url:'/default/website/suggest',
		success:function(data){
			if(data == '1'){
				alert('您的宝贵建议我们已收到，我们会认真考虑您的提议，尽快做出调整，谢谢您对本网站的支持！');
				jQuery(obj).parent().parent().find('textarea').val('');
				jQuery('.feedback_box').hide();
			}else{
				alert('您的建议提交时发生错误，请稍候尝试');
			}
		},
		error:function(){
			alert('您的建议提交时发生错误，请稍候尝试');
		}
	});
}

//发表页面交易
function makePageSuggestion(obj)
{
	var content = trim(jQuery(obj).parent().parent().find('textarea').val());
	if(content == ''){
		alert('请填写建议内容');
		return;
	}
	if(content.length >= 240){
		alert('建议的内容太多，请精简一下内容，谢谢！');
		return;
	}
	var path = window.document.location.href;
	content = '页面(' + path + ')' + '不满意:' + content;
	jQuery.ajax({
		type:'get',
		data:{content:content},
		dataType:'json',
		url:'/default/website/suggest',
		success:function(data){
			if(data == '1'){
				alert('您的宝贵建议我们已收到，我们会认真考虑您的提议，尽快做出调整，谢谢您对本网站的支持！');
				jQuery(obj).parent().parent().find('textarea').val('');
			}else{
				alert('您的建议提交时发生错误，请稍候尝试');
			}
		},
		error:function(){
			alert('您的建议提交时发生错误，请稍候尝试');
		}
	});
}

//收藏本站
function addFavorite()
{
	var url = 'http://www.mobilefrom.com';
	var title = '机荒网:顺德二手手机交易平台';
	try{
		window.external.addFavorite(url, title);
	}catch(e){
		try{
			window.sidebar.addPanel(title, url, "");
		}catch(e){
			alert("请使用Ctrl+D进行添加收藏！");
		}
	}
}

/***********************************************************弹出注册、登陆、找回密码*************************************/
jQuery(document).ready(function(){
	jQuery('.pop_window').css('width', window.screen.width)
						.css('height', window.screen.height);
	jQuery('.pop_login input[name=password]').keydown(function(e){
		if(e.keyCode==13){
			jQuery('.pop_login .pop_log_btn').click();
		}
	});
	jQuery('.pop_retrieve input[name=email]').keydown(function(e){
		if(e.keyCode==13){
			jQuery('.pop_retrieve .pop_log_btn').click();
		}
	});
});
//显示弹窗登录框
function popOpen(target)
{
	jQuery('.pop_window').show();
	jQuery('.pop_wp').hide();
	switch(target){
		case 'register':
			jQuery('.pop_register').css('margin-top', '-267px').css('margin-left', '-300px');
			jQuery('.pop_register').show();
			jQuery('.pop_register input[name=username]').focus();
			break;
		case 'login':
			jQuery('.pop_login').css('margin-top', '-174px').css('margin-left', '-250px');
			jQuery('.pop_login').show();
			jQuery('.pop_login input[name=customer]').focus();
			break;
		case 'retrieve':
			jQuery('.pop_retrieve').css('margin-top', '-174px').css('margin-left', '-250px');
			jQuery('.pop_retrieve').show();
			jQuery('.pop_retrieve input[name=username]').focus();
			break;
	}
}

//隐藏弹窗登录框
function popClose()
{
	jQuery('.pop_window').hide();
	jQuery('.pop_wp').hide();
}

//弹窗ajax提交登录
function ajaxPopLogin(obj)
{
	var msg_box = jQuery('.pop_login .tis_area p');
	var customer = jQuery('.pop_login input[name=customer]').val();
	var pwd = jQuery('.pop_login input[name=password]').val();
	var remember = jQuery('.pop_login input[name=auto_login]:checked').val();
	var msg = '';
	if(customer == ''){
		msg += '请填写用户名/注册邮箱;';
	}
	if(pwd == ''){
		msg += '请填写登录密码;';
	}
	if(msg != ''){
		msg_box.html('提示：' + msg);
		return;
	}
	jQuery.ajax({
		type:'get',
		data:{customer:customer, pwd:pwd, remember:remember},
		dataType:'json',
		url:'/customer/account/login-post',
		beforeSend:function(){
			jQuery(obj).html('正在登录...');
		},
		success:function(data){
			if(data == '1'){
				window.location.reload();
			} else {
				jQuery(obj).html('立即登录');
				msg_box.html('提示：登录失败，请核对您的用户名/邮箱/密码');
			}
		},
		error:function(){
			jQuery(obj).html('立即登录');
			msg_box.html('提示：登录失败，请核对您的用户名/邮箱/密码');
		}
	});
}

//弹窗ajax提交找回密码申请
function ajaxPopRetrieve(obj)
{
	var msg_box = jQuery('.pop_retrieve .tis_area p');
	var username = jQuery('.pop_retrieve input[name=username]').val();
	var email = jQuery('.pop_retrieve input[name=email]').val();
	var msg = '';
	if(username == ''){
		msg += '请填写用户名;';
	}
	if(email == ''){
		msg += '请填写电子邮箱;';
	}
	if(msg != ''){
		msg_box.html('提示：' + msg);
		return;
	}
	jQuery.ajax({
		type:'get',
		data:{username:username, email:email},
		dataType:'json',
		url:'/customer/account/forget-pwd',
		beforeSend:function(){
			jQuery(obj).html('正在请求...');
		},
		success:function(data){
			if(data == '1'){
				jQuery('.pop_retrieve input[name=username]').val('');
				jQuery('.pop_retrieve input[name=email]').val('');
				jQuery(obj).html('找回密码');
				alert('您的登陆密码已被重新设置，并发送到您的注册邮箱，请及时查收！');
				popOpen('login');
			} else {
				jQuery(obj).html('找回密码');
				msg_box.html('提示：核对失败，请确认您的用户名或邮箱是否输入正确');
			}
		},
		error:function(){
			jQuery(obj).html('找回密码');
			msg_box.html('提示：请求失败，请稍候尝试');
		}
	});
}
