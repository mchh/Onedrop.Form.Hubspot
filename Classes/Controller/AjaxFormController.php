<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\Controller;

use Neos\ContentRepository\Domain\Model\Node;
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
     */
    public function submitAction(Node $node)
    {
        $this->view->setFusionPath('ajaxForm');
        $this->view->assign('value', $node);
    }
}
