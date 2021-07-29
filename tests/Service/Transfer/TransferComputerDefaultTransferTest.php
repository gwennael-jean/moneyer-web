<?php

namespace App\Tests\Service\Transfer;

use App\Service\Transfer\Model\Transfer;
use Doctrine\Common\Collections\ArrayCollection;

class TransferComputerDefaultTransferTest extends AbstractTransferTest
{
    public function testOneTransfer(): void
    {
        $user1 = $this->createUser('user1@mail.test');

        $accounts = new ArrayCollection();

        $accounts->add($this->createAccount("Account 01", $user1, [
            $this->createResource(2000),
        ]));

        $accounts->add($this->createAccount("Account 02", $user1, [
            $this->createCharge(200),
            $this->createCharge(200),
        ]));

        $transfers = $this->transferComputer->computeByUser($user1, $accounts);

        $this->assertCount(1, $transfers->toArray());

        /** @var Transfer $transfer */
        $transfer = $transfers->get(0);
        $this->assertEquals("user1@mail.test", $transfer->getUser()->getEmail());
        $this->assertEquals("Account 01", $transfer->getFrom()->getName());
        $this->assertEquals("Account 02", $transfer->getTo()->getName());
        $this->assertEquals(400, $transfer->getAmount());
    }

    public function testMultiTransfer(): void
    {
        $user1 = $this->createUser('user1@mail.test');

        $accounts = new ArrayCollection();

        $accounts->add($this->createAccount("Account 01", $user1, [
            $this->createResource(2000),
        ]));

        $accounts->add($this->createAccount("Account 02", $user1, [
            $this->createResource(500),
        ]));

        $accounts->add($this->createAccount("Account 03", $user1, [
            $this->createCharge(1900),
            $this->createCharge(200),
        ]));

        $transfers = $this->transferComputer->computeByUser($user1, $accounts);

        $this->assertCount(2, $transfers->toArray());

        $transfer = $transfers->get(0);
        $this->assertEquals("user1@mail.test", $transfer->getUser()->getEmail());
        $this->assertEquals("Account 01", $transfer->getFrom()->getName());
        $this->assertEquals("Account 03", $transfer->getTo()->getName());
        $this->assertEquals(1900, $transfer->getAmount());

        $transfer = $transfers->get(1);
        $this->assertEquals("user1@mail.test", $transfer->getUser()->getEmail());
        $this->assertEquals("Account 02", $transfer->getFrom()->getName());
        $this->assertEquals("Account 03", $transfer->getTo()->getName());
        $this->assertEquals(200, $transfer->getAmount());
    }
}
