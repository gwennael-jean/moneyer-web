<?php

namespace App\Service\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\BlockServiceInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class SearchbarBlock implements BlockServiceInterface
{
    public function __construct(
        private Environment $twig,
    )
    {
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $content = $this->twig->render($blockContext->getTemplate(), [
            'form' => $blockContext->getSetting('form')
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
            'template' => 'blocks/navbars/searchbar.html.twig',
        ]);

        $resolver->setRequired('form');
        $resolver->setAllowedTypes('form', FormView::class);
    }

}
