<?php
/**
 * @package consultation
 */
class Consultation extends UserDefinedForm {

	private static $db = array(
		'Starts' => 'Date',
		'Expires' => 'Date'
	);

	private static $summary_fields = array(
		'ID' => 'ID',
		'Title' => 'Title',
		'getCategoryName' => 'Category',
		'getparticipation' => 'Participation',
		'getGlobalPopularityAsString' => 'Global Popularity',
		'getPopularityInCatgeoryAsString' => 'Popularity in catgory',
		'getStatus' => 'Status'
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$embargo = ToggleCompositeField::create('Embargo', 'Embargo',array(
			DateField::create('Starts', 'Allow participation from')
			->setRightTitle('Optional. If left blank, participation starts when page is published')
			->setConfig('showcalendar', true),
			DateField::create('Expires', 'Until')
			->setRightTitle('Optional. If left blank, participation will end when page is unpublished.')
			->setConfig('showcalendar', true)
		))->setStartClosed(false);

		$fields->addFieldToTab('Root.FormOptions', $embargo, 'SubmitButtonText');
		return $fields;
	}

	/**
	* Return whether this onsultation is open for participation
	*
	* @return Boolean
	*/
	public function isOpen() {
		return ($this->hasStarted() && !$this->hasExpired());
	}

	public function hasStarted() {
		$now = strtotime('now');		

		if ($this->Starts) {
			$start = strtotime($this->Starts);
			return ($now > $start);
		}
		return true;
	}

	public function hasExpired() {
		$now = strtotime('now');		

		if ($this->Expires) {
			$end = strtotime($this->Expires);
			return ($now > $end);
		}
		return false;
	}

	/**
	* Return whether the participation is opened
	* for gridfield summary
	*
	* @return HTMLText
	*/
	public function getStatus() {
		$colours = array('red', 'green', 'blue');

		$colour = (!$this->hasStarted()) ? $colours[2] : $colours[(int)$this->isOpen()];
		$message = '';
		// Not started yet
		if (!$this->hasStarted()) {
			$date = new DateTime($this->Starts);
			$message = 'Starting on '.$date->format('d-m-Y');
		} 
		// Active / Epxpired
		else  { 
			$message = ($this->hasExpired()) ? 'Expired' : 'Active';
		}

		$output = sprintf('<span style="color:%s">%s</span>', $colour, $message);
		$field = HTMLText::create('Status');
		$field->setValue($output);
		return $field;
	} 

	/**
	* Return the parent category
	*
	* @return ConsultationCategory
	*/
	public function getCategory() {
		if ($this->Parent()){
			$categories = ClassInfo::subclassesFor('ConsultationCategory');
			if (in_array($this->Parent()->ClassName, $categories)) {
				return $this->Parent();
			}
		}		
		return null;
	}

	/**
	* Return the parent category name
	*
	* @return String
	*/
	public function getCategoryName() {
		if ($category = $this->getCategory()) {
			return $category->MenuTitle;
		}
		return null;
	}

	/**
	* Return the position of this consultation
	* compared with the amount of submission all other consultations have
	*
	* @return Int
	*/
	public function getGlobalPopularity() {
		$consultations = self::get()
			->sort("(SELECT COUNT(ID) FROM SubmittedForm WHERE ParentID = UserDefinedForm.ID)")
			->reverse();

		$rank = (int) array_search($this->ID, $consultations->column('ID')) + 1;
		return $rank; 
	}

	/**
	* Return the position of this consultation
	* compared with the amount of submission all other consultations have
	* in a position / number consultation string format
	*
	* @return String
	*/
	public function getGlobalPopularityAsString() {
		$consultations = self::get()->count();
		return (string) $this->getGlobalPopularity() .' / '.$consultations;
	}

	/**
	* Return the position of this consultation
	* compared with the amount of submission all other consultations have
	* within the same category
	*
	* @return Int
	*/
	public function getPopularityInCategory() {
		$category = $this->getCategory();
		if (!$category) { return; }

		$consultations = self::get() 
			->filter('ParentID', $category->ID)
			->sort("(SELECT COUNT(ID) FROM SubmittedForm WHERE ParentID = UserDefinedForm.ID)")
			->reverse();

		$rank = (int) array_search($this->ID, $consultations->column('ID')) + 1;
		return $rank; 
	}

	/**
	* Return the position of this consultation
	* compared with the amount of submission all other consultations have
	* within the same category
	* in a position / number consultation string format
	*
	* @return String
	*/
	public function getPopularityInCatgeoryAsString() {
		$category = $this->getCategory();
		if (!$category) { return; }

		$consultations = $category->getConsultations()->count();
		return (string) $this->getPopularityInCategory() .' / '.$consultations;
	}

	/**
	* Return the number of submission for this cosultation
	*
	* @return Int
	*/
	public function getParticipation() {
		return $this->Submissions()->count();
	}

}

/**
 * @package consultation
 */

class Consultation_Controller extends UserDefinedForm_Controller {

	public function init() {
		parent::init(); 
		Requirements::css(CONSULTATION_MODULE_DIR . '/css/consultations.css');
	}
}

