<?php
$image = '.\05_03\images\477987145.jpg';
$exif = exif_read_data($image);
//print_r($exif);

getimagesize($image, $info);
if (isset($info['APP13'])) {
    $iptc = iptcparse($info['APP13']);
    print_r($iptc);
}