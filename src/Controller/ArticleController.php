<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/article', name: 'article_')]
class ArticleController extends AbstractController {

    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
    }

    #[Route('', name: 'list')]
    public function listArticle(): Response {
        $articles = $this->manager->getRepository(Article::class)->findAll();

        return $this->render('article/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/view/{slug}', name: 'view')]
    public function viewArticle(Article $article): Response {
        return $this->render('article/view.html.twig', [
            'article' => $article
        ]);
    }

    #[Route('/add', name: 'add')]
    public function addArticle(Request $request): Response {
        $article = New Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $article->setAuthor($user);

            $this->manager->persist($article);
            $this->manager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('article/form.html.twig', [
            'title' => 'Ajouter un article', //TODO traduire
            'action' => 'Ajouter',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{slug}', name: 'edit')]
    public function editArticle(Request $request, Article $article): Response {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('article/form.html.twig', [
            'action' => 'Modifier', //TODO traduire
            'form' => $form->createView(),
        ]);
    }

    #[Route('/del/{slug}', name: 'view')]
    public function delArticle(Article $article): Response {
        $this->manager->remove($article);
        $this->manager->flush();

        return $this->redirectToRoute('article_list');
    }
}
