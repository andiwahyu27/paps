<?php

namespace App\Services;

use App\Http\Controllers\Asesor\PenilaianController;
use App\Models\Pengajuan;
use App\Models\Penilaian;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;

class RincianPenilaianService
{
    public function build(Pengajuan $pengajuan): array
    {
        $result = app(PenilaianController::class)->rincianPenilaian($pengajuan->id);
        $pengajuan->refresh();
        $asesorIds = [$pengajuan->id_asesor1, $pengajuan->id_asesor2, $pengajuan->id_asesor3];
        $finalItemIds = Penilaian::where('id_pengajuan', $pengajuan->id)
            ->whereIn('id_asesor', $asesorIds)->where('pra_paska', 'final')
            ->pluck('id_item_penilaian')->unique()->flip();
        $rows = [];
        $hasMissingFinal = false;

        foreach ($result['data'] as $index => $unsur) {
            $subunsurs = $unsur['subunsurs'];
            $unsurItemIds = collect($subunsurs)->flatMap(fn ($sub) => collect($sub['items'])->pluck('id'));
            $unsurAvailable = $unsurItemIds->isNotEmpty() && $unsurItemIds->every(fn ($id) => $finalItemIds->has($id));
            if (!$unsurAvailable) $hasMissingFinal = true;
            $unsurRowIndex = count($rows);
            $rows[] = [
                'level' => 'unsur',
                'label' => 'UNSUR '.$this->roman($index + 1).' — '.strtoupper($unsur['unsur']).' ('.$unsur['bobot_unsur'].'%)',
                'nilai_subunsur' => null,
                'nilai_unsur' => $unsurAvailable ? $this->number($unsur['nilai_bobot_unsur_final']) : '-',
            ];

            $showSubunsurRows = count($subunsurs) > 1;
            $subunsurValues = [];
            foreach ($subunsurs as $subunsur) {
                $items = $subunsur['items'];
                $itemIds = collect($items)->pluck('id');
                $subunsurAvailable = $itemIds->isNotEmpty() && $itemIds->every(fn ($id) => $finalItemIds->has($id));
                if (!$subunsurAvailable) $hasMissingFinal = true;
                if ($subunsurAvailable) $subunsurValues[] = (float) $subunsur['nilai_bobot_subunsur_final'];

                if ($showSubunsurRows) {
                    $rows[] = [
                        'level' => 'subunsur',
                        'label' => 'Subunsur '.$subunsur['su'].' ('.$subunsur['bobot_subunsur'].'%)',
                        'nilai_subunsur' => $subunsurAvailable ? $this->number($subunsur['nilai_bobot_subunsur_final']) : '-',
                        'nilai_unsur' => null,
                    ];
                }

                if (count($items) > 1) {
                    foreach ($items as $item) {
                        $itemAvailable = $finalItemIds->has($item['id']);
                        if (!$itemAvailable) $hasMissingFinal = true;
                        $rows[] = [
                            'level' => 'item',
                            'label' => ($item['kode_item'] ? $item['kode_item'].' — ' : '').$item['nama_item'].' ('.$item['bobot_item'].'%)',
                            'nilai_subunsur' => $itemAvailable ? $this->number(($item['nilai_bobot_final'] * $subunsur['bobot_subunsur']) / 100) : '-',
                            'nilai_unsur' => null,
                        ];
                    }
                }
            }
            $rows[$unsurRowIndex]['nilai_subunsur'] = $unsurAvailable
                ? $this->number(count($subunsurs) === 0 ? $unsur['nilai_bobot_unsur_final'] : array_sum($subunsurValues))
                : '-';
            $groupEnd = count($rows) - 1;
            $rowspan = $groupEnd - $unsurRowIndex + 1;
            for ($rowIndex = $unsurRowIndex; $rowIndex <= $groupEnd; ++$rowIndex) {
                $rows[$rowIndex]['unsur_rowspan'] = $rowspan;
                $rows[$rowIndex]['show_nilai_unsur'] = $rowIndex === $unsurRowIndex;
                $rows[$rowIndex]['vmerge'] = $rowIndex === $unsurRowIndex ? 'restart' : 'continue';
            }
        }

        if ($hasMissingFinal) {
            Log::warning('Rincian penilaian final belum lengkap.', ['pengajuan_id' => $pengajuan->id]);
        }
        $nilaiFinalAvailable = !$hasMissingFinal;
        return [
            'rows' => $rows,
            'nilai_final' => $nilaiFinalAvailable ? $this->number($result['nilai_final']) : '-',
            'predikat_final' => $nilaiFinalAvailable ? strtoupper($pengajuan->predikat_final ?: $this->predikat($result['nilai_final'])) : '-',
            'is_valid' => $result['isValid'],
        ];
    }

