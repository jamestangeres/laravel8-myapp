<?php

namespace App\Jobs;

//use PDF;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProcessPdfDownload
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // implements ShouldQueue

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // retreive all records from db
//        $employees = Employee::all();
//
//        $data = [
//            'employees' => $employees
//        ];
//
//        $contents = PDF::loadView('pdf.pdf-download', $data)->download()->getOriginalContent();
//        $path= 'public\pdf\sdfsdf.pdf';
//        Storage::put($path, $contents);
//        $pdf = Storage::disk('local')->path($path);
//        dd($pdf);
//        return response()->download($pdf);

        // $pdf = PDF::loadView('pdf.pdf-download', $data);
        // Storage::put('public/pdf/invoice.pdf', $pdf->output());
        // return $pdf->download('invoice.pdf');

    }
}
