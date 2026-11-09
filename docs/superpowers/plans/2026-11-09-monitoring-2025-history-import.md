# Monitoring PSTK 2025 History Import Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use `superpowers:subagent-driven-development` or `superpowers:executing-plans` to implement this plan task-by-task. Steps use checkbox syntax for tracking.

**Goal:** Import 2025 monitoring responses into Gen2 report history while reusing, not overwriting or duplicating, existing institution, program, and accreditation master data.

**Architecture:** A new provenance table records the source reference and monitoring-answer payload, keyed idempotently to a submitted version. The importer resolves profile/program/accreditation from Gen2, creates or reuses an eligible report row, and inserts its historical version transactionally. Secretariat detail reads identity from the version's master snapshots and monitoring results from the imported answers.

**Tech Stack:** Laravel 8, PHP, Eloquent, MySQL, Artisan, Blade.

**Spec:** `docs/superpowers/specs/2026-11-09-monitoring-2025-history-import-design.md`

## Global Constraints

- Gen2 is the only write target; never update profile, program, or accreditation master rows from the spreadsheet.
- The importer stores questions 10–26 as report history; question 9 is `report_year`; questions 1–8 come from matched Gen2 snapshots.
- Source headers must match all 29 expected columns in order; blank response rows are skipped; malformed rows block the entire write run.
- Dry-run performs reads only. Write mode prevalidates the full file and inserts all valid rows in one transaction or inserts none.
- Existing report rows with no versions may be reused; any existing version without this import's provenance key is a conflict.
- Reimport with the same source key and content hash is a no-op, including when later report versions exist.
- Keep contact and identity data out of provenance payloads, logs, and ordinary exports.
- Parse timestamps using `d/m/Y G:i:s` in `Asia/Jakarta` by default; support `--timestamp-format` and `--source-timezone` overrides.
- Do not add or run an automated test suite in this work. Verify PHP syntax, Blade compilation, route registration, and the importer's explicit dry-run before any authorized write.

## Review Focus

- Certificate number spacing varies: verify normalization matches exactly one accreditation while preserving punctuation; owned by Task 2's dry-run checks.
- Legacy answer values can be outside current form options: keep them visible and unmodified; owned by Task 3's rendered detail check.
- Existing draft/link report rows may already exist: reuse only when there are no versions; otherwise block without writes; owned by Task 2's dry-run checks.
- A literal `-` in numeric questions 10–12 maps to zero; empty numeric fields map to zero only for a `Tidak` response. Unanswered narrative fields stay blank; verify in the import dry run.
- Source rows can be malformed or repeated: block differing duplicates and malformed rows; skip identical duplicates once; owned by Task 2's dry-run checks.

---

### Task 1: Provenance schema and model

**Files:**
- Create: `database/migrations/2026_11_09_000001_create_monitoring_report_import_sources.php`
- Create: `app/Models/MonitoringReportImportSource.php`
- Modify: `app/Models/MonitoringReport.php`

**Interfaces:**
- Produces `MonitoringReportImportSource` with `report()` relation, fillable/guarded data, and a report `importSources()` relation.
- Table columns: `id`, `monitoring_report_id`, `source_ref`, `source_key` char(64), `content_hash` char(64), `source_row`, `source_timestamp`, `raw_responses` JSON, `imported_at`, timestamps.
- Unique index: explicit short name `mr_import_source_key_uq`; report foreign key uses existing monitoring report table.

- [ ] Create an idempotent migration following local schema conventions, with the concise named unique index and report index.
- [ ] Create the Eloquent model and relationship; do not add any fields containing profile contact/master values.
- [ ] Run `php -l` on the new model/migration and `php artisan migrate --pretend` to confirm the SQL is valid without applying it.

### Task 2: CSV history importer

**Files:**
- Create: `app/Console/Commands/ImportMonitoringReportHistory.php`
- Modify: `app/Console/Kernel.php` only if commands are not auto-discovered in this project.
- Use: `MonitoringAccreditation`, `MonitoringReport`, `MonitoringReportVersion`, `MonitoringReportImportSource`, `Profile`, `JenisPengajuan`.

**Interfaces:**
- Command signature: `monitoring:import-history {--file=} {--source-ref=} {--dry-run} {--timestamp-format=d/m/Y G:i:s} {--source-timezone=Asia/Jakarta}`.
- Matching: exact normalized certificate, then exact normalized `Profile.nama_lembaga` and `JenisPengajuan.nama` or the explicit reviewed source aliases in the design doc; normalize predicate from `A`/`B`/`C` or exact `Terakreditasi A/B/C` labels and compare to the matched master. Predicate mismatch blocks import with both values shown for reconciliation.
- Source key: SHA-256 of canonical JSON `[trimmed source_ref, trimmed timestamp, normalized certificate, normalized institution name]`.
- Content hash: SHA-256 of canonical JSON for all ordered header/value pairs; raw persisted payload contains only timestamp and response columns 11–29.
- Report creation: `status=submitted`, `submitted_version=1`, `edit_version=2`; version 1 channel `historical_import`, `submitted_by=null`; snapshots come from matched Gen2 master data.

- [ ] Parse UTF-8 BOM and quoted CSV, require exact ordered headers, reject row widths other than 29 and invalid UTF-8, and skip rows where columns 2–29 are blank.
- [ ] Parse timestamp with configured PHP format/timezone; block populated rows with invalid timestamps.
- [ ] Map columns 11–29 to answers 10–26. Split comma-separated questions 14, 17, 18 into trimmed arrays. For the no-training indicator `Tidak`, map blank 10, 11, 12a, 12b to integer zero; preserve blanks in raw response payload.
- [ ] Resolve exactly one official accreditation and verify institution and program with reviewed explicit aliases plus predicate. Do not fuzzy match or update master records.
- [ ] Preflight every row before writing. Treat same source key/hash as already imported; count an identical duplicate in the CSV once; block same-key/different-hash rows.
- [ ] Reuse a matching report only if it has no versions and no submitted version. Treat any other pre-existing report history as a conflict unless the import provenance already matches.
- [ ] In write mode, use one Gen2 transaction for provenance, report, and submitted version inserts; if any row is blocked, write nothing. Dry-run must not mutate any table and must not print contact/identity values.
- [ ] Run `php -l app/Console/Commands/ImportMonitoringReportHistory.php` and `php artisan list --raw` to confirm syntax and command registration.

### Task 3: Secretariat detail rendering

**Files:**
- Modify: `resources/views/sekretariat/monitoring-akreditasi-detail.blade.php`

**Interfaces:**
- Questions 1–8 fall back to the version's `identity_snapshot` and `accreditation_snapshot`; question 9 falls back to `report_year`; answers 10–26 render from version `answers`.
- Prefer an answer stored on a normal form version where present; use the master snapshot fallback for imported legacy versions.
- Arrays render as comma-separated option values; scalars render as text; missing values render as `—`.

- [ ] Replace the direct array-to-string output with a formatter supporting scalar and array answer values.
- [ ] Build the identity/accreditation/year fallback without exposing import provenance JSON.
- [ ] Run `php artisan view:cache` to verify Blade compilation.

### Task 4: Integrate the approved history import

**Files:**
- Modify: `dokumen_md/plan_monitoring_akreditasi_pstk.md` to add Task 1.5 and status.
- Input: user's 2025 response CSV export; do not add it to the repository.

**Interfaces:**
- Consume the Task 2 command and Task 1 schema.
- Persist only when all three populated source rows pass dry-run matching and conflict checks; skip the timestamp-only row.

- [ ] Run dry-run with the user's sheet CSV and its sheet ID/gid source reference; confirm three valid candidates, one blank skipped, zero blocked rows, and no writes.
- [ ] If dry-run reports any unmatched/conflicting row, do not write; resolve only through explicit master data or report the exact blocker.
- [ ] After a clean dry-run, run the write command once to persist the three historical versions in Gen2.
- [ ] Re-run dry-run to confirm the same three rows are idempotent no-ops.
- [ ] Confirm `monitoring_reports`, `monitoring_report_versions`, and provenance counts/relationships, with no changes to profile or accreditation master rows.
- [ ] Update the plan's task breakdown to identify Task 1.5 as complete and note remaining Task 3/4 work.

## Completion checks

- `php -l` passes for created/modified PHP files.
- `php artisan migrate --pretend`, `php artisan list --raw`, and `php artisan view:cache` complete successfully.
- Dry-run is read-only and shows the expected row classifications; write mode imports exactly the validated rows.
- No existing Task 2 edits are staged, reverted, or included in this plan's final diff unless separately requested.
