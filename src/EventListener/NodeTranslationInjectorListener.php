<?php

namespace ArsThanea\KunstmaanSystemPagesBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Entity\Node;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class NodeTranslationInjectorListener
{
    /** @var string */
    private $defaultLocale;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @param EntityManager $em
     * @param \Twig_Environment $twig
     * @param string $defaultLocale
     */
    public function __construct(EntityManager $em, \Twig_Environment $twig, $defaultLocale)
    {
        $this->em = $em;
        $this->twig = $twig;
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $internalName = $event->getRequest()->attributes->get('_internal_name');

        if (null === $internalName) {
            return ;
        }

        $nodes = $this->em->getRepository('KunstmaanNodeBundle:Node')->getNodesByInternalName($internalName, $this->defaultLocale);
        if (0 === sizeof($nodes)) {
            return;
        }

        /** @var Node $node */
        $node = reset($nodes);

        $nodeTranslation = $node->getNodeTranslation($this->defaultLocale);

        $page = null;
        if ($nodeTranslation && $nodeTranslation->getPublicNodeVersion()) {
            $page = $nodeTranslation->getPublicNodeVersion()->getRef($this->em);
        }

        $event->getRequest()->attributes->set('nodeTranslation', $nodeTranslation);
        $event->getRequest()->attributes->set('page', $page);

        if ($page && $nodeTranslation) {
            $this->twig->addGlobal('nodetranslation', $nodeTranslation);
            $this->twig->addGlobal('page', $page);
        }
    }
}
