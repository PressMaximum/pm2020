<div id="page-titlebar" class="page-titlebar">
	<div class="page-titlebar-inner customify-container">
		<h1 class="titlebar-title h3"><?php the_title(); ?> Documentation</h1>
		<div class="wedocs-header-search">
			<form role="search" method="post" class="search-form wedocs-search-form"
				itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction"
				action="<?php echo esc_url( home_url( '/help/documentation/search/' ) ); ?>">
				<meta itemprop="target" content="https://wpcustomify.com/help/documentation/search/{query}/"/>
				<div class="wedocs-search-input">
					<span class="wedocs-inw">
						<input type="search"
							class="search-field"
							itemprop="query-input"
							name="s"
							data-swplive="true"
							tabindex="1"
							placeholder="<?php _e( 'What can we help you with?', 'freemius' ); ?>"
							value="<?php echo get_search_query(); ?>"
							title="<?php echo esc_attr_x( 'Search for:', 'label' ); ?>"/>
						<?php
						// Search in the docs but it not working with search wp.
						
						/*
						$doc_id = get_the_ID();
						$dropdown_args = array(
							'post_type'         => 'docs',
							'echo'              => 1,
							'depth'             => 1,
							'show_option_none'  => __( 'All Docs', 'wedocs' ),
							'option_none_value' => 'all',
							'name'              => 'search_in_doc',
							'class'              => 'search_in_doc',
							'selected'              => false,
						);

						if ( isset( $_GET['search_in_doc'] ) && 'all' != $_GET['search_in_doc'] ) {
							$dropdown_args['selected'] = (int) $_GET['search_in_doc'];
						}

						if ( ! $dropdown_args['selected'] ) {
							$dropdown_args['selected']  = $doc_id;
						}


						wp_dropdown_pages( $dropdown_args );
						*/

						?>
					</span>
					<input type="hidden" name="post_type" value="docs">
					<button type="submit" tabindex="1"><span><?php _e( 'Search', 'freemius' ); ?></span></button>

				</div>
				<div class="wedocs-sample-term"><?php _e( 'Search documentation using terms like "install", "import site", or "header builder".' ); ?></div>
			</form>
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
