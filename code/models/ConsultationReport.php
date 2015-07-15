<?php

class ConsultationReport extends DataObject {

	private static $db = array(
		'Title' => 'Varchar(255)',
		'Type' => 'Varchar(255)'
	);

	private static $has_one = array(
		'Field' => 'EditableFormField',
		'Consultation' => 'Consultation'		
	);

	private static $summary_fields = array(
		'Title' => 'Title',
		'Consultation.Title' => 'Consultation',
		'Field.Title' => 'Reports on'
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
			$dropdown_types = DropdownField::create('Type', 'Type', $this->getAllowedReportTypes());
			$fields->push($dropdown_types);

			$fields->push(ColorField::create('Colour', 'Colour'));
		}
		else {
			$warning = LiteralField::create('Warning', 'Please save this component to edit it.');
			$fields->push($warning);
		}

		return $fields;
	}

	private function getAllowedReportTypes() {
		$map = [];
		$types = $this->Consultation()->Config()->get('allowed_reports');

		foreach($types as $type) {
			if (class_exists($type)) {
				$map[$type] = singleton($type)->getName();
			}
			// $map[$type] = FormField::name_to_label($type);
		}
		return $map;
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
			$parentID = $option->ParentID;
			$result = SubmittedFormField::get()->filter(
				array(
					'ParentID' => $this->Consultation()->Submissions()->column('ID'),
					// confirm the value actually belongs to the correct form field
					'Name' => "EditableDropdown$parentID",
					'Value' => $option->Title
				)
			);
			$optionResult = [];
			$optionResult['Label'] = $option->Title;
			$optionResult['Value'] = $result->count();
			$results[] = $optionResult;
		}

		return $results;
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
	public function generate() {
		if (!$this->Type || !class_exists($this->Type)) { return; }	

		$data = $this->dataAsArraydata();
		$data->setField('ReportTitle', $this->Title);
		$data->setField('ReportID', $this->IDHash());

		$reportType = singleton($this->Type);
		return $reportType->render(Controller::curr(), $data);
	}

	/**
	* Return a unique ID
	*/
	public function IDHash() {
		$hash = $this->Consultation()->ID.$this->Field()->ID.$this->ID;
		return 'Report'.$hash;
	}


}
