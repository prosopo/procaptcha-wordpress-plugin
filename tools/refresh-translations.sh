#!/bin/bash
generatePotFile(){
  wp i18n make-pot .

  return $?
}

updatePoFiles(){
  cd ./lang || {
    echo "Failed to change directory to lang"
    return 1
  }

  wp i18n update-po prosopo-procaptcha.pot

  return $?
}

exitIfFailed(){
  local exitCode="$1"

  if [ "$exitCode" -ne 0 ]; then
    echo -e "\n> Failed to refresh translations\n"
    exit "$exitCode"
  fi
}

refreshTranslations(){
  local parentPath;

  parentPath=$(
    cd "$(dirname "${BASH_SOURCE[0]}")" || exit
    pwd -P
  )

  cd "$parentPath"/../prosopo-procaptcha || {
    echo "Error: Could not change directory to the plugin root."
    exit 1
  }

  generatePotFile
  exitIfFailed $?

  updatePoFiles
  exitIfFailed $?

  echo -e "\n> All translations are successfully refreshed\n"
}

refreshTranslations
