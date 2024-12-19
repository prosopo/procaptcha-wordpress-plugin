#!/bin/bash

exitWhenFailed(){
  local exitCode="$1"

  if [ "$exitCode" -ne 0 ]; then
    echo -e "\n> Failed to scope php vendors\n"
    exit "$exitCode"
  fi
}

scopePhpVendors(){
  local parentPath

  parentPath=$(
      cd "$(dirname "${BASH_SOURCE[0]}")" || exit
      pwd -P
  )

  local pathToOriginVendors="$parentPath"/../php-tools/origin-vendors
  local pathToPrefixedVendorsDir="$parentPath"/../prosopo-procaptcha/prefixed-vendors
  local pathToScoperDir="$parentPath"/../php-tools/scoper
  local pathToScoperConfig="$pathToScoperDir"/scoper.inc.php

  # 1. remove the current folder (if present)
  if [ -d "$pathToPrefixedVendorsDir" ]; then
        rm -rf "$pathToPrefixedVendorsDir"
  fi

 # 2. cd to the origin vendors folder (we must launch scoper from it)
  cd "$pathToOriginVendors" || {
     echo "Failed to change directory to originVendors"
     return 1
  }
  exitWhenFailed $?

  # 3. launch scoper
  "$pathToScoperDir"/vendor/bin/php-scoper add-prefix --config "$pathToScoperConfig" --output-dir "$pathToPrefixedVendorsDir"
  exitWhenFailed $?

  # 4. generate autoload
  composer dump-autoload --working-dir "$pathToPrefixedVendorsDir"
  exitWhenFailed $?

  echo "Successfully scoped!"
}

scopePhpVendors
