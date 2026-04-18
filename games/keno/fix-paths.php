<?php
$html = file_get_contents('index.html');

// Corriger les chemins CSS
$html = str_replace('href="css/', 'href="games/keno/css/', $html);
// Corriger les chemins JS
$html = str_replace('src="js/', 'src="games/keno/js/', $html);
// Corriger les chemins images/sons
$html = str_replace('href=\'./favicon.ico\'', 'href=\'games/keno/favicon.ico\'', $html);
$html = preg_replace('/src="(sounds|sprites)\//', 'src="games/keno/$1/', $html);
$html = preg_replace('/url\(\'(sounds|sprites)\//', 'url(\'games/keno/$1/', $html);

file_put_contents('index.html', $html);
echo "✅ Chemins KENO corrigés\n";
