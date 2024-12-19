#!/bin/bash
runPhpCodeBeautifer() {
  local parentPath="$1"

  cd "$parentPath"/../php-tools/code-quality || { echo "Failed to change directory to php-tools/code-quality"; return 1; }

  # 'phpcbf' is used to fix, while 'phpcs' to check
  bash -c "php ./vendor/bin/phpcbf --standard=./wp-ruleset.xml"

  return $?
}

checkPhpCodeSniffer() {
  local parentPath="$1"

  cd "$parentPath"/../php-tools/code-quality || { echo "Failed to change directory to php-tools/code-quality"; return 1; }

  # 'phpcbf' is used to fix, while 'phpcs' to check
  bash -c "php ./vendor/bin/phpcs --standard=./wp-ruleset.xml"

  return $?
}

checkPhpStan() {
  local parentPath="$1"

  cd "$parentPath"/../php-tools/code-quality || { echo "Failed to change directory to php-tools/code-quality"; return 1; }

  config="phpstan.neon"

  bash -c "php vendor/bin/phpstan analys -c $config"

  return $?
}

checkPhpPest(){
  local parentPath="$1"

  cd "$parentPath"/../php-tools/code-quality || { echo "Failed to change directory to php-tools/code-quality"; return 1; }

  php vendor/bin/pest

  return $?
}

checkJsEslint(){
  local parentPath="$1"

  cd "$parentPath"/../assets || { echo "Failed to change directory to assets"; return 1; }

  yarn lint:check

  return $?
}

checkJsPrettier(){
  local parentPath="$1"

  cd "$parentPath"/../assets || { echo "Failed to change directory to assets"; return 1; }

  yarn prettier:check

  return $?
}

detectCurrentType(){
  local type=$1
  local valid_types=("phpstan" "codesniffer" "codebeautifer" "pest" "eslint" "prettier")

   for valid_type in "${valid_types[@]}"; do
      if [ "$type" == "$valid_type" ]; then
        echo "$type"
        return
      fi
    done

  echo "all"
}

exitWhenFailed(){
  local exitStatus=$1

  if [ "$exitStatus" -ne 0 ]; then
    echo -e "\n> One of the checks is failed\n"
    exit "$exitStatus"
  fi
}

runChecks(){
  local type
  local exitStatus
  local parentPath

  type=$(detectCurrentType "$1")
  exitStatus=0
  parentPath=$(
          cd "$(dirname "${BASH_SOURCE[0]}")" || exit
          pwd -P
        )

 declare -A checksList=(
   ["codesniffer"]="checkPhpCodeSniffer"
   ["phpstan"]="checkPhpStan"
   ["pest"]="checkPhpPest"
   ["eslint"]="checkJsEslint"
   ["prettier"]="checkJsPrettier"
 )

 for tool in "${!checksList[@]}"; do
   if [ "$type" == "all" ] || [ "$type" == "$tool" ]; then
     "${checksList[$tool]}" "$parentPath"
     exitWhenFailed $?
   fi
 done

 # separately, as it shouldn't be run when the type is 'all'.
 if [ "$type" == "codebeautifier" ]; then
      runPhpCodeBeautifer "$parentPath"
      exitWhenFailed $?
 fi

  echo -e "\n> Everything looks good\n"

  exit 0
}

runChecks "$1"
