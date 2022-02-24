<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Repository\ShelfRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/category", "category_")]
class CategoryController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function index(CategoryRepository $categoryRepository, ShelfRepository $shelfRepository,EntityManagerInterface $em): Response
    {
        $id = $categoryRepository->getLatestId();

        $category = (new Category())
            ->setTitle("categorie-" . $id)
            ->setShelf($shelfRepository->getRandom());

        $em->persist($category);
        $em->flush();


        return $this->render('category/index.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route("/all", "all")]
    public function all(CategoryRepository $repository): Response
    {
        $categories = $repository->findAllOrderByShelfId();

        return $this->render("category/all.html.twig", [
            "categories" => $categories
        ]);
    }
}
