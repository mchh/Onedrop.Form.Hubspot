<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\FusionObjects;

/*
 * This file is part of the Onedrop.Form.Hubspot package.
 *
 * (c) 2018 Oliver Eglseder <oeglseder@1drop.de>, Onedrop GmbH & Co. KG
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Response;
use Neos\Flow\Mvc\ActionRequest;
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
     * @return string
     * @throws Exception
     * @throws \Neos\Cache\Exception
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     * @throws \Neos\Form\Exception\RenderingException
     */
    public function evaluate(): string
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
            throw Exception::formUnavailable();
        }

        $request = $this->getRuntime()->getControllerContext()->getRequest();
        if (!($request instanceof ActionRequest)) {
            throw new Exception('Can not render a form outside of action requests');
        }
        $response = $this->getRuntime()->getControllerContext()->getResponse();
        if (!($response instanceof Response)) {
            throw new Exception('Can not render without a http response');
        }

        $formDefinition['renderingOptions']['_fusionRuntime'] = $this->runtime;
        $form = $this->arrayFormFactory->build($formDefinition, 'hubspotAtomicFusion');

        $formRuntime = $form->bind($request, new Response($response));

        return $formRuntime->render();
    }
}
