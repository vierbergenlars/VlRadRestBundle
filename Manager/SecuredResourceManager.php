<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Manager;

use vierbergenlars\Bundle\RadRestBundle\Security\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecuredResourceManager implements ResourceManagerInterface
{
    private $resourceManager;
    private $authorizationChecker;

    public function __construct(ResourceManagerInterface $resourceManager, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->resourceManager = $resourceManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getPageDescription()
    {
        if(!$this->authorizationChecker->mayList()) {
            throw new AccessDeniedException();
        }

        return $this->resourceManager->getPageDescription();
    }

    public function find($id)
    {
        $object = $this->resourceManager->find($id);

        if(!$this->authorizationChecker->mayView($object)) {
            throw new AccessDeniedException();
        }

        return $object;
    }

    public function create($object)
    {
        if(!$this->authorizationChecker->mayCreate($object)) {
            throw new AccessDeniedException();
        }

        return $this->resourceManager->create($object);
    }

    public function update($object)
    {
        if(!$this->authorizationChecker->mayEdit($object)) {
            throw new AccessDeniedException();
        }

        return $this->resourceManager->update($object);
    }

    public function delete($object)
    {
        if(!$this->authorizationChecker->mayDelete($object)) {
            throw new AccessDeniedException();
        }

        return $this->resourceManager->delete($object);
    }

    public function newInstance()
    {
        return $this->resourceManager->newInstance();
    }

    /**
     * @return ResourceManagerInterface
     */
    public function getResourceManager()
    {
        return $this->resourceManager;
    }

    /**
     * @return AuthorizationCheckerInterface
     */
    public function getAuthorizationChecker()
    {
        return $this->authorizationChecker;
    }
}