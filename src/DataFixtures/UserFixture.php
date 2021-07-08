<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends AbstractFixture
{
    const PREFIX_REFERENCE = 'user';

    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $key => $data) {
            $entity = (new User())
                ->setFirstname($data['firstname'])
                ->setLastname($data['lastname'])
                ->setEmail($data['email'])
                ->setIsAdmin($data['is_admin'] ?? false)
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
