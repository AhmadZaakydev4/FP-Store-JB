<?php
/**
 * Script untuk minify CSS dan JS
 * Jalankan untuk membuat versi minified
 */

function minifyCSS($css) {
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    
    // Remove unnecessary whitespace
    $css = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $css);
    $css = str_replace(['; ', ' {', '{ ', ' }', '} ', ': ', ' :'], [';', '{', '{', '}', '}', ':', ':'], $css);
    
    return trim($css);
}

function minifyJS($js) {
    // Simple minification - remove comments and extra spaces
    $js = preg_replace('/\/\*[\s\S]*?\*\//', '', $js); // Remove /* */ comments
    $js = preg_replace('/\/\/.*$/m', '', $js); // Remove // comments
    $js = preg_replace('/\s+/', ' ', $js); // Replace multiple spaces with single space
    $js = str_replace(['; ', ' {', '{ ', ' }', '} '], [';', '{', '{', '}', '}'], $js);
    
    return trim($js);
}

echo "<h2>Minify Assets</h2>";

// Minify CSS files
$cssFiles = glob('assets/css/*.css');
foreach ($cssFiles as $cssFile) {
    if (strpos($cssFile, '.min.css') === false) {
        $css = file_get_contents($cssFile);
        $minified = minifyCSS($css);
        
        $minFile = str_replace('.css', '.min.css', $cssFile);
        file_put_contents($minFile, $minified);
        
        $originalSize = strlen($css);
        $minifiedSize = strlen($minified);
        $saved = $originalSize - $minifiedSize;
        $percentage = round(($saved / $originalSize) * 100, 2);
        
        echo "<p>CSS: " . basename($cssFile) . " → " . basename($minFile);
        echo " (Saved: " . round($saved/1024, 2) . " KB, {$percentage}%)</p>";
    }
}

// Minify JS files
$jsFiles = glob('assets/js/*.js');
foreach ($jsFiles as $jsFile) {
    if (strpos($jsFile, '.min.js') === false) {
        $js = file_get_contents($jsFile);
        $minified = minifyJS($js);
        
        $minFile = str_replace('.js', '.min.js', $jsFile);
        file_put_contents($minFile, $minified);
        
        $originalSize = strlen($js);
        $minifiedSize = strlen($minified);
        $saved = $originalSize - $minifiedSize;
        $percentage = round(($saved / $originalSize) * 100, 2);
        
        echo "<p>JS: " . basename($jsFile) . " → " . basename($minFile);
        echo " (Saved: " . round($saved/1024, 2) . " KB, {$percentage}%)</p>";
    }
}

echo "<p><strong>Catatan:</strong> Update link di HTML untuk menggunakan file .min.css dan .min.js</p>";
?>