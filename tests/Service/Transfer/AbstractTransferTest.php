<?php

namespace App\Tests\Service\Transfer;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Entity\Bank;
use App\Entity\User;
use App\Repository\Bank\AccountRepository;
use App\Repository\Bank\ResourceRepository;
use App\Service\Provider\Bank\AccountProvider;
use App\Service\Provider\Bank\ResourceProvider;
use App\Service\Transfer\TransferChargeDistribution\DefaultTransferChargeDistribution;
use App\Service\Transfer\TransferChargeDistribution\FiftyFiftyTransferChargeDistribution;
use App\Service\Transfer\TransferChargeDistribution\ResourcePercentTransferChargeDistribution;
use App\Service\Transfer\TransferComputer;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

abstract class AbstractTransferTest extends TestCase
{
    protected AccountRepository $accountRepositoryMock;

    protected ResourceRepository $resourceRepositoryMock;

    protected AccountProvider $accountProvider;

    protected ResourceProvider $resourceProvider;

    protected TransferComputer $transferComputer;

    protected function setUp(): void
    {
        $this->accountRepositoryMock = $this->getMockBuilder(AccountRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourceRepositoryMock = $this->getMockBuilder(ResourceRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->accountProvider = new AccountProvider($this->accountRepositoryMock);
        $this->resourceProvider = new ResourceProvider($this->resourceRepositoryMock);

        $this->transferComputer = (new TransferComputer())
            ->addTransferChargeDistribution(new DefaultTransferChargeDistribution())
            ->addTransferChargeDistribution(new FiftyFiftyTransferChargeDistribution($this->accountProvider))
            ->addTransferChargeDistribution(new ResourcePercentTransferChargeDistribution($this->accountProvider, $this->resourceProvider));
    }

    protected function createUser(string $email): User
    {
        return (new User())
            ->setEmail($email);
    }

    protected function createAccount(string $name, User $user, array $data = []): Bank\Account
    {
        $account = (new Bank\Account())
            ->setName($name)
            ->setOwner($user);

        foreach ($data as $datum) {
            $datum instanceof Bank\Resource
                ? $account->addResource($datum)
                : $account->addCharge($datum);
        }

        return $account;
    }

    protected function createCharge(float $amount, $distributionType = ChargeDistributionType::VIEW, array $users = []): Bank\Charge
    {
        $charge = (new Bank\Charge())
            ->setAmount($amount);

        if (!empty($users)) {
            $charge->setChargeDistribution((new Bank\ChargeDistribution())
                ->setType($distributionType)
                ->setUsers(new ArrayCollection($users)));
        }

        return $charge;
    }

    protected function createResource(float $amount): Bank\Resource
    {
        return (new Bank\Resource())
            ->setAmount($amount);
    }
}
