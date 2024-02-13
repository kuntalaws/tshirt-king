<!-- Start :: <?=str_replace(get_stylesheet_directory(),'theme',dirname(__FILE__).DS.__FILE__)?> -->
<style>
    .category-list li.initially-hidden{
        display:none;
    }
</style>
<div class="template-wrapper">
    <div class="template-wrapper-in">
    <?php
	$categories = tkawsGetLumiseTemplateCategories(0);
	if($categories){
	?>
        <h2>WHAT KIND OF DESIGNS ARE YOU LOOKING FOR?</h2>
        <ul class="category-list">
        <?php 
        $countTmplCats = 1;
        foreach($categories as $category){
        	$liclass = 'category-item ';
			if($category['has_subcat'] == 1){
				$liclass .= 'has-sub-category ';
			}
			if($countTmplCats > 8){
				$liclass .= 'initially-hidden ';
			}
        ?>
            <li class="<?=$liclass?>">
                <a href="#" data-id="<?=$category['id']?>" data-slug="<?=$category['slug']?>">
                    <span class="category"><?=wp_specialchars(stripslashes($category['name']));?></span>
                    <div class="image-wrap">
                    	<img src="<?=$category['thumbnail_url']?>" alt="<?=$category['slug']?>">
                    </div>
                </a>
            </li>
        <?php 
        	$countTmplCats++;
        }
        ?>          
        </ul>
        <?php if(count($categories) > 8){?>
			<button class="see-more-design"><span>See More Designs</span></button>
		<?php }?>
    <?php 
    }else{
    ?>
    	<h2>Please try again latter</h2>
    	<p class="category-list not-found">Sorry, no Template Categories are available at this moment.</p>
    <?php
	}
    ?>
    </div>
</div>