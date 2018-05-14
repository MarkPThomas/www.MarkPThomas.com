<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/23/18
 * Time: 10:55 PM
 */

namespace markpthomas\gis;


class Form {


    /**
     * Writes the html for a selection toggle slider control.
     * @param string $item_id Id value to associate with the value of the control.
     * @param string $status Current value associated with the control.
     * @param string $on Value for the toggle 'on' position displayed.
     * @param string $off Value for the toggle 'off' position displayed.
     * @param string $postTarget Path/file target of the control POST method.
     * If not set, it is assumed that post is being handled by the form.
     * @param string $dataOffStyle CSS style name to associate with the control when it is set to 'off'.
     * @param bool $isDisabled
     * @return string
     */
    public static function sliderControl($item_id, $status, $on, $off, $postTarget = null, $dataOffStyle = 'info', $isDisabled = false){
        $isCheckedAttribute = ($status === $on)? 'checked' : '';
        $isDisabledAttribute = ($isDisabled)? 'disabled' : '';
        $value = $postTarget? $postTarget . '&' . $item_id : $item_id;

        return "<input type='checkbox' class='approvalSwitch-checkbox'
                   data-toggle='toggle' data-on='{$on}' data-off='{$off}'
                   data-onstyle='success' data-offstyle='{$dataOffStyle}'
                   value='{$value}' {$isCheckedAttribute} {$isDisabledAttribute}>";
    }


    public static function checkBoxControl($item_id, $isChecked, $postTarget){
        // State setup
        $isCheckedAttribute = $isChecked? 'checked' : '';

        // Dynamic preparation
        $value = $postTarget? $postTarget . '&' . $item_id : $item_id;
        $dynamicClass = $postTarget? 'checkBoxes-dynamic' : '';

        // TODO: Name attribute? https://stackoverflow.com/questions/3626883/what-is-the-purpose-of-the-name-attribute-in-a-checkbox-input-element
        return "<input type='checkbox' class='checkBoxes {$dynamicClass}'
                    name='checkBox'
                    value='{$value}' {$isCheckedAttribute}>";
    }


    public static function checkBoxArrayMasterControl(){
        return "<input type='checkbox' id='selectAllBoxes'>";
    }


    public static function checkBoxArrayChildControl($item_id, $postTarget = null){
        // Dynamic preparation
        $value = $postTarget? $postTarget . '&' . $item_id : $item_id;
        $dynamicClass = $postTarget? ' ' . 'checkBoxes-dynamic' : '';

        return "<input type='checkbox' class='checkBoxes-child{$dynamicClass}'
                    name='checkBoxArray[]'
                    value='{$value}'>";
    }


    public static function spinBoxControlFuelUx($item_id, $postTarget = null, $currentValue = 0){
        $value = $postTarget? $postTarget . '&' . $item_id : $item_id;

        return "<div class='spinbox' data-initialize='spinbox' id='mySpinbox'>
                  <input type='text' class='form-control input-mini spinbox-input'
                        name='spinBox&{$value}'
                        value='{$currentValue}'>
                  <div class='spinbox-buttons btn-group btn-group-vertical'>
                    <button type='button' class='btn btn-default spinbox-up btn-xs'>
                      <span class='glyphicon glyphicon-chevron-up'></span><span class='sr-only'>Increase</span>
                    </button>
                    <button type='button' class='btn btn-default spinbox-down btn-xs'>
                      <span class='glyphicon glyphicon-chevron-down'></span><span class='sr-only'>Decrease</span>
                    </button>
                  </div>
                </div>";
    }


