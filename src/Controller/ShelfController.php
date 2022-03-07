<?php

namespace App\Controller;

use App\Entity\Shelf;
use App\Form\ShelfType;
use App\Repository\ClientRepository;
use App\Repository\ShelfRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    #[Route("/add/form", name:"add_form")]
    public function addForm(): Response
    {
        $shelf = new Shelf();
        $form = $this->createForm(ShelfType::class, $shelf);

        return $this->render("shelf/add.html.twig", [
            "form" => $form->createView(),
        ]);
    }

    #[Route("/", "all")]
    public function all(ShelfRepository $repository, ClientRepository $clientRepository): Response
    {
        $shelfs = $repository->findAll();
        $client = $clientRepository->find(1);


        if(isset($_GET["id"])){
            $fade = $_GET["id"];
        }
        else{
            $fade = 0;
        }

        return $this->render("shelf/all.html.twig", [
            "shelfs" => $shelfs,
            "user" => $client,
            "fade" => $fade,
        ]);
    }
}
