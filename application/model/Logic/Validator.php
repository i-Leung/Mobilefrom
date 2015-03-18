<?php
class Logic_Validator
{
	/**
	 * 检测用户名
	 * @param 用户名 $username
	 * @return boolean
	 */
	public function checkUsername($username)
	{
		if (
			$username
			 && 
			$this->checkLength($username, 2, 25)
			 && 
			!Factory::getLogic('customer')->isExist($username)
		) {
			return true;
		}
		Factory::setMsg('用户名验证失败');
		return false;
	}
	
	/**
	 * 检测邮箱
	 * @param 邮箱 $email
	 * @return boolean
	 */
	public function checkEmail($email)
	{
		if (
			$email
			 && 
			$this->isEmail($email)
			 && 
			!Factory::getLogic('customer')->isExist($email)
		) {
			return true;
		}
		Factory::setMsg('邮箱验证失败');
		return false;
	}
	
	/**
	 * 检测用户性质
	 * @param 用户性质 $group
	 * @return boolean
	 */
	public function checkGroup($group)
	{
		if ($group) {
			return true;
		}
		Factory::setMsg('用户性质验证失败');
		return false;
	}
	
	/**
	 * 检测密码
	 * @param 密码 $pwd
	 * @param 确认密码 $repwd
	 * @return boolean
	 */
	public function checkPwd($pwd, $repwd)
	{
		if ($pwd && $repwd && $this->checkLength($pwd, 6, 16) && $pwd == $repwd) {
			return true;
		}
		Factory::setMsg('密码验证失败');
		return false;
	}
	
	/**
	 * 检测数据长度是否符合要求
	 * @param 待检测数据 $str
	 * @param 最小长度 $min
	 * @param 最大长度 $max
	 * @return boolean
	 */
	public function checkLength($str, $min, $max)
	{
		$length = strlen($str);
		return ($length >= $min && $length <= $max);
	}
	
	/**
	 * 检测是否合法邮箱地址
	 * @param 邮箱地址 $email
	 * @return boolean
	 */
	public function isEmail($email)
	{
		$pattern = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,5}\$/i";
		if (strpos($email, '@') !== false && strpos($email, '.') !== false) {
			if (preg_match($pattern, $email)) {
				return true;
			}
		}
		return false;
	}
}
