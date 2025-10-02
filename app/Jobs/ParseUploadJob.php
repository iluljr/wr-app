<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\Ticket;
use App\Models\Upload;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ParseUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $uploadId)
    {
    }

    public function handle(): void
    {
        $upload = Upload::findOrFail($this->uploadId);
        $full   = Storage::path($upload->stored_path);

        // Load Excel file
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($full);
            $spread = $reader->load($full);
        } catch (\Throwable $e) {
            \Log::warning("ParseUploadJob gagal load file {$full} untuk upload {$this->uploadId}: " . $e->getMessage());
            return;
        }

        $sheet = $spread->getSheet(0);
        $rows  = $sheet->toArray(null, true, true, true);

        // Cari baris header
        $headerRow = null;
        foreach ($rows as $i => $r) {
            $line = implode('|', array_map(fn ($v) => strtolower(trim((string)$v)), $r));
            if (
                $upload->kind === 'resolved' &&
                str_contains($line, 'created') &&
                str_contains($line, 'resolved') &&
                str_contains($line, 'request type')
            ) {
                $headerRow = $i;
                break;
            }
            if (
                $upload->kind === 'actual_end' &&
                str_contains($line, 'task id') &&
                str_contains($line, 'actual end')
            ) {
                $headerRow = $i;
                break;
            }
        }

        if (!$headerRow) {
            \Log::warning("ParseUploadJob header tidak ditemukan untuk upload {$this->uploadId}, kind={$upload->kind}");
            return;
        }

        // Mapping header ke kolom
        $headers = $rows[$headerRow];
        $map     = [];
        foreach ($headers as $col => $val) {
            $k = strtolower(trim(preg_replace('/\s+/', ' ', (string) $val)));
            if ($k !== '') $map[$k] = $col;
        }

        // Loop isi baris setelah header
        for ($i = $headerRow + 1; $i <= count($rows); $i++) {
            $r = $rows[$i] ?? null;
            if (!$r) continue;

            if ($upload->kind === 'resolved') {
                $created  = $r[$this->findCol($map, 'created')] ?? null;
                $resolved = $r[$this->findCol($map, 'resolved')] ?? null;
                $rtype    = $r[$this->findCol($map, 'request type')] ?? null;
                $rid      = $r[$this->findCol($map, 'request id')] ?? null;
                $subj     = $r[$this->findCol($map, 'subject')] ?? null;

                if (!$resolved && !$rtype && !$rid && !$subj) continue;

                Ticket::create([
                    'upload_id'       => $upload->id,
                    'created_at_src'  => $this->toJakartaTs($created),
                    'resolved_at_src' => $this->toJakartaTs($resolved),
                    'request_type'    => $this->nz($rtype),
                    'request_id'      => $this->nz($rid),
                    'subject'         => $this->nz($subj),
                ]);
            } else {
                $tid   = $r[$this->findCol($map, 'task id')] ?? null;
                $rid   = $r[$this->findCol($map, 'request id')] ?? null;
                $pid   = $r[$this->findCol($map, 'problem id')] ?? null;
                $cid   = $r[$this->findCol($map, 'change id')] ?? null;
                $title = $r[$this->findCol($map, 'title')] ?? null;
                $start   = $r[$this->findCol($map, 'scheduled start')] ?? null;
                $end   = $r[$this->findCol($map, 'actual end')] ?? null;

                if (!$end && !$tid && !$title) continue;

                Task::create([
                    'upload_id'          => $upload->id,
                    'task_id'            => $this->nz($tid),
                    'request_id'         => $this->nz($rid),
                    'problem_id'         => $this->nz($pid),
                    'change_id'          => $this->nz($cid),
                    'title'              => $this->nz($title),
                    'scheduled_start_at_src'  => $this->toJakartaTs($start),
                    'actual_end_at_src'  => $this->toJakartaTs($end),
                ]);
            }
        }
    }

    private function toJakartaTs($val)
    {
        if ($val === null || $val === '') return null;

        try {
            // 1) Excel serial number (xls/xlsx)
            if (is_numeric($val)) {
                // Ubah serial ke DateTime lalu INTERPRET jamnya sebagai WIB (tanpa geser jam)
                $dt  = Date::excelToDateTimeObject($val);               // DateTime "naif"
                $str = $dt->format('Y-m-d H:i:s');                      // jam as-is
                return Carbon::createFromFormat('Y-m-d H:i:s', $str, 'Asia/Jakarta'); // simpan WIB
            }

            // 2) Sudah berupa DateTimeInterface (jarang terjadi, tapi amankan)
            if ($val instanceof \DateTimeInterface) {
                $str = $val->format('Y-m-d H:i:s');                     // ambil jam as-is
                return Carbon::createFromFormat('Y-m-d H:i:s', $str, 'Asia/Jakarta'); // simpan WIB
            }

            // 3) String: coba beberapa format umum (WIB)
            $strVal  = trim((string)$val);
            $formats = [
                'd/m/Y h:i A',   // 29/08/2025 01:24 PM
                'd/m/Y h:i:s A', // 29/08/2025 01:24:00 PM
                'd/m/Y H:i',     // 29/08/2025 13:24
                'd/m/Y H:i:s',   // 29/08/2025 13:24:00
                'd-m-Y h:i A',
                'd-m-Y h:i:s A',
                'd-m-Y H:i',
                'd-m-Y H:i:s',
                'Y-m-d H:i:s',
                'Y-m-d H:i',
            ];

            foreach ($formats as $fmt) {
                try {
                    return Carbon::createFromFormat($fmt, $strVal, 'Asia/Jakarta'); // simpan WIB
                } catch (\Throwable $e) {
                    // coba format berikutnya
                }
            }

            // 4) Fallback parser (anggap WIB)
            return Carbon::parse($strVal, 'Asia/Jakarta'); // simpan WIB

        } catch (\Throwable $e) {
            \Log::warning("ParseUploadJob gagal parse tanggal '{$val}' di upload {$this->uploadId}: " . $e->getMessage());
            return null;
        }
    }



    private function nz($v)
    {
        $s = trim((string) $v);
        return ($s === '' || strtolower($s) === 'null') ? null : $s;
    }

    private function findCol(array $map, string $needle)
    {
        $needle = strtolower($needle);
        foreach ($map as $k => $col) {
            if (str_contains($k, $needle)) {
                return $col;
            }
        }
        return null;
    }
}
