<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/footer')]
final class FooterController extends AbstractController
{
    // Page de mentions légales
    #[Route('/mentionslegales', name: 'footer_mentionslegales')]
    public function MentionsLegales(): Response
    {
        return $this->render('footer/mentionslegales.html.twig');
    }
    
    // Page Politique de confidentialité
    #[Route('/politiqueconf', name: 'footer_confidentialite')]
    public function PolitiqueConf(): Response
    {
        return $this->render('footer/confidentialite.html.twig');
    }

    // Page CGU
    #[Route('/cgu', name: 'footer_cgu')]
    public function CGU(): Response
    {
        return $this->render('footer/CGU.html.twig');
    }

    // Page Prochains évènements
    #[Route('/prochainsevenements', name: 'footer_prochainsevenements')]
    public function prochainsEvenements(): Response
    {
        return $this->render('footer/prochainsevenements.html.twig');
    }
    // Page Vidéos Témoignages
    #[Route('/videostemoignages', name: 'footer_videostemoignages')]
    public function videosTemoignages(): Response
    {
        return $this->render('footer/videostemoignages.html.twig');
    }
}
