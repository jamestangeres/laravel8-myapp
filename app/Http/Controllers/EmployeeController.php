<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use PDF;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function showEmployees()
    {
        $employee = Employee::all();
        return view('pdf.index', compact('employee'));
    }

    //Generate PDF

    public function downloadPDF()
    {

        // retreive all records from db
        $data = Employee::all();

        $data = [
          'employees' => $data
        ];

        // share data to view
        view()->share('employee',$data);
        $pdf = PDF::loadView('pdf.pdf-download', $data);

        //download PDF file with download method
        return $pdf->download('pdf_file.pdf');

//        $path= 'public\pdf\sdfsdf.pdf';
//        Storage::put($path, $contents);
//        $pdf = Storage::disk('local')->path($path);
//        return response()->download($pdf);

    }
}
