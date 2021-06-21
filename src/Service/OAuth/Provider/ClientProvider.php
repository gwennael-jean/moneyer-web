<?php

namespace App\Service\OAuth\Provider;

use App\Entity\OAuth\Client as EntityClient;
use App\Service\OAuth\Model\Client as ModelClient;
use App\Repository\OAuth\ClientRepository;
use App\Service\OAuth\DataTransformer\ClientTransformer;
use Doctrine\Common\Collections\ArrayCollection;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientProvider implements ClientRepositoryInterface
{
    /**
     * @var ArrayCollection
     */
    private $entities;

    public function __construct(
        private ClientRepository $clientRepository,
        private ClientTransformer $clientTransformer,
    )
    {
        $this->entities = new ArrayCollection();
    }

    public function getEntity($clientIdentifier): ?EntityClient
    {
        if (!$this->entities->containsKey($clientIdentifier)) {
            $client = $this->clientRepository->findOneByIdentifier($clientIdentifier);

            if (null === $client) {
                return null;
            }

            $this->entities->set($clientIdentifier, $client);
        }

        return $this->entities->get($clientIdentifier);
    }

    /**
     * This method should return an implementation of \League\OAuth2\Server\Entities\ClientEntityInterface
     *
     * @param string $clientIdentifier
     * @return ModelClient|null
     */
    public function getClientEntity($clientIdentifier): ?ModelClient
    {
        $client = $this->getEntity($clientIdentifier);

        if (null === $client) {
            return null;
        }

        return $this->clientTransformer->transform($client);
    }

    /**
     * Cette méthode est appelée pour valider les informations d'identification d'un client.
     * Le secret client peut être fourni ou non en fonction de la demande envoyée par le client. Si le client est confidentiel (c'est-à-dire qu'il est capable de stocker un secret en toute sécurité), alors le secret doit être validé.
     * Vous pouvez utiliser le type d'octroi pour déterminer si le client est autorisé à utiliser le type d'octroi.
     * Si les informations d'identification du client sont validées, vous devez retourner true, sinon retourner false.
     *
     * @param string $clientIdentifier
     * @param string|null $clientSecret
     * @param string|null $grantType
     * @return bool
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        return in_array($grantType, ['password', 'refresh_token']);
    }

}