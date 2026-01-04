<?php

namespace app\modules\story;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use yii\base\Component;
use yii\helpers\Json;

class StoryService extends Component
{
    private $client;
    private $apiUrl;

    public function __construct($apiUrl, $config = [])
    {
        $this->apiUrl = $apiUrl;
        $this->client = new Client([
            'base_uri' => $apiUrl,
            'timeout'  => 60.0,
        ]);
        parent::__construct($config);
    }

    /**
     * Sends request to Python API and returns the stream resource.
     *
     * @param int $age
     * @param string $language
     * @param array $characters
     * @return \Psr\Http\Message\StreamInterface
     * @throws \Exception
     */
    public function generateStoryStream(int $age, string $language, array $characters)
    {
        try {
            $response = $this->client->post('/story/generate', [
                'json' => [
                    'age' => $age,
                    'language' => $language,
                    'characters' => $characters,
                ],
                'stream' => true,
            ]);

            return $response->getBody();
        } catch (RequestException $e) {
            throw new \Exception("Error connecting to AI service: " . $e->getMessage());
        }
    }
}