<?php
namespace Onedrop\NeosHubspot\Domain\Factory;

use Neos\Flow\Annotations as Flow;
use SevenShores\Hubspot\Factory;
use SevenShores\Hubspot\Resources\Forms;

/**
 * @Flow\Scope("singleton")
 */
class FormsFactory
{
    /**
     * @Flow\InjectConfiguration()
     * @var array
     */
    protected $settings = [];

    /**
     * @return Forms
     */
    public function getForms()
    {
        return Factory::create($this->settings['api']['hapikey'])->forms();
    }
}
