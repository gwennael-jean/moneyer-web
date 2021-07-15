<?php

namespace App\DataFixtures\Bank;

use App\DataFixtures\AbstractFixture;
use App\DataFixtures\UserFixture;
use App\Entity\Bank\Charge;
use App\Entity\Bank\ChargeDistribution;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ChargeDistributionFixture extends AbstractFixture implements DependentFixtureInterface
{
    const PREFIX_REFERENCE = 'bank_charge_distribution';

    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $key => $data) {
            $entity = (new ChargeDistribution())
                ->setCharge($this->getReferenceEntity(ChargeFixture::PREFIX_REFERENCE, $data['charge']))
                ->setType($data['type']);

            foreach ($data['users'] as $user) {
                $entity
                    ->addUser($this->getReferenceEntity(UserFixture::PREFIX_REFERENCE, $user));
            }

            $this->addReference($this->getReferencePath(self::PREFIX_REFERENCE, $key), $entity);

            $manager->persist($entity);
        }

        $manager->flush();
    }

    protected function getYamlPath(): string
    {
        return 'bank/charge_distributions.yaml';
    }

    public function getDependencies()
    {
        return [
            ChargeFixture::class,
            UserFixture::class,
        ];
    }
}