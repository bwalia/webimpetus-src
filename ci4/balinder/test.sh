#!/bin/bash
#!/usr/bin/env bash

declare -a webimpetus_array # declare the array                                                                                                                                                                  
# Read each line and use regex parsing (with Bash's `=~` operator)
# to extract the value.
while read -r line; do
  # Extract the value from between the double quotes
  # and add it to the array.
  [[ $line =~ :[[:blank:]]+\"(.*)\" ]] && webimpetus_array+=( "${BASH_REMATCH[1]}" )
done </var/www/html/webimpetus.json                                                                                                                                 

#declare -p webimpetus_array # print the array
for i in ${!webimpetus_array[@]}; do
  if [ $i -eq "1" ]; then
  APP_FULL_VERSION_NO="${webimpetus_array[$i]}"
  export APP_FULL_VERSION_NO="${webimpetus_array[$i]}"
  fi
  if [ $i -eq "2" ]; then
  APP_FULL_BUILD_NO="${webimpetus_array[$i]}"
  export APP_FULL_BUILD_NO="${webimpetus_array[$i]}"
  fi
done

echo $APP_FULL_VERSION_NO
echo $APP_FULL_BUILD_NO