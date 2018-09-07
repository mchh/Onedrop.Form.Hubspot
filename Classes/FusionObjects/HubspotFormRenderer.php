<?php
namespace Onedrop\Form\Hubspot\FusionObjects;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Response;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Form\Factory\ArrayFormFactory;
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use Onedrop\Form\Hubspot\Exception;
use Onedrop\Form\Hubspot\Service\FormsService;

/**
 * Class HubspotFormRenderer
 */
class HubspotFormRenderer extends AbstractFusionObject
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

    /**
     * @Flow\InjectConfiguration(path="finishers")
     * @var array
     */
    protected $finishers = [];

    /**
     * @var array
     */
    protected $typeMap = [
        'text'            => 'Neos.Form:SingleLineText',
        'textarea'        => 'Neos.Form:MultiLineText',
        'select'          => 'Neos.Form:SingleSelectDropdown',
        'radio'           => 'Neos.Form:SingleSelectRadiobuttons',
        'checkbox'        => 'Neos.Form:MultipleSelectCheckboxes',
        'booleancheckbox' => 'Neos.Form:Checkbox',
        'number'          => 'Neos.Form:SingleLineText',
        'file'            => 'Neos.Form:FileUpload',
        'date'            => 'Neos.Form:DatePicker',
    ];

    /**
     * @throws Exception
     * @throws \Neos\Cache\Exception
     * @throws \Neos\Form\Exception\RenderingException
     * @return string
     */
    public function evaluate(): string
    {
        $formIdentifier = $this->fusionValue('identifier');
        $hubspotForm = $this->formService->getFormByIdentifier($formIdentifier);

        if (empty($hubspotForm)) {
            return 'Please select a form';
        }

        $sections = $this->getSections($hubspotForm['formFieldGroups']);
        $page = $this->getPage('page-one', $sections);
        $formDefinition = $this->getForm($hubspotForm['guid'], $hubspotForm['name'], [$page]);
        $formDefinition['renderingOptions']['_fusionRuntime'] = $this->runtime;
        $formDefinition['finishers'] = $this->finishers;

        $form = $this->arrayFormFactory->build($formDefinition, 'fusion');
        $request = $this->getRuntime()->getControllerContext()->getRequest();
        $response = $this->getRuntime()->getControllerContext()->getResponse();

        if (!($request instanceof ActionRequest)) {
            throw new Exception('Can not render a form outside of action requests');
        }
        $formRuntime = $form->bind($request, new Response($response));

        return $formRuntime->render();
    }

    /**
     * @param  string $identifier
     * @param  string $label
     * @param  array  $children
     * @return array
     */
    protected function getForm(string $identifier, string $label, array $children): array
    {
        return [
            'type'        => 'Neos.Form:Form',
            'identifier'  => $identifier,
            'label'       => $label,
            'renderables' => $children,
        ];
    }

    /**
     * @param  string $identifier
     * @param  array  $children
     * @return array
     */
    protected function getPage(string $identifier, array $children): array
    {
        return [
            'type'        => 'Neos.Form:Page',
            'identifier'  => $identifier,
            'renderables' => $children,
        ];
    }

    /**
     * @param  array $formFieldGroups
     * @return array
     */
    protected function getSections(array $formFieldGroups): array
    {
        return array_map(
            function (array $formFieldGroup) {
                $fields = $this->getFields($formFieldGroup['fields']);

                return $this->renderSection('section-' . uniqid(), $fields);
            },
            $formFieldGroups
        );
    }

    /**
     * @param  string $identifier
     * @param  array  $children
     * @return array
     */
    protected function renderSection(string $identifier, array $children): array
    {
        return [
            'type'       => 'Neos.Form:Section',
            'identifier' => $identifier,
            'properties' => [
                'sectionClassAttribute' => 'row',
            ],
            'renderables' => $children,
        ];
    }

    /**
     * @param  array $fields
     * @return array
     */
    protected function getFields(array $fields): array
    {
        return array_filter(
            array_map(
                function (array $formFieldGroupFields) {
                    return $this->renderField($formFieldGroupFields);
                },
                $fields
            )
        );
    }

    /**
     * @param  array $definition
     * @return array
     */
    protected function renderField(array $definition): array
    {
        if (!$definition['enabled']) {
            return [];
        }

        $type = $this->typeMap[$definition['fieldType']];
        $identifier = $definition['name'];
        $label = $definition['label'];
        $validators = [];
        $properties = [];
        $defaultValue = $definition['defaultValue'];

        if ($definition['required']) {
            $validators[] = ['identifier' => 'Neos.Flow:NotEmpty'];
        }
        if (isset($definition['validation'])) {
            $validation = $definition['validation'];
            if (isset($validation['useDefaultBlockList']) && true === $validation['useDefaultBlockList']) {
                $validators[] = ['identifier' => 'Onedrop.Form.Hubspot:FreeEmailAddressProvider'];
            }
            if (!empty($validation['data'])) {
                $validators[] = [
                    'identifier' => 'Onedrop.Form.Hubspot:EmailAddressBlacklist',
                    'options'    => ['blacklist' => $validation['data']],
                ];
            }
        }
        if (!empty($definition['placeholder'])) {
            $properties['placeholder'] = $definition['placeholder'];
        }
        if (!empty($definition['options'])) {
            foreach ($definition['options'] as $option) {
                $properties['options'][$option['value']] = $option['label'];
            }
        }

        $properties['elementClassAttribute'] = 'form-control';
        $properties['elementErrorClassAttribute'] = 'form-error';

        if (!empty($definition['selectedOptions'])) {
            $defaultValue = $definition['selectedOptions'];
        }
        if (!empty($definition['description'])) {
            $properties['elementDescription'] = $definition['description'];
        }
        if ($definition['hidden']) {
            $properties['elementClassAttribute'] .= ' hidden';
        }

        return [
            'type'         => $type,
            'identifier'   => $identifier,
            'label'        => $label,
            'validators'   => $validators,
            'properties'   => $properties,
            'defaultValue' => $defaultValue,
        ];
    }
}
