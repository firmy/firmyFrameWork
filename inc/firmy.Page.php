<?php
if(!defined('IN_SYS')) 
{
	header("HTTP/1.1 404 Not Found");
	die;
}
/**
 *	@name:	class.Page.php
 *	@desc: 	分页类
 *	<code>
 		$page = new Page();
		$page->Setvar(array('order'=>$orderby,'category'=>$cat_id));
		$page->Set(LIST_PER_ROWS, $total_num);
		echo $page->Output(true, true);
	</code>
 *
 */
 
 class Page{

    /**
     * 页面输出结果
     *
     * @var string
     */
    var $output;

    /**
     * 使用该类的文件,默认为 PHP_SELF
     *
     * @var string
     */
    var $file;

    /**
     * 页数传递变量，默认为 'p'
     *
     * @var string
     */
    var $pvar = "page";

    /**
     * 页面大小
     *
     * @var integer
     */
    var $psize;

    /**
     * 当前页面
     *
     * @var ingeger
     */
    var $curr;

    /**
     * 要传递的变量数组
     *
     * @var array
     */
    var $varstr;

    /**
     * 总页数
     *
     * @var integer
     */
    var $tpage;

	var $inputPageNum;
	
    /**
     * 分页设置
     *
     * @access public
     * @param int $pagesize 页面大小
     * @param int $total    总记录数
     * @param int $current  当前页数，默认会自动读取
     * @return void
     */
    function Set($pagesize=20,$total,$current=false,$target='_self', $rewrite=false) {
    	if(!$rewrite){
			$gue1 = '?';$gue2='&';$gue3="=";
		}else{
			$gue1 = '/';$gue2='_';$gue3="-";
		}
	
        $show_num = 6;	//显示几个翻页按钮

        $this->tpage = ceil($total/$pagesize);
        if (!$current) {$current = isset($_REQUEST[$this->pvar])?$_REQUEST[$this->pvar]:1;}
        if ($current>$this->tpage) {$current = $this->tpage;}
        if ($current<1) {$current = 1;}

        $this->curr  = $current;
        $this->psize = $pagesize;

        if (!$this->file) {$this->file = $_SERVER['PHP_SELF'];}
        if ($this->tpage >= 1) {

            if ($current>1) {
                $this->output.='<a href="'.$this->file.$gue1.($this->varstr).$gue2.$this->pvar.$gue3.'1'.'" title="首页" target="'.$target.'">首页</a>';
            }
            if ($current>1) {
                $this->output.='<a href="'.$this->file.$gue1.($this->varstr).$gue2.$this->pvar.$gue3.($current-1).'" title="上页" target="'.$target.'">上页 </a>';
            }

            if($current > $show_num / 2) {
            	$this->output .= '...';
            }
//          $start  = floor($current/$show_num)*$show_num;
//          $end    = $start+($show_num-1);
			$start  = $current-floor($show_num/2);
            $end    = $current+ceil($show_num/2);

            if ($start<1)            {$start=1;}
            if ($end>$this->tpage)    {$end=$this->tpage;}

            for ($i=$start; $i<=$end; $i++) {
                if ($current==$i) {
                    $this->output.='<span class="current"><a href="javascript:">'.$i.'</a></span>';    //输出当前页数
                } else {
                    $this->output.='<a href="'.$this->file.$gue1.$this->varstr.$gue2.$this->pvar.$gue3.$i.'" target="'.$target.'">'.$i.'</a>';    //输出页数
                }
            }

            if($this->tpage - $current > $show_num / 2) {
            	$this->output .= '...';
            }
            
            if ($current<$this->tpage) {
                $this->output.='<a href="'.$this->file.$gue1.($this->varstr).$gue2.$this->pvar.$gue3.($current+1).'" title="下页" target="'.$target.'">下页 </a>';
            }
            if ($this->tpage > $current) {
                $this->output.='<a href="'.$this->file.$gue1.($this->varstr).$gue2.$this->pvar.$gue3.$this->tpage.'" title="尾页" target="'.$target.'">尾页</a>';
            }
            
            $this->totalPage = '<span class="disabled">共'.$this->tpage.'页</span>';
            if($this->tpage >= $show_num) {
            	$this->inputPageNum = '<span class="goto_page">转到<input class="inp" onKeypress="var osO;try{osO=window.event.keyCode}catch(e){osO=event.which;}if(osO==13){if(this.value<='.($this->tpage).'){document'.($target!='_self'?".$target":'').'.location = \''.$this->file.$gue1.($this->varstr).$gue2.$this->pvar.$gue3.'\'+this.value;}else{ alert(\'数值太大!\');}};if (osO < 45 || osO > 57) try{event.returnValue = false;}catch(e){}" type="text" value="'.($current).'" size="2" maxlength="5"  style="ime-mode:disabled" onChange="if(this.value<='.($this->tpage).'){document'.($target!='_self'?".$target":'').'.location = \''.$this->file.$gue1.($this->varstr).$gue2.$this->pvar.$gue3.'\'+this.value;}else{ alert(\'数值太大!\');}" />页<input name="" onclick="var objectPageNumIpt=this.parentElement.getElementsByTagName(\'INPUT\')[0];if(objectPageNumIpt.value<='.($this->tpage).'){document'.($target!='_self'?".$target":'').'.location = \''.$this->file.$gue1.($this->varstr).$gue2.$this->pvar.$gue3.'\'+objectPageNumIpt.value;}else{ alert(\'数值太大!\');}" type="button" value="GO" /></span>';
            }
        }
    }

    
     /**
       * @name setAjax
       * @param int $pagesize 页面大小
	   * @param int $total    总记录数
	   * @param str $anchors 描记，为了页面不翻动.   eg #auchors
       * @param str $setPageFunc 设置第几页的js函数名
	   * @param int $current  当前页数，默认会自动读取
       * @access 
       * @todo 获取ajax分页字符窜
       * @return void
       */
    public function setAjax($pagesize=20,$total,$current=false,$setPageFunc='',$anchors='') {
       
        $show_num = 6;	//显示几个翻页按钮
    	if ($anchors){
			$anchors = '#'.$anchors;
		}else{
			$anchors = 'javascript:;';
		}        

        $this->tpage = ceil($total/$pagesize);
        if (!$current) {$current = $_GET[$this->pvar];}
        if ($current>$this->tpage) {$current = $this->tpage;}
        if ($current<1) {$current = 1;}

        $this->curr  = $current;
        $this->psize = $pagesize;

        if ($this->tpage >= 1) {

            /*if ($current>$show_num) {
                $this->output.='<li><a href="'.$anchors.'" onclick="'.$setPageFunc.'('. ($current - $show_num) .')" title="前'.$show_num.'页">&lt;&lt;</a></li>';
            }*/
           if ($current>1) {
              // $this->output.='<a href="'.$anchors.'" onclick="'.$setPageFunc.'(1)" title="首页">首页</a>';
            }        	
            if ($current>1) {
                $this->output.='<a href="'.$anchors.'" onclick="'.$setPageFunc.'('. ($current - 1) .')" title="前页">上页</a>';
            }

            if($current > $show_num / 2) {
            	//$this->output .= '...';
            }
            
			$start  = $current-floor($show_num/2);
            $end    = $current+ceil($show_num/2);

            if ($start<1)            {$start=1;}
            if ($end>$this->tpage)    {$end=$this->tpage;}

            for ($i=$start; $i<=$end; $i++) {
                if ($current==$i) {
                    $this->output.='<span class="current"><a href="javascript:;">'.$i.'</a></span>';    //输出当前页数
                } else {
                    $this->output.='<a href="'.$anchors.'" onclick="'.$setPageFunc.'('. $i .')">'.$i.'</a>';    //输出页数
                }
            }

            if($this->tpage - $current > $show_num / 2) {
            	//$this->output .= '...';
            }
                        
            if ($current<$this->tpage) {
                $this->output.='<a href="'.$anchors.'" onclick="'.$setPageFunc.'('. ($current + 1) .')" title="下页">下页</a>';
            }
            
            $this->totalPage = '<span class="disabled">共'.$this->tpage.'页</span>';
            
        }
    }
    /**
     * 要传递的变量设置
     *
     * @access public
     * @param array $data   要传递的变量，用数组来表示，参见上面的例子
     * @return void
     */
    function Setvar($data, $rewrite=false) {
        if (!$omit) {
            $omit = array($this->pvar);
        }
        foreach ($_GET as $k=>$v)	if (!in_array($k,$omit))  $this->varstr .= $k.'='.urlencode($v).'&';
        if($append)  foreach ($append as $k=>$v) $this->varstr .= $k.'='.urlencode($v).'&';   

        //截掉右边的&,避免重复.
        $this->varstr = rtrim($this->varstr,'&');
        
        return $this->varstr;
    }

    /**
     * 分页结果输出
     *
     * @access public
     * @param bool $return 为真时返回一个字符串，否则直接输出，默认直接输出
     * @return string
     */
    function Output($return = false, $showInput=false) {
        $output = $showInput?($this->totalPage.$this->output.$this->inputPageNum):$this->totalPage.$this->output;
        if ($return) {
            return $output;
        } else {
            echo $output;
        }
    }
    
     public function getTotalPage() {
        return $this->tpage;
    }    

    /**
     * 生成Limit语句
     *
     * @access public
     * @return string
     */
    function Limit() {
        return (($this->curr-1)*$this->psize).','.$this->psize;
    }

 }
?>