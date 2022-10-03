<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/')]
class HomeController extends AbstractController
{
    #[Route('', name: 'app_home')]
    public function index(Request $req): Response
    {
        $session = $req->getSession();
        if ($session->has('nbVisites')) {
            $nb = $session->get('nbVisites');
            $nb = $nb + 1;

        } else {
            $nb = 1;
        }
        $session->set('nbVisites', $nb);

        $user = $this->getUser();
        if($user){
            $email = $user->getUserIdentifier();
            $this->addFlash('info', "You are connected, your email is $email.");
        } else {
            $this->addFlash('info', "You are not connected.");
        }
        return $this->render('base.html.twig', [
            'nb' => $nb
        ]);
    }
}
