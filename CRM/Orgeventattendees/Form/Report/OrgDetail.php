<?php
/**
 * @file
 * Event Attendees by Organization Report.
 */

/**
 * Very similar to event participant listing report.
 *
 * Key differences are the following:
 * - header display for each organization,
 * - header display for each event, and
 * - option to have section headers for event start date by month.
 */
class CRM_Orgeventattendees_Form_Report_OrgDetail extends CRM_Report_Form_Event_ParticipantListing {

  /**
   * Custom fields available in this report.
   */
  protected $_customGroupExtends = array('Contact', 'Participant');

  protected $_customGroupGroupBy = TRUE;

  protected $_autoIncludeIndexedFieldsAsOrderBys = 1;

  public function __construct() {
    parent::__construct();
    $this->_columns['civicrm_event']['fields']['event_start_date_month'] = array(
      'title' => ts('Event Start Month'),
      'dbAlias' => 'DATE_FORMAT(event_civireport.start_date, "%Y%m")',
      'no_display' => TRUE,
      'required' => TRUE,
    );
    $this->_columns['civicrm_event']['order_bys']['event_start_date'] = array(
      'title' => ts('Event Start Date'),
    );
  }

  /**
   * Reworked to pull all events regardless of whether there were participants.
   * @return {[type]} [description]
   */
  public function from() {
    $this->_from = "
        FROM civicrm_participant {$this->_aliases['civicrm_participant']}
             LEFT JOIN civicrm_event {$this->_aliases['civicrm_event']}
                    ON ({$this->_aliases['civicrm_event']}.id = {$this->_aliases['civicrm_participant']}.event_id ) AND
                        {$this->_aliases['civicrm_event']}.is_template = 0
             LEFT JOIN civicrm_contact {$this->_aliases['civicrm_contact']}
                    ON ({$this->_aliases['civicrm_participant']}.contact_id  = {$this->_aliases['civicrm_contact']}.id  )
             {$this->_aclFrom}
             LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']}
                    ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_address']}.contact_id AND
                       {$this->_aliases['civicrm_address']}.is_primary = 1
             LEFT JOIN  civicrm_email {$this->_aliases['civicrm_email']}
                    ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND
                       {$this->_aliases['civicrm_email']}.is_primary = 1)
             LEFT  JOIN civicrm_phone  {$this->_aliases['civicrm_phone']}
                     ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_phone']}.contact_id AND
                         {$this->_aliases['civicrm_phone']}.is_primary = 1
      ";
    if ($this->_contribField) {
      $this->_from .= "
             LEFT JOIN civicrm_participant_payment pp
                    ON ({$this->_aliases['civicrm_participant']}.id  = pp.participant_id)
             LEFT JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']}
                    ON (pp.contribution_id  = {$this->_aliases['civicrm_contribution']}.id)
      ";
    }
    if ($this->_lineitemField) {
      $this->_from .= "
            LEFT JOIN civicrm_line_item line_item_civireport
                  ON line_item_civireport.entity_table = 'civicrm_participant' AND
                     line_item_civireport.entity_id = {$this->_aliases['civicrm_participant']}.id AND
                     line_item_civireport.qty > 0
      ";
    }
    if ($this->_balance) {
      $this->_from .= "
            LEFT JOIN civicrm_entity_financial_trxn eft
                  ON (eft.entity_id = {$this->_aliases['civicrm_contribution']}.id)
            LEFT JOIN civicrm_financial_account fa
                  ON (fa.account_type_code = 'AR')
            LEFT JOIN civicrm_financial_trxn ft
                  ON (ft.id = eft.financial_trxn_id AND eft.entity_table = 'civicrm_contribution') AND
                     (ft.to_financial_account_id != fa.id)
      ";
    }
  }

  public function orderBy() {
    parent::orderBy();
    if (!empty($this->_sections['civicrm_event_event_start_date'])) {
      $newSections = array();
      foreach ($this->_sections as $key => $value) {
        if ($key == 'civicrm_event_event_start_date') {
          $key = 'civicrm_event_event_start_date_month';
        }
        $newSections[$key] = $value;
      }
      $this->_sections = $newSections;
      $this->assign('sections', $this->_sections);
    }
  }

}
