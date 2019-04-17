<?php

namespace App\Controller;

use App\Repository\VendorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class VendorController extends AbstractController {

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct() {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function index($company, Request $request, VendorRepository $repo) {

        $company = filter_var($company, FILTER_SANITIZE_STRING);

        $limit = $request->query->getInt('limit', 100);
        $offset = $request->query->getInt('offset', 0);
        $search = $request->query->get('search', null);

        if ($search == null) {
            $vendors = $repo->getItems($company, $limit, $offset);
        } else {
            
            $search = filter_var($search, FILTER_SANITIZE_STRING);
            
            $vendors = $repo->searchItems($company, $search, $limit, $offset);
        }

        return JsonResponse::fromJsonString($this->serializer->serialize($vendors, 'json'));
    }

    public function find($company, $vendorNumber, VendorRepository $repo) {

        $company = filter_var($company, FILTER_SANITIZE_STRING);
        $vendorNumber = filter_var($vendorNumber, FILTER_SANITIZE_STRING);

        $vendor = $repo->getItem($company, $vendorNumber);

        return JsonResponse::fromJsonString($this->serializer->serialize($vendor, 'json'));
    }

}
