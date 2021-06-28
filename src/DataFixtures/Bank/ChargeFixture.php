<?php

namespace App\DataFixtures\Bank;

use App\DataFixtures\AbstractFixture;
use App\Entity\Bank\Charge;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ChargeFixture extends AbstractFixture implements DependentFixtureInterface
{
    const PREFIX_REFERENCE = 'bank_charge';

    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $key => $data) {
            $entity = (new Charge())
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
        return 'bank/charges.yaml';
    }

    public function getDependencies()
    {
        return [
            AccountFixture::class
        ];
    }
}
