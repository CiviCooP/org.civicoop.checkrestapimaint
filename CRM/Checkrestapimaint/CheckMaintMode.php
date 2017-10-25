<?php
/**
 * Class to process APIWrapper hook
 * Check if User Framework is in maintenance mode and if so, throw Exception
 * for all API calls
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date October 2017
 * @license AGPL-3.0
 */

class CRM_Checkrestapimaint_CheckMaintMode implements API_Wrapper
{

  /**
   * Method required for APIWrapper
   *
   * @param array $apiRequest
   * @throws Exception when in maintenance mode
   * @return array
   */
  function fromApiInput($apiRequest)
  {
    // only when api comes in from REST (key and api_key are both in request values)
    $requestValues = CRM_Utils_Request::exportValues();
    if (isset($requestValues['key']) && isset($requestValues['api_key'])) {
      // determine if site is in maintenance mode depending on userframework
      $maintenanceMode = $this->checkMaintenanceMode(CRM_Core_Config::singleton()->userFramework);
      if ($maintenanceMode == TRUE) {
        throw new API_Exception(ts('API request with REST can not be processed because site is in maintenance mode'), 'maintenance_mode');
      }
    }
    return $apiRequest;
  }

  /**
   * Method required for APIWrapper
   *
   * @param array $apiRequest
   * @param array $result
   * @return array
   */
  function toApiOutput($apiRequest, $result)
  {
    return $result;
  }

  /**
   * Method to check if site is in maitenance mode based on user framework
   *
   * @param $userFramework
   * @return bool
   */
  private function checkMaintenanceMode($userFramework) {
    switch ($userFramework) {
      case 'Drupal':
        if (variable_get('maintenance_mode', 0)) {
          return TRUE;
        }
        break;
      default:
        return FALSE;
    }
  }

}