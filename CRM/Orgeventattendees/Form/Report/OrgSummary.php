<?php
/**
 * @file
 * Event Attendees by Organization Report.
 */

/**
 * Similar to CRM_Report_Form_Event_ParticipantListing.
 *
 * Extends CRM_Report_Form instead of the empty class CRM_Report_Form_Event.
 */
class CRM_Orgeventattendees_Form_Report_orgSummary extends CRM_Report_Form {

  /**
   * Set up the fields.
   */
  public function __construct() {
    $this->_columns = array(
      'civicrm_contact' => array(
        'dao' => 'CRM_Contact_DAO_Contact',
        'fields' => array(
          'sort_name_linked' => array(
            'title' => ts('Participant Name'),
            'dbAlias' => 'contact_civireport.sort_name',
          ),
          'first_name' => array(
            'title' => ts('First Name'),
          ),
          'middle_name' => array(
            'title' => ts('Middle Name'),
          ),
          'last_name' => array(
            'title' => ts('Last Name'),
          ),
          'id' => array(
            'no_display' => TRUE,
            'required' => TRUE,
          ),
        ),
        'grouping' => 'contact-fields',
        'order_bys' => array(
          'sort_name' => array(
            'title' => ts('Sort Name'),
          ),
        ),
        'group_bys' => array(
          'employer_id' => array(
            'title' => ts('Organization'),
            'default' => TRUE,
          ),
        ),
        'filters' => array(
          'sort_name' => array(
            'title' => ts('Participant Name'),
            'operator' => 'like',
          ),
          'employer_id' => array(
            'title' => ts('Organization'),
          ),
          'gender_id' => array(
            'title' => ts('Gender'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Core_PseudoConstant::get('CRM_Contact_DAO_Contact', 'gender_id'),
          ),
          'birth_date' => array(
            'title' => ts('Birth Date'),
            'operatorType' => CRM_Report_Form::OP_DATE,
          ),
          'contact_type' => array(
            'title' => ts('Contact Type'),
          ),
          'contact_sub_type' => array(
            'title' => ts('Contact Subtype'),
          ),
        ),
      ),
      'organization' => array(
        'name' => 'civicrm_contact',
        'dao' => 'CRM_Contact_DAO_Contact',
        'fields' => array(
          'display_name' => array(
            'title' => ts('Organization Name'),
            'default' => TRUE,
          ),
        ),
        'order_bys' => array(
          'org_sort_name' => array(
            'name' => 'sort_name',
            'title' => ts('Organization Name'),
            'default' => '1',
            'default_weight' => '0',
            'default_order' => 'ASC',
          ),
        ),
      ),
      'civicrm_participant' => array(
        'dao' => 'CRM_Event_DAO_Participant',
        'fields' => array(
          'participant_id' => array('title' => 'Participant ID'),
          'participant_record' => array(
            'name' => 'id',
            'no_display' => TRUE,
            'required' => TRUE,
          ),
          'event_id' => array(
            'default' => TRUE,
            'title' => ts('Events Attended'),
            // 'type' => CRM_Utils_Type::T_STRING,
            'statistics' => array(
              'count_distinct' => ts('Events Attended'),
            ),
          ),
          'unique_staff' => array(
            'name' => 'contact_id',
            'default' => TRUE,
            'title' => ts('Unique Staff'),
            // 'type' => CRM_Utils_Type::T_STRING,
            'statistics' => array(
              'count_distinct' => ts('Unique Staff'),
            ),
          ),
          'status_id' => array(
            'title' => ts('Status'),
          ),
          'role_id' => array(
            'title' => ts('Role'),
          ),
        ),
        'grouping' => 'event-fields',
        'filters' => array(
          'event_id' => array(
            'name' => 'event_id',
            'title' => ts('Event'),
            'operatorType' => CRM_Report_Form::OP_ENTITYREF,
            'type' => CRM_Utils_Type::T_INT,
            'attributes' => array(
              'entity' => 'event',
              'select' => array('minimumInputLength' => 0),
            ),
          ),
          'sid' => array(
            'name' => 'status_id',
            'title' => ts('Participant Status'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Event_PseudoConstant::participantStatus(NULL, NULL, 'label'),
          ),
          'rid' => array(
            'name' => 'role_id',
            'title' => ts('Participant Role'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Event_PseudoConstant::participantRole(),
          ),
          'participant_register_date' => array(
            'title' => 'Registration Date',
            'operatorType' => CRM_Report_Form::OP_DATE,
          ),
        ),
      ),
      'civicrm_event' => array(
        'dao' => 'CRM_Event_DAO_Event',
        'fields' => array(
          'event_type_id' => array('title' => ts('Event Type')),
          'event_start_date' => array('title' => ts('Event Start Date')),
        ),
        'grouping' => 'event-fields',
        'filters' => array(
          'eid' => array(
            'name' => 'event_type_id',
            'title' => ts('Event Type'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Core_OptionGroup::values('event_type'),
          ),
          'event_start_date1' => array(
            'title' => ts('Event Start Date (Period 1)'),
            'type' => CRM_Utils_Type::T_DATE,
            'operatorType' => CRM_Report_Form::OP_DATE,
            'name' => 'event_start_date',
            'default' => 'this.year',
          ),
          'event_start_date2' => array(
            'title' => ts('Event Start Date (Period 2)'),
            'type' => CRM_Utils_Type::T_DATE,
            'operatorType' => CRM_Report_Form::OP_DATE,
            'name' => 'event_start_date',
            'default' => 'previous.year',
          ),
        ),
        'order_bys' => array(
          'event_type_id' => array(
            'title' => ts('Event Type'),
          ),
        ),
      ),
    );

    parent::__construct();
  }

  /**
   * Enforce valid date ranges.
   *
   * Borrowed from CRM_Report_Form_Contribute_Repeat::formRule().
   *
   * @param array $fields
   *   Fields from the form.
   * @param array $files
   *   Not used.
   * @param object $self
   *   Not used.
   *
   * @return array
   *   Array of errors.
   */
  public static function formRule($fields, $files, $self) {

    $errors = $checkDate = $errorCount = array();

    if ($fields['event_start_date1_relative'] == '0') {
      $checkDate['event_start_date1']['event_start_date1_from'] = $fields['event_start_date1_from'];
      $checkDate['event_start_date1']['event_start_date1_to'] = $fields['event_start_date1_to'];
    }

    if ($fields['event_start_date2_relative'] == '0') {
      $checkDate['event_start_date2']['event_start_date2_from'] = $fields['event_start_date2_from'];
      $checkDate['event_start_date2']['event_start_date2_to'] = $fields['event_start_date2_to'];
    }

    foreach ($checkDate as $date_range => $range_data) {
      foreach ($range_data as $key => $value) {
        if (CRM_Utils_Date::isDate($value)) {
          $errorCount[$date_range][$key]['valid'] = 'true';
          $errorCount[$date_range][$key]['is_empty'] = 'false';
        }
        else {
          $errorCount[$date_range][$key]['valid'] = 'false';
          $errorCount[$date_range][$key]['is_empty'] = 'true';
          if (is_array($value)) {
            foreach ($value as $v) {
              if ($v) {
                $errorCount[$date_range][$key]['is_empty'] = 'false';
              }
            }
          }
          elseif (!isset($value)) {
            $errorCount[$date_range][$key]['is_empty'] = 'false';
          }
        }
      }
    }

    $errorText = ts("Select valid date range");
    foreach ($errorCount as $date_range => $error_data) {

      if (($error_data[$date_range . '_from']['valid'] == 'false') &&
        ($error_data[$date_range . '_to']['valid'] == 'false')
      ) {

        if (($error_data[$date_range . '_from']['is_empty'] == 'true') &&
          ($error_data[$date_range . '_to']['is_empty'] == 'true')
        ) {
          $errors[$date_range . '_relative'] = $errorText;
        }

        if ($error_data[$date_range . '_from']['is_empty'] == 'false') {
          $errors[$date_range . '_from'] = $errorText;
        }

        if ($error_data[$date_range . '_to']['is_empty'] == 'false') {
          $errors[$date_range . '_to'] = $errorText;
        }
      }
      elseif (($error_data[$date_range . '_from']['valid'] == 'true') &&
        ($error_data[$date_range . '_to']['valid'] == 'false')
      ) {
        if ($error_data[$date_range . '_to']['is_empty'] == 'false') {
          $errors[$date_range . '_to'] = $errorText;
        }
      }
      elseif (($error_data[$date_range . '_from']['valid'] == 'false') &&
        ($error_data[$date_range . '_to']['valid'] == 'true')
      ) {
        if ($error_data[$date_range . '_from']['is_empty'] == 'false') {
          $errors[$date_range . '_from'] = $errorText;
        }
      }
    }

    return $errors;
  }

  public function select() {
    parent::select();
  }

  public function from() {
    $this->_from = "
        FROM civicrm_participant {$this->_aliases['civicrm_participant']}
             LEFT JOIN civicrm_event {$this->_aliases['civicrm_event']}
                    ON ({$this->_aliases['civicrm_event']}.id = {$this->_aliases['civicrm_participant']}.event_id ) AND
                        {$this->_aliases['civicrm_event']}.is_template = 0
             LEFT JOIN civicrm_contact {$this->_aliases['civicrm_contact']}
                    ON ({$this->_aliases['civicrm_participant']}.contact_id  = {$this->_aliases['civicrm_contact']}.id  )
             LEFT JOIN civicrm_contact {$this->_aliases['organization']}
                    ON {$this->_aliases['civicrm_contact']}.employer_id = {$this->_aliases['organization']}.id
             {$this->_aclFrom}
      ";
  }

  public function where() {
    parent::where();
  }

  public function groupBy() {
    parent::groupBy();
    if (empty($this->_groupBy)) {
      $this->_groupBy = "GROUP BY {$this->_aliases['civicrm_participant']}.id";
    }
  }

  /**
   * Lifted from CRM_Report_Form_Event_ParticipantListing.
   */
  public function postProcess() {

    // get ready with post process params
    $this->beginPostProcess();

    // get the acl clauses built before we assemble the query
    $this->buildACLClause($this->_aliases['civicrm_contact']);
    // build query
    $sql = $this->buildQuery(TRUE);

    // build array of result based on column headers. This method also allows
    // modifying column headers before using it to build result set i.e $rows.
    $rows = array();
    $this->buildRows($sql, $rows);

    // format result set.
    $this->formatDisplay($rows);

    // assign variables to templates
    $this->doTemplateAssignment($rows);

    // do print / pdf / instance stuff if needed
    $this->endPostProcess($rows);
  }

  /**
   * Alter display of rows.
   *
   * Iterate through the rows retrieved via SQL and make changes for display purposes,
   * such as rendering contacts as links.
   *
   * @param array $rows
   *   Rows generated by SQL, with an array for each row.
   */
  public function alterDisplay(&$rows) {
  }

}
