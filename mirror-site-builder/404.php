<?php
function get_origin() {
	return get_theme_mod(!isset($_SERVER['HTTP_USER_AGENT']) || stripos($_SERVER['HTTP_USER_AGENT'], 'mobile') === false ? 'origin' : 'mobile');
}

status_header(404);
header('Content-Type: text/html');
$fn = WP_CONTENT_DIR . '/mirrorsite/' . wp_parse_url(get_origin(), PHP_URL_HOST) . '/error/404/index.html';
header('Content-Length: ' . filesize($fn));
readfile($fn);
exit;
?>
