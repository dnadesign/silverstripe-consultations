<?php
/**
 * @package consultation
 */
class ConsultationFormExtension extends Extension {

	public function updateForm ($form) {
		$form->addExtraClass('consultation-form');
	}

}