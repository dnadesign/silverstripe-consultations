<?php
/**
 * @package consultation
 */
class ConsultationFormField extends DataExtension {

	/**
	* Add an option to include in the comment 
	*/
	public function updateFieldConfiguration (FieldList $fields) {

		if (in_array($this->owner->Parent()->Classname, ClassInfo::subclassesFor('Consultation'))) {

			$comment = CheckboxField::create($this->owner->getSettingName('CommentField'), _t('CONSULTATION.INCLUDEINCOMMENT', 'Include this field in comment post'), $this->owner->getSetting('CommentField'));
			$fields->push($comment);
		}

	}

}