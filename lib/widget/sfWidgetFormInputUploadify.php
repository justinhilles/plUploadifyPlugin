<?php

/**
 * sfWidgetFormInputUploadify class
 * 
 * This provides file upload widget for file uploads with the Uploadify
 * javascript library.
 *
 * @package default
 * @author Chris LeBlanc <chris@webPragmatist.com>
 * @see 
 */
class sfWidgetFormInputUploadify extends sfWidgetFormInputFile
{
  /**
   * Instance counter
   *
   * @var integer
   */
  protected static $INSTANCE_COUNT = 0;

  /**
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormInput
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    
    $this->addOption('path', '/plUploadifyPlugin/vendor/uploadify');
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    return array(
      'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js',
      $this->getOption('path').'/jquery.uploadify.min.js',
    );
  }

  public function getStylesheets()
  {
    return array(
      $this->getOption('path') . '/uploadify.css' => 'all'
    );
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    self::$INSTANCE_COUNT++;

    $output = parent::render($name, $value, $attributes, $errors);

    $widget_id  = $this->getAttribute('id') ? $this->getAttribute('id') : $this->generateId($name);
    $session_name = ini_get('session.name');
    $session_id = session_id();
    $uploader = $this->getOption('path').'/uploadify.swf';
    $cancel_img = $this->getOption('path').'/cancel.png';
    $button_img = $this->getOption('path').'/browse-btn.png';
    
    $form = new BaseForm();
    $csrf_token = $form->getCSRFToken();
    $debug = sfConfig::get('sf_debug') ? 'true' : 'false';
    
    $output .= <<<EOF
      <div class="swfupload-buttontarget">
        <noscript>
          We're sorry.  SWFUpload could not load.  You must have JavaScript enabled to enjoy SWFUpload.
        </noscript>
      </div>
      <script type="text/javascript">
        //<![CDATA[
          var f = jQuery('#$widget_id').closest('form');

          jQuery('#$widget_id').uploadify({
            swf             : '$uploader',
            uploader        : f.attr('action'),
            debug           : $debug,
            buttonImage     : '$button_img',
            fileObjName     : 'upload[photos]',
            formData        : {'$session_name':'$session_id', '_csrf_token':'$csrf_token'},
            onUploadSuccess: function(){
              location.reload(); 
            }
          });
        //]]>
      </script>
EOF;
    return $output;
  }
}