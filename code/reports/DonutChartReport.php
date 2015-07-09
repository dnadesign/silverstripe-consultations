<?php

class DonutChartReport extends ConsultationReportType {

	protected $name = 'Donut Chart';
	protected $template = 'DonutChartreport';
	protected $requiresColours = true;

	public function render(Controller $controller, ArrayData $data) {
		// Require js library
		Requirements::javascript(CONSULTATION_MODULE_DIR . '/js/chart.js');
		Requirements::javascript(CONSULTATION_MODULE_DIR . '/js/doughnut.init.js');
		Requirements::css(CONSULTATION_MODULE_DIR . '/css/doughnut_report.css');
		// Customise data
		$data->setField('jsData', $this->jsData($data));
		// Perform rendering
		return $controller->customise($data)->renderWith($this->template);
	}

	public function jsData($data) {
		$options = $data->getField('Options')->toArray();
		$json = [];
		foreach($options as $option) {
			$json[] = array (
				'value' => $option['Value'],
				'label' => $option['Label'],
				'color' => $this->rand_color(),
				'highlight' => $this->rand_color()
			);
		}
		return json_encode($json);
	}	

	function rand_color() {
	    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
	}

}