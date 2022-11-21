<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\Form\Finisher;

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
use Neos\Form\Core\Model\AbstractFinisher;
use Neos\Form\Core\Model\AbstractFormElement;
use Neos\Form\Core\Runtime\FormRuntime;
use Onedrop\Form\Hubspot\Service\HubspotFormService;

class HubSpotFinisher extends AbstractFinisher
{
    /**
     * @Flow\Inject()
     * @var HubspotFormService
     */
    protected $hubspotFormService;

    /**
     * @throws \Neos\Cache\Exception
     */
    protected function executeInternal()
    {
        $formRuntime = $this->finisherContext->getFormRuntime();

        $hubspotFormData = $this->populateHubspotFormData($formRuntime);
        $hubspotFormData['hs_context'] = json_encode($this->buildHubspotContext($formRuntime));
        $hubspotFormId = $formRuntime->getFormDefinition()->getIdentifier();
        $formSubmitResponse = $this->hubspotFormService->submit($hubspotFormId, $hubspotFormData);

        $formRuntime->getResponse()->setContent($formSubmitResponse);
        $this->finisherContext->cancel();
    }

    /**
     * @param FormRuntime $formRuntime
     * @return array
     */
    protected function populateHubspotFormData(FormRuntime $formRuntime): array
    {
        $formData = [];
        foreach ($formRuntime->getFormDefinition()->getPages() as $page) {
            foreach ($page->getElementsRecursively() as $element) {
                if ($element instanceof AbstractFormElement) {
                    $identifier = $element->getIdentifier();
                    $formData[$identifier] = $formRuntime->getFormState()->getFormValue($identifier);
                }
            }
        }

        return $formData;
    }

    /**
     * @param FormRuntime $formRuntime
     * @return array
     */
    protected function buildHubspotContext(FormRuntime $formRuntime): array
    {
        $httpRequest = $formRuntime->getRequest()->getHttpRequest();
        $hubspotContext = [
            'ipAddress' => $httpRequest->getServerParams()['REMOTE_ADDR'] ?? '',
            'pageUri' => $httpRequest->getServerParams()['HTTP_REFERER'] ?? '',
            'pageName' => $formRuntime->getFormState()->getFormValue('page') ?? '',
        ];

        if (isset($httpRequest->getCookieParams()['hubspotutk'])) {
            $hubspotContext['hutk'] = $httpRequest->getCookieParams()['hubspotutk'];
        }

        return $hubspotContext;
    }
}
