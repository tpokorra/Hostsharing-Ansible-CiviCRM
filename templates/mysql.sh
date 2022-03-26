#!/bin/bash

cat web/sites/default/settings.php | grep -E "^  'password"
mysql -u {{pac}}_{{user}} {{pac}}_{{user}} -p