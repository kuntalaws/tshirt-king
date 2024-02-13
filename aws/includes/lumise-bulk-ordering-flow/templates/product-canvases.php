<!-- PATH :: <?=str_replace(get_stylesheet_directory(),'theme',dirname(__FILE__).DS.__FILE__)?> -->
<div class="canvas-product-wrap">
<?php
$count = 1;
while($products->have_posts()){
	$products->the_post();
	$dsgnStgCnt = 0;
	$designStages = tkawsGetDesignStagesByProductID(get_the_ID());
	$lumifiledata = tkawsGetSampleLumiFileData(get_the_ID());
	if($designStages){	
?>
		<!-- Start :: Canvas Dats for Product <?=wp_specialchars(get_the_title())?> (ID : <?=get_the_ID()?>) -->
		<script>
			tkawslumifilesdata.push(<?php echo json_encode($lumifiledata); ?>);
		</script>
<?php 
		foreach($designStages as $key => $val){
			if($dsgnStgCnt == 0 && is_object($val) && isset($val->url)){
				$tkAwsStageImg = tkawsGetProductStageImage($key,$designStages);
				
				if($tkAwsStageImg){
					$val->url = $tkAwsStageImg;
					$val->title = wp_specialchars(stripslashes(get_the_title()));
					$canvasImg = imagecreatefromstring(file_get_contents($val->url));
					$val->woo_prd_id = get_the_ID(); 
					$val->base_prd_id = intval(get_post_meta(get_the_ID(),'lumise_product_base',true));
					/*$val->canvas_width = imagesx($canvasImg);
					$val->canvas_height = imagesy($canvasImg);*/
					
					$val->img_width = imagesx($canvasImg);
					$val->img_height = imagesy($canvasImg);
					$val->canvas_width = $val->product_width;
					$val->canvas_height = $val->product_height;
					
					/*$ph = $val->canvas_height*0.9;
					$pw = ($val->img_width*($val->canvas_height/$val->img_height))*0.9;
					
					if($val->img_height <= $ph) {
						$ph = $val->img_height;
						$pw = $val->img_width;
					};
					
					$ez_ratio = $val->img_width ? $pw/$val->img_width : 1;
					$val->edit_zone = (object) array(
						'width'		=> ($val->edit_zone->width)*$ez_ratio,
						'height'	=> ($val->edit_zone->height)*$ez_ratio,
						'top'		=> ($val->edit_zone->top*$ez_ratio)
											+
									   (
									   		(($val->canvas_height-40)/2)
									   			-
									   		($ph/2)
									   ),
						'left'=> ($val->edit_zone->left)*$ez_ratio,
						'radius'=>$val->edit_zone->radius
					);*/
					
					
					$ph = $val->canvas_height*0.9;
					$pw = ($val->product_width*($val->canvas_height/$val->product_height))*0.9;

					if ($val->product_height <= $val->canvas_height*0.9) {
						$ph = $val->product_height;
						$pw = $val->product_width;
					};
					
					$ez_ratio = $val->product_width ? $pw/$val->product_width : 1;
					$val->edit_zone_calc = (object) array(
						'width'		=> $val->edit_zone->width*$ez_ratio,
						'height'	=> $val->edit_zone->height*$ez_ratio,
						'top'		=> ($val->edit_zone->top*$ez_ratio)+((($val->canvas_height-40)/2)-($ph/2)),
						'left'		=> $val->edit_zone->left*$ez_ratio,
						'ph'		=> $ph,
						'pw'		=> $pw
					);
					

					imagedestroy($canvasImg);
?>
		<div class="canvas-outer">
			<script>tkawsprdcanvasdata.push(<?php echo json_encode($val); ?>);</script>
			<canvas id="tshirt-canvas-<?php echo $count; ?>" width="<?=$val->canvas_width?>" height="<?=$val->canvas_height?>"></canvas>
		</div>
<?php						
					$dsgnStgCnt++;	
					$count++;
				}			
			}
			
		}
?>
		<!-- End :: Canvas Dats for Product <?=wp_specialchars(get_the_title())?> (ID : <?=get_the_ID()?>) -->
<?php
		echo "\r\n";
	}
}
?>
</div>
