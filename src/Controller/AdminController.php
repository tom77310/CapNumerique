<?php

namespace App\Controller;

use App\Entity\ColonneTrello;
use App\Repository\CarteTrelloRepository;
use App\Repository\ColonneTrelloRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    
    #[Route('/trello', name: 'Admin_gestionCandidat')]
    public function gestionCandidat(ColonneTrelloRepository $colonneRepo): Response
    {
        // Récupération des colonnes avec leurs cartes
        $colonnes = $colonneRepo->findBy([], ['position' => 'ASC']);

        return $this->render('admin/trello.html.twig', [
            'colonnes' => $colonnes
        ]);
    }
     #[Route('/trello/deplacement-carte', name: 'Admin_deplacementcartetrello', methods: ['POST'])]
    public function deplacementcarte(Request $request, EntityManagerInterface $em, CarteTrelloRepository $carteRepo, ColonneTrelloRepository $colRepo)
    {
        $data = json_decode($request->getContent(), true);

        // Vérifie que la colonne existe et que les positions sont fournies
        $colonne = $colRepo->find($data['colonneId']);
        if (!$colonne || !isset($data['positions'])) {
            return $this->json(['success' => false], 400);
        }

        // Met à jour toutes les cartes avec leur nouvelle position
        foreach ($data['positions'] as $pos) {
            $carte = $carteRepo->find($pos['carteId']);
            if ($carte) {
                $carte->setColonne($colonne);
                $carte->setPosition($pos['position']);
            }
        }

        $em->flush();

        return $this->json(['success' => true]);
    }
    #[Route('/trello/download/cv/{filename}', name: 'download_cv')]
    public function downloadCv(string $filename): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/assets/uploads/cv/' . $filename;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Fichier non trouvé');
        }

        return $this->file($filePath, $filename);
    }

    // Supprimer uniquement la carte
    #[Route('/trello/delete-card', name: 'delete_card', methods: ['POST'])]
    public function deleteCard(Request $request, EntityManagerInterface $em, CarteTrelloRepository $carteRepo)
    {
        $data = json_decode($request->getContent(), true);

        $carte = $carteRepo->find($data['carteId']);

        if ($carte) {
            $em->remove($carte);
            $em->flush();
        }

        return $this->json(['success' => true]);
    }

    // Supprimer Carte et utilisateur associé
    #[Route('/trello/delete-user', name: 'delete_user', methods: ['POST'])]
    public function deleteUser(Request $request, EntityManagerInterface $em, UtilisateurRepository $userRepo, CarteTrelloRepository $carteRepo)
    {
        $data = json_decode($request->getContent(), true);

        $user = $userRepo->find($data['userId']);

        if ($user) {
            // Supprime toutes les cartes de cet utilisateur
            $cartes = $carteRepo->findBy(['utilisateur' => $user]);
            foreach ($cartes as $carte) {
                $em->remove($carte);
            }

            // Supprime l'utilisateur
            $em->remove($user);
            $em->flush();
        }

        return $this->json(['success' => true]);
    }
    
    // Modifier le titre d'une colonne
    #[Route('/trello/colonne/modifier', name: 'modifier_colonne', methods: ['POST'])]
    public function modifierColonne(Request $request, EntityManagerInterface $em, ColonneTrelloRepository $colRepo)
    {
        $data = json_decode($request->getContent(), true);

        // Récupération de la colonne a modifier
        $colonne = $colRepo->find($data['colonneId']);

        if (!$colonne || empty($data['titre'])) {
            return $this->json(['success' => false, 'message' => 'Colonne introuvable ou titre vide'], 400);
        }

        // Mise a jour du titre de la colonne
        $colonne->setTitre($data['titre']);
        $em->flush();

        return $this->json(['success' => true, 'nouveauTitre' => $colonne->getTitre()]);
    }

    // Supprimer une colonne du trello 
    #[Route('/trello/delete-colonne', name: 'delete_colonne', methods: ['POST'])]
    public function deleteColonne(Request $request, EntityManagerInterface $em, ColonneTrelloRepository $colRepo, CarteTrelloRepository $carteRepo)
    {
        $data = json_decode($request->getContent(), true);

        $colonne = $colRepo->find($data['colonneId']);

        if ($colonne) {
            // Supprimer toutes les cartes liés à la colonne
            $cartes = $carteRepo->findBy(['colonne' => $colonne]);
            foreach ($cartes as $carte) {
                $em->remove($carte);
            }
            // Supprimer la colonne
            $em->remove($colonne);
            $em->flush();
        }
        return $this->json(['success' => true]);
    }

    // Ajouter une colonne
    #[Route('/trello/add-colonne', name: 'add_colonne', methods: ['POST'])]
    public function addColonne(Request $request, EntityManagerInterface $em, ColonneTrelloRepository $colRepo)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['titre'])) {
            return $this->json(['success' => false, 'message' => 'Titre vide'], 400);
        }

        // Récupérer la dernière position
        $lastColonne = $colRepo->findOneBy([], ['position' => 'DESC']);
        $position = $lastColonne ? $lastColonne->getPosition() + 1 : 1;

        $colonne = new ColonneTrello();
        $colonne->setTitre($data['titre']);
        $colonne->setPosition($position);

        $em->persist($colonne);
        $em->flush();

        return $this->json([
            'success' => true,
            'id' => $colonne->getId(),
            'titre' => $colonne->getTitre()
        ]);
    }

}