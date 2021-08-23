<?php

namespace App\Tests\Service\Transfer;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Entity\User;
use App\Repository\Bank\AccountRepository;
use App\Repository\Bank\ResourceRepository;
use App\Service\Provider\Bank\AccountProvider;
use App\Service\Provider\Bank\ResourceProvider;
use App\Service\Transfer\Model\Transfer;
use App\Service\Transfer\TransferChargeDistribution\DefaultTransferChargeDistribution;
use App\Service\Transfer\TransferChargeDistribution\FiftyFiftyTransferChargeDistribution;
use App\Service\Transfer\TransferChargeDistribution\ResourcePercentTransferChargeDistribution;
use App\Service\Transfer\TransferComputer;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Bank;
use PHPUnit\Framework\TestCase;

class TransferComputerTest extends TestCase
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
            ->addTransferChargeDistribution(new FiftyFiftyTransferChargeDistribution())
            ->addTransferChargeDistribution(new ResourcePercentTransferChargeDistribution($this->resourceProvider));
    }

    public function testOneTransfer()
    {
        $user1 = new User();

        $user2 = new User();

        $resource3000 = (new Bank\Resource())
            ->setAmount(3000);

        $resource2000 = (new Bank\Resource())
            ->setAmount(2000);

        $charge100 = (new Bank\Charge())
            ->setAmount(100)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::FIFTY_FIFTY));

        $charge200 = (new Bank\Charge())
            ->setAmount(200)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::FIFTY_FIFTY));

        $account1 = (new Bank\Account())
            ->setOwner($user1)
            ->addResource($resource3000);

        $account2 = (new Bank\Account())
            ->setOwner($user2)
            ->addResource($resource2000)
            ->addCharge($charge100)
            ->addCharge($charge200);

        $transfers = $this->transferComputer->compute(new ArrayCollection([
            $account1,
            $account2
        ]));

        $this->assertEquals(2850, $transfers->getAccountBalances()->get($account1));
        $this->assertEquals(1850, $transfers->getAccountBalances()->get($account2));
        $this->assertCount(1, $transfers->toArray());

        $account1Transfers = $transfers->filter(function (Transfer $transfer) use ($account1) {
            return $transfer->getFrom() === $account1;
        });

        $transfer = $account1Transfers->first();

        $this->assertEquals($account2, $transfer->getTo());
        $this->assertEquals(150, $transfer->getAmount());
    }

    public function testTwoTransfer()
    {
        $user1 = new User();

        $user2 = new User();

        $resource3000 = (new Bank\Resource())
            ->setAmount(3000);

        $resource2000 = (new Bank\Resource())
            ->setAmount(2000);

        $charge100 = (new Bank\Charge())
            ->setAmount(100)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::FIFTY_FIFTY));

        $charge200 = (new Bank\Charge())
            ->setAmount(200)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::FIFTY_FIFTY));

        $charge300 = (new Bank\Charge())
            ->setAmount(300)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::FIFTY_FIFTY));

        $account1 = (new Bank\Account())
            ->setOwner($user1)
            ->addResource($resource3000);

        $account2 = (new Bank\Account())
            ->setOwner($user2)
            ->addResource($resource2000);

        $account3 = (new Bank\Account())
            ->addCharge($charge100)
            ->addCharge($charge200)
            ->addCharge($charge300);

        $transfers = $this->transferComputer->compute(new ArrayCollection([
            $account1,
            $account2,
            $account3
        ]));

        $this->assertEquals(2700, $transfers->getAccountBalances()->get($account1));
        $this->assertEquals(1700, $transfers->getAccountBalances()->get($account2));

        $this->assertCount(2, $transfers->toArray());

        $account1Transfers = $transfers->filter(function (Transfer $transfer) use ($account1) {
            return $transfer->getFrom() === $account1;
        });

        $transfer = $account1Transfers->first();

        $this->assertSame($account3, $transfer->getTo());
        $this->assertEquals(300, $transfer->getAmount());

        $account2Transfers = $transfers->filter(function (Transfer $transfer) use ($account2) {
            return $transfer->getFrom() === $account2;
        });

        $transfer = $account2Transfers->first();

        $this->assertSame($account3, $transfer->getTo());
        $this->assertEquals(300, $transfer->getAmount());
    }

    public function testWithAnonymousAccount()
    {
        $user1 = new User();

        $user2 = new User();

        $resource2350 = (new Bank\Resource())
            ->setAmount(2350);

        $resource1150 = (new Bank\Resource())
            ->setAmount(1150);

        $resource302_77 = (new Bank\Resource())
            ->setAmount(302.77);

        $resource443 = (new Bank\Resource())
            ->setAmount(443);

        $charge21_92 = (new Bank\Charge())
            ->setAmount(21.92)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::RESOURCE_PERCENT));

        $charge31_98 = (new Bank\Charge())
            ->setAmount(31.98)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::FIFTY_FIFTY));

        $charge756_57 = (new Bank\Charge())
            ->setAmount(756.57)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::FIFTY_FIFTY));

        $charge260_8 = (new Bank\Charge())
            ->setAmount(260.8)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::RESOURCE_PERCENT));

        $charge95_99 = (new Bank\Charge())
            ->setAmount(95.99)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::RESOURCE_PERCENT));

        $charge174 = (new Bank\Charge())
            ->setAmount(174)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::RESOURCE_PERCENT));

        $charge112 = (new Bank\Charge())
            ->setAmount(112)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::RESOURCE_PERCENT));

        $charge13 = (new Bank\Charge())
            ->setAmount(13)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::RESOURCE_PERCENT));

        $charge62_6 = (new Bank\Charge())
            ->setAmount(62.6)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::RESOURCE_PERCENT));

        $charge39_99 = (new Bank\Charge())
            ->setAmount(39.99)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::RESOURCE_PERCENT));

        $charge6_03 = (new Bank\Charge())
            ->setAmount(6.03)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::RESOURCE_PERCENT));

        $charge20 = (new Bank\Charge())
            ->setAmount(20)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::RESOURCE_PERCENT));

        $charge850 = (new Bank\Charge())
            ->setAmount(850)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::RESOURCE_PERCENT));

        $charge100 = (new Bank\Charge())
            ->setAmount(100)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::RESOURCE_PERCENT));

        $charge200 = (new Bank\Charge())
            ->setAmount(200)
            ->setChargeDistribution((new Bank\ChargeDistribution())
                ->setUsers(new ArrayCollection([$user1, $user2]))
                ->setType(ChargeDistributionType::FIFTY_FIFTY));

        $account1 = (new Bank\Account())
            ->setOwner($user1)
            ->addResource($resource2350)
            ->addCharge($charge21_92)
        ;

        $account2 = (new Bank\Account())
            ->setOwner($user2)
            ->addResource($resource1150)
            ->addResource($resource302_77)
            ->addResource($resource443)
            ->addCharge($charge31_98)
        ;

        $account3 = (new Bank\Account())
            ->addCharge($charge756_57)
            ->addCharge($charge260_8)
            ->addCharge($charge95_99)
            ->addCharge($charge174)
            ->addCharge($charge112)
            ->addCharge($charge13)
            ->addCharge($charge62_6)
            ->addCharge($charge39_99)
            ->addCharge($charge6_03)
            ->addCharge($charge20)
            ->addCharge($charge850)
            ->addCharge($charge100)
            ->addCharge($charge200)
        ;

        $accounts = [$account1, $account2, $account3];

        $this->resourceRepositoryMock->method('findByUser')->willReturnCallback(function ($user) use ($accounts) {
            $userAccounts = array_filter($accounts, function (Bank\Account $account) use ($user) {
                return $account->getOwner() === $user;
            });

            $resources = [];

            foreach ($userAccounts as $account) {
                foreach ($account->getResources() as $resource) {
                    $resources[] = $resource;
                }
            }

            return $resources;
        });

        $transfers = $this->transferComputer->compute(new ArrayCollection($accounts));

        $this->assertEquals(883.6, round($transfers->getAccountBalances()->get($account1), 2));
        $this->assertEquals(617.29, round($transfers->getAccountBalances()->get($account2), 2));
        $this->assertEquals(0, round($transfers->getAccountBalances()->get($account3), 2));

        $this->assertCount(2, $transfers->toArray());

        $account1Transfers = $transfers->filter(function (Transfer $transfer) use ($account1) {
            return $transfer->getFrom() === $account1;
        });

        $transfer = $account1Transfers->first();

        $this->assertSame($account3, $transfer->getTo());
        $this->assertEquals(1444.48, $transfer->getAmount());

        $account2Transfers = $transfers->filter(function (Transfer $transfer) use ($account2) {
            return $transfer->getFrom() === $account2;
        });

        $transfer = $account2Transfers->first();

        $this->assertSame($account3, $transfer->getTo());
        $this->assertEquals(1246.5, $transfer->getAmount());
    }
}
