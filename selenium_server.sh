#!/bin/bash
# Install Selenium server, http://www.seleniumhq.org
#
# Add at least the following environment variables to your project configuration
# (otherwise the defaults below will be used).
# * SELENIUM_VERSION
# * SELENIUM_PORT
#
# Include in your builds via
# \curl -sSL https://raw.githubusercontent.com/codeship/scripts/master/packages/selenium_server.sh | bash -s
SELENIUM_VERSION=${SELENIUM_VERSION:="2.46.0"}
SELENIUM_PORT=${SELENIUM_PORT:="4444"}
SELENIUM_OPTIONS=${SELENIUM_OPTIONS:=""}
SELENIUM_WAIT_TIME=${SELENIUM_WAIT_TIME:="10"}

set -e

MINOR_VERSION=${SELENIUM_VERSION%.*}
CACHED_DOWNLOAD="${HOME}/cache/selenium-server-standalone-${SELENIUM_VERSION}.jar"

wget --continue --output-document "${CACHED_DOWNLOAD}" "http://selenium-release.storage.googleapis.com/${MINOR_VERSION}/selenium-server-standalone-${SELENIUM_VERSION}.jar"
java -jar "${CACHED_DOWNLOAD}" -port "${SELENIUM_PORT}" ${SELENIUM_OPTIONS} 2>&1 &
sleep "${SELENIUM_WAIT_TIME}"
echo "Selenium ${SELENIUM_VERSION} is now ready to connect on port ${SELENIUM_PORT}..."
