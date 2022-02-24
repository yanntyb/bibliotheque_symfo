<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/book", "book_")]
class BookController extends AbstractController
{
    #[Route('/all', name: 'all')]
    public function index(BookRepository $repository): Response
    {
        $books = $repository->getAllOrderByCategory();
        dd($books);
    }

    #[Route("/add", "add")]
    public function add(CategoryRepository $categoryRepository, BookRepository $bookRepository, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $book
            ->setTitle("book-" . $bookRepository->getLatestId())
            ->setCategory($categoryRepository->getRandom())
            ->setAvailable(true)
            ->setClient(null);

        $em->persist($book);
        $em->flush();

        return $this->render('book/index.html.twig', [
            'book' => $book,
        ]);

    }

}
