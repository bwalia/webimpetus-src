#!/bin/bash

set -x

rm -Rf env
cp /home/bwalia/env_workerra-ci_dev_ci4baseimagetest env
prepare_test_environment.sh
# sed "s^#