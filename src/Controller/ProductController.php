<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ProductController extends AbstractController {

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct() {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function index($company, Request $request, ProductRepository $repo) {

        $company = filter_var($company, FILTER_SANITIZE_STRING);

        $limit = $request->query->getInt('limit', 100);
        $offset = $request->query->getInt('offset', 0);
        $search = $request->query->get('search', null);

        if ($search == null) {
            $products = $repo->getItems($company, $limit, $offset);
        } else {
            
            $search = filter_var($search, FILTER_SANITIZE_STRING);
            
            $products = $repo->searchItems($company, $search, $limit, $offset);
        }

        return JsonResponse::fromJsonString($this->serializer->serialize($products, 'json'));
    }

    public function find($company, $itemNumber, ProductRepository $repo) {

        $company = filter_var($company, FILTER_SANITIZE_STRING);
        $itemNumber = filter_var($itemNumber, FILTER_SANITIZE_STRING);

        $product = $repo->getItem($company, $itemNumber);

        return JsonResponse::fromJsonString($this->serializer->serialize($product, 'json'));
    }

}
