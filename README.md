# CakePHP file upload plugin

A simple CakePHP plugin to add file upload functionality when saving models.

## Installation

To install, either clone this repository or add it as a submodule to your project.
Both are done in a command line client from the root of your project.

### Cloning the repository

The simplest way is to clone the repository. The command for this is:

    $ git clone git@github.com:martinbean/cakephp-file-upload-plugin.git app/Plugin/FileUpload

### Adding as a submodule

Alternatively, you can add the plugin as a submodule to your project if it’s already version-controlled with Git.
To do this, run the following commands:

    $ git submodule add git@github.com:martinbean/cakephp-file-upload-plugin.git app/Plugin/FileUpload
    $ git submodule init

For more information on submodules in Git, read http://git-scm.com/book/en/Git-Tools-Submodules.

## Using the Behavior

To use the behavior, first enable the plugin in your **app/Config/bootstrap.php** file by adding the following line to the bottom:

```php
CakePlugin::load('FileUpload');
```

Alternatively, you can just use the following if you have many plugins:

```php
CakePlugin::loadAll();
```

Then use it in your models:

```php
<?php
class Article extends AppModel {
    
    public $actsAs = array(
        'FileUpload.FileUpload'
    );
}
```

## Configuration

The behavior has some options available:

* **allowedExtensions:** (array) an array of valid file extensions
* **field:** (string) the name of the field in the HTML form
* **required:** (boolean) whether a field is needed for the model to validate
* **uploadDir:** (string) path of upload directory, relative to webroot and ending with DS constant

You can modify these options when you attach the behavior to your models:

```php
class Article extends AppModel {
    
    public $actsAs = array(
        'FileUpload.FileUpload' => array(
            'allowedExtensions' => array('gif', 'jpeg', 'jpg', 'png'),
            'field' => 'image',
            'required' => true,
            'uploadDir' => 'files' . DS
        )
    );
}
```

You also need to define a method called `generateFilename()` in your model that accepts one parameter.
This will be the model data to be saved, so you can generate a filename from one of your model‘s other fields, or just the file’s original name.

An example that saves the file as originally named:

```php
class Article extends AppModel {
    
    public $actsAs = array(
        'FileUpload.FileUpload'
    );
    
    public function generateFilename($data) {
        return $data['image']['name'];
    }
}
```

Or if you wanted to name your image upload after another field, this is how you could do it:

```php
class Article extends AppModel {
    
    public $actsAs = array(
        'FileUpload.FileUpload'
    );
    
    public function generateFilename($data) {
        $slug = Inflector::slug(strtolower($data['title']), '-');
        $file = new File($data['name']);
        
        return sprintf('%s.%s', $slug, $file->ext());
    }
}
```

If you have any issues with this plugin then please feel free to [create a new Issue](https://github.com/martinbean/cakephp-file-upload-plugin/issues/new) on the [GitHub repository](https://github.com/martinbean/cakephp-sluggable-plugin/).
This plugin is licensed under the [MIT License](http://opensource.org/licenses/MIT).