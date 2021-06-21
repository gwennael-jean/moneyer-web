<?php

namespace App\DataFixtures\OAuth;

use App\DataFixtures\AbstractFixture;
use App\Entity\OAuth\Client;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

class ClientFixture extends AbstractFixture
{
    const PREFIX_REFERENCE = 'oauth_client';

    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $key => $data) {
            $entity = (new Client())
                ->setIdentifier($data['identifier'])
                ->setName($data['name'])
                ->setRedirectUri($data['redirect_uri'])
                ->setIsActif($data['is_actif'] ?? true)
                ->setIsConfidential($data['is_confidential'] ?? false);

            $this->addReference($this->getReferencePath(self::PREFIX_REFERENCE, $key), $entity);

            $manager->persist($entity);
        }

        $manager->flush();
    }

    protected function getYamlPath(): string
    {
        return 'oauth/clients.yaml';
    }
}
