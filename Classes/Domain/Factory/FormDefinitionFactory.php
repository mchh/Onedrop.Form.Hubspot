<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\Domain\Factory;

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
use Neos\Fusion\Core\Runtime;
use Onedrop\Form\Hubspot\Service\HubspotFormService;

class FormDefinitionFactory
{
    /**
     * @var array
     */
    protected $typeMap = [
        'email' => 'Onedrop.Form.Hubspot:Component.Atom.Email',
        'text' => 'Onedrop.Form.Hubspot:Component.Atom.SingleLineText',
        'hidden' => 'Onedrop.Form.Hubspot:Component.Atom.Hidden',
        'textarea' => 'Onedrop.Form.Hubspot:Component.Atom.MultiLineText',
        'select' => 'Onedrop.Form.Hubspot:Component.Atom.SingleSelectDropdown',
        'radio' => 'Onedrop.Form.Hubspot:Component.Atom.SingleSelectRadiobuttons',
        'checkbox' => 'Onedrop.Form.Hubspot:Component.Atom.MultipleSelectCheckboxes',
        'booleancheckbox' => 'Onedrop.Form.Hubspot:Component.Atom.Checkbox',
        'number' => 'Onedrop.Form.Hubspot:Component.Atom.SingleLineNumber',
        'file' => 'Onedrop.Form.Hubspot:Component.Atom.FileUpload',
        'date' => 'Onedrop.Form.Hubspot:Component.Atom.DatePicker',
        'rte' => 'Onedrop.Form.Hubspot:Component.Atom.Rte',
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
     * @param  string $formIdentifier
     * @param  Runtime|null $runtime
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

        if (!empty($hubspotForm['metaData'])) {
            foreach ($hubspotForm['metaData'] as $metaData) {
                if ('legalConsentOptions' === $metaData['name']) {
                    $fields = [];
                    $value = json_decode($metaData['value'], true);

                    if (!empty($value['communicationConsentText'])) {
                        $fields[] = [
                            'name' => 'consent-communication-consent-text',
                            'label' => htmlspecialchars($value['communicationConsentText']),
                            'type' => 'rte',
                            'fieldType' => 'rte',
                            'description' => '',
                            'required' => false,
                            'selectedOptions' => [],
                            'enabled' => 1,
                            'hidden' => false,
                            'defaultValue' => '',
                        ];
                    }

                    if (isset($value['communicationConsentCheckboxes'])) {
                        foreach ($value['communicationConsentCheckboxes'] as $constentCheckBox) {
                            $fields[] = [
                                'name' => "consent-checkbox-{$constentCheckBox['communicationTypeId']}",
                                'label' => htmlspecialchars($constentCheckBox['label']),
                                'type' => 'enumeration',
                                'fieldType' => 'booleancheckbox',
                                'description' => '',
                                'required' => $constentCheckBox['required'],
                                'selectedOptions' => [],
                                'options' => [
                                    [
                                        'value' => $constentCheckBox['communicationTypeId'],
                                        'label' => htmlspecialchars($constentCheckBox['label']),
                                    ],
                                ],
                                'enabled' => 1,
                                'hidden' => false,
                            ];
                        }
                    }

                    if (!empty($value['processingConsentText'])) {
                        $fields[] = [
                            'name' => 'consent-processing-consent-text',
                            'label' => htmlspecialchars($value['processingConsentText']),
                            'type' => 'rte',
                            'fieldType' => 'rte',
                            'description' => '',
                            'required' => false,
                            'selectedOptions' => [],
                            'enabled' => 1,
                            'hidden' => false,
                            'defaultValue' => '',
                        ];
                    }

                    $consentRequired = true;
                    if (isset($value['processingConsentType'])) {
                        $consentRequired = ('REQUIRED_CHECKBOX' === $value['processingConsentType']);
                    }

                    if (!empty($value['processingConsentCheckboxLabel'])) {
                        $fields[] = [
                            'name' => "consent-checkbox-checkbox-label}",
                            'label' => htmlspecialchars($value['processingConsentCheckboxLabel']),
                            'type' => 'enumeration',
                            'fieldType' => 'booleancheckbox',
                            'description' => '',
                            'required' => $consentRequired,
                            'selectedOptions' => [],
                            'options' => [
                                [
                                    'value' => 1,
                                    'label' => $value['processingConsentCheckboxLabel'],
                                ],
                            ],
                            'enabled' => 1,
                            'hidden' => false,
                        ];
                    }

                    if (!empty($value['processingConsentFooterText'])) {
                        $fields[] = [
                            'name' => 'consent-processing-consent-footer-text',
                            'label' => htmlspecialchars($value['processingConsentFooterText']),
                            'type' => 'rte',
                            'fieldType' => 'rte',
                            'description' => '',
                            'required' => false,
                            'selectedOptions' => [],
                            'enabled' => 1,
                            'hidden' => false,
                            'defaultValue' => '',
                        ];
                    }

                    if (!empty($value['privacyPolicyText'])) {
                        $fields[] = [
                            'name' => 'consent-privacy-policy-text',
                            'label' => htmlspecialchars($value['privacyPolicyText']),
                            'type' => 'rte',
                            'fieldType' => 'rte',
                            'description' => '',
                            'required' => false,
                            'selectedOptions' => [],
                            'enabled' => 1,
                            'hidden' => false,
                            'defaultValue' => '',
                        ];
                    }

                    foreach ($fields as $field) {
                        $page['renderables'] = array_merge(
                            $page['renderables'],
                            $this->getSections([['fields' => [$field]]])
                        );
                    }
                }
            }
            $formDefinition['renderingOptions']['submitButtonLabel'] = $hubspotForm['submitText'];
        }

        $formDefinition = $this->getForm($hubspotForm['guid'], $hubspotForm['name'], [$page]);
        $formDefinition['renderingOptions']['_fusionRuntime'] = $runtime;
        $formDefinition['finishers'] = $this->finishers;
        if (!empty($hubspotForm['submitText'])) {
            $formDefinition['renderingOptions']['submitButtonLabel'] = htmlspecialchars($hubspotForm['submitText']);
        }

        return $formDefinition;
    }

