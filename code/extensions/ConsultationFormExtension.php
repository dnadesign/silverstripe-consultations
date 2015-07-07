<?php
/**
 * @package consultation
 */
class ConsultationFormExtension extends Extension {

	public function updateForm ($form) {
		$form->addExtraClass('consultation-form');
		
		if($this->owner->data()->isOpen()) {
			$form->addExtraClass('consultation-form__active');
		} else if (!$this->owner->data()->hasStarted()) {
			$form->addExtraClass('consultation-form__pending');
			$this->owner->disable($form);
		} else if ($this->owner->data()->hasSExpired()) {
			$form->addExtraClass('consultation-form__expired');
			$this->owner->disable($form);
		}
	}

	public function disable($form) {
		$fields = $form->Fields();
		foreach($fields as $field) {
			$field->setAttribute('disabled', 'disabled');
		}
	}

}