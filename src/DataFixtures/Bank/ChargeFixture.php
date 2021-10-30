<?php

namespace App\DataFixtures\Bank;

use App\DataFixtures\AbstractFixture;
use App\DataFixtures\UserFixture;
use App\Entity\Bank\Charge;
use App\Entity\Bank\ChargeDistribution;
use App\Entity\Bank\ChargeGroup;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ChargeFixture extends AbstractFixture implements DependentFixtureInterface
{
    const PREFIX_REFERENCE = 'bank_charge';

    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $key => $data) {
            $chargeGroup = (new ChargeGroup())
                ->setName($data['name'])
                ->setAccount($this->getReferenceEntity(AccountFixture::PREFIX_REFERENCE, $data['account']))
                ->setAmount($data['amount']);

            if (isset($data['chargeDistribution'])) {
                foreach ($data['chargeDistribution'] as $chargeDistributionData) {
                    $chargeDistribution = (new ChargeDistribution())
                        ->setType($chargeDistributionData['type']);

                    foreach ($chargeDistributionData['users'] as $user) {
                        $chargeDistribution
                            ->addUser($this->getReferenceEntity(UserFixture::PREFIX_REFERENCE, $user));
                    }

                    $chargeGroup->setChargeDistribution($chargeDistribution);
                }
            }

            if (isset($data['charges'])) {
                foreach ($data['charges'] as $chargeData) {
                    $charge = (new Charge())
                        ->setName($chargeGroup->getName())
                        ->setChargeDistribution(clone $chargeGroup->getChargeDistribution())
                        ->setAmount($chargeGroup->getAmount())
                        ->setMonth($this->getDateTime($chargeData['month']));

                    $chargeGroup->addCharge($charge);
                }
            }

            $this->addReference($this->getReferencePath(self::PREFIX_REFERENCE, $key), $chargeGroup);

            $manager->persist($chargeGroup);
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
            AccountFixture::class,
            UserFixture::class,
        ];
    }
}
