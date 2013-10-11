<?php
if(!defined('IN_SYS')) 
{
	header("HTTP/1.1 404 Not Found");
	die;
}
/**
 * 输出html代码工具
 */
class THtml {
	private static $_inputType = array('button', 'checkbox', 'file', 'hidden', 'image', 'text', 'password', 'radio', 'reset', 'submit');
	
	public static $_error;
	
	/**
	 * 生成一个input
	 *
	 * @example THtml :: TInput('test', 'hidden', 'hello world', 'testClass', 'test', array('onclick' => '$(\'tableHeader\').style.display=\'none\';'));
	 * @param String $type（button, checkbox, file, hidden, image, text, password, radio, reset, submit） 类型
	 * @param String $value	值
	 * @param Mix $checked 已选值(radio 或 checkbox使用)
	 * @param String $class	样式class名
	 * @param String $name 名字
	 * @param String $id id
	 * @param Array $extra 附加参数
	 * @return String $html 返回的html或错误
	 */
	public static function TInput($name, $type = 'text', $value = '', $checked = '', $class = '', $id = '', array $extra = array()) {
		if(!in_array($type, self :: $_inputType)) {
			self :: $_error = '00001';
			return false;
		}
		$html = '<input type="' . $type . '" name="' . $name . '"';
		if($value)	$html .= ' value="' . $value . '"';
		if($class)	$html .= ' class="' . $class . '"';
		if($id)		$html .= ' id="' . $id . '"';
		if($type == 'radio') {
			if($checked == $value) $html .= ' checked="checked"';
		} elseif($type == 'checkbox') {
			if(in_array($value, $checked)) $html .= ' checked="checked"';
		}
		if(!empty($extra)) {
			$vars = array();
			foreach ($extra as $key => $val) {
				$vars[] = $key . '="' . $val . '"';
			}
			$html .= ' ' . implode(' ', $vars);
		}
		$html .= ' />';
		return $html;
	}
	
	/**
	 * 生成一个下拉菜单
	 *
	 * @example THtml :: TSelect('test', array('Test1' => 'a', 'Test2' => 'b'), 'b', 'testClass', 'tstSel', array('onchange' => 'window.location=\'http://www.56.com\';'))
	 * @param String $name 名字
	 * @param array $values 下来值
	 * @param String $selected 选中值
	 * @param String $class 样式class名
	 * @param String $id Id
	 * @param array $extra 附加参数
	 * @return String $html 返回的html或错误
	 */
	public static function TSelect($name, array $values, $selected = '', $class = '', $id = '', array $extra = array()) {
		if(empty($values)) {
			self :: $_error = '00003';
			return false;
		}
		
		$html = '<select name="' . $name . '"';
		if($class)	$html .= ' class="' . $class . '"';
		if($id)		$html .= ' id="' . $id . '"';
		
		if(!empty($extra)) {
			$vars = array();
			foreach ($extra as $key => $val) {
				$vars[] = $key . '="' . $val . '"';
			}
			$html .= ' ' . implode(' ', $vars);
		}
		$html .= '>';
		$html .= '<option value="">' . Core :: $configs['lang']['thtml']['00002'] . '</option>';
		foreach ($values as $key => $val) {
			if($key === 'optgroup') {
				$html .= '	<optgroup lable="' . $val . '">';
			} else {
				$html .= '	<option value="' . $key . '" ' . (($selected !== '' && strcasecmp($selected, $key) == 0) ? 'selected="selected"' : '') . '>' . $val . '</option>';
			}
		}
		$html .= '</select>';
		return $html;
	}
	
	/**
	 * 生成一堆单选代码
	 *
	 * @param String $name 单选参数名
	 * @param array $values 单选参数值
	 * @param String $checked 选中的值
	 * @param String $class 样式class名
	 * @param String $id Id
	 * @param array $extra 附加参数
	 * @return String $html 返回的html或错误
	 */
	public static function TRadio($name, array $values, $checked = '', $class = '', $id = '', array $extra = array()) {
		if(empty($values)) {
			self :: $_error = '00004';
			return false;
		}
		
		$html = '';
		foreach ($values as $key => $val) {
			$html .= self :: TInput($name, 'radio', $val, $checked, $class, $id ? $id . '[' . $val . ']' : '', $extra[$val]) . $key;
		}
		return $html;
	}
	
	/**
	 * 生成一堆多选代码
	 *
	 * @param String $name 多选参数名
	 * @param array $values 多选参数值
	 * @param Mix $checked 选中的值
	 * @param String $class 样式class名
	 * @param String $id Id
	 * @param array $extra 附加参数
	 * @return String $html 返回的html或错误
	 */
	public static function TCheckBox($name, array $values, $checked = '', $class = '', $id = '', array $extra = array()) {
		if(empty($values)) {
			self :: $_error = '00005';
			return false;
		}
		
		$html = '';
		foreach ($values as $key => $val) {
			$html .= self :: TInput($name, 'checkbox', $val, $checked, $class, $id ? $id . '[' . $val . ']' : '', $extra[$val]) . $key;
		}
		return $html;
	}
	
	/**
	 * 生成一个文本框
	 *
	 * @param String $name 参数名
	 * @param String $value	文本内容
	 * @param String $class 样式名
	 * @param String $id Id
	 * @param array $extra 附加参数
	 * @return String $html 返回的文本框
	 */
	public static function TTextArea($name, $value = '', $class = '', $id = '', array $extra = array()) {
		$html = '<textarea name="' . $name . '"';
		if($class)	$html .= ' class="' . $class . '"';
		if($id)		$html .= ' id="' . $id . '"';
		if(!empty($extra)) {
			$vars = array();
			foreach ($extra as $key => $val) {
				$vars[] = $key . '="' . $val . '"';
			}
			$html .= ' ' . implode(' ', $vars);
		}
		$html .= '>' . $value . '</textarea>';
		return $html;
	}
	
	/**
	 * 生成一段<img src=''...的html代码
	 *
	 * @param String $src 图片地址
	 * @param String $class 样式名
	 * @param String $id Id
	 * @param array $extra 附加参数
	 * @return String $html 返回html
	 */
	public static function TImg($src, $class = '', $id = '', array $extra = array()) {
		if(!$src) {
			self :: $_error = '00006';
			return false;
		}
		$html = '<img src="' . $src . '"';
		if($class)	$html .= ' class="' . $class . '"';
		if($id)		$html .= ' id="' . $id . '"';
		if(!empty($extra)) {
			$vars = array();
			foreach ($extra as $key => $val) {
				$vars[] = $key . '="' . $val . '"';
			}
			$html .= ' ' . implode(' ', $vars);
		}
		$html .= ' />';
		return $html;
	}
	
	/**
	 * 生成一段<a href=......代码
	 *
	 * @param String $href 链接地址
	 * @param String $text 链接文本
	 * @param String $target 链接目标
	 * @param String $class 样式名
	 * @param String $id Id
	 * @param array $extra 附加参数
	 * @return String $html 返回html
	 */
	public static function THrefLink($href = '#', $text = '', $target = '_blank', $class = '', $id = '', array $extra = array()) {
		$html = '<a href="' . $href . '" target="' . $target . '"';
		if($class)	$html .= ' class="' . $class . '"';
		if($id)		$html .= ' id="' . $id . '"';
		if(!empty($extra)) {
			$vars = array();
			foreach ($extra as $key => $val) {
				$vars[] = $key . '="' . $val . '"';
			}
			$html .= ' ' . implode(' ', $vars);
		}
		$html .= '>' . $text . '</a>';
		return $html;
	}
}
?>