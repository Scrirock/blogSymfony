<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController {

    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
    }

    #[Route('/comment/edit/{id}', name: 'edit')]
    #[IsGranted('ROLE_MODERATOR')]
    public function editComment(Request $request, Comment $comment): Response {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('comment/form.html.twig', [
            'action' => 'Update',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/comment/del/{id}', name: 'view')]
    #[IsGranted('ROLE_MODERATOR')]
    public function delComment(Comment $comment): Response {
        $this->manager->remove($comment);
        $this->manager->flush();

        return $this->redirectToRoute('article_list');
    }
}
