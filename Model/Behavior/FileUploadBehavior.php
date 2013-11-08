<?php
/**
 * Behavior for uploading files.
 * 
 * Licensed under the MIT License.
 * 
 * @author   Martin Bean <martin@martinbean.co.uk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('ModelBehavior', 'Model');

/**
 * File upload behavior class.
 */
class FileUploadBehavior extends ModelBehavior {

    /**
     * Initiate behavior for the model using specified settings.
     *
     * Available settings:
     *
     * - allowedExtensions: (array) an array of allowed file extensions
     * - field: (string) the name of the field in the HTML form
     * - required: (boolean) whether a field is needed for the model to validate
     * - uploadDir: (string) path of upload directory, relative to webroot and ending with DS constant
     *
     * @param Model $Model
     * @param array $settings
     * @return void
     */
    public function setup(Model $Model, $settings = array()) {
        if (!isset($this->settings[$Model->alias])) {
            $this->settings[$Model->alias] = array(
                'allowedExtensions' => array('gif', 'jpeg', 'jpg', 'png'),
                'field' => 'image',
                'required' => true,
                'uploadDir' => 'files' . DS
            );
        }
        $this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], $settings);
    }
    
    /**
     * Called before the model is validated.
     *
     * @param Model $Model
     * @param array $options
     * @return boolean
     */
    public function beforeValidate(Model $Model, $options = array()) {
        extract($this->settings[$Model->alias]);
        
        if ($required) {
            $Model->validator()->add($field, 'extension', array(
                'rule' => array('extension', $allowedExtensions),
                'message' => __('Please supply a valid file')
            ));
            $Model->validator()->add($field, 'uploadError', array(
                'rule' => 'uploadError',
                'message' => __('Something went wrong with the upload')
            ));
        }
        
        return true;
    }
    
    /**
     * Called before a model is saved.
     *
     * @param Model $Model
     * @param array $options
     * @param boolean
     */
    public function beforeSave(Model $Model, $options = array()) {
        extract($this->settings[$Model->alias]);
        
        $value = $Model->data[$Model->alias][$field];
        
        if (!empty($value['tmp_name'])) {
            $Model->data[$Model->alias][$field] = $this->moveUploadedFile($Model, $value);
        }
        else {
            if (!$required) {
                unset($Model->data[$Model->alias][$field]);
            }
        }
        
        return true;
    }
    
    /**
     * Called before a model is deleted.
     *
     * @param Model $Model
     * @param boolean $cascade
     * @return boolean
     */
    public function beforeDelete(Model $Model, $cascade = true) {
        $this->filename = $Model->field($this->settings[$Model->alias]['field']);
        
        return true;
    }
    
    /**
     * Called after a model is deleted.
     *
     * @param Model $Model
     * @return void
     */
    public function afterDelete(Model $Model) {
        $path = WWW_ROOT . $this->settings[$Model->alias]['uploadDir'] . $this->filename;
        
        $file = new File($path);
        $file->delete();
    }
    
    /**
     * Moves the uploaded file to the specified upload directory.
     *
     * @param Model $Model
     * @param array $file
     * @return string
     */
    private function moveUploadedFile(Model $Model, $file) {
        extract($this->settings[$Model->alias]);
        
        $filename = $Model->generateFilename($Model->data[$Model->alias]);
        $source = $file['tmp_name'];
        $destination = WWW_ROOT . $uploadDir . $filename;
        
        if (!move_uploaded_file($source, $destination)) {
            throw new CakeException( __('Error moving file'));
        }
        
        return $filename;
    }
}