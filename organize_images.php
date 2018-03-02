<?php
require_once 'xmpdata.php';

$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'));
$images = new RegexIterator($dir, '/\.(?:jpg|png|gif)$/i');
foreach ($images as $image) {
    $path = $image->getPathname();
    $files[] = $path;
    if ($caption = getIptcCaption($path)) {
        $captions[] = $caption;
    } else {
        $xmp = getXmpData($path);
        if ($xmp) {
            $captions[] = getXmpCaption($xmp);
        } else {
            $captions[] = 'No caption';
        }
    }
}
array_multisort($captions, SORT_STRING | SORT_FLAG_CASE, $files);
echo '<dl>';
$previous = '';
$len = count($files);

for ($i = 0; $i < $len; $i++) {
    $first = strtoupper(substr($captions[$i], 0, 1));
    if ($first != $previous) {
        echo '<dt>' . $first . '</dt>';
        if (!is_dir($first)) {
            mkdir($first, 0755);
        }
    }
    $filename = basename($files[$i]);
    echo "<dd>$filename &mdash; $captions[$i]</dd>";
    copy($files[$i], $first . '/' . $filename);
    //unlink($files[$i]);
    $previous = $first;
}
echo '</dt>';

function getIptcCaption($image) {
    if(!getimagesize($image, $info)) {
        return "Can't open $image";
    } else {
        $caption = '';
        if (isset($info['APP13'])) {
            $iptc = iptcparse($info['APP13']);
            if (isset($iptc['2#120'][0])) {
                $caption = $iptc['2#120'][0];
            }
        }
        return $caption;
    }
}