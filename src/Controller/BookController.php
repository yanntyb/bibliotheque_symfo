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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/book", "book_")]
class BookController extends AbstractController
{

    private BookRepository $bookR;
    private ClientRepository $clientR;
    private CategoryRepository $categoryR;
    private EntityManagerInterface $em;

    public function __construct(BookRepository $bookRepository, CategoryRepository $categoryRepository, ClientRepository $clientRepository, EntityManagerInterface $em){
        $this->bookR = $bookRepository;
        $this->clientR = $clientRepository;
        $this->categoryR = $categoryRepository;
        $this->em = $em;
    }

    #[Route("/add", name: "add")]
    public function add(): Response
    {
        $book = new Book();
        $book
            ->setTitle("book-" . $this->bookR->getLatestId())
            ->setCategory($this->categoryR->getRandom())
            ->setAvailable(true)
            ->setClient(null);

        $this->em->persist($book);
        $this->em->flush();

        return $this->render('book/index.html.twig', [
            'book' => $book,
        ]);

    }

    #[Route("/take", name: "take")]
    public function take(ShelfRepository $shelfRepository): JsonResponse
    {
        $client = $this->clientR->find(1);
        $data = json_decode(file_get_contents("php://input"));
        $id = $data->id;
        $book = $this->bookR->find($id);
        $book
            ->setAvailable(false)
            ->setClient($client);

        $client->addBook($book);

        $this->em->flush();

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
    public function rendre(): RedirectResponse
    {
        $client = $this->clientR->find(1);
        /**
         * @var Book $book
         */
        foreach($client->getBooks() as $book){
            $book->setAvailable(true);
            $client->removeBook($book);
        }
        $this->em->flush();

        return $this->redirectToRoute("shelf_all");
    }
}
