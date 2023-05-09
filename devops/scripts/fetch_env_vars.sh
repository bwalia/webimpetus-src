#!/bin/bash

set -x

rm -Rf env
cp /home/bwalia/env_webimpetus_dev_ci4baseimagetest env
prepare_test_environment.sh
# sed "s^#