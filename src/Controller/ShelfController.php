<?php

namespace App\Controller;

use App\Entity\Shelf;
use App\Repository\ClientRepository;
use App\Repository\ShelfRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/", "shelf_")]
class ShelfController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function index(EntityManagerInterface $em): Response
    {

        $shelf = new Shelf();
        $em->persist($shelf);
        $em->flush();

        return $this->render('shelf/index.html.twig', [
            'shelf' => $shelf,
        ]);
    }

    #[Route("/", "all")]
    public function all(ShelfRepository $repository, ClientRepository $clientRepository): Response
    {
        $shelfs = $repository->findAll();
        $client = $clientRepository->find(1);

        return $this->render("shelf/all.html.twig", [
            "shelfs" => $shelfs,
            "user" => $client
        ]);
    }
}
