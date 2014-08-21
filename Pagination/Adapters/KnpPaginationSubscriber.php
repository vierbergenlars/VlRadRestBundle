<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Pagination\Adapters;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;
use vierbergenlars\Bundle\RadRestBundle\Pagination\PageDescriptionInterface;

class KnpPaginationSubscriber implements EventSubscriberInterface
{
    public function items(ItemsEvent $event)
    {
        if($event->target instanceof PageDescriptionInterface) {
            $event->count = $event->target->getTotalItemCount();
            $event->items = $event->target->getSlice($event->getOffset(), $event->getLimit());
            $event->stopPropagation();
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'knp_pager.items' => array('items', 0)
        );
    }
}