    /**
     * @param  string $identifier
     * @param  string $label
     * @param  array $children
     * @return array
     */
    protected function getForm(string $identifier, string $label, array $children): array
    {
        return [
            'type' => 'Onedrop.Form.Hubspot:Component.Molecule.Form',
            'identifier' => $identifier,
            'label' => htmlspecialchars($label),
            'renderables' => $children,
        ];
    }

    /**
     * @param  string $identifier
     * @param  array $children
     * @return array
     */
    protected function getPage(string $identifier, array $children): array
    {
        return [
            'type' => 'Onedrop.Form.Hubspot:Component.Molecule.Page',
            'identifier' => $identifier,
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
     * @param  array $children
     * @return array
     */
    protected function renderSection(string $identifier, array $children): array
    {
        return [
            'type' => 'Onedrop.Form.Hubspot:Component.Molecule.Section',
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

        $properties = [];

        $type = $this->typeMap[$definition ['fieldType']];
        if ('email' === $definition['name']) {
            $type = $this->typeMap['email'];
            $definition['required'] = true;
        }

        if (!empty($definition['placeholder'])) {
            $properties['placeholder'] = $definition['placeholder'];
        }
        if (!empty($definition['options'])) {
            foreach ($definition['options'] as $option) {
                $properties['options'][$option['value']] = htmlspecialchars($option['label']);
            }
        }

        $properties['elementClassAttribute'] = 'form-control';
        $properties['elementErrorClassAttribute'] = 'form-error';
        $properties['multiple'] = ('multiple_files' === $definition['name']);
        $properties['defaultChecked'] = false;

        if (!empty($definition['description'])) {
            $properties['elementDescription'] = $definition['description'];
        }
        if ($definition['hidden']) {
            $type = $this->typeMap['hidden'];
        }
        if ('number' === $definition['fieldType'] && !empty($definition['validation']['data'])) {
            list($minimum, $maximum) = explode(':', $definition['validation']['data']);
            $properties['min'] = $minimum;
            $properties['max'] = $maximum;
        }

        if ('enumeration' === $definition['type']) {
            $defaultValue = $definition['selectedOptions'];
        } else {
            $defaultValue = $definition['defaultValue'];
        }

        return [
            'type' => $type,
            'identifier' => !empty($definition['name']) ? $definition['name'] : md5(json_encode($definition)),
            'label' => htmlspecialchars($definition['label']),
            'validators' => $this->renderFieldValidators($definition),
            'properties' => $properties,
            'defaultValue' => $defaultValue,
        ];
    }

    /**
     * @param array $definition
     * @return array
     */
    protected function renderFieldValidators(array $definition): array
    {
        $validators = [];
        if ($definition['required']) {
            $validators[] = ['identifier' => 'Neos.Flow:NotEmpty'];
        }
        if (empty($definition['validation'])) {
            return $validators;
        }

        $validation = $definition['validation'];
        if (isset($validation['useDefaultBlockList']) && true === $validation['useDefaultBlockList']) {
            $validators[] = ['identifier' => 'Onedrop.Form.Hubspot:FreeEmailAddressProvider'];
        }

        if (!empty($validation['data'])) {
            if ('number' === $definition['fieldType']) {
                list($minimum, $maximum) = explode(':', $validation['data']);
                $validators[] = [
                    'identifier' => 'Neos.Flow:NumberRange',
                    'options' => ['minimum' => $minimum, 'maximum' => $maximum],
                ];
            } else {
                $validators[] = [
                    'identifier' => 'Onedrop.Form.Hubspot:EmailAddressBlacklist',
                    'options' => ['blacklist' => $validation['data']],
                ];
            }
        }

        return $validators;
    }
}
