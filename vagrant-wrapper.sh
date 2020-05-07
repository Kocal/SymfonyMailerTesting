#!/usr/bin/env bash

vagrant_wrapper() {
    local user_command=$@

    # we assume that we are outside the VM if command `vagrant` is available
    if [[ -x "$(command -v vagrant)" ]]; then
        vagrant ssh -- "cd /srv/app && ${user_command}"
    else
        eval ${user_command}
    fi
}

vagrant_wrapper $@
