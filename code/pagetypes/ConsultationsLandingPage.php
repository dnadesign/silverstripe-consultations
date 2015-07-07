<?php
/**
 * @package consultation
 */
class ConsultationsLandingPage extends Page {
	
	private static $default_child = 'ConsultationCategory';

	private static $description = "Holder for consultations and consultation categories";

	/**
	* Return categories that exists directly under the landing page
	*
	* @return DataList
	*/
	public function getCategories() {
		$categories = ClassInfo::subclassesFor('ConsultationCategory');
		return $this->Children()->filter('Classname', $categories);
	}

	/**
	* Return consultations that exists directly under the landing page
	*
	* @return DataList
	*/
	public function getConsultations() {
		$consultations = ClassInfo::subclassesFor('Consultation');
		return $this->Children()->filter('Classname', $consultations);
	}

}

/**
 * @package consultation
 */
class ConsultationsLandingPage_Controller extends Page_Controller {}