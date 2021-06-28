<?php

namespace App\DataFixtures\Bank;

use App\DataFixtures\AbstractFixture;
use App\Entity\Bank\Resource;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ResourceFixture extends AbstractFixture implements DependentFixtureInterface
{
    const PREFIX_REFERENCE = 'bank_resource';

    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $key => $data) {
            $entity = (new Resource())
                ->setName($data['name'])
                ->setAccount($this->getReferenceEntity(AccountFixture::PREFIX_REFERENCE, $data['account']))
                ->setAmount($data['amount']);

            $this->addReference($this->getReferencePath(self::PREFIX_REFERENCE, $key), $entity);

            $manager->persist($entity);
        }

        $manager->flush();
    }

    protected function getYamlPath(): string
    {
        return 'bank/resources.yaml';
    }

    public function getDependencies()
    {
        return [
            AccountFixture::class
        ];
    }
}
