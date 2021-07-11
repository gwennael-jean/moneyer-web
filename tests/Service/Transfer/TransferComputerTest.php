<?php

namespace App\Tests\Service\Transfer;

use App\Entity\Bank;
use App\Entity\User;
use App\Service\Transfer\Model\Transfer;
use App\Service\Transfer\TransferChargeDistribution\DefaultTransferChargeDistribution;
use App\Service\Transfer\TransferComputer;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class TransferComputerTest extends TestCase
{
    private TransferComputer $transferComputer;

    protected function setUp(): void
    {
        $this->transferComputer = new TransferComputer();

        $this->transferComputer->addTransferChargeDistribution(new DefaultTransferChargeDistribution());
    }

    public function testClassicOneTransfer(): void
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

    public function testClassicTwoTransfer(): void
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

    private function createUser(string $email): User
    {
        return (new User())
            ->setEmail($email);
    }

    private function createAccount(string $name, User $user, array $data): Bank\Account
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

    private function createCharge(float $amount): Bank\Charge
    {
        return (new Bank\Charge())
            ->setAmount($amount);
    }

    private function createResource(float $amount): Bank\Resource
    {
        return (new Bank\Resource())
            ->setAmount($amount);
    }
}
