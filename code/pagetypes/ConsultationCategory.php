<?php
/**
 * @package consultations
 */
class ConsultationCategory extends Page {
	
	private static $default_child = 'Consultation';

	private static $description = "Optional category for grouping consultations";

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

	// public function EngagementOffset($radius = 45) {
	// 	$percent = $this->getEngagementPercent();
	// 	$c = pi() * ($radius * 2);

	// 	$result = ((100 - $percent) / 100)* $c;
	// 	$rotate = -90;

	// 	return new ArrayData(array(
	// 		'Offset' => $result,
	// 		'Rotate' => $rotate . 'deg'
	// 	));
	// }

	// public function getEngagementPercent() {
	// 	$comments = $this->getSubmissions()->Count();

	// 	$allComments = SubmittedForm::get()->filter(array(
	// 		'IsConsultationSubmission' => 1
	// 	))->Count();
		
	// 	if($allComments <= 0) {
	// 		return 0;
	// 	}
		
	// 	return ($comments/$allComments) * 100;
	// }
}

/**
 * @package consultation
 */

class ConsultationCategory_Controller extends Page_Controller {

	/**
	* Redirect to the first consultation in this category
	* when linked directly
	*/
	public function init() {
		parent::init();

		if($this->AllChildren()->count() > 0) {
			$child = $this->Children()->First();

			if($child->ID !== $this->ID) {
				return $this->redirect($child->Link());
			}
		}
	}
 }