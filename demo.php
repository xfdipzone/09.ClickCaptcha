<?php
session_start();
require('ClickCaptcha.class.php');

if(isset($_GET['get_captcha'])){ // get captcha
    $obj = new ClickCaptcha();
    $obj->create();
    exit();
}

if(isset($_POST['send']) && $_POST['send']=='true'){ // submit
    $name = isset($_POST['name'])? trim($_POST['name']) : '';
    $captcha = isset($_POST['captcha'])? trim($_POST['captcha']) : '';

    $obj = new ClickCaptcha();

    if($obj->check($captcha)){
        echo 'your name is:'.$name;
    }else{
        echo 'captcha not match';
    }
    echo ' <a href="demo.php">back</a>';

}else{ // html
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
 <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title> Click Captcha Demo </title>
  <script type="text/javascript" src="jquery-1.6.2.min.js"></script>
  <script type="text/javascript">
    $(function(){
        $('#captcha_img').click(function(e){
            var x = e.pageX - $(this).offset().left;
            var y = e.pageY - $(this).offset().top;
            $('#captcha').val(x+','+y);
        })

        $('#btn').click(function(e){
            if($.trim($('#name').val())==''){
                alert('Please input name!');
                return false;
            }

            if($.trim($('#captcha').val())==''){
                alert('Please click captcha!');
                return false;
            }
            $('#form1')[0].submit();
        })
    })
  </script>
 </head>

 <body>
    <form name="form1" id="form1" method="post" action="demo.php" onsubmit="return false">
    <p>name:<input type="text" name="name" id="name"></p>
    <p>Captcha:Please click full circle<br><img id="captcha_img" src="demo.php?get_captcha=1&t=<?=time() ?>" style="cursor:pointer"></p>
    <p><input type="submit" id="btn" value="submit"></p>
    <input type="hidden" name="send" value="true">
    <input type="hidden" name="captcha" id="captcha">
    </form>
 </body>
</html>
<?php } ?>