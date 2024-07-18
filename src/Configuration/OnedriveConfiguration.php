<?php

namespace FDTStudio\UploadExtOnedrive\Configuration;

use Exception;
use Flarum\Settings\SettingsRepositoryInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client as Guzzle;
use Psr\Http\Message\ResponseInterface;

class OnedriveConfiguration
{
    /**
     * @var string
     */
    public string $email;
    public string $clientId;
    public string $region;
    public string $clientKey;
    public string $tenantId;
    public string $rootPath;
    public string $baseUrl;
    public string $oauthApi;
    public string $commonApi;
    public string $accessToken;

    public Guzzle $api;

    public function __construct()
    {
        /**
         * @var SettingsRepositoryInterface $settings
         */
        $settings = app(SettingsRepositoryInterface::class);
        $this->api = new Guzzle(['verify' => false]);

        $this->email = $settings->get('fdt-studio-fof-upload-onedrive.onedriveConfig.email');
        $this->region = $settings->get('fdt-studio-fof-upload-onedrive.onedriveConfig.region', 'global');
        $this->clientId = $settings->get('fdt-studio-fof-upload-onedrive.onedriveConfig.clientId');
        $this->clientKey = $settings->get('fdt-studio-fof-upload-onedrive.onedriveConfig.clientKey');
        $this->tenantId = $settings->get('fdt-studio-fof-upload-onedrive.onedriveConfig.tenantId');
        $this->rootPath = $settings->get("fdt-studio-fof-upload-onedrive.onedriveConfig.rootPath");
        $this->baseUrl = $settings->get("fdt-studio-fof-upload-onedrive.onedriveConfig.baseUrl");

        if ($this->region == null || strlen($this->region) == 0) {
            $this->region = 'global';
        }
        $this->oauthApi = $this->getRegionUrl($this->region)["oauth"];
        $this->commonApi = $this->getRegionUrl($this->region)["api"];
        $this->getAccessToken();
    }


    /**
     * @param $path string
     *
     * @return string
     */
    public function generateUrl(string $path): string
    {
        if ($path == "/" || $path == "\\") {
            return sprintf("%s/v1.0/users/%s/drive/root", $this->commonApi, $this->email);
        }
        return sprintf("%s/v1.0/users/%s/drive/root:%s:", $this->commonApi, $this->email, $path);
    }

    /**
     * @return array(oauth, api)
     */
    public function getRegionUrl(string $region): array
    {
        switch ($region) {
            case "cn":
                return array(
                    "oauth" => "https://login.chinacloudapi.cn",
                    "api" => "https://microsoftgraph.chinacloudapi.cn",
                );
            case "us":
                return array(
                    "oauth" => "https://login.microsoftonline.us",
                    "api" => "https://graph.microsoft.us",
                );
            case "de":
                return array(
                    "oauth" => "https://login.microsoftonline.de",
                    "api" => "https://graph.microsoft.de",
                );
            case "global":
            default:
                return array(
                    "oauth" => "https://login.microsoftonline.com",
                    "api" => "https://graph.microsoft.com",
                );
        }
    }

    public function getAccessToken(): bool {
        $response = $this->api->request('POST',
            $this->oauthApi."/".$this->tenantId."/oauth2/token", [
                'form_params' => [
                    "grant_type"=>    "client_credentials",
                    "client_id"=>    $this->clientId,
                    "client_secret"=> $this->clientKey,
                    "resource"=>      $this->commonApi,
                    "scope"=> $this->commonApi ."/.default",
                ]
            ]);
        $result = json_decode($response->getBody(), true);
        if (!is_array($result)) {
            return false;
        }
        $this->accessToken = $result["access_token"];

        if ($this->accessToken == "") {
            return false;
        }

        return true;
    }

}
