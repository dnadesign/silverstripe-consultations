<?php

class JustGageReport extends ConsultationReportType {

	protected $name = 'Just Gage';
	protected $template = 'JustGageReport';
	protected $requiresColours = true;

	public function render(Controller $controller, ArrayData $data) {
		// Require js library
		Requirements::javascript(CONSULTATION_MODULE_DIR . '/js/justGage/raphael.2.1.0.min.js');
		Requirements::javascript(CONSULTATION_MODULE_DIR . '/js/justGage/justgage.1.0.1.min.js');
		// Customise data
		$data->setField('jsData', $this->jsData($data));
		// Perform rendering
		return $controller->customise($data)->renderWith($this->template);
	}

	public function jsData($data) {
		$options = $data->getField('Options')->toArray();
		$json['id'] = 'gauge';
		$json['min'] = 0;
		$json['value'] = 0;
		$json['max'] = 0;
		$results = array();
		foreach($options as $option) {
			$json['max'] += $option['Value'];
			$results[$option['Label']] = $option['Value'];
			if ($option['Value'] > $json['value']) {
				$json['value'] = $option['Value'];
			}
		}
		// show the winning vote as the title
		$json['title'] = array_search(max($results), $results);

		return json_encode($json);
	}	

}
