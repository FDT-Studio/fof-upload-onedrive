<?php

namespace FDTStudio\UploadExtOnedrive\Adapters;

use Carbon\Carbon;
use FoF\Upload\Contracts\UploadAdapter;
use FoF\Upload\File;
use FDTStudio\UploadExtOnedrive\Configuration\OnedriveConfiguration;
use GuzzleHttp\Psr7\Request;
use Flarum\Settings\SettingsRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OnedriveFofAdapter implements UploadAdapter
{
    private OnedriveConfiguration $pluginConfig;

    public function __construct()
    {
        $this->pluginConfig = new OnedriveConfiguration();
    }

    /**
     * Attempt to upload to the (remote) filesystem.
     *
     * @param File $file
     * @param UploadedFile|null $upload
     * @param string $contents
     *
     * @return File|bool
     */
    public function upload(File $file, ?UploadedFile $upload, $contents): bool|File
    {
        $this->generateFilename($file);
        $url = $this->pluginConfig->generateUrl(
            $this->pluginConfig->rootPath."/".$file->path);

        $file->url = $this->pluginConfig->baseUrl."/api/fof-upload-onedrive/download/".$file->uuid;

        $response = $this->pluginConfig->api->request(
            "PUT",
            $url . "/content",
            [
                'headers' => [
                    "Authorization" => "Bearer {$this->pluginConfig->accessToken}"
                ],
                'body' => $contents
            ]);

        if ($response->getStatusCode() != 201) {
            $result = json_decode($response->getBody(), true);
            if (!is_array($result)) {
                return false;
            }
            if ($result["code"] == "InvalidAuthenticationToken") {
                $this->pluginConfig->getAccessToken();
                $response = $this->pluginConfig->api->request(
                    "PUT",
                    $url . "/content",
                    [
                        'headers' => [
                            "Authorization" => "Bearer {$this->pluginConfig->accessToken}"
                        ],
                        'body' => $contents
                    ]);
                if ($response->getStatusCode() != 201) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return $file;
    }

    protected function generateFilename(File $file): void
    {
        $today = (new Carbon());

        $file->path = sprintf(
            '%s%s%s',
            $today->toDateString(),
            '/',
            $today->timestamp.'-'.$today->micro.'-'.$file->base_name
        );
    }

    /**
     * In case deletion is not possible, return false.
     *
     * @param File $file
     *
     * @return File|bool
     */
    public function delete(File $file): bool|File
    {
        $url = $this->pluginConfig->generateUrl(
            $this->pluginConfig->rootPath."/".$file->path);
        $response = $this->pluginConfig->api->request(
            "DELETE",
            $url,
            [
                'headers' => [
                    "Authorization" => "Bearer {$this->pluginConfig->accessToken}"
                ],
            ]);

        if ($response->getStatusCode() != 204) {
            $result = json_decode($response->getBody(), true);
            if (!is_array($result)) {
                return false;
            }
            if ($result["code"] == "InvalidAuthenticationToken") {
                $this->pluginConfig->getAccessToken();
                $response = $this->pluginConfig->api->request(
                    "DELETE",
                    $url,
                    [
                        'headers' => [
                            "Authorization" => "Bearer {$this->pluginConfig->accessToken}"
                        ],
                    ]);
                if ($response->getStatusCode() != 204) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return $file;
    }

    /**
     * Whether the upload adapter works on a specific mime type.
     *
     * @param string $mime
     *
     * @return bool
     */
    public function forMime($mime): bool
    {
        // We allow all, no checking.
        return true;
    }

    /**
     * @return bool
     */
    public function supportsStreams(): bool
    {
        return true;
    }
}
