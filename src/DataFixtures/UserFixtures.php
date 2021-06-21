<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends AbstractFixture
{
    const PREFIX_REFERENCE = 'user';

    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $key => $data) {
            $entity = (new User())
                ->setEmail($data['email'])
                ->setPlainPassword($data['password']);

            $this->addReference($this->getReferencePath(self::PREFIX_REFERENCE, $key), $entity);

            $manager->persist($entity);
        }

        $manager->flush();
    }

    protected function getYamlPath(): string
    {
        return 'users.yaml';
    }
}
