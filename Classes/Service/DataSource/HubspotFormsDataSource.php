<?php
namespace Onedrop\NeosHubspot\Service\DataSource;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\Service\DataSource\AbstractDataSource;
use Onedrop\NeosHubspot\Service\FormsService;

/**
 * Class HubspotFormsDataSource
 */
class HubspotFormsDataSource extends AbstractDataSource
{
    /**
     * @var string
     */
    protected static $identifier = 'onedrop-hubspot-forms';

    /**
     * @Flow\Inject()
     * @var FormsService
     */
    protected $formsService = null;

    /**
     * @param NodeInterface|null $node
     * @param array $arguments
     * @return \Neos\Flow\Persistence\QueryResultInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData(NodeInterface $node = null, array $arguments)
    {
        $forms = $this->formsService->listAll();
        return array_combine(array_column($forms, 'identifier'), $forms);
    }
}
