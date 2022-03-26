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
  $pdo = new PDO('mysql:host=localhost;dbname='.$DB_NAME, $DB_USERNAME, $DB_PASSWORD);
  $statement = $pdo->prepare("update users_field_data set mail=:email, status=1, created=NOW(), changed=NOW() where name=:username and status=0");
  $statement->execute(array(':email' => Get('UserEmailAddress', 'invalid@solidcharity.com'), ':username' => 'civi_admin'));

  # initiate password reset, without sending the email
  $token = Get('PasswordResetToken', 'invalid');
  if ($token != 'invalid') {
    $statement = $pdo->prepare("update public.password_reset set is_active = false where is_active = true");
    $statement->execute();
    $statement = $pdo->prepare("insert into public.password_reset(token, user_id, date_expiration, date_creation, ip, user_agent, is_active)".
      " values(:token, 1, :date_expiration, :date_creation, '127.0.0.1', 'php', true)");
    $statement->execute(array(':token' => $token, ':date_expiration' => time()+30*60, 'date_creation' => time()));
  }
}
catch (Exception $e) {
    // echo 'Exception caught: ',  $e->getMessage(), "\n";
    echo '{"success": false, "msg": "error happened"}';
    exit(1);
}

echo '{"success": true}';
?>
