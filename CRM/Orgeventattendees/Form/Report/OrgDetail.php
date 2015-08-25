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

  /**
   * List of all events matching the criteria.
   */
  protected $_allEvents = array();

  public function __construct() {
    parent::__construct();
    $this->_autoIncludeIndexedFieldsAsOrderBys = 0;

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
   * Build the array of rows, nested by month and event
   * @param $sql
   * @param $rows
   */
  public function buildRows($sql, &$rows) {
    // First, get the big events array.
    $this->getAllEvents();

    $dao = CRM_Core_DAO::executeQuery($sql);
    if (!is_array($rows)) {
      $rows = array();
    }

    // use this method to modify $this->_columnHeaders
    $this->modifyColumnHeaders();

    $unselectedSectionColumns = $this->unselectedSectionColumns();

    while ($dao->fetch()) {
      $row = array();
      foreach ($this->_columnHeaders as $key => $value) {
        if (property_exists($dao, $key)) {
          $row[$key] = $dao->$key;
        }
      }

      // section headers not selected for display need to be added to row
      foreach ($unselectedSectionColumns as $key => $values) {
        if (property_exists($dao, $key)) {
          $row[$key] = $dao->$key;
        }
      }

      $rows[$dao->civicrm_event_event_start_date_month][$dao->civicrm_event_event_id_hidden][] = $row;
    }
  }

  /**
   * @param $rows
   * @param bool $pager
   */
  public function formatDisplay(&$rows, $pager = TRUE) {
    // set pager based on if any limit was applied in the query.
    if ($pager) {
      $this->setPager();
    }

    // unset columns not to be displayed.
    foreach ($this->_columnHeaders as $key => $value) {
      if (!empty($value['no_display'])) {
        unset($this->_columnHeaders[$key]);
      }
    }

    // unset columns not to be displayed.
    if (!empty($rows)) {
      foreach ($this->_noDisplay as $noDisplayField) {
        foreach ($rows as $rowNum => $row) {
          unset($this->_columnHeaders[$noDisplayField]);
          break;
        }
      }
    }

    // use this method for formatting rows for display purpose.
    foreach ($rows as $month => &$mRows) {
      foreach ($mRows as $eventId => &$eRows) {
        $this->alterDisplay($eRows);
        CRM_Utils_Hook::alterReportVar('rows', $eRows, $this);

        // use this method for formatting custom rows for display purpose.
        $this->alterCustomDataDisplay($rows);
      }
    }
    $this->assign('events', $this->_allEvents);
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

    while ($eDAO->fetch()) {
      $this->_allEvents[$eDAO->start_date_month][$eDAO->id] = array(
        'id' => $eDAO->id,
        'title' => $eDAO->title,
        'event_type_id' => $eDAO->event_type_id,
        'start_date' => $eDAO->start_date,
      );
    }
  }

  /**
   * override this method to build your own statistics
   * @param $rows
   *
   * @return array
   */
  public function statistics(&$rows) {
    // $statistics = array();
    //
    // $count = count($rows);
    //
    // if ($this->_rollup && ($this->_rollup != '') && $this->_grandFlag) {
    //   $count++;
    // }
    //
    // $this->countStat($statistics, $count);
    //
    // $this->groupByStat($statistics);
    //
    // $this->filterStat($statistics);
    //
    // return $statistics;
  }

  public function limit($rowCount = self::ROW_COUNT_LIMIT) {
    // lets do the pager if in html mode
    $this->_limit = NULL;
  }

}
