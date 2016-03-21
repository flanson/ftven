<?php

namespace Tests\FtvenBundle\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FtvenBundle\Entity\Article;


class LoadArticleData extends AbstractFixture implements OrderedFixtureInterface
{
    static public $stories = array();

    public function load(ObjectManager $manager)
    {
        $article1 = new Article();
        $article1->setTitle('My test Article');
        $article1->setBody('my body 1');
        $article1->setCreatedBy('fixture 1');
        $article1->setLeadingText('accroche 1');

        $article2 = new Article();
        $article2->setTitle('My test Article 2');
        $article2->setBody('my body 2');
        $article2->setCreatedBy('fixture 2');
        $article2->setLeadingText('accroche 2');

        $manager->persist($article1);
        $manager->persist($article2);
        $manager->flush();

        $this->addReference('article-1',$article1);
        $this->addReference('article-2',$article2);

        self::$stories = array($article1, $article2);
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 1;
    }
}