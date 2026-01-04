<?php

namespace app\modules\story\controllers;

use app\modules\story\models\GenerateStoryForm;
use app\modules\story\models\StoryHistory;
use app\modules\story\StoryService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `story` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the generation form.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new GenerateStoryForm();
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Proxies the request to Python API and streams the response.
     * Saves the result to DB upon completion.
     */
    public function actionGenerate()
    {
        $request = Yii::$app->request;
        $model = new GenerateStoryForm();

        if ($request->isPost && $model->load($request->post()) && $model->validate()) {

            // Disable buffering for streaming
            if (ob_get_level()) ob_end_clean();

            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');

            $module = $this->module;
            $service = new StoryService($module->pythonApiUrl);

            $fullContent = '';

            try {
                $stream = $service->generateStoryStream(
                    $model->age,
                    $model->language,
                    $model->characters
                );

                while (!$stream->eof()) {
                    $chunk = $stream->read(1024);
                    $fullContent .= $chunk;

                    // Format as Server-Sent Events (SSE) data
                    echo "data: " . json_encode(['text' => $chunk]) . "\n\n";
                    flush();
                }

                // Save to History
                $history = new StoryHistory();
                $history->age = $model->age;
                $history->language = $model->language;
                $history->characters = $model->characters;
                $history->content = $fullContent;
                $history->save();

                echo "data: [DONE]\n\n";
                flush();

            } catch (\Exception $e) {
                echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
                flush();
            }
            exit();
        }

        return $this->redirect(['index']);
    }

    /**
     * Lists historical generations.
     *
     * @return string
     */
    public function actionHistory()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => StoryHistory::find()->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('history', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single history item.
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = StoryHistory::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }
}