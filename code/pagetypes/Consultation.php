<?php
/**
 * @package consultation
 */
class Consultation extends UserDefinedForm
{

    private static $db = array(
        'Starts' => 'Date',
        'Expires' => 'Date'
    );

    private static $has_many = array(
        'Reports' => 'ConsultationReport'
    );

    private static $summary_fields = array(
        'ID' => 'ID',
        'Title' => 'Title',
        'getCategoryName' => 'Category',
        'getparticipation' => 'Participation',
        'getGlobalPopularityAsString' => 'Global Popularity',
        'getPopularityInCatgeoryAsString' => 'Popularity in catgory',
        'getStatus' => 'Status'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Embargo
        $embargo = ToggleCompositeField::create('Embargo', 'Embargo', array(
            DateField::create('Starts', 'Allow participation from')
            ->setRightTitle('Optional. If left blank, participation starts when page is published')
            ->setConfig('showcalendar', true),
            DateField::create('Expires', 'Until')
            ->setRightTitle('Optional. If left blank, participation will end when page is unpublished.')
            ->setConfig('showcalendar', true)
        ))->setStartClosed(false);

        $fields->addFieldToTab('Root.FormOptions', $embargo, 'SubmitButtonText');

        // Reports
        $config = GridFieldConfig_RecordEditor::create();
        $reports = GridField::create('Reports', 'Reports', $this->Reports(), $config);

        $fields->addFieldToTab('Root.Reports', $reports);

        return $fields;
    }

    /**
    * Return whether this onsultation is open for participation
    *
    * @return Boolean
    */
    public function isOpen()
    {
        return ($this->hasStarted() && !$this->hasExpired());
    }

    public function hasStarted()
    {
        $now = strtotime('now');

        if ($this->Starts) {
            $start = strtotime($this->Starts);
            return ($now > $start);
        }
        return true;
    }

    public function hasExpired()
    {
        $now = strtotime('now');

        if ($this->Expires) {
            $end = strtotime($this->Expires);
            return ($now > $end);
        }
        return false;
    }

    /**
    * Return whether the participation is opened
    * for gridfield summary
    *
    * @return HTMLText
    */
    public function getStatus()
    {
        $colours = array('red', 'green', 'blue');

        $colour = (!$this->hasStarted()) ? $colours[2] : $colours[(int)$this->isOpen()];
        $message = '';
        // Not started yet
        if (!$this->hasStarted()) {
            $date = new DateTime($this->Starts);
            $message = 'Starting on '.$date->format('d-m-Y');
        }
        // Active / Epxpired
        else {
            $message = ($this->hasExpired()) ? 'Expired' : 'Active';
        }

        $output = sprintf('<span style="color:%s">%s</span>', $colour, $message);
        $field = HTMLText::create('Status');
        $field->setValue($output);
        return $field;
    }

    /**
    * Return the parent category
    *
    * @return ConsultationCategory
    */
    public function getCategory()
    {
        if ($this->Parent()) {
            $categories = ClassInfo::subclassesFor('ConsultationCategory');
            if (in_array($this->Parent()->ClassName, $categories)) {
                return $this->Parent();
            }
        }
        return null;
    }

    /**
    * Return the parent category name
    *
    * @return String
    */
    public function getCategoryName()
    {
        if ($category = $this->getCategory()) {
            return $category->MenuTitle;
        }
        return null;
    }

    /**
    * Return the position of this consultation
    * based on the amount of submission
    *
    * @return Int
    */
    public function getGlobalPopularity()
    {
        $consultations = self::getOrderedByPopulartity();
        $rank = (int) array_search($this->ID, $consultations->column('ID')) + 1;
        return $rank;
    }

    /**
    * Return the position of this consultation
    * based on the amount of submission
    * in a position / number consultation string format
    *
    * @return String
    */
    public function getGlobalPopularityAsString()
    {
        $consultations = self::get()->count();
        return (string) $this->getGlobalPopularity() .' / '.$consultations;
    }

    /**
    * Return the position of this consultation
    * based on the amount of submission
    * within the same category
    *
    * @return Int
    */
    public function getPopularityInCategory()
    {
        $category = $this->getCategory();
        if (!$category) {
            return;
        }

        $consultations = self::getOrderedByPopulartity();
        $consultations = $consultations->filter('ParentID', $category->ID);

        $rank = (int) array_search($this->ID, $consultations->column('ID')) + 1;
        return $rank;
    }

    /**
    * Return the position of this consultation
    * based on the amount of submission
    * within the same category
    * in a position / number consultation string format
    *
    * @return String
    */
    public function getPopularityInCatgeoryAsString()
    {
        $category = $this->getCategory();
        if (!$category) {
            return;
        }

        $consultations = $category->getConsultations()->count();
        return (string) $this->getPopularityInCategory() .' / '.$consultations;
    }

    /**
    * Return the number of submission for this cosultation
    *
    * @return Int
    */
    public function getParticipation()
    {
        return $this->Submissions()->count();
    }

    /**
    * Get the consultation with the most submissions
    */
    public static function getMostPopular()
    {
        $popularity = self::getOrderedByPopulartity();
        return $popularity->First();
    }

    /**
    * Get all submission for all consultation
    */
    public static function getAllSubmissions()
    {
        $consultations = self::get()->column('ID');
        return SubmittedForm::get()->filter('ParentID', $consultations);
    }

    /**
    * Return all consultation ordered by popularity
    * based on the number of submissions
    *
    * @return DataList
    */
    public static function getOrderedByPopulartity()
    {
        $table = 'UserDefinedForm';
        if (Versioned::current_stage() == 'Live') {
            $table .= '_Live';
        }
        return self::get()->sort("(SELECT COUNT(ID) FROM SubmittedForm WHERE ParentID = " . $table . ".ID)")->reverse();
    }
}

/**
 * @package consultation
 */

class Consultation_Controller extends UserDefinedForm_Controller
{

    private static $allowed_actions = array(
        'index'
    );

    public function init()
    {
        parent::init();
        Requirements::css(CONSULTATION_MODULE_DIR . '/css/consultations.css');
    }
}
