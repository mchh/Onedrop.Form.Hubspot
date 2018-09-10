<?php
namespace Onedrop\Form\Hubspot\Service;

use Neos\Cache\Frontend\VariableFrontend;
use Neos\Flow\Annotations as Flow;
use SevenShores\Hubspot\Exceptions\BadRequest;
use SevenShores\Hubspot\Resources\Forms;

/**
 * @Flow\Scope("singleton")
 */
class HubspotFormService
{
    const CACHE_KEY_ALL = 'all_forms';
    const CACHE_KEY_ONE = 'forms';

    /**
     * Injection configured via Objects.yaml
     *
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
     * @Flow\InjectConfiguration(path="api.portalId")
     * @var string
     */
    protected $portalId = '';

    /**
     * @throws \Neos\Cache\Exception
     * @return array|mixed
     */
    public function listAll(): array
    {
        if ($this->cache->has(self::CACHE_KEY_ALL)) {
            return $this->cache->get(self::CACHE_KEY_ALL);
        }

        $response = $this->forms->all();
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

        $response = $this->forms->getById($formIdentifier);
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
     * @return mixed
     * @throws \Neos\Cache\Exception
     */
    public function submit(string $formIdentifier, array $formData)
    {
        try {
            $apiResponse = $this->forms->submit($this->portalId, $formIdentifier, $formData);
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
