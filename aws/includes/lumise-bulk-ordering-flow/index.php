<?php
if(file_exists(__DIR__.'/rewrite-functions.php')){
	include_once __DIR__.'/rewrite-functions.php';
}
if(file_exists(__DIR__.'/general-functions.php')){
	include_once __DIR__.'/general-functions.php';
}
if(file_exists(__DIR__.'/ajax-functions.php')){
	include_once __DIR__.'/ajax-functions.php';
}

add_shortcode('tkaws_product_design_canvases','tkawsGetProductCanvasesByTag');
function tkawsGetProductCanvasesByTag($atts){
	global $lumise;	
	
	$atts = shortcode_atts(
        array('tag' => 'merch-store'),
        $atts
    );    
    $tag = $atts['tag'];
    	
	$prdAssetUrl = $lumise->cfg->assets_url . 'assets/raws/';
	$productColors = tkawsGetProductColorsByTag($tag);
	$products = new WP_Query(
						array(
							'post_type'=>'product',
							'posts_per_page'=>-1,
							'tax_query'=>array(
											'relation'=>'AND',
											array(
												'taxonomy' => 'product_tag',
												'field' => 'slug',
												'terms' => array($tag),
											)
										),
							'orderby'=>'menu_order',
							'order'=>'DESC'
						)
					);
	
	if($products->have_posts()){		
		include_once __DIR__.DS.'templates'.DS.'assets-enqueue.php';		
		ob_start();
		echo '<div class="product-design-wrapper">'."\n";
			include_once __DIR__.DS.'templates'.DS.'loader-popup.php';
			include_once __DIR__.DS.'templates'.DS.'product-canvases.php';
			include_once __DIR__.DS.'templates'.DS.'design-start.php';
			include_once __DIR__.DS.'templates'.DS.'design-alt-start.php';
			include_once __DIR__.DS.'templates'.DS.'design-categories.php';
			include_once __DIR__.DS.'templates'.DS.'design-products.php';
			include_once __DIR__.DS.'templates'.DS.'design-final.php';
		echo '</div>'."\n";
		$return = ob_get_clean();		
	}
	wp_reset_query();
	
	return $return;
}

add_action( 'wp_print_footer_scripts',function(){
	$lumiseEdtPage =intval(get_option('lumise_editor_page'));
	if($lumiseEdtPage > 0 && is_page($lumiseEdtPage)){
		$tmplcats = base64_decode(urldecode($_GET['tmplcats']));
		$tmpl = base64_decode(urldecode($_GET['tmpl']));

		if(!empty($tmplcats) && !empty($tmpl)){
			echo 	'<script>'."\n".
						'var tkawsSelectedTemplateCategories = '.$tmplcats.';'."\n".
						'var tkawsSelectedTemplate = '.$tmpl.';'."\n".
						'console.log(tkawsSelectedTemplateCategories,"template categories");'.
						'console.log(tkawsSelectedTemplate,"template");'.
					'</script>'."\n".
					'<script type="text/javascript" '.
							'src="'.trailingslashit(
										get_stylesheet_directory_uri()
									).'aws'.DS.'includes'.DS.
									'lumise-bulk-ordering-flow'.DS.
									'assets'.DS.'scripts'.DS.
									'template-select-scripts.js?v='.
									filemtime(__DIR__ . '/assets/scripts/template-select-scripts.js').
								'" '.
							'id="lmsedtrpage-template-selection"'.
					'></script>'."\n".
					'<div class="loader"><svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve"><path fill="#000" d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">          <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform></path></svg></div>'."\n".
					'<style>.loader{width:100%; height:100%; position:fixed; left:0; top:0; background:#fff; display:flex; align-items:center; justify-content:center; z-index:9999999999;} .loader svg{width:100px; height:100px;}</style>';
		}
	}
});

add_action('design-editor-footer',function(){
	if(isset($_GET['tkawsd2c']) && intval($_GET['tkawsd2c']) == 1){
		ob_start();
			get_header();
		$_header = ob_get_clean();
		
		$hexp = explode('<header',$_header,2);
		if(is_array($hexp) && count($hexp) > 0 && isset($hexp[1])){
			$_header = '<header'.$hexp[1];
		}else{
			$_header = false;
		}
		
		ob_start();
			remove_all_actions('wp_footer');
			get_footer();
		$_footer = ob_get_clean();
		
		$fexp = explode('<footer',$_footer,2);
		if(is_array($fexp) && count($fexp) > 0 && isset($fexp[1])){
			$fexp = $fexp[1];
			$fexp = explode('</footer',$fexp);
			if(is_array($fexp) && count($fexp) > 0){
				unset($fexp[count($fexp)-1]);
				$_footer = '<footer'.implode('</footer',$fexp).'</footer>';
			}else{
				$_footer = false;
			}
		}else{
			$_footer = false;
		}
		
		ob_start();
			include_once __DIR__.DS.'templates'.DS.'edit-or-a2c.php';
		$_template = ob_get_clean();	
		
		$return = '<div class="lumise-design-editor-tkawsd2c">'.
					$_header.
						$template.
					$_footer.
				  '</div>';
		
		echo $return;
	}
},99999);
add_action( 'wp_print_scripts',function(){
	$lumiseEdtPage =intval(get_option('lumise_editor_page'));
	if($lumiseEdtPage > 0 && is_page($lumiseEdtPage)){
		if(isset($_GET['tkawsd2c']) && intval($_GET['tkawsd2c']) == 1){
			echo '<style>'."\n".
					'body > .wrapper{display : none;}'.
				 '</style>'."\n".
				 '<link rel="stylesheet" '.
							'href="'.trailingslashit(
										get_stylesheet_directory_uri()
									).'assets'.DS.'css'.DS.
									'styles.css?v='.
									filemtime(trailingslashit(
										get_stylesheet_directory()
									). '/assets/css/styles.css').
								'" '.
							' id="lmsedtrpage-head-foot-styles"'.
					' />'."\n".
				 '<!--<style>.loader{width:100%; height:100%; position:fixed; left:0; top:0; background:#fff; display:flex; align-items:center; justify-content:center; z-index:9999999999;} .loader svg{width:100px; height:100px;}</style>-->';
		}
	}
});
add_action( 'wp_print_footer_scripts',function(){
	$lumiseEdtPage =intval(get_option('lumise_editor_page'));
	if($lumiseEdtPage > 0 && is_page($lumiseEdtPage)){
		if(isset($_GET['tkawsd2c']) && intval($_GET['tkawsd2c']) == 1){
			echo '<link rel="stylesheet" '.
							'href="'.trailingslashit(
										get_stylesheet_directory_uri()
									).'aws'.DS.'includes'.DS.
									'lumise-bulk-ordering-flow'.DS.
									'assets'.DS.'css'.DS.
									'bulk-order-a2c.css?v='.
									filemtime(__DIR__ . '/assets/scripts/bulk-order-a2c.css').'" '.
							' id="lmsedtrpage-blkordr-a2c-style"'.
					' />'."\n".
					'<script type="text/javascript" '.
							'src="'.trailingslashit(
										get_stylesheet_directory_uri()
									).'aws'.DS.'includes'.DS.
									'lumise-bulk-ordering-flow'.DS.
									'assets'.DS.'scripts'.DS.
									'bulk-order-a2c.js?v='.
									filemtime(__DIR__ . '/assets/scripts/bulk-order-a2c.js').
								'" '.
							'id="lmsedtrpage-blkordr-a2c-script"'.
					'></script>'."\n".
					'<!--<div class="loader"><svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve"><path fill="#000" d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">          <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform></path></svg></div>-->';
		}
	}
},999999999);
