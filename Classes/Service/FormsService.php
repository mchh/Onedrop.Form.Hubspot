<?php

namespace Onedrop\NeosHubspot\Service;

use Neos\Cache\Frontend\VariableFrontend;
use Neos\Flow\Annotations as Flow;
use SevenShores\Hubspot\Resources\Forms;

/**
 * @Flow\Scope("singleton")
 */
class FormsService
{
    const CACHE_KEY = 'forms';

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
     *
     */
    public function listAll()
    {
        if (!$this->cache->has(self::CACHE_KEY)) {
            $forms = $this->forms->all();
            if (200 !== $forms->getStatusCode()) {
                return [];
            }
            $forms = $forms->toArray();
            $this->cache->set(self::CACHE_KEY, $forms);
        } else {
            $forms = $this->cache->get(self::CACHE_KEY);
        }

        return array_map(
            function (array $form) {
                return [
                    'identifier' => $form['guid'],
                    'label'      => $form['name'],
                    'formGroups' => $form['formFieldGroups'],
                ];
            },
            $forms
        );
    }

    /**
     * @param string $hubspotFormIdentifier
     * @return array
     * @throws \SevenShores\Hubspot\Exceptions\BadRequest
     */
    public function getFormByIdentifier(string $hubspotFormIdentifier): array
    {
        return $this->forms->getById($hubspotFormIdentifier)->toArray();
    }
}
