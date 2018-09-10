<?php
namespace Onedrop\Form\Hubspot\Command;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Onedrop\Form\Hubspot\Service\HubspotFormService;

/**
 * Class FormsCommandController
 */
class FormsCommandController extends CommandController
{
    /**
     * @Flow\Inject()
     * @var HubspotFormService
     */
    protected $formsService = null;

    /**
     * @throws \Neos\Cache\Exception
     */
    public function listAllCommand()
    {
        foreach ($this->formsService->listAll() as $form) {
            $this->outputLine($form['identifier'], $form['label']);
        }
    }
}
