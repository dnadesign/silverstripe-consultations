<?php

class ConsultationReport extends DataObject {

	private static $db = array(
		'Title' => 'Varchar(255)'
	);

	private static $has_one = array(
		'Field' => 'EditableFormField',
		'Consultation' => 'Consultation',
		'Type' => 'ConsultationReportType'
	);

	private static $summary_fields = array(
		'Title' => 'Title',
		'Consultation.Title' => 'Consultation',
		'Field.Title' => 'Reports on',
		'Type.Name' => 'Type'
	);

	public function getCMSFields() {
		$fields = FieldList::create();

		$title = Textfield::create('Title', 'Title');
		$fields->push($title);

		if ($this->IsInDB()) {
			// Allowed Fields
			$allowed = $this->Consultation()->Fields()->filterByCallback(function ($field) {
				return $field->getHasAddableOptions();
			});
			$dropdown_field = DropdownField::create('FieldID', 'Field', $allowed->map())->setEmptyString('Select field to report on...');
			$fields->push($dropdown_field);

			// Report Types
			$types = ConsultationReportType::get()->map('ID', "Name");
			$dropdown_types = DropdownField::create('TypeID', 'Report Type', $types);
			$fields->push($dropdown_types);
		}
		else {
			$warning = LiteralField::create('Warning', 'Please save this component to edit it.');
			$fields->push($warning);
		}

		return $fields;
	}

	/**
	* Return an array containing the amount of submissions for each options
	*
	* @return array
	*/
	public function data() {
		if (!$this->Field()->exists() || 
			!$this->Consultation()->Submissions()->count() > 0) { 
			return; 
		}

		$results = [];

		$options = $this->Field()->Options();
		foreach($options as $option) {
			$result = SubmittedFormField::get()->filter(array('ParentID' => $this->Consultation()->Submissions()->column('ID'), 'Value' => $option->Title));
			$optionResult = [];
			$optionResult['Label'] = $option->Title;
			$optionResult['Value'] = $result->count();
			$results[] = $optionResult;
		}

		return $results;
	}

	/**
	* Return results as a json string
	*
	* @return json
	*/
	public function dataAsJson() {
		return json_encode($this->data());
	}

	/**
	* Return results as an ArrayData for template processing
	*
	* @return ArrayData
	*/
	public function dataAsArraydata() {
		return new ArrayData(array(
			'Options' => new ArrayList($this->data())
		));
	}

	/**
	* Render HTML
	*/
	public function render() {
		if (!$this->Type() || !$this->Type()->exists()) { return; }

		$template = $this->Type()->TemplateName;
		$controller = Controller::curr();

		$data = $this->dataAsArraydata();
		$data->setField('ReportTitle', $this->Title);
		$data->setField('ReportID', $this->IDHash());
		$data->setField('JsData', $this->Type()->jsData($this->data()));

		$html = $controller->customise($data)->renderWith($template);
		return $html; 
	}

	/**
	* Return a unique ID
	*/
	public function IDHash() {
		$hash = $this->Consultation()->ID.$this->Field()->ID.$this->ID;
		return 'Report'.$hash;
	}


}