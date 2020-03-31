<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        // Create 3 fake categories
        for($i=1;$i<=3;$i++){
            $category = new Category();
            $category->setTitle($faker->word())
                     ->setDescription($faker->paragraph());
            $manager->persist($category);

            // Créer entre 4 et 6 articles
            for ($j=1; $j<= mt_rand(4,6); $j++){
                $article = new Article();

                $content = "<p>" . join($faker->paragraphs(5), '<p></p>') . "</p>";

                $article->setTitle($faker->sentence())
                        ->setDescription($content)
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween("-6 mounths"))
                        ->setCategory($category);

                $manager->persist($article);

                for($k=1;$k<= mt_rand(2,6);$k++){
                    $comment = new Comment();

                    $content = "<p>" . join($faker->paragraphs(2), '<p></p>') . "</p>";

                    $now = new \DateTime();
                    $interval = $now->diff($article->getCreatedAt());
                    $days = $interval->days;
                    $minimum = "-{$days} days";

                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween($minimum))
                            ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }



        $manager->flush();
    }
}
