<?php
/**
 * @package consultation
 */
class ConsultationSubmittedForm extends DataExtension
{

    private static $db = array(
        'IsConsultationSubmission' => 'Boolean'
    );

    public function onBeforeWrite()
    {
        if ($parent = $this->owner->Parent()) {
            $this->owner->IsConsultationSubmission = ($parent instanceof Consultation);
        }
    }

    /**
    * Return all fields to include in a comment and their value
    *
    * @return SubmittedFormField
    */
    public function getCommentFields()
    {
        $fields = $this->owner->Values()->filterByCallback(function ($field) {
            return $field->isCommentField();
        });
        return $fields;
    }

    /**
    * Return all fields that would generate a report
    *
    * @return SubmittedFormField
    */
    public function getReportFields()
    {
        $fields = $this->owner->Values()->filterByCallback(function ($field) {
            return $field->isReportField();
        });
        return $fields;
    }
}
