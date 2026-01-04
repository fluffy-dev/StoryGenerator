<?php

namespace app\modules\story;

/**
 * story module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @var string URL to the Python service
     */
    public $pythonApiUrl;

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\story\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }
}