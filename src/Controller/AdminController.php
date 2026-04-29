<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/utilisateur/admin')]
final class AdminController extends AbstractController
{
    #[Route('/espaceAdmin', name: 'Admin_espaceAdmin')]
    public function espaceAdmin(): Response
    {
        return $this->render('admin/espaceAdmin.html.twig');
    }
    
    

}