#!/bin/bash
checkWpLogs() {
  local environment="$1"
  local pathToWP

  if [ "local" == "$environment" ]; then
    pathToWP="$HOME/Local Sites/procaptcha/app/public"
  else
    pathToWP="/var/www/procaptcha"
  fi

  if [ ! -d "$pathToWP" ]; then
    echo "Path to WordPress is wrong (FAIL): $pathToWP"
    return 1
  fi

  local pathToWPDebugLog="$pathToWP/wp-content/debug.log"

  if [ ! -f "$pathToWPDebugLog" ]; then
    echo "debug.log doesn't exist (OK)"
    return 0
  fi

  echo "debug.log exists, checking for plugin-related lines: $pathToWPDebugLog"

  if grep -q "/prosopo-procaptcha/" "$pathToWPDebugLog"; then
    echo "debug.log contains plugin-related lines (FAIL)"
    return 1
  else
    echo "debug.log doesn't contain plugin-related lines (OK)"
    return 0
  fi
}

getSpecificationsPath(){
  local targetFolder=$1
  local specPath="./cypress/e2e/"

  if [[ "all" == "$targetFolder" ]]; then
      specPath="$specPath/**/*.cy.ts"
  else
      specPath="$specPath/$targetFolder/**/*.cy.ts"
  fi

  echo "$specPath"
}

runCypress(){
  local specificationsPath=$1

  npx cypress run --browser chrome --spec "$specificationsPath"

  local responseCode=$?

  if [ "$responseCode" -ne 0 ]; then
    echo "Cypress tests failed (FAIL)"
    return 1
  fi

  return 0
}

runTests(){
  local targetFolder="$1"
  local environment="$2"
  local parentPath;

  parentPath=$(
    cd "$(dirname "${BASH_SOURCE[0]}")" || exit
    pwd -P
  )

  cd "$parentPath"/../tests || {
      echo "Tests folder doesn't exist (FAIL)"
      return 1
  }

  specificationsPath=$(getSpecificationsPath "$targetFolder")

  runCypress "$specificationsPath"
  cypressStatus=$?

  checkWpLogs "$environment"
  wpLogsStatus=$?

  if [ "$cypressStatus" -ne 0 ] ||
  [ "$wpLogsStatus" -ne 0 ]; then
    return 1
  fi

  echo "All tests passed (OK)"

  return 0
}

runTests "$1" "$2"
exit $?
