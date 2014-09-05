<?php

namespace vierbergenlars\Bundle\RadRestBundle\View;

use FOS\RestBundle\View\View as FView;

class View extends FView
{
    /**
     * @var array
     */
    private $extraData = array();

    /**
     * Convenience method to allow for a fluent interface.
     *
     * @param mixed $data
     * @param int   $statusCode
     * @param array $headers
     *
     * @return View
     */
    public static function create($data = null, $statusCode = null, array $headers = array())
    {
        return new static($data, $statusCode, $headers);
    }

    /**
     * Adds extra data that will be merged with the data when using a templating format
     *
     * Extra data added here will not be visible in serialized representation,
     * but it will be available as a variable when rendering a template.
     *
     * @param array $extraData
     * @return View
     */
    public function setExtraData(array $extraData)
    {
        $this->extraData = array_merge($this->extraData, $extraData);
        return $this;
    }

    /**
     * Gets the extra data that is added to the view
     *
     * @return array
     */
    public function getExtraData()
    {
        return $this->extraData;
    }
}
