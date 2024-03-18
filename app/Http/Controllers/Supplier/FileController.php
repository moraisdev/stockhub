<?php

namespace App\Http\Controllers\Supplier;

use App\Models\CollectiveImport;
use Response;
use Storage;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

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


    public function viewPdfImportCollective($id)
    {
        $collectiveImport = CollectiveImport::with('shop.address')->findOrFail($id);
        $generatedPdfContent = PDF::loadView('supplier.pdf.importCollective', ['collectiveImport' => $collectiveImport])->output();
    
        $packingListPath = $collectiveImport->packing_list_path;
        $invoicePath = $collectiveImport->invoice_path;
    
        $pdf = new Fpdi();
    
        // Adiciona a página do PDF gerado dinamicamente
        $pdf->addPage();
        $pdf->setSourceFile(StreamReader::createByString($generatedPdfContent));
        $tplId = $pdf->importPage(1);
        $pdf->useTemplate($tplId, 10, 10, 200);
    
        // Função para adicionar um PDF do Storage
        $addPdfFromFile = function($path) use ($pdf) {
            if(Storage::exists($path)) {
                $fileContent = Storage::get($path);
                $pageCount = $pdf->setSourceFile(StreamReader::createByString($fileContent));
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $tplId = $pdf->importPage($pageNo);
                    if ($pageNo === 3 || $pageNo === 5) continue;
                    $pdf->addPage();
                    $pdf->useTemplate($tplId, 10, 10, 200);
                }
            }
        };
    
        // Adiciona os PDFs de packing list e invoice se existirem
        $addPdfFromFile($packingListPath);
        $addPdfFromFile($invoicePath);
    
        // Stream do PDF resultante
        return response($pdf->Output('S'), 200, ['Content-Type' => 'application/pdf']);
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
