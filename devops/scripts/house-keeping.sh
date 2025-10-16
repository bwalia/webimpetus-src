# Housekeeping script to delete stale temp files in specified directories.
# This script runs indefinitely, sleeping for a defined interval between cleanups.
# housekeeping-daily.sh is modified to run every 2 hours via cronjob in k3s cluster.
        #!/usr/bin/env bash

        set -euo pipefail

        SLEEP_INTERVAL_SECONDS=${SLEEP_INTERVAL_SECONDS:-60} # every 1 minute run
        TMP_MAX_AGE_MINUTES=${TMP_MAX_AGE_MINUTES:-3600}  # delete files older than 1 hour

        # Directories to clean up, space-separated. Default is /tmp
        TARGET_DIRECTORIES="/var/www/html/writable/session/"
        TARGET_DIRECTORIES=(${TARGET_DIRECTORIES:-"/var/www/html/writable/session/"})

            log() {
                echo "$(date '+%Y-%m-%d %H:%M:%S') - $1"
            }

            delete_stale_files() {
                local description=$1
                local directory=$2
                local max_age_minutes=$3

                if [[ ! -d "$directory" ]]; then
                    log "Skip: $directory does not exist"
                    return
                fi

                log "$description in $directory (older than ${max_age_minutes}m)"

                local before after
                before=$(find "$directory" -mindepth 1 -type f | wc -l || true)

                find "$directory" -mindepth 1 -type f -mmin "+$max_age_minutes" -print -delete | sed 's/^/  deleted: /'

                after=$(find "$directory" -mindepth 1 -type f | wc -l || true)

                log "Files before: $before | after: $after"
            }

            while true; do
                for dir in "${TARGET_DIRECTORIES[@]}"; do
                    delete_stale_files "Cleaning stale temp files" "$dir" "$TMP_MAX_AGE_MINUTES"
                done

                log "Sleeping for ${SLEEP_INTERVAL_SECONDS}s before next cleanup"
                sleep "$SLEEP_INTERVAL_SECONDS"
            done