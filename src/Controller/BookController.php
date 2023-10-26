<?php

namespace App\Controller;
use App\Entity\Book;


use App\Form\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\BookRepository;



class BookController extends AbstractController
{
    /**
     * @Route("/updatebook/{ref}", name="update_book")
     */
    public function updateUser(Request $request, $ref,ManagerRegistry $managerRegistry): Response
    {            $em= $managerRegistry->getManager();

        $book = $em->getRepository(Book::class)->find($ref);

        if (!$book) {
            throw $this->createNotFoundException('User not found');
        }

        // Handle form submission
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
        
            $published = $request->request->get('published');
            $PublicationDate = $request->request->get('PublicationDate');
            $Bookid = $request->request->get('authorid');
            $category = $request->request->get('category');


            // Update user data
            $book->setPublished($published);
            $book->setTitle($title);
            $book->getPublicationDate($PublicationDate);
            $book->getCategory($category);
            $book->getAuthorId($Bookid);
            

            $em= $managerRegistry->getManager();

            // Persist changes to the database
            $em->flush();

            // Redirect or return a response
        }

        return $this->render('book/update.html.twig', [
            'book' => $book,
        ]);
    }
   /**
     * @Route("/ajouterbook", name="ajouterbook")
     */
    public function ajouterbook(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

        }

        return $this->render('book/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/List', name: 'list')]
    public function list(BookRepository $repository): Response
    {
        $books = $repository->findAll();
 //just saying if in html want to show {{book.published_date}} and its an error then {{book.published_date|date}} to make it date

        
        return $this->render("List.html.twig"
            ,array('books'=>$books));
    }
    #[Route('/tribook/{id}', name: 'tri_book')]
    public function tri(BookRepository $repository,$id): Response
    {   $books1 = $repository->triparauthor($id);
        $books = $repository->findAll();
        
        return $this->render("List.html.twig", [
            'books' => $books,
            'books1' => $books1,
        ]); 
    }
    #[Route('/removeBook/{id}', name: 'book_remove')]
    public function deleteBook($id,BookRepository $repository,ManagerRegistry $managerRegistry)
    {
        $Book= $repository->find($id);
        $em= $managerRegistry->getManager();
        
        $em->remove($Book);
        $em->flush();
        return $this->redirectToRoute("list");

    }
    
     /**
     * @Route("/books/{ref}", name="book_edit")
     */
    public function edit(Request $request,$ref): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $book = $entityManager->getRepository(Book::class)->find($ref);

        if (!$book) {
            throw $this->createNotFoundException('No book found for id ' . $ref);
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('list');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    }

   
