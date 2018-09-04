<?php
namespace Onedrop\NeosHubspot\Command;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Onedrop\NeosHubspot\Service\FormsService;

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
