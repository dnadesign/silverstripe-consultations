<?php

/**
 * @package consultation
 */
class ConsultationsLandingPage extends Page {
	
	private static $default_child = 'ConsultationCategory';

	private static $description = "Holder for consultations and consultation categories";

	public function getCategories() {
		$categories = ClassInfo::subclassesFor('ConsultationCategory');
		return $this->Children()->filter('Classname', $categories);
	}

	public function getConsultations() {
		$consultations = ClassInfo::subclassesFor('Consultation');
		return $this->Children()->filter('Classname', $consultations);
	}

}

/**
 * @package consultation
 */
class ConsultationsLandingPage_Controller extends Page_Controller {}