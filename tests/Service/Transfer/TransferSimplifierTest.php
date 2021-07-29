<?php

namespace App\Tests\Service\Transfer;

use App\Service\Transfer\Model\Transfer;
use App\Service\Transfer\TransferSimplifier;
use Doctrine\Common\Collections\ArrayCollection;

class TransferSimplifierTest extends AbstractTransferTest
{
    private TransferSimplifier $transferSimplifier;

    protected function setUp(): void
    {
        $this->transferSimplifier = new TransferSimplifier();

        $this->transferSimplifier
            ->addTransferSimplifierType(new TransferSimplifier\ReverseTransferSimplifierType())
            ->addTransferSimplifierType(new TransferSimplifier\SameRecipientAccountSimplifierType());
    }

    public function testWithTwoTransfer()
    {
        $user1 = $this->createUser('user1@mail.test');
        $user2 = $this->createUser('user2@mail.test');

        $account1 = $this->createAccount("Account 01", $user1);
        $account2 = $this->createAccount("Account 02", $user2);

        $transfer1 = (new Transfer())
            ->setUser($user1)
            ->setFrom($account1)
            ->setTo($account2)
            ->setAmount(500);

        $transfer2 = (new Transfer())
            ->setUser($user2)
            ->setFrom($account2)
            ->setTo($account1)
            ->setAmount(300);

        $transfers = $this->transferSimplifier->simplify(new ArrayCollection([$transfer1, $transfer2]));

        $this->assertCount(1, $transfers->toArray());

        /** @var Transfer $transfer */
        $transfer = $transfers->get(0);
        $this->assertEquals("user1@mail.test", $transfer->getUser()->getEmail());
        $this->assertEquals("Account 01", $transfer->getFrom()->getName());
        $this->assertEquals("Account 02", $transfer->getTo()->getName());
        $this->assertEquals(200, $transfer->getAmount());
    }
}
