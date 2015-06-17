<?php

/**
 * @package wcc_longtermplan
 */
class ResultSummaryPage extends Page {

	private static $db = array(
		'InTheFuture' => 'Boolean'
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Main', new CheckboxField('InTheFuture'));

		return $fields;
	}

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
class ResultSummaryPage_Controller extends Page_Controller {
	
	private $idea;

	private static $allowed_actions = array(
		'IdeaSelectorForm',
		'FacetSearchForm',
		'summary',
		'comments',
		'map',
		'rss',
		'kmlfile',
		'comment',
		'resyncform'
	);

	public function init() {
		parent::init();

		RSSFeed::linkToFeed($this->Link('rss'), 'Recent submissions');
	}

	public function resyncform() {
		$id = $this->request->param('ID');

		if($id) {
			$page = Page::get()->byId($id);

			if($page) {
				$uid = $page->getTypeformUid();

				if($uid) {
					$fetch = new SyncTypeformSubmissions_Single($uid);
					$results = $fetch->syncComments($page);
				}

				Config::inst()->update('RebuildStaticCacheTask', 'quiet', true);
				$rebuild = new RebuildStaticCacheTask();
				$rebuild->rebuildCache($page->pagesAffectedByChanges(), false);

				if($page->ClassName == "InTheFuturePage") {
					return $this->redirect($page->AbsoluteLink('comments'));
				} else {
					return $this->redirect(Controller::join_links($this->Link(), '?IdeaID='. $id));
				}
			}
		}

		return $this->httpError(400);
	}

	/**
	 * 
	 */
	public function rss() {
		$feed = new RSSFeed(
    		$this->getFilteredSubmissions()->sort("Created", "DESC")->limit(20),
    		$this->Link('rss'),
    		'Latest Submissions',
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
			$submission = TypeformSubmission::get()->byId($id);

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

	public function map() {

		return array();
	}

	public function getSuburbs() {
		$request = $this->getRequest();
		$suburbs = SiteConfig::current_site_config()->getSuburbs();
		$output = new ArrayList();

		if($suburb = $request->getVar('Suburbs')) {
			if(is_array($suburb)) {
				$options = array();

				foreach($suburb as $x) {
					if(strtolower($x) == "all") {
						continue;
					}

					if(isset($suburbs[$x - 1])) {
						$output->push(new ArrayData(array(
							'Title' => $suburbs[$x - 1]
						)));
					}
				}
			}
			else if(isset($suburbs[$suburb - 1])) {
				$output->push(new ArrayData(array(
					'Title' => $suburbs[$suburb]
				)));
			}
		}

		return $output;
	}

	/**
	 * 
	 */
	public function kmlfile() {
		$this->response->addHeader('Content-Type','application/vnd.google-earth.kml+xml');

		return $this->customise(new ArrayData(array(
			'Suburbs' => $this->getKmlDataSet()
		)))->renderWith('ResultSummaryPageKml');
	}

	public function  getKmlDataSet() {
		$suburbs = SiteConfig::current_site_config()->getSuburbs();
		$output = new ArrayList();

		$data = array();
		if($idea = $this->getIdea()) {
			$q = "SELECT Suburb, SupportsIdea, COUNT(*) AS Count FROM TypeformSubmission WHERE ParentID = '$idea->ID' GROUP BY Suburb, SupportsIdea";
		} else {
			$q = "SELECT Suburb, SupportsIdea, COUNT(*) AS Count FROM TypeformSubmission GROUP BY Suburb, SupportsIdea";
		}
		$sql = DB::query($q);

		while($record = $sql->nextRecord()) {
			if(!isset($data[$record['Suburb']])) {
				$data[$record['Suburb']] = array();
			}
			
			$data[$record['Suburb']][$record['SupportsIdea']] = $record['Count'];
		}

		$colors = array(
			'2f2020', // bbggrr
			'847575',
			'bee3ea',
			'66ecff',
			'00d5ff'
		);

		foreach($suburbs as $k => $suburb) {
			if(isset($data[$suburb])) {
				$total = array_sum($data[$suburb]);

				$positive = (isset($data[$suburb][1])) ? $data[$suburb][1] : 0;
				$negative = (isset($data[$suburb][0])) ? $data[$suburb][0] : 0;

				$percent = $positive / $total;

				if($percent > 0.7) {
					$line = '9a'. $colors[4];
					$poly = 'ee'. $colors[4];
				} else if($percent > 0.6) {
					$line = '9a'. $colors[3];
					$poly = 'ee'. $colors[3];
				} else if($percent >= 0.5) {
					$line = '9a'. $colors[2];
					$poly = 'ee'. $colors[2];
				} else if($percent > 0.35) {
					$line = '9a'. $colors[1];
					$poly = 'ee'. $colors[1];
				} else {
					$line = '9a'. $colors[0];
					$poly = 'ee'. $colors[0];
				}
			} else {
				$total = 0;
				$positive = 0;
				$negative = 0;
				$percent = 0;
				$line = 'bbffffff';
				$poly = 'bbffffff';
			}

			$link = $this->dataRecord->Link('comments?Suburbs['. ($k + 1) .']='. ($k + 1), true);
			$output->push(new ArrayData(array(
				'Title' => $suburb,
				'Key' => str_replace(array(' '), '', strtolower($suburb)),
				'Color' => $line,
				'Link' => $link, 
				'LineColor' => $line,
				'PolyColor' => $poly,
				'Percentage' => number_format($percent * 100, 0),
				'TotalSubmissions' => $total,
				'PositiveSubmissions' => $positive,
				'NegativeSubmissions' => $negative,
			)));
		}

		return $output;
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


	public function FacetSearchForm() {
		$all = $this->getSubmissions()->Count();

		if($idea = $this->getIdea()) {
			$suburbCounts = DB::query("
				SELECT Suburb, COUNT(*) FROM TypeformSubmission WHERE ParentID = ". $idea->ID ." GROUP BY Suburb"
			)->map();

			$sentimentCounts = DB::query("
				SELECT Sentiment, COUNT(*) FROM TypeformSubmission WHERE ParentID = ". $idea->ID ." GROUP BY Sentiment"
			)->map();
		} else if($this->ClassName == "InTheFuturePage") {
			$suburbCounts = DB::query("
				SELECT Suburb, COUNT(*) FROM TypeformSubmission WHERE InTheFuture = 1 GROUP BY Suburb"
			)->map();

			$sentimentCounts = DB::query("
				SELECT Sentiment, COUNT(*) FROM TypeformSubmission WHERE InTheFuture = 1 GROUP BY Sentiment"
			)->map();
		} else {
			$suburbCounts = DB::query("
				SELECT Suburb, COUNT(*) FROM TypeformSubmission WHERE InTheFuture = 0 GROUP BY Suburb"
			)->map();

			$sentimentCounts = DB::query("
				SELECT Sentiment, COUNT(*) FROM TypeformSubmission WHERE InTheFuture = 0 GROUP BY Sentiment"
			)->map();
		}

		$suburbs = array(
			'All' => 'All ('. $all .')'
		);

		foreach(SiteConfig::current_site_config()->getSuburbs() as $k => $v) {
			if(isset($suburbCounts[$v])) {
				$suburbs[count($suburbs)] = $v . ' ('. $suburbCounts[$v] . ')';
			} else {
				$suburbs[count($suburbs)] = $v . ' (0)';
			}
		}

		$sentiments = SiteConfig::current_site_config()->getSentiments();
		$parsedSentiments = array(
			'All' => 'All ('. $all .')'
		);

		foreach($sentiments as $k => $sentiment) {
			$count = (isset($sentimentCounts[$k + 1])) ? $sentimentCounts[$k + 1] : '0';

			$parsedSentiments[$sentiment] = $sentiment . ' ('. $count .')';
		}

		$fields = new FieldList(
			new HiddenField('Sort', '', $this->getCurrentSort()),
			new HiddenField('Action', '', $this->getAction()),
			new HiddenField('IdeaID', '', ($idea = $this->getIdea()) ? $idea->ID : null),
			$sentiment = new FieldGroup(
				HeaderField::create('SentimentHeading', 'Sentiment', 6),
				new CheckboxSetField('Sentiment', '', $parsedSentiments, $this->request->getVar('Sentiment'))
			),
			new FieldGroup(
				HeaderField::create('Location', 'Suburb', 6),
				$burbs = new CheckboxSetField('Suburbs', '', $suburbs, $this->request->getVar('Suburbs'))
			)
		);

		$sentiment->addExtraClass('sentiment-selector');

		$actions = new FieldList(
			new FormAction('doFacetSearchForm', 'Update')
		);

		$form = new Form($this, 'FacetSearchForm', $fields, $actions);
		$form->setFormMethod('GET');
		$form->disableSecurityToken();

		return $form;
	}

	public function doFacetSearchForm($data, $form) {
		$action = (isset($data['Action'])) ? $data['Action'] : 'comments';

		$params = array();

		if(isset($data['Sort'])) {
			$params['Sort'] = $data['Sort'];
		}

		if(isset($data['Suburbs']) && $data['Suburbs']) {
			$params['Suburbs'] = $data['Suburbs'];
		}
		
		if(isset($data['Sentiment']) && $data['Sentiment']) {
			$params['Sentiment'] = $data['Sentiment'];
		}

		return $this->redirect(
			Controller::join_links($this->Link($action), '?'. http_build_query($params))
		);
	}

	public function IdeaSelectorForm() {
		$ideas = BigIdeaPage::get();
		$ids = array();

		foreach($ideas as $idea) {
			if($idea->ClassName == "BigIdeaCategoryPage") {
				if($idea->Children()->Count() == 0) {
					$ids[$idea->ID] = $idea->Title;
				}
			} else {
				$ids[$idea->ID] = $idea->Title;
			}
		}

		$fields = new FieldList(
			$list = DropdownField::create('IdeaID', 'Show feedback on', $ids),
			new HiddenField('Action', '', $this->request->param('Action'))
		);

		$list->setEmptyString('All Ideas');

		$actions = new FieldList(
			new FormAction('doIdeaSelectorForm', 'Go')
		);

		$form = new Form($this, 'IdeaSelectorForm', $fields, $actions);
		$form->loadDataFrom($_GET);
		$form->disableSecurityToken();

		return $form;
	}

	/**
	 * @param array $data
	 * @param Form $form
	 */
	public function doIdeaSelectorForm($data, $form) {
		$id = (isset($data['IdeaID'])) ? (int) $data['IdeaID'] : false;

		if(isset($data['Action'])) {
			if($data['Action'] == "comments") {
				return $this->redirect($this->Link('comments/?IdeaID='. $id));
			} else if($data['Action'] == "map") {
				return $this->redirect($this->Link('map/?IdeaID='. $id));
			}
			
			return $this->redirect($this->Link('?IdeaID='. $id));
		}

		return $this->redirect($this->Link());
	}


	public function getIdea() {
		if(!$this->idea) {
			$id = $this->request->getVar('IdeaID');

			if($id) {
				$this->idea = BigIdeaPage::get()->byId($id);
			}
		}

		return $this->idea;
	}

	public function getSubmissions() {
		$idea = $this->getIdea();

		if($idea) {
			$submissions = $idea->getSubmissions();
		} else {
			// in the future
			$submissions = TypeformSubmission::get()->exclude('InTheFuture', 1);
		}

		return $submissions;
	}

	public function getFilteredSubmissions() {
		$submissions = $this->getSubmissions();
		$request = $this->request;

		$suburbs = SiteConfig::current_site_config()->getSuburbs();

		if($suburb = $request->getVar('Suburbs')) {
			if(is_array($suburb)) {
				$options = array();

				foreach($suburb as $x) {
					if(strtolower($x) == "all") {
						continue;
					}

					if(isset($suburbs[$x - 1])) {
						$options[] = $suburbs[$x - 1];
					}
				}

				if($options) {
					$submissions = $submissions->filter('Suburb', $options);
				}
			}
			else if(isset($suburbs[$suburb - 1])) {
				$submissions = $submissions->filter('Suburb', $suburbs[$suburb]);
			}
		}

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

		$submissions = $submissions->sort('DateSubmitted', $this->getCurrentSort());

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

		return $submissions->sort('DateSubmitted', 'DESC')->Limit(10);
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