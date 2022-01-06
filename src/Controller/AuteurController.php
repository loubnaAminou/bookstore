<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Auteur;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;


class AuteurController extends AbstractController
{            
    #[Route('/auteur', name: 'auteur')]
    public function index(ManagerRegistry $doctrine, Request $req, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $repo = $doctrine->getRepository(Auteur::class);

        $items = $repo->findAll();
        $auteurs = $paginator->paginate(
            $items,
            $req->query->getInt('page', 1),
            8
        );


        return $this->render('auteur/index.html.twig', [
            'controller_name' => 'AuteurController',
            'auteurs' => $auteurs 
        ]);
    }

    #[Route('/auteur/new', name: 'newAuteur')]
    #[Route('/auteur/{id}/edit', name: 'editAuteur')]
    public function Auteur(Auteur $auteur = null, Request $req, ObjectManager $manager)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $edit = true;
        if(!$auteur){
            $auteur = new Auteur();
            $edit = false;
        } 

        $formAuteur = $this->createFormBuilder($auteur)
                           ->add('NomPrenom')
                           ->add('Sexe')
                           ->add('DateNaissance', DateType::class, [
                            'widget' => 'single_text',
                        ])
                           ->add('Nationalite')
                           ->getForm();

        $formAuteur->handleRequest($req);

        if($formAuteur->isSubmitted() && $formAuteur->isValid()){
            $manager->persist($auteur);
            $manager->flush();

            return $this->redirectToRoute('show_aut', ['id'=> $auteur->getId()]);
        }


        return $this->render('auteur/Auteur.html.twig', [
            'formAuteur' => $formAuteur->createView(),
            'edit' => $edit
        ]);
    }

    #[Route('/auteur/delete/{id}', name: 'delAuteur')]
    public function delete(ManagerRegistry $doctrine, $id, ObjectManager $manager) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $repo = $doctrine->getRepository(Auteur::class);
        $auteur = $repo->find($id);
        
        $manager->remove($auteur);
        $manager->flush();
        
        return $this->redirectToRoute('auteur');

    }

    #[Route('/auteur/{id}', name: 'show_aut')]
    public function show(ManagerRegistry $doctrine,$id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $repo = $doctrine->getRepository(Auteur::class);
        $auteur = $repo->find($id);

        return $this->render('auteur/show.html.twig',[
            'auteur' => $auteur
        ]);
    }
}
