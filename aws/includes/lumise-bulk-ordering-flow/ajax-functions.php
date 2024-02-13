<?php
add_action('wp_ajax_tkawsLumiseCreateShare','tkawsLumiseCreateShare');
add_action('wp_ajax_nopriv_tkawsLumiseCreateShare','tkawsLumiseCreateShare');
function tkawsLumiseCreateShare(){
	global $lumise;	

	$return = array('response'=>'error','message'=>'somthing went wrong, please try again later','target'=>'#');
	
	$product_id = intval($_POST['prdId']);
	$product_color = trim($_POST['prdColor']);
	$previewimg = trim(urldecode($_POST['previewimg']));
	$previewjsn = trim(urldecode($_POST['previewjsn']));
	if($product_id > 0 && get_post_type($product_id) == 'product' && !empty($previewimg) && !empty($previewjsn)){
		$hist = $lumise->connector->get_session('share-design');
		if ($hist === null)
			$hist = 0;
			
		$check_share  =  $lumise->apply_filters('verify-share',$hist);
		$lumise->connector->set_session('share-design', $hist+1);
		$shareid = $lumise->generate_id();
		
		$path = $lumise->cfg->upload_path.'shares'.DS.date('Y').DS.date('m').DS;
		
		$_lumifile = file_put_contents($path.$shareid.'.lumi',$previewjsn);

		$base64string = $previewimg;
		$parts        = explode(";base64,", $base64string);
		$imageparts   = explode("image/", @$parts[0]);
		$imagetype    = $imageparts[1];
		$imagebase64  = base64_decode($parts[1]);		

		$_preview = file_put_contents($path.$shareid.'.jpg',$imagebase64);
		
		if($_lumifile && $_preview){
			$product_base = intval(get_post_meta($product_id,'lumise_product_base',true));
			if($product_base > 0){
				$data = array(
							'name' => 'Template - Product : '.get_the_title($product_id),
							'aid' => $lumise->connector->cookie('lumise-AID'),
							'share_id' => $shareid,
							'product' => $product_base,
							'product_cms' => $product_id,
							'view' => 0,
							'author' => $lumise->vendor_id,
							'active' => 1,
							'created' => date("Y-m-d").' '.date("H:i:s")
						);
				$data = $lumise->apply_filters('new-section',$data, 'shares');
				$insert = $lumise->db->insert('shares', $data);
				$shareUrl = $lumise->cfg->tool_url;
				$shareUrl = add_query_arg(
					array(
						'product_base'=>$product_base,
						'product_cms'=>$product_id,
						'color'=>str_replace('#','%23',$product_color),
						'share'=>$shareid
					),
					$shareUrl
				);
				$return = array('response'=>'success','message'=>'','target'=>$shareUrl);
			}
		}
	}
	echo json_encode($return);
	die();	
}

add_action('wp_ajax_tkawsLumiseTemplateCatSubCat','tkawsLumiseTemplateCatSubCat');
add_action('wp_ajax_nopriv_tkawsLumiseTemplateCatSubCat','tkawsLumiseTemplateCatSubCat');
function tkawsLumiseTemplateCatSubCat(){
	global $lumise;	
	$category = $_REQUEST['category'];
	$return = tkawsGetLumiseTemplateCategories($category);
	if($return){
		foreach($return as $key=>$val){
			$return[$key]['name'] = wp_specialchars(stripslashes($val['name'])); 
		}
		echo json_encode(array('response'=>'success','categories'=>$return));
	}else{
		echo json_encode(array('response'=>'error','categories'=>false));
	}
	die();
}

add_action('wp_ajax_tkawsLumiseCatToTemplates','tkawsLumiseCatToTemplates');
add_action('wp_ajax_nopriv_tkawsLumiseCatToTemplates','tkawsLumiseCatToTemplates');
function tkawsLumiseCatToTemplates(){
	global $lumise;	
	$category = $_REQUEST['category'];
	$hierarchy = tkawsGetFullTemplateCatHierarchy($category);
	$return = tkawsGetLumiseTemplatesByCats($hierarchy);
	if($return){
		foreach($return as $key=>$val){
			$return[$key]['name'] = wp_specialchars(stripslashes($val['name'])); 
		}		
		echo json_encode(array('response'=>'success','templates'=>$return));
	}else{
		echo json_encode(array('response'=>'error','templates'=>false));
	}
	die();
}

add_action('wp_ajax_tkawsLumiseTemplateSS','tkawsLumiseTemplateSS');
add_action('wp_ajax_nopriv_tkawsLumiseTemplateSS','tkawsLumiseTemplateSS');
function tkawsLumiseTemplateSS(){
	global $lumise;	
	$ssurl = $_REQUEST['ssurl'];
	$return = array(
				'response'=>'success',
				'message'=>'Screenshot Retrieved',
				'base64img'=>''
			  );
	try{
    	$return['base64img'] = file_get_contents($ssurl);
	}catch (Exception $e) {
	    $return['message'] = $e->getMessage();
	    $return['response'] = 'error';
	}
	
	if(!empty($return['base64img'])){
		$type = pathinfo($ssurl, PATHINFO_EXTENSION);
		$return['base64img'] = urlencode(
								'data:image/'.$type.';base64,'.
								base64_encode($return['base64img'])
							   );
	}
	
	echo json_encode($return);
	
	die();
}