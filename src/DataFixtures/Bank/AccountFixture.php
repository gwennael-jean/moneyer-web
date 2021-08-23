<?php

namespace App\DataFixtures\Bank;

use App\DataFixtures\AbstractFixture;
use App\DataFixtures\UserFixture;
use App\Entity\Bank\Account;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AccountFixture extends AbstractFixture implements DependentFixtureInterface
{
    const PREFIX_REFERENCE = 'bank_account';

    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $key => $data) {
            $entity = (new Account())
                ->setName($data['name'])
                ->setCreatedBy($this->getReferenceEntity(UserFixture::PREFIX_REFERENCE, $data['createdBy']));

            if (isset($data['owner']) && null !== $data['owner']) {
                $entity->setOwner($this->getReferenceEntity(UserFixture::PREFIX_REFERENCE, $data['owner']));
            }

            $this->addReference($this->getReferencePath(self::PREFIX_REFERENCE, $key), $entity);

            $manager->persist($entity);
        }

        $manager->flush();
    }

    protected function getYamlPath(): string
    {
        return 'bank/accounts.yaml';
    }

    public function getDependencies()
    {
        return [
            UserFixture::class
        ];
    }

}
