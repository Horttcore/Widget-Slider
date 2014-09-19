var WidgetSlider;

jQuery(document).ready(function(){

	var Plugin = {

		init:function(){

			// Cache
			Plugin.body = jQuery('body');
			Plugin.widgetHolder = jQuery('.widgets-holder-wrap');
			Plugin.sliderLists = jQuery('.slider-list');
			Plugin.currentSliderList = false;
			Plugin.currentPostsField = false;
			Plugin.thickboxWindow = false;
			Plugin.thickboxTitle = false;
			Plugin.thickboxContent = false;

			// Bindings
			Plugin.bindings();
		},

		bindings:function(){

			// Focus fields
			Plugin.body.on({
				mouseenter: function () {
					Plugin.currentSliderList = jQuery(this).find('.slider-list');
					Plugin.currentPostsField = jQuery(this).find('.slider-post-ids');
				}
			}, '.widget');

			// Open thickbox
			Plugin.widgetHolder.on( 'click', '.add-slides', function(e){
				e.preventDefault();
				Plugin.resetSearch();
				setTimeout( function(){
					Plugin.setThickboxSize();
				}, 500 );

			});

			// Search Post
			Plugin.body.on( 'click', '#widget-slider-search-button', function(e){
				e.preventDefault();
				Plugin.searchPost();
			});

			Plugin.body.on( 'keyup', '#widget-slider-search', function(e){

				if ( 13 !== e.which )
					return;

				e.preventDefault();
				Plugin.searchPost();

			}).on( 'change', '#widget-slider-search', function(e){

				if ( '' === jQuery(this).val() )
					Plugin.resetSearch();

			});

			// Add post
			Plugin.body.on( 'click', '.add-slide', function(e){
				e.preventDefault();
				Plugin.addSlide( jQuery(this) );
			});

			// Make lists sortable
			Plugin.listSortable();

			// Remove post
			Plugin.widgetHolder.on('click', '.remove-slide', function(e){
				e.preventDefault();
				Plugin.removeSlide( jQuery(this) );
			});

		},

		addSlide:function( obj ){

			Plugin.currentSliderList.append( '<li data-id="' + obj.data('id') + '"><a class="dashicons dashicons-menu sort-slide" href="#"></a> ' + obj.data('title') + ' <a href="#" class="dashicons dashicons-dismiss remove-slide"></a></li>' );
			Plugin.updateSlider();

		},

		listSortable:function(){

			// Sortable posts
			Plugin.sliderLists = jQuery('.slider-list');
			Plugin.sliderLists.each(function(){
				Plugin.sliderLists.sortable({
					axis: 'y',
					forceHelperSize: true,
					forcePlaceholderSize: true,
					handle:'.sort-slide',
					helper: 'clone',
					stop: function(){
						Plugin.updateSlider();
					}
				});
			});

		},

		removeSlide:function( obj ){

			obj.parents('li:first').remove();
			Plugin.updateSlider();

		},

		resetSearch:function(){

			var search = jQuery('#widget-slider-search'),
				recent = jQuery('#widget-slider-recent-posts'),
				result = jQuery('#widget-slider-search-result');

			search.val('');
			result.html('');
			recent.show();

		},

		searchPost:function() {

			var search = jQuery('#widget-slider-search'),
				button = jQuery('#widget-slider-search-post'),
				recent = jQuery('#widget-slider-recent-posts'),
				result = jQuery('#widget-slider-search-result'),
				spinner = false;

			if ( '' !== search.val() ) {

				// Add spinner
				button.after('<span class="spinner" id="widget-slider-spinner"></span>');

				// Hide recent posts
				recent.hide();

				// Search
				jQuery.post(ajaxurl, {action: 'widget-slider-search-posts', search: search.val(), nonce: widgetSlider.searchNonce }, function(response){
					console.log( response );
					// Inject search result
					result.html( response.output );

					// Remove spinner
					button.next().remove();

				}, 'json' );

			} else {

				Plugin.resetSearch();

			}

		},

		setThickboxSize:function(){

			Plugin.thickboxWindow = jQuery('#TB_window');
			Plugin.thickboxTitle = jQuery('#TB_title');
			Plugin.thickboxContent = jQuery('#TB_ajaxContent');

			Plugin.thickboxContent.css( {
				width: parseInt( Plugin.thickboxWindow.width() ) - parseInt( Plugin.thickboxContent.css('paddingLeft') ) - parseInt( Plugin.thickboxContent.css('paddingRight') ),
				height: parseInt( Plugin.thickboxWindow.height() ) - parseInt( Plugin.thickboxTitle.height() ) - parseInt( Plugin.thickboxContent.css('paddingBottom') ) - parseInt( Plugin.thickboxContent.css('paddingBottom') ),
				overflow: 'scroll'
			} );

		},

		updateSlider:function(){

			var ids = [];

			Plugin.currentSliderList.children().each(function(i,e){
				ids.push( jQuery(this).data('id') );
			});

			Plugin.currentPostsField.val( ids );

		}

	};

	WidgetSlider = Plugin;

	WidgetSlider.init();

});

// Make new slider widgets sortable
jQuery(document).ajaxSuccess(function(e, xhr, settings) {

	if( settings.data && ( -1 !== settings.data.search('action=widgets-order') || -1 !== settings.data.search('id_base=widget-slider') ) ) {
		WidgetSlider.listSortable();
	}

});
