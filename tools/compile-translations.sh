#!/bin/bash
compileTranslations(){
  local parentPath;
  parentPath=$(
                     cd "$(dirname "${BASH_SOURCE[0]}")" || exit
                     pwd -P
                   )

  cd "$parentPath"/../prosopo-procaptcha || { echo "Failed to change directory to prosopo-procaptcha"; return 1; }

  wp i18n make-mo ./lang
  wp i18n make-php ./lang

  return $?
}

compileTranslations
