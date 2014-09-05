<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Redirect;

use vierbergenlars\Bundle\RadRestBundle\View\View;

/**
 * This trait provides the default redirect to a route implementation
 */
trait DefaultRedirectTrait
{
    /**
     * Gets the name of the route for the given action on this controller.
     * @param string $action
     * @return string The route name
     */
    abstract protected function getRouteName($action);

    /**
     * Redirects to another action on the same controller
     * @param string $nextAction The action name to redirect to
     * @param array<string> $params Parameters to pass to the route generator
     * @return View
     */
    protected function redirectTo($nextAction, array $params = array())
    {
        return View::createRouteRedirect($this->getRouteName($nextAction), $params);
    }
}
