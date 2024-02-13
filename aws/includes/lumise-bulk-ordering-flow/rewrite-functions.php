<?php
$tkawsBulkDesignPageIDs = array();
$tkawsBulkDsgnSteps = array(
						'upload-your-logo',
						'choose-a-template',
						'select-a-product',
						'select-a-variation',
						'choose-from-designs'
					  );


add_action(
	'init',
	function(){
		global $tkawsBulkDesignPageIDs;
		add_rewrite_tag('%tkawsblkdsgnstep%', '([^&]+)');
		add_rewrite_tag('%tkawsblkdsgninst%', '([^&]+)');

		$bulkDesignPages = new WP_Query(
									array(
										'post_type'		=> 'page',
										'post_status'	=> 'publish',
										'posts_per_page'=> -1,
										's'				=> '[tkaws_product_design_canvases '
									)
								);
		if($bulkDesignPages->have_posts()){
			$bulkDesignPageIDs = array();
			while($bulkDesignPages->have_posts()){
				$bulkDesignPages->the_post();
				$bulkDesignPageID 			= get_the_ID();
				$bulkDesignPageContent  	= get_the_content();
				$tkawsBulkDesignPageIDs[]	= $bulkDesignPageID;
				
				if(has_shortcode($bulkDesignPageContent,'tkaws_product_design_canvases')){
					$bulkDesignPagePath = get_permalink($bulkDesignPageID);
					$bulkDesignPagePath = str_replace(
										        trailingslashit(get_option('home')),
										        '', 
										        $bulkDesignPagePath
										   );
					
					add_rewrite_rule(
						'^'.trailingslashit($bulkDesignPagePath).'step/([^/]*)/instance/([^/]*)/?$', 
						'index.php?page_id='.$bulkDesignPageID.
						'&tkawsblkdsgnstep=$matches[1]'.
						'&tkawsblkdsgninst=$matches[2]', 
						'top'
					);
				}				
			}
			flush_rewrite_rules();
		}
		wp_reset_query();
		if(count($tkawsBulkDesignPageIDs) > 0){
			add_filter(
				'permalink_manager_excluded_post_ids',
				function($excluded_ids){
					global $tkawsBulkDesignPageIDs;
					$excluded_ids = array_merge(
										$excluded_ids,
										$tkawsBulkDesignPageIDs
									  );
					return $excluded_ids;
				},
				99999
			);
		}
	},
	99999
);

add_filter( 
	'template_include',
	function($template){
		global $post, $tkawsBulkDesignPageIDs, $tkawsBulkDsgnSteps;
		
		if(in_array($post->ID,$tkawsBulkDesignPageIDs)){
			$redirect = false;
		
			$designStep 	= get_query_var('tkawsblkdsgnstep',false);
			$designInstance = get_query_var('tkawsblkdsgninst',false);
			if(!in_array($designStep,$tkawsBulkDsgnSteps)){
				$designStep = $tkawsBulkDsgnSteps[0];
				$redirect = trailingslashit(get_permalink($post->ID));			
			}
			if(!tkawsCheckBulkDesignInstance($designInstance)){
				$designInstance = tkawsCreateBulkDesignInstance();
				$redirect = trailingslashit(get_permalink($post->ID));
			}
			
			if($redirect){
				$redirect .= 'step/'.$designStep.'/instance/'.$designInstance.'/';
				wp_safe_redirect($redirect);
				exit;
			}
		}		
		
		return $template;
	}, 
	99999,
	2
);

unset($tkawsBulkDesignPageIDs);
