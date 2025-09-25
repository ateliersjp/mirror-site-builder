<?php
require_once(get_template_directory() . '/mime.php');

add_action('customize_register', function($wp_customize) {
	$wp_customize->add_section(
		'rewrite_section',
		array(
			'title' => 'リライトルール',
			'priority' => 10,
		)
	);

	$wp_customize->add_setting(
		'origin_control',
		array(
			'sanitize_callback' => 'esc_url_raw',
			'default' => 'https://www.example.com',
		)
	);

	$wp_customize->add_control(
		'origin_control',
		array(
			'label' => 'オリジナルサイト',
			'description' => 'ミラーサイトの元となる、既存のWebサイトのトップページです。',
			'setting' => 'origin_control',
			'section' => 'rewrite_section',
			'type' => 'text',
		)
	);

	$wp_customize->add_setting(
		'mobile_control',
		array(
			'sanitize_callback' => 'esc_url_raw',
			'default' => 'https://m.example.com',
		)
	);

	$wp_customize->add_control(
		'mobile_control',
		array(
			'label' => 'モバイルサイト',
			'description' => 'ミラーサイトの元となる既存のWebサイトの、モバイル用トップページです。',
			'setting' => 'mobile_control',
			'section' => 'rewrite_section',
			'type' => 'text',
		)
	);

	$wp_customize->add_setting(
		'cf_worker_control',
		array(
			'sanitize_callback' => 'esc_url_raw',
			'default' => 'https://workers.dev/proxy',
		)
	);

	$wp_customize->add_control(
		'cf_worker_control',
		array(
			'label' => 'Cloudflare Worker',
			'description' => 'リバースプロキシとして機能するCloudflare WorkerのURLです。',
			'setting' => 'cf_worker_control',
			'section' => 'rewrite_section',
			'type' => 'text',
		)
	);

	$wp_customize->add_setting(
		'active_param_control',
		array(
			'default' => 'page',
		)
	);

	$wp_customize->add_control(
		'active_param_control',
		array(
			'label' => 'アクティブパラメータ',
			'description' => 'URLに付加することでウェブサイトの内容に影響を与える値です。',
			'setting' => 'active_param_control',
			'section' => 'rewrite_section',
			'type' => 'text',
		)
	);

	$wp_customize->add_setting(
		'expiry_control',
		array(
			'default' => 5,
		)
	);

	$wp_customize->add_control(
		'expiry_control',
		array(
			'label' => 'アップデート間隔',
			'description' => 'オリジナルサイトから最新のコンテンツを同期する間隔(分)です。0にすると初回のみ同期を行います。',
			'setting' => 'expiry_control',
			'section' => 'rewrite_section',
			'type' => 'number',
		)
	);

	$wp_customize->add_setting(
		'special_page_control',
		array(
			'default' => '^/[^/]*$',
		)
	);

	$wp_customize->add_control(
		'special_page_control',
		array(
			'label' => '特別ページ',
			'description' => '異なるアップデート間隔を適用するページを、スラッグの正規表現で設定できます。',
			'setting' => 'special_page_control',
			'section' => 'rewrite_section',
			'type' => 'text',
		)
	);

	$wp_customize->add_setting(
		'special_expiry_control',
		array(
			'default' => 5,
		)
	);

	$wp_customize->add_control(
		'special_expiry_control',
		array(
			'label' => '特別ページのアップデート間隔',
			'description' => '特別ページに適用されるアップデート間隔(分)です。0にすると初回のみ同期を行います。',
			'setting' => 'special_expiry_control',
			'section' => 'rewrite_section',
			'type' => 'number',
		)
	);

	$wp_customize->add_section(
		'filter_section',
		array(
			'title' => 'コンテンツフィルター',
			'priority' => 10,
		)
	);

	$wp_customize->add_setting(
		'charset_control',
		array(
			'default' => 'UTF-8',
		)
	);

	$wp_customize->add_control(
		'charset_control',
		array(
			'label' => '文字コード',
			'description' => 'オリジナルサイトの文字コードです。',
			'setting' => 'charset_control',
			'section' => 'filter_section',
			'type' => 'select',
			'choices' => array(
				'UTF-8' => __( 'UTF-8 (デフォルト)' ),
				'Shift_JIS' => __( 'Shift_JIS' ),
			),
		)
	);

	$wp_customize->add_setting(
		'filter_control',
		array(
			'default' => <<< _EOT_
			function (\$content) {
				return \$content;
			}
			_EOT_,
		)
	);

	$wp_customize->add_control(
		'filter_control',
		array(
			'label' => 'コンテンツフィルター',
			'description' => 'コンテンツを書き換えるルールです。',
			'setting' => 'filter_control',
			'section' => 'filter_section',
			'type' => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'html_filter_control',
		array(
			'default' => <<< _EOT_
			function (\$content) {
				return \$content;
			}
			_EOT_,
		)
	);

	$wp_customize->add_control(
		'html_filter_control',
		array(
			'label' => 'HTMLコンテンツフィルター',
			'description' => 'HTMLコンテンツを書き換えるルールです。',
			'setting' => 'html_filter_control',
			'section' => 'filter_section',
			'type' => 'textarea',
		)
	);
});

function save_theme_mod($name, $value) {
	file_put_contents(get_template_directory() . '/' . $name, <<< _EOT_
	<?php
	return Closure::fromCallable($value);
	?>
	_EOT_);
}

add_action('customize_save_after', function($wp_customize) {
	set_theme_mod('origin', $wp_customize->get_setting('origin_control')->value());
	set_theme_mod('mobile', $wp_customize->get_setting('mobile_control')->value());
	set_theme_mod('cf_worker', $wp_customize->get_setting('cf_worker_control')->value());
	set_theme_mod('active_param', $wp_customize->get_setting('active_param_control')->value());
	set_theme_mod('expiry', $wp_customize->get_setting('expiry_control')->value());
	set_theme_mod('special_page', $wp_customize->get_setting('special_page_control')->value());
	set_theme_mod('special_expiry', $wp_customize->get_setting('special_expiry_control')->value());
	set_theme_mod('charset', $wp_customize->get_setting('charset_control')->value());
	save_theme_mod('filter.php', $wp_customize->get_setting('filter_control')->value());
	save_theme_mod('html.php', $wp_customize->get_setting('html_filter_control')->value());
});

function is_mobile() {
	return isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'mobile') !== false;
}

