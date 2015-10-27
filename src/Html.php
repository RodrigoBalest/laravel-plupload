<?php


namespace Jenky\LaravelPlupload;

class Html
{
    /**
     * @var bool
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $id;

    /**
     * @var bool
     */
    protected $autoStart = false;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $pickFilesButton;
    
    /**
     * @var string
     */
    protected $pickFilesButtonLabel = 'Browse';

    /**
     * @var string
     */
    protected $uploadButton;
    
    /**
     * @var string
     */
    protected $uploadButtonLabel = 'Upload';

    public function __construct($id, $url)
    {
        $this->initDefaultOptions();

        $this->id = $id;
        $this->options['url'] = $url;
    }

    /**
     * Set default uploader options.
     */
    protected function initDefaultOptions()
    {
        $this->options = array_except(config('plupload'), ['chunk_path']);
    }

    /**
     * Set default uploader buttons.
     * 
     * @param array $options
     * 
     * @return void
     */
    protected function initDefaultButtons(array $options)
    {
        if (!$this->pickFilesButton) {
            $this->pickFilesButton = '
				<a class="btn btn-primary btn-browse" id="'.$options['browse_button'].'" href="javascript:;">
					<i class="fa fa-file"></i> ' . $this->pickFilesButtonLabel . '
				</a>
			';
        }

        if (!$this->uploadButton) {
            $this->uploadButton = '
				<a class="btn btn-default btn-upload" id="uploader-'.$this->id.'-upload" href="javascript:;">
					<i class="fa fa-upload"></i> ' . $this->uploadButtonLabel . '
				</a>
			';
        }
    }

    protected function init()
    {
        if (empty($this->options['url'])) {
            throw new Exception('Missing URL option.', 1);
        }

        $options = [];

        if (empty($this->options['browse_button'])) {
            $options['browse_button'] = 'uploader-'.$this->id.'-pickfiles';
        }

        if (empty($this->options['container'])) {
            $options['container'] = 'uploader-'.$this->id.'-container';
        }

        $options = array_merge($this->options, $options);

        // csrf token
        $options['multipart_params']['_token'] = csrf_token();

        $this->initDefaultButtons($options);

        $id = $this->id;
        $autoStart = $this->autoStart;
        $buttons = [
            'pickFiles' => $this->pickFilesButton,
            'upload'    => $this->uploadButton,
        ];

        $this->data = array_merge($this->data, compact('options', 'id', 'autoStart', 'buttons'));

        return $this->data;
    }

    /**
     * Set uploader auto start.
     * 
     * @param bool $bool
     * 
     * @return void
     */
    public function setAutoStart($bool)
    {
        $this->autoStart = (bool) $bool;

        return $this;
    }

    /**
     * Set uploader options.
     *
     * @see https://github.com/moxiecode/plupload/wiki/Options
     * 
     * @param array $options
     * 
     * @return void
     */
    public function setOptions(array $options)
    {
        $options = array_except($options, ['url']);
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Set uploader pick files button.
     * 
     * @param string $button
     * 
     * @return void
     */
    public function setPickFilesButton($button)
    {
        $this->pickFilesButton = $button;

        return $this;
    }
    
    public function setPickFilesButtonLabel($label)
    {
        $this->pickFilesButtonLabel = $label;

        return $this;
    }

    /**
     * Set uploader upload button.
     * 
     * @param string $button
     * 
     * @return void
     */
    public function setUploadButton($button)
    {
        $this->uploadButton = $button;

        return $this;
    }
    
    public function setUploadButtonLabel($label)
    {
        $this->uploadButtonLabel = $label;

        return $this;
    }

    /**	 
     * Set uploader custom params.
     * 
     * @param array $params
     * 
     * @return void
     */
    public function setCustomParams(array $params)
    {
        $this->data['params'] = $params;

        return $this;
    }

    public function render($view = 'plupload::uploader', array $extra = [])
    {
        $this->init();

        return view($view, array_merge($this->data, $extra));
    }
}
