<?php

include_once BARCODE_PLUGIN_CLASSES_DIR . 'DatabaseTrait.php';
require_once(BARCODE_PLUGIN_LIB_DIR . 'BCGColor.php');
require_once(BARCODE_PLUGIN_LIB_DIR . 'BCGDrawing.php');
require_once(BARCODE_PLUGIN_LIB_DIR . 'BCGcode39.barcode.php');

/**
 * Class BarcodeAdmin
 */
class BarcodeAdmin
{

    use DatabaseTrait;

    /**
     * @var BarcodeGeneratorJPG
     */
    private $generator;

    private $code;
    private $colorFront;
    private $colorBack;

    /**
     * BarcodeAdmin constructor.
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'adminMenu']);
        add_action('admin_post_barcode_form', [$this, 'adminForm']);
        add_shortcode('barcode', [$this, 'barcodeShortCode']);
        add_filter('gform_notification', [$this, 'gfNotificationSignature'], 10, 3 );
        add_action('admin_post_barcode_clear', [$this, 'clearForm']);

        $this->colorFront = new BCGColor(0, 0, 0);
        $this->colorBack = new BCGColor(255, 255, 255);

        $this->code = new BCGcode39();
        $this->code->setScale(2);
        $this->code->setColor($this->colorFront, $this->colorBack);
    }

    /**
     * Add plugin to admin menu
     */
    public function adminMenu()
    {
        add_menu_page( 'Barcode', 'Barcode', 'manage_options', 'wp-barcode', [$this, 'adminPage'] );
    }

    /**
     * Plugin admin page
     */
    public function adminPage()
    {
        $barcodeW = get_option('barcode_w');
        $barcodeH = get_option('barcode_h');
        $from     = get_option('barcode_range_from');
        $to       = get_option('barcode_range_to');

        include_once BARCODE_PLUGIN_ADM_TEMPLATES_DIR . 'main.php';
    }

    /**
     * @param $code
     * @return string
     */
    private function generateBarcode($code)
    {
        $this->code->parse($code);

        $drawing = new BCGDrawing($code, $this->colorBack);
        $drawing->setBarcode($this->code);
        $drawing->draw();
        return $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
    }

    /**
     * Shortcode callback
     * @return string
     */
    public function barcodeShortCode()
    {
        $from = get_option('barcode_range_from');
        $to   = get_option('barcode_range_to');

        $fieldNumber = $this->checkField($_SESSION['gFormId']);

        $code = $this->generateCode($from, $to, $_SESSION['gFormId'], $fieldNumber);

        $id = $this->saveGF($_SESSION['gfLeadId'], $_SESSION['gFormId'], $fieldNumber, $code);

        $barcode = $this->generateBarcode($code);

        if (!$barcode) {
            return;
        }

        $this->save($id, $_SESSION['gFormId'], $code);

        $w = get_option('barcode_w');
        $h = get_option('barcode_h');

        return "<img style=\"width:{$w}px;height:{$h}px;\" src='{$barcode}'>";
    }

    /**
     * @param $notification
     * @param $form
     * @param $entry
     * @return mixed
     */
    public function gfNotificationSignature($notification, $form, $entry)
    {
        if(!session_id()) {
           session_start();
        }

        $_SESSION['gFormId'] = $form['id'];
        $_SESSION['gfLeadId'] = $entry['id'];

        return $notification;
    }

    /**
     * Save admin from
     */
    public function adminForm()
    {
        $barcodeW    = isset($_POST['barcode_w']) ? intval($_POST['barcode_w']) : 0;
        $barcodeH    = isset($_POST['barcode_h']) ? intval($_POST['barcode_h']) : 0;
        $barcodeFrom = isset($_POST['barcode_range_from']) ? intval($_POST['barcode_range_from']) : 0;
        $barcodeTo   = isset($_POST['barcode_range_to']) ? intval($_POST['barcode_range_to']) : 0;

        update_option('barcode_w', $barcodeW);
        update_option('barcode_h', $barcodeH);
        update_option('barcode_range_from', $barcodeFrom);
        update_option('barcode_range_to', $barcodeTo);

        $this->redirect();
    }

    /**
     * Redirect to refer
     */
    private function redirect()
    {
        if ( wp_get_referer() ) {
            wp_safe_redirect( wp_get_referer() );
        } else {
            wp_safe_redirect( get_home_url() );
        }
    }

    /**
     * @param $val
     * @param $min
     * @param $max
     * @return bool
     */
    private function range($val, $min, $max) {
        return ($val >= $min && $val < $max);
    }

    /**
     * @param $from
     * @param $to
     * @param $formId
     * @param $fieldNumber
     * @return int
     */
    private function generateCode($from, $to, $formId, $fieldNumber)
    {
        $lastCode = $this->getLastCode($formId, $fieldNumber);

        if (!$lastCode) {
            return $from;
        }

        if ($this->range($lastCode, $from, $to)) {
            return $lastCode + 1;
        }

        return $from;
    }

    /**
     * Clear form
     */
    public function clearForm()
    {
        $this->clear();
        $this->redirect();
    }
}