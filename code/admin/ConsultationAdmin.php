<?php
/**
 * @package consultation
 */
class ConsultationAdmin extends ModelAdmin {

    private static $managed_models = array(
        'Consultation'
    );

    private static $url_segment = 'consultations';
    private static $menu_title = 'Consultations';

    public function getEditForm($id = null, $fields = null) {
        $form = parent::getEditForm($id , $fields);

        $listfield = $form->Fields()->fieldByName($this->modelClass);
        $listFieldConfig = $listfield->getConfig();

        // remove add new
        $listFieldConfig->removeComponentsByType('GridFieldAddNewButton');

        // remove print button
        $listFieldConfig->removeComponentsByType('GridFieldPrintButton');

        return $form;
    }

}