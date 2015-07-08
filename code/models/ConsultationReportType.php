<?php

class ConsultationReportType extends DataObject {

	private static $db = array(
		'Name' => 'Varchar(255)',
		'TemplateName' => 'Varchar(255)'
	);

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();

		$donut = new ConsultationReportType();
		$donut->Name = 'Donut Chart';
		$donut->TemplateName = 'DonutChartReport';
		$donut->write();
		DB::alteration_message('Donut Chart report type created', 'created');
	}

	public function jsData($data) {
		$json = [];
		foreach($data as $entry) {
			$json[] = array (
				'value' => $entry['Value'],
				'label' => $entry['Label'],
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
