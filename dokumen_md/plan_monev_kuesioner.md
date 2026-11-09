# Monev Kuesioner Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans for inline execution or superpowers:subagent-driven-development for delegated execution. Track steps with checkbox (`- [ ]`) syntax for tracking.

**Goal:** Store one shared set of typed answers per institution, program and reporting year, with authenticated editing and export.

**Architecture:** Introduce questionnaire masters and normalized transactions alongside the existing tables. Dry-run a historical backfill before switching the form, link, detail and export routes together.

**Tech Stack:** Laravel, Blade, MySQL, Artisan, PHPUnit.

**Spec:** Agreed design below.

## Agreed design

- `mt_kuesioner` has code, name, type, version and active flag. `mt_pertanyaan` belongs to a questionnaire version and defines stable question code, block, label, display order, input type and choices. Existing reports stay pinned to their questionnaire version.
- `tr_monev` is unique by profile, program and year. It stores questionnaire ID, status, identity snapshot, timestamps, edit revision and nullable `submitted_by`. Any authenticated member of the matching profile can view and edit, including in Draft. Secretariat can view and export in its existing role.
- `tr_monev_jawaban` is unique by report and question. Store numbers in `numeric_value` and strings, radio selections or JSON arrays from multi-select in `text_value`. Store 12a Internal and 12b Eksternal as separate numeric rows under question 12.
- Successful save sets Draft and preserves the previous submitter. Successful submit sets Submitted, records the current user and refreshes the identity snapshot. Opening a report makes no write. A stale revision returns 409.

## Global Constraints

- Login and profile authorization apply to link access, draft and submit. A link never grants profile membership.
- One current answer set. Form, detail and export read that same set.
- Preserve existing historical rows and provenance during migration. Legacy public submissions may have no known submitter.
- Keep the existing block navigation and conditional validation.

## Review Focus

- Legacy public submission without an actor stays attributable only to its known channel.
- A newer revision draft takes precedence over the older submitted answers during import.
- Concurrent edits return a conflict without a partial write.
- A valid link used by an unrelated account returns 403.
- Multi-select labels and numeric zero survive reopening and CSV export.

### Task 1: Schema and historical backfill

**Files:** Add `database/migrations/2026_11_10_000001_create_monev_questionnaire_tables.php`, `app/Services/MonevAnswerCodec.php`, `app/Console/Commands/BackfillMonev.php` and `tests/Feature/MonevBackfillTest.php`.

**Interfaces:** `MonevAnswerCodec::encode(string $inputType, mixed $value): array` returns the two value columns. `monev:backfill --dry-run` previews conversion, and `monev:backfill` writes only after a clean preflight.

- [ ] Define the four tables and seed the current instrument with stable codes and existing choices. Split question 12 into `12a` and `12b`. Keep a legacy report reference for link and import reconciliation.
- [ ] Backfill the latest version for a revision draft and the official version for a submitted report. Retain the last official submitter and snapshot. Leave unknown submitters null. Reject unsupported values before writes. Re-running the command skips previously mapped reports without overwriting edits.
- [ ] Verify dry run writes zero rows, a repeated import creates zero duplicates, and a revision draft with a null legacy submitter keeps the newer answers. Run `vendor/bin/phpunit --filter MonevBackfillTest` on a test database.

### Task 2: Shared form and access

**Files:** Modify `routes/web.php`, `app/Http/Controllers/MonitoringAkreditasiController.php`, `app/Services/MonitoringReportService.php`, `app/Services/MonitoringReportLinkService.php` and `resources/views/lembaga/monitoring-akreditasi/form-content.blade.php`. Add `tests/Feature/MonevAccessTest.php`.

**Interfaces:** Keep existing form and link URLs. Writes take the current edit revision and return the new revision with status.

- [ ] Resolve link and account routes to the same report. Require login and check membership against the report profile on every request. Preserve link expiry and deletion checks.
- [ ] Render questions and choices from the pinned questionnaire. Read normalized answers back into the same form controls. With Ya, require 10–23, integer 10 greater than zero and `12a + 12b = 11`. With Tidak, skip 10–23 and require 24–26.
- [ ] Save answers, status and revision in one transaction. Only successful submit updates `submitted_by` and the identity snapshot.
- [ ] Verify unrelated accounts get 403, a second member of the same profile can edit an existing draft, stale edits return 409, and a submit followed by refresh restores selections and zero values. Run `vendor/bin/phpunit --filter MonevAccessTest`.

### Task 3: Detail, export and cutover

**Files:** Modify `app/Http/Controllers/MonitoringSekretariatController.php`, `app/Http/Controllers/MonitoringAkreditasiController.php`, `routes/web.php` and `resources/views/sekretariat/monitoring-akreditasi-detail.blade.php`. Add `tests/Feature/MonevExportTest.php`.

**Interfaces:** CSV export reads the same current answers as detail and includes report status.

- [ ] Authorize export by profile membership or secretariat role. Output stable questionnaire order, separate 12a/12b columns, and readable multi-select labels in one cell. Keep numeric zero and protect text cells from spreadsheet formula execution.
- [ ] Verify the CSV and detail reflect the current draft, preserve zero, and deny unrelated accounts. Run `vendor/bin/phpunit --filter MonevExportTest`.
- [ ] Reconcile historical report counts, links, import provenance and answer mappings. Switch reads and writes together after a final dry run and backfill with writes paused.
- [ ] Back up the legacy tables and verify the backup can be restored. Repoint link, link audit and import provenance foreign keys to `tr_monev` using the verified legacy ID mapping. Keep profile mapping and accreditation tables.
- [ ] After validating form reopening, submit, export and historical imports on the new schema, remove legacy code references and drop only `monitoring_report_versions` and `monitoring_reports` in a separate cleanup migration. Drop versions before reports. Verify the target database and obtain confirmation before running the cleanup migration.
