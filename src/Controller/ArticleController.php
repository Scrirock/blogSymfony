<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Service\ImagesService;
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
    public function listArticle(Request $request): Response {
        $articles = $this->manager->getRepository(Article::class)->findBy(['isDraft' => 0]);

        $request->setLocale('fr');

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/view/{slug}', name: 'view')]
    public function viewArticle(Request $request, Article $article): Response {
        if (!$article->getIsDraft()) {
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
        else {
            return $this->redirectToRoute('article_list');
        }
    }

    #[Route('/add', name: 'add')]
    #[IsGranted('ROLE_AUTHOR')]
    public function addArticle(Request $request, ImagesService $imagesService): Response {
        $article = New Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $article->setAuthor($user);
            $article->setSlug(str_replace(" ", "-", strtolower($article->getTitle())));

            //encode the cover image
            $file = $form['cover']->getData();
            if ($file) {
                $ext = $file->guessExtension();
                if (!$ext) {
                    $ext = 'bin';
                }
                $article->setCover($imagesService->setCover($file));
            }
            else {
                $article->setCover($imagesService->setCover());
            }

            $this->manager->persist($article);
            $this->manager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('article/form.html.twig', [
            'action' => 'Add',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{slug}', name: 'edit')]
    #[IsGranted('ROLE_AUTHOR')]
    public function editArticle(Request $request, Article $article, ImagesService $imagesService): Response {
        $cover = $article->getCover();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //encode the cover image
            $file = $form['cover']->getData();
            if ($file) {
                $ext = $file->guessExtension();
                if (!$ext) {
                    $ext = 'bin';
                }
                $article->setCover($imagesService->setCover($file));
            }
            else {
                $article->setCover($cover);
            }

            $article->setSlug(str_replace(" ", "-", strtolower($article->getTitle())));

            $this->manager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('article/form.html.twig', [
            'action' => 'Update',
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
