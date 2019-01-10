<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\Controller;

/*
 * This file is part of the Onedrop.Form.Hubspot package.
 *
 * (c) 2018 Oliver Eglseder <oeglseder@1drop.de>, Onedrop GmbH & Co. KG
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\ContentRepository\Domain\Model\Node;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Neos\View\FusionView;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class AjaxFormController extends ActionController
{
    /**
     * @var string
     */
    protected $defaultViewObjectName = FusionView::class;

    /**
     * @var FusionView
     */
    protected $view = null;

    /**
     * @param Node $node
     * @Flow\SkipCsrfProtection()
     */
    public function submitAction(Node $node)
    {
        $this->view->setFusionPath('ajaxForm');
        $this->view->assign('value', $node);
    }
}
