<?php
/**
 * @file
 * Event Attendees by Organization Report.
 *
 * Copyright (C) 2015, AGH Strategies, LLC <info@aghstrategies.com>
 * Licensed under the GNU Affero Public License 3.0 (see LICENSE.txt)
 */

/**
 * Very similar to event participant listing report.
 *
 * Key differences are header display for each organization, event start date by
 * month, and event.
 */
class CRM_Orgeventattendees_Form_Report_OrgDetail extends CRM_Report_Form_Event_ParticipantListing {

  /**
   * Custom fields available in this report.
   */
  protected $_customGroupExtends = array('Contact', 'Participant');

  /**
   * List of all events matching the criteria.
   */
  protected $_allEvents = array();

  /**
   * Rows nested by month and event.
   */
  protected $_nestedRows = array();

  /**
   * Build the class, adjusting the settings as compared to the main report.
   */
  public function __construct() {
    parent::__construct();
    $this->_autoIncludeIndexedFieldsAsOrderBys = 0;

    // Unset some defaults:
    $this->_columns['civicrm_phone']['fields']['phone']['default'] = FALSE;
    $this->_columns['civicrm_participant']['fields']['event_id']['default'] = FALSE;
    $this->_columns['civicrm_participant']['fields']['status_id']['default'] = FALSE;
    $this->_columns['civicrm_contribution']['fields']['contribution_status_id']['default'] = FALSE;

    if (array_key_exists('campaign_id', $this->_columns['civicrm_participant']['fields'])) {
      $this->_columns['civicrm_participant']['fields']['campaign_id']['default'] = FALSE;
    }

    $this->_columns['civicrm_event']['filters']['event_start_date']['default'] = 'this.year';

    $this->_columns['civicrm_event']['fields']['event_id_hidden'] = array(
      'name' => 'id',
      'title' => ts('Event ID'),
      'no_display' => TRUE,
      'required' => TRUE,
    );
    $this->_columns['civicrm_event']['fields']['event_start_date_month'] = array(
      'title' => ts('Event Start Month'),
      'dbAlias' => 'DATE_FORMAT(event_civireport.start_date, "%Y%m")',
      'no_display' => TRUE,
      'required' => TRUE,
    );
    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('order_bys', $table)) {
        unset($this->_columns[$tableName]['order_bys']);
      }
    }
    $this->_options = array();
  }

  /**
   * Hard-coded ordering.
   */
  public function orderBy() {
    $this->_orderBy = "ORDER BY {$this->_aliases['civicrm_event']}.start_date ASC, {$this->_aliases['civicrm_contact']}.sort_name ASC";
    $this->_sections = array();
    $this->assign('sections', $this->_sections);
  }

  /**
   * Build the array of rows, nested by month and event.
   *
   * @param string $sql
   *   The SQL query.
   * @param array $rows
   *   The array of rows to build.
   */
  public function buildRows($sql, &$rows) {
    // First, get the big events array.
    $this->getAllEvents();

    parent::buildRows($sql, $rows);
    foreach ($rows as $rowId => $row) {
      $this->_nestedRows[$row['civicrm_event_event_start_date_month']][$row['civicrm_event_event_id_hidden']][] = $row;
    }
  }

  /**
   * @param $rows
   * @param bool $pager
   */
  public function formatDisplay(&$rows, $pager = TRUE) {
    parent::formatDisplay($rows, $pager);

    // Format the nested rows.
    foreach ($this->_nestedRows as $month => &$mRows) {
      foreach ($mRows as $eventId => &$eRows) {
        $this->alterDisplay($eRows);
        CRM_Utils_Hook::alterReportVar('rows', $eRows, $this);
        $this->alterCustomDataDisplay($eRows);
      }
    }
    $this->assign('events', $this->_allEvents);
  }

  /**
   * Handles the assignment of the rows and nested rows.
   *
   * @param array &$rows
   *   The report rows.
   */
  public function doTemplateAssignment(&$rows) {
    parent::doTemplateAssignment($rows);
    $this->assign_by_ref('nestedRows', $this->_nestedRows);
  }

  /**
   * Set up array of all events within the range, regardless of participants.
   */
  public function getAllEvents() {
    $eSQL = 'SELECT e.id, e.title, e.event_type_id, e.start_date, DATE_FORMAT(e.start_date, "%Y%m") as start_date_month FROM civicrm_event e WHERE e.is_template = 0';
    $clauses = array();
    if (array_key_exists('filters', $this->_columns['civicrm_event'])) {
      foreach ($this->_columns['civicrm_event']['filters'] as $fieldName => $field) {
        $clause = NULL;

        if (CRM_Utils_Array::value('type', $field) & CRM_Utils_Type::T_DATE) {
          $relative = CRM_Utils_Array::value("{$fieldName}_relative", $this->_params);
          $from = CRM_Utils_Array::value("{$fieldName}_from", $this->_params);
          $to = CRM_Utils_Array::value("{$fieldName}_to", $this->_params);

          if ($relative || $from || $to) {
            $clause = $this->dateClause($field['name'], $relative, $from, $to, $field['type']);
          }
        }
        else {
          $op = CRM_Utils_Array::value("{$fieldName}_op", $this->_params);

          if ($fieldName == 'rid') {
            $value = CRM_Utils_Array::value("{$fieldName}_value", $this->_params);
            if (!empty($value)) {
              $clause = "( {$field['dbAlias']} REGEXP '[[:<:]]" .
                implode('[[:>:]]|[[:<:]]', $value) . "[[:>:]]' )";
            }
            $op = NULL;
          }

          if ($op) {
            $clause = $this->whereClause($field,
              $op,
              CRM_Utils_Array::value("{$fieldName}_value", $this->_params),
              CRM_Utils_Array::value("{$fieldName}_min", $this->_params),
              CRM_Utils_Array::value("{$fieldName}_max", $this->_params)
            );
          }
        }

        if (!empty($clause)) {
          $clauses[] = $clause;
        }
      }
    }
    if (!empty($clauses)) {
      $eSQL .= ' AND ' . implode(' AND ', $clauses);
    }
    $eSQL .= " ORDER BY e.start_date ASC";
    $eDAO = CRM_Core_DAO::executeQuery($eSQL);

    $eventType = CRM_Core_OptionGroup::values('event_type');
    while ($eDAO->fetch()) {
      $this->_allEvents[$eDAO->start_date_month][$eDAO->id] = array(
        'id' => $eDAO->id,
        'title' => $eDAO->title,
        'event_type' => CRM_Utils_Array::value($eDAO->event_type_id, $eventType, $eDAO->event_type_id),
        'start_date' => $eDAO->start_date,
      );
    }
  }

  /**
   * No statistics for now.
   *
   * @param array $rows
   *   The array of rows in the report.
   *
   * @return array $statistics
   *   Statistics to display.
   */
  public function statistics(&$rows) {
    $statistics = array();

    $this->groupByStat($statistics);

    $this->filterStat($statistics);

    $this->totalPrograms($statistics);
    $this->distinctPart($statistics, $rows);
    return $statistics;
  }

  /**
   * Calculate number of programs.
   *
   * @param array &$statistics
   *   Statistics to display on the report.
   */
  public function totalPrograms(&$statistics) {
    $count = 0;
    foreach ($this->_allEvents as $ym => $events) {
      $count += count($events);
    }
    $statistics['filters']['allEventCount'] = array(
      'title' => ts('Total programs during this time'),
      'value' => $count,
    );
  }

  public function distinctPart(&$statistics, $rows) {
    $sql = "SELECT COUNT(DISTINCT {$this->_aliases['civicrm_participant']}.contact_id) as contacts, COUNT(DISTINCT {$this->_aliases['civicrm_participant']}.event_id) as events {$this->_from} {$this->_where} {$this->_having}";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $statistics['filters']['eventCount'] = array(
        'title' => ts('Events attended'),
        'value' => $dao->events,
      );
      $statistics['filters']['contactCount'] = array(
        'title' => ts('Unique participants'),
        'value' => $dao->contacts,
      );
    }
  }

  /**
   * Bypass any limit on results.
   *
   * @param int $rowCount
   *   The limit set elsewhere on rows.
   */
  public function limit($rowCount = self::ROW_COUNT_LIMIT) {
    $this->_limit = NULL;
  }

}
