/*全选/取消全选ks*/
jQuery(document).ready(
	function(){
		$('#chk_all').click(
			function(){
				var chk_all_value=$(this).attr("checked");
				if(chk_all_value){
					$("input[name='chk_list']").attr("checked",true);
				}else{
					$("input[name='chk_list']").attr("checked",false);
				}
			}
		);
	}
);
/*全选/取消全选end*/

//用户重命名
function rename(obj)
{
	var input = jQuery(obj).parent().parent().parent().find('input[name=info-username]');
	var username = trim(input.val());
	if(username == ''){
		alert('请填写你想修改的用户名');
		return;
	}
	if(username == input.attr('placeholder')){
		alert('用户信息已修改成功');
		return;
	}
	jQuery.ajax({
		type:'get',
		data:{newname:username},
		dataType:'json',
		url:'/customer/account/rename',
		beforeSend:function(){
			jQuery(obj).html('数据正在提交...');
		},
		success:function(data){
			jQuery(obj).html('保存基本信息');
			if(data == '1') {
				alert('用户信息已修改成功');
			} else {
				alert('您说输入的用户名已被使用，请尝试其它用户名');
			}
		},
		error:function(){
			jQuery(obj).html('保存基本信息');
			alert('修改用户名失败，请稍候尝试');
		}
	});
}

//修改联系方式
function editContact(obj)
{
	var parent = jQuery(obj).parent().parent().parent();
	var name = trim(parent.find('input[name=contact-name]').val());
	var qq = trim(parent.find('input[name=contact-qq]').val());
	var tel = trim(parent.find('input[name=contact-tel]').val());
	var region = parent.find('select[name=contact-region]').val();
	var location = trim(parent.find('input[name=contact-location]').val());
	if(name == '' || qq == '' || tel == '' ||  region == '0' || location == ''){
		alert('请填写完整联系信息');
		return;
	}
	var data = {
		'contact':{
			'name':name,
			'qq':qq,
			'tel':tel,
			'region':region,
			'location':location
		}
	};
	jQuery.ajax({
		type:'get',
		data:jQuery.param(data),
		dataType:'json',
		url:'/customer/account/edit-contact',
		beforeSend:function(){
			jQuery(obj).html('数据正在提交...');
		},
		success:function(data){
			jQuery(obj).html('保存联系信息');
			if(data == '1') {
				alert('联系信息已修改成功');
			} else {
				alert('联系信息修改失败，请稍候尝试');
			}
		},
		error:function(data){
			jQuery(obj).html('保存联系信息');
			alert('联系信息修改失败，请稍候尝试');
		}
	});
}

//检测密码正确性
function checkPwd(obj)
{
	var pwd = trim(jQuery(obj).val());
	var msg = jQuery(obj).parent().find('.error_tis');
	if(pwd == ''){
		return;
	}
	jQuery.ajax({
		type:'get',
		data:{pwd:pwd},
		dataType:'json',
		url:'/customer/account/check-pwd',
		beforeSend:function(){
			msg.html('正在检测中...');
		},
		success:function(data){
			if(data == '1'){
				msg.html('密码正确');
			} else {
				msg.html('您所输入的密码有误，请重新确认');
				jQuery(obj).val('').focus();
			}
		},
		error:function(data){
			msg.html('密码检测失败，请稍候尝试');
			jQuery(obj).focus();
		}
	});
}

//重设密码
function newPwd(obj)
{
	var parent = jQuery(obj).parent().parent().parent();
	var pwd = trim(parent.find('input[name=pwd]').val());
	var newpwd = trim(parent.find('input[name=newpwd]').val());
	var repwd = trim(parent.find('input[name=repwd]').val());
	if(pwd == ''){
		parent.find('input[name=pwd]').focus();
		parent.find('input[name=pwd]').parent().find('.error_tis').html('请填写当前密码');
		return;
	}
	if(newpwd.length < 6){
		parent.find('input[name=newpwd]').focus();
		parent.find('input[name=newpwd]').parent().find('.error_tis').html('密码长度不能小于6位');
		return;
	}
	if(newpwd == pwd){
		parent.find('input[name=newpwd]').focus();
		parent.find('input[name=newpwd]').parent().find('.error_tis').html('新密码不能与旧密码相同');
		return;
	}
	if(newpwd != repwd){
		parent.find('input[name=repwd]').focus();
		parent.find('input[name=repwd]').parent().find('.error_tis').html('两次密码不一样');
		return;
	}
	jQuery.ajax({
		type:'get',
		data:{pwd:pwd,newpwd:newpwd,repwd:repwd},
		dataType:'json',
		url:'/customer/account/repwd',
		beforeSend:function(){
			jQuery(obj).html('数据正在提交...');
		},
		success:function(data){
			jQuery(obj).html('保存修改');
			if(data == '1'){
				alert('密码修改成功');
				parent.find('input[name=pwd]').val('');
				parent.find('input[name=newpwd]').val('');
				parent.find('input[name=repwd]').val('');
			} else {
				alert('密码修改失败，请重新尝试');
			}
		},
		error:function(){
			jQuery(obj).html('保存修改');
			alert('密码修改失败，请重新尝试');
		}
	});
}

//取消关注
function removeFavor(obj, type, id)
{
	jQuery.ajax({
		type:'get',
		data:{type:type,id:id},
		dataType:'json',
		url:'/customer/favor/remove',
		success:function(data){
			if(data == '1') {
				jQuery(obj).parent().parent().remove();
				jQuery('.jh_toolbar_r .yellow:eq(1)').html(
					parseInt(jQuery('.jh_toolbar_r .yellow:eq(1)').html()) - 1
				);
			}else{
				alert('删除关注失败，请稍候再尝试');
			}
		},
		error:function(){
			alert('删除关注失败，请稍候再尝试');
		}
	});
}

//获取通知信息内容
function getNoticeContent(obj, id)
{
	jQuery('.notice_box').show();
	if(jQuery(obj).parent().find('span').length > 0){
		jQuery('.notice_detail_box').show();
		jQuery('.notice_title').html(
			jQuery(obj).parent().parent().find('td:eq(0)').html()
		);
		jQuery('.notice_detail').html(
			jQuery(obj).parent().find('span').html()
		);
	}else{
		jQuery.ajax({
			type:'get',
			data:{id:id},
			dataType:'json',
			url:'/customer/message/get-notice-content',
			success:function(data){
				if(data != '0'){
					jQuery(obj).parent().append(
						'<span style="display:none;">' + data + '</span>'
					);
					jQuery('.notice_detail_box').show();
					jQuery('.notice_title').html(
						jQuery(obj).parent().parent().find('td:eq(0)').html()
					);
					jQuery('.notice_detail').html(data);
				}else{
					alert('获取通知内容失败，请稍候尝试');
				}
			},
			error:function(){
				alert('获取通知内容失败，请稍候尝试');
			}
		});
	}
}
