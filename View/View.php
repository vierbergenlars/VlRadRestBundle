<?php

namespace vierbergenlars\Bundle\RadRestBundle\View;

use FOS\RestBundle\View\View as FView;

class View extends FView
{
    /**
     *
     * @var array
     */
    private $extraData = array();

    /**
     *
     * @param array $extraData
     * @return \vierbergenlars\ShortenBundle\View\View
     */
    public function setExtraData(array $extraData)
    {
        $this->extraData = array_merge($this->extraData, $extraData);
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getExtraData()
    {
        return $this->extraData;
    }
}
