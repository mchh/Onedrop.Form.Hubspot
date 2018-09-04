<?php
namespace Onedrop\Neos\Hubspot\Command;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Onedrop\Neos\Hubspot\Service\FormsService;

/**
 * Class FormsCommandController
 */
class FormsCommandController extends CommandController
{
    /**
     * @Flow\Inject()
     * @var FormsService
     */
    protected $formsService = null;

    /**
     *
     */
    public function listAllCommand()
    {
        $this->formsService->listAll();
    }
}
