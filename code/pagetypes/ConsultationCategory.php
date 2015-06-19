<?php

/**
 * @package consultations
 */
class ConsultationCategory extends Page {
	
	private static $default_child = 'Consultation';

	private static $description = "Optional category for grouping consultations";

	private static $db = array(
		'SVGIcon' => 'Text',
		'Tagline' => 'HTMLText'
	);

	private static $has_one = array(
		'CategoryImage' => 'Image'
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldToTab('Root.Main', new HTMLEditorField('Tagline', 'Tagline - displayed on feedback pages'));
		$fields->addFieldToTab('Root.Main', new TextareaField('SVGIcon', 'SVG Icon'));
		$fields->addFieldToTab('Root.Main', new UploadField('CategoryImage', 'Category Image'));

		return $fields;	
	}

	public function getSubmissions() {
		if($list = $this->AllChildren()->column('ID')) {
			return TypeformSubmission::get()->filter(array(
				'ParentID' => $list
			));
		}

		return new ArrayList();
	}

	public function EngagementOffset($radius = 45) {
		$percent = $this->getEngagementPercent();
		$c = pi() * ($radius * 2);

		$result = ((100 - $percent) / 100)* $c;
		$rotate = -90;

		return new ArrayData(array(
			'Offset' => $result,
			'Rotate' => $rotate . 'deg'
		));
	}

	public function getEngagementPercent() {
		$comments = $this->getSubmissions()->Count();
		$allComments = TypeformSubmission::get()->Count();
		
		if($allComments <= 0) {
			return 0;
		}
		
		return ($comments/$allComments) * 100;
	}
}

/**
 * @package consultation
 */

class ConsultationCategory_Controller extends Page_Controller {

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