<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\Domain\Factory;

use Neos\Flow\Annotations as Flow;
use Neos\Fusion\Core\Runtime;
use Onedrop\Form\Hubspot\Service\HubspotFormService;

/**
 * Class FormDefinitionFactory
 */
class FormDefinitionFactory
{
    /**
     * @var array
     */
    protected $typeMap = [
        'text'            => 'Onedrop.Form.Hubspot:Component.Atom.SingleLineText',
        'textarea'        => 'Onedrop.Form.Hubspot:Component.Atom.MultiLineText',
        'select'          => 'Onedrop.Form.Hubspot:Component.Atom.SingleSelectDropdown',
        'radio'           => 'Onedrop.Form.Hubspot:Component.Atom.SingleSelectRadiobuttons',
        'checkbox'        => 'Onedrop.Form.Hubspot:Component.Atom.MultipleSelectCheckboxes',
        'booleancheckbox' => 'Onedrop.Form.Hubspot:Component.Atom.Checkbox',
        'number'          => 'Onedrop.Form.Hubspot:Component.Atom.SingleLineText',
        'file'            => 'Onedrop.Form.Hubspot:Component.Atom.FileUpload',
        'date'            => 'Onedrop.Form.Hubspot:Component.Atom.DatePicker',
    ];

    /**
     * @var HubspotFormService
     * @Flow\Inject()
     */
    protected $hubspotFormService;

    /**
     * @Flow\InjectConfiguration(path="finishers")
     * @var array
     */
    protected $finishers = [];

    /**
     * @param  string                $formIdentifier
     * @param  Runtime|null          $runtime
     * @throws \Neos\Cache\Exception
     * @return array
     */
    public function getFromDefinitionByHubspotIdentifier(string $formIdentifier, Runtime $runtime = null): array
    {
        $hubspotForm = $this->hubspotFormService->getFormByIdentifier($formIdentifier);

        if (empty($hubspotForm)) {
            return [];
        }

        $sections = $this->getSections($hubspotForm['formFieldGroups']);
        $page = $this->getPage('page-one', $sections);
        $formDefinition = $this->getForm($hubspotForm['guid'], $hubspotForm['name'], [$page]);
        $formDefinition['renderingOptions']['_fusionRuntime'] = $runtime;
        $formDefinition['finishers'] = $this->finishers;

        return $formDefinition;
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
            'type'        => 'Onedrop.Form.Hubspot:Content.Form',
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
            'type'        => 'Onedrop.Form.Hubspot:Component.Molecule.Page',
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
            'type'       => 'Onedrop.Form.Hubspot:Component.Molecule.Section',
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
