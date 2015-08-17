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
            'required' => TRUE,
            'no_repeat' => TRUE,
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
          'employer_id' => array(
            'title' => ts('Organization'),
            'default' => TRUE,
          ),
        ),
        'grouping' => 'contact-fields',
        'order_bys' => array(
          'sort_name' => array(
            'title' => ts('Sort Name'),
            'default' => '1',
            'default_weight' => '0',
            'default_order' => 'ASC',
          ),
        ),
        'group_bys' => array(
          'employer_id' => array(
            'title' => ts('Organization'),
          ),
        )
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
            'type' => CRM_Utils_Type::T_STRING,
          ),
          'status_id' => array(
            'title' => ts('Status'),
            'default' => TRUE,
          ),
          'role_id' => array(
            'title' => ts('Role'),
            'default' => TRUE,
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
        'order_bys' => array(
          'event_id' => array(
            'title' => ts('Event'),
            'default_weight' => '1',
            'default_order' => 'ASC',
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
          'event_start_date' => array(
            'title' => ts('Event Start Date'),
            'operatorType' => CRM_Report_Form::OP_DATE,
          ),
        ),
        'order_bys' => array(
          'event_type_id' => array(
            'title' => ts('Event Type'),
            'default_weight' => '2',
            'default_order' => 'ASC',
          ),
        ),
      ),
    );

    parent::__construct();
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
