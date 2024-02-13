console.log(tkawsprdcanvasdata);
			
var canvases = [];
var screenshots = [];
for (let i = 0; i < tkawsprdcanvasdata.length; i++) { // loop through the data array
	var j = (i + 1);
	canvases[i] = new fabric.Canvas('tshirt-canvas-'+j);
	var result = tkawsprdcanvasdata[i];
	var ratio = result.product_height/result.canvas_height;

	
	canvases[i].setWidth(result.canvas_width);
	canvases[i].setHeight(result.canvas_height);
	
	canvases[i].setOverlayImage(result.url, function() {
		canvases[i].requestRenderAll();
		canvases[i].overlayImage.scaleToWidth(result.canvas_width);
		canvases[i].overlayImage.backgroundColor = '';
	});
	
	/*var ph = result.canvas_height*0.9,
	pw = (result.product_width*(result.canvas_height/result.product_height))*0.9;

	if (result.product_height <= result.canvas_height*0.9) {
		ph = result.product_height;
		pw = result.product_width;
	};
	
	var ez_ratio = result.product_width ? pw/result.product_width : 1;
	var editing = {
		width: result.edit_zone.width*ez_ratio,
		height:  result.edit_zone.height*ez_ratio,
		top: (result.edit_zone.top*ez_ratio)+(((result.canvas_height-40)/2)-(ph/2)),
		left: result.edit_zone.left*ez_ratio
	};


	console.log('js-editing-var',editing);*/
	console.log('php-editing-var',result.edit_zone_calc);
	
	
	
	var rectangle = new fabric.Rect({ 
		height: result.edit_zone_calc.height,
		width: result.edit_zone_calc.width,
		selectable: false,
		evented: false,
		//visible: false,
		radius: result.edit_zone_calc.radius,					
		rx: result.edit_zone_calc.radius,
		ry: result.edit_zone_calc.radius,
		originX:'left',
		originY:'top',
		left: ((result.canvas_width/2)+result.edit_zone_calc.left) - (result.edit_zone_calc.width/2),
		top: (((result.edit_zone_calc.ph/2)+result.edit_zone_calc.top) - (result.edit_zone_calc.height/2)),
		hasControls:false,
		lockMovementX:true,
		lockMovementY:true,
		fill: 'transparent', 
		stroke: 'transparent', 
		strokeWidth: 0
	});
	console.log('logo left',((result.canvas_width/2)+result.edit_zone_calc.left) - (result.edit_zone_calc.width/2));
	canvases[i].add(rectangle);
	jQuery('#upload-logo').on('change',function(event){
		/* if(canvases[i].getActiveObject())
		{
			canvases[i].remove(canvases[i].getActiveObject());
		} */	
		var logoVal = URL.createObjectURL(event.target.files[0]);
		jQuery('#uploaded-logo').attr('src',logoVal);

		var reader = new FileReader();
		tkawsLumiseCstmBlkOrdrLogoSize = event.target.files[0].size;
		tkawsLumiseCstmBlkOrdrLogoType = event.target.files[0].type;
		reader.onload = function (event){
			var imgObj = new Image();
			imgObj.src = event.target.result;
			// When the picture loads, create the image in Fabric.js
			imgObj.onload = function () {
				if(tkawsLumiseCstmBlkOrdrLogoValidated){
					console.log(tkawsprdcanvasdata[i].edit_zone_calc);
					/* var img = new fabric.Image(imgObj,{
						left: ((canvases[i]._objects[0].width/2)+canvases[i]._objects[0].left) - ( canvases[i]._objects[0].width/2) + 5,
						top: (canvases[i]._objects[0].top) + (canvases[i]._objects[0].height - canvases[i]._objects[0].width) + 10,
					}); */
					var logoPlacementX = ((tkawsprdcanvasdata[i].product_width/2)+tkawsprdcanvasdata[i].edit_zone_calc.left) - ( tkawsprdcanvasdata[i].edit_zone_calc.width/2) + 5;
						//logoPlacementX = logoPlacementX - (imgObj.width/2);
					var editZnImgWidthDiff = tkawsprdcanvasdata[i].edit_zone_calc.width - canvases[i]._objects[0].width;
					if(editZnImgWidthDiff > 0){
						logoPlacementX = logoPlacementX + (editZnImgWidthDiff/2);
					}
					var logoPlacementY = (((tkawsprdcanvasdata[i].edit_zone_calc.ph/2)+tkawsprdcanvasdata[i].edit_zone_calc.top) - (tkawsprdcanvasdata[i].edit_zone_calc.height/2));
					var editZnImgHeightDiff = tkawsprdcanvasdata[i].edit_zone_calc.height - parseFloat(imgObj.height/imgObj.width*tkawsprdcanvasdata[i].edit_zone_calc.width);
					if(editZnImgHeightDiff > 0){
						logoPlacementY = logoPlacementY + (editZnImgHeightDiff/2);
					}
					logoPlacementY = logoPlacementY + 5;
					//alert('editzoneheight:'+tkawsprdcanvasdata[i].edit_zone_calc.height+' diff:'+editZnImgHeightDiff);
					var img = new fabric.Image(imgObj,{
						left: logoPlacementX,
						top: logoPlacementY,
					});							
					//img.scaleToHeight(parseInt((canvases[i]._objects[0].width - 10)));
					img.scaleToWidth(parseInt(tkawsprdcanvasdata[i].edit_zone_calc.width) - 10); 
					//canvases[i].centerObject(img);
					canvases[i].setActiveObject(canvases[i].item(0));
					//canvases[i].remove(canvases[i].getActiveObject());						
					canvases[i].add(img);
					canvases[i].renderAll();
					screenshots[i] = canvases[i].toDataURL({format: 'png'});
					
					var countStgCntr = 0;
					for (var key in tkawslumifilesdata[i].stages) {
						if(countStgCntr == 0){
							tkawslumifilesdata[i].stages[key].data.objects.forEach(function(item,index){
								if(item !=null){
								var	cv = document.createElement('canvas'),
									ctx = cv.getContext('2d'),
									time = new Date().getTime();

								
								cv.width = tkawsprdcanvasdata[i].edit_zone_calc.width;
								cv.height = ((imgObj.height/imgObj.width)*cv.width);

								ctx.drawImage(imgObj, 0, 0, cv.width, cv.height);
								var logoScaledV = cv.toDataURL('image/'+(imgObj.src.indexOf('.png') > -1 ? 'png' : 'png'));

								tkawslumifilesdata[i].stages[key].data.objects[index].src = logoScaledV;
								tkawslumifilesdata[i].stages[key].data.objects[index].full_src = imgObj.src;
								delete logoScaledV;
								delete cv;
								delete ctx;

								}
							})
						}
						countStgCntr++;
					}							
				}
			};
		};					

		// If the user selected a picture, load it
		if(event.target.files[0]){
			var defaultUploadIcon = jQuery('#uploaded-logo').data('default-icon');
			tkawsLumiseCstmBlkOrdrLogoValidated = true;
			for (var index in tkawslumiseconfigdata){
				item = tkawslumiseconfigdata[index];
				/* if(index == 'file_type'){
					if (tkawsLumiseCstmBlkOrdrLogoType != 'image/png'){
						tkawsLumiseCstmBlkOrdrLogoValidated = false;
						jQuery('.massage-popup .massage-popup-in').html(item.message);
						jQuery('.massage-popup').addClass('massage-popup-show');
						jQuery('#uploaded-logo').attr('src',defaultUploadIcon);						
					}
				}else  */if(index == 'min_upload'){
					if(tkawsLumiseCstmBlkOrdrLogoSize < item.limit){
						tkawsLumiseCstmBlkOrdrLogoValidated = false;
						jQuery('.massage-popup .massage-popup-in').html(item.message);
						jQuery('.massage-popup').addClass('massage-popup-show');
						jQuery('#uploaded-logo').attr('src',defaultUploadIcon);						
					}
				}else if(index == 'max_upload'){
					if(tkawsLumiseCstmBlkOrdrLogoSize > item.limit){
						tkawsLumiseCstmBlkOrdrLogoValidated = false;
						jQuery('.massage-popup .massage-popup-in').html(item.message);
						jQuery('.massage-popup').addClass('massage-popup-show');
						jQuery('#uploaded-logo').attr('src',defaultUploadIcon);						
					}
				}else if(index == 'min_dimensions'){
					if(item.limit !==0){
							var imbObjMinDim = new Image();
							var minLogoImg = URL.createObjectURL(event.target.files[0]);
							imbObjMinDim.src = minLogoImg;

							var dimexp = item.limit.split('x');
							if(typeof dimexp == 'object' && dimexp.length == 2){
								tkawsLumiseCstmBlkOrdrLogoMnW = parseFloat(dimexp[0]);
								tkawsLumiseCstmBlkOrdrLogoMnH = parseFloat(dimexp[1]);
								imbObjMinDim.onload = function () {
									var imgW = imbObjMinDim.width;
									var imgH = imbObjMinDim.height;
									if(imgW < tkawsLumiseCstmBlkOrdrLogoMnW || imgH < tkawsLumiseCstmBlkOrdrLogoMnH){
										tkawsLumiseCstmBlkOrdrLogoValidated = false;
										jQuery('.massage-popup .massage-popup-in').html(tkawslumiseconfigdata.min_dimensions.message);
										jQuery('.massage-popup').addClass('massage-popup-show');
										var defaultUploadIcon = jQuery('#uploaded-logo').data('default-icon');
										jQuery('#uploaded-logo').attr('src',defaultUploadIcon);	
									}									
								}
							}
					}
				}else if(index == 'max_dimensions'){
					if(item.limit !==0){
						var imbObjMaxDim = new Image();
						var maxLogoImg = URL.createObjectURL(event.target.files[0]);
						imbObjMaxDim.src = maxLogoImg;
						var dimexp = item.limit.split('x');
						if(typeof dimexp == 'object' && dimexp.length == 2){
							tkawsLumiseCstmBlkOrdrLogoMxW = parseFloat(dimexp[0]);
							tkawsLumiseCstmBlkOrdrLogoMxH = parseFloat(dimexp[1]);
							imbObjMaxDim.onload = function () {
								var imgW = imbObjMaxDim.width;
								var imgH = imbObjMaxDim.height;
								if(imgW > tkawsLumiseCstmBlkOrdrLogoMxW || imgH > tkawsLumiseCstmBlkOrdrLogoMxH){
									tkawsLumiseCstmBlkOrdrLogoValidated = false;
									jQuery('.massage-popup .massage-popup-in').html(tkawslumiseconfigdata.max_dimensions.message);
									jQuery('.massage-popup').addClass('massage-popup-show');
									var defaultUploadIcon = jQuery('#uploaded-logo').data('default-icon');
									jQuery('#uploaded-logo').attr('src',defaultUploadIcon);	
								}									
							}
					}
				}					
				}/* else if(index == 'min_ppi'){
					var imbObjMinDip = new Image();
					var maxLogoImg = URL.createObjectURL(event.target.files[0]);
					imbObjMinDip.src = maxLogoImg;
					var pi = 300/parseFloat(tkawslumiseconfigdata.min_ppi.limit);
					console.log(pi);
					imbObjMinDip.onload = function () {
						var imgW = imbObjMaxDim.width;
						var imgH = imbObjMaxDim.height;	
						console.log(imgW*pi < tkawsprdcanvasdata[i].product_width);					
						if (
							imgW*pi < tkawsprdcanvasdata[i].product_width ||
							imgH*pi < tkawsprdcanvasdata[i].product_height
						) {
							tkawsLumiseCstmBlkOrdrLogoValidated = false;
							jQuery('.massage-popup .massage-popup-in').html(tkawslumiseconfigdata.max_dimensions.message);
							jQuery('.massage-popup').addClass('massage-popup-show');
							var defaultUploadIcon = jQuery('#uploaded-logo').data('default-icon');
							jQuery('#uploaded-logo').attr('src',defaultUploadIcon);								
						}
					}
				}else if(index == 'max_ppi'){
					var imbObjMaxDip = new Image();
					var maxLogoImg = URL.createObjectURL(event.target.files[0]);
					imbObjMaxDip.src = maxLogoImg;
					var pi = 300/parseFloat(tkawslumiseconfigdata.max_ppi.limit);
					imbObjMaxDip.onload = function () {
						var imgW = imbObjMaxDim.width;
						var imgH = imbObjMaxDim.height;	
						console.log(imgW*pi < tkawsprdcanvasdata[i].product_width);					
						if (
							imgW*pi > tkawsprdcanvasdata[i].product_width ||
							imgH*pi > tkawsprdcanvasdata[i].product_height
						) {
							tkawsLumiseCstmBlkOrdrLogoValidated = false;
							jQuery('.massage-popup .massage-popup-in').html(tkawslumiseconfigdata.max_dimensions.message);
							jQuery('.massage-popup').addClass('massage-popup-show');
							var defaultUploadIcon = jQuery('#uploaded-logo').data('default-icon');
							jQuery('#uploaded-logo').attr('src',defaultUploadIcon);								
						}
					}
				} */
				/* if(!tkawsLumiseCstmBlkOrdrLogoValidated){
					break;
				}  */
			}
			//if(tkawsLumiseCstmBlkOrdrLogoValidated){
				reader.readAsDataURL(event.target.files[0]);
			//}
		}

		

	});
	jQuery('.color-group li').on('click',function(){
		jQuery('.color-group li').removeClass('selected');
		jQuery(this).addClass('selected');
		var colorVal =  jQuery(this).data('color');
		canvases[i].setBackgroundColor(colorVal, canvases[i].renderAll.bind(canvases[i]));
		screenshots[i] = canvases[i].toDataURL({format: 'png'});
	});

	jQuery('.massage-popup-close').on('click',function(){
		jQuery('.massage-popup').removeClass('massage-popup-show');
	});
}	
jQuery('.product-design-wrapper .color-group li:first-child').trigger('click');
jQuery('.product-design-wrapper .all-done').on('click',function(e){
		e.preventDefault();
		if(tkawsLumiseCstmBlkOrdrLogoValidated){
			jQuery('.loader').addClass('loading');
			if(jQuery('.upload #upload-logo').val() !=''){
				var previewItems = '';
				screenshots.forEach(function(item,index){
					previewItems +='<div class="preview-item"><a class="image-wrap" href="#"><img src="'+item+'"></a><span class="title">'+tkawsprdcanvasdata[index].title+'</span><input class="preview-image" type="hidden" value="'+encodeURIComponent(item)+'"><input class="preview-json" type="hidden" value="'+encodeURIComponent(JSON.stringify(tkawslumifilesdata[index]))+'"><input class="product-id" type="hidden" value="'+tkawsprdcanvasdata[index].woo_prd_id+'"></div>';
				});
				if(previewItems.length > 0){
					jQuery('.see-more-designed-products').addClass('show');

				}
				if(previewItems.length > 0){
					jQuery('.logo-color-popup').fadeOut();
					jQuery('.lumise-preview-designs').fadeIn(function(){
						jQuery('.lumise-preview-designs-in').html(previewItems).promise().done(function(){
							jQuery('.preview-item a').on('click',function(e){
								e.preventDefault();
								var previewImage = jQuery(this).closest('.preview-item').find('.preview-image').val();
								var previewJson = jQuery(this).closest('.preview-item').find('.preview-json').val();
								var productId = jQuery(this).closest('.preview-item').find('.product-id').val();
								var productColor = jQuery('.color-group li.selected').data('color');
								var ajaxArr = {action : 'tkawsLumiseCreateShare', previewimg: previewImage, previewjsn : previewJson, prdId: productId, prdColor: productColor };
								jQuery.ajax({
									type : "post",
									dataType : "json",
									url : jQuery('.lumise-preview-designs').data('submit-url'),
									data : ajaxArr,
									beforeSend: function(){
										jQuery('.loader').addClass('loading');
									},
									success: function (result) {
										/* jQuery('.loader').removeClass('loading'); */
										if(result.response == 'success' && result.target.length > 0){
											window.location = result.target ;
										}else{
											alert(result.message)
										}	
									}
								});
							});
						});							
					});
				}	
			}
			jQuery('.loader').removeClass('loading');	
		}			
});

jQuery('.product-design-wrapper .skip-step').on('click',function(event){
	event.preventDefault();
	var breadcrumbOutput = '<li><a href="#">Templates</a></li>';
	jQuery('.template-select-breadcrumb').append(breadcrumbOutput);
	jQuery('.logo-color-popup').fadeOut(function(){
		setTimeout(function(){
			jQuery('.template-wrapper').fadeIn();
			const element = document.querySelector(".template-wrapper");
			element.scrollIntoView();
		},200)
	});
});

jQuery('.template-wrapper .see-more-design').on('click',function(){
	jQuery('.category-list li.initially-hidden').fadeIn(function(){
		jQuery('.category-list li').removeClass('initially-hidden')
		jQuery('.template-wrapper .see-more-design').fadeOut();
	})
});

jQuery('.see-more-designed-products').on('click',function(){
	jQuery('.preview-item, .preview-template-item').fadeIn();
	jQuery(this).removeClass('show');
});

jQuery(document).on('click','.category-list li.category-item a',function(event){
	event.preventDefault();
	var categoryName = jQuery(this).find('.category').text();
	var categoryID = jQuery(this).data('id');
	var categorySlug = jQuery(this).data('slug');
	var categoryClass = 'category-breadcrumb-link ';
	if(jQuery(this).closest('li').hasClass('has-sub-category')){
		categoryClass += 'has-sub-category';
	}
	var breadcrumbOutput = '<li class="'+categoryClass+'"><a data-id="'+categoryID+'" data-slug="'+categorySlug+'" href="#">'+categoryName+'</a></li>';
	jQuery('.template-select-breadcrumb').append(breadcrumbOutput);
	if(jQuery(this).closest('li').hasClass('has-sub-category')){
		var dataId = jQuery(this).data('id');
		var ajaxArr = {action : 'tkawsLumiseTemplateCatSubCat', category: dataId};
		jQuery.ajax({
			type : "post",
			dataType : "json",
			url : jQuery('.lumise-preview-designs').data('submit-url'),
			data : ajaxArr,
			beforeSend: function(){
				jQuery('.loader').addClass('loading');
			},
			success: function (result) {
				/* jQuery('.loader').removeClass('loading'); */
				if(result.response == 'success' && result.categories.length > 0){
					var output = '';
					var countLI = 1;
					result.categories.forEach(function(item){
						var liclass = 'category-item ';
						if(item.has_subcat == 1){
							liclass += 'has-sub-category ';
						}
						if(countLI > 8){
							liclass += 'initially-hidden ';
						}
						output +='<li class="'+liclass+'">';
						output +='<a href="#" data-id="'+item.id+'" data-slug="'+item.slug+'">';
						output +='<span class="category">'+item.name+'</span>';
						output +='<div class="image-wrap"><img src="'+item.thumbnail_url+'" /></div>';
						output +='</a>';
						output +='</li>';
						countLI++;
					});
					if(countLI <= 8){
						jQuery('.see-more-design').fadeOut();
					}else{
						jQuery('.see-more-design').fadeIn();
					}
					jQuery('.category-list').html(output).promise().done(function(){
						jQuery('.loader').removeClass('loading');
					});
				}else{
					alert(result.response)
				}	
			}
		});
	}else{
		var dataId = jQuery(this).data('id');
		var ajaxArr = {action : 'tkawsLumiseCatToTemplates', category: dataId};
		jQuery.ajax({
			type : "post",
			dataType : "json",
			url : jQuery('.lumise-preview-designs').data('submit-url'),
			data : ajaxArr,
			beforeSend: function(){
				jQuery('.loader').addClass('loading');
			},
			success: function (result) {
				/* jQuery('.loader').removeClass('loading'); */
				if(result.response == 'success' && result.templates.length > 0){
					var output = '';
					var countLI = 1;
					result.templates.forEach(function(item){
						var liclass = 'template-item ';
						if(countLI > 8){
							liclass += 'initially-hidden ';
						}
						output +='<li class="'+liclass+'">';
						output +='<a href="#" data-id="'+item.id+'" data-name="'+item.name+'" data-screenshot="'+item.screenshot+'">';
						output +='<span class="category">'+item.name+'</span>';
						output +='<div class="image-wrap"><img src="'+item.screenshot+'" /></div>';
						output +='</a>';
						output +='</li>';
						countLI++;
					});
					if(countLI <= 8){
						jQuery('.see-more-design').fadeOut();
					}else{
						jQuery('.see-more-design').fadeIn();
					}
					jQuery('.category-list').html(output).promise().done(function(){
						jQuery('.loader').removeClass('loading');
					});
				}else{
					alert(result.response)
				}	
			}
		});
	}
});

jQuery(document).on('click','.category-list li.template-item a',function(event){
	event.preventDefault();
	jQuery('.loader').addClass('loading');
	var templateName = jQuery(this).data('name');
	var templateID = jQuery(this).data('id');
	var templateScreenshot = jQuery(this).data('screenshot');

	var breadcrumbOutput = '<li class="template-breadcrumb-link"><a data-id="'+templateID+'" data-name="'+templateName+'" href="#">'+templateName+'</a></li>';
	jQuery('.template-select-breadcrumb').append(breadcrumbOutput);
	var ajaxArr = {action : 'tkawsLumiseTemplateSS',ssurl:templateScreenshot};
	jQuery.ajax({
		type : "post",
		dataType : "json",
		url : jQuery('.lumise-preview-designs').data('submit-url'),
		data : ajaxArr,
		success: function (result) {
			/* jQuery('.loader').removeClass('loading'); */
			if(result.response == 'success' && result.base64img.length > 0){
				var base64Image = decodeURIComponent(result.base64img);
				var tmlSS = new Image();
				tmlSS.src = base64Image;
				tmlSS.onload = function () {
					canvases.forEach(function(item,i){
						var logoPlacementX = ((tkawsprdcanvasdata[i].canvas_width/2)+tkawsprdcanvasdata[i].edit_zone_calc.left) - ( tkawsprdcanvasdata[i].edit_zone_calc.width/2);
						var editZnImgWidthDiff = tkawsprdcanvasdata[i].edit_zone_calc.width - canvases[i]._objects[0].width - 5;
						if(editZnImgWidthDiff > 0){
							logoPlacementX = logoPlacementX + (editZnImgWidthDiff/2);
						}
						var logoPlacementY = (((tkawsprdcanvasdata[i].edit_zone_calc.ph/2)+tkawsprdcanvasdata[i].edit_zone_calc.top) - (tkawsprdcanvasdata[i].edit_zone_calc.height/2));
						var editZnImgHeightDiff = tkawsprdcanvasdata[i].edit_zone_calc.height - parseFloat(tmlSS.height/tmlSS.width*tkawsprdcanvasdata[i].edit_zone_calc.width);
						if(editZnImgHeightDiff > 0){
							logoPlacementY = logoPlacementY + (editZnImgHeightDiff/2);
						}
						var img = new fabric.Image(tmlSS,{
							left: logoPlacementX,
							top: logoPlacementY,
						});							
						img.scaleToWidth(parseInt(tkawsprdcanvasdata[i].edit_zone_calc.width)); 						
						canvases[i].add(img);
						canvases[i].renderAll();
						screenshots[i] = canvases[i].toDataURL({format: 'png'});
					});	
					if(screenshots.length > 0){
						var previewItems = '';
						screenshots.forEach(function(item,index){
							console.log(tkawsprdcanvasdata[index]);
							previewItems +='<div class="preview-template-item" data-base-id="'+tkawsprdcanvasdata[index].base_prd_id+'" data-woo-id="'+tkawsprdcanvasdata[index].woo_prd_id+'"><a class="image-wrap" href="#"><img src="'+item+'"></a><span class="title">'+tkawsprdcanvasdata[index].title+'</span></div>';
						});
						if(previewItems.length > 4){
							jQuery('.see-more-designed-products').addClass('show');
						}
						if(previewItems.length > 0){
							jQuery('.logo-color-popup').fadeOut();
							jQuery('.template-wrapper').fadeOut(function(){
								setTimeout(function(){
									jQuery('.lumise-preview-designs').fadeIn(function(){
										jQuery('.lumise-preview-designs-in').html(previewItems).promise().done(function(){
											jQuery('.loader').removeClass('loading');
											jQuery('.preview-template-item').on('click',function(){
												var tmplCats = [];
												jQuery('.category-breadcrumb-link').each(function(){
													var tmplCat = {};
													tmplCat[jQuery(this).children().data('slug')] = jQuery(this).children().data('id')
													tmplCats.push(tmplCat);
												});
												console.log(tmplCats,'category');
												var tmplItm = [];
												var tmplItmk = jQuery('.template-breadcrumb-link').children().data('id');
												var tmplItmV = jQuery('.template-breadcrumb-link').children().data('name');
												tmplItm.push({'id' : tmplItmk, 'name' : tmplItmV })
												var tmplPrdClr = jQuery('.color-group li.selected').data('color');
												tmplPrdClr = tmplPrdClr.replace('#','%23');
												var tmplPrdBaseID = jQuery(this).data('base-id');
												var tmplPrdWooID = jQuery(this).data('woo-id');
												var tmplURL = jQuery('.lumise-preview-designs').data('tmpl-url');
												tmplURL += '?product_base='+tmplPrdBaseID+'&product_cms='+tmplPrdWooID+'&color='+tmplPrdClr;
												tmplURL += '&tmplcats='+encodeURIComponent(btoa(JSON.stringify(tmplCats)));
												tmplURL += '&tmpl='+encodeURIComponent(btoa(JSON.stringify(tmplItm)));
												window.location = tmplURL;
											});
										});							
									});
								},200);
							});
						}	
					}					
					console.log(screenshots,'screenshot');				
				}
			}else{
				alert(result.message)
			}	
		}
	});
});
