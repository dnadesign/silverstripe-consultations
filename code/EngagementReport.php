<?php

class EngagementReport extends SS_Report {

	public function title() {
		return "Engagement Report";
	}
	
	public function sourceRecords($params, $sort, $limit) {
		$returnSet = Consultation::get();
		$result = array();

		foreach($returnSet as $page) {
			$result[$page->Ranking()] = $page;
		}
		ksort($result);
		$output = new ArrayList();

		foreach($result as $x) {
			$output->push($x);
		}


		return $output;
	}

	public function columns() {
		$fields = array(
			"Title" => array(
				"title" => 'Idea',
				'formatting' => function($value, $item) { 
					return sprintf(
						'<a href="%s">%s</a>',
						$item->Link(),
						$value
					);
				}
			),
			"Ranking" => array(
				"title" => 'Ranking',
				'formatting' => function($value, $item) { 
					return '#'.$value;
				}
			),
			"TotalSubmissions" => array(
				"title" => 'Total Submissions'
			),
			'SupportPercentage' => array(
				'title' => 'Supports Plan',
				'formatting' => function($value, $item) { 
					return $value . '%';
				}
			),
			'SupportsIdeaPercentage' => array(
				'title' => 'Supports Idea',
				'formatting' => function($value, $item) { 
					return $value . '%';
				}
			)
		);
		
		return $fields;
	}

	public function getReportField() {
		$field = parent::getReportField();
		$field->getConfig()->removeComponentsByType('GridFieldPaginator');

		return $field;
	}
	
}
