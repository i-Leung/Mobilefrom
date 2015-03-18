<?php
class Logic_System_Mlib
{
	private $_num = array(
			  '01', '02', '03', '04', '05', '06', '07', '08', '09',
		'10', '11', '12', '13', '14', '15', '16', '17', '18', '19',
		'20', '21', '22', '23', '24', '25', '26', '27', '28', '29',
		'30', '31', '32', '33', '34', '35', '36', '37', '38', '39',
		'40', '41', '42', '43', '44', '45', '46', '47', '48', '49',
		'50', '51', '52', '53', '54', '55', '56', '57', '58', '59',
		'60', '61', '62', '63', '64', '65', '66', '67', '68', '69',
		'70', '71', '72', '73', '74', '75', '76', '77', '78', '79',
		'80', '81', '82', '83', '84', '85', '86', '87', '88', '89',
		'90', '91', '92', '93', '94', '95', '96', '97', '98', '99',
		'9A', '9B', '9C', '9D', '9E', '9F', '9G', '9H', '9I', '9J',
		'9K', '9L', '9M', '9N', '9O', '9P', '9Q', '9R', '9S', '9T',
		'9U', '9V', '9W', '9X', '9Y', '9Z', 'AA', 'AB', 'AC', 'AD',
		'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN',
		'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX',
		'AY', 'AZ'
	);
	/*********************************************属性操作*********************************************/
	/**
	 * 添加属性
	 * @param 属性信息 $params
	 */
	public function addAttribute($params)
	{
		if ($params) {
			$resource = Factory::getResource('system/mlib/attribute');
			$resource->create($params);
			return $resource->getNewId();
		}
		return false;
	}

	/**
	 * 修改属性
	 * @param 属性ID $id
	 * @param 属性信息 $params
	 */
	public function modifyAttribute($id, $params)
	{
		if ($id && $params) {
			return Factory::getResource('system/mlib/attribute')->modify(
				array('id = ?' => $id), $params
			);
		}
		return false;
	}

	/**
	 * 改变属性状态
	 * @param 属性ID $id
	 * @param 属性状态 $status
	 */
	public function changeAttributeStatus($id, $status)
	{
		return Factory::getResource('system/mlib/attribute')->modify(
			array('id = ?' => $id), 
			array('status' => $status)
		);
	}

	/*********************************************属性值操作*********************************************/
	/**
	 * 添加属性值
	 * @param 属性信息 $params
	 */
	public function addAttributeValue($params)
	{
		if ($params) {
			$resource = Factory::getResource('system/mlib/attribute/value');
			return $resource->create($params, 'multiple');
		}
		return false;
	}

	/**
	 * 修改属性值
	 * @param 属性值ID $id
	 * @param 属性值信息 $data
	 */
	public function modifyAttributeValue($id, $data)
	{
		if ($id && $data) {
			return Factory::getResource('system/mlib/attribute/value')->modify(
				array('id = ?' => $id), $data
			);
		}
		return false;
	}

	/**
	 * 删除属性值
	 * @param 属性值ID $id
	 */
	public function removeAttributeValue($id)
	{
		if ($id) {
			return Factory::getResource('system/mlib/attribute/value')->remove(
				array('id = ?' => $id)
			);
		}
		return false;
	}

	/**
	 * 创建属性值索引
	 * @param 索引数据 $index 
	 */
	public function createModelAttributeIndex($index)
	{
		if ($index) {
			return Factory::getResource('system/mlib/attribute/index')->create($index, 'multiple');
		}
		return false;
	}

	/**
	 * 删除属性值索引
	 * @param 手机型号ID $id
	 */
	public function removeModelAttributeIndex($id)
	{
		if ($id) {
			return Factory::getResource('system/mlib/attribute/index')->remove(array('model_id = ?' => $id));
		}
		return false;
	}

	/*********************************************属性组操作*********************************************/
	/**
	 * 添加属性组
	 * @param 属性组信息 $group
	public function addAttributeGroup($group)
	{
		if ($group) {
			$resource = Factory::getResource('system/mlib/attribute/group');
			if ($resource->create($group)) {
				return $resource->getNewId();
			}
		}
		return false;
	}
	 */

