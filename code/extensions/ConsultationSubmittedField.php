<?php
/**
 * @package consultation
 */
class ConsultationSubmittedField extends DataExtension {

	/**
	* Check if Submitted Form Field should be include in comment
	*
	* @return Boolean
	*/
	public function isCommentField() {
		$setting = $this->owner->getEditableField()->getSetting('CommentField');
		return filter_var($setting, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	* Check if Submitted Form Field should be generate a report (graph)
	*
	* @return Boolean
	*/
	public function isReportField() {
		$setting = $this->owner->getEditableField()->getSetting('ReportField');
		return filter_var($setting, FILTER_VALIDATE_BOOLEAN);
	}


}