function is_update($path) {
	if (!file_exists($path['local'])) {
		return true;
	}

	$special = '/' . str_replace('/', "\\/", get_theme_mod('special_page')) . '/i';
	$is_special = (bool) preg_match($special, $path['slug']);
	$expiry = get_theme_mod($is_special ? 'special_expiry' : 'expiry') * 60;

	if ($expiry === 0) {
		return false;
	}

	$elapsed_time = time() - filemtime($path['local']);
	return $expiry < $elapsed_time;
}

function get_path($uri) {
	$slug = (string) wp_parse_url((string) $uri, PHP_URL_PATH);
	$query = (string) wp_parse_url((string) $uri, PHP_URL_QUERY);
	$args = array();

	if (!empty($query)) {
		$args = wp_parse_args($query);
		$query = '?' . $query;
	}

	$path = array(
		'slug' => rtrim($slug, '/'),
		'remote' => $slug . $query,
	);

	if (strrpos($path['slug'], '/') < strrpos($path['slug'], '.')) {
		$path['local'] = WP_CONTENT_DIR . '/mirrorsite/{{origin}}' . $path['slug'];
	} else {
		$path['local'] = WP_CONTENT_DIR . '/mirrorsite/{{origin}}' . $path['slug'] . '/index.html';
	}

	$param = get_theme_mod('active_param');

	if (!empty($param) && isset($args[$param])) {
		$offset = strrpos($path['local'], '.');
		$before = substr($path['local'], 0, $offset + 1);
		$after = substr($path['local'], $offset);
		$path['local'] = $before . rawurlencode($args[$param]) . $after;
	}

	return $path;
}

function get_slug($uri) {
	return get_path($uri)['remote'];
}

add_action('template_redirect', function () {
	$origin = home_url((string) wp_parse_url(get_theme_mod('origin'), PHP_URL_PATH));
	$mobile = home_url((string) wp_parse_url(get_theme_mod('mobile'), PHP_URL_PATH));

	$origin_slug = get_slug($origin);
	$mobile_slug = get_slug($mobile);

	if ($origin_slug === $mobile_slug) {
		return;
	}

	$slug = get_slug($_SERVER['REQUEST_URI']);

	if (is_mobile()) {
		if ($slug === $origin_slug) {
			wp_safe_redirect($mobile);
			exit;
		}
	} else {
		if ($slug === $mobile_slug) {
			wp_safe_redirect($origin);
			exit;
		}
	}
});

