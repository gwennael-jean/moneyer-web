<?php

namespace App\DataFixtures\Bank;

use App\DataFixtures\AbstractFixture;
use App\Entity\Bank\Resource;
use App\Entity\Bank\ResourceGroup;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ResourceFixture extends AbstractFixture implements DependentFixtureInterface
{
    const PREFIX_REFERENCE = 'bank_resource';

    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $key => $data) {
            $resourceGroup = (new ResourceGroup())
                ->setName($data['name'])
                ->setAccount($this->getReferenceEntity(AccountFixture::PREFIX_REFERENCE, $data['account']))
                ->setAmount($data['amount']);

            if (isset($data['resources'])) {
                foreach ($data['resources'] as $resourceData) {
                    $resource = (new Resource())
                        ->setName($resourceGroup->getName())
                        ->setAmount($resourceGroup->getAmount())
                        ->setMonth($this->getDateTime($resourceData['month']));

                    $resourceGroup->addResource($resource);
                }
            }

            $this->addReference($this->getReferencePath(self::PREFIX_REFERENCE, $key), $resourceGroup);

            $manager->persist($resourceGroup);
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
