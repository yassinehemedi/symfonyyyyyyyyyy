<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
   


    #[Route('/showauthor/{var}', name: 'show_author')]
    public function showAuthor($var)
    {
        return $this->render("author/show.html.twig"
            ,array('nameAuthor'=>$var));
    }

   

    #[Route('/authorsList', name: 'authors_list')]
    public function list(AuthorRepository $repository)
    {
        $authors= $repository->findAll();
        return $this->render("author/listAuthors.html.twig",
            array("tabAuthors"=>$authors));
    }
    #[Route('/tri', name: 'tri_list')]
    public function trie(AuthorRepository $repository)
    {
        $authors= $repository->tri();
        return $this->render("author/listAuthors.html.twig",
            array("tabAuthors"=>$authors));
    }
    #[Route('/chercher', name: 'chercher_list')]
    public function chercher(Request $request,AuthorRepository $repository)
    {$id = $request->query->get('id');
        if($id==0)
        $authors= $repository->findAll();
        else
        $authors= $repository->findByid($id);
        return $this->render("author/listAuthors.html.twig",
            array("tabAuthors"=>$authors));
    }


   

    #[Route('/removeAuthor/{id}', name: 'author_remove')]
    public function deleteAuthor($id,AuthorRepository $repository,ManagerRegistry $managerRegistry)
    {
        $author= $repository->find($id);
        $em= $managerRegistry->getManager();
        
        $em->remove($author);
        $em->flush();
        return $this->redirectToRoute("authors_list");

    }
     /**
     * @Route("/adduser", name="add_user")
     */
    public function addUser(Request $request,ManagerRegistry $managerRegistry): Response
    {
        // Handle form submission
        $author = new  Author();
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $email = $request->request->get('email');
            $author->setUsername($username);
            $author->setEmail($email);
            $em= $managerRegistry->getManager();
            $em->persist($author);
            $em->flush();

            // Handle the data (e.g., save it to the database)
            // ...

         
        }

        // Render the form template
        return $this->render('author/ajout.html.twig');
    }
    /**
     * @Route("/updateuser/{id}", name="update_user")
     */
    public function updateUser(Request $request, $id,ManagerRegistry $managerRegistry): Response
    {            $em= $managerRegistry->getManager();

        $user = $em->getRepository(Author::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Handle form submission
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $email = $request->request->get('email');

            // Update user data
            $user->setUsername($username);
            $user->setEmail($email);
            $em= $managerRegistry->getManager();

            // Persist changes to the database
            $em->flush();

            // Redirect or return a response
        }

        // Render the form template with the user data
        return $this->render('author/update.html.twig', [
            'user' => $user,
        ]);
    }
}
