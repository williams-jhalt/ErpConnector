<?php

namespace App\Controller;

use App\Service\ErpOneConnector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController {

    
    public function index() {
        
        return $this->render('default/index.html.twig');
        
    }
    
    /**
     * Returns BASE64 encoded PDF document
     */
    public function pdf($company, $type, $record, $sequence, ErpOneConnector $service) {
        
        $company = filter_var($company, FILTER_SANITIZE_STRING);
        
        $response = $service->getPdf($company, $type, $record, (int) $sequence);
        
        return new Response($response->document);
        
    }

}
