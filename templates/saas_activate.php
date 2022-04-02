<?php

include "/home/pacs/{{pac}}/users/{{user}}/drupal-civicrm/web/sites/default/settings.php";

function Get($index, $defaultValue) {
  return isset($_GET[$index]) ? $_GET[$index] : $defaultValue;
}

# check SaasActivationPassword
if (Get('SaasActivationPassword', 'invalid') != '{{SaasActivationPassword}}') {
  echo '{"success": false, "msg": "invalid SaasActivationPassword"}';
  exit(1);
}

try {
  $DB_NAME = $databases['default']['default']['database'];
  $DB_USERNAME = $databases['default']['default']['username'];
  $DB_PASSWORD = $databases['default']['default']['password'];
  $USER_EMAIL_ADDRESS = Get('UserEmailAddress', '');
  if (empty($USER_EMAIL_ADDRESS)) {
    echo '{"success": false, "msg": "missing email address"}';
    exit(1);
  }
  $pdo = new PDO('mysql:host=localhost;dbname='.$DB_NAME, $DB_USERNAME, $DB_PASSWORD);
  $statement = $pdo->prepare("update users_field_data set mail=:email, init=:email, status=1, created=NOW(), changed=NOW() where name=:username and status=0");
  $statement->execute(array(':email' => $USER_EMAIL_ADDRESS, ':username' => 'civi_admin'));

  $statement = $pdo->prepare("update civicrm_email set email=:email where email='admin@example.com'");
  $statement->execute(array(':email' => $USER_EMAIL_ADDRESS));
}
catch (Exception $e) {
    // echo 'Exception caught: ',  $e->getMessage(), "\n";
    echo '{"success": false, "msg": "error happened"}';
    exit(1);
}

echo '{"success": true}';
?>
