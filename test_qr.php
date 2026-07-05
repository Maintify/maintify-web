<?php
require 'vendor/autoload.php';
use chillerlan\QRCode\{QRCode, QROptions};
$options = new QROptions([
    'outputType' => \chillerlan\QRCode\Output\QRMarkupSVG::class,
    'eccLevel' => \chillerlan\QRCode\Common\EccLevel::H
]);
$qrcode = new QRCode($options);
echo $qrcode->render('test');
