<?php
function tkawsGetDesignStagesByProductID($product_id){
	global $lumise;
	$return = false;
	$data = $lumise->db->rawQuery("SELECT `stages` FROM `{$lumise->db->prefix}products` WHERE `author`='{$lumise->vendor_id}' AND `product`=$product_id");
	if (isset($data[0]) && isset($data[0]['stages'])) {
		$return = urldecode(base64_decode($data[0]['stages']));
		if(!empty($return) && is_string($return)){
			$return = json_decode($return); 
			if(json_last_error() !== JSON_ERROR_NONE){
				$return = false;
			}else if(!is_object($return)){
				$return = false;
			}
		}
	}
	return $return;
}

function tkawsGetProductsByTag($tag){
	$shares = array();
	$products = new WP_Query(
						array(
							'post_type'=>'product',
							'posts_per_page'=>-1,
							'tax_query'=>array(
											'relation'=>'AND',
											array(
												'taxonomy' => 'product_tag',
												'field' => 'slug',
												'terms' => $tag,
											)
										),
							'orderby'=>'menu_order',
							'order'=>'DESC'
						)
					);
					
	if($products->have_posts()){
		while($products->have_posts()){
			$products->the_post();
			$shares[] = tkawsLumiseCreateShare(get_the_ID(),false,false,false);
		}
	}
	wp_reset_query();
	if(count($shares) <= 0){
		$shares = false;
	}
	
	return $shares;
}

function tkawsGetSampleLumiFileData($product_id){
	$return = false;
	$sampleLumiFile = __DIR__.DS.
						 'lumise-sample-shares'.DS.
						 'lumise-sample-share-product-'.$product_id.'.lumi';
						 
	if(file_exists($sampleLumiFile)){
		$sampleLumiFile = file_get_contents($sampleLumiFile);
		$lfc = json_decode($sampleLumiFile,true);
		if(is_array($lfc) && isset($lfc['stages'])){
			foreach($lfc['stages'] as $ke => $stg){
				if(is_array($stg['data']['objects']) && count($stg['data']['objects']) > 0){
					foreach($stg['data']['objects'] as $objk => $v){
						$dataUri = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+P+/HgAFhAJ/wlseKgAAAABJRU5ErkJggg==';
						if(isset($v['src'])){								
							$lfc['stages'][$ke]['data']['objects'][$objk]['src'] = $dataUri;
						}
						if(isset($v['full_src'])){
							$lfc['stages'][$ke]['data']['objects'][$objk]['full_src'] = $dataUri;
						}							
					}
				}
				if(isset($stg['screenshot'])){
					$lfc['stages'][$ke]['data']['screenshot'] = $dataUri;
				}
			}
		}
		$return = $lfc;
	}
	return $return;
}


function tkawsGetProductColorsByTag($tag){
	global $lumise;
	$return = false;
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
		$return = array();
		while($products->have_posts()){
			$products->the_post();
			$product_id = get_the_ID();
			$data = $lumise->db->rawQuery("SELECT `attributes` FROM `{$lumise->db->prefix}products` WHERE `author`='{$lumise->vendor_id}' AND `product`=$product_id");
			
			if (isset($data[0]) && isset($data[0]['attributes'])) {
				$attributes = urldecode(base64_decode($data[0]['attributes']));
				if(!empty($attributes) && is_string($attributes)){
					$attributes = json_decode($attributes); 
					if(json_last_error() !== JSON_ERROR_NONE){
						$attributes = false;
					}else if(!is_object($attributes)){
						$attributes = false;
					}
					if($attributes){
						if(is_object($attributes) && @count($attributes) > 0){
							foreach($attributes as $attr){
								if(
									is_object($attr) 
										&& 
									isset($attr->type) 
										&& 
									(
										$attr->type == 'color'
											||
										$attr->type == 'product_color'
									)
										&&
									isset($attr->values->options)
										&&
									count($attr->values->options) > 0
								){
									foreach($attr->values->options as $clr){
										$clrkey = str_replace('#','HASH',$clr->value);
										$return['options'][$clrkey] = array(
											'title' => $clr->title,
											'code' => $clr->value,
										);
										if(intval($clr->default) == 1){
											$return['default'] = array(
												'title' => $clr->title,
												'code' => $clr->value,
											);
										}
									}
								}
							}
						}
					}					
				}
			}
			
			
		}
	}
	wp_reset_query();
	
	if(@count($return) <= 0){
		$return = false;
	}else if(!isset($return['default'])){
		$return['default'] = array('title'=>'White','code'=>'#FFFFFF');
	}
	
	return $return;
}

function tkawsGetFullTemplateCatHierarchy($startCat,$return = array()){
	$return[] = $startCat;
	$children = tkawsGetLumiseTemplateCategories($startCat);
	if($children){
		foreach($children as $child){
			$return = tkawsGetFullTemplateCatHierarchy($child['id'],$return);
		}
	}
	return $return;
}

