<?php

/**
 * @package consultation
 */
class ConsultationsLandingPage extends Page {
	
	private static $default_child = 'ConsultationCategory';

	public function getCategories() {
		return $this->Children()->filterByCallback(function ($page) {
			return (is_a($page, 'ConsultationCategory') || is_subclass_of($page, 'ConsultationCategory'));
		});
	}

}

/**
 * @package consultation
 */
class ConsultationsLandingPage_Controller extends Page_Controller {

}