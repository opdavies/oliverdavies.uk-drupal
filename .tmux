#!/usr/bin/env bash

set -o errexit

# 1. Vim.
tmux send-keys -t "$1:1" "nvim" Enter

# 2. Server.
tmux new-window -t "$1" -c "$PWD"
tmux send-keys -t "$1:2" "docker compose up --remove-orphans" Enter
tmux split-window -t "$1:2" -v
tmux send-keys -t "$1:2.bottom" "./run test" Enter

# 3. General shell use.
tmux new-window -t "$1" -c "$PWD"
tmux send-keys -t "$1:3" "git status" Enter

tmux select-window -t "$1:1"
