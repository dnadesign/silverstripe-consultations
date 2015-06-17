<?php

/**
 * @package wcc_longtermplan
 */
class TypeformSubmissionExtension extends DataExtension {
	
	private static $db = array(
		'FirstName' => 'Varchar(255)',
		'PriorityResponse' => 'Int',
		'SupportsOverallPlan' => 'Boolean',
		'SupportsOverallPlanRanking' => 'Int',
		'WhoWouldBenefit' => 'Text',
		'Suburb' => 'Varchar(200)',
		'SupportsIdea' => "Boolean",
		'SupportsIdeaRanking' => 'Int',
		'Sentiment' => "Int",
		'FeedbackText' => 'Text',
		'InTheFuture' => 'Boolean',
		'PromotedHomePage' => 'Boolean',
		'PromotedSummary' => 'Boolean'
	);

	private static $summary_fields = array(
		'ID',
		'FeedbackText',
		'PromotedHomePage',
		'PromotedSummary',
		'FirstName',
		'Suburb',
		'Parent.Title'
	);

	private static $has_one = array(
		'Author' => 'Member'
	);

	
	private static $searchable_fields = array(
		'SupportsIdea',
		'SupportsOverallPlan',
		'Sentiment',
		'PromotedHomePage',
		'PromotedSummary',
		'InTheFuture'
	);

	public function onAfterAnswersSynced() {
		$config = SiteConfig::current_site_config();
		$email = null;
		$gender = null; 
		$firstName = null; 
		$age = null; 
		$type = null;
		$suburb = null;

		if($parent = $this->owner->Parent()) {
			if($parent->ClassName == "InTheFuturePage") {
				$this->owner->InTheFuture = true;

				foreach($this->owner->Answers() as $answer) {
					if($question = $answer->Question()) {
						if($question->Title == "In the future, Wellington will...") {
							$this->owner->FeedbackText = $answer->Value;
						} else if($question->Title == "Your first name") {
							$this->owner->FirstName = $answer->Value;
						} else if($question->Title == "Where do you live?") {
							$this->owner->Suburb = $answer->Value;
						}
					}
				}

				$this->owner->write();
			}
		}

		foreach($this->owner->Answers() as $answer) {
			if($question = $answer->Question()) {
				if(preg_match("/^What do you think about/i", $question->Title)) {
					// Feedback question.
					$this->owner->FeedbackText = $answer->Value;
				} 
				else if(preg_match("/^Considering all of the projects and ideas/i", $question->Title)) {
					// Rating scale 1 - 5 of how to priority the work
					$this->owner->PriorityResponse = $answer->Value;
				}
				else if($question->Title == "Do you support the broad approach taken in this plan of investing for economic growth, in addition to providing current levels of service?") {
					// Supports the over plan.
					$this->owner->SupportsOverallPlanRanking = $answer->Value;

					if($answer->Value > 3) {
						$this->owner->SupportsOverallPlan = true;
					} else {
						$this->owner->SupportsOverallPlan = false;
					}
				}
				else if(preg_match("/^How do you feel/", $question->Title)) {
					$this->owner->Sentiment = $answer->Value;
				}
				else if(preg_match("/^Do you/", $question->Title)) {
					$this->owner->SupportsIdeaRanking = $answer->Value;

					if($answer->Value > 3) {
						$this->owner->SupportsIdea = true;
					} else {
						$this->owner->SupportsIdea = false;
					}
				}
				else if($question->Title == "Who do you think would benefit most from this idea?" || $question->Title == "Who do you think would benefit most from this idea?") {
					// Who would benefit
					if($answer->Value) {
						$this->owner->WhoWouldBenefit .= $answer->Value .',';
					}
				}

				// Generic member information.
				if($question->Title == "Your email address") {
					$email = $answer->Value;
				} else if($question->Title == "Your first name") {
					$firstName = $answer->Value;
					$this->owner->FirstName = $firstName;
				} else if($question->Title == "Where do you live?" || $question->Title == "What suburb do you live in?") {
					$suburb = $answer->Value;
					$this->owner->Suburb = $suburb;
				} else if($question->Title == "Your age") {
					$age = $answer->Value;
				} else if($question->Title == "Which of the following best describes you?") {
					$type = $answer->Value;
				} else if($question->Title == "Your gender") {
					$gender = $answer->Value;
				}
			}
		}

		if(!$this->owner->PriorityResponse) {
			$this->owner->PriorityResponse = 1;
		}


		if(!$firstName) {
			$this->owner->FirstName = "Anonymous";
		}
		
		if($email) {
			$member = Member::get()->filter('Email', $email)->first();

			if(!$member) {
				$member = new Member();
				$member->Email  = $email;
			}
			
			$member->FirstName = $firstName;
			$member->Gender = $gender;
			$member->Suburb = $suburb;
			$member->write();
			
			$this->owner->AuthorID = $member->ID;

			$member->addToGroupByCode('submissions', 'Submissions');
		}

		$this->owner->write();
	}

	public function Link($action = null) {
		$page = ResultSummaryPage::get()->first();

		return $page->Link(Controller::join_links('comment', $action, $this->owner->ID));
	}
}