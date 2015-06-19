<?php

/**
 * @package consultation
 */
class Consultation extends UserDefinedForm {

	private static $db = array(
		'SVGIcon' => 'Text',
		'Tagline' => 'HTMLText'
	);

	private static $has_one = array(
		'CategoryImage' => 'Image'
	);

	private static $has_many = array(
		'Facts' => 'Consultation_Fact'
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldToTab('Root.Main', new HTMLEditorField('Tagline', 'Tagline - displayed on feedback pages'));
		$fields->addFieldToTab('Root.Main', new TextareaField('SVGIcon', 'SVG Icon'));
		$fields->addFieldToTab('Root.Main', new UploadField('CategoryImage', 'Category Image'));

		$config = GridFieldConfig_RelationEditor::create();
		$config->addComponent(new GridFieldOrderableRows('Sort'));

		$grid = new GridField('Facts', 'Facts', $this->Facts()->sort('Sort', 'ASC'), $config);

		$fields->addFieldToTab('Root.Figures', $grid);

		return $fields;	
	}



	public function EngagementOffset($radius = 45) {
		$percent = $this->getEngagementPercent();
		$c = pi() * ($radius * 2);

		$result = ((100 - $percent) / 100)* $c;
		$result = ceil($result);
		$rotate = -90;

		return new ArrayData(array(
			'Offset' => $result,
			'Rotate' => $rotate . 'deg'
		));
	}

	public function getEngagementPercent() {
		$comments = $this->Submissions()->Count();
		$allComments = SubmittedForm::get()->filter('IsConsultationSubmission', true)->count();

		if($allComments == 0) {
			return 0;
		}
		
		return ($comments / $allComments) * 100;
	}

	public function AllConsultations() {
		return Consultation::get();
	}

	public function Ranking() {
		$ideas = DB::query("
			SELECT ID, 
			(SELECT COUNT(*) FROM SubmittedForm WHERE ParentID = Consultation_Live.ID) AS Count
			FROM Consultation_Live
			ORDER BY Count DESC"
		)->map();

		$ranking = 0;

		foreach($ideas as $k => $v) {
			$ranking++;

			if($k == $this->ID) {
				return $ranking;
			}
		}

		return $ranking;
	}

	public function RankingOrd() {
		$locale = 'en_US';
		$nf = new NumberFormatter($this->Ranking, NumberFormatter::ORDINAL);
		
		return $nf->format($number);
	}

	public function HighPriorityPercentage() {
		$comments = $this->Submissions();

		if($comments->Count() < 1) {
			return 0;
		}

		$high = $comments->filter('PriorityResponse', 5);

		return round(($high->Count() / $comments->Count()) * 100);
	}

	public function TotalSubmissions() {
		return number_format($this->Submissions()->Count(), 0);
	}

	public function SupportPercentage() {
		$comments = $this->Submissions();

		if($comments->Count() < 1) {
			return 0;
		}

		$positive = $this->Submissions()->filter('SupportsIdea', true);

		return round(($positive->Count() / $comments->Count()) * 100);
	}
}

/**
 * @package consultation
 */

class Consultation_Controller extends UserDefinedForm_Controller {


}

/**
 * @package consultation
 */

class Consultation_Fact extends DataObject {

	private static $summary_fields = array(
		'Figure',
		'Content'
	);

	private static $db = array(
		'Figure' => 'Varchar',
		'Content' => 'Varchar',
		'Sort' => 'Int'
	);

	private static $has_one = array(
		'Consultation' => 'Consultation'
	);
}