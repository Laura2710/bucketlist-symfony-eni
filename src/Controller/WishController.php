<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use App\Services\Censurator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/wish', name: 'wish_')]
class WishController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(WishRepository $wishRepository): Response
    {
        $wishes = $wishRepository->getWishesByDate();
        return $this->render('wish/list.html.twig', ['wishes' => $wishes]);
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function detail(WishRepository $wishRepository, int $id): Response
    {
        $wish = $wishRepository->find($id);
        return $this->render('wish/detail.html.twig', ['wish' => $wish]);
    }

    #[Route('/create', name: 'create')]
    public function create(
        EntityManagerInterface $entityManager,
        Request                $request,
        Censurator             $censurator
    ): Response
    {
        $wish = new Wish();
        $wish->setAuthor($this->getUser()->getUserIdentifier());
        $wishForm = $this->createForm(WishType::class, $wish);

        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {

            $this->censor($wish, $censurator);

            $wish->setPublished(true);
            $wish->setDateCreated(new DateTime());

            $entityManager->persist($wish);
            $entityManager->flush();

            $this->addFlash('success', 'Wish created!');

            return $this->redirectToRoute('wish_list');
        }

        return $this->render('wish/create.html.twig', ['wishForm' => $wishForm->createView()]);
    }

    private function censor(Wish $wish, Censurator $censurator): void
    {
        $wish->setTitle($censurator->censor($wish->getTitle()));
        $wish->setDescription($censurator->censor($wish->getDescription()));
        $wish->setAuthor($censurator->censor($wish->getAuthor()));
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(EntityManagerInterface $entityManager, Wish $wish)
    {

        $this->denyAccessUnlessGranted('POST_DELETE', $wish);

        if ($wish) {
            $entityManager->remove($wish);
            $entityManager->flush();
        }


        return $this->redirectToRoute('wish_list');
    }

    #[Route('/update/{id}', name: 'update')]
    public function update(Wish $wish, Request $request, EntityManagerInterface $entityManager, Censurator $censurator)
    {

        $this->denyAccessUnlessGranted('POST_EDIT', $wish);

        // Création du formulaire prérempli
        $wishForm = $this->createForm(WishType::class, $wish);

        // Lier les données de la requête au formulaire
        $wishForm->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($wishForm->isSubmitted() && $wishForm->isValid()) {

            $this->censor($wish, $censurator);

            $entityManager->persist($wish);
            $entityManager->flush();
            $this->addFlash('success', 'Wish updated!');
            return $this->redirectToRoute('wish_detail', ['id' => $wish->getId()]);
        }
        return $this->render('wish/update.html.twig', ['wishForm' => $wishForm->createView()]);
    }
}
