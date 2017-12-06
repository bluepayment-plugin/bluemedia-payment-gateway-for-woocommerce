<?php ?>
<div class="wrap">
    <h2 class="wp-heading-inline"><?php _e('Kanały płatności', $this->plugin_text_domain); ?></h2>
    <a href="<?php echo $this->url_to_update; ?>" class="page-title-action">Aktualizuj kanały płatności</a>
    <div id="nds-wp-list-table-demo">
        <div id="nds-post-body">
            <form id="nds-user-list-form" method="get">
                <?php $this->gateway_list_table->display(); ?>
            </form>
        </div>
    </div>
</div>
