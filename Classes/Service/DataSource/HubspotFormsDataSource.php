<?php
namespace Onedrop\Form\Hubspot\Service\DataSource;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\Service\DataSource\AbstractDataSource;
use Onedrop\Form\Hubspot\Service\FormsService;

/**
 * Class HubspotFormsDataSource
 */
class HubspotFormsDataSource extends AbstractDataSource
{
    /**
     * @var string
     */
    protected static $identifier = 'onedrop-form-hubspot-forms';

    /**
     * @Flow\Inject()
     * @var FormsService
     */
    protected $formsService = null;

    /**
     * @param NodeInterface|null $node
     * @param array $arguments
     * @throws \Neos\Cache\Exception
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData(NodeInterface $node = null, array $arguments): array
    {
        return $this->formsService->listAll();
    }
}
