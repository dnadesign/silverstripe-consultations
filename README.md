# SilverStripe Consultations

## Maintainer Contact

* Will Rossiter (Nickname: wrossiter, willr) <will.rossiter@dna.co.nz>

## Requirements

* SilverStripe 3.1
* SilverStripe GridFieldExtensions
* SilverStripe Typeform

## Documentation

This module provides a boilerplate setup for publishing online consultations and
displaying feedback.

**This work has been open sourced from http://our10yearplan.co.nz and still
opinionated and an early release.**

## Installation

	composer require "dnadesign/silverstripe-consultations"

## Functionality

This module provides the models for `Consultation`. Each consultation can be
attached to an engagement form (at the moment hard coded to Typeform). These
submissions can be displayed and graphed (work in progress).

Currently all forms use Typeform and data is synced across, long term we want to
support multiple options (user forms, typeform or custom).
