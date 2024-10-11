<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticleFormType;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'article_list')]
    public function listAction(ArticlesRepository $articleRepository)
    {
        return $this->render('article/list.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/article/create', name: 'article_create')]
    #[Route('/article/{id}/edit', name: 'article_edit')]
    public function createAction(Articles $article = null, Request $request, EntityManagerInterface $entityManager)
    {
        if (!$article) {
            $article = new Articles();
        }
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null == $article->getId()) {
                $article->setUser($this->getUser());
            }
            $entityManager->persist($article);

            if (null !== $article->getId()) {
                $this->addFlash('success', 'L\'article a été bien été modifié.');
            } else {
                $this->addFlash('success', 'L\'article a été bien été ajouté.');
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_home ');
        }

        return $this->render('article/create.html.twig', [
            'editMode' => null !== $article->getId(),
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/article/{id}', name: 'article_show')]
    public function show(Articles $article): Response
    {
        return $this->render('article/show.html.twig', [
            'controller_name' => 'ArticleController',
            'article' => $article,
            'id' => $article->getId(),
        ]);
    }

    #[Route('/article/{id}/delete', name: 'article_delete')]
    public function deletearticle(Articles $article, EntityManagerInterface $entityManager)
    { {
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été supprimée.');

            return $this->redirectToRoute('app_home');
        }
    }
}
