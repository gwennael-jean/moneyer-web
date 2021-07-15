<?php

namespace App\Tests\Service\Transfer;

use App\DBAL\Types\Bank\ChargeDistributionType;
use App\Service\Transfer\Model\Transfer;

class TransferComputerFiftyFiftyChargeDistributionTest extends AbstractChargeDistributionTest
{
    public function testOneCharge(): void
    {
        $user1 = $this->createUser('user1@mail.test');
        $user2 = $this->createUser('user2@mail.test');

        $account1 = $this->createAccount("Account 01", $user1, [
            $this->createResource(2200),
        ]);

        $account2 = $this->createAccount("Account 02", $user2, [
            $this->createResource(1800),
            $this->createCharge(1000, ChargeDistributionType::FIFTY_FIFTY, [
                $user1
            ]),
        ]);

        $this->accountRepositoryMock->method('findByUser')->willReturn([$account1, $account2]);

        $transfers = $this->transferComputer->computeByUser($user1, $this->accountProvider->getByUser($user1));

        $this->assertCount(1, $transfers->toArray());

        /** @var Transfer $transfer */
        $transfer = $transfers->get(0);
        $this->assertEquals("user1@mail.test", $transfer->getUser()->getEmail());
        $this->assertEquals("Account 01", $transfer->getFrom()->getName());
        $this->assertEquals("Account 02", $transfer->getTo()->getName());
        $this->assertEquals(500, $transfer->getAmount());
    }

    public function testMultiCharge(): void
    {
        $user1 = $this->createUser('user1@mail.test');
        $user2 = $this->createUser('user2@mail.test');

        $account1 = $this->createAccount("Account 01", $user1, [
            $this->createResource(2200),
            $this->createCharge(500),
            $this->createCharge(100, ChargeDistributionType::FIFTY_FIFTY, [
                $user2
            ]),
        ]);

        $account2 = $this->createAccount("Account 02", $user2, [
            $this->createResource(1800),
            $this->createCharge(200, ChargeDistributionType::FIFTY_FIFTY, [
                $user1
            ]),
        ]);

        $this->accountRepositoryMock->method('findByUser')->willReturn([$account1, $account2]);

        $transfers = $this->transferComputer->computeByUser($user1, $this->accountProvider->getByUser($user1));

        $this->assertCount(2, $transfers->toArray());

        /** @var Transfer $transfer */
        $transfer = $transfers->get(0);
        $this->assertEquals("user2@mail.test", $transfer->getUser()->getEmail());
        $this->assertEquals("Account 02", $transfer->getFrom()->getName());
        $this->assertEquals("Account 01", $transfer->getTo()->getName());
        $this->assertEquals(50, $transfer->getAmount());

        /** @var Transfer $transfer */
        $transfer = $transfers->get(1);
        $this->assertEquals("user1@mail.test", $transfer->getUser()->getEmail());
        $this->assertEquals("Account 01", $transfer->getFrom()->getName());
        $this->assertEquals("Account 02", $transfer->getTo()->getName());
        $this->assertEquals(100, $transfer->getAmount());
    }

}
