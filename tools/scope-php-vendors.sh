#!/bin/bash

function scopePhpVendors(){
   local parentPath=$(
      cd "$(dirname "${BASH_SOURCE[0]}")" || exit
      pwd -P
    )

  local pathToPrefixedVendorsDir="$parentPath"/../prosopo-procaptcha/prefixed-vendors

  if [ -d "$pathToPrefixedVendorsDir" ]; then
        rm -rf "$pathToPrefixedVendorsDir"
  fi

  cd "$parentPath"/../php-tools/scoper || {
        echo "php-tools/scoper not found"
        return 1
  }

  ./vendor/bin/php-scoper add-prefix --config ./scoper.inc.php --output-dir "$pathToPrefixedVendorsDir"

   cd "$parentPath"/../php-tools/origin-vendors || {
          echo "php-tools/origin-vendors not found"
          return 1
  }

    # the optimize flag generates a class list, and significantly speeds up by avoiding unnecessary I/O operations
   composer dump-autoload --optimize --working-dir "$pathToPrefixedVendorsDir"

   echo "Successfully scoped!"
}

scopePhpVendors

