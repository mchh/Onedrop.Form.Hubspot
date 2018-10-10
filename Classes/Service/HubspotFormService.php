<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\Service;

/*
 * This file is part of the Onedrop.Form.Hubspot package.
 *
 * (c) 2018 Oliver Eglseder <oeglseder@1drop.de>, Onedrop GmbH & Co. KG
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Cache\Frontend\VariableFrontend;
use Neos\Flow\Annotations as Flow;
use SevenShores\Hubspot\Exceptions\BadRequest;
use SevenShores\Hubspot\Factory;
use SevenShores\Hubspot\Resources\Forms;

/**
 * @Flow\Scope("singleton")
 */
class HubspotFormService
{
    const CACHE_KEY_ALL = 'all_forms';
    const CACHE_KEY_ONE = 'forms';

    /**
     * @var Forms
     */
    protected $forms = null;

    /**
     * Injection configured via Objects.yaml
     *
     * @var VariableFrontend
     */
    protected $cache = null;

    /**
     * @Flow\InjectConfiguration()
     * @var array
     */
    protected $settings = [];

    /**
     * HubspotFormService constructor.
     */
    public function initializeObject()
    {
        $this->forms = Factory::create($this->settings['api']['hapikey'])->forms();
    }

    /**
     * @throws \Neos\Cache\Exception
     * @return array|mixed
     */
    public function listAll(): array
    {
        if ($this->cache->has(self::CACHE_KEY_ALL)) {
            return $this->cache->get(self::CACHE_KEY_ALL);
        }

        try {
            $response = $this->forms->all();
        } catch (BadRequest $exception) {
            if (401 === $exception->getCode()) {
                return [
                    [
                        'identifier' => null,
                        'label' => 'Your HAPI Key is invalid',
                        'formGroups' => null,
                    ],
                ];
            } else {
                return [
                    [
                        'identifier' => null,
                        'label' => 'Unknown error: ' . $exception->getMessage(),
                        'formGroups' => null,
                    ],
                ];
            }
        }

        if (200 !== $response->getStatusCode()) {
            return [];
        }

        $forms = array_map(
            function (array $form) {
                return [
                    'identifier' => $form['guid'],
                    'label' => $form['name'],
                    'formGroups' => $form['formFieldGroups'],
                ];
            },
            $response->toArray()
        );
        $forms = array_combine(array_column($forms, 'identifier'), $forms);
        $this->cache->set(self::CACHE_KEY_ALL, $forms);

        return $forms;
    }

    /**
     * @param  string|null $formIdentifier
     * @throws \Neos\Cache\Exception
     * @return array
     */
    public function getFormByIdentifier(string $formIdentifier = null): array
    {
        if (null === $formIdentifier) {
            return [];
        }

        $cacheIdentifier = implode('_', [self::CACHE_KEY_ONE, $formIdentifier]);

        if ($this->cache->has($cacheIdentifier)) {
            return $this->cache->get($cacheIdentifier);
        }

        try {
            $response = $this->forms->getById($formIdentifier);
        } catch (BadRequest $exception) {
            return [];
        }
        if (200 !== $response->getStatusCode()) {
            return [];
        }

        $form = $response->toArray();
        $this->cache->set($cacheIdentifier, $form);

        return $form;
    }

    /**
     * For status codes:
     *
     * @link https://developers.hubspot.com/docs/methods/forms/submit_form
     *
     * @param  string $formIdentifier
     * @param  array $formData
     * @throws \Neos\Cache\Exception
     * @return mixed
     */
    public function submit(string $formIdentifier, array $formData)
    {
        try {
            $apiResponse = $this->forms->submit($this->settings['api']['portalId'], $formIdentifier, $formData);
            switch ($apiResponse->getStatusCode()) {
                case 204:
                    return $this->getFormByIdentifier($formIdentifier);
                case 302:
                case 500:
                default:
            }
        } catch (BadRequest $exception) {
            if (400 === $exception->getCode()) {
                // Validation failed.
            }
        }
    }
}
