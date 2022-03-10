<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'article_')]
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
    public function viewArticle(Request $request, Article $article): Response {
        $comment = New Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $comment->setAuthor($user);
            $comment->setArticle($article);

            $this->manager->persist($comment);
            $this->manager->flush();

            return $this->redirectToRoute('article_view', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/view.html.twig', [
            'article' => $article,
            'addComment' => $form->createView(),
        ]);
    }

    #[Route('/add', name: 'add')]
    #[IsGranted('ROLE_AUTHOR')]
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
            'action' => 'Ajouter', //TODO traduire
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{slug}', name: 'edit')]
    #[IsGranted('ROLE_AUTHOR')]
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

    #[Route('/del/{slug}', name: 'del')]
    #[IsGranted('ROLE_AUTHOR')]
    public function delArticle(Article $article): Response {
        $this->manager->remove($article);
        $this->manager->flush();

        return $this->redirectToRoute('article_list');
    }
}