function filter_contents($content, $replacements) {
	foreach ($replacements as $search => $replace) {
		$content = str_ireplace($search, $replace, $content);
	}

	return $content;
}

function save_contents($fn, $mime, $content, $replacements) {
	if (strpos($mime, 'text/') === 0) {
		$charset = get_theme_mod('charset');

		if ($charset && $charset !== 'UTF-8') {
			$content = mb_convert_encoding($content, 'UTF-8', $charset);
			$content = str_ireplace($charset, 'UTF-8', $content);
		}

		$content = Closure::fromCallable(require(get_template_directory() . '/filter.php'))($content);

		$content = filter_contents($content, $replacements);
	}

	if (strpos($mime, 'text/html') === 0) {
		$content = Closure::fromCallable(require(get_template_directory() . '/html.php'))($content);
	}

	if (!empty($content)) {
		$dir = dirname($fn);

		if (!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}

		file_put_contents($fn, $content, LOCK_EX);
	}
}

add_action('template_redirect', function () {
	$origin = wp_parse_url(get_theme_mod(is_mobile() ? 'mobile' : 'origin'));
	$mirror = wp_parse_url(home_url());

	if (!isset($origin['scheme'])) {
		$origin['scheme'] = 'https';
	}

	$path = get_path($_SERVER['REQUEST_URI']);
	$path['local'] = str_replace('{{origin}}', $origin['host'], $path['local']);
	$mime = get_mime($path['local']);


	if (is_update($path)) {
		$uri = sprintf(
			'%s://%s%s',
			$origin['scheme'],
			$origin['host'],
			$path['remote']
		);

		if ($origin['scheme'] === ($mirror['scheme'] ?? 'https')) {
			$origin['proto'] = '';
			$mirror['proto'] = '';
		} else {
			$origin['proto'] = $origin['scheme'];
			$mirror['proto'] = ($mirror['scheme'] ?? 'https');
		}

		if (strpos($origin['host'], 'www.') === 0 && strpos($mirror['host'], 'www.') === 0) {
			$origin['domain'] = substr($origin['host'], 4);
			$mirror['domain'] = substr($mirror['host'], 4);
		} else {
			$origin['domain'] = $origin['host'];
			$mirror['domain'] = $mirror['host'];
		}

		$replacements = [
			$origin['proto'] . '://' . $origin['host'] => $mirror['proto'] . '://' . $mirror['host'],
			$origin['domain'] => $mirror['domain'],
		];

		while (true) {
			$remote_uri = get_theme_mod('cf_worker') . '?proxyUrl=' . rawurlencode($uri);
			$ch = curl_init($remote_uri);

			curl_setopt($ch, CURLOPT_TIMEOUT, 3);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

			if (isset($_SERVER['HTTP_USER_AGENT'])) {
				curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			}

			$content = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if (300 <= $http_code && $http_code < 400) {
				$redirect_uri = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

				if ($uri === $redirect_uri) {
					break;
				} else {
					$uri = $redirect_uri;
					curl_close($ch);
				}
			} else {
				break;
			}
		}

		if ($http_code === 200) {
			$fn = $path['local'];
		}

		if ($http_code === 404) {
			$fn = WP_CONTENT_DIR . '/mirrorsite/' . $origin['host'] . '/error/404/index.html';
			$mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		}

		if ($http_code === 200 || $http_code === 404) {
			save_contents($fn, $mime, $content, $replacements);
		}

		curl_close($ch);
	}

	if (is_file($path['local'])) {
		status_header(200);
		header('Content-Type: ' . $mime);
		header('Content-Length: ' . filesize($path['local']));
		readfile($path['local']);
		exit;
	} else {
		include_once(get_template_directory() . '/404.php');
	}

	exit;
}, 50);

add_action('pre_get_posts', function($query) {
	if(!is_admin() && $query->is_main_query()) {
		$param = get_theme_mod('active_param');

		if(!empty($param) && isset($_GET[$param])) {
			$query->set($param, false);
		}
	}
});
?>