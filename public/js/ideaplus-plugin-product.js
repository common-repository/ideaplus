var Ideaplus_Plugin_Goods;

(function ( $ ) {
	'use strict';

	document.write('<script type="text/javascript" src="https://cdn.staticfile.org/compressorjs/1.0.0/compressor.min.js"></script>')
	document.write('<script type="text/javascript" src="https://cdn.staticfile.org/cropper/3.0.0/cropper.min.js"></script>')
	Ideaplus_Plugin_Goods = {
		goods_atts: [],
		sale_set  : {},
		sku_map   : [],
		effect    : {},
		init      : function ( meta_data ) {
			this.goods_atts = meta_data.goods_atts;
			this.sale_set = meta_data.sale_set;
			this.sku_map = meta_data.sku_map;
			this.effect = meta_data.effect ?? [];
			console.log(222,meta_data)
		},
		render    : function () {
			let data = {
				goods_atts: this.goods_atts,
				sale_set  : this.sale_set,
				sku_map   : this.sku_map
			};
			let that = this;
			console.log( 'data', data );
			let isHavOption = ($( ".thwepo-extra-options" ).length > 0);
			console.log(222, isHavOption)
			let html = '';
			let json = {
				variations: []
			}
			$('.variations').css({
				"position": "absolute",
				"z-index": "-1",
				"opacity": "0"
			})
			if ( !isHavOption ) {
				html +=
					'<table class="thwepo-extra-options  thwepo_simple" cellspacing="0"><tbody>';
			}

			data.goods_atts.forEach( e => {
				console.log( e );
				if ( e.i[2] == 1 ) {
					html += '<tr class>' +
						'<td class="label leftside">' +
						'<label class="label-tag">' + e.i[1] + '</label></td>' +
						'<td class="value leftside"><select id="' + e.i[0] + '" name="ideaplus_' + e.i[1].replace(/[\s\/\\]+/g, '') + '_' + e.i[0] + '" class="thwepof-input-field" '+ (e.i[6] ? 'required' : '') +'>';
					e.v.forEach( v => {
						html += "<option value='ideaplus_" + e.i[1].replace(/[\s\/\\]+/g, '') + '_' + v[0] + "'>" + v[1] + "</option>";
					} );
					html += '</select>' + '</td>' + '</tr>';
				}
				else if (e.i[2] == 3) {
					html += '<tr class>' +
						'<td class="label leftside">' +
						'<label class="label-tag ssss">' + e.i[1] + '</label></td>' +
						'<td class="value leftside"><input type="file" id="' + e.i[0] + '" data-valueid="'+ e.v[0][0] + '" name="ideaplus_' + e.i[1].replace(/[\s\/\\]+/g, '') + '_'+  e.i[0] + '" class="thwepof-input-field" '+ (e.i[6] ? 'required' : '') +'>' +
						'</td>' +
						'</tr>';
				}
				else {
					html += '<tr class>' +
						'<td class="label leftside">' +
						'<label class="label-tag ssss">' + e.i[1] + '</label></td>' +
						'<td class="value leftside"><input type="text" id="' + e.i[0] + '" data-valueid="'+ e.v[0][0] + '" name="ideaplus_' + e.i[1].replace(/[\s\/\\]+/g, '') + '_' + e.i[0] + '" class="thwepof-input-field" maxlength="' + (e.i[7] ? e.i[7] : '') + '" placeholder="' + e.i[8]+ '" ' + (e.i[6] ? 'required' : '') +'>' +
						'</td>' +
						'</tr>';
				}

				json.variations.push({
					name: e.i[1],
					id: e.i[0],
					type: e.i[2],
					value: '',
					value_id: ''
				});
			} );

			if ( !isHavOption ) {
				html += '<input id="ideaplusAttrs" name="ideaplus_data" readonly value="'+ JSON.stringify(json) +'" style="display: none">'
				html += '</tbody></table>';
				$( '.variations' ).length ? $( '.variations' ).after( html ) : $('.summary form').prepend(html);
			} else {
				$( '.variations' ).append( html );
			}

			console.log( html );
			// $('.quantity').after(`<p id="totalPrice">Price: ${(parseFloat(skuPrice.apply(that)) * $('.qty').val()).toFixed(2)}</p>`)
			// $('.qty').on('change', function() {
			// 	let count = this.value*1;
			// 	console.log(count, $('#totalPrice').length);
			// 	if ($('#totalPrice').length) {
			// 		$('#totalPrice').html(`Price: ${(parseFloat(skuPrice.apply(that)) * count).toFixed(2)}`);
			// 	}
			// })
			$('.thwepo-extra-options').on('change', 'select', function() {
				let aid = this.id;
				let val = this.value
				let info;
				let html;
				info = findCurNode(that.goods_atts, aid);
				console.log(666, info);
				let originalSelectedOptionValue = $(this).children('option[value="'+ val +'"]').html();
				let originalSelectId = $(this).parent().parent().find('.label-tag').html().toLowerCase().replace(/\?$/g, '').replace(/[\s\/\\]+/g, '-').replace(/\(/g, '').replace(/\)/g, '');
				let $root = $(this).parent().parent().parent();
				$root.children('tr[data-parent="'+ aid +'"]').each((index, item) => {
					$root.children('tr[data-parent="'+ $(item).find('.thwepof-input-field').attr('id') +'"]').each((index2, item2) => {
						json.variations = json.variations.filter(item4 => item4.id != $(item2).find('.thwepof-input-field').attr('id'))
						$(item2).remove();
					});
					json.variations = json.variations.filter(item3 => item3.id != $(item).find('.thwepof-input-field').attr('id'))
					$(item).remove();
				})
				$('.variations').length ? $('.variations').find('select#' + originalSelectId).val(originalSelectedOptionValue).trigger('change') : '';
				if (info.childrens && info.childrens.length > 0) {
					info.childrens.forEach(item => {
						if (item.i[9].includes(val.split('_').slice(-1)[0]*1)) {
							if (item.i[2] === 1) {
								html += '<tr class data-parent="' + info.i[0] + '">' +
									'<td class="label leftside">' +
									'<label class="label-tag">' + item.i[1] + '</label></td>' +
									'<td class="value leftside"><select id="' + item.i[0] + '" name="ideaplus_' + item.i[1].replace(/[\s\/\\]+/g, '') + '_' + item.i[0] + '" class="thwepof-input-field" '+ (item.i[6] ? 'required' : '') +'>';
								item.v.forEach( v => {
									html += '<option value="ideaplus_' + item.i[1].replace(/[\s\/\\]+/g, '') + '_' + v[0]+ '">' + v[1] + "</option>";
								} );
								html += '</select>' +
									'</td>' +
									'</tr>';
							}
							else if (item.i[2] == 3) {
								html += '<tr class>' +
									'<td class="label leftside">' +
									'<label class="label-tag ssss">' + item.i[1] + '</label></td>' +
									'<td class="value leftside"><input type="file" id="' + item.i[0] + '" data-valueid="'+ item.v[0][0] + '" name="ideaplus_' + item.i[1].replace(/[\s\/\\]+/g, '') + '_' + item.i[0] + '" class="thwepof-input-field" '+ (item.i[6] ? 'required' : '') +'>' +
									'</td>' +
									'</tr>';
							} else {
								html += '<tr class data-parent="' + info.i[0] + '">' +
									'<td class="label leftside">' +
									'<label class="label-tag">' + item.i[1] + '</label></td>' +
									'<td class="value leftside"><input type="text" id="' + item.i[0] + '" data-valueid="'+ item.v[0][0] + '" name="ideaplus_' + item.i[1].replace(/[\s\/\\]+/g, '') + '_' + item.i[0] + '" class="thwepof-input-field" maxlength="' + (item.i[7] ? item.i[7] : '') + '" placeholder="' + item.i[8]+ '" ' + (item.i[6] ? 'required' : '') +'>' +
									'</td>' +
									'</tr>';
							}
							if (!json.variations.find(item2 => item2.id == item.i[0])) {
								let i, j;
								i = json.variations.findIndex(item => {
									return item.id == info.i[0]
								})
								let arr1 = json.variations.slice(0, i + 1)
								let arr2 = json.variations.slice(i + 1)
								j = arr2.filter(item => item.parent_id == info.i[0]).length;
								if (j) {
									let arr3 = arr2.slice(0, j);
									let arr4 = arr2.slice(j);
									arr3.push({
										name: item.i[1],
										id: item.i[0],
										type: item.i[2],
										value: '',
										value_id: '',
										parent_id: info.i[0]
									})
									arr2 = arr3.concat(arr4);
								} else {
									arr1.push({
										name: item.i[1],
										id: item.i[0],
										type: item.i[2],
										value: '',
										value_id: '',
										parent_id: info.i[0]
									})
								}
								json.variations = arr1.concat(arr2)
							}
						}
					})
				}
				$(this).parent().parent().after(html);
				// console.log(777, skuPrice.apply(that))
				// if ($('#totalPrice').length) {
				// 	$('#totalPrice').html(`Price: ${(parseFloat(skuPrice.apply(that)) * $('.qty').val()).toFixed(2)}`);
				// } else {
				// 	$('.quantity').after(`<p id="totalPrice">Price: ${(parseFloat(skuPrice.apply(that)) * $('.qty').val()).toFixed(2)}</p>`)
				// }

				function findCurNode(tree, id, node = null) {
					tree.forEach(item => {
					  if (item.i[0] == id) {
						node = item
					  } else {
						  if (item.childrens && item.childrens.length) {
							const findChildren = findCurNode(item.childrens, id, node)
							if (findChildren) {
								node = findChildren
							}
						}
					  }
					})
					return node
				}

			})
			$('form.cart').submit(function() {
				json.variations.forEach(item => {

					if (item.type === 1) {
						item.value = $('option[value="'+ $('#' + item.id).val() +'"]').html();
						item.value_id = $('#' + item.id).val().split('_').slice(-1)[0];
					} else if(item.type === 3) {
						item.value = that.effect ? $('#image-data').val() : $('#' + item.id).val();
						item.value_id = $('#' + item.id).data('valueid');
					} else {
						item.value = $('#' + item.id).val();
						item.value_id = $('#' + item.id).data('valueid');
					}
				})
				// json.variations['value'] = $('#image-data').val();
				// alert(JSON.stringify(json))
				$('#ideaplusAttrs').val(JSON.stringify(json));
			});
			$('.thwepo-extra-options select').trigger('change')
			// function skuPrice() {
			// 	let price;
			// 	let sku = $('.sku').html();
			// 	console.log(sku, $('.sku'), this.sku_map)
			// 	let result = this.sku_map.find(item => item.ref_sku_id == sku)
			// 	console.log(result)
			// 	result && (price = result.price);
			// 	return price;
			// }

			if (that.effect) {
				let html = '<input type="hidden" name="image-data" id="image-data" value="" />'
				$(document.body).append(html)
				var cropperAvatar = {
					avatar : null,
					style : '',
					init : function() {
						var $this = this;
						let html = `
						<div id="avatar-container">
						<div class="avatar-area" id="avatar-area">
							<div class="avatar-base">
								<div class="avatar-template">
									<img src="${that.effect.crop_template_path}" alt="" id="slider-imgss">
								</div>
																<div class="avatar-template-base">
										<img src="${that.effect.crop_base_image_path}" alt="">
									</div>
														</div>
	
							<div style="position: absolute;width:100%;height:100%;">
								<img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" id="avatar-img" class="" style="display:none;">
							</div>
						</div>
	
						<div class="avatar-button-group">
							<div class="btn-group" style="width:100%;">
								<button type="button" class="btn btn-primary" data-method="move" data-option="-10" data-second-option="0" title="Move Left">
								<span class="docs-tooltip" title="" data-original-title="Move Left">
								  <span class="fa fa-chevron-left"></span>
								</span>
								</button>
								<button type="button" class="btn btn-primary" data-method="move" data-option="10" data-second-option="0" title="Move Right">
							<span title="" data-original-title="Move Right">
							  <span class="fa fa-chevron-right"></span>
							</span>
								</button>
								<button type="button" class="btn btn-primary" data-method="move" data-option="0" data-second-option="-10" title="Move Up">
								<span class="docs-tooltip" title="" data-original-title="Move Up">
								  <span class="fa fa-chevron-up"></span>
								</span>
								</button>
								<button type="button" class="btn btn-primary" data-method="move" data-option="0" data-second-option="10" title="Move Down">
								<span class="docs-tooltip" title="" data-original-title="Move Down">
								  <span class="fa fa-chevron-down"></span>
								</span>
								</button>
	
								<button type="button" class="btn btn-primary" data-method="zoom" data-option="0.1" title="Zoom In">
								<span class="docs-tooltip" title="" data-original-title="Zoom In">
								  <span class="fa fa-search-plus"></span>
								</span>
								</button>
								<button type="button" class="btn btn-primary" data-method="zoom" data-option="-0.1" title="Zoom Out">
								<span class="docs-tooltip" title="" data-original-title="Zoom Out">
								  <span class="fa fa-search-minus"></span>
								</span>
								</button>
	
								<button type="button" class="btn btn-primary" data-method="rotate" data-option="-10" title="Rotate Left">
								<span class="docs-tooltip" title="" data-original-title="Rotate Left">
								  <span class="fa fa-rotate-left"></span>
								</span>
								</button>
								<button type="button" class="btn btn-primary" data-method="rotate" data-option="10" title="Rotate Right">
								<span class="docs-tooltip" title="" data-original-title="Rotate Right">
								  <span class="fa fa-rotate-right"></span>
								</span>
								</button>
							</div>
	
						</div>
					</div>
					`
						$('.woocommerce-product-gallery').append(html)
						$('.leftside input[type="file"]').on('change', function() {
							var file = this.files[0];
							var my = this;
							console.log(this.files)
							if (!file) {
								// clear;
								$('#avatar-img').cropper('destroy');
								$('.avatar-base').show();
								$this.avatar = null;
								// designModal.resetImage();
								return false;
							}

							// show the avatar-area


							new Compressor(file, {
								quality: 0.6,
								success:function(result) {
									let img;
									img = new Image();
									img.onload = function() {
										if (img.width < 500 || img.height < 500) {
											alert('The size of the uploaded image is too small. Please re-upload')
											my.value = '';
											if ($('#avatar-container').is(':visible')) {
												$('#slider-image-container').slideDown('fast');
												$('#avatar-container').slideUp('fast');
												$('.flex-control-thumbs, .woocommerce-product-gallery__wrapper').slideDown('fast')
											}
										} else {
											if ($('#avatar-container').is(':hidden')) {
												$('#slider-image-container').slideUp('fast');
												$('#avatar-container').slideDown('fast');
												$('.flex-control-thumbs, .woocommerce-product-gallery__wrapper').slideUp('fast')
											}
											$('#avatar-img').prop('src', URL.createObjectURL(result));
											$this.setUpCropper();
										}
									}
									img.src = URL.createObjectURL(result);
								},
								error:function(err) {
									console.log(err.message);
								},
							})
						});

						$('.avatar-button-group button[data-method]').on('click', function() {
							if ($this.avatar != null) {
								if ($(this).has('data-second-option')) {
									$('#avatar-img').cropper($(this).data('method'), $(this).data('option'), $(this).data('second-option'));
								} else {
									$('#avatar-img').cropper($(this).data('method'), $(this).data('option'));
								}

								gaHelper.sendEvent('Photo_pro', $(this).prop('title'), '');
							}
						});

						$('#btn-confirm-design').on('click', function() {
							// send to ga
							gaHelper.sendEvent('Photo_pro', 'Confirm', 'Confirm!');
							// checkimage
							if ($('#avatar-input').val() == '') {
								alert('Oops, don\'t forget to add image first!');
								return;
							}

							// check if designed
							// if (designModal.isDesigned()) {
							// 	designModal.show();
							// 	return false;
							// }

							var file = $('#avatar-input').get(0).files[0];

							new Compressor(file, {
								quality: 0.6,
								//maxWidth:300,
								//maxHeight:300,
								success:function(result) {
									var form = $('#design-form')[0];
									//$('[name="avatar_file"']).val()
									var formData = new FormData(form);
									if(IEVersion() == 'edge' || IEVersion() == -1){
										formData.set('avatar_file', result, result.name);
									}
									$.ajax({
										type: 'post',
										url: '{{url route="design/crop"}}',
										data: formData,
										async: true,
										processData: false,
										contentType: false,
										dataType: "json",
										beforeSend: function () {
											loadingMask.open();
										},
										complete: function () {
											loadingMask.close();
										},
										success: function(data){
											// console.log(data);
											loadingMask.close();
											if (data.error) {
												gaHelper.sendEvent('Photo_pro', 'ConfirmReturnError', data.msg);
												alert(data.msg);
											} else {
												// success
												gaHelper.sendEvent('Photo_pro', 'ConfirmReturnSuccess', '');
												// designModal.show(data.result);
											}
										},
										error: function(xhr){
											loadingMask.close();
											alert(xhr.responseText);
										}
									});
								},
								error:function(err) {
									console.log(err.message);
								},
							})

						})
					},
					setUpCropper : function() {
						$('.avatar-base').hide();
						if (this.avatar != null) {
							$('#avatar-img').cropper('destroy');
						}
						this.avatar = $('#avatar-img').cropper({
							dragMode: 'move',
							strict: false,
							minCropBoxWidth: 500,
							minCropBoxHeight: 400,
							aspectRatio: 500 / 400,
							autoCropArea: 0.1,
							guides: false,
							highlight: false,
							dragCrop: false,
							cropBoxMovable: false,
							cropBoxResizable: false,
							doubleClickToggle: false,
							toggleDragModeOnDblclick: false,
							responsive : false,
							crop: function(e) {
								var json = ['{"x":' + e.x, '"y":' + e.y, '"height":' + e.height, '"width":' + e.width, '"rotate":' + e.rotate + '}'].join();
								$('#image-data').val(json);
								// designModal.resetImage();
							},
							ready: function(e, a) {
								$('#avatar-area, .cropper-container').css({
									'height': $('#avatar-container').width() * (400 / 500) + 'px',
									'width': 'auto'
								})
								$('.cropper-crop-box').css({
									'width': $('#avatar-container').width() + 'px',
									'height': $('#avatar-container').width() * (400 / 500) + 'px',
								})
								$('.cropper-crop-box').prepend($('.avatar-template').prop("outerHTML"));
								$('.cropper-view-box').prop('id', 'cropper-view-box');
								cropperAvatar.setStyle();
							}
						});
					},
					changeStyle : function(style) {
						this.style = style;
						this.setStyle();
					},
					setStyle : function() {
						var obj = $('#cropper-view-box img');
						obj.removeClass();
						// $('#cropper-view-box').addClass('cropper-view-box');
						if (that.effect.color_style === 2) {
							obj.addClass('filter');
						}
						$('#design-style').val(this.style);
					}
				};
				cropperAvatar.init();
			}
		}
	}
})( jQuery );
