<?php

require 'vendor/autoload.php';
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

$options = new QROptions([
    'outputType' => QRMarkupSVG::class,
    'eccLevel' => EccLevel::H,
]);
$qrcode = new QRCode($options);
echo $qrcode->render('test');
