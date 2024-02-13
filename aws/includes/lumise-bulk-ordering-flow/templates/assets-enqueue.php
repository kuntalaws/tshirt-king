<!-- Start :: <?=str_replace(get_stylesheet_directory(),'theme',dirname(__FILE__).DS.__FILE__)?> -->
<?php
global $lumise;
wp_enqueue_style( 
	'aws-lumise-custom-style', 
	trailingslashit(
		get_stylesheet_directory_uri()
	).
	'aws'.DS.'includes'.DS.
	'lumise-bulk-ordering-flow'.DS.
	'assets'.DS.'css'.DS.'style.css', 
	false, 
	filemtime(__DIR__ . '/assets/css/style.css'), 
	false 
);

wp_enqueue_script( 
	'aws-lumise-custom-script-fabric',
	'https://cdnjs.cloudflare.com/ajax/libs/fabric.js/3.6.3/fabric.min.js',
	false,
	filemtime(__DIR__ . '/assets/scripts/scripts.js'),
	array('in_footer'=>true)
);

wp_enqueue_script( 
	'aws-lumise-custom-script',
	trailingslashit(
		get_stylesheet_directory_uri()
	).
	'aws'.DS.'includes'.DS.
	'lumise-bulk-ordering-flow'.DS.
	'assets'.DS.'scripts'.DS.'scripts.js',
	array('aws-lumise-custom-script-fabric'),
	filemtime(__DIR__ . '/assets/scripts/scripts.js'),
	array('in_footer'=>true)
);

$lumiseCfgSettings = $lumise->cfg->settings;
$lumiseCfgSettings['file_type']='png';

if(is_array($lumiseCfgSettings) && count($lumiseCfgSettings)){
	
	$lumiseAllowedCfgs = array(
							'file_type',
							'min_upload',
							'max_upload',
							'min_dimensions',
							'max_dimensions',
							'min_ppi',
							'max_ppi'
						);
	//$lumiseAllowedCfgs = array('file_type');
	foreach($lumiseCfgSettings as $k => $v){
		if(!in_array($k,$lumiseAllowedCfgs)){
			unset($lumiseCfgSettings[$k]);
		}else{
			$message = '';
			switch($k){
				case "file_type":
					$message = str_replace('image', strtoupper($v).' image',$lumise->cfg->js_lang[148]).'.';
					break;
				case "min_upload":
					$message = $lumise->cfg->js_lang[197].', minimum upload size is '.floatval($v).' KB.';
					break;
				case "max_upload":
					$message = $lumise->cfg->js_lang[53].', maximum upload size is '.floatval($v).' KB.';
					break;
				case "min_dimensions":
					$message = $lumise->cfg->js_lang[197].', minimum allowed dimension is '.$v.' PX.';
					break;
				case "max_dimensions":
					$message = $lumise->cfg->js_lang[53].', maximum allowed dimension is '.$v.' PX.';
					break;
				case "min_ppi":
					$message = $lumise->cfg->js_lang[194].' is '.$v.'.';
					break;
				case "max_ppi":
					$message = $lumise->cfg->js_lang[53].', maximum PPI requirement is '.floatval($v).'.';
					break;
			}
			if(empty($v)){
				$v = 0;
			}
			$lumiseCfgSettings[$k] = array('limit'=>$v,'message'=>$message);
		}
	}
}
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
<script>
var tkawslumiseconfigdata = <?php echo json_encode($lumiseCfgSettings);?>;
var tkawsprdcanvasdata = [];
var tkawslumifilesdata = [];
var tkawsLumiseCstmBlkOrdrLogoSize;
var tkawsLumiseCstmBlkOrdrLogoType;
var tkawsLumiseCstmBlkOrdrLogoMnW;
var tkawsLumiseCstmBlkOrdrLogoMnH;
var tkawsLumiseCstmBlkOrdrLogoMxW;
var tkawsLumiseCstmBlkOrdrLogoMxH;
var tkawsLumiseCstmBlkOrdrLogoValidated = true;
</script>
