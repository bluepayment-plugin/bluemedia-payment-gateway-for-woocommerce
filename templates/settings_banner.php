<?php defined('ABSPATH') || exit; ?>

<div class="bm-settings-banner">
    <div class="bm-settings-banner-title">
        <?php _e('Jak zacząć?', 'bm-woocommerce') ?>
    </div>

    <div class="bm-settings-banner-row">
        <img class="bm-settings-banner-image"
             src="<?php echo blue_media()->get_plugin_images_url() ?>/logo-blue-media-banner.svg">
            <span class="banner-txt"><span class="banner-txt-icon">&#10003;</span><a target="_blank"
                     href="https://developers.bluemedia.pl/online/wdrozenie-krok-po-kroku?mtm_campaign=woocommerce_developers_aktywacja_platnosci&mtm_source=woocommerce_backend&mtm_medium=hyperlink">
                    <?php _e('Prowizja tylko 1,19%', 'bm-woocommerce') ?>
                </a></span>
            <span class="banner-txt"><span class="banner-txt-icon">&#10003;</span><?php _e('Przygotuj regulamin sklepu 10% taniej.', 'bm-woocommerce') ?></br>
            <span><a target="_blank" href="https://developers.bluemedia.pl/legal-geek?mtm_campaign=woocommerce_legalgeek&mtm_source=woocommerce_backoffice&mtm_medium=cta"><?php _e('Dowiedz się więcej', 'bm-woocommerce') ?></a></span>
            </span>
    </div>

    <div class="bm-settings-banner-row-iconBox">
        <div class="bm-settings-banner-icon-box">
            <span>1</span>
            <div>
                <?php _e('Załóż darmowe konto w serwisie', 'bm-woocommerce') ?>
                <a class="icon-box-link" target="_blank" href="https://platnosci.bm.pl/?pk_campaign=woocommerce_panel&pk_source=woocommerce_panel&pk_medium=cta"><?php _e('Zarejestruj się', 'bm-woocommerce') ?></a>
            </div>
        </div>

        <div class="bm-settings-banner-icon-box">
            <span>2</span>
            <span><?php _e('Podaj dane twojej firmy i potwierdź je przelewem weryfikacyjnym', 'bm-woocommerce') ?></span>
        </div>

        <div class="bm-settings-banner-icon-box">
            <span>3</span>
            <span><?php _e('Skonfiguruj płatności w swoim sklepie', 'bm-woocommerce') ?></span>
        </div>
    </div>

    <div class="bm-settings-banner-row-info">
        <span><?php _e('Dowiedz się więcej o wdrożeniu płatności w Twoim sklepie.', 'bm-woocommerce') ?></span>
    </div>

</div>
