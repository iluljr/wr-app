<?php

namespace App\Http\Controllers;

use App\Services\WRService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WRController extends Controller
{
    public function generate(Request $request, WRService $service)
    {
        $payload = $request->validate([
            'for_month' => 'required|date',
            'signature_date' => 'required|date',
            'template_id' => 'nullable|integer',
        ]);

        $month = Carbon::parse($payload['for_month'])->startOfMonth();
        $signatureDate = Carbon::parse($payload['signature_date'])->startOfDay();
        $templateId = $payload['template_id'] ?? null;

        $xlsxPath = $service->buildMonthlyWR($month, $signatureDate, $request->user()->id, $templateId);

        return response()->download($xlsxPath)->deleteFileAfterSend(false);
    }
}


