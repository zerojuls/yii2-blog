<?php

namespace akiraz2\blog\controllers\backend;

use akiraz2\blog\controllers\backend\BaseAdminController;
use yii\web\Response;

class UploadController extends BaseAdminController
{
    //public $module = 'redactorBlog';

    public $enableCsrfValidation = false;

    public function init()
    {
        $this->module = \Yii::$app->getModule(\Yii::$app->getModule('blog')->redactorModule);
        parent::init(); // TODO: Change the autogenerated stub
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ],
            ]
        ];
    }

    public function actions()
    {
        return [
            'file' => 'yii\redactor\actions\FileUploadAction',
            'image' => 'yii\redactor\actions\ImageUploadAction',
            'image-json' => 'yii\redactor\actions\ImageManagerJsonAction',
            'file-json' => 'yii\redactor\actions\FileManagerJsonAction',
        ];
    }

}
