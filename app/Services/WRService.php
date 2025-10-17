<?php

namespace App\Services;

use App\Models\Holiday;
use App\Models\ManualActivity;
use App\Models\RepetitiveActivity;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WRService
{
    private const DEFAULT_PROJECT_NAME = 'Nama Proyek';
    private const DEFAULT_PROJECT_COMPANY_UPPER = 'NAMA PERUSAHAAN PROYEK';
    private const DEFAULT_PROJECT_COMPANY_TITLE = 'Nama Perusahaan Proyek';
    private const DEFAULT_CONTRACTOR_UPPER = 'NAMA KONTRAKTOR';
    private const DEFAULT_CONTRACTOR_TITLE = 'Nama Kontraktor';
    private const DEFAULT_POSITION = 'Jabatan User';
    private const DEFAULT_CONTRACTOR_SUPERVISOR_NAME = 'Nama Atasan Kontraktor';
    private const DEFAULT_CONTRACTOR_SUPERVISOR_TITLE = 'Nama Jabatan Atasan Kontraktor';
    private const DEFAULT_PROJECT_SUPERVISOR_NAME = 'Nama Atasan Proyek';
    private const DEFAULT_PROJECT_SUPERVISOR_TITLE = 'Nama Jabatan Atasan Proyek';
    private const DEFAULT_USER_NAME = 'Nama User';

    private array $defaultSlots = [
        ['07:30', '08:30', '07.30-08.30'],
        ['08:30', '09:30', '08.30-09.30'],
        ['09:30', '10:30', '09.30-10.30'],
        ['10:30', '12:00', '10.30-12.00'],
        ['13:00', '14:00', '13.00-14.00'],
        ['14:00', '15:00', '14.00-15.00'],
        ['15:00', '16:30', '15.00-16.30'],
    ];

    private array $fridaySlots = [
        ['07:30', '08:30', '07.30-08.30'],
        ['08:30', '09:30', '08.30-09.30'],
        ['09:30', '10:30', '09.30-10.30'],
        ['10:30', '11:30', '10.30-11.30'],
        ['13:00', '14:00', '13.00-14.00'],
        ['14:00', '15:00', '14.00-15.00'],
        ['15:00', '17:00', '15.00-17.00'],
    ];

    private array $templatePageBreakRows = [34, 73, 115, 150, 174, 206];

    public function buildMonthlyWR(Carbon $month, Carbon $signatureDate, int $userId, ?int $templateId = null): string
    {
        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        $signatureLabel = 'Gresik, ' . $signatureDate->copy()->locale('id')->translatedFormat('d F Y');
        $user = User::find($userId);

        $tickets = Ticket::whereBetween('resolved_at_src', [$start, $end->copy()->endOfDay()])
            ->whereHas('upload', fn ($query) => $query->where('user_id', $userId))
            ->get();
        $tasks   = Task::whereBetween('actual_end_at_src', [$start, $end->copy()->endOfDay()])
            ->whereHas('upload', fn ($query) => $query->where('user_id', $userId))
            ->get();
        $manualActivities = ManualActivity::where('user_id', $userId)
            ->whereBetween('activity_date', [$start->toDateString(), $end->toDateString()])
            ->get();
        $repetitives = RepetitiveActivity::where('user_id', $userId)
            ->orderBy('start_time')
            ->get();
        $holidays = Holiday::whereBetween('date', [$start->toDateString(), $end->toDateString()])->pluck('date')->all();

        $spread = $this->buildUsingTemplate($start, $end, $tickets, $tasks, $manualActivities, $repetitives, $holidays, $signatureLabel, $user)
            ?? $this->buildFallbackSheet($start, $end, $tickets, $tasks, $manualActivities, $repetitives, $holidays, $signatureLabel, $user);

        $fileName = storage_path('app/wr/WR_' . $start->format('Y_m') . '.xlsx');
        @mkdir(dirname($fileName), 0777, true);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spread))->save($fileName);

        return $fileName;
    }
    private function buildUsingTemplate(
        Carbon $start,
        Carbon $end,
        $tickets,
        $tasks,
        $manualActivities,
        $repetitives,
        array $holidays,
        string $signatureLabel,
        ?User $user
    ): ?Spreadsheet {
        $templatePath = storage_path('app/private/templates/WR_template.xlsx');
        if (!is_file($templatePath)) {
            return null;
        }

        try {
            $spread = IOFactory::load($templatePath);
        } catch (\Throwable $e) {
            return null;
        }

        $sheet = $spread->getActiveSheet();
        $sheet->setTitle($start->isoFormat('MMMM'));
        $this->applySignatureLabel($sheet, $signatureLabel);
        $profile = $this->profileLabels($user);

        $sheet->setCellValue('B3', 'Project');
        $sheet->setCellValue('C3', ':');
        $sheet->setCellValue('D3', $profile['project_name_top']);
        $sheet->setCellValue('F3', 'Nama :');
        $sheet->setCellValue('G3', $profile['user_name_top']);
        $sheet->setCellValue('D4', $profile['project_company_top']);
        $sheet->setCellValue('F4', 'Posisi :');
        $sheet->setCellValue('G4', $profile['position_top']);
        $sheet->setCellValue('B5', 'Kontraktor');
        $sheet->setCellValue('C5', ':');
        $sheet->setCellValue('D5', $profile['contractor_top']);

        $sheet->setCellValue('B236', $profile['contractor_bottom']);
        $sheet->setCellValue('F236', $profile['project_company_bottom']);
        $sheet->setCellValue('B242', $profile['contractor_supervisor_name']);
        $sheet->setCellValue('B243', $profile['contractor_supervisor_title']);
        $sheet->setCellValue('F242', $profile['project_supervisor_name']);
        $sheet->setCellValue('F243', $profile['project_supervisor_title']);

        $profile = $this->profileLabels($user);

        $sheet->setCellValue('D3', $profile['project_name_top']);
        $sheet->setCellValue('D4', $profile['project_company_top']);
        $sheet->setCellValue('D5', $profile['contractor_top']);
        $sheet->setCellValue('G3', $profile['user_name_top']);
        $sheet->setCellValue('G4', $profile['position_top']);
        $sheet->setCellValue('B236', $profile['contractor_bottom']);
        $sheet->setCellValue('F236', $profile['project_company_bottom']);
        $sheet->setCellValue('B242', $profile['contractor_supervisor_name']);
        $sheet->setCellValue('B243', $profile['contractor_supervisor_title']);
        $sheet->setCellValue('F242', $profile['project_supervisor_name']);
        $sheet->setCellValue('F243', $profile['project_supervisor_title']);

        $sheet->setCellValue('D8', $start->copy()->locale('id')->translatedFormat('F'));

        $slotCount   = count($this->defaultSlots);
        $maxDays     = 31;
        $startRow    = 11;
        $maxDataRow  = $startRow + ($maxDays * $slotCount) - 1;

        for ($row = $startRow; $row <= $maxDataRow; $row++) {
            foreach (['B', 'C', 'D', 'E', 'F', 'G'] as $col) {
                $sheet->setCellValueExplicit($col . $row, null, DataType::TYPE_STRING);
            }
            $sheet->getStyle('B' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_NONE);
            $sheet->getRowDimension($row)->setRowHeight(-1);
        }

        $rowCursor = $startRow;
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $isWeekend = in_array($date->dayOfWeekIso, [6, 7], true);
            $isHoliday = in_array($date->toDateString(), $holidays, true);
            $isRedDate = $isWeekend || $isHoliday;
            $slots     = $date->isFriday() ? $this->fridaySlots : $this->defaultSlots;

            $dayTicketsCreated = $tickets->filter(function ($ticket) use ($date) {
                return $ticket->created_at_src
                    && $ticket->created_at_src->copy()->setTimezone('Asia/Jakarta')->isSameDay($date);
            });

            $dayTicketsResolved = $tickets->filter(function ($ticket) use ($date) {
                return $ticket->resolved_at_src
                    && $ticket->resolved_at_src->copy()->setTimezone('Asia/Jakarta')->isSameDay($date);
            });

            $dayTasksCreated = $tasks->filter(function ($task) use ($date) {
                return $task->scheduled_start_at_src
                    && $task->scheduled_start_at_src->copy()->setTimezone('Asia/Jakarta')->isSameDay($date);
            });

            $dayTasksClose = $tasks->filter(function ($task) use ($date) {
                return $task->actual_end_at_src
                    && $task->actual_end_at_src->copy()->setTimezone('Asia/Jakarta')->isSameDay($date);
            });

            $dayManuals = $manualActivities->filter(function ($activity) use ($date) {
                if (!$activity->activity_date) {
                    return false;
                }

                $activityDate = $activity->activity_date instanceof Carbon
                    ? $activity->activity_date
                    : Carbon::parse($activity->activity_date);

                return $activityDate->isSameDay($date);
            });

            for ($slotIdx = 0; $slotIdx < $slotCount; $slotIdx++) {
                $row = $rowCursor + $slotIdx;
                $slotLabel = $slots[$slotIdx][2];

                $sheet->setCellValue('C' . $row, $slotIdx + 1);
                $sheet->setCellValue('D' . $row, $slotLabel);

                if ($slotIdx === 0) {
                    $sheet->setCellValue('B' . $row, strtoupper($date->copy()->locale('id')->dayName));
                } elseif ($slotIdx === 3) {
                    $sheet->setCellValue('B' . $row, $date->copy()->translatedFormat('d F Y'));
                }

                $sheet->getStyle('B' . $row . ':G' . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'C8C8C8'],
                        ],
                    ],
                ]);
                $sheet->getStyle('B' . $row)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setRGB('000000');
                $sheet->getStyle('G' . $row)->getBorders()->getRight()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setRGB('000000');

                if ($isRedDate) {
                    $sheet->getStyle('B' . $row . ':G' . $row)
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F5F5F5');
                }

                $detailLines = [];
                $outputLines = [];
                if (!$isRedDate) {
                    $slotStart = $slots[$slotIdx][0];
                    $slotEnd = $slots[$slotIdx][1];

                    $slotRepetitives = $repetitives->filter(function ($activity) use ($slotStart, $slotEnd) {
                        return $this->timesOverlap($activity->start_time, $activity->end_time, $slotStart, $slotEnd);
                    });

                    foreach ($slotRepetitives as $repetitive) {
                        $title = trim((string) ($repetitive->title ?? '-'));
                        $detailLines[] = ' - ' . ($title === '' ? '-' : $title);

                        $output = trim((string) ($repetitive->output ?? ''));
                        if ($output !== '') {
                            $outputLines[] = ' - ' . $output;
                        }
                    }
                }


                foreach ($dayTicketsCreated as $ticket) {
                    $slot = $this->determineSlotIndex($ticket->resolved_at_src, $slots);
                    if ($slot !== $slotIdx) {
                        continue;
                    }

                    $detailLines[] = sprintf(
                        ' - Evaluasi Kebutuhan Ticket Baru : (%s) ID %s : %s',
                        $ticket->request_type ?? '-',
                        $ticket->request_id ?? '-',
                        $ticket->subject ?? '-'
                    );
                    $outputLines[] = sprintf(
                        ' - Tiket %s (%s) : %s Telah dievaluasi',
                        $ticket->request_id ?? '-',
                        $ticket->request_type ?? '-',
                        $ticket->subject ?? '-'
                    );
                }

                foreach ($dayTicketsResolved as $ticket) {
                    $slot = $this->determineSlotIndex($ticket->resolved_at_src, $slots);
                    if ($slot !== $slotIdx) {
                        continue;
                    }

                    $detailLines[] = sprintf(
                        ' - Penyelesaian pekerjaan dan pengisian resolution Ticket (%s) ID %s : %s',
                        $ticket->request_type ?? '-',
                        $ticket->request_id ?? '-',
                        $ticket->subject ?? '-'
                    );
                    $outputLines[] = sprintf(
                        ' - Tiket %s (%s) terselesaikan: %s',
                        $ticket->request_id ?? '-',
                        $ticket->request_type ?? '-',
                        $ticket->subject ?? '-'
                    );
                }

                foreach ($dayTasksCreated as $task) {
                    $slot = $this->determineSlotIndex($task->actual_end_at_src, $slots);
                    if ($slot !== $slotIdx) {
                        continue;
                    }

                    $detailLines[] = sprintf(
                        ' - Pengerjaan Task ID  (%s) : %s',
                        $task->task_id ?? '-',
                        $task->title ?? '-'
                    );
                    $outputLines[] = sprintf(
                        ' - Task ID %s : %s Dalam proses pengerjaan',
                        $task->task_id ?? '-',
                        $task->title ?? '-'
                    );
                }

                foreach ($dayTasksClose as $task) {
                    $slot = $this->determineSlotIndex($task->actual_end_at_src, $slots);
                    if ($slot !== $slotIdx) {
                        continue;
                    }

                    $atas = $task->request_id
                        ? "Request ID {$task->request_id}"
                        : ($task->problem_id
                            ? "Problem ID {$task->problem_id}"
                            : ($task->change_id ? "Change ID {$task->change_id}" : 'Request ID -'));

                    $detailLines[] = sprintf(
                        ' - Penyelesaian pengerjaan dan Close Task ID  (%s) : %s',
                        $task->task_id ?? '-',
                        $task->title ?? '-'
                    );
                    $outputLines[] = sprintf(
                        ' - Task ID %s : %s Terselesaikan',
                        $task->task_id ?? '-',
                        $task->title ?? '-'
                    );
                }

                foreach ($dayManuals as $activity) {
                    $coveredSlots = $this->determineSlotIndexesFromRange(
                        $activity->start_time,
                        $activity->end_time,
                        $slots
                    );

                    if (!in_array($slotIdx, $coveredSlots, true)) {
                        continue;
                    }

                    $title = trim((string) ($activity->title ?? '-'));
                    $timeRange = sprintf(
                        '%s-%s',
                        $this->formatClock($activity->start_time),
                        $this->formatClock($activity->end_time)
                    );
                    $detailLines[] = sprintf(' - %s (%s)', $title ?: '-', $timeRange);

                    $output = trim((string) ($activity->output ?? ''));
                    if ($output !== '') {
                        $outputLines[] = ' - ' . $output;
                    }
                }

                $sheet->setCellValue('E' . $row, $detailLines ? implode("\n", $detailLines) : null);
                $sheet->setCellValue('G' . $row, $outputLines ? implode("\n", $outputLines) : null);

                $sheet->getStyle('E' . $row . ':G' . $row)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $fontColor = $isRedDate ? 'C00000' : '000000';
                $sheet->getStyle('B' . $row . ':G' . $row)->getFont()->getColor()->setRGB($fontColor);
            }

            $sheet->getStyle('B' . $rowCursor . ':G' . $rowCursor)->applyFromArray([
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                ],
            ]);
            $lastRowOfDay = $rowCursor + $slotCount - 1;
            $sheet->getStyle('B' . $lastRowOfDay . ':G' . $lastRowOfDay)->applyFromArray([
                'borders' => [
                    'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                ],
            ]);

            $rowCursor += $slotCount;
        }

        $this->configurePageSetup($sheet, $rowCursor - 1);

        return $spread;
    }

    private function buildFallbackSheet(
        Carbon $start,
        Carbon $end,
        $tickets,
        $tasks,
        $manualActivities,
        $repetitives,
        array $holidays,
        string $signatureLabel,
        ?User $user
    ): Spreadsheet {
        $spread = new Spreadsheet();
        $sheet  = $spread->getActiveSheet();
        $sheet->setTitle($start->isoFormat('MMMM'));
        $this->applySignatureLabel($sheet, $signatureLabel);
        $profile = $this->profileLabels($user);

        $sheet->setCellValue('B3', 'Project');
        $sheet->setCellValue('C3', ':');
        $sheet->setCellValue('D3', $profile['project_name_top']);
        $sheet->setCellValue('F3', 'Nama :');
        $sheet->setCellValue('G3', $profile['user_name_top']);
        $sheet->setCellValue('D4', $profile['project_company_top']);
        $sheet->setCellValue('F4', 'Posisi :');
        $sheet->setCellValue('G4', $profile['position_top']);
        $sheet->setCellValue('B5', 'Kontraktor');
        $sheet->setCellValue('C5', ':');
        $sheet->setCellValue('D5', $profile['contractor_top']);

        $sheet->setCellValue('B236', $profile['contractor_bottom']);
        $sheet->setCellValue('F236', $profile['project_company_bottom']);
        $sheet->setCellValue('B242', $profile['contractor_supervisor_name']);
        $sheet->setCellValue('B243', $profile['contractor_supervisor_title']);
        $sheet->setCellValue('F242', $profile['project_supervisor_name']);
        $sheet->setCellValue('F243', $profile['project_supervisor_title']);


        $sheet->getDefaultRowDimension()->setRowHeight(18);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(6);
        $sheet->getColumnDimension('D')->setWidth(16);
        $sheet->getColumnDimension('E')->setWidth(55);
        $sheet->getColumnDimension('F')->setWidth(4);
        $sheet->getColumnDimension('G')->setWidth(55);

        $row = 2;
        $sheet->mergeCells('B' . $row . ':G' . $row);
        $sheet->setCellValue('B' . $row, 'WR Bulanan - ' . $start->isoFormat('MMMM Y'));
        $sheet->getStyle('B' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row += 2;

        $sheet->setCellValue('B' . $row, 'HARI');
        $sheet->setCellValue('C' . $row, 'NO');
        $sheet->setCellValue('D' . $row, 'WAKTU');
        $sheet->setCellValue('E' . $row, 'DETAIL AKTIVITAS PEKERJAAN');
        $sheet->mergeCells('E' . $row . ':F' . $row);
        $sheet->setCellValue('G' . $row, 'OUTPUT AKTIVITAS PEKERJAAN');
        $sheet->getStyle('B' . $row . ':G' . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5EEF9']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'A0A0A0']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->freezePane('B' . ($row + 1));
        $row++;

        $slotCount = count($this->defaultSlots);

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $isWeekend = in_array($date->dayOfWeekIso, [6, 7], true);
            $isHoliday = in_array($date->toDateString(), $holidays, true);
            $isRedDate = $isWeekend || $isHoliday;
            $slots     = $date->isFriday() ? $this->fridaySlots : $this->defaultSlots;

            $dayTicketsResolved = $tickets->filter(function ($ticket) use ($date) {
                return $ticket->resolved_at_src
                    && $ticket->resolved_at_src->copy()->setTimezone('Asia/Jakarta')->isSameDay($date);
            });

            $dayTasksClose = $tasks->filter(function ($task) use ($date) {
                return $task->actual_end_at_src
                    && $task->actual_end_at_src->copy()->setTimezone('Asia/Jakarta')->isSameDay($date);
            });

            $dayManuals = $manualActivities->filter(function ($activity) use ($date) {
                if (!$activity->activity_date) {
                    return false;
                }

                $activityDate = $activity->activity_date instanceof Carbon
                    ? $activity->activity_date
                    : Carbon::parse($activity->activity_date);

                return $activityDate->isSameDay($date);
            });

            $dayTopRow = $row;
            $sheet->mergeCells('B' . $dayTopRow . ':B' . ($dayTopRow + $slotCount - 1));
            $sheet->setCellValue('B' . $dayTopRow, strtoupper($date->copy()->locale('id')->dayName) . "\n" . $date->copy()->translatedFormat('d F Y'));
            $sheet->getStyle('B' . $dayTopRow)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            for ($slotIdx = 0; $slotIdx < $slotCount; $slotIdx++) {
                $currentRow = $dayTopRow + $slotIdx;
                $sheet->setCellValue('C' . $currentRow, $slotIdx + 1);
                $sheet->setCellValue('D' . $currentRow, $slots[$slotIdx][2]);
                $sheet->mergeCells('E' . $currentRow . ':F' . $currentRow);

                $sheet->getStyle('B' . $currentRow . ':G' . $currentRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C8C8C8']]],
                ]);
                $sheet->getStyle('B' . $currentRow)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setRGB('000000');
                $sheet->getStyle('G' . $currentRow)->getBorders()->getRight()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setRGB('000000');

                if ($isRedDate) {
                    $sheet->getStyle('B' . $currentRow . ':G' . $currentRow)
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F5F5F5');
                } else {
                    $sheet->getStyle('B' . $currentRow . ':G' . $currentRow)->getFill()->setFillType(Fill::FILL_NONE);
                }

                $sheet->getRowDimension($currentRow)->setRowHeight($slotIdx === 0 ? 85 : -1);

                $detailLines = [];
                $outputLines = [];
                if (!$isRedDate) {
                    $slotStart = $slots[$slotIdx][0];
                    $slotEnd = $slots[$slotIdx][1];

                    $slotRepetitives = $repetitives->filter(function ($activity) use ($slotStart, $slotEnd) {
                        return $this->timesOverlap($activity->start_time, $activity->end_time, $slotStart, $slotEnd);
                    });

                    foreach ($slotRepetitives as $repetitive) {
                        $title = trim((string) ($repetitive->title ?? '-'));
                        $detailLines[] = ' - ' . ($title === '' ? '-' : $title);

                        $output = trim((string) ($repetitive->output ?? ''));
                        if ($output !== '') {
                            $outputLines[] = ' - ' . $output;
                        }
                    }
                }


                foreach ($dayTicketsResolved as $ticket) {
                    $slot = $this->determineSlotIndex($ticket->resolved_at_src, $slots);
                    if ($slot !== $slotIdx) {
                        continue;
                    }

                    $detailLines[] = sprintf(
                        ' - Penyelesaian pekerjaan dan pengisian resolution Ticket (%s) ID %s : %s',
                        $ticket->request_type ?? '-',
                        $ticket->request_id ?? '-',
                        $ticket->subject ?? '-'
                    );
                    $outputLines[] = sprintf(
                        ' - Tiket %s (%s) terselesaikan: %s',
                        $ticket->request_id ?? '-',
                        $ticket->request_type ?? '-',
                        $ticket->subject ?? '-'
                    );
                }

                foreach ($dayTasksClose as $task) {
                    $slot = $this->determineSlotIndex($task->actual_end_at_src, $slots);
                    if ($slot !== $slotIdx) {
                        continue;
                    }

                    $atas = $task->request_id
                        ? "Request ID {$task->request_id}"
                        : ($task->problem_id
                            ? "Problem ID {$task->problem_id}"
                            : ($task->change_id ? "Change ID {$task->change_id}" : 'Request ID -'));

                    $detailLines[] = sprintf(
                        ' - Penyelesaian pengerjaan dan Close Task ID  (%s) : %s',
                        $task->task_id ?? '-',
                        $task->title ?? '-'
                    );
                    $outputLines[] = sprintf(
                        ' - Task ID %s : %s Terselesaikan',
                        $task->task_id ?? '-',
                        $task->title ?? '-'
                    );
                }

                foreach ($dayManuals as $activity) {
                    $coveredSlots = $this->determineSlotIndexesFromRange($activity->start_time, $activity->end_time, $slots);
                    if (!in_array($slotIdx, $coveredSlots, true)) {
                        continue;
                    }

                    $title = trim((string) ($activity->title ?? '-'));
                    $timeRange = sprintf('%s-%s', $this->formatClock($activity->start_time), $this->formatClock($activity->end_time));
                    $detailLines[] = sprintf(' - %s (%s)', $title ?: '-', $timeRange);

                    $output = trim((string) ($activity->output ?? ''));
                    if ($output !== '') {
                        $outputLines[] = ' - ' . $output;
                    }
                }

                $sheet->setCellValue('E' . $currentRow, $detailLines ? implode("\n", $detailLines) : null);
                $sheet->setCellValue('G' . $currentRow, $outputLines ? implode("\n", $outputLines) : null);
                $sheet->getStyle('E' . $currentRow . ':G' . $currentRow)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $fontColor = $isRedDate ? 'C00000' : '000000';
                $sheet->getStyle('B' . $currentRow . ':G' . $currentRow)->getFont()->getColor()->setRGB($fontColor);
            }

            $sheet->getStyle('B' . $dayTopRow . ':G' . $dayTopRow)->applyFromArray([
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                ],
            ]);
            $dayBottomRow = $dayTopRow + $slotCount - 1;
            $sheet->getStyle('B' . $dayBottomRow . ':G' . $dayBottomRow)->applyFromArray([
                'borders' => [
                    'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                ],
            ]);

            $row = $dayTopRow + $slotCount;
        }

        $this->configurePageSetup($sheet, $row - 1);

        return $spread;
    }

    private function profileLabels(?User $user): array
    {
        $projectName = $this->trimNullable($user?->project_name);
        $projectCompany = $this->trimNullable($user?->project_company);
        $contractor = $this->trimNullable($user?->contractor_name);
        $position = $this->trimNullable($user?->position);

        return [
            'project_name_top' => $this->toUpperOrDefault($projectName, self::DEFAULT_PROJECT_NAME),
            'project_company_top' => $this->toUpperOrDefault($projectCompany, self::DEFAULT_PROJECT_COMPANY_UPPER),
            'contractor_top' => $this->toUpperOrDefault($contractor, self::DEFAULT_CONTRACTOR_UPPER),
            'user_name_top' => $this->toUpperOrDefault($this->trimNullable($user?->name), self::DEFAULT_USER_NAME),
            'position_top' => $this->toUpperOrDefault($position, self::DEFAULT_POSITION),
            'contractor_bottom' => $this->titleCasePreservingOrDefault($contractor, self::DEFAULT_CONTRACTOR_TITLE),
            'project_company_bottom' => $this->titleCasePreservingOrDefault($projectCompany, self::DEFAULT_PROJECT_COMPANY_TITLE),
            'contractor_supervisor_name' => $this->valueOrDefault($user?->contractor_supervisor_name, self::DEFAULT_CONTRACTOR_SUPERVISOR_NAME),
            'contractor_supervisor_title' => $this->valueOrDefault($user?->contractor_supervisor_title, self::DEFAULT_CONTRACTOR_SUPERVISOR_TITLE),
            'project_supervisor_name' => $this->valueOrDefault($user?->project_supervisor_name, self::DEFAULT_PROJECT_SUPERVISOR_NAME),
            'project_supervisor_title' => $this->valueOrDefault($user?->project_supervisor_title, self::DEFAULT_PROJECT_SUPERVISOR_TITLE),
        ];
    }

    private function toUpperOrDefault(?string $value, string $default): string
    {
        $trimmed = $this->trimNullable($value);
        return $trimmed === null ? $default : mb_strtoupper($trimmed, 'UTF-8');
    }

    private function titleCasePreservingOrDefault(?string $value, string $default): string
    {
        $trimmed = $this->trimNullable($value);
        return $trimmed === null ? $default : $this->titleCasePreserving($trimmed);
    }

    private function valueOrDefault(?string $value, string $default): string
    {
        $trimmed = $this->trimNullable($value);
        return $trimmed === null ? $default : $trimmed;
    }

    private function trimNullable(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);
        return $trimmed === '' ? null : $trimmed;
    }

    private function titleCasePreserving(string $value): string
    {
        $segments = preg_split('/(\s+)/u', $value, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = '';

        foreach ($segments as $segment) {
            if ($segment === '') {
                continue;
            }

            if (preg_match('/^\s+$/u', $segment)) {
                $result .= $segment;
                continue;
            }

            $lettersOnly = preg_replace('/[^\pL]+/u', '', $segment);
            if ($lettersOnly !== '' && mb_strtoupper($lettersOnly, 'UTF-8') === $lettersOnly) {
                $result .= $segment;
                continue;
            }

            $lower = mb_strtolower($segment, 'UTF-8');
            $result .= mb_convert_case($lower, MB_CASE_TITLE, 'UTF-8');
        }

        return $result;
    }

    private function determineSlotIndex(Carbon $timestamp, array $slots): int
    {
        $time = $timestamp->copy()->setTimezone('Asia/Jakarta');
        $timeValue = (int) $time->format('Hi');
        $lastIndex = count($slots) - 1;

        $firstStart = (int) str_replace(':', '', $slots[0][0]);
        if ($timeValue < $firstStart) {
            return 0;
        }

        $lastEnd = (int) str_replace(':', '', $slots[$lastIndex][1]);
        if ($timeValue >= $lastEnd) {
            return $lastIndex;
        }

        foreach ($slots as $index => $range) {
            [$start, $end] = $range;
            $startValue = (int) str_replace(':', '', $start);
            $endValue   = (int) str_replace(':', '', $end);
            $isLast     = $index === $lastIndex;
            $endComparison = $isLast ? $endValue + 1 : $endValue;

            if ($timeValue >= $startValue && $timeValue < $endComparison) {
                return $index;
            }
        }

        return $lastIndex;
    }

    private function determineSlotIndexFromClock(?string $time, array $slots): int
    {
        $minutes = $this->timeToMinutes($time);
        if ($minutes === null) {
            return 0;
        }

        $lastIndex = count($slots) - 1;
        $firstStart = $this->timeToMinutes($slots[0][0]) ?? 0;
        if ($minutes < $firstStart) {
            return 0;
        }

        $lastEnd = $this->timeToMinutes($slots[$lastIndex][1]) ?? $minutes;
        if ($minutes >= $lastEnd) {
            return $lastIndex;
        }

        foreach ($slots as $index => $range) {
            $startValue = $this->timeToMinutes($range[0]) ?? 0;
            $endValue   = $this->timeToMinutes($range[1]) ?? 0;
            $isLast = $index === $lastIndex;
            $endComparison = $isLast ? $endValue + 1 : $endValue;

            if ($minutes >= $startValue && $minutes < $endComparison) {
                return $index;
            }
        }

        return $lastIndex;
    }

    private function determineSlotIndexesFromRange(?string $startTime, ?string $endTime, array $slots): array
    {
        $startMinutes = $this->timeToMinutes($startTime);
        $endMinutes = $this->timeToMinutes($endTime);

        if ($startMinutes === null || $endMinutes === null) {
            return [];
        }

        $coveredSlots = [];

        foreach ($slots as $index => $range) {
            $slotStart = $this->timeToMinutes($range[0]);
            $slotEnd = $this->timeToMinutes($range[1]);

            // kondisi: slot dan aktivitas bersinggungan (overlap)
            if ($startMinutes < $slotEnd && $endMinutes > $slotStart) {
                $coveredSlots[] = $index;
            }
        }

        return $coveredSlots;
    }

    private function timeToMinutes(?string $time): ?int
    {
        if ($time === null || $time === '') {
            return null;
        }

        $normalized = substr((string) $time, 0, 5);
        if (!preg_match('/^\d{2}:\d{2}$/', $normalized)) {
            return null;
        }

        [$hour, $minute] = explode(':', $normalized);

        return ((int) $hour * 60) + (int) $minute;
    }
    private function timesOverlap($startA, $endA, string $slotStart, string $slotEnd): bool
    {
        $activityStart = $this->timeToMinutes($startA);
        $activityEnd = $this->timeToMinutes($endA);
        $slotStartMinutes = $this->timeToMinutes($slotStart);
        $slotEndMinutes = $this->timeToMinutes($slotEnd);

        if ($activityStart === null || $activityEnd === null || $slotStartMinutes === null || $slotEndMinutes === null) {
            return false;
        }

        return $activityStart < $slotEndMinutes && $activityEnd > $slotStartMinutes;
    }

    private function formatClock(?string $time): string
    {
        $normalized = substr((string) $time, 0, 5);
        return preg_match('/^\\d{2}:\\d{2}$/', $normalized) ? $normalized : (string) $time;
    }

    private function applySignatureLabel(Worksheet $sheet, string $label): void
    {
        foreach ($sheet->getRowIterator() as $row) {
            $cells = $row->getCellIterator();
            $cells->setIterateOnlyExistingCells(true);

            foreach ($cells as $cell) {
                $value = $cell->getValue();
                if (!is_string($value)) {
                    continue;
                }

                $normalized = strtolower(trim((string) $value));
                if (str_contains($normalized, 'gresik,')) {
                    $cell->setValue($label);
                    return;
                }
            }
        }

        $sheet->setCellValue('F233', $label);
    }

    private function configurePageSetup(Worksheet $sheet, int $lastDataRow): void
    {
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        $pageSetup->setPaperSize(PageSetup::PAPERSIZE_A4);
        $pageSetup->setFitToWidth(1);
        $pageSetup->setFitToHeight(0);

        foreach (array_keys($sheet->getBreaks()) as $coord) {
            $sheet->setBreak($coord, Worksheet::BREAK_NONE);
        }

        foreach ($this->templatePageBreakRows as $row) {
            if ($row <= $lastDataRow) {
                $sheet->setBreak('A' . $row, Worksheet::BREAK_ROW);
            }
        }
    }
}
