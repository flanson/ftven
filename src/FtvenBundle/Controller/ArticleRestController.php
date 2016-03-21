<?php
/**
 * Created by PhpStorm.
 * User: Grumly
 * Date: 21/03/2016
 * Time: 15:01
 */

namespace FtvenBundle\Controller;


use FtvenBundle\Entity\Article;
use FtvenBundle\Exception\InvalidFormException;
use FtvenBundle\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Hero controller.
 *
 * @Route("/articles")
 */
class ArticleRestController extends Controller
{

    /**
     * @Route("/", name="article_list_get")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function getArticleListAction(Request $request)
    {
        $articleList = $this->get('article.handler')->all($request);
        return $this->returnJsonResponse($articleList);
    }

    /**
     * @Route("/{slug}", name="article_get")
     * @Method("GET")
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function getArticleAction(Article $article)
    {
        return $this->returnJsonResponse($article);
    }

    /**
     * @Route("/", name="article_post")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function postArticleAction(Request $request)
    {
        try {
//            $form = new ArticleType();
//            $articleData = $request->request->get($form->getName());
//            $newArticle = $this->get('article.handler')->post($articleData);
            $newArticle = $this->get('article.handler')->post($request);
//            $newArticle = $this->get('article.handler')->post($request->request->all());
            $routeParams = [
                'slug' => $newArticle->getSlug(),
            ];
            return $this->redirectToRoute('article_detail', $routeParams, 201);
        } catch (InvalidFormException $exception) {
            //Find a better way to show error details
            return $this->returnJsonResponse($exception->getMessage(), 400);
        }


//        $article = new Article();
//        $form = $this->createForm('FtvenBundle\Form\ArticleType', $article);
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//            $article = $form->getData();
//
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($article);
//            $em->flush($article);
//
//            $routeParams = [
//                'id' => $article->getId(),
//            ];
//            return $this->redirectToRoute('article_detail', $routeParams, 201);
//        }
//        throw new InvalidFormException('Invalid submitted data');
    }

    /**
     * @Route("/{slug}", name="article_delete")
     * @Method("DELETE")
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteArticleAction(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();
        return $this->returnJsonResponse('', 204);
    }

    private function returnJsonResponse($data, $status = 200)
    {
        $json = $this->get('serializer')->serialize($data, 'json');
        $response = new Response($json, $status, array('application/json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}