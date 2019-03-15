<?php

namespace App\Controller;

use App\Repository\SalesPersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SalesPersonController extends AbstractController {

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct() {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function index($company, Request $request, SalesPersonRepository $repo) {

        $company = filter_var($company, FILTER_SANITIZE_STRING);

        $limit = $request->query->getInt('limit', 100);
        $offset = $request->query->getInt('offset', 0);
        $search = $request->query->get('search', null);

        if ($search == null) {
            $salespeople = $repo->getItems($company, $limit, $offset);
        } else {
            
            $search = filter_var($search, FILTER_SANITIZE_STRING);
            
            $salespeople = $repo->searchItems($company, $search, $limit, $offset);
        }

        return JsonResponse::fromJsonString($this->serializer->serialize($salespeople, 'json'));
    }

    public function find($company, $id, SalesPersonRepository $repo) {

        $company = filter_var($company, FILTER_SANITIZE_STRING);
        $id = filter_var($id, FILTER_SANITIZE_STRING);

        $salesperson = $repo->getItem($company, $id);

        return JsonResponse::fromJsonString($this->serializer->serialize($salesperson, 'json'));
    }

}
