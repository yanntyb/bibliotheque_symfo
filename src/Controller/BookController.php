<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Client;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Repository\ClientRepository;
use App\Repository\ShelfRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/book", "book_")]
class BookController extends AbstractController
{
    #[Route("/add", name: "add")]
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

    #[Route("/take", name: "take")]
    public function take(BookRepository $repository, ClientRepository $clientRepository, EntityManagerInterface $em, ShelfRepository $shelfRepository): JsonResponse
    {
        $client = $clientRepository->find(1);
        $data = json_decode(file_get_contents("php://input"));
        $id = $data->id;
        $book = $repository->find($id);
        $book
            ->setAvailable(false)
            ->setClient($client);

        $client->addBook($book);

        $em->flush();

        $shelfs = $shelfRepository->findAll();
        $data = [];
        foreach($shelfs as $shelf){
            $categories = [];
            foreach($shelf->getCategories() as $category){
                $books = [];
                foreach($category->getBooks() as $book){
                    $books[] = [
                        "title" => $book->getTitle(),
                        "id" => $book->getId(),
                        "available" => $book->getAvailable()
                    ];
                }
                $categories[] = [
                    "title" => $category->getTitle(),
                    "books" => $books
                ];
            }
            $data[] = [
                "id" => $shelf->getId(),
                "categories" => $categories
            ];
        }

        $clientBooks = [];
        foreach($client->getBooks() as $book){
            $clientBooks[] = [
                "title" => $book->getTitle(),
                "id" => $book->getId()
            ];
        }


        return $this->json(
            [
                "shelfs" => $data,
                "client" => [
                    "books" => $clientBooks,
                    "id" => $client->getId()
                ]
            ]
        );
    }

    #[Route("/render", name: "rendre")]
    /**
     * @var Client $client
     */
    public function rendre(BookRepository $repository, ClientRepository $clientRepository, EntityManagerInterface $em){
        $client = $clientRepository->find(1);
        /**
         * @var Book $book
         */
        foreach($client->getBooks() as $book){
            $book->setAvailable(true);
            $client->removeBook($book);
        }
        $em->flush();

        return $this->redirectToRoute("shelf_all");
    }
}
