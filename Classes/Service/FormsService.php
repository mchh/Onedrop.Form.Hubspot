<?php
namespace Onedrop\Form\Hubspot\Service;

use Neos\Cache\Frontend\VariableFrontend;
use Neos\Flow\Annotations as Flow;
use SevenShores\Hubspot\Resources\Forms;

/**
 * @Flow\Scope("singleton")
 */
class FormsService
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
     * @return array|mixed
     * @throws \Neos\Cache\Exception
     */
    public function listAll()
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
     * @param string $formIdentifier
     * @return array
     * @throws \Neos\Cache\Exception
     */
    public function getFormByIdentifier(string $formIdentifier): array
    {
        $cacheIdentifier = implode('|', [self::CACHE_KEY_ONE, $formIdentifier]);

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
}
