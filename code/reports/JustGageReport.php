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
		$json['title'] = $data->ReportTitle;
		$results = array();
		foreach($options as $option) {
			$json['max'] += $option['Value'];
			$results[$option['Label']] = $option['Value'];
			if ($option['Value'] > $json['value']) {
				$json['value'] = $option['Value'];
			} else {
				$json['min'] = $option['Value'];
			}
		}
		return json_encode($json);
	}	

}
