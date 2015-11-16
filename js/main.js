(function($) {
	$(document).ready(function(){
		$('.mlynf-media-button').click( mlynf_media_window );
	});

	function mlynf_media_window(e) {

		if (this.window === undefined) {
			
			this.window = wp.media({
				frame: 'select',
				multiple: false,
				editing: true
			});

			var self = this;
			this.window.on('select', function() {
				images = self.window.state().get('selection').toJSON();
				$(self).parent().find('input').val( images[0].url );
			});
		}

		this.window.open();
		return false;
	}

})(jQuery);