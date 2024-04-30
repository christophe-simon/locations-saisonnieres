<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * Enables to display all comments
     *
     * @param CommentRepository $repo
     * @return Response
     */
    #[Route('/admin/comments', name: 'app_admin_comments_index')]
    public function index(CommentRepository $repo): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $repo->findAll(),
        ]);
    }

    /**
     * Enables to update a comment
     *
     * @param Comment $comment
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/comment/update/{id}', name: 'app_admin_comment_update')]
    public function update(Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire <strong>{$comment->getId()}</strong> a bien été modifié"
            );
        }

        return $this->render('admin/comment/update.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    /**
     * Enables to delete a comment
     *
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/comment/delete/{id}', name: 'app_admin_comment_delete')]
    public function delete(Comment $comment, EntityManagerInterface $manager): Response
    {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le commentaire de <strong>{$comment->getWriter()->getFullName()}</strong> a bien été supprimé"
        );

        return $this->redirectToRoute('app_admin_comments_index');
    }
}
