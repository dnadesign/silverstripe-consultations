<?php

class ConsultationSubmission extends DataExtension {

	public function getCommentFields() {
		$fields = $this->owner->Values()->filterByCallback(function($field) {
			return $field->includeInComment();
		});
	}


}