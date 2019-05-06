<div id="page-titlebar" class="page-titlebar">
	<div class="page-titlebar-inner customify-container">
		<h1 class="titlebar-title h3"><?php the_title(); ?> Documentation</h1>
		<div class="wedocs-header-search">
			<?php pm2020_search_docs_form(); ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	(function ($) {
		$('.search-form.wedocs-search-form').on('submit', function () {
			var search = $(this).find('input').val().toLowerCase().trim();
			if ('' === search){
				return false;
			}
			$(this).attr('action', $(this).attr('action') + encodeURIComponent(search) + '/');
		});
	})(jQuery);
</script>
