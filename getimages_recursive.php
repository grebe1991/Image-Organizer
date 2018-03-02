<?php
function getImages($dir) {
    if (is_dir($dir) && is_readable($dir)) {
        // Get an array of files and folders without the dot files
        $files = array_diff(scandir($dir), array('.', '..'));
    }
    $images = array();
    $subdirs = array();
    foreach ($files as $file) {
        // Add images and subdirectories to separate arrays
        if (preg_match('/\.(?:jpg|gif|png)$/i', $file)) {
            $images[] = $dir . '/' . $file;
        } elseif (is_dir($dir . '/' . $file)) {
            $subdirs[] = $dir . '/' . $file;
        }
    }
    // Loop through subdirectories calling getImages() recursively
    foreach ($subdirs as $sub) {
        $images = array_merge($images, getImages($sub));
    }
    return $images;
}

$images = getImages('.');
print_r($images);