<?php

namespace FDTStudio\UploadExtOnedrive\Api\Controllers;

use Exception;
use Flarum\Post\PostRepository;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\NotAuthenticatedException;
use FoF\Upload\Api\Serializers\FileSerializer;
use FoF\Upload\Repositories\FileRepository;
use FDTStudio\UploadExtOnedrive\Configuration\OnedriveConfiguration;
use GuzzleHttp\Psr7\Request;
use http\Env\Response;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\RedirectResponse;
use League\Flysystem\FileNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DownloadController implements RequestHandlerInterface
{
    public $serializer = FileSerializer::class;

    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @var PostRepository
     */
    private $posts;

    /**
     * @var SettingsRepositoryInterface
     */
    private $settings;

    /**
     * @var FileRepository
     */
    private $files;

    /**
     * @var OnedriveConfiguration
     */
    private OnedriveConfiguration $pluginConfig;

    public function __construct(Dispatcher $bus, PostRepository $posts, SettingsRepositoryInterface $settings, FileRepository $files, OnedriveConfiguration $pluginConfig)
    {
        $this->bus = $bus;
        $this->posts = $posts;
        $this->settings = $settings;
        $this->files = $files;
        $this->pluginConfig = $pluginConfig;
    }

    /**
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $uuid = Arr::get($request->getQueryParams(), 'uuid');

        $file = $this->files->findByUuid($uuid);
        if ($file == null) {
            throw new ModelNotFoundException();
        }

        $url = $this->pluginConfig->generateUrl(
            $this->pluginConfig->rootPath."/".$file->path);

        $response = $this->pluginConfig->api->request(
            "GET",
            $url,
            [
                'headers' => [
                    "Authorization" => "Bearer {$this->pluginConfig->accessToken}"
                ],
            ]);
        if ($response->getStatusCode() != 200) {
            $result = json_decode($response->getBody(), true);
            if (!is_array($result)) {
                throw new ModelNotFoundException();
            }
            if ($result["code"] == "InvalidAuthenticationToken") {
                $this->pluginConfig->getAccessToken();
                $response = $this->pluginConfig->api->request(
                    "GET",
                    $url,
                    [
                        'headers' => [
                            "Authorization" => "Bearer {$this->pluginConfig->accessToken}"
                        ],
                    ]);
                if ($response->getStatusCode() != 200) {
                    throw new ModelNotFoundException();
                }

                $result = json_decode($response->getBody(), true);
                if (!is_array($result)) {
                    throw new ModelNotFoundException();
                }
                return new RedirectResponse($result["@microsoft.graph.downloadUrl"]);
            } else {
                throw new ModelNotFoundException();
            }
        }

        $result = json_decode($response->getBody(), true);
        if (!is_array($result)) {
            throw new ModelNotFoundException();
        }

        return new RedirectResponse($result["@microsoft.graph.downloadUrl"]);
    }
}
