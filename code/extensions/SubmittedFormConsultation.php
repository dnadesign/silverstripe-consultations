<?php

class SubmittedFormConsultation extends DataExtension {

	private static $db = array(
		'IsConsultationSubmission' => 'Boolean',
		'SupportsIdea' => 'Boolean',
		'PromotedSummary' => 'Boolean'
	);

	public function onBeforeWrite() {
		if($parent = $this->owner->Parent()) {
			if($parent instanceof Consultation) {
				$this->owner->IsConsultationSubmission = true;
			} else {
				$this->owner->IsConsultationSubmission = false;
			}
		}
	}
}