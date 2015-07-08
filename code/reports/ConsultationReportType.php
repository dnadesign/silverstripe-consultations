<?php

abstract class ConsultationReportType {

	protected $name;
	protected $template;
	protected $extraConfig;

	abstract public function render(Controller $controller, ArrayData $data);

	public function getName() {
		return $this->name;
	}

	public function getTemplateName() {
		return $this->template;
	}

	public function getExtraConfig() {
		return $this->extraConfig;
	}
}
