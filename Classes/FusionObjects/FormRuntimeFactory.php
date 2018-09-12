<?php
namespace Onedrop\Form\Hubspot\FusionObjects;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Response;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Form\Core\Runtime\FormRuntime;
use Neos\Form\Factory\ArrayFormFactory;
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use Onedrop\Form\Hubspot\Domain\Factory\FormDefinitionFactory;
use Onedrop\Form\Hubspot\Exception;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class FormRuntimeFactory extends AbstractFusionObject
{
    /**
     * @var ArrayFormFactory
     * @Flow\Inject()
     */
    protected $arrayFormFactory;

    /**
     * @Flow\Inject()
     * @var FormDefinitionFactory
     */
    protected $formDefinitionFactory = null;

    /**
     * @throws Exception
     * @throws \Neos\Cache\Exception
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     * @return FormRuntime
     */
    public function evaluate(): FormRuntime
    {
        $formIdentifier = $this->fusionValue('identifier');

        if (empty($formIdentifier)) {
            $request = $this->runtime->getControllerContext()->getRequest();
            if ($request instanceof ActionRequest
                && 'Onedrop.Form.Hubspot' === $request->getControllerPackageKey()
                && $request->hasArgument('formIdentifier')
            ) {
                $formIdentifier = $request->getArgument('formIdentifier');
            }
        }

        if (empty($formIdentifier)) {
            throw Exception::noFormSelected();
        }

        $formDefinition = $this->formDefinitionFactory->getFromDefinitionByHubspotIdentifier(
            $formIdentifier,
            $this->runtime
        );
        if (empty($formDefinition)) {
            throw new Exception('Please select a form');
        }

        $request = $this->getRuntime()->getControllerContext()->getRequest();
        if (!($request instanceof ActionRequest)) {
            throw new Exception('Can not render a form outside of action requests');
        }
        $response = $this->getRuntime()->getControllerContext()->getResponse();
        if (!($response instanceof Response)) {
            throw new Exception('Can not render without a http response');
        }

        $form = $this->arrayFormFactory->build($formDefinition, 'hubspotAtomicFusion');

        return $form->bind($request, new Response($response));
    }
}
