<?php

class ConsultationFormField extends DataExtension {

	public function updateFieldConfiguration (FieldList $fields) {

		$report = CheckboxField::create($this->owner->getSettingName('CreateReport'), _t('CONSULTATION.CREATEREPORT', 'Create a report for this field'), $this->owner->getSetting('CreateReport'));
		$fields->push($report);

		$comment = CheckboxField::create($this->owner->getSettingName('IncludeInComment'), _t('CONSULTATION.INCLUDEINCOMMENT', 'Include this field in comment post'), $this->owner->getSetting('IncludeInComment'));
		$fields->push($comment);
	}

}