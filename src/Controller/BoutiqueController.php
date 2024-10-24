<?php

namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Boutique;
use App\Form\BoutiqueType;
use App\Form\AddEditFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\BoutiqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BoutiqueController extends AbstractController
{
    #[Route('/boutique', name: 'app_boutique')]
    public function listboutique(BoutiqueRepository $boutiqueRepository)
    {   
        
        $boutiquesDB = $boutiqueRepository->findAll();

        return $this->render('boutique/index.html.twig', [
            'boutiques'=> $boutiquesDB,

           
        ]);
    }


    

    #[Route('/boutique/ajout', name: 'app_boutique_ajout')]
    public function addAuthorform(ManagerRegistry $mr,BoutiqueRepository $repo,Request $req): Response
    {
        $boutique = new Boutique();
        $forum= $this->createForm(BoutiqueType::class,$boutique);
        $forum->handleRequest($req);
        if($forum->isSubmitted()&&$forum->isValid())
        {
            $em=$mr->getManager();
            $em->persist($boutique);
            $em->flush();
            return $this->redirectToRoute('app_boutique');
        }
      
        return $this->render('boutique/ajout.html.twig', ['form'=>$forum->createView(),]);
    }

    #[Route('/boutique/modier/{id}', name: 'app_boutique_modifier')]
    public function editAuthor($id, Request $request, BoutiqueRepository $boutiqueRepository, EntityManagerInterface $em){
        $boutique= $boutiqueRepository->find($id);
        $form= $this->createForm(BoutiqueType::class, $boutique);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            //$em->persist($author);
            $em->flush();
            return $this->redirectToRoute('app_boutique');
        }
        return $this->render('boutique/ajout.html.twig',[
            'title' => 'Update Boutique',
            'form' => $form
        ]);
    }
    #[Route('/boutique/details/{id}', name: 'app_boutique_details')]
    public function boutiqueDetails($id, BoutiqueRepository $boutiqueRepository){
        $boutique = $boutiqueRepository->find($id);


        return $this->render('boutique/details.html.twig',[
            'boutique' => $boutique
        ]);
    }

    #[Route('/boutique/supprimer/{id}', name: 'app_boutique_supprimer')]
    public function deleteAuthor($id, BoutiqueRepository $boutiqueRepository, EntityManagerInterface $em){
        $boutique= $boutiqueRepository->find($id);
        $em->remove($boutique);
        $em->flush();
        return $this->redirectToRoute('app_boutique');
        //return new Response('Author deleted');

    }
}
