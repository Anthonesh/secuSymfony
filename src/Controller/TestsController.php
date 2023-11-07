<?php

namespace App\Controller;

use App\Entity\Tests;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/tests', name: 'app_tests', methods: ['POST'])]
    public function validationTests(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['title'], $data['date'], $data['statut'])) {
            return new JsonResponse(['error' => 'Données JSON incorrectes'], 400);
        }

        $test = new Tests();
        $test->setTitle($data['title']);
        $test->setDate(new \DateTime($data['date']));
        $test->setStatut($data['statut']);

        $this->entityManager->persist($test);
        $this->entityManager->flush();

        return new JsonResponse(['error' => 'test enregistré'], 200);
        
    }

}
