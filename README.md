# SilverStripe Consultations

## Maintainer Contact

* Will Rossiter (Nickname: wrossiter, willr) <will.rossiter@dna.co.nz>

## Requirements

* SilverStripe 3.1
* SilverStripe GridFieldExtensions
* SilverStripe UserForms

## Documentation

This module provides a boilerplate setup for publishing online consultations and
displaying feedback.

**This work has been open sourced from http://our10yearplan.co.nz and still
opinionated and an early release.**

## Installation

	composer require "dnadesign/silverstripe-consultations"

## Functionality
This module provides the models for `Consultation`. Each consultation can be
attached to an engagement form. These submissions can be displayed and graphed.

Forms are built using the SilverStripe UserForm module.

## Page Types
This module introduces 3 new page types:
* `Consultation`: display a form, optional reports and ranking
*  `Consultation category`: allow to group consultations
* `Consultation landing page`: holds categories and consultation

## Reports
Consultations can display "reports", a graphic way of displaying data from the form submission.
Reports can only be based on EditableMultipleOptionField (e.g DropdownField, RadioField...).
To create a report, on a `Consultation` page , head to the `Report` tab, create a new report with a title, save, then choose a field to base the report on.

### Report Types
Out-of -the-box, you can display report as a doughnut chart (powered by [Chart.js](http://www.chartjs.org/ "Chart.js")
You can create new report type by extending `ConsultationReportType` and implementing:
	``function render($controller, $data) {} `` 


## Comments
Comments can include any field from the consultation form. To include a field in a comment, simply tick the option `include in comment` when creating the field.

##TO DO
* Create more report types
