<?php
define('DOC_ROOT', __DIR__ . '/../pages');

// Securely fetch the file Apache intended to serve
$filePathRaw = $_SERVER['PATH_TRANSLATED'] ?? '';

if (!empty($filePathRaw) && file_exists($filePathRaw) && is_file($filePathRaw)) {
    // Use realpath to resolve any symlinks for a consistent path comparison.
    $filePath = realpath($filePathRaw);
    $docRoot = realpath(DOC_ROOT);

    // 1. Read the raw target HTML file
    $content = file_get_contents($filePath);

    // Make path relative to DOC_ROOT for the webrobot editor to use.
    // Use DIRECTORY_SEPARATOR for better cross-platform compatibility.
    $relativePath = str_replace($docRoot . DIRECTORY_SEPARATOR, '', $filePath);

    // 2. Wrap the SSI tags using standard PHP regex
    $content = "<includessi data-path=\"{$relativePath}\">" . $content . '</includessi>';

    // 3. Output the result to the output buffer
    header('Content-Type: text/html; charset=UTF-8');
    echo $content;
} else {
    // Fallback if someone hits the wrapper directly or a file is missing
    header("HTTP/1.1 404 Not Found");
    echo "Target file not found.";
}
