<?php
/** Click Captcha 验证码类
*   Date:   2013-05-04
*   Author: fdipzone
*   Ver:    1.0
*/

class ClickCaptcha { // class start

    public $sess_name = 'm_captcha';
    public $width = 500;
    public $height = 200;
    public $icon = 5;
    public $iconColor = array(255, 255, 0);
    public $backgroundColor = array(0, 0, 0);
    public $iconSize = 56;

    private $_img_res = null;


    public function __construct($sess_name=''){
        if(session_id() == ''){
            session_start();
        }

        if($sess_name!=''){
            $this->sess_name = $sess_name; // 设置session name
        }
    }


    /** 创建验证码 */
    public function create(){

        // 创建图象
        $this->_img_res = imagecreate($this->width, $this->height);
        
        // 填充背景
        ImageColorAllocate($this->_img_res, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2]);

        // 分配颜色
        $col_ellipse = imagecolorallocate($this->_img_res, $this->iconColor[0], $this->iconColor[1], $this->iconColor[2]);

        $minArea = $this->iconSize/2+3;

        // 混淆用图象,不完整的圆
        for($i=0; $i<$this->icon; $i++){
            $x = mt_rand($minArea, $this->width-$minArea);
            $y = mt_rand($minArea, $this->height-$minArea);
            $s = mt_rand(0, 360);
            $e = $s + 330;
            imagearc($this->_img_res, $x, $y, $this->iconSize, $this->iconSize, $s, $e, $col_ellipse);
        }

        // 验证用图象,完整的圆
        $x = mt_rand($minArea, $this->width-$minArea);
        $y = mt_rand($minArea, $this->height-$minArea);
        $r = $this->iconSize/2;
        imagearc($this->_img_res, $x, $y, $this->iconSize, $this->iconSize, 0, 360, $col_ellipse);

        // 记录圆心坐标及半径
        $this->captcha_session($this->sess_name, array($x, $y, $r));

        // 生成图象
        Header("Content-type: image/PNG");
        ImagePNG($this->_img_res);
        ImageDestroy($this->_img_res);

        exit();
    }


    /** 检查验证码
    * @param String $captcha  验证码
    * @param int    $flag     验证成功后 0:不清除session 1:清除session
    * @return boolean
    */
    public function check($captcha, $flag=1){
        if(trim($captcha)==''){
            return false;
        }
        
        if(!is_array($this->captcha_session($this->sess_name))){
            return false;
        }

        list($px, $py) = explode(',', $captcha);
        list($cx, $cy, $cr) = $this->captcha_session($this->sess_name);

        if(isset($px) && is_numeric($px) && isset($py) && is_numeric($py) && 
            isset($cx) && is_numeric($cx) && isset($cy) && is_numeric($cy) && isset($cr) && is_numeric($cr)){
            if($this->pointInArea($px,$py,$cx,$cy,$cr)){
                if($flag==1){
                    $this->captcha_session($this->sess_name,'');
                }
                return true;
            }
        }
        return false;
    }


    /** 判断点是否在圆中
    * @param int $px  点x
    * @param int $py  点y
    * @param int $cx  圆心x
    * @param int $cy  圆心y
    * @param int $cr  圆半径
    * sqrt(x^2+y^2)<r
    */
    private function pointInArea($px, $py, $cx, $cy, $cr){
        $x = $cx-$px;
        $y = $cy-$py;
        return round(sqrt($x*$x + $y*$y))<$cr;
    }


    /** 验证码session处理方法
    * @param   String   $name    captcha session name
    * @param   String   $value
    * @return  String
    */
    private function captcha_session($name,$value=null){
        if(isset($value)){
            if($value!==''){
                $_SESSION[$name] = $value;
            }else{
                unset($_SESSION[$name]);
            }
        }else{
            return isset($_SESSION[$name])? $_SESSION[$name] : '';
        }
    }

} // class end

?>