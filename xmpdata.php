<?php
function getXmpData($filename) {
    if (!is_readable($filename)) {
        return false;
    }
    $buffer = null;

    // Open file and read 50,000 bytes at a time
    $file = fopen($filename, 'r');
    while (($chunk = fread($file, 50000)) !== FALSE) {

        $buffer .= $chunk;

        // Find opening and closing XMP tags
        $start = strpos($buffer, '<x:xmpmeta');
        $end = strpos($buffer, '</x:xmpmeta>');

        if ($start !== false && $end !== false) {
            // If both tags found, return XMP
           return substr($buffer, $start, $end + 12);
        } elseif ($start !== false) {
            // Trim buffer to start tag if closing not found
            $buffer = substr($buffer, $start);
        } elseif (strlen($buffer) >= 50000) {
            // If neither found, keep only last 20 bytes of buffer
            $buffer = substr($buffer, 49980);
        }
    }
    fclose($file);
    // Return false if XMP not found
    return false;
}

function getXmpCaption($xmp) {
    $caption = '';
    $meta = simplexml_load_string($xmp);
    $meta->registerXPathNamespace('dc', 'http://purl.org/dc/elements/1.1/');
    $meta->registerXPathNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');

    // Get description
    $description = $meta->xpath('//dc:description/rdf:Alt/rdf:li');
    if (isset($description[0])) {
        // Cast value explicitly to string
        $caption = (string) $description[0];
    }
    if (empty($caption)) {
        // Use title if description not set
        $title = $meta->xpath('//dc:title/rdf:Alt/rdf:li');
        if (isset($title[0])) {
            $caption = (string) $title[0];
        }
        if (empty ($caption)) {
           $caption = 'No caption';
        }
    }
    return $caption;
}
