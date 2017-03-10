<?php

class CaptchaAction extends Action{

    public function index(){

        import('@.ORG.Util.captcha.SimpleCaptcha');
        $captcha = new SimpleCaptcha();
        $captcha->width  = 140;
        $captcha->height = 60;
        $captcha->scale  = 4;
        $captcha->blur   = true;
        // OPTIONAL Change configuration...
        //$captcha->imageFormat = 'png';
        //$captcha->resourcesPath = "/var/cool-php-captcha/resources";
        $code = $captcha->getText();
        session_start();
        $_SESSION['verify_code'] = $code;
        $captcha->CreateImage();
    }

}

?>