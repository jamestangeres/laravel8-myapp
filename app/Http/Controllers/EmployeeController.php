<?php

namespace App\Http\Controllers;

use App\Jobs\DomPdf;
use App\Models\Employee;
use Illuminate\Http\Request;
use PDF;

//use TCPDF;


class EmployeeController extends Controller
{
    
    protected DomPdf $dompdf;

    /**
     * @param $dompdf
     */
  

    public function showEmployees()
    {
        $employee = Employee::all();
        return view('pdf.index', compact('employee'));
    }


    public function downloadPdf()
    {

        // retreive all records from db
        $data = Employee::all();

        $data = [
          'employees' => $data
        ];

        // share data to view
        view()->share('employee',$data);
        $pdf = PDF::loadView('pdf.pdf-download', $data);
        return $pdf->downloadMe('invASDFASDFoice.pdf' , 'D');

    }

    public function tcpdfDownload()
    {
        // retreive all records from db
        $data = Employee::all();

        $data = [
            'employees' => $data
        ];

        TCPDF::SetTitle('Hello World');
        TCPDF::AddPage();
        TCPDF::Write(0, 'Hello World');
        TCPDF::Output('hello_world.pdf');
    }


}
