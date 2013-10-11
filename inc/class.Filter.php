<?php
/**
   * @name 搜索过滤
   * @todo  专门用于3g.56.com的搜索过滤处理
   * @author jinlong 2012-05-17
   */

class Filter{
	/**
	 * 封装后的过滤
	 */
	public static function filterSpecialString ($string) {
		$tmp = '';
		$tmp = self::filterSpecialChar($string,3);
		$tmp = self::removeHalfEnString($tmp);
		return $tmp;
	}

	//过滤特殊字符函数
	public static function filterSpecialChar($str, $toType=1) {
		$search   = array(':',  ';',  '?',  '~', '!', '@',  '%', '^',  '&', '*',   '[', ']',  '{', '}',  '(', ')', '-' ,'/','|','#','+','"',"'");
		$replaced = array('：', '；', '？', '～', '！', '＠', '％','＾', '＆', '＊', '［', '］', '｛', '｝', '（', '）','－' ,'／','|','','','“ ','‘ ');
		if($toType == 1) {
			return str_replace($search, $replaced, $str);
		} else if($toType==2) {
			return str_replace($replaced, $search, $str);
		} else if($toType==3) {
			return str_replace($search, ' ', $str);
		}
	}

	public static function removeHalfEnString($str) {
		$badstr =array('','','','','','','','','','','','','','','','','','','','','','','');
		return str_replace($badstr,'',$str);
	}

	/**
	 * @name filter_movie
	 * @todo 过滤3g的版权剧集
	 * http://api.v.56.com/API/black_word_lib.php?type=mobile
	 * @param unknown_type $str
	 */
        public static function filter_movie($str, $type="") {
		$cacheKey = 'filter_movie';
		if(!Core::$kt){
			Core::InitMemd(); // kt 缓存 Core::$kt
		}
		$cacheData = Core::$kt->Get($cacheKey);
                
		if(!$cacheData || $_GET['dy'] == 'c') {
			//获取3g版权黑词
			$cacheData =  Http::Get('api.v.56.com', '/API/black_word_lib.php?type=mobile');
			$cacheData = explode("\n", $cacheData);
                        if($cacheData){
                                Core::$kt->Put($cacheKey,$cacheData,CACHE_TIME); // 3天
                        }
		}
		if($cacheData) {
			if($type) {
				return in_array($str, $cacheData);
			}
			foreach ($cacheData as $v) {
				$str = str_replace($v,'',$str);
			}
		}
		return $str;
	}
	public static function __filter_movie($str, $type="") {
		$cacheKey = 'filter_movie';
		Core::$redis->SetKey($cacheKey);
		$cacheData = Core::$redis->Get();

		if(!$cacheData || $_GET['dy'] == 'c') {
			//获取3g版权黑词
			$cacheData =  Http::Get('api.v.56.com', '/API/black_word_lib.php?type=mobile');
			$cacheData = explode("\n", $cacheData);
			Core::$redis->SetKey($cacheKey);
			Core::$redis->Setex($cacheData,CACHE_TIME);
		}
		if($cacheData) {
			if($type) {
				return in_array($str, $cacheData);
			}
			foreach ($cacheData as $v) {
				$str = str_replace($v,'',$str);
			}
		}
		return $str;
	}

	/**
	 * @name filter_video
	 * @todo 过滤3g的视频
	 * @param unknown_type $str
	 */
        public static function filter_video($ids, $type="") {
		$cacheKey = 'filter_vids';
                if(!Core::$kt){
			Core::InitMemd(); // kt 缓存 Core::$kt
		}
		$cacheData = Core::$kt->Get($cacheKey);
		
		if($_GET['dy'] == 'c' || !$cacheData) {
		//if(!$cacheData) {
			$thisCtrl = new DbFilterVideo();
			
			$where  = 'expires=0 OR (expires=1 AND overtime > "'.date("Y-m-d H:i:s").'")';
			$idData = $thisCtrl->getFilterVideos($where);
		
			
			if($idData){
				foreach ($idData as $v) {
					$delIds[] = $v['vid'];
				}
                                $cacheData = $delIds;
                                Core::$kt->Put($cacheKey,$cacheData,3600); // 3天
			}
			
		}
		if($cacheData){
			if($type) {
				return in_array($ids, $cacheData);
			}
			if($ids) {
				foreach ($ids as $k => $v) {
					if(in_array($v, $cacheData)) {
						unset($ids[$k]);
					}
				}
			}
		}
		
		return $ids;
	}
	public static function __filter_video($ids, $type="") {
		$cacheKey = 'filter_vids';
		 Core::$redis->SetKey($cacheKey);
		$cacheData = Core::$redis->Get();
		
		//if($_GET['dy'] == 'c' || !$cacheData) {
		if(!$cacheData) {
			$thisCtrl = new DbFilterVideo();
			
			$where  = 'expires=0 OR (expires=1 AND overtime > "'.date("Y-m-d H:i:s").'")';
			$idData = $thisCtrl->getFilterVideos($where);
		
			
			if($idData){
				foreach ($idData as $v) {
					$delIds[] = $v['vid'];
				}
			}
			$cacheData = $delIds;
			Core::$redis->Setex($cacheData,CACHE_TIME);
		}
		if($cacheData){
			if($type) {
				return in_array($ids, $cacheData);
			}
			if($ids) {
				foreach ($ids as $k => $v) {
					if(in_array($v, $cacheData)) {
						unset($ids[$k]);
					}
				}
			}
		}
		
		return $ids;
	}


	/**
	 * @name filter_channel
	 * @todo 过滤频道数据
	 * @param unknown_type $data
	 * @return number
	 */
	public static function filter_channel($data){
		
		if($data['action'] == 'view') {
			if($data['data']) {
				foreach ($data['data'] as $key => $value) {
					if(self::filter_video(g::flvDeId($value['vid']), 1)){
						unset($data['data'][$key]);
					}
				}
			}
		} else if($data['action'] == 'opera'){
			if($data['data']) {
				foreach ($data['data'] as $key => $value) {
					if(self::filter_movie($value['title'], 1)){
						unset($data['data'][$key]);
					}
				}
			}
		}
		return $data;
	}
}