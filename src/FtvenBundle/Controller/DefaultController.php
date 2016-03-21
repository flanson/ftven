<?php

namespace FtvenBundle\Controller;

use FtvenBundle\Entity\Article;
use FtvenBundle\Exception\InvalidFormException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="article_liste")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function articleListAction(Request $request)
    {
        $articleList = $this->get('article.handler')->all($request);
        return $this->render('article/list.html.twig', [
            'articleList' => $articleList,
        ]);
    }

    /**
     * @Route("/article/{slug}", name="article_detail")
     */
    public function detailArticleAction(Article $article)
    {
        $deleteForm = $this->createDeleteForm($article);
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();

            return $this->redirectToRoute('article_liste');
        }
        return $this->render('article/detail.html.twig', [
            'article' => $article,
            'delete_form'   => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/creer", name="article_create")
     * @Method("GET")
     */
    public function createArticleAction(Request $request)
    {
        $form = $this->createForm('FtvenBundle\Form\ArticleType');
        return $this->render('article/create.html.twig', [
            'form'     => $form->createView(),
        ]);
    }

    /**
     * @Route("/creer", name="article_create_post")
     * @Method("POST")
     */
    public function createArticlePostAction(Request $request)
    {
        try {
            $newArticle = $this->get('article.handler')->post($request);
            $routeParams = [
                'slug' => $newArticle->getSlug(),
            ];
            return $this->redirectToRoute('article_detail', $routeParams, 201);
        } catch (InvalidFormException $exception) {
            //Find a better way to show error details
            return $this->returnJsonResponse($exception->getMessage(), 400);
        }
    }

    /**
     * @Route("/article/{slug}", name="article_detail_delete")
     * @Method("DELETE")
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteArticleAction(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('article_liste');
    }

    /**
     * Creates a form to delete a Hero entity.
     *
     * @param Article $article The Hero entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Article $article)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('article_detail_delete', array('slug' => $article->getSlug())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
