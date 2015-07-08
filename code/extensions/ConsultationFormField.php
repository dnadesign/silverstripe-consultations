<?php
/**
 * @package consultation
 */
class ConsultationFormField extends DataExtension {

	/**
	* Add an option to create a report (graph)
	* Add an option to include in the comment 
	*/
	public function updateFieldConfiguration (FieldList $fields) {

		$comment = CheckboxField::create($this->owner->getSettingName('CommentField'), _t('CONSULTATION.INCLUDEINCOMMENT', 'Include this field in comment post'), $this->owner->getSetting('CommentField'));
		$fields->push($comment);
	}

}