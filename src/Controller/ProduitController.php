<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AddEditFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function listproduit(ProduitRepository $produitRepository)
    {   
        
        $produitsDB = $produitRepository->findAll();

        return $this->render('produit/index.html.twig', [
            'produits'=> $produitsDB,

           
        ]);
    }
    #[Route('/produit/filter', name: 'app_produit_filter')]
public function filterProducts(Request $request, ProduitRepository $productRepository): Response
{
    $minPrice = $request->query->get('minPrice');
    $maxPrice = $request->query->get('maxPrice');

    // Fetch products within the specified price range
    $products = $productRepository->findByPriceRange($minPrice, $maxPrice);

    return $this->render('produit/index.html.twig', [
        'produits' => $products,
    ]);
}
    #[Route('/produit/ajout', name: 'app_produit_ajout')]
    public function addAuthorform(ManagerRegistry $mr,ProduitRepository $repo,Request $req): Response
    {
        $produit = new Produit();
        $forum= $this->createForm(ProduitType::class,$produit);
        $forum->handleRequest($req);
        if($forum->isSubmitted()&&$forum->isValid())
        {
            $em=$mr->getManager();
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('app_produit');
        }
      
        return $this->render('produit/ajout.html.twig', ['form'=>$forum->createView(),]);
    }
    #[Route('/produit/modier/{id}', name: 'app_produit_modifier')]
    public function editAuthor($id, Request $request, ProduitRepository $produitRepository, EntityManagerInterface $em){
        $produit= $produitRepository->find($id);
        $form= $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            //$em->persist($author);
            $em->flush();
            return $this->redirectToRoute('app_produit');
        }
        return $this->render('produit/ajout.html.twig',[
            'title' => 'Update Produit',
            'form' => $form
        ]);
    }
    #[Route('/produit/supprimer/{id}', name: 'app_produit_supprimer')]
    public function deleteAuthor($id, ProduitRepository $produitRepository, EntityManagerInterface $em){
        $produit= $produitRepository->find($id);
        $em->remove($produit);
        $em->flush();
        return $this->redirectToRoute('app_produit');
        //return new Response('Author deleted');

    }
    #[Route('/produit/details/{id}', name: 'app_produit_details')]
    public function produitDetails($id, ProduitRepository $produitRepository){
        $produit = $produitRepository->find($id);


        return $this->render('produit/details.html.twig',[
            'produit' => $produit
        ]);
    }

}
