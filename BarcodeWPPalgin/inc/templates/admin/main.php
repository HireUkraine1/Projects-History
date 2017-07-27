<div class="wrap">
    <h1 class="wp-heading-inline"><?=_e('Barcode', 'barcode'); ?></h1>
    <div class="main postbox" style="padding: 10px;">
        Paste shortcode: <strong>[barcode]</strong>
    </div>
</div>

<div class="wrap">
    <h1 class="wp-heading-inline"><?=_e('Barcodes', 'barcode'); ?>:</h1>
    <div class="wrap">
        <div class="main postbox" style="padding: 10px; overflow: hidden;">
            <form action="<?=admin_url( 'admin-post.php' ); ?>" method="post">
                <input type="hidden" name="action" value="barcode_form">
                <?=wp_original_referer_field(); ?>

                <p style="margin: 20px 0;">
                    <label for="barcode_w">Barcode width(px):</label>
                    <input type="text" id="barcode_w" name="barcode_w" style="margin:0 20px 0 10px;"
                        value="<?=$barcodeW; ?>"
                    >
                </p>

                <p style="margin: 20px 0;">
                    <label for="barcode_h">Barcode height(px):</label>
                    <input type="text" id="barcode_h" name="barcode_h" style="margin:0 20px 0 10px;"
                        value="<?=$barcodeH; ?>"
                    >
                </p>

                <p><h2>Range</h2></p>

                <p style="margin: 20px 0;">
                    <label for="barcode_range_from">From:</label>
                    <input type="text" id="barcode_range_from" name="barcode_range_from" style="margin:0 20px 0 10px;"
                           value="<?=$from; ?>"
                    >

                    <label for="barcode_range_to">To:</label>
                    <input type="text" id="barcode_range_to" name="barcode_range_to" style="margin:0 20px 0 10px;"
                           value="<?=$to; ?>"
                    >
                </p>

                <div class="publishing-action" style="float: right;">
                    <input name="save" type="submit" class="button button-primary button-large" value="Save">
                </div>
            </form>
            <form action="<?=admin_url( 'admin-post.php' ); ?>" method="post">
                <input type="hidden" name="action" value="barcode_clear">
                <?=wp_original_referer_field(); ?>
                <div class="publishing-action" style="float: right; margin-right: 10px;">
                    <input name="save" type="submit" class="button button-primary button-large" value="Clear All">
                </div>
            </form>
        </div>
    </div>
</div>