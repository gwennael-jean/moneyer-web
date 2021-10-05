<?php

namespace App\Service\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\BlockServiceInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class MonthPickerBlock implements BlockServiceInterface
{
    public function __construct(
        private Environment $twig,
    )
    {
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $date = $blockContext->getSetting('date');

        if (!$date instanceof \DateTime) {
            throw new \InvalidArgumentException(sprintf('date must be instance of %s', \DateTime::class));
        }

        $content = $this->twig->render($blockContext->getTemplate(), [
            'previous' => (clone $date)->modify('-1 month'),
            'date' => $date,
            'next' => (clone $date)->modify('+1 month'),
        ]);
        $response->setContent($content);
        return $response;
    }

    public function load(BlockInterface $block): void
    {
        // TODO: Implement load() method.
    }

    public function getCacheKeys(BlockInterface $block): array
    {
        // TODO: Implement getCacheKeys() method.
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => 'blocks/monthpicker.html.twig',
            'date' => new \DateTime()
        ]);

        $resolver->setRequired('date');
        $resolver->setAllowedTypes('date', \DateTime::class);
    }

}
