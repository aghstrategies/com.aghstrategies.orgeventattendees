<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array(
  0 => array(
    'name' => 'CRM_Orgeventattendees_Form_Report_OrgSummary',
    'entity' => 'ReportTemplate',
    'params' => array(
      'version' => 3,
      'label' => 'Event Attendees by Organization',
      'description' => 'Display numbers of events attended and staff attending events from each organization (com.aghstrategies.orgeventattendees)',
      'class_name' => 'CRM_Orgeventattendees_Form_Report_OrgSummary',
      'report_url' => 'event/orgSummary',
      'component' => 'CiviEvent',
    ),
  ),
);
