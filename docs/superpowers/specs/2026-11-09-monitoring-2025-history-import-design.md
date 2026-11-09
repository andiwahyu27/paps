# Monitoring PSTK 2025 Historical Import

**Status:** Draft for user approval  
**Date:** 2026-11-09

## Intent

Preserve the existing 2025 monitoring responses for institutions accredited in 2024 in the Gen2 monitoring database. The imported reports must appear as submitted reports in the same history and secretariat views as reports created through the new account or link forms. Gen2 profile, program, and accreditation records are canonical master data: use them to resolve each response, but do not insert duplicates or update them from the historical sheet.

The source is the Google Sheets response tab identified by spreadsheet `10J8RGoePVC4akQHxITcI34kS1lmipT6jOEyr21BjQjs`, gid `881485306`. Its CSV export has 29 columns, three populated responses, and one timestamped but otherwise blank row. The populated responses name an institution, program, certificate, predicate, and reporting year. Historical answers include values that do not match the new form's current options, and one no-training response has blank numeric and explanation fields.

## Goals

- Import valid historical rows into `monitoring_reports` and `monitoring_report_versions` as immutable submitted version 1 records.
- Preserve the source timestamp and raw monitoring responses in a restricted import provenance table; do not duplicate identity/contact/accreditation fields from the sheet into monitoring answers or provenance.
- Make reruns idempotent and prevent accidental overwrite or duplicate reports.
- Require deterministic institution, program, and accreditation matching; report unresolved or conflicting rows without partial writes.
- Display arrays and legacy free-text values safely on the secretariat report detail page.

## Non-goals

- Edit the source spreadsheet or alter current submitted answers to fit newer form options.
- Import other reporting years or other spreadsheet tabs in this change.
- Change the current form's validation rules or the dual-database architecture.
- Automatically guess an institution or accreditation when source identifiers disagree.

## Approaches

1. **Chosen: import into the existing report/version model.** Create one report and submitted version for each matched institution × program × year, plus a provenance record keyed to the source row. This makes historical and new reports share one history, detail page, and rekap model.
2. **Archive-only table.** Preserve spreadsheet rows in a separate historical archive and teach the dashboard to read two data models. This isolates legacy data but splits history, queries, and exports, contrary to the goal of retaining a single report history.

The user chose approach 1. The importer is an additive part of the Task 1 data foundation, tracked as **Task 1.5: Import Histori 2025**, and should run before Task 4 rekap and dry-test completion.

## Import behavior

### Command and execution modes

Add `php artisan monitoring:import-history --file=<csv> --source-ref=<sheet-id>:<gid> --dry-run` and the corresponding write mode without `--dry-run`.

- Require the source headers below in this order; handle UTF-8 BOM and standard CSV quoting. A changed/missing header is an error, not an invitation to shift columns.
- Treat a row as blank when source columns 2–29 are all empty (Timestamp alone is not a response); skip it with a count. Do not create a report for that row.
- A populated row with a missing/invalid timestamp is blocked. If the same source key appears more than once in the file, identical content is counted once; differing content blocks the whole write run.
- A row with a wrong cell count, invalid UTF-8, or malformed CSV structure blocks the entire run. Any blocked populated row prevents all writes.
- Pre-validate the complete file. If any populated row is unmatched, ambiguous, malformed, or conflicts with existing report history, write mode aborts before inserting anything.
- Dry run performs reads and validation only. It reports rows accepted, blank rows skipped, already-imported rows, and blocked rows with reasons; it prints no contact values.
- Write mode imports all accepted rows in one Gen2 transaction. Do not write to Gen1 or the spreadsheet.

### Source column mapping

| CSV column / header | Destination |
|---|---|
| 1 `Timestamp` | Import provenance and `submitted_at`; parse with the configured format and timezone |
| 2 `1. Nama Lembaga Pelatihan` | Match to `tb_profile_lembagas`; do not import into answers or update the master profile |
| 3 `2. Program Pelatihan yang Terakreditasi:` | Match to `mt_jenis_pengajuans`; do not import into answers |
| 4 `3. Alamat` | Do not import; use matched Gen2 master snapshot |
| 5 `4. Nomor Telepon` | Do not import; use matched Gen2 master snapshot |
| 6 `5. E-mail` | Do not import; use matched Gen2 master snapshot |
| 7 `6. Website` | Do not import; use matched Gen2 master snapshot |
| 8 `7. Nomor Sertifikat Akreditasi` | Match to `monitoring_accreditations`; do not import into answers or update the register |
| 9 `8. Status Akreditasi` | Cross-check against official predicate; use the master value |
| 10 `9. Tahun Pemantauan` | Use as `report_year`; do not duplicate it in answers |
| 11 `Apakah lembaga menyelenggarakan pelatihan setelah terakreditasi?` | Historical applicability indicator in raw payload; `Tidak` maps questions 10–12 totals to zero |
| 12 `10. Jumlah pelatihan yang dilaksanakan pada tahun ini` | `answers[10]`; blank with indicator `Tidak` maps to integer zero; source `-` means zero |
| 13 `11. Jumlah peserta pelatihan pada tahun ini` | `answers[11]`; blank with indicator `Tidak` maps to integer zero; source `-` means zero |
| 14 `12a. Jumlah peserta pelatihan yang berasal dari internal:` | `answers[12].internal`; blank with indicator `Tidak` maps to integer zero; source `-` means zero |
| 15 `12b. Jumlah peserta pelatihan yang berasal dari eksternal` | `answers[12].external`; blank with indicator `Tidak` maps to integer zero; source `-` means zero |
| 16 `13. Kesesuaian pelaksanaan dengan program yang terakreditasi` | `answers[13]`, preserve exact submitted value even if not a current option |
| 17 `14. Metode pelatihan yang digunakan` | `answers[14]`, split comma-separated values and trim each item |
| 18 `15. Penilaian kelulusan peserta pelatihan` | `answers[15]`, preserve exact submitted value |
| 19 `16. Penilaian penyelenggaraan pelatihan secara umum` | `answers[16]`, preserve exact submitted value |
| 20 `17. Tenaga pengajar yang digunakan` | `answers[17]`, split comma-separated values and trim each item |
| 21 `18. Jabatan tenaga pengajar:` | `answers[18]`, split comma-separated values and trim each item |
| 22 `19. Ketersediaan dokumen mutu pelatihan (SOP, panduan, silabus, RPP)` | `answers[19]` |
| 23 `20. Pemutakhiran dokumen akreditasi (kurikulum, silabus, data SDM, dsb)` | `answers[20]` |
| 24 `21. Kepatuhan terhadap persyaratan akreditasi (tenaga pengajar, sarana, kurikulum, dll)` | `answers[21]` |
| 25 `22. Evaluasi peserta terhadap pelatihan:` | `answers[22]` |
| 26 `23. Tindak lanjut hasil pelatihan (monitoring alumni, penerapan hasil belajar, dsb)` | `answers[23]`; source has no separate keterangan column |
| 27 `24. Kendala atau permasalahan dalam penyelenggaraan pelatihan` | `answers[24]`, preserve blank historical response |
| 28 `25. Rencana pelatihan tahun depan` | `answers[25]` |
| 29 `26. Kebutuhan dukungan atau pembinaan dari instansi pembina/BPS` | `answers[26]` |

### Matching

- Normalize names by trimming outer whitespace, collapsing internal Unicode whitespace to one ASCII space, and applying UTF-8 uppercase. Normalize certificate numbers by trimming, removing all whitespace, and applying UTF-8 uppercase; preserve punctuation. Resolve exactly one `MonitoringAccreditation` by normalized certificate number, then verify institution and program using exact normalized names or these explicit aliases from the 2025 instrument: `Badan Pengembangan Sumber Daya Manusia daerah Provinsi Jawa Tengah` → `BPSDMD Provinsi Jawa Tengah`; `Pusdiklat Tekfunghan BPSDM Han Kemhan` → `Pusdiklat Tekfunghan Badiklat Kemhan RI`; `Badan Pengembangan Sumber Daya Manusia Daerah Provinsi Kalimantan Selatan` → `BPSDMD Provinsi Kalimantan Selatan`; and `Program Pelatihan Teknis di Bidang Sistem Teknologi Berbasis Komputer` → `Sistem Teknologi Berbasis Komputer`. These are deterministic aliases, not fuzzy matching. Do not compare or update historical contacts from source columns 4–7. Spreadsheet values are matching evidence only; canonical master records are never updated by this import.
- Map predicate only from `A`/`B`/`C` or the exact labels `Terakreditasi A`/`Terakreditasi B`/`Terakreditasi C`, then compare with the matched `MonitoringAccreditation.predicate`. A mismatch blocks the row and the dry-run diagnostic displays both values for reconciliation. Keep the source predicate in the content hash; do not write it to the master register.
- Require exactly one matching accreditation. Do not fuzzy-match names, assume a program based on another row, or ignore a conflicting certificate.
- Enforce the existing unique report key `(profile_id, jenis_pengajuan_id, report_year)`. If a report exists with no versions and no submitted version, reuse it and preserve its link records; create its historical version and mark it submitted. If it has any report version or submitted version and is not already linked to this import source key, block it as a conflict. Never replace an existing answer or master record.

### Historical report and answer snapshots

- Create report status `submitted`, `submitted_version = 1`, and `edit_version = 2`. Create version 1 with the row timestamp as `submitted_at` interpreted in `Asia/Jakarta`, `submitted_by = null`, and channel `historical_import` (fits the existing 20-character channel column).
- Store only monitoring answers 10–26 in the version's `answers`; question 9 is `report_year`, and questions 1–8 are read from master snapshots. Split comma-separated values for historical multi-select questions 14, 17, and 18 into arrays for display, while retaining original monitoring responses in provenance. Preserve unsupported free text (including the anomalous response for question 13) unchanged; do not reject it using present-day form validation.
- For numeric questions 10–12, normalize a literal source `-` to integer zero; preserve the original dash in the raw payload. When the source says the institution did not conduct training, represent questions 10 and 11 as zero and question 12 internal/external as zero when blank to make report totals coherent. Keep the original `Tidak` and blank source cells in the raw payload. Leave unanswered historical narrative fields blank rather than inventing answers or backfilling current required-question rules.
- Populate `identity_snapshot` and `accreditation_snapshot` from matched Gen2 master records at import time so later master edits do not rewrite the report's identity. Do not copy old identity/contact/accreditation values from the spreadsheet into those snapshots.

### Provenance and idempotency

Add a migration for `monitoring_report_import_sources` with a concise named unique index on `source_key`, a report foreign key, source reference, source row number, content hash, raw JSON payload limited to the source timestamp and monitoring response columns 11–29, and import timestamp. Do not store duplicate profile/contact/certificate/predicate values in this table. Keep the monitoring response payload server-side and do not expose it in public pages, logs, or ordinary exports.

Compute `source_key` as SHA-256 of UTF-8 JSON encoding (unescaped Unicode/slashes, fixed array order) of `(trimmed source_ref, trimmed raw timestamp, normalized certificate number, normalized institution name)`. Compute `content_hash` from the same canonical JSON settings over all 29 header/value pairs in source order, preserving each cell's raw text in memory; persist only the source reference, source row, timestamp, and monitoring response columns 11–29. If a key already exists with the same content hash and the same report, report it as already imported even if the report has since gained later versions; do not modify it. If its content hash or report differs, block the run as a source conflict. A report/version created outside this importer is never treated as an idempotent match solely because its report key is the same. Identical duplicate rows within one CSV count as one candidate plus one duplicate-skipped row; same key with different content blocks the run.

## Presentation

Update the secretariat detail renderer to format arrays as joined option labels and scalars as text. Render questions 1–8 from the version's Gen2 master snapshots and question 9 from `report_year`; render questions 10–26 from imported answers. Keep unknown historical values visible as supplied. Do not expose the provenance JSON in normal report details.

## Acceptance checks

- Dry run counts the three populated sheet rows as candidate imports and skips the blank response row, without writes.
- Each populated response maps to exactly one existing official accreditation and creates one submitted version for year 2025, reusing a matching report record only when it has no versions yet.
- The no-training record remains submitted with zero participant totals while its original blanks remain recoverable in the raw source payload.
- Values outside current form options remain intact and visible in secretariat detail.
- Repeating the same import reports all rows as already imported and changes no report/version values.
- A changed source row, duplicate source key with different content, ambiguous accreditation, malformed CSV row, or existing conflicting report blocks the entire write run.
- The report detail renders string and array answers without conversion warnings.

## Timestamp parsing

Use the spreadsheet's `Timestamp` as the submitted time, parsed by PHP `DateTime` format with default `d/m/Y G:i:s` in `Asia/Jakarta`, while retaining the unparsed timestamp. Provide `--timestamp-format=<php-format>` and `--source-timezone=<IANA-zone>` overrides; an invalid timestamp blocks that populated row. Do not guess a locale based on an ambiguous date.
