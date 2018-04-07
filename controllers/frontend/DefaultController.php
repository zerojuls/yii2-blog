<?php
/**
 * Project: yii2-blog for internal using
 * Author: akiraz2
 * Copyright (c) 2018.
 */

namespace akiraz2\blog\controllers\frontend;

use akiraz2\blog\models\BlogCommentSearch;
use akiraz2\blog\models\BlogPostSearch;
use akiraz2\blog\Module;
use akiraz2\blog\traits\IActiveStatus;
use akiraz2\blog\traits\ModuleTrait;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use akiraz2\blog\models\BlogCategory;
use akiraz2\blog\models\BlogPost;
use akiraz2\blog\models\BlogComment;
use akiraz2\blog\models\Status;
use akiraz2\blog\models\BlogTag;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;

class DefaultController extends Controller
{
    use ModuleTrait;

    public $mainMenu = [];
    //public $layout = 'main';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new BlogPostSearch();
        $searchModel->scenario= BlogPostSearch::SCENARIO_USER;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $categories = BlogCategory::find()->where(['status' => IActiveStatus::STATUS_ACTIVE, 'is_nav' => BlogCategory::IS_NAV_YES])
            ->orderBy(['sort_order' => SORT_ASC])->all();

        $cat_items = [];

        for ($i = 0; $i < count($categories); $i++) {
            $category = $categories[$i];
            $cat_items[] = [
                'label' => $category->title,
                'url' => ['default/index', 'category_id' => $category->id]
            ];
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'cat_items' => $cat_items
        ]);
    }

    public function actionView($id)
    {
        $post = BlogPost::find()->where(['status' => IActiveStatus::STATUS_ACTIVE,'id' => $id])->one();
        if($post===null)
            throw new NotFoundHttpException(Yii::t('yii','Page not found.'));

        $post->updateCounters(['click' => 1]);

        $searchModel = new BlogCommentSearch();
        $searchModel->scenario= BlogComment::SCENARIO_USER;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);


        $comment = new BlogComment();
        $comment->scenario=BlogComment::SCENARIO_USER;

        if ($comment->load(Yii::$app->request->post()) && $post->addComment($comment)) {
            Yii::$app->session->setFlash('success', Module::t('blog', 'A comment has been added and is awaiting validation'));
            return $this->redirect(['view', 'id' => $post->id, '#' => $comment->id]);
        }

        return $this->render('view', [
            'post' => $post,
            'dataProvider' => $dataProvider,
            'comment' => $comment,
        ]);
    }
}
