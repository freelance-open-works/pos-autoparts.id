<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;
use setasign\Fpdf\Fpdf;  // Ensure this is imported

class PdfMergerService
{
    /**
     * Merge multiple PDFs into a single PDF.
     *
     * @param array $pdfFiles Array of paths to PDF files
     * @param string $outputPath Path to save the merged PDF
     * @return void
     */
    public function merge(array $pdfFiles, string $outputPath): void
    {
        $pdf = new Fpdi();

        foreach ($pdfFiles as $file) {
            $pageCount = $pdf->setSourceFile($file);

            for ($i = 1; $i <= $pageCount; $i++) {
                $tplId = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tplId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId);
            }
        }

        $pdf->Output($outputPath, 'F');
    }
}
