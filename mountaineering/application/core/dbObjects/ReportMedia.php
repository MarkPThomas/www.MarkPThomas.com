<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/30/18
 * Time: 4:22 PM
 */

namespace markpthomas\mountaineering\dbObjects;


abstract class ReportMedia extends MountaineeringBase
{
// ========== Properties ===========
    protected $objectPrefix;

    public $suppress_caption;
    public $custom_caption;


// ========== I/O ===========
    public function toStdClass($isVerbose = false, $isJoin = false){
        $object = new \stdClass();

        $object->id = $this->getId();
        if (isset($this->suppress_caption)) $object->suppress_caption = $this->suppress_caption;
        if (isset($this->custom_caption)) $object->custom_caption = $this->custom_caption;

        return $object;
    }



// ========== Methods ===========
    /**
     * @return string
     */
    public function getReportCaption(){
        return $this->custom_caption;
    }

    /**
     * @param Media $currentMedia
     * @return string
     */
    protected function getCaptionFromMedia(Media $currentMedia){
        if ($this->suppress_caption){
            return '';
        }

        return (empty($this->custom_caption))?
            $currentMedia->caption:
            $this->custom_caption;
    }

    protected function castData(){
        parent::castData();
        parent::setBool('suppress_caption', $this->suppress_caption);
    }
} 