<?php
/**
 * Created by PhpStorm.
 * User: Grumly
 * Date: 21/03/2016
 * Time: 14:53
 */

namespace FtvenBundle\Handler;

use Doctrine\ORM\EntityManager;
use FtvenBundle\Entity\Article;
use FtvenBundle\Exception\InvalidFormException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class ArticleHandler
{
    const ENTITY_CLASS = 'FtvenBundle:Article';
    const FORM_CLASS = 'FtvenBundle\Form\ArticleType';

    private $em;
    private $repository;
    private $formFactory;

    public function __construct(EntityManager $em, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository(self::ENTITY_CLASS);
        $this->formFactory = $formFactory;
    }

    /**
     * Get a Article.
     *
     * @param mixed $id
     *
     * @return Article
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get a list of Stories.
     *
     * @param Request $request
     * @return array
     *
     */
    public function all(Request $request)
    {
        $offset = $request->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $request->get('limit');
        $limit = null == $limit ? 5 : $limit;
        return $this->repository->findBy(array(), null, $limit, $offset);
    }

    /**
     * Create a new Article.
     *
     * @param Request $request
     * @return Article
     *
     */
    public function post(Request $request)
    {
        $article = new Article();
        return $this->processForm($article, $request);
    }

    /**
     * Processes the form.
     *
     * @param Article $article
     * @param Request $request
     * @return Article
     * @internal param array $parameters
     *
     */
    private function processForm(Article $article, Request $request)
    {
        $form = $this->formFactory->create(self::FORM_CLASS, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $this->em->persist($article);
            $this->em->flush($article);
            return $article;
        }
        throw new InvalidFormException('Invalid submitted data');
    }

}