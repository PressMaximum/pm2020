<div id="page-titlebar" class="page-titlebar">
    <div class="page-titlebar-inner customify-container">
        <h1 class="titlebar-title h3">Documentation</h1>
        <div class="titlebar-tagline">Everything you need to get started</div>
        <div class="wedocs-header-search">
            <form role="search" method="post" class="search-form wedocs-search-form"
                  itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction"
                  action="<?php echo esc_url( home_url( '/help/documentation/search/' ) ) ?>">

                <meta itemprop="target" content="https://wpcustomify.com/help/documentation/search/{query}/"/>
                <div class="wedocs-search-input">
                    <input type="search"
                           class="search-field"
                           itemprop="query-input"
                           name="query"
                           data-swplive="true"
                           tabindex="1"
                           placeholder="<?php _e( 'What can we help you with?', 'freemius' ) ?>"
                           value="<?php echo get_search_query() ?>"
                           title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>"/>
                    <input type="hidden" name="post_type" value="docs">
                    <button type="submit" tabindex="1"><span><?php _e( 'Search', 'freemius' ) ?></span></button>

                </div>
                <div class="wedocs-sample-term"><?php _e('Search documentation using terms like "install", "import site", or "header builder".') ?></div>
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