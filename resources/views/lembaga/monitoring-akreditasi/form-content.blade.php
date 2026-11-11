@php
    $public = $public ?? false;
    $blocks = [
        [
            'Panduan Pengisian Laporan Monev',
            [],
            'Isi satu laporan untuk setiap program terakreditasi dan tahun pemantauan. Laporkan kegiatan pada periode tahun yang tercantum; periksa data awal dan koreksi kontak bila perlu. Jika tidak ada pelatihan, tetap kirim laporan dengan jumlah pelatihan dan peserta nol serta jelaskan alasan nihil. Draf tersimpan saat Anda berpindah blok atau memilih Simpan Draf. Periksa ringkasan dan pernyataan kebenaran sebelum mengirim. Laporan yang sudah dikirim dapat diperbaiki dan dikirim ulang. ' .
            ($public
                ? 'Link ini dapat digunakan sampai ' .
                    $link->expires_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') .
                    ' WIB.'
                : 'Pelaporan melalui akun lembaga tetap tersedia tanpa bergantung pada masa berlaku link sekretariat.'),
        ],
        [
            'A. Identitas Lembaga',
            [
                1 => 'Nama lembaga pelatihan',
                2 => 'Program pelatihan yang terakreditasi',
                3 => 'Alamat',
                4 => 'Nomor telepon',
                5 => 'E-mail',
                6 => 'Website',
                7 => 'Nomor sertifikat akreditasi',
                8 => 'Status akreditasi',
                9 => 'Tahun pemantauan',
                'has_training' => 'Apakah lembaga menyelenggarakan pelatihan setelah terakreditasi?',
            ],
            'Periksa identitas, kontak, program, sertifikat, dan tahun pemantauan. Identitas resmi berasal dari profil serta register akreditasi.',
        ],
        [
            'B. Pelaksanaan Pelatihan Setelah Terakreditasi',
            [
                10 => 'Jumlah pelatihan yang dilaksanakan pada tahun ini',
                11 => 'Jumlah peserta pelatihan pada tahun ini',
                12 => 'Asal peserta pelatihan',
                13 => 'Kesesuaian pelaksanaan dengan program yang terakreditasi',
                14 => 'Metode pelatihan yang digunakan',
                15 => 'Penilaian kelulusan peserta pelatihan',
                16 => 'Penilaian penyelenggaraan pelatihan secara umum',
            ],
                    'Jawab pertanyaan penyaring terlebih dahulu. Jika jawabannya Ya, lengkapi pertanyaan 10–23. Jika jawabannya Tidak, lanjutkan ke blok D dan lengkapi pertanyaan 24–26.',
        ],
        [
            'C. Sumber Daya Pelatihan',
            [
                17 => 'Tenaga pengajar yang digunakan',
                18 => 'Jabatan tenaga pengajar',
                19 => 'Ketersediaan dokumen mutu pelatihan (SOP, panduan, silabus, RPP)',
                20 => 'Pemutakhiran dokumen akreditasi (kurikulum, silabus, data SDM, dan lainnya)',
                21 => 'Kepatuhan terhadap persyaratan akreditasi (tenaga pengajar, sarana, kurikulum, dan lainnya)',
            ],
            'Jelaskan sumber daya dan bukti dukung yang digunakan lembaga selama periode pemantauan.',
        ],
        [
            'D. Evaluasi dan Tindak Lanjut',
            [
                22 => 'Evaluasi peserta terhadap pelatihan',
                23 => 'Tindak lanjut hasil pelatihan (monitoring alumni, penerapan hasil belajar, dan lainnya)',
                24 => 'Kendala atau permasalahan dalam penyelenggaraan pelatihan',
                25 => 'Rencana pelatihan tahun depan',
                26 => 'Kebutuhan dukungan atau pembinaan dari instansi pembina/BPS',
            ],
            'Lengkapi evaluasi, kendala, rencana, dan dukungan yang dibutuhkan. Tinjau ringkasan seluruh blok sebelum mengirim laporan.',
        ],
    ];
    $questionnaireQuestions = $questionnaireQuestions ?? collect();
    foreach ($blocks as &$block) {
        foreach ($block[1] as $number => $label) {
            $block[1][$number] = $questionnaireQuestions->get($number)?->pertanyaan ?? $label;
        }
        uksort($block[1], static function ($first, $second) use ($questionnaireQuestions) {
            $firstCode = $first === 12 ? '12a' : $first;
            $secondCode = $second === 12 ? '12a' : $second;
            return ($questionnaireQuestions->get($firstCode)?->order_number ?? PHP_INT_MAX)
                <=> ($questionnaireQuestions->get($secondCode)?->order_number ?? PHP_INT_MAX);
        });
    }
    unset($block);
    $blocks[] = [
        'Tinjau & Kirim',
        [],
        'Periksa ringkasan jawaban seluruh blok, centang pernyataan kebenaran, lalu kirim laporan.',
    ];
    $formAction = $public
        ? route('monitoring-akreditasi.public.draft', $token)
        : ($report
            ? route('monitoring-akreditasi.update', $report)
            : route('monitoring-akreditasi.store'));
