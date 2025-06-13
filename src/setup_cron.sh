#!/bin/bash

# Absolute path to the current script directory
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CRON_CMD="php $DIR/cron.php"
CRON_JOB="0 * * * * $CRON_CMD"

# Install the CRON job if it's not already present
(crontab -l 2>/dev/null | grep -v -F "$CRON_CMD" ; echo "$CRON_JOB") | crontab -

echo "✅ Cron job scheduled to run every hour: $CRON_JOB"
