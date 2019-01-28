#!/usr/bin/env bash

commit=$1
if [ -z ${commit} ]; then
    commit=$(git tag | tail -n 1)
    if [ -z ${commit} ]; then
        commit="master";
    fi
fi

# Remove old release
rm -rf SpShareBasket SpShareBasket-*.zip

# Build new release
mkdir -p SpShareBasket
git archive ${commit} | tar -x -C SpShareBasket
composer install --no-dev -n -o -d SpShareBasket
zip -x "*build.sh*" -x "*.MD" -r SpShareBasket-${commit}.zip SpShareBasket
