<?php
/**
 * @package consultations
 */
class ConsultationsSummaryPage extends Page {

	private static $db = array();

	private static $description = "Displays summary information for consultations";

	public function Link($action = null, $clearSentiment = false) {
		$param = null;

		if(strpos($action, "=") !== false) {
			$param = explode("=", substr($action, strpos($action, '?') + 1));
			$action = substr($action, 0, strpos($action, '?'));
		}

		$link = parent::Link($action);
		$params = Controller::curr()->getRequest()->getVars();

		if(isset($params['url'])) {
			unset($params['url']);
		}

		if($clearSentiment) {
			if(isset($params['Sentiment'])) {
				unset($params['Sentiment']);
			}
		}

		if($param) {
			$params[$param[0]] = $param[1];
		}

		if($params) {
			return Controller::join_links($link, '?'. http_build_query($params));
		}

		return $link;
	}

	public function GenerateResyncLink($typeformUrl) {
		$page = Page::get()->filter('TypeformURL', $typeformUrl)->first();

		if($page) {
			$link = Controller::join_links($this->AbsoluteLink('resyncform'), $page->ID);
			$link = str_replace(array("http://", "https://"), "", $link);

			return $link;
		}
	}
}

/**
 * @package wcc_longtermplan
 */
class ConsultationsSummaryPage_Controller extends Page_Controller {
	
	private $consultation;

	private static $allowed_actions = array(
		'ConsultationSelectorForm',
		'FacetSearchForm',
		'summary',
		'comments',
		'rss',
		'comment'
	);

	public function init() {
		parent::init();

		RSSFeed::linkToFeed($this->Link('rss'), 'Recent submissions');
	}

	/**
	 * 
	 */
	public function rss() {
		$feed = new RSSFeed(
		$this->getFilteredSubmissions()->sort("Created", "DESC")->limit(20),
		$this->Link('rss'),
		'Latest submissions',
		null,
		null,
		null,
    		null,
    		null
		);

		return $feed->outputToBrowser();
	}

	public function comment() {
		$id = $this->request->param('ID');

		if($id) {
			$submission = SubmittedForm::get()->byId($id);

			if($submission) {
				return array(
					'Submission' => $submission
				);
			}
		}

		return $this->httpError(404);
	}

	/**
	 *
	 */
	public function summary() {
		$idea = $this->getIdea();

		if(!$idea) {
			return $this->httpError(404);
		}
	}

	/**
	 * 
	 */
	public function ResultsList() {
		$submissions = $this->getFilteredSubmissions();
		$list = new PaginatedList($submissions, $this->request);
		$list = $list->setPageLength(10);

		return $list;
	}

	public function ConsultationSelectorForm() {
		$ideas = Consultation::get();
		$ids = array();

		foreach($ideas as $idea) {
			if($idea->ClassName == "ConsultationCategory") {
				if($idea->Children()->Count() == 0) {
					$ids[$idea->ID] = $idea->Title;
				}
			} else {
				$ids[$idea->ID] = $idea->Title;
			}
		}

		$fields = new FieldList(
			$list = DropdownField::create('ConsultationID', 'Show feedback on', $ids),
			new HiddenField('Action', '', $this->request->param('Action'))
		);

		$list->setEmptyString('All Consultations');

		$actions = new FieldList(
			new FormAction('doConsultationSelectorForm', 'Go')
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
	public function doConsultationSelectorForm($data, $form) {
		$id = (isset($data['ConsultationID'])) ? (int) $data['ConsultationID'] : false;

		if(isset($data['Action'])) {
			if($data['Action'] == "comments") {
				return $this->redirect($this->Link('comments/?ConsultationID='. $id));
			} else if($data['Action'] == "map") {
				return $this->redirect($this->Link('map/?ConsultationID='. $id));
			}
			
			return $this->redirect($this->Link('?ConsultationID='. $id));
		}

		return $this->redirect($this->Link());
	}

	/**
	 * @return Consultation
	 */
	public function getConsultation() {
		if(!$this->consultation) {
			$id = $this->request->getVar('ConsultationID');

			if($id) {
				$this->consultation = Consultation::get()->byId($id);
			}
		}

		return $this->consultation;
	}

	/**
	 * @return DataList
	 */
	public function getSubmissions() {
		$consultation = $this->getConsultation();

		if($consultation) {
			$submissions = $consultation->Submissions();
		} else {
			$submissions = SubmittedForm::get()->filter('IsConsultationSubmission', true);
		}

		return $submissions;
	}

	public function getFilteredSubmissions() {
		$submissions = $this->getSubmissions();
		$request = $this->request;

		if($sentiment = $request->getVar('Sentiment')) {
			$finalMap = SiteConfig::current_site_config()->getSentiments();

			if(is_array($sentiment)) {
				$options = array();

				foreach($sentiment as $x) {
					if(strtolower($x) == "all") {
						continue;
					}

					$x = array_search($x, $finalMap);

					if($x !== false) {
						$x++;

						$options[$x] = $x;
					}
				}

				if($options) {
					$submissions = $submissions->filter('Sentiment', $options);
				}
			}
			else {
				if(strtolower($sentiment) == "all") {
					continue;
				}

				$x = array_search($sentiment, $finalMap) || 0;
				
				if($x !== false) {
					$x++;

					$submissions = $submissions->filter('Sentiment', $x);
				}
			}
		}

		$submissions = $submissions->sort('Created', $this->getCurrentSort());

		return $submissions;
	}

	public function getCurrentSort() {
		$sort = strtolower($this->request->getVar('Sort'));

		if(!$sort) {
			$sort = strtolower($this->request->getVar('sort'));
		}

		if($sort == 'asc' || $sort == 'desc') {
			return $sort;
		}

		return 'desc';
	}

	public function LatestComments() {
		$submissions = $this->getFilteredSubmissions();

		return $submissions->sort('Created', 'DESC')->Limit(10);
	}

	public function LatestPromotedComments() {
		return $this->LatestComments()->filter('PromotedSummary', 1);
	}

	public function getTotalSubmissions() {
		return number_format($this->getSubmissions()->count(), 0);
	}

	public function getTotalSupport() {
		$submissions = $this->getSubmissions();

		$total = $submissions->count();
		$support = $submissions->filter('SupportsOverallPlan', 1)->count();

		if($total) {
			return number_format(($support / $total) * 100, 0);
		}

		return 0;
	}

}