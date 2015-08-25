<?php
/**
 * @file
 * Event Attendees by Organization
 *
 * A CiviCRM extension to add two reports:
 * - A summary report to display numbers of events attended and staff attending
 *   events from each organization
 * - A detail report intended to display the events available to attend for an
 *   organization, along with the contacts attending.
 *
 * Copyright (C) 2015, AGH Strategies, LLC <info@aghstrategies.com>
 * Licensed under the GNU Affero Public License 3.0 (see LICENSE.txt)
 */

require_once 'orgeventattendees.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function orgeventattendees_civicrm_config(&$config) {
  _orgeventattendees_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function orgeventattendees_civicrm_xmlMenu(&$files) {
  _orgeventattendees_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function orgeventattendees_civicrm_install() {
  _orgeventattendees_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function orgeventattendees_civicrm_uninstall() {
  _orgeventattendees_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function orgeventattendees_civicrm_enable() {
  _orgeventattendees_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function orgeventattendees_civicrm_disable() {
  _orgeventattendees_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function orgeventattendees_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _orgeventattendees_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function orgeventattendees_civicrm_managed(&$entities) {
  _orgeventattendees_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function orgeventattendees_civicrm_caseTypes(&$caseTypes) {
  _orgeventattendees_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function orgeventattendees_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _orgeventattendees_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
