<?php

namespace App\Http\Controllers\Supplier;

use App\Models\CollectiveImport;
use Response;
use Storage;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class FileController extends Controller
{
    public function downloadInvoice($id)
    {
        $collective = CollectiveImport::findOrFail($id);
        $path = $collective->invoice_path;

        $fileContent = Storage::get($path);
        $mimeType = Storage::mimeType($path);

        return Response::make($fileContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . basename($path) . '"'
        ]);
    }

    public function downloadPdfImportCollective($id)
    {
        $collectiveImport = CollectiveImport::with('shop.address')->findOrFail($id);

        $pdf = PDF::loadView('supplier.pdf.importCollective', ['collectiveImport' => $collectiveImport]);

        return $pdf->download('importacao-coletiva-' . $id . '.pdf');
    }

    public function downloadPackingList($id)
    {
        $collective = CollectiveImport::findOrFail($id);
        $path = $collective->packing_list_path;

        $fileContent = Storage::get($path);
        $mimeType = Storage::mimeType($path);

        return Response::make($fileContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . basename($path) . '"'
        ]);
    }
}
