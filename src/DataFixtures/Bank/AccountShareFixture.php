<?php

namespace App\DataFixtures\Bank;

use App\DataFixtures\AbstractFixture;
use App\DataFixtures\UserFixture;
use App\DBAL\Types\Bank\AccountShareType;
use App\Entity\Bank\Account;
use App\Entity\Bank\AccountShare;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AccountShareFixture extends AbstractFixture implements DependentFixtureInterface
{
    const PREFIX_REFERENCE = 'bank_account_share';

    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $key => $data) {
            $entity = (new AccountShare())
                ->setAccount($this->getReferenceEntity(AccountFixture::PREFIX_REFERENCE, $data['account']))
                ->setUser($this->getReferenceEntity(UserFixture::PREFIX_REFERENCE, $data['user']))
                ->setType($data['type']);

            $this->addReference($this->getReferencePath(self::PREFIX_REFERENCE, $key), $entity);

            $manager->persist($entity);
        }

        $manager->flush();
    }

    protected function getYamlPath(): string
    {
        return 'bank/account_shares.yaml';
    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
            AccountFixture::class,
        ];
    }

}
