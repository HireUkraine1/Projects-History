<?php

/**
 * Class DatabaseTrait
 */
trait DatabaseTrait
{
    /**
     * @var string
     */
    private $field = 'Barcode';

    /**
     * @var string
     */
    private static $table = 'barcodes';

    /**
     * @param $leadId
     * @param $formId
     * @param $fieldNumber
     * @param $code
     * @return int
     */
    public function saveGF($leadId, $formId, $fieldNumber, $code)
    {
        global $wpdb;

        $lead_detail_table = GFFormsModel::get_lead_details_table_name();

        $wpdb->insert( $lead_detail_table, [

            'lead_id'       => $leadId,
            'form_id'       => $formId,
            'field_number'  => $fieldNumber,
            'value'         => $code

        ], ['%d', '%d', '%F', '%s' ] );

        return $wpdb->insert_id;
    }

    /**
     * @param $formId
     * @return int|mixed
     */
    private function checkField($formId)
    {
        $max  = 0;
        $form = GFFormsModel:: get_form_meta($formId);

        foreach ($form['fields'] as $field) {
            if (!is_array($field)) {
                if ($field->label == $this->field) {
                    return $field->id;
                }

                if ($field->id > $max) {
                    $max = $field->id;
                }
                continue;
            }

            if ($field['label'] == $this->field) {
                return $field['id'];
            }

            if ($field['id'] > $max) {
                $max = $field['id'];
            }
        }

        $id = $max + 1;
        $form['fields'][] = [
            'label'         => $this->field,
            'id'            => $id,
            'visibility'    => 'hidden',
        ];
        $form = GFFormsModel::add_default_properties($form);

        GFFormsModel::update_form_meta($formId, $form);
        return $id;
    }

    /**
     * @param $formId
     * @param $fieldNumber
     * @return int
     */
    public function getLastCode($formId, $fieldNumber)
    {
        global $wpdb;

        $lead_detail_table = GFFormsModel::get_lead_details_table_name();

        return (int) $wpdb->get_var("
            SELECT `value` FROM {$lead_detail_table} 
            WHERE form_id = {$formId} AND field_number = {$fieldNumber}
            ORDER BY id DESC
            LIMIT 1;");
    }

    /**
     * @param $id
     * @param $formId
     * @param $code
     */
    public function save($id, $formId, $code)
    {
        global $wpdb;

        $table = self::table();

        $wpdb->insert( $table, [
            'code'      => $code,
		    'form_id'   => $formId,
		    'detail_id' => $id,

        ], ['%d', '%d', '%s'] );
    }

    /**
     * @return string
     */
    public static function table()
    {
        global $wpdb;

        return $wpdb->prefix . self::$table;
    }

    /**
     * Clear old barcodes
     */
    private function clear()
    {
        $details = $this->getBarcodeDetails();
        $this->clearLeadDetail($details);
        $this->setDeletedStatus($details);
    }

    /**
     * @return array
     */
    private function getBarcodeDetails()
    {
        global $wpdb;

        $table = self::table();
        $prepared = $wpdb->prepare("SELECT `detail_id` FROM {$table} WHERE status = %d", 1);
        return $wpdb->get_col($prepared);
    }

    /**
     * @param array $ids
     */
    private function clearLeadDetail($ids=array())
    {
        global $wpdb;

        $lead_detail_table = GFFormsModel::get_lead_details_table_name();

        $wpdb->query("DELETE FROM {$lead_detail_table} WHERE id IN (". implode(',', $ids) .");");
    }

    /**
     * @param array $ids
     */
    private function setDeletedStatus($ids=array())
    {
        global $wpdb;

        $table = self::table();
        $wpdb->query("UPDATE {$table} SET status = 0 WHERE detail_id IN (". implode(',', $ids) .");");
    }
}