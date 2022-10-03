<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Exception;
use Dompdf\Options;
use Symfony\Component\Security\Core\Security;

class PDFService
{
    private $domPdf;

    public function __construct(private Security $security){
        $this->domPdf = new Dompdf();
        $domPdfOptions = new Options();
        $domPdfOptions->set('defaultFont', 'Garamond');
        $this->domPdf->setOptions($domPdfOptions);
    }

    public function showPdf($html){
        if($this->security->isGranted('ROLE_ADMIN')){
            $this->domPdf->loadHtml($html);
            $this->domPdf->render();
            ob_end_clean();
            $this->domPdf->stream('person-details.pdf');
        } else {
            return new Exception('Unauthorized');
        }
    }

    public function generateBinaryPdf($html){
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->output();
    }
}