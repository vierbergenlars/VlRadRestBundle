<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Twig;

class ObjectExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('radrest_object_keys', array($this, 'objectKeys')),
            new \Twig_SimpleFilter('radrest_object_stringify', array($this, 'objectStringify')),
        );
    }

    public function objectKeys($object)
    {
        $keys = array();
        $refl = new \ReflectionClass($object);
        $properties = $refl->getProperties();
        foreach($properties as $property) {
            if($property->isPublic()||$refl->hasMethod('get'.ucfirst($property->getName()))) {
                $keys[] = $property->getName();
            }
        }
        return $keys;
    }

    public function objectStringify($object)
    {
        switch(gettype($object)) {
            case 'boolean':
                return $object?'Y':'N';
            case 'integer':
            case 'double':
            case 'string':
                return (string)$object;
            case 'array':
                return '(Array)';
            case 'resource':
                return '(Resource)';
            case 'NULL':
                return '(NULL)';
            case 'object':
                if($object instanceof \DateTime) {
                    return $object->format(\DateTime::ISO8601);
                } elseif(method_exists($object, '__toString')) {
                    return (string)$object;
                } else {
                    return '(Object)';
                }
            // @codeCoverageIgnoreStart
            default:
                return '(Unknown)';
            // @codeCoverageIgnoreEnd
        }
    }

    public function getName()
    {
        return 'radrest_object';
    }
}