    public function exportDocx(Pengajuan $pengajuan)
    {
        $pengajuan->loadMissing(['profile', 'jenis']);
        $data = $this->build($pengajuan);
        $templatePath = public_path('template_rincian_unsur_penilaian.docx');
        abort_unless(is_file($templatePath), 404, 'Template rincian penilaian tidak ditemukan.');

        $processor = new TemplateProcessor($templatePath);
        $processor->setValue('jenis_pengajuan', strtoupper(optional($pengajuan->jenis)->nama ?: '-'));
        $processor->setValue('nama_lembaga', strtoupper(optional($pengajuan->profile)->nama_lembaga ?: '-'));
        $processor->setValue('nilai_final', $data['nilai_final']);
        $processor->setValue('predikat_final', $data['predikat_final']);
        $tempFile = tempnam(sys_get_temp_dir(), 'rincian-');
        $processor->saveAs($tempFile);
        $this->reorderHeader($tempFile);
        $this->replaceDynamicTable($tempFile, $data['rows']);

        $name = preg_replace('/[^A-Za-z0-9._ -]/', '', (string) optional($pengajuan->profile)->nama_lembaga) ?: 'pengajuan';
        return response()->download($tempFile, 'Rincian Penilaian Akreditasi '.$name.'.docx')->deleteFileAfterSend(true);
    }

    private function reorderHeader(string $path): void
    {
        $zip = new ZipArchive();
        abort_unless($zip->open($path) === true, 500, 'Dokumen rincian penilaian gagal dibuka.');
        $xml = $zip->getFromName('word/document.xml');
        $xml = preg_replace_callback('/(<w:body\b[^>]*>)(.*?)(<w:tbl\b)/s', function ($match) {
            preg_match_all('/<w:p\b[^>]*>.*?<\/w:p>/s', $match[2], $paragraphs, PREG_OFFSET_CAPTURE);
            if (count($paragraphs[0]) < 3) return $match[0];
            $start = $paragraphs[0][0][1];
            $end = $paragraphs[0][2][1] + strlen($paragraphs[0][2][0]);
            $prefix = substr($match[2], 0, $start);
            $suffix = substr($match[2], $end);
            $middle = $paragraphs[0][0][0].$paragraphs[0][2][0].$paragraphs[0][1][0];
            return $match[1].$prefix.$middle.$suffix.$match[3];
        }, $xml, 1);
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    private function replaceDynamicTable(string $path, array $rows): void
    {
        $zip = new ZipArchive();
        abort_unless($zip->open($path) === true, 500, 'Dokumen rincian penilaian gagal dibuka.');
        $xml = $zip->getFromName('word/document.xml');
        $xml = preg_replace_callback('/<w:tbl\b[^>]*>.*?<\/w:tbl>/s', function ($tableMatch) use ($rows) {
            preg_match_all('/<w:tr\b[^>]*>.*?<\/w:tr>/s', $tableMatch[0], $matches);
            $templateRows = $matches[0];
            if (count($templateRows) < 4) return $tableMatch[0];
            $unsurTemplate = $this->findRow($templateRows, 'UNSUR ${UNSUR_1}') ?: $templateRows[2];
            $subunsurTemplate = $this->findRow($templateRows, 'Subunsur ${subunsur_2_1}') ?: $templateRows[3];
            $itemTemplate = $this->findRow($templateRows, '${item_4_2_1}') ?: $subunsurTemplate;
            $footerIndex = count($templateRows) - 2;
            $body = $templateRows[0].$templateRows[1];
            foreach ($rows as $row) {
                $template = $row['level'] === 'unsur' ? $unsurTemplate : ($row['level'] === 'subunsur' ? $subunsurTemplate : $itemTemplate);
                $body .= $this->fillRow($template, $row);
            }
            $body .= implode('', array_slice($templateRows, $footerIndex));
            $open = strpos($tableMatch[0], '>');
            $close = strrpos($tableMatch[0], '</w:tbl>');
            return substr($tableMatch[0], 0, $open + 1).$body.substr($tableMatch[0], $close);
        }, $xml, 1);
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    private function findRow(array $rows, string $needle): ?string
    {
        foreach ($rows as $row) if (strpos($row, $needle) !== false) return $row;
        return null;
    }

    private function fillRow(string $row, array $data): string
    {
        preg_match_all('/<w:tc\b[^>]*>.*?<\/w:tc>/s', $row, $cells);
        foreach ($cells[0] as $index => $cell) {
            $value = $index === 0 ? $data['label'] : ($index === 1 ? ($data['nilai_subunsur'] ?? '') : ($index === 2 ? ($data['nilai_unsur'] ?? '') : null));
            if ($value === null) continue;
            $seen = false;
            $escaped = htmlspecialchars((string) $value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
            $newCell = preg_replace_callback('/<w:t\b[^>]*>.*?<\/w:t>/s', function ($match) use (&$seen, $escaped) {
                if (!$seen) {
                    $seen = true;
                    return '<w:t xml:space="preserve">'.$escaped.'</w:t>';
                }
                return '<w:t xml:space="preserve"></w:t>';
            }, $cell);
            if (!$seen) {
                $newCell = str_replace('</w:tc>', '<w:p><w:r><w:t xml:space="preserve">'.$escaped.'</w:t></w:r></w:p></w:tc>', $cell);
            }
            if ($index > 0) {
                $newCell = $this->centerCell($newCell);
            }
            if (($data['level'] ?? null) === 'unsur' && $index === 2) {
                $newCell = preg_replace('/<w:shd\b[^>]*\/?>/s', '', $newCell);
            } elseif (($data['level'] ?? null) !== 'unsur') {
                $newCell = preg_replace('/<w:shd\b[^>]*\/?>/s', '', $newCell);
            }
            if ($index === 2 && isset($data['vmerge'])) {
                $newCell = $this->setVerticalMerge($newCell, $data['vmerge']);
            }
            $row = str_replace($cell, $newCell, $row);
        }
        return $row;
    }

    private function centerCell(string $cell): string
    {
        $cell = preg_replace('/<w:jc\b[^>]*\/?>/', '', $cell);
        $count = 0;
        $cell = preg_replace_callback('/<w:pPr\b[^>]*>/', function ($match) use (&$count) {
            ++$count;
            return $match[0].'<w:jc w:val="center"/>';
        }, $cell);
        if ($count === 0) {
            $cell = preg_replace('/(<w:p\b(?!Pr)[^>]*>)/', '$1<w:pPr><w:jc w:val="center"/></w:pPr>', $cell, 1);
        }
        return $cell;
    }

    private function setVerticalMerge(string $cell, string $merge): string
    {
        $cell = preg_replace('/<w:vMerge\b[^>]*\/?>/', '', $cell);
        $value = $merge === 'restart' ? ' w:val="restart"' : '';
        return preg_replace('/(<w:tcPr\b[^>]*>)/', '$1<w:vMerge'.$value.'/>', $cell, 1);
    }

    private function number($value): string
    {
        return number_format((float) $value, 2, '.', '');
    }

    private function predikat($value): string
    {
        if ($value >= 3.51) return 'Terakreditasi A';
        if ($value >= 3.00) return 'Terakreditasi B';
        return 'Tidak Terakreditasi';
    }

    private function roman(int $number): string
    {
        $map = [10 => 'X', 9 => 'IX', 5 => 'V', 4 => 'IV', 1 => 'I'];
        $result = '';
        foreach ($map as $value => $symbol) while ($number >= $value) { $result .= $symbol; $number -= $value; }
        return $result;
    }
}
