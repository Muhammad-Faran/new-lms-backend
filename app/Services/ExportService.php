<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GenericExport;

class ExportService
{
    /**
     * Export data to an Excel file.
     *
     * @param string $fileName
     * @param array $data
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportToExcel(string $filename, array $data, array $headers)
    {
        return Excel::download(new GenericExport($data, $headers), $filename);
    }
}
