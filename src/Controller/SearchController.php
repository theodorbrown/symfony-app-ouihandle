<?php

namespace App\Controller;

use App\Entity\Person;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[
        Route('/search', name: 'app_search'),
    ]
    public function search(Request $req, ManagerRegistry $mr){
        $form = $this->createFormBuilder(null)
            ->add('Filter', TextType::class)
            ->add('Search', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-light'
                ]
            ])
            ->getForm();

        $form->handleRequest($req);


        if($form->isSubmitted() && $form->isValid()){
            $keyword = $form->get('Filter')->getData();
            $repo = $mr->getRepository(Person::class);
            $persons = $repo->search($keyword);

            return $this->render('person/search.html.twig', [
                'persons' => $persons,
                'keyword' => $keyword
            ]);
        }

        return $this->render('/search/search-form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