    public static function selectionControlFuelUx($item_id, array $values, $currentKeyOrValue,
                                                  $control_id = '', $postTarget = null, $isCurrentKey = false){
        // Dynamic preparation
        $valueBase = $postTarget? $postTarget . '&' . $item_id : $item_id;

        $controlStream = "<div class='btn-group selectlist' data-resize='auto' data-initialize='selectlist' id='{$control_id}'>
                              <button class='btn btn-default dropdown-toggle' data-toggle='dropdown' type='button'>
                                <span class='selected-label'>&nbsp;</span>
                                <span class='caret'></span>
                                <span class='sr-only'>Toggle Dropdown</span>
                              </button>
                              <ul class='dropdown-menu' role='menu'>";
        foreach ($values as $listKey => $listValue){
            $value = $valueBase . '&' . $listKey;
            if ($isCurrentKey){
                $selectedAttribute = ($listKey == $currentKeyOrValue)? 'data-selected="true"' : '';
            } else {
                $selectedAttribute = ($listValue == $currentKeyOrValue)? 'data-selected="true"' : '';
            }
            $controlStream .= "<li data-value='{$value}' {$selectedAttribute}><a href='#'>{$listValue}</a></li>";
        }
        $controlStream .= "        </ul>
                                <input class='hidden hidden-field' name='mySelectlist' readonly='readonly' aria-hidden='true' type='text'/>
                            </div>";
        return $controlStream;
    }


    public static function selectionControl($item_id, array $values, $currentKeyOrValue,
                                            $control_name, $control_id = '', $postTarget = null, $isCurrentKey = false){
        // Dynamic preparation
        $valueBase = $postTarget? $postTarget . '&' . $item_id : $item_id;
        $dynamicClass = $postTarget? 'selection-dynamic' : '';

        $controlStream = "<select name='{$control_name}' id='{$control_id}' class='{$dynamicClass}'>";
        foreach ($values as $listKey => $listValue){
            if ($isCurrentKey){
                $selected = ($listKey == $currentKeyOrValue)? 'selected' : '';
            } else {
                $selected = ($listValue == $currentKeyOrValue)? 'selected' : '';
            }
            $value = $valueBase . '&' . $listKey;

            $controlStream .= "<option value='{$value}' {$selected}>{$listValue}</option>";
        }
        $controlStream .= "</select>";
        return $controlStream;
    }

    /**
     * @param string $name Name associated with the uploaded file. This is used in $_FILES['name'] in later methods.
     * @param string $imageName Name of the image file to display.
     * @param string $path Path to the image file to display.
     * @param null|int $imageWidth If not specified, a default of 100px will be used.
     * @param null|int $imageHeight If not specified, a default height proportional to 100px width will be used.
     * @param null|int $maxUploadSize Maximum file upload size, in MB, unless greater than the system-enforced limit.
     * @param bool $isRequired
     * @return string
     */
    public static function uploadFileControl($name, $imageName, $path = "images/", $imageWidth = null, $imageHeight = null, $maxUploadSize = null, $isRequired = true){
        // Note: This must be placed in a post form with enctype="multipart/form-data"
        // See ini_get('post_max_size');  for max post size.
        // The upload module limits the size of a single attachment to be less than either post_max_size, or upload_max_filesize, whichever is smaller.
        // The default PHP values are 2 MB for upload_max_filesize, and 8 MB for post_max_size.
        // To override, set the following values in a .htaccess file:
        // php_value upload_max_filesize 10M
        // php_value post_max_size 10M

        $url = Config::get('URL');
        $maxSystemUploadSize = str_replace('M', '', ini_get('upload_max_filesize'));
        $maxUploadSize = $maxUploadSize? min($maxUploadSize, $maxSystemUploadSize) : $maxSystemUploadSize;
        $requiredAttribute = $isRequired? 'required' : '';

        $controlStream = '';
        if (!empty($imageName)){
            $imageWidthAttribute = $imageWidth? "width={$imageWidth}" : '';
            $imageHeightAttribute = $imageHeight? "width={$imageHeight}" : '';

            // Set default image width if no dimensions are given
            if (!$imageWidthAttribute && !$imageHeightAttribute){
                $imageWidth = 100;
                $imageWidthAttribute = "width={$imageWidth}";
            }

            $controlStream .= "<img src='{$url}{$path}{$imageName}' {$imageWidthAttribute} {$imageHeightAttribute}' />";
        }
        $controlStream .=  "<input type='file' name='{$name}' {$requiredAttribute}>
                            (Max Size: {$maxUploadSize}M)";
        // Scale file size limit up to bytes
        $maxUploadSize *= 1000000;
        $controlStream .= "<input type='hidden' name='MAX_FILE_SIZE' value='{$maxUploadSize}' />";
        return $controlStream;
    }

} 