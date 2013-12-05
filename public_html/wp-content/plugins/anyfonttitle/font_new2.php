<?php

header("Content-type: image/png");


$gesttit_tam = $_GET['tam'];
$gesttit_ang = $_GET['ang'];
$gesttit_ini = $_GET['ini'];
$gesttit_font = $_GET['font'];
$texto = $_GET['text'];
$gesttit_col = $_GET['col'];
$gesttit_fond = $_GET['fond'];



$long = imagettfbbox($gesttit_tam,$gesttit_ang,$gesttit_font,$texto);
$ancho = $long[2] - $long[0] + 2 + $gesttit_ini;
$alto = $long[1] - $long[7];
$inicio_y = $alto - $long[1];


if ($ancho > 450) {
	$ancho = 450;
	}
$im = imagecreatetruecolor($ancho, $alto);


$fondo = imagecolorallocate($im, hexdec('0x'.substr($gesttit_fond,0,2)), hexdec('0x'.substr($gesttit_fond,2,2)), hexdec('0x'.substr($gesttit_fond,4,2)));
$color = imagecolorallocate($im, hexdec('0x'.substr($gesttit_col,0,2)), hexdec('0x'.substr($gesttit_col,2,2)), hexdec('0x'.substr($gesttit_col,4,2))); 


ImageFill($im, 0, 0, $fondo); 



imagettftext($im, $gesttit_tam, $gesttit_ang, $gesttit_ini, $inicio_y, $color, $gesttit_font, $texto);


imagepng($im);
imagedestroy($im);
?> 