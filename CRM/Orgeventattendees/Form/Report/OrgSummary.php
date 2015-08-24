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
   * Clauses that should be on the join for a table rather than the where.
   */
  public $_joinClauses = array();

  /**
   * Custom fields available in this report.
   */
  protected $_customGroupExtends = array('Contact');

  protected $_customGroupGroupBy = TRUE;

  protected $_autoIncludeIndexedFieldsAsOrderBys = 1;

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
        'filters' => array(
          'sort_name' => array(
            'title' => ts('Participant Name'),
            'operator' => 'like',
          ),
          'employer_id' => array(
            'title' => ts('Employer Name'),
          ),
          'contact_type' => array(
            'title' => ts('Contact Type (Participant)'),
          ),
          'contact_sub_type' => array(
            'title' => ts('Contact Subtype (Participant)'),
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
        'group_bys' => array(
          'org_id' => array(
            'name' => 'id',
            'title' => ts('Organization'),
          ),
        ),
      ),
      'civicrm_event' => array(
        'dao' => 'CRM_Event_DAO_Event',
        'grouping' => 'event-fields',
        'filters' => array(
          'eid' => array(
            'name' => 'event_type_id',
            'title' => ts('Event Type (Recent Period)'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Core_OptionGroup::values('event_type'),
            'joinclause' => TRUE,
          ),
          'event_start_date1' => array(
            'title' => ts('Event Start Date (Recent Period)'),
            'type' => CRM_Utils_Type::T_DATE,
            'operatorType' => CRM_Report_Form::OP_DATE,
            'name' => 'event_start_date',
            'default' => 'this.year',
            'joinclause' => TRUE,
          ),
        ),
      ),
      'civicrm_participant' => array(
        'dao' => 'CRM_Event_DAO_Participant',
        'fields' => array(
          'participant_id' => array('title' => 'Participant ID (Recent Period)'),
          'participant_record' => array(
            'name' => 'id',
            'no_display' => TRUE,
            'required' => TRUE,
          ),
          'event_id' => array(
            'default' => TRUE,
            'title' => ts('Events Attended (Recent Period)'),
            // 'type' => CRM_Utils_Type::T_STRING,
            'statistics' => array(
              'count_distinct' => ts('Events Attended (Recent Period)'),
            ),
          ),
          'unique_staff' => array(
            'name' => 'contact_id',
            'default' => TRUE,
            'title' => ts('Unique Staff'),
            // 'type' => CRM_Utils_Type::T_STRING,
            'statistics' => array(
              'count_distinct' => ts('Unique Staff (Recent Period)'),
            ),
          ),
        ),
        'grouping' => 'event-fields',
        'filters' => array(
          'event_id' => array(
            'name' => 'event_id',
            'title' => ts('Event (Recent Period)'),
            'operatorType' => CRM_Report_Form::OP_ENTITYREF,
            'type' => CRM_Utils_Type::T_INT,
            'attributes' => array(
              'entity' => 'event',
              'select' => array('minimumInputLength' => 0),
            ),
            'joinclause' => TRUE,
          ),
          'sid' => array(
            'name' => 'status_id',
            'title' => ts('Participant Status (Recent Period)'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Event_PseudoConstant::participantStatus(NULL, NULL, 'label'),
            'joinclause' => TRUE,
          ),
          'rid' => array(
            'name' => 'role_id',
            'title' => ts('Participant Role (Recent Period)'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Event_PseudoConstant::participantRole(),
            'joinclause' => TRUE,
          ),
          'participant_register_date' => array(
            'title' => ts('Registration Date (Recent Period)'),
            'operatorType' => CRM_Report_Form::OP_DATE,
            'joinclause' => TRUE,
          ),
        ),
      ),
      'civicrm_event2' => array(
        'name' => 'civicrm_event',
        'dao' => 'CRM_Event_DAO_Event',
        'grouping' => 'event-fields',
        'filters' => array(
          'eid2' => array(
            'name' => 'event_type_id',
            'title' => ts('Event Type (Prior Period)'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Core_OptionGroup::values('event_type'),
            'joinclause' => TRUE,
          ),
          'event_start_date2' => array(
            'title' => ts('Event Start Date (Prior Period)'),
            'type' => CRM_Utils_Type::T_DATE,
            'operatorType' => CRM_Report_Form::OP_DATE,
            'name' => 'event_start_date',
            'default' => 'previous.year',
            'joinclause' => TRUE,
          ),
        ),
      ),
      'civicrm_participant2' => array(
        'name' => 'civicrm_participant',
        'dao' => 'CRM_Event_DAO_Participant',
        'fields' => array(
          'participant_id' => array('title' => 'Participant ID (Prior Period)'),
          'participant_record2' => array(
            'name' => 'id',
            'no_display' => TRUE,
            'required' => TRUE,
          ),
          'event_id2' => array(
            'name' => 'event_id',
            'default' => TRUE,
            'title' => ts('Events Attended (Prior Period)'),
            // 'type' => CRM_Utils_Type::T_STRING,
            'statistics' => array(
              'count_distinct' => ts('Events Attended (Prior Period)'),
            ),
          ),
          'unique_staff2' => array(
            'name' => 'contact_id',
            'default' => TRUE,
            'title' => ts('Unique Staff (Prior Period)'),
            // 'type' => CRM_Utils_Type::T_STRING,
            'statistics' => array(
              'count_distinct' => ts('Unique Staff (Prior Period)'),
            ),
          ),
        ),
        'grouping' => 'event-fields',
        'filters' => array(
          'event_id2' => array(
            'name' => 'event_id',
            'title' => ts('Event (Prior Period)'),
            'operatorType' => CRM_Report_Form::OP_ENTITYREF,
            'type' => CRM_Utils_Type::T_INT,
            'attributes' => array(
              'entity' => 'event',
              'select' => array('minimumInputLength' => 0),
            ),
            'joinclause' => TRUE,
          ),
          'sid2' => array(
            'name' => 'status_id',
            'title' => ts('Participant Status (Prior Period)'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Event_PseudoConstant::participantStatus(NULL, NULL, 'label'),
            'joinclause' => TRUE,
          ),
          'rid' => array(
            'name' => 'role_id',
            'title' => ts('Participant Role (Prior Period)'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Event_PseudoConstant::participantRole(),
            'joinclause' => TRUE,
          ),
          'participant_register_date2' => array(
            'name' => 'participant_register_date',
            'title' => ts('Registration Date (Prior Period)'),
            'operatorType' => CRM_Report_Form::OP_DATE,
            'joinclause' => TRUE,
          ),
        ),
      ),
    );

    $this->_groupFilter = TRUE;
    $this->_tagFilter = TRUE;

    parent::__construct();

    $this->_columns['civicrm_group']['filters']['gid']['title'] = ts('Group (Employer)');
    $this->_columns['civicrm_tag']['filters']['tagid']['title'] = ts('Tag (Employer)');

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

  /**
   * Move where() to before from().
   *
   * @param bool $applyLimit
   *   Limit the rows to the page limit.
   *
   * @return string
   */
  public function buildQuery($applyLimit = TRUE) {
    $this->select();
    $this->where();
    $this->from();
    $this->customDataFrom();
    $this->groupBy();
    $this->orderBy();

    // The order_by columns not selected for display need to be included in SELECT.
    $unselectedSectionColumns = $this->unselectedSectionColumns();
    foreach ($unselectedSectionColumns as $alias => $section) {
      $this->_select .= ", {$section['dbAlias']} as {$alias}";
    }

    if ($applyLimit && empty($this->_params['charts'])) {
      $this->limit();
    }
    CRM_Utils_Hook::alterReportVar('sql', $this, $this);

    $sql = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy} {$this->_having} {$this->_orderBy} {$this->_limit}";
    return $sql;
  }

  /**
   * Build the select for the query.
   */
  public function select() {
    parent::select();
    if (!empty($this->_params['fields']['event_id']) && !empty($this->_params['fields']['event_id2'])) {
      // $this->_select .= ", (100*((COUNT(DISTINCT {$this->_aliases['civicrm_participant']}.event_id) - COUNT(DISTINCT {$this->_aliases['civicrm_participant2']}.event_id)) / COUNT(DISTINCT {$this->_aliases['civicrm_participant2']}.event_id))) as event_id_pctchange";
      $this->_columnHeaders['event_id_pctchange']['title'] = ts('Events Attended Percent Change');
      $this->_columnHeaders['event_id_pctchange']['type'] = CRM_Utils_Type::T_FLOAT;
      // $this->_statFields[ts('Events Attended Percent Change')] = 'event_id_pctchange';
      // $this->_selectAliases[] = 'event_id_pctchange';
    }
    if (!empty($this->_params['fields']['unique_staff']) && !empty($this->_params['fields']['unique_staff2'])) {
      // $this->_select .= ", (100*((COUNT(DISTINCT {$this->_aliases['civicrm_participant']}.contact_id) - COUNT(DISTINCT {$this->_aliases['civicrm_participant2']}.contact_id)) / COUNT(DISTINCT {$this->_aliases['civicrm_participant2']}.contact_id))) as unique_staff_pctchange";
      $this->_columnHeaders['unique_staff_pctchange']['title'] = ts('Unique Staff Percent Change');
      $this->_columnHeaders['unique_staff_pctchange']['type'] = CRM_Utils_Type::T_FLOAT;
      // $this->_statFields[ts('Unique Staff Percent Change')] = 'unique_staff_pctchange';
      // $this->_selectAliases[] = 'unique_staff_pctchange';
    }
  }

  /**
   * Build the FROM for the query.
   */
  public function from() {
    $joinOn = array();
    foreach ($this->_joinClauses as $tableName => $clauses) {
      $joinOn[$tableName] = 'AND ' . implode(' AND ', $clauses);
    }
    $this->_from = "
        FROM civicrm_contact {$this->_aliases['civicrm_contact']}
          LEFT JOIN civicrm_contact {$this->_aliases['organization']}
            ON {$this->_aliases['civicrm_contact']}.employer_id = {$this->_aliases['organization']}.id
          LEFT JOIN (
            civicrm_participant {$this->_aliases['civicrm_participant']}
            JOIN civicrm_event {$this->_aliases['civicrm_event']}
              ON ({$this->_aliases['civicrm_event']}.id = {$this->_aliases['civicrm_participant']}.event_id ) AND
                ({$this->_aliases['civicrm_event']}.is_template = 0)
                " . CRM_Utils_Array::value('civicrm_event', $joinOn) . "
            )
            ON ({$this->_aliases['civicrm_participant']}.contact_id  = {$this->_aliases['civicrm_contact']}.id  )
            " . CRM_Utils_Array::value('civicrm_participant', $joinOn) . "

          LEFT JOIN (
            civicrm_participant {$this->_aliases['civicrm_participant2']}
            JOIN civicrm_event {$this->_aliases['civicrm_event2']}
              ON ({$this->_aliases['civicrm_event2']}.id = {$this->_aliases['civicrm_participant2']}.event_id ) AND
                ({$this->_aliases['civicrm_event2']}.is_template = 0)
                " . CRM_Utils_Array::value('civicrm_event2', $joinOn) . "
            )
            ON ({$this->_aliases['civicrm_participant2']}.contact_id  = {$this->_aliases['civicrm_contact']}.id  )
            " . CRM_Utils_Array::value('civicrm_participant2', $joinOn) . "

          {$this->_aclFrom}
    ";
  }

  /**
   * Altered to apply group to organization.
   */
  public function whereGroupClause($field, $value, $op) {

    $smartGroupQuery = "";

    $group = new CRM_Contact_DAO_Group();
    $group->is_active = 1;
    $group->find();
    $smartGroups = array();
    while ($group->fetch()) {
      if (in_array($group->id, $this->_params['gid_value']) &&
        $group->saved_search_id
      ) {
        $smartGroups[] = $group->id;
      }
    }

    CRM_Contact_BAO_GroupContactCache::check($smartGroups);

    $smartGroupQuery = '';
    if (!empty($smartGroups)) {
      $smartGroups = implode(',', $smartGroups);
      $smartGroupQuery = " UNION DISTINCT
                  SELECT DISTINCT smartgroup_contact.contact_id
                  FROM civicrm_group_contact_cache smartgroup_contact
                  WHERE smartgroup_contact.group_id IN ({$smartGroups}) ";
    }

    $sqlOp = $this->getSQLOperator($op);
    if (!is_array($value)) {
      $value = array($value);
    }
    $clause = "{$field['dbAlias']} IN (" . implode(', ', $value) . ")";

    return " {$this->_aliases['organization']}.id {$sqlOp} (
                          SELECT DISTINCT {$this->_aliases['civicrm_group']}.contact_id
                          FROM civicrm_group_contact {$this->_aliases['civicrm_group']}
                          WHERE {$clause} AND {$this->_aliases['civicrm_group']}.status = 'Added'
                          {$smartGroupQuery} ) ";
  }

  /**
   * Altered to apply group to organization.
   *
   * @param $field
   * @param $value
   * @param $op
   *
   * @return string
   */
  public function whereTagClause($field, $value, $op) {
    // not using left join in query because if any contact
    // belongs to more than one tag, results duplicate
    // entries.
    $sqlOp = $this->getSQLOperator($op);
    if (!is_array($value)) {
      $value = array($value);
    }
    $clause = "{$field['dbAlias']} IN (" . implode(', ', $value) . ")";
    return " {$this->_aliases['organization']}.id {$sqlOp} (
                          SELECT DISTINCT {$this->_aliases['civicrm_tag']}.entity_id
                          FROM civicrm_entity_tag {$this->_aliases['civicrm_tag']}
                          WHERE entity_table = 'civicrm_contact' AND {$clause} ) ";
  }

  /**
   * The WHERE of the report query.
   */
  public function where() {
    if (!count($this->_params['gid_value'])) {
      $this->_whereClauses[] = "({$this->_aliases['civicrm_event']}.id IS NOT NULL OR {$this->_aliases['civicrm_event2']}.id is not null)";
    }
    parent::where();
  }

  /**
   * Modified to handle filters that belong in the join on.
   */
  public function storeWhereHavingClauseArray() {
    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('filters', $table)) {
        foreach ($table['filters'] as $fieldName => $field) {
          // respect pseudofield to filter spec so fields can be marked as
          // not to be handled here
          if (!empty($field['pseudofield'])) {
            continue;
          }
          $clause = NULL;
          if (CRM_Utils_Array::value('type', $field) & CRM_Utils_Type::T_DATE) {
            if (CRM_Utils_Array::value('operatorType', $field) ==
              CRM_Report_Form::OP_MONTH
            ) {
              $op = CRM_Utils_Array::value("{$fieldName}_op", $this->_params);
              $value = CRM_Utils_Array::value("{$fieldName}_value", $this->_params);
              if (is_array($value) && !empty($value)) {
                $clause
                  = "(month({$field['dbAlias']}) $op (" . implode(', ', $value) .
                  '))';
              }
            }
            else {
              $relative = CRM_Utils_Array::value("{$fieldName}_relative", $this->_params);
              $from = CRM_Utils_Array::value("{$fieldName}_from", $this->_params);
              $to = CRM_Utils_Array::value("{$fieldName}_to", $this->_params);
              $fromTime = CRM_Utils_Array::value("{$fieldName}_from_time", $this->_params);
              $toTime = CRM_Utils_Array::value("{$fieldName}_to_time", $this->_params);
              $clause = $this->dateClause($field['dbAlias'], $relative, $from, $to, $field['type'], $fromTime, $toTime);
            }
          }
          else {
            $op = CRM_Utils_Array::value("{$fieldName}_op", $this->_params);
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
            if (!empty($field['having'])) {
              $this->_havingClauses[] = $clause;
            }
            elseif (!empty($field['joinclause'])) {
              $this->_joinClauses[$tableName][] = $clause;
            }
            else {
              $this->_whereClauses[] = $clause;
            }
          }
        }
      }
    }

  }

  /**
   * GROUP BY for the query.
   */
  public function groupBy() {
    parent::groupBy();
    // $this->_groupBy = "GROUP BY {$this->_aliases['organization']}.id";
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
   * Rearrange columns to put groupings first.
   */
  public function modifyColumnHeaders() {

    // Get a list of the column headers for the groupBys.
    $groupBys = $this->_params['group_bys'];
    foreach ($groupBys as $key => $value) {
      if ($value) {
        foreach ($this->_columns as $tableName => $table) {
          if (array_key_exists('group_bys', $table)) {
            foreach ($table['group_bys'] as $fieldName => $field) {
              if (!empty($this->_params['group_bys'][$fieldName]) && $fieldName == $key) {
                $groupBys[$key] = ($key == 'org_id') ? 'organization_display_name' : "{$tableName}_{$key}";
              }
            }
          }
        }
      }
    }

    // Work through the groupBys in reverse order.
    array_reverse($groupBys);
    foreach ($groupBys as $key => $value) {
      if (!empty($this->_columnHeaders[$value])) {
        $x = $this->_columnHeaders[$value];
        unset($this->_columnHeaders[$value]);
        $this->_columnHeaders = array_merge(array($value => $x), $this->_columnHeaders);
      }
    }
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
    foreach ($rows as $rowNum => $row) {
      if (!empty($this->_params['fields']['event_id']) && !empty($this->_params['fields']['event_id2'])) {
        if ($row["civicrm_participant2_event_id2_count_distinct"] > 0) {
          $rows[$rowNum]['event_id_pctchange'] = 100 * ($row["civicrm_participant_event_id_count_distinct"] - $row["civicrm_participant2_event_id2_count_distinct"]) / $row["civicrm_participant2_event_id2_count_distinct"];
        }
        else {
          $rows[$rowNum]['event_id_pctchange'] = ($row["civicrm_participant_event_id_count_distinct"] > 0) ? 100 : 0;
        }
        $rows[$rowNum]['event_id_pctchange'] = round($rows[$rowNum]['event_id_pctchange']) . '%';
      }

      if (!empty($this->_params['fields']['unique_staff']) && !empty($this->_params['fields']['unique_staff2'])) {
        if ($row['civicrm_participant2_unique_staff2_count_distinct'] > 0) {
          $rows[$rowNum]['unique_staff_pctchange'] = 100 * ($row["civicrm_participant_unique_staff_count_distinct"] - $row["civicrm_participant2_unique_staff2_count_distinct"]) / $row["civicrm_participant2_unique_staff2_count_distinct"];
        }
        else {
          $rows[$rowNum]['unique_staff_pctchange'] = ($row["civicrm_participant_unique_staff_count_distinct"] > 0) ? 100 : 0;
        }
        $rows[$rowNum]['unique_staff_pctchange'] = round($rows[$rowNum]['unique_staff_pctchange']) . '%';
      }
    }
  }

}
