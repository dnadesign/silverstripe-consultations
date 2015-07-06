<?php

class ConsultationSubmittedField extends DataExtension {

	public function includeInComment() {
		$include = $this->owner->getEditableField()->getSetting('IncludeInComment');
		return filter_var($include, FILTER_VALIDATE_BOOLEAN);
	}


}