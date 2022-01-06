<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Livre;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Genre;

class BookstoreController extends AbstractController
{
    #[Route('/store', name: 'store')]
    #[Route('/home', name: 'home')]
    public function index(ManagerRegistry $doctrine, Request $req, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $repo = $doctrine->getRepository(Livre::class);
        $items = $repo->findAll();
        $livres = $paginator->paginate(
            $items,
            $req->query->getInt('page', 1),
            8
        );

        $repo = $doctrine->getRepository(Genre::class);
        $genres = $repo->findAll();

        return $this->render('bookstore/index.html.twig', [
            'controller_name' => 'BookstoreController',
            'livres' => $livres,
            'genres' => $genres
        ]);
    }

    #[Route('/bookstore', name: 'bookstore')]
    public function books(ManagerRegistry $doctrine, Request $req, PaginatorInterface $paginator) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $repo = $doctrine->getRepository(Livre::class);
        $items = $repo->findAll();
        $livres = $paginator->paginate(
            $items,
            $req->query->getInt('page', 1),
            8
        );

        return $this->render('bookstore/bookstore.html.twig', [
            'controller_name' => 'BookstoreController',
            'livres' => $livres
        ]); 
    }

    #[Route('/bookstore/book/new', name: 'newBook')]
    #[Route('/bookstore/book/{id}/edit', name: 'editBook')]
    public function book(Livre $livre = null, Request $req, ObjectManager $manager)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $edit = true;
        if(!$livre){
            $livre = new Livre();
            $edit = false;
        } 

        $formBook = $this->createFormBuilder($livre)
                           ->add('isbn')
                           ->add('titre')
                           ->add('NombrePages')
                           ->add('DateParution', DateType::class, [
                            'widget' => 'single_text',
                        ])
                           ->add('note')
                           ->getForm();

        $formBook->handleRequest($req);

        if($formBook->isSubmitted() && $formBook->isValid()){
            $manager->persist($livre);
            $manager->flush();

            return $this->redirectToRoute('book_show', ['id'=> $livre->getId()]);
        }


        return $this->render('bookstore/book.html.twig', [
            'formBook' => $formBook->createView(),
            'edit' => $edit
        ]);
    }

    #[Route('/book/delete/{id}', name: 'delBook')]
    public function delete(ManagerRegistry $doctrine, $id, ObjectManager $manager) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $repo = $doctrine->getRepository(Livre::class);
        $book = $repo->find($id);
        
        $manager->remove($book);
        $manager->flush();
        
        return $this->redirectToRoute('bookstore');

    }



    #[Route('/bookstore/book/{id}', name: 'book_show')]
    public function show(ManagerRegistry $doctrine,$id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $repo = $doctrine->getRepository(Livre::class);
        $livre = $repo->find($id);
        return $this->render('bookstore/show.html.twig',[
            'livre' => $livre
        ]);
    }
}
