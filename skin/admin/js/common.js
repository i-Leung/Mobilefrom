/**
 * 后台公用js
 * author 斌
 */

jQuery(document).ready(function(){
	jQuery('.table1 tbody tr').mouseover(function(){
		jQuery(this).css('background-color', '#e9f1fa');
	});
	jQuery('.table1 tbody tr').mouseout(function(){
		jQuery(this).css('background-color', '');
	});
	jQuery('.table1 tbody tr td').click(function(){
		if(jQuery(this).find('a').length == 0){
			var entity = jQuery('.table1').attr('entity');
			var url = '';
			var fresh = 0;
			switch(entity){
				case 'mobile':
					url = '/mobile/item?id=';
					break;
				case 'purchase':
					url = '/purchase/item?id=';
					break;
				case 'customer':
					url = '/monitor/customer_common/base?id=';
					break;
				case 'personal':
					url = '/monitor/customer_personal/mobile?id=';
					break;
				case 'resource-main-group':
					fresh = 1;
					url = '/monitor/system_resource/group-list?pid=';
					break;
				case 'resource-group':
					fresh = 1;
					url = '/monitor/system_resource/list?gid=';
					break;
				case 'resource':
					fresh = 1;
					url = '/monitor/system_resource/operation?id=';
					break;
				case 'member-group':
					fresh = 1;
					url = '/monitor/system_member/group-info?id=';
					break;
				case 'member':
					fresh = 1;
					url = '/monitor/system_member/info?id=';
					break;
				case 'operation':
					return;
				case 'mlib-item':
					return;
				case 'mlib-brand':
					fresh = 1;
					url = '/monitor/system_mlib/model-list?bid=';
					break;
				case 'mlib-model':
					fresh = 1;
					url = '/monitor/system_mlib/model?id=';
					break;
				case 'mlib-attribute':
					if (jQuery(this).parent().attr('type') != 'text') {
						url = '/monitor/system_mlib/attribute?id=';
						break;
					} else {
						return;
					}
				case 'mlib-attribute-value':
					return;
				case 'tlib-item':
					return;
				case 'tlib-brand':
					fresh = 1;
					url = '/monitor/system_tlib/model-list?bid=';
					break;
				case 'tlib-model':
					fresh = 1;
					url = '/monitor/system_tlib/model?id=';
					break;
				case 'tlib-attribute':
					if (jQuery(this).parent().attr('type') != 'text') {
						url = '/monitor/system_tlib/attribute?id=';
						break;
					} else {
						return;
					}
				case 'tlib-attribute-value':
					return;
				case 'pfrom-site':
					return;
				case 'pfrom-module':
					return;
				case 'pfrom-action':
					return;
				case 'attribute-filter':
					return;
				case 'report':
					url = '/website/report/item?id=';
					break;
				case 'service':
					return;
				default:
					break;
			}
			if(fresh){
				window.location.href = url + jQuery(this).parent().attr('entity');
			}else{
				window.open(url + jQuery(this).parent().attr('entity'));
			}
		}
	});
});

/**
 * 禁用对象
 * @param 触发对象 obj
 * @param 操作对象ID id
 * @param 操作对象类型 type
 */