	/**
	 * 修改属性组
	 * @param 属性组ID $id
	 * @param 属性组信息 $group
	public function modifyAttributeGroup($id, $group)
	{
		if ($id && $group) {
			return Factory::getResource('system/mlib/attribute/group')->modify(
					array('id = ?' => $id), $group
				);
		}
		return false;
	}
	 */

	/**
	 * 获取子属性修改前后差集
	 * @param 修改后子属性列表 $after
	 * @param 修改前子属性列表 $before
	public function getDiffGroupAttribute($before, $after)
	{
		$diff = array(
			'add' => array(), 
			'delete' => array()
		);
		foreach ($before as $key => &$item) {
			$item = $key;
		}
		$diff['add'] = array_diff($after, $before);
		$diff['delete'] = array_diff($before, $after);
		return $diff;
	}
	 */

	/**
	 * 添加属性组下子属性
	 * @param 属性组ID $group_id
	 * @param 子属性集合 $attributes
	public function addGroupAttribute($group_id, $attributes)
	{
		if ($group_id && $attributes) {
			$data = array();
			foreach ($attributes as $attribute) {
				$data['group_id'][] = $group_id;
				$data['attribute_id'][] = $attribute;
			}
			if ($data) {
				return Factory::getResource('relation/mlib/groupattr')->create($data, 'multiple');
			}
		}
		return true;
	}
	 */

	/**
	 * 删除属性组下子属性
	 * @param 属性组ID $group_id
	 * @param 子属性集合 $attributes
	public function removeGroupAttribute($group_id, $attributes)
	{
		if ($group_id && $attributes) {
			$attributes = implode(' , ', $attributes);
			return Factory::getResource('relation/mlib/groupattr')->remove(
				array(
					'group_id = ?' => $group_id, 
					'attribute_id in (?)' => $attributes
				)
			);
		}
		return true;
	}
	 */

	/**
	 * 改变属性组状态
	 * @param 属性组id $id
	 * @param 状态 $status
	public function changeAttributeGroupStatus($id, $status)
	{
		if ($id) {
			return Factory::getResource('system/mlib/attribute/group')->modify(
				array('id = ?' => $id), 
				array('status' => $status)
			);
		}	
		return false;
	}
	 */

	/*********************************************手机操作*********************************************/
	/**
	 * 添加手机品牌
	 * @param 手机品牌参数 $params
	 */
	public function createBrand($params)
	{
		$brandResource = Factory::getResource('system/mlib/brand');
		$last = $brandResource->getLastId();
		$last = array_search($last, $this->_num);
		$id = $last === false ? $this->_num[0] : $this->_num[$last + 1];
		$params['id'] = $id;
		return $brandResource->create($params);
	}

	/**
	 * 添加手机品牌
	 * @param 品牌ID $id
	 * @param 手机品牌参数 $params
	 */
	public function modifyBrand($id, $params)
	{
		return Factory::getResource('system/mlib/brand')->modify(
			array('id = ?' => $id), $params
		);
	}

	/**
	 * 改变手机品牌状态
	 * @param 品牌ID $id
	 * @param 状态 $status
	 */
	public function statusBrand($id, $status)
	{
		return Factory::getResource('system/mlib/brand')->modify(
			array('id = ?' => $id),
			array('status' => $status)
		);
	}

	/**
	 * 添加手机型号
	 * @param 手机型号参数 $params
	 */
	public function createModel($params)
	{
		$modelResource = Factory::getResource('system/mlib/model');
		$last = $modelResource->getLastId($params['bid']);
		$last = substr($last, 2);
		$last = array_search($last, $this->_num);
		$id = $last === false ? $this->_num[0] : $this->_num[$last + 1];
		$params['id'] = $params['bid'] . $id;
		return $modelResource->create($params);
	}

	/**
	 * 添加手机型号
	 * @param 型号ID $id
	 * @param 手机型号参数 $params
	 */
	public function modifyModel($id, $params)
	{
		return Factory::getResource('system/mlib/model')->modify(
			array('id = ?' => $id), $params
		);
	}

	/**
	 * 改变手机型号状态
	 * @param 型号ID $id
	 * @param 状态 $status
	 */
	public function statusModel($id, $status)
	{
		return Factory::getResource('system/mlib/model')->modify(
			array('id = ?' => $id),
			array('status' => $status)
		);
	}
}
