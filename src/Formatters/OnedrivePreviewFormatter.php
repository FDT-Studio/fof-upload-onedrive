<?php

namespace FDTStudio\UploadExtOnedrive\Formatters;

use FoF\Upload\Repositories\FileRepository;
use FDTStudio\UploadExtOnedrive\Configuration\OnedriveConfiguration;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laminas\Diactoros\Response\RedirectResponse;
use s9e\TextFormatter\Renderer;
use s9e\TextFormatter\Utils;

class OnedrivePreviewFormatter
{
    /**
     * @var FileRepository
     */
    private $files;

    /**
     * @var OnedriveConfiguration
     */
    private $config;

    public function __construct(FileRepository $files, OnedriveConfiguration $config)
    {
        $this->files = $files;
        $this->config = $config;
    }

    /**
     * Configure rendering for text preview uploads.
     *
     * @param Renderer $renderer
     * @param mixed    $context
     * @param string   $xml
     *
     * @return string $xml to be rendered
     */
    public function __invoke(Renderer $renderer, $context, string $xml)
    {
        return Utils::replaceAttributes($xml, 'UPL-ONEDRIVE-PREVIEW', function ($attributes) {
            $file = $this->files->findByUuid($attributes['uuid']);
            $url = $this->config->generateUrl(
                $this->config->rootPath."/".$file->path);
            $response = $this->config->api->request(
                "GET",
                $url,
                [
                    'headers' => [
                        "Authorization" => "Bearer {$this->config->accessToken}"
                    ],
                ]);

            if ($response->getStatusCode() != 200) {
                $result = json_decode($response->getBody(), true);
                if ($result["code"] == "InvalidAuthenticationToken") {
                    $this->config->getAccessToken();
                    $response = $this->config->api->request(
                        "GET",
                        $url,
                        [
                            'headers' => [
                                "Authorization" => "Bearer {$this->config->accessToken}"
                            ],
                        ]);
                    $result = json_decode($response->getBody(), true);

                    $attributes['preview_uri'] = $result["@microsoft.graph.downloadUrl"];
                    $attributes['fullscreen_uri'] = $result["@microsoft.graph.downloadUrl"];
                } else {
                    return $attributes;
                }
            } else {
                $result = json_decode($response->getBody(), true);
                $attributes['preview_uri'] = $result["@microsoft.graph.downloadUrl"];
                $attributes['fullscreen_uri'] = $result["@microsoft.graph.downloadUrl"];
            }

            $attributes['base_name'] = $file->base_name;

            return $attributes;
        });
    }
}