function itemActive(obj, id, type)
{
	if(!id){
		alert('请选择操作对象');
		return;
	}	
	var url = null;
	var params = {
		id:id,
		status:1
	};
	switch(type){
		case 'resource-group':
			params = {
				id:id,
				pid:jQuery(obj).attr('pid'),
				status:1
			};
			url = '/monitor/system_resource/change-group-status';
			break;
		case 'resource':
			url = '/monitor/system_resource/change-item-status';
			break;
		case 'member-group':
			url = '/monitor/system_member/change-group-status';
			break;
		case 'member':
			url = '/monitor/system_member/change-member-status';
			break;
		case 'mlib-attribute':
			url = '/monitor/system_mlib/change-attribute-status';
			break;
		case 'mlib-brand':
			url = '/monitor/system_mlib/status-brand';
			break;
		case 'mlib-model':
			url = '/monitor/system_mlib/status-model';
			break;
		case 'tlib-attribute':
			url = '/monitor/system_mlib/change-attribute-status';
			break;
		case 'tlib-brand':
			url = '/monitor/system_tlib/status-brand';
			break;
		case 'tlib-model':
			url = '/monitor/system_tlib/status-model';
			break;
		case 'report':
			url = '/monitor/website_report/status';
			break;
		default:
			break;
	}
	jQuery.ajax({
		type:'get',
   		data:jQuery.param(params),
		dataType:'json',
		url:url,
		beforeSend:function(XMLHttpRequest){
			jQuery(obj).html('正在操作...')
				.attr('onclick', '');
		},	
		success:function(data){
			if(data == '1'){
				jQuery(obj).attr('onclick', 'itemInActive(this, \'' + id + '\', \'' + type + '\')')
					.html('禁用');
				jQuery(obj).parent().parent().find('.green').html('有效');
			}else{
				jQuery(obj).html('启用')
					.attr('onclick', 'itemActive(this, \'' + id + '\', \'' + type + '\')');
				alert('启用该对象失败，请稍候尝试');
			}
		},
		error:function(){
			jQuery(obj).html('启用')
				.attr('onclick', 'itemActive(this, \'' + id + '\', \'' + type + '\')');
			alert('启用该对象失败，请稍候尝试');
		}	      
	});
}

/**
 * 禁用对象
 * @param 触发对象 obj
 * @param 操作对象ID id
 * @param 操作对象类型 type
 */
function itemInActive(obj, id, type)
{
	if(!id){
		alert('请选择操作对象');
		return;
	}	
	var url = null;
	var params = {
		id:id,
		status:0
	};
	switch(type){
		case 'mobile':
			params = {
				id:id,
				status:3
			};
			url = '/monitor/info_mobile/status';
			break;
		case 'purchase':
			params = {
				id:id,
				status:3
			};
			url = '/monitor/info_purchase/status';
			break;
		case 'resource-group':
			params = {
				id:id,
				pid:jQuery(obj).attr('pid'),
				status:0
			};
			url = '/monitor/system_resource/change-group-status';
			break;
		case 'resource':
			url = '/monitor/system_resource/change-item-status';
			break;
		case 'member-group':
			url = '/monitor/system_member/change-group-status';
			break;
		case 'member':
			url = '/monitor/system_member/change-member-status';
			break;
		case 'mlib-attribute':
			url = '/monitor/system_mlib/change-attribute-status';
			break;
		case 'mlib-brand':
			url = '/monitor/system_mlib/status-brand';
			break;
		case 'mlib-model':
			url = '/monitor/system_mlib/status-model';
			break;
		case 'tlib-attribute':
			url = '/monitor/system_tlib/change-attribute-status';
			break;
		case 'tlib-brand':
			url = '/monitor/system_tlib/status-brand';
			break;
		case 'tlib-model':
			url = '/monitor/system_tlib/status-model';
			break;
		case 'report':
			url = '/monitor/website_report/status';
			break;
		default:
			break;
	}
	jQuery.ajax({
		type:'get',
   		data:jQuery.param(params),
		dataType:'json',
		url:url,
		beforeSend:function(XMLHttpRequest){
			jQuery(obj).html('正在操作...')
				.attr('onclick', '');
		},	
		success:function(data){
			if(data == '1'){
				if(type == 'mobile' || type == 'purchase'){
					jQuery(obj).parent().parent().remove();
				}else{
					jQuery(obj).attr('onclick', 'itemActive(this, \'' + id + '\', \'' + type + '\')')
						.html('启用');
					jQuery(obj).parent().parent().find('.green').html('冻结');
				}
			}else{
				jQuery(obj).html('禁用')
					.attr('onclick', 'itemInActive(this, \'' + id + '\', \'' + type + '\')');
				alert('禁用该对象失败，请稍候尝试');
			}
		},
		error:function(){
			jQuery(obj).html('禁用')
				.attr('onclick', 'itemInActive(this, \'' + id + '\', \'' + type + '\')');
			alert('禁用该对象失败，请稍候尝试');
		}	      
	});
}