function tkawsGetLumiseTemplateCategories($parent = 0){
	global $lumise,$wpdb;
	
	$return = false;
	
	/*$sql = "SELECT DISTINCT c.* FROM `".$lumise->db->prefix."categories` c,`".$lumise->db->prefix."categories` calt, `".$lumise->db->prefix."categories_reference` cr, `".$lumise->db->prefix."templates` t WHERE c.active=1 AND c.parent=".$parent." AND c.type='templates' AND ((c.id=cr.category_id AND cr.item_id=t.id AND t.active=1) OR (calt.parent=c.id AND calt.type='templates' AND calt.active=1))";*/
	
	$sql = "SELECT DISTINCT * FROM `".$lumise->db->prefix."categories` WHERE `active`=1 AND `parent`=".$parent." AND `type`='templates' ORDER BY `order` ASC";
	$data = $lumise->db->rawQuery($sql);
	

	if(is_array($data) && count($data) > 0){
		foreach($data as $k => $cat){
			if(empty($cat['thumbnail_url'])){
				$data[$k]['thumbnail_url'] = $lumise->cfg->assets_url . 'assets/images/img-none.png';
			}			
			$hierarchy = tkawsGetFullTemplateCatHierarchy($cat['id']);
			
			if(is_array($hierarchy) && count($hierarchy) > 1){
				$data[$k]['has_subcat'] = 1;
			}else{
				$data[$k]['has_subcat'] = 0;
				$hasActiveTemplates = tkawsGetLumiseTemplatesByCats(array($cat['id']));
				if(!$hasActiveTemplates){
					unset($data[$k]);
				}
			}
		}
		$return = $data;
	}
	
	return $return;
}

function tkawsGetLumiseTemplatesByCats($cats = array()){
	global $lumise;
	
	$return = false;
	
	if(is_array($cats) && count($cats) > 0){
		$cats = implode(', ',$cats);
		
		$sql = "SELECT DISTINCT t.* FROM `{$lumise->db->prefix}templates` t, `{$lumise->db->prefix}categories_reference` cr  WHERE t.active=1 AND t.id=cr.item_id AND cr.category_id IN (".$cats.") AND cr.type='templates' ORDER BY t.order DESC";
		
		$data = $lumise->db->rawQuery($sql);
		
		if(is_array($data) && count($data) > 0){
			foreach($data as $k => $tmpl){
				$catsql = "SELECT DISTINCT c.* FROM `{$lumise->db->prefix}categories` c, `{$lumise->db->prefix}categories_reference` cr  WHERE c.active=1 AND c.id=cr.category_id AND cr.item_id=".$tmpl['id']." AND cr.type='templates' ORDER BY cr.id DESC";
				$assoccats = $lumise->db->rawQuery($catsql);
				if(is_array($assoccats) && count($assoccats) > 0){
					$data[$k]['category'] = $assoccats[0]['id'];
				}else{
					$data[$k]['category'] = 0;
				}
			}
			
			$return = $data;
		}
	}
	return $return;
}

function tkawsGetProductStageImage($stageslug,$stages){
	global $lumise;
	$return = false;
	$stages = json_encode($stages);
	$stages = json_decode($stages,true);
	
	if(isset($stages[$stageslug]['source']) && $stages[$stageslug]['source'] == 'raws'){
		$stgImgUrl = $lumise->cfg->assets_url.'assets/raws/';			
	}else{
		$stgImgUrl = $lumise->cfg->upload_url;
	}
	$stgImgUrl .= $stages[$stageslug]['url'];
	
	$ch = curl_init($stgImgUrl);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 2);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
	curl_exec($ch);
	if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200){
	    $return = $stgImgUrl;
	}
	curl_close($ch);
	
	return $return;
}

function tkawsCreateBulkDesignInstance(){
	$return = false;
	
	$uniqID = uniqid();
	$userID = WC()->session->generate_customer_id();
	
	$bdInstID = crypt($uniqID,$userID);
	$bdInstVl = array('instance'=>$bdInstID,'user_session_id'=>$userID);
	
	if(is_user_logged_in()){
		$bdInstVl['user_id'] = $userID;
	}else{
		$bdInstVl['user_id'] = 0;
	}
	
	if(set_transient('tkaws_bulk_design_instance_'.$bdInstID,$bdInstVl,3600)){
		$return = urlencode($bdInstID);
	}
	
	if(!$return){
		$return = tkawsCreateBulkDesignInstance();
	}
	
	return $return;
}

function tkawsCheckBulkDesignInstance($designInstance = false){
	$return = false;
	
	if($designInstance){
		$designInstance = urldecode($designInstance);
		$transientBDI = get_transient('tkaws_bulk_design_instance_'.$designInstance);
		if($transientBDI){
			$return = $transientBDI;
		}
	}
	
	return $return;
}
