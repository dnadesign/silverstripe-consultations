<?php
/**
 * @package consultations
 */
class ConsultationsSummaryPage extends Page {

	private static $db = array();

	private static $description = "Displays summary information for consultations";
	
}

/**
 * @package consultations
 */
class ConsultationsSummaryPage_Controller extends Page_Controller {
	
	private $current_consultation;

	private static $allowed_actions = array(
		'ConsultationSelectorForm'
	);

	public function init() {
		parent::init();
		$this->setCurrentConsultation();
	}

	/**
	* Return currently selected consultation
	*
	* @return Consultation | null
	*/
	public function getCurrentConsultation() {
		return $this->current_consultation;
	}

	/**
	* Set current consultation
	*/
	public function setCurrentConsultation($id = null) {
		if ($id || $id = $this->request->getVar('ConsultationID')) {
			$this->current_consultation = Consultation::get()->byID($id);
		}
		else {
			$this->current_consultation = null;
		}
	}

	/**
	* Return Dropdown form to select consultation
	*
	* @return Form
	*/
	public function ConsultationSelectorForm() {
		$consultations = Consultation::get();

		$fields = new FieldList(
			$list = DropdownField::create('ConsultationID', 'Show feedback on', $consultations->map())
					->setEmptyString('All Consultations')
		);

		$actions = new FieldList(
			new FormAction('filterConsultation', 'Go')
		);

		$form = new Form($this, __FUNCTION__, $fields, $actions);
		$form->loadDataFrom($_GET);
		$form->disableSecurityToken();

		return $form;
	}

	/**
	 * @param array $data
	 * @param Form $form
	 */
	public function filterConsultation($data, $form) {
		$id = (isset($data['ConsultationID'])) ? (int) $data['ConsultationID'] : false;
		return $this->redirect($this->Link('?ConsultationID='. $id));
	}

	/**
	* Return data for template processing
	*
	* @return ArrayData
	*/
	public function getConsultationSummary() {
		$current = $this->getCurrentConsultation();
		$consultation = ($current) ? $current :  Consultation::getMostPopular();

		return new ArrayData(array(
			'isDetail' => ($current && $current->exists()),
			'Consultation' => $consultation,
			'ConsultationSummaryLink' => $this->Link('?ConsultationID='. $consultation->ID),
			'Participation' => ($current) ? $current->getParticipation() : Consultation::getAllSubmissions()->count()
		));
	}

	/**
	* Return All submissions
	*
	* @return ArrayData
	*/
	public function getComments() {
		$current = $this->getCurrentConsultation();
		return ($current) ? $current->Submissions() : Consultation::getAllSubmissions();
	}

}