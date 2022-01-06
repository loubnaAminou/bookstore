<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Genre;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class GenreController extends AbstractController
{
    #[Route('/genre', name: 'genre')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $repo = $doctrine->getRepository(Genre::class);

        $genres = $repo->findAll();

        return $this->render('genre/index.html.twig', [
            'controller_name' => 'GenreController',
            'genres' => $genres
        ]);
    }

    #[Route('/genre/delete/{id}', name: 'delGenre')]
    public function delete(ManagerRegistry $doctrine, $id, ObjectManager $manager) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $repo = $doctrine->getRepository(Genre::class);
        $genre = $repo->find($id);
        
        $manager->remove($genre);
        $manager->flush();
        
        return $this->redirectToRoute('genre');

    }


    #[Route('/genre/new', name: 'newGenre')]
    public function Genre(Request $req, ObjectManager $manager)
    { 
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $Genre = new Genre();
        $formGenre = $this->createFormBuilder($Genre)
                           ->add('nom')
                           ->getForm();

        $formGenre->handleRequest($req);

        if($formGenre->isSubmitted() && $formGenre->isValid()){
            $manager->persist($Genre);
            $manager->flush();

            return $this->redirectToRoute('genre');
        }


        return $this->render('genre/Genre.html.twig', [
            'formGenre' => $formGenre->createView()
        ]);
    }
}
