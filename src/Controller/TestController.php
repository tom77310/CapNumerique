<?php

namespace App\Controller;

use App\Entity\CarteTrello;
use App\Entity\ColonneTrello;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(EntityManagerInterface $em): Response
    {
        // Créer les colonnes
        $todo = new ColonneTrello();
        $todo->setTitre('A Faire');
        $todo->setPosition(1);

        $encours = new ColonneTrello();
        $encours->setTitre('En cours');
        $encours->setPosition(2);

        $termine = new ColonneTrello();
        $termine->setTitre('Termine');
        $termine->setPosition(3);

        $em->persist($todo);
        $em->persist($encours);
        $em->persist($termine);

        // Création des cartes
        $carte1 = new CarteTrello();
        $carte1->setTitre('Carte 1');
        $carte1->setPosition(1);
        $carte1->setColonne($todo);

        $carte2 = new CarteTrello();
        $carte2->setTitre('Carte 2');
        $carte2->setPosition(2);
        $carte2->setColonne($todo);

        $em->persist($carte1);
        $em->persist($carte2);

        // Sauvegarde en base
        $em->flush();

        return new Response('Données test ajoutées');
    }
}
