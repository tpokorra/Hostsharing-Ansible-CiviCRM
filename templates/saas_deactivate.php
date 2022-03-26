<?php

include "/home/pacs/{{pac}}/users/{{user}}/drupal-civicrm/web/sites/default/settings.php";

function Get($index, $defaultValue) {
    return isset($_GET[$index]) ? $_GET[$index] : $defaultValue;
}

function is_run_from_cli() {
    if( defined('STDIN') )
    {
        return true;
    }
    return false;
}

if (!is_run_from_cli()) {
    # check SaasActivationPassword
    if (Get('SaasActivationPassword', 'invalid') != '{{SaasActivationPassword}}') {
        echo '{"success": false, "msg": "invalid SaasActivationPassword"}';
        exit(1);
    }
}

try {
    $DB_NAME = $databases['default']['default']['database'];
    $DB_USERNAME = $databases['default']['default']['username'];
    $DB_PASSWORD = $databases['default']['default']['password'];
    $pdo = new PDO('mysql:host=localhost;dbname='.$DB_NAME, $DB_USERNAME, $DB_PASSWORD);
    # deactivate all users
    $statement = $pdo->prepare("update users_field_data set status=0");
    $statement->execute();
  }
  catch (Exception $e) {
      // echo 'Exception caught: ',  $e->getMessage(), "\n";
      echo "error happened";
      exit(1);
  }
?>