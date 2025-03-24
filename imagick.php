<?php
require 'vendor/autoload.php';

use Imagick;

$imagick = new Imagick();
$imagick->newImage(100, 100, new ImagickPixel('white'));
$imagick->setImageFormat('png');
header('Content-Type: image/png');
echo $imagick;