<?php

/**
 * @package consultation
 */
class Consultation extends Page {

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
		$config->addComponent(new GridFieldSortableRows('Sort'));

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
		$comments = $this->getSubmissions()->Count();
		$allComments = TypeformSubmission::get()->Count();

		if($allComments == 0) {
			return 0;
		}
		
		return ($comments/$allComments) * 100;
	}

	public function Ranking() {
		$ideas = DB::query("
			SELECT ID, 
			(SELECT COUNT(*) FROM TypeformSubmission WHERE ParentID = BigIdeaPage_Live.ID) AS Count
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
		$comments = $this->getSubmissions();

		if($comments->Count() < 1) {
			return 0;
		}

		$high = $comments->filter('PriorityResponse', 5);

		return round(($high->Count() / $comments->Count()) * 100);
	}

	public function TotalSubmissions() {
		return number_format($this->getSubmissions()->Count(), 0);
	}

	public function SupportPercentage() {
		$comments = $this->getSubmissions();

		if($comments->Count() < 1) {
			return 0;
		}

		$positive = $this->getSubmissions()->filter('SupportsOverallPlan', true);

		return round(($positive->Count() / $comments->Count()) * 100);
	}

	public function SupportsIdeaPercentage() {
		$comments = $this->getSubmissions();

		if($comments->Count() < 1) {
			return 0;
		}

		$positive = $this->getSubmissions()->filter('SupportsIdea', true);

		return round(($positive->Count() / $comments->Count()) * 100);
	}

	public function getSubmissions() {
		return TypeformSubmission::get()->filter('ParentID', $this->ID);
	}
}

/**
 * @package consultation
 */

class Consultation_Controller extends Page_Controller {

	private static $allowed_actions = array(
		'index',
		'completed'
	);

	/**
	 * When the big idea is completed, reload and fetch the results in the background.
	 */
	public function completed() {
		if($key = $this->getTypeformUid()) {
			// sync latest comments to the page
			$sync = new SyncTypeformSubmissions_Single($key);
			$sync->syncComments($this->getRecord(), true, $this->getSubmissions()->Count());

			// update page rankings
			$update = new UpdateRankings();
			$update->run($this->request);
		}

		$resultsSummary = ResultSummaryPage::get()->first();

		return $this->redirect($resultsSummary->Link('summary/'. $this->URLSegment .'/'));
	}
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
		'Wide' => 'Boolean',
		'Sort' => 'Int'
	);

	private static $has_one = array(
		'Consultation' => 'Consultation'
	);
}