#!/bin/sh

set -e

cd "$(dirname "$0")"

php -f killed.cron.php
