<?php

namespace App\Controller;

use App\Entity\CarteTrello;
use App\Entity\ColonneTrello;
use App\Repository\CarteTrelloRepository;
use App\Repository\ColonneTrelloRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(EntityManagerInterface $em, ColonneTrelloRepository $colRepo): Response
    {
        // Securité - Verifier si les colonnes existent déjà, permet d'eviter le doublons
        if ($colRepo->count([]) > 0) {
            return new Response('Colonnes déjà existantes');
        }
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

        // Sauvegarde en base
        $em->flush();

        // return new Response('Données test ajoutées');
        return new Response('Colonnes créées');
    }
   #[Route('/test-candidat', name: 'candidat_test')]
    public function carteCandidat(EntityManagerInterface $em, UtilisateurRepository $userRepo, ColonneTrelloRepository $colRepo, CarteTrelloRepository $carteRepo): Response 
    {

        // 🔹 Récupérer la colonne "A Faire"
        $colonne = $colRepo->findOneBy(['titre' => 'A Faire']);

        if (!$colonne) {
            return new Response('Colonne "A Faire" introuvable');
        }

        // 🔹 Récupérer les candidats
        $candidats = $userRepo->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_CANDIDAT%')
            ->getQuery()
            ->getResult();

        // 🔹 Récupérer la dernière position
        $dernieresCartes = $carteRepo->findBy(
            ['colonne' => $colonne],
            ['position' => 'DESC'],
            1
        );

        $position = $dernieresCartes ? $dernieresCartes[0]->getPosition() + 1 : 1;

        foreach ($candidats as $candidat) {

            // ❗ Anti doublon (très important)
            $existe = $carteRepo->findOneBy(['utilisateur' => $candidat]);
            if ($existe) {
                continue;
            }

            $carte = new CarteTrello();
            $carte->setTitre('Carte'); // ✅ titre par défaut
            $carte->setUtilisateur($candidat); // ✅ lien utilisateur
            $carte->setColonne($colonne); // ✅ colonne "A Faire"
            $carte->setPosition($position);

            $em->persist($carte);
            $position++;
        }

        $em->flush();

        // return new Response('Cartes candidats créées');
         return $this->render('admin/trello.html.twig', [
            'colonnes' => $colonne,
         ]);
    }
}