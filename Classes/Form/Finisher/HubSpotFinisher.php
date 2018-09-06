<?php
namespace Onedrop\Form\Hubspot\Form\Finisher;

use Neos\Flow\Annotations as Flow;
use Neos\Form\Core\Model\AbstractFinisher;
use Neos\Form\Core\Model\AbstractFormElement;
use Neos\Form\FormElements\Section;
use Onedrop\Form\Hubspot\Service\FormsService;

/**
 * Class HubSpotFinisher
 */
class HubSpotFinisher extends AbstractFinisher
{
    /**
     * @Flow\Inject()
     * @var FormsService
     */
    protected $formsService;

    /**
     * This method is called in the concrete finisher whenever self::execute() is called.
     *
     * Override and fill with your own implementation!
     *
     * @return void
     * @api
     */
    protected function executeInternal()
    {
        $formRuntime = $this->finisherContext->getFormRuntime();
        $formIdentifier = $formRuntime->getFormDefinition()->getIdentifier();

        $formData = [];

        foreach ($formRuntime->getFormDefinition()->getPages() as $page) {
            foreach ($page->getElementsRecursively() as $element) {
                if ($element instanceof AbstractFormElement) {
                    $identifier = $element->getIdentifier();
                    $formData[$identifier] = $formRuntime->getFormState()->getFormValue($identifier);
                }
            }
        }

        $this->formsService->submit($formIdentifier, $formData);
    }
}
