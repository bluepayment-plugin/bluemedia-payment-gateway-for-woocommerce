<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <header class="entry-header">
            <h1 class="entry-title">Wybierz kanał płatności</h1>
        </header>
        <div>
            <div class="columns-3">
                <ul class="products">
                    <?php foreach ($gateways as $row): ?>
                        <li class="product" style="border: 1px solid #ddd; margin: 10px; width: 24.99%">
                            <a href="?wc-api=WC_Payment_Gateway_BlueMedia&order_id=<?php echo $orderId; ?>&gateway_id=<?php echo $row['gateway_id']; ?>">
                                <img src="<?php echo $row['gateway_logo_url'] ?>" width="100"
                                     class="woocommerce-placeholder wp-post-image" height="100">
                                <h2 class="woocommerce-loop-product__title"><?php echo $row['gateway_type']; ?></h2>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

    </main><!-- #content -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>