@endphp

<div class="container-xxl flex-grow-1 container-p-y" id="monitoring-form">
    <div class="modal fade" id="submit-confirm-modal" tabindex="-1" aria-labelledby="submit-confirm-title"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submit-confirm-title">Kirim Laporan Monitoring</h5><button
                        type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Kirim laporan monitoring ini? Setelah dikirim, laporan tersimpan sebagai versi
                        resmi.</p>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary"
                        id="confirm-submit-report">Kirim Laporan</button></div>
            </div>
        </div>
    </div>
    <div id="monitoring-validation-toast"
        class="bs-toast toast bg-danger text-white position-fixed top-0 start-50 translate-middle-x m-3" role="alert"
        aria-live="assertive" aria-atomic="true" data-bs-autohide="true" style="z-index:1090;">
        <div class="d-flex">
            <div class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
    <h4 class="fw-bold py-3">Monitoring Akreditasi PSTK / Form Laporan</h4>
    <div class="card mb-4">
        <div class="card-body row g-3">
            <div class="col-md-6"><strong>Lembaga</strong>
                <div>{{ $profile->nama_lembaga }}</div>
            </div>
            <div class="col-md-3"><strong>Program</strong>
                <div id="header-program">{{ $report?->jenisPengajuan?->nama ?? 'Pilih program pada form' }}</div>
            </div>
            <div class="col-md-3"><strong>Tahun</strong>
                <div>{{ $year }}</div>
            </div>
            <div class="col-md-3"><strong>Sertifikat / predikat</strong>
                <div id="header-accreditation">{{ $accreditation?->certificate_number ?? 'Belum tercatat' }} /
                    {{ $accreditation?->predicate ?? '—' }}</div>
            </div>
            @if ($report)
                <div class="col-md-3"><strong>Status</strong>
                    <div>
                        {{ $report->status === 'revision_draft' ? 'Perbaikan belum dikirim' : ($report->status === 'submitted' ? 'Terkirim' : 'Draf') }}
                    </div>
                </div>
                <div class="col-md-3"><strong>Disimpan terakhir</strong>
                    <div>{{ $report->updated_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</div>
                </div>
            @endif
            @if ($public)
                <div class="col-12 text-muted">Link berlaku sampai
                    {{ $link->expires_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB.</div>
            @endif
        </div>
    </div>

    @if ($public)
        <form id="monitoring-report-form" method="POST"
            action="{{ route('monitoring-akreditasi.public.draft', $token) }}">
            @csrf<input type="hidden" name="jenis_pengajuan_id" value="{{ $report->jenis_pengajuan_id }}"><input
                type="hidden" name="report_year" value="{{ $year }}"><input type="hidden" name="edit_version"
                value="{{ $report->edit_version }}">
        @else
            <form id="monitoring-report-form" method="POST" action="{{ $formAction }}">
                @csrf
                @if ($report)
                    @method('PUT')<input type="hidden" name="edit_version" value="{{ $report->edit_version }}">
                @endif
                <input type="hidden" name="report_year" value="{{ $year }}">
                @if ($report)
                    <input type="hidden" name="jenis_pengajuan_id" value="{{ $report->jenis_pengajuan_id }}">
                @else
                    <div class="card mb-4">
                        <div class="card-body"><label class="form-label" for="program-select">Program
                                terakreditasi</label>
                            <select id="program-select" name="jenis_pengajuan_id" class="form-select" required>
                                <option value="">Pilih program</option>
                                @foreach ($accreditations as $item)
                                    <option value="{{ $item->jenis_pengajuan_id }}"
                                        data-program="{{ $item->jenisPengajuan->nama ?? '' }}"
                                        data-certificate="{{ $item->certificate_number }}"
                                        data-predicate="{{ $item->predicate }}">
                                        {{ $item->jenisPengajuan->nama ?? '-' }} —
                                        {{ $item->predicate ?: 'Predikat belum ditetapkan' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
    @endif

    <div class="row g-4">
        <aside class="col-lg-3">
            <div class="card">
                <div class="card-body"><small class="fw-bold text-uppercase">Daftar Blok</small>
                    <div class="nav flex-column nav-pills mt-3" id="block-nav">
                        @foreach ($blocks as $i => $block)
                            <button type="button" class="nav-link text-start mb-2 {{ $i === 0 ? 'active' : '' }}"
                                data-block="{{ $i }}">{{ $i === 0 ? 'Panduan' : $block[0] }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>
        <section class="col-lg-9">
            <div class="card mb-3">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between"><strong>Progres pengisian</strong><strong
                            id="progress-label">0 dari 0 terjawab</strong></div>
                    <div class="progress mt-2" style="height:8px">
                        <div id="progress-bar" class="progress-bar" role="progressbar" style="width:0%"
                            aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
                    </div>
                </div>
                <div class="card-header"><span class="text-primary fw-bold" id="block-index">Panduan</span>
                    <h4 id="block-title" class="mt-2">{{ $blocks[0][0] }}</h4>
                    <p class="mb-0" id="block-description">{{ $blocks[0][2] }}</p>
                </div>
                <div class="card-body" id="question-area">
                    @foreach ($blocks as $i => $block)
                        <div class="monitoring-block {{ $i === 0 ? '' : 'd-none' }}"
                            data-block-content="{{ $i }}">
                            @if ($i === 0)
                                <p>{{ $block[2] }}</p>
                            @elseif($i === count($blocks) - 1)
                                <div id="review-panel" aria-live="polite">
                                    <div id="review-summary" class="row g-3"></div>
                                    <div class="form-check mt-3"><input class="form-check-input" type="checkbox"
                                            id="confirm-accuracy"><label class="form-check-label"
                                            for="confirm-accuracy">Saya menyatakan informasi dalam laporan ini benar
                                            dan dapat dipertanggungjawabkan.</label></div>
                                </div>
                            @else<div class="row g-3">
                                    @foreach ($block[1] as $number => $label)
                                        <div class="col-md-{{ in_array($number, [13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26]) ? '12' : '6' }}"
                                            data-question-container="{{ $number }}">
                                            <label class="form-label"
                                                for="answer-{{ $number }}">@if ($number !== 'has_training'){{ $number }}. @endif
                                                {{ $label }} @if ($number !== 6)
                                                    <span class="text-danger" aria-hidden="true">*</span>
                                                @endif
                                            </label>
                                            @if ($number === 'has_training')
                                                <select class="form-select answer-field" id="answer-has-training"
                                                    name="answers[has_training]">
                                                    <option value="">Pilih jawaban</option>
                                                    <option value="Ya" @selected(old('answers.has_training', $answers['has_training'] ?? '') === 'Ya')>Ya</option>
                                                    <option value="Tidak" @selected(old('answers.has_training', $answers['has_training'] ?? '') === 'Tidak')>Tidak</option>
                                                </select>
                                                <div class="form-text">Jika Ya, pertanyaan 10–23 wajib diisi. Jika Tidak, lanjut ke blok D.</div>
                                            @elseif ($number === 12)
                                                <div class="row g-2">
                                                    <div class="col-sm-6"><label class="form-label small"
                                                            for="answer-12-internal">Internal</label><input
                                                            type="number" min="0" step="1"
                                                            class="form-control answer-field" id="answer-12-internal"
                                                            name="answers[12][internal]"
                                                            value="{{ old('answers.12.internal', data_get($answers, '12.internal', '')) }}"
                                                            inputmode="numeric"></div>
                                                    <div class="col-sm-6"><label class="form-label small"
                                                            for="answer-12-external">Eksternal</label><input
                                                            type="number" min="0" step="1"
                                                            class="form-control answer-field" id="answer-12-external"
                                                            name="answers[12][external]"
                                                            value="{{ old('answers.12.external', data_get($answers, '12.external', '')) }}"
                                                            inputmode="numeric"></div>
                                                </div>
                                                <div class="form-text" id="participant-total-help">Jumlah peserta
                                                    internal dan eksternal jika dijumlahkan harus sama dengan jumlah
                                                    peserta pada pertanyaan nomor 11.</div>
                                            @elseif(in_array($number, [24, 25, 26]))
                                                <textarea class="form-control answer-field" id="answer-{{ $number }}" name="answers[{{ $number }}]"
                                                    rows="4"
                                                    @if ($number === 24) placeholder="Jika tidak ada pelatihan, jelaskan alasan nihil. Jika ada, uraikan kendala atau permasalahan." @endif>{{ old('answers.' . $number, $answers[(string) $number] ?? '') }}</textarea>
                                            @elseif($number === 23)
                                                <select class="form-select answer-field" id="answer-23"
                                                    name="answers[23]">
                                                    <option value="">Pilih jawaban</option>
                                                    @foreach ($questionnaireQuestions->get(23)?->opsi ?? ['Ada', 'Tidak ada'] as $choice)
                                                        <option value="{{ $choice }}" @selected(old('answers.23', $answers['23'] ?? '') === $choice)>{{ ['Ada' => 'Ada tindak lanjut', 'Tidak ada' => 'Tidak ada tindak lanjut'][$choice] ?? $choice }}</option>
                                                    @endforeach
                                                </select>
                                                <textarea
                                                    class="form-control answer-field mt-2 {{ old('answers.23', $answers['23'] ?? '') === 'Ada' ? '' : 'd-none' }}"
                                                    id="answer-23-detail" name="answers[23_keterangan]" rows="3"
                                                    placeholder="Jelaskan tindak lanjut yang dilakukan">{{ old('answers.23_keterangan', $answers['23_keterangan'] ?? '') }}</textarea>
                                            @elseif(in_array($number, [14, 17, 18]))
                                                @php
                                                    $choices = $questionnaireQuestions->get($number)?->opsi ?? [
                                                        14 => ['Tatap muka', 'Daring', 'Blended learning'],
                                                        17 => ['Internal', 'Eksternal'],
                                                        18 => [
                                                            'Widyaiswara',
                                                            'Pejabat Struktural',
                                                            'Fungsional Lainnya',
                                                        ],
                                                    ][$number];
                                                    $selectedChoices = (array) old(
                                                        'answers.' . $number,
                                                        $answers[(string) $number] ?? [],
                                                    );
                                                @endphp
                                                <div id="answer-{{ $number }}" role="group"
                                                    aria-label="{{ $label }}">
                                                    @foreach ($choices as $choice)
                                                        <div class="form-check form-check-inline"><input
                                                                class="form-check-input answer-field" type="checkbox"
                                                                id="answer-{{ $number }}-{{ $loop->index }}"
                                                                name="answers[{{ $number }}][]"
                                                                value="{{ $choice }}"
                                                                {{ in_array($choice, $selectedChoices, true) ? 'checked' : '' }}><label
                                                                class="form-check-label"
                                                                for="answer-{{ $number }}-{{ $loop->index }}">{{ $choice }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @elseif(in_array($number, [13, 15, 16, 19, 20, 21, 22]))
                                                @php
                                                    $choices = $questionnaireQuestions->get($number)?->opsi ?? [
                                                        13 => ['Sesuai', 'Ada modifikasi minor', 'Tidak sesuai'],
                                                        15 => [
                                                            'Sangat memuaskan',
                                                            'Memuaskan',
                                                            'Cukup memuaskan',
                                                            'Tidak lulus',
                                                        ],
                                                        16 => ['Sangat baik', 'Baik', 'Kurang baik', 'Tidak baik'],
                                                        19 => ['Lengkap', 'Sebagian', 'Tidak ada'],
                                                        20 => ['Sudah dilakukan', 'Belum dilakukan'],
                                                        21 => ['Terpenuhi', 'Sebagian', 'Tidak terpenuhi'],
                                                        22 => ['Sangat puas', 'Puas', 'Cukup', 'Kurang'],
                                                    ][$number];
                                                @endphp
                                                <select class="form-select answer-field"
                                                    id="answer-{{ $number }}"
                                                    name="answers[{{ $number }}]">
                                                    <option value="">Pilih jawaban</option>
                                                    @foreach ($choices as $choice)
                                                        <option value="{{ $choice }}"
                                                            @selected(old('answers.' . $number, $answers[(string) $number] ?? '') === $choice)>{{ $choice }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input class="form-control answer-field"
                                                    id="answer-{{ $number }}"
                                                    name="answers[{{ $number }}]"
                                                    value="{{ old('answers.' . $number, $answers[(string) $number] ?? '') }}"
                                                    @if (in_array($number, [1, 2, 7, 8, 9])) readonly @endif
                                                    @if (in_array($number, [10, 11])) type="number" min="0" step="1" inputmode="numeric" @endif>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center"><button
                        class="btn btn-outline-secondary" type="button" id="previous-block">Sebelumnya</button><span
                        id="save-status" class="small text-muted" role="status"><i
                            class="bx bx-info-circle me-1"></i>Siap menyimpan draf.</span>
                    <div><button class="btn btn-secondary" type="button" id="save-draft">Simpan Draf</button>
                        <button class="btn btn-primary" type="button" id="next-block">Selanjutnya →</button>
                        @if ($public || $report)
                            <button class="btn btn-success d-none" type="submit" id="submit-report"
                                formaction="{{ $public ? route('monitoring-akreditasi.public.submit', $token) : route('monitoring-akreditasi.submit', $report) }}"
                                formmethod="POST">Tinjau & Kirim</button>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
    </form>
</div>

<script>
    (() => {
        const form = document.getElementById('monitoring-report-form');
        const buttons = [...document.querySelectorAll('[data-block]')];
        const panels = [...document.querySelectorAll('[data-block-content]')];
        const title = document.getElementById('block-title');
        const index = document.getElementById('block-index');
        const description = document.getElementById('block-description');
        const save = document.getElementById('save-status');
        const progressLabel = document.getElementById('progress-label');
        const progressBar = document.getElementById('progress-bar');
        const reviewPanel = document.getElementById('review-panel');
        const reviewSummary = document.getElementById('review-summary');
        const descriptions = @json(array_column($blocks, 2));
        let active = 0;
        let saving = false;
        let dirty = false;
        const invalidQuestions = new Set();

        function setSaveStatus(message, type = 'info') {
            const styles = {
                info: 'text-muted',
                saving: 'text-primary',
                success: 'text-success',
                error: 'text-danger'
            };
            save.className = `small ${styles[type]||styles.info}`;
            save.innerHTML =
                `<i class="bx ${type==='error'?'bx-error-circle':type==='success'?'bx-check-circle':type==='saving'?'bx-loader-circle':'bx-info-circle'} me-1"></i>${message}`;
        }

        function hasTraining() {
            return form.querySelector('[name="answers[has_training]"]')?.value === 'Ya';
        }

        function updateBranch() {
            const training = hasTraining();
            document.querySelectorAll('[data-question-container]').forEach(container => {
                const question = container.dataset.questionContainer;
                container.classList.toggle('d-none', !training && /^([1-9]|1[0-9]|2[0-3])$/.test(question) && Number(question) >= 10);
            });
            buttons.forEach((button, n) => button.classList.toggle('d-none', !training && [2, 3].includes(n)));
            if (!training && [2, 3].includes(active)) show(4);
        }

        function updateProgress() {
            const totalFields = [...form.querySelectorAll('.answer-field')];
            const noTraining = !hasTraining();
            const excluded = noTraining ? new Set(['10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23']) : new Set();
            const questions = [...new Set(totalFields.map(field => field.name.match(/answers\[(\d+)\]/)?.[1])
                .filter(Boolean))].filter(number => number !== '6' && !excluded.has(number));
            if (form.querySelector('[name="answers[has_training]"]')) questions.unshift('has_training');
            const answered = questions.filter(number => {
                if (number === 'has_training') return form.querySelector('[name="answers[has_training]"]')?.value !== '';
                const fields = totalFields.filter(field => field.name.startsWith(`answers[${number}]`));
                if (fields.some(field => field.type === 'checkbox')) return fields.some(field => field
                    .type === 'checkbox' && field.checked);
                return fields.length > 0 && fields.every(field => String(field.value).trim() !== '');
            }).length;
            const percent = questions.length ? Math.round(answered * 100 / questions.length) : 0;
            progressLabel.textContent = `${answered} dari ${questions.length} pertanyaan terjawab`;
            progressBar.style.width = `${percent}%`;
            progressBar.setAttribute('aria-valuenow', String(percent));
            updateBranch();
        }

        function validateParticipantTotal() {
            const total = form.querySelector('[name="answers[11]"]');
            const internal = form.querySelector('[name="answers[12][internal]"]');
            const external = form.querySelector('[name="answers[12][external]"]');
            const help = document.getElementById('participant-total-help');
            if (!total || !internal || !external) return true;
            const complete = [total, internal, external].every(field => String(field.value).trim() !== '');
            const valid = !complete || Number(internal.value) + Number(external.value) === Number(total.value);
            [total, internal, external].forEach(field => field.setCustomValidity(valid ? '' :
                'Jumlah peserta internal dan eksternal jika dijumlahkan harus sama dengan jumlah peserta pada pertanyaan nomor 11.'
            ));
            help?.classList.toggle('text-danger', !valid);
            return valid;
        }

        function showValidationToast(message, type = 'error') {
            const toast = document.getElementById('monitoring-validation-toast');
            if (!toast) return;
            toast.classList.toggle('bg-danger', type === 'error');
            toast.classList.toggle('bg-success', type === 'success');
            toast.querySelector('.toast-body').textContent = message;
            const instance = bootstrap.Toast.getOrCreateInstance(toast, {
                autohide: true,
                delay: 2000
            });
            instance.show();
        }

        function requiredQuestionNumbers() {
            const required = new Set(['has_training', 1, 2, 3, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26]);
            if (!hasTraining()) [10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23].forEach(number => required.delete(number));
            return required;
        }

        function questionIsAnswered(number) {
            if (number === 'has_training') return form.querySelector('[name="answers[has_training]"]')?.value !== '';
            const fields = [...form.querySelectorAll(`[name^="answers[${number}]"]`)];
            if (number === 12) return fields.length === 2 && fields.every(field => String(field.value).trim() !==
                '');
            if (fields.some(field => field.type === 'checkbox')) return fields.some(field => field.checked);
            return fields.length > 0 && fields.every(field => String(field.value).trim() !== '');
        }

        function markInvalidQuestions(numbers) {
            numbers.forEach(number => invalidQuestions.add(String(number)));
            document.querySelectorAll('[data-question-container]').forEach(container => {
                const invalid = invalidQuestions.has(String(container.dataset.questionContainer));
                container.classList.toggle('border', invalid);
                container.classList.toggle('border-danger', invalid);
                container.classList.toggle('rounded', invalid);
                container.classList.toggle('p-2', invalid);
                container.querySelectorAll('.answer-field').forEach(field => field.classList.toggle(
                    'is-invalid', invalid));
            });
        }

        function clearInvalidQuestion(number) {
            invalidQuestions.delete(String(number));
            markInvalidQuestions([]);
        }

        function missingRequiredQuestions() {
            return [...requiredQuestionNumbers()].filter(number => !questionIsAnswered(number));
        }

        function show(i) {
            active = Math.max(0, Math.min(buttons.length - 1, i));
            buttons.forEach((button, n) => button.classList.toggle('active', n === active));
            panels.forEach((panel, n) => panel.classList.toggle('d-none', n !== active));
            title.textContent = active === 0 ? 'Panduan Pengisian Laporan Monev' : buttons[active].textContent
                .trim();
            index.textContent = active === 0 ? 'Panduan' : `Blok ${active} dari 4`;
            description.textContent = descriptions[active] || '';
            document.getElementById('previous-block').disabled = active === 0;
            document.getElementById('next-block').classList.toggle('d-none', active === buttons.length - 1);
            document.getElementById('submit-report')?.classList.toggle('d-none', active !== buttons.length - 1);
            reviewPanel.classList.toggle('d-none', active !== buttons.length - 1);
            if (active === buttons.length - 1) renderReview();
        }

        function renderReview() {
            reviewSummary.replaceChildren();
            const required = requiredQuestionNumbers();
            for (let block = 1; block < buttons.length - 1; block++) {
                const column = document.createElement('div');
                column.className = 'col-md-6';
                const card = document.createElement('div');
                if (!hasTraining() && [2, 3].includes(block)) return;
                card.className = 'border rounded p-3 h-100';
                const heading = document.createElement('h6');
                heading.textContent = buttons[block].textContent.trim();
                card.appendChild(heading);
                const list = document.createElement('dl');
                list.className = 'mb-0 small';
                const summarized = new Set();
                panels[block].querySelectorAll('.answer-field').forEach(field => {
                    const match = field.name.match(/^answers\[(\d+)\](?:\[(\w*)\])?$/);
                    if (!match || match[2] === 'external') return;
                    if (summarized.has(match[1])) return;
                    if (!hasTraining() && Number(match[1]) >= 10 && Number(match[1]) <= 23) return;
                    summarized.add(match[1]);
                    const questionNumber = Number(match[1]);
                    const missing = required.has(questionNumber) && !questionIsAnswered(questionNumber);
                    const term = document.createElement('dt');
                    term.className = missing ? 'text-danger' : '';
                    term.textContent =
                        `Pertanyaan ${match[1]}${match[2]==='internal'?' (internal/eksternal)':''}${required.has(questionNumber)?' * (wajib)':''}`;
                    const detail = document.createElement('dd');
                    detail.className = `mb-2 ${missing?'text-danger fw-semibold':''}`;
                    if (field.type === 'checkbox') detail.textContent = [...panels[block].querySelectorAll(
                            `[name="${field.name}"]:checked`)].map(option => option.value).join(', ') ||
                        'Belum dipilih';
                    else detail.textContent = match[2] === 'internal' ?
                        `Internal: ${field.value||'—'}; Eksternal: ${form.querySelector('[name="answers[12][external]"]').value||'—'}` :
                        (field.value.trim() || 'Belum diisi');
                    list.append(term, detail);
                });
                const followUp = panels[block].querySelector('[name="answers[23_keterangan]"]');
                if (followUp) {
                    const term = document.createElement('dt');
                    term.textContent = 'Keterangan tindak lanjut';
                    const detail = document.createElement('dd');
                    detail.className = 'mb-2';
                    detail.textContent = followUp.value.trim() || 'Belum diisi';
                    list.append(term, detail);
                }
                card.appendChild(list);
                column.appendChild(card);
                reviewSummary.appendChild(column);
            }
        }

        function focusValidationError(errors) {
            const key = Object.keys(errors || {})[0];
            const match = key?.match(/answers\.(\d+)/);
            if (!match) return false;
            const question = Number(match[1]);
            const block = question <= 9 ? 1 : question <= 16 ? 2 : question <= 21 ? 3 : 4;
            show(block);
            form.querySelector(`[name="answers[${question}]"], [name^="answers[${question}]["]`)?.focus();
            setSaveStatus(errors[key][0] || 'Periksa jawaban pada blok ini.', 'error');
            return true;
        }

        async function saveDraft() {
            if (saving) return false;
            // Draf boleh disimpan sebagian; validasi lengkap dilakukan saat kirim.
            saving = true;
            setSaveStatus('Menyimpan draf…', 'saving');
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                });
                if (!response.ok) {
                    const messages = {
                        401: 'Sesi login tidak berlaku. Silakan login kembali.',
                        419: 'Sesi form berakhir. Muat ulang halaman.',
                        429: 'Terlalu banyak penyimpanan dalam waktu singkat. Silakan tunggu 1 menit sebelum mencoba lagi.',
                        500: 'Server gagal menyimpan draf. Periksa log aplikasi.'
                    };
                    throw new Error(response.status === 409 ?
                        'Laporan berubah di sesi lain. Salin jawaban lalu muat ulang.' : (messages[response
                            .status] || 'Gagal menyimpan draf. Periksa koneksi lalu coba lagi.'));
                }
                const data = await response.json();
                const version = form.querySelector('[name=edit_version]');
                if (version) version.value = data.edit_version;
                if (data.edit_url) {
                    dirty = false;
                    window.location.href = data.edit_url;
                    return false;
                }
                dirty = false;
                setSaveStatus('Draf tersimpan.', 'success');
                return true;
            } catch (error) {
                setSaveStatus(error.message, 'error');
                return false;
            } finally {
                saving = false;
            }
        }

        document.getElementById('program-select')?.addEventListener('change', event => {
            const option = event.target.selectedOptions[0];
            const values = {
                '1': @json($profile->nama_lembaga),
                '2': option.dataset.program,
                '3': @json($profile->alamat_lembaga),
                '4': @json($profile->telepon),
                '5': @json($profile->email),
                '6': @json($profile->website),
                '7': option.dataset.certificate,
                '8': option.dataset.predicate,
                '9': @json($year)
            };
            document.getElementById('header-program').textContent = option.dataset.program ||
                'Pilih program pada form';
            document.getElementById('header-accreditation').textContent =
                `${option.dataset.certificate||'Belum tercatat'} / ${option.dataset.predicate||'—'}`;
            Object.entries(values).forEach(([number, value]) => {
                const field = form.querySelector(`[name="answers[${number}]"]`);
                if (field) field.value = value || '';
            });
            updateProgress();
        });
        form.querySelectorAll('.answer-field').forEach(field => field.addEventListener('input', () => {
            dirty = true;
            const match = field.name.match(/answers\[(\d+)/);
            if (match) clearInvalidQuestion(match[1]);
            updateProgress();
            validateParticipantTotal();
        }));
        form.querySelectorAll('.answer-field').forEach(field => field.addEventListener('change', () => {
            dirty = true;
            const match = field.name.match(/answers\[(\d+)/);
            if (match) clearInvalidQuestion(match[1]);
            updateProgress();
            validateParticipantTotal();
        }));
        document.getElementById('program-select')?.addEventListener('change', () => {
            dirty = true;
        });
        form.querySelector('[name="answers[10]"]')?.addEventListener('change', updateProgress);
        form.querySelector('[name="answers[23]"]')?.addEventListener('change', event => document.getElementById(
            'answer-23-detail').classList.toggle('d-none', event.target.value !== 'Ada'));
        buttons.forEach((button, n) => button.addEventListener('click', async () => {
            if (n === 0) {
                show(n);
                return;
            }
            if (await saveDraft()) show(n);
        }));
        document.getElementById('previous-block').onclick = async () => {
            if (active === 0) return;
            const previous = !hasTraining() && active === 4 ? 1 : active - 1;
            if (await saveDraft()) show(previous);
        };
        document.getElementById('next-block').onclick = async () => {
            const next = !hasTraining() && active === 1 ? 4 : active + 1;
            if (await saveDraft()) show(next);
        };
        document.getElementById('save-draft').onclick = saveDraft;
        form.querySelector('[name="answers[has_training]"]')?.addEventListener('change', () => {
            dirty = true;
            updateProgress();
        });
        async function submitReport() {
            const button = document.getElementById('submit-report');
            button.disabled = true;
            try {
                const submitData = new FormData(form);
                submitData.delete('_method');
                const response = await fetch(button.getAttribute('formaction'), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
                        'Accept': 'application/json'
                    },
                    body: submitData
                });
                if (!response.ok) {
                    let payload = {};
                    try {
                        payload = await response.json();
                    } catch (_) {}
                    if (response.status === 422) {
                        const messages = Object.values(payload.errors || {}).flat().filter(Boolean);
                        renderReview();
                        markInvalidQuestions(Object.keys(payload.errors || {}).map(key => key.match(
                            /answers\.(\d+)/)?.[1] || (key === 'answers.has_training' ? 'has_training' : null)).filter(Boolean));
                        showValidationToast(messages.join(' ') ||
                            'Data belum lengkap. Periksa pertanyaan wajib pada ringkasan.');
                        setSaveStatus(messages.join(' ') || 'Data belum lengkap.', 'error');
                        button.disabled = false;
                        return;
                    }
                    throw new Error(response.status === 409 ?
                        'Laporan berubah di sesi lain. Salin jawaban lalu muat ulang.' : response.status === 429 ?
                        'Terlalu banyak percobaan. Silakan tunggu 1 menit sebelum mencoba lagi.' : (payload.message ||
                            'Pengiriman gagal. Periksa jawaban dan koneksi.'));
                }
                const payload = await response.json();
                const version = form.querySelector('[name="edit_version"]');
                if (version && payload.edit_version) version.value = payload.edit_version;
                dirty = false;
                button.disabled = false;
                showValidationToast(payload.message || 'Laporan berhasil dikirim.', 'success');
                setSaveStatus('Laporan berhasil dikirim. Anda tetap berada di halaman Tinjau & Kirim.', 'success');
            } catch (error) {
                button.disabled = false;
                setSaveStatus(error.message, 'error');
            }
        }

        form.addEventListener('submit', async event => {
            event.preventDefault();
            const missing = missingRequiredQuestions();
            if (missing.length) {
                renderReview();
                markInvalidQuestions(missing);
                const labels = missing.map(number => number === 'has_training' ? 'pertanyaan penyaring pelatihan' : number);
                showValidationToast(`Lengkapi pertanyaan wajib: ${labels.join(', ')}.`);
                return;
            }
            if (!validateParticipantTotal()) {
                markInvalidQuestions([11, 12]);
                showValidationToast(
                    'Jumlah peserta internal dan eksternal jika dijumlahkan harus sama dengan jumlah peserta pada pertanyaan nomor 11.'
                );
                return;
            }
            if (!document.getElementById('confirm-accuracy').checked) {
                setSaveStatus('Centang pernyataan kebenaran data sebelum mengirim.', 'error');
                return;
            }
            bootstrap.Modal.getOrCreateInstance(document.getElementById('submit-confirm-modal')).show();
        });
        document.getElementById('confirm-submit-report').addEventListener('click', async () => {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('submit-confirm-modal')).hide();
            await submitReport();
        });
        window.addEventListener('beforeunload', event => {
            if (dirty) {
                event.preventDefault();
                event.returnValue = '';
            }
        });
        updateProgress();
        validateParticipantTotal();
        show(0);
    })();
</script>
