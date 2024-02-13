<!-- Start :: <?=str_replace(get_stylesheet_directory(),'theme',dirname(__FILE__)).DS.__FILE__?> -->
<div class="logo-color-popup">
	<div class="logo-color-popup-in">
		<div class="popup-logo-title-wrap">
			<div class="popup-logo-title-wrap-in">
				<div class="popup-logo"><img src="/wp-content/uploads/2024/02/91fb2112b754bf32-1.png" alt=""></div>
				<h6>ADD YOUR LOGO<?php if($productColors){?> AND PICK YOUR COLOURS<?php }?></h6>
			</div>
		</div>
		<div class="logo-color-wrap">
			<div class="logo-wrap">
				<span>SELECT LOGO FROM FILE OR PASTE HERE</span>
				<div class="upload">
					<input id="upload-logo" type="file" name="upload-logo">
					<img id="uploaded-logo" src="<?php echo trailingslashit(
						get_stylesheet_directory_uri()
					);?>aws/includes/lumise-bulk-ordering-flow/assets/images/upload-icon.svg" data-default-icon="<?php echo trailingslashit(
						get_stylesheet_directory_uri()
					);?>aws/includes/lumise-bulk-ordering-flow/assets/images/upload-icon.svg" alt="Upload Your Logo">
				</div>
			</div>
			<?php if($productColors){?>
			<div class="color-wrap">
				<span>SELECT YOUR PREFERRED COLOURS</span>
				<div class="color-group-wrap">

					<ul class="color-group">
						<li data-color="<?=$productColors['default']['code']?>"></li>
						<?php foreach($productColors['options'] as $optn){?>
						<li data-color="<?=$optn['code']?>" style="background-color:<?=$optn['code']?>;"></li>
						<?php }?>                      
					</ul>
				</div>
			</div>
			<?php }?>
		</div>
		<button class="all-done"><span>ALL DONE</span></button>
		<a href="#" class="skip-step">SKIP THIS STEP</a>
	</div>
</div>
