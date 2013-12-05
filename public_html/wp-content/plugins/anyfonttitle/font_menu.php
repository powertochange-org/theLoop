<?php

header("Content-type: image/png");


$tam = 20;
$angulo = 0;
$inicio_x = 5;
$inicio_y = 25;


$long = strlen($_GET['text'])*15+5;
if ($long > 450) {
	$long2 = 450;
	} else {
	$long2 = $long;
	}
$im = imagecreatetruecolor($long2, 40);


$blanco = imagecolorallocate($im, 255, 255, 255);
$negro  = imagecolorallocate($im, 0, 0, 0);
$orange = imagecolorallocate($im, 243, 116, 33);


ImageFill($im, 0, 0, $blanco); 


$texto = $_GET['text'];


$fuente = $_GET['font'];
$fuente = 'font/'.$fuente;


imagettftext($im, $tam, $angulo, $inicio_x, $inicio_y, $orange, $fuente, $texto);


imagepng($im);
imagedestroy($im);
?> 