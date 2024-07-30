<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(__DIR__);
$baseDir = dirname($vendorDir);

return array(
    'think\\composer\\' => array($vendorDir . '/topthink/think-installer/src'),
    'think\\captcha\\' => array($vendorDir . '/topthink/think-captcha/src'),
    'think\\' => array($baseDir . '/thinkphp/library/think', $vendorDir . '/topthink/think-helper/src', $vendorDir . '/topthink/think-image/src'),
    'app\\' => array($baseDir . '/application'),
    'PhpMimeMailParser\\' => array($vendorDir . '/php-mime-mail-parser/php-mime-mail-parser/src'),
    'PHPMailer\\PHPMailer\\' => array($vendorDir . '/phpmailer/phpmailer/src'),
);
