#!/bin/bash

installPhpCodeQuality(){
  local parentPath="$1"

    cd "$parentPath"/../php-code-quality-tools || {
        echo "php-code-quality-tools not found"
        return 1
    }

    composer install

    return $?
}

installAssets(){
  local parentPath="$1"

  cd "$parentPath"/../assets || {
    echo "assets not found"
    return 1
  }

  corepack use yarn@latest

  return $?
}

installTests(){
  local parentPath="$1"

  cd "$parentPath"/../tests || {
    echo "tests not found"
    return 1
  }

  corepack use yarn@latest

  return $?
}

exitWhenFailed(){
  local exitCode="$1"

  if [ "$exitCode" -ne 0 ]; then
    echo -e "\n> Failed to install tools\n"
    exit "$exitCode"
  fi
}

installAll(){
  local parentPath;

  parentPath=$(
    cd "$(dirname "${BASH_SOURCE[0]}")" || exit
    pwd -P
  )

  corepack enable
  exitWhenFailed $?

  installPhpCodeQuality "$parentPath"
  exitWhenFailed $?

  installAssets "$parentPath"
  exitWhenFailed $?

  installTests "$parentPath"
  exitWhenFailed $?

  echo -e "\n> All tools are successfully installed\n"
}

installAll
