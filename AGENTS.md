# AI Agent Guidelines & Infimium Workflow

This document provides persistent rules and operational instructions for all AI coding agents (Antigravity, Gemini, Claude, Cursor, etc.) working on this repository.

---

## 1. Infimium Core Capabilities & Workflow

### What `infimium watch` Does

- **Command**: `infimium watch`
- **Functionality**: Runs a continuous background file watcher (similar to `npm run dev` or `vite`).
- **How it works**: Whenever you create, edit, or delete a code or documentation file, it automatically re-parses and updates the local vector database in real time.
- **When to use**: Keep running in a terminal tab during active coding sessions so that AI agent context and dependency graphs remain 100% synchronized with the latest saves without manual indexing.

---

## 2. Infimium Command Cheat-Sheet

| Command                      | What It Does                                                                     | When To Run It                                                                          |
| :--------------------------- | :------------------------------------------------------------------------------- | :-------------------------------------------------------------------------------------- |
| `infimium watch`             | Real-time continuous file watcher and indexer                                    | Keep running in a small terminal tab during active coding                               |
| `infimium remember "<text>"` | Stores permanent rules, architectural decisions, and milestones into disk memory | **MANDATORY**: Always ask the user for confirmation before running `infimium remember` |
| `infimium resume`            | Displays recent milestones, rules, and unfinished tasks                          | At the start of a day/session to see where you or the agent left off                    |
| `infimium get-context`       | Exports a compressed summary (`layer.md`) of the project                         | To quickly inspect what high-level context the AI agent receives                        |
| `infimium playground`        | Launches visual UI in browser at `http://localhost:1434`                         | Whenever you want to visually explore the dependency map and stored memory              |
| `infimium doctor`            | Runs health check on Ollama, SQLite, and index status                            | If searches fail or the agent claims it cannot find symbols                             |

> **Rule on Memory Retention**: The agent must NEVER run `infimium remember` automatically without first explicitly asking the user and receiving approval.

---
