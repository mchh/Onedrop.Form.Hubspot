<?php
namespace Onedrop\Neos\Hubspot\Domain\Model;

use Neos\ContentRepository\Domain\Model\Node;
use Neos\Flow\Annotations as Flow;
use Neos\Form\Factory\ArrayFormFactory;
use Onedrop\Neos\Hubspot\Service\FormsService;

class FormNode extends Node
{
    /**
     * @var FormsService
     * @Flow\Inject()
     */
    protected $formService;

    /**
     * @var ArrayFormFactory
     * @Flow\Inject()
     */
    protected $arrayFormFactory;

    public function getFormConfiguration()
    {
        $hubspotFormIdentifier = $this->getProperty('formIdentifier');
        $form = $this->formService->getFormByIdentifier($hubspotFormIdentifier);
        \Neos\Flow\var_dump($form);

        //todo: convert to neos form
        $this->arrayFormFactory->build($convertedForm);
    }
}
