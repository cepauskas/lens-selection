<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Fetcher\AttributesFetcherInterface;

#[Route(path: "/", name: "api_")]
class ApiController extends AbstractController
{
    #[Route(path: "parameter", name: "parameter", methods: ["GET"])]
    public function parameter(Request $request, AttributesFetcherInterface $fetcher): Response
    {
        // pass through all query parameters
        // let filter out the invalid ones by the responsible service

        $query = $request->query->all();

        return $this->json(
            $fetcher->fetch($query)
        );
    }
}
