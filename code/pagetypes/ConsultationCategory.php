<?php
/**
 * @package consultations
 */
class ConsultationCategory extends Page {
	
	private static $default_child = 'Consultation';

	private static $description = "Optional category for grouping consultations";

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		// Remove content since this page redirects to first consultation
		$fields->removeByName('Content');

		return $fields;
	}

	/**
	* Return all consultation in this category
	*
	* @return DataList
	*/
	public function getConsultations() {
		$consultationTypes = ClassInfo::subclassesFor('Consultation');
		return $this->AllChildren()->filter('Classname', $consultationTypes);		
	}

	/**
	* Return all submissions in this category
	*
	* @return DataList || empty ArrayList
	*/
	public function getSubmissions() {		
		if ($consultations = $this->getConsultations() && $consultations->count() > 0) {
			return SubmittedForm::get()->filter(array('ParentID' => $list));
		}
		return new ArrayList();
	}

}

/**
 * @package consultation
 */

class ConsultationCategory_Controller extends Page_Controller { }