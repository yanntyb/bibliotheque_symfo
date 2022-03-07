<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Client;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Repository\ClientRepository;
use App\Repository\ShelfRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route("/add/form", name:"add_form")]
    public function addForm(Request $request, ParameterBagInterface $container): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $file = $form["cover"]->getData();
            $ext = $file->guessExtension();
            if(!$ext){
                $ext = "bin";
            }

            $path = $container->get("upload.dir") . "/" .  $book->getTitle() . "." . $ext;
            $file->move($container->get("upload.dir"), $book->getTitle() . "." . $ext);
            $book->setCover($path);
            $this->em->persist($book);
            $this->em->flush();
        }

        return $this->render("book/add.html.twig", [
            "form" => $form->createView(),
        ]);
    }

    #[Route("/take/{id}", name: "take")]
    public function take(ShelfRepository $shelfRepository, Book $book): Response
    {
        $client = $this->clientR->find(1);
        $book
            ->setAvailable(false)
            ->setClient($client);

        $client->addBook($book);

        $this->em->flush();

        return $this->redirectToRoute("shelf_all", [
            "id" => $book->getId(),
            "animation" => "fade-out"
        ]);
    }

    #[Route("/rendre/{id}", name: "rendre")]
    /**
     * @var Client $client
     */
    public function rendre(Book $book): RedirectResponse
    {
        $client = $this->clientR->find(1);
        $book
            ->setAvailable(true)
            ->setClient(null);
        $client->removeBook($book);
        $this->em->flush();



        return $this->redirectToRoute("shelf_all", [
            "id" => $book->getId(),
            "animation" => "fade-in"
        ]);
    }
}
