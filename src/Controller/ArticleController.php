<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/article', name: 'article_')]
class ArticleController extends AbstractController {

    #[Route('', name: 'list')]
    public function articleList(): Response {
        return $this->render('article/index.html.twig');
    }
}
