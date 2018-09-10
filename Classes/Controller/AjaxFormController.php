<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Response;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Form\Factory\ArrayFormFactory;
use Neos\Fusion\Core\RuntimeFactory;
use Onedrop\Form\Hubspot\Domain\Factory\FormDefinitionFactory;
use Sitegeist\Monocle\Fusion\FusionService;

/**
 * Class AjaxFormController
 */
class AjaxFormController extends ActionController
{
    /**
     * @Flow\Inject()
     * @var ArrayFormFactory
     */
    protected $arrayFormFactory = null;

    /**
     * @Flow\Inject()
     * @var FormDefinitionFactory
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    protected $formDefinitionFactory = null;

    /**
     * @Flow\Inject()
     * @var RuntimeFactory
     */
    protected $runtimeFactory = null;

    /**
     * @Flow\Inject()
     * @var FusionService
     */
    protected $fusionService = null;

    /**
     * @param  string                                  $formIdentifier
     * @throws \Neos\Cache\Exception
     * @throws \Neos\Form\Exception\RenderingException
     * @throws \Neos\Neos\Domain\Exception
     * @return string
     */
    public function submitAction(string $formIdentifier)
    {
        $fusionConfig = $this->fusionService->getMergedFusionObjectTreeForSitePackage('Onedrop.ProSoft');
        $runtime = $this->runtimeFactory->create($fusionConfig, $this->controllerContext);

        $formDefinition = $this->formDefinitionFactory->getFromDefinitionByHubspotIdentifier($formIdentifier, $runtime);
        if (empty($formDefinition)) {
            return 'Please select a form';
        }

        $runtime->pushContext('identifier', $formIdentifier);

        $formDefinition['renderingOptions']['_fusionRuntime'] = $runtime;
        $form = $this->arrayFormFactory->build($formDefinition, 'hubspotAtomicFusion');

        return $form->bind($this->request, new Response($this->response))->render();
    }
}
