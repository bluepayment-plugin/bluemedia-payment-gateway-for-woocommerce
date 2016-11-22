<?php
/**
 * System płatności online Blue Media.
 *
 * View for payment form
 *
 * @author    Piotr Żuralski <piotr@zuralski.net>
 * @copyright 2015 Blue Media S.A.
 * @license   http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 * @see       http://english.bluemedia.pl/project/payment_gateway_on-line_payment_processing/ (English)
 * @see       http://bluemedia.pl/projekty/payment_gateway_bramka_platnicza_do_realizowania_platnosci_online (Polish)
 * @since     2015-02-28
 * @version   v1.0.5
 */
?>
<div>
    <form action="<?php echo $data['action']; ?>" method="post" id="bluemedia_form" name="bluemedia_form">
        <div class="hidden">
            <?php
            foreach ($data['form'] as $fieldName => $fieldValue) {
                printf('<input type="hidden" name="%s" value="%s" />', $fieldName, $fieldValue);
            }
            ?>
        </div>
        <div class="buttons">
            <div class="pull-right">
                <input type="submit" value="<?php echo $data['submit']; ?>" id="button-confirm" class="btn btn-primary" />
            </div>
        </div>
        <script type="text/javascript">
            // post data to server
            document.getElementById('bluemedia_form').submit();
        </script>
    </form>
</div>

<?php die(); ?>

