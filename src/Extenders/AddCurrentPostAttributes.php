<?php

namespace FDTStudio\UploadExtOnedrive\Extenders;

use Exception;
use Flarum\Api\Serializer\PostSerializer;
use Flarum\Post\Post;
use FoF\Upload\Repositories\FileRepository;
use FDTStudio\UploadExtOnedrive\Configuration\OnedriveConfiguration;
use FDTStudio\UploadExtOnedrive\Templates\OnedriveAudioTemplate;
use FDTStudio\UploadExtOnedrive\Templates\OnedriveDownloadTemplate;
use FDTStudio\UploadExtOnedrive\Templates\OnedrivePdfTemplate;
use FDTStudio\UploadExtOnedrive\Templates\OnedrivePreviewTemplate;
use FDTStudio\UploadExtOnedrive\Templates\OnedriveVideoTemplate;
use Illuminate\Support\Str;

class AddCurrentPostAttributes
{
    /**
     * @var OnedriveConfiguration
     */
    private OnedriveConfiguration $config;

    /**
     * @var FileRepository
     */
    private FileRepository $file;

    public function __construct(OnedriveConfiguration $config, FileRepository $file)
    {
        $this->config = $config;
        $this->file = $file;
    }

    /**
     * @throws Exception
     */
    public function __invoke(PostSerializer $serializer, Post $post, array $attributes): array
    {
        $actor = $serializer->getActor();

        if ($actor->id != $post->user_id || !isset($attributes['content'])) {
            return $attributes;
        }

        if (!isset($attributes['contentType']) || ($attributes['contentType'] !== 'comment')) {
            return $attributes;
        }

        $content = $attributes['content'];
        $content = $this->replaceonedriveBBCode($content);
        $attributes['content'] = $content;

        return $attributes;
    }

    /**
     * @param $content
     *
     * @throws Exception
     *
     * @return string
     */
    private function replaceonedriveBBCode($content): string
    {
        $regexpr = '/\[upl-onedrive-((video|audio|download|pdf)-)?preview [^]]+]/i';

        return preg_replace_callback($regexpr, function ($s) {
            $s = $s[0];

            if (Str::startsWith($s, '[upl-onedrive-preview ')) {
                $feature = OnedrivePreviewTemplate::templateName;
            } elseif (Str::startsWith($s, '[upl-onedrive-video ')) {
                $feature = OnedriveVideoTemplate::templateName;
            } elseif (Str::startsWith($s, '[upl-onedrive-audio ')) {
                $feature = OnedriveAudioTemplate::templateName;
            } elseif (Str::startsWith($s, '[upl-onedrive-download ')) {
                $feature = OnedriveDownloadTemplate::templateName;
            } elseif (Str::startsWith($s, '[upl-onedrive-pdf ')) {
                $feature = OnedrivePdfTemplate::templateName;
            } else {
                return '';
            }

            $kvs = array_filter(explode(' ', $s), function ($it) {
                return Str::contains($it, '=');
            });
            $uuid = false;

            foreach ($kvs as $item) {
                if (Str::startsWith($item, 'uuid=')) {
                    $uuid = substr($item, 5);
                }
            }

            if ($uuid === false) {
                return '';
            }

            $file = $this->file->findByUuid($uuid);
            if ($file == null) {
                return '';
            }

            $uuid = $file->uuid;
            $filename = $file->base_name;
            $fullscreenUri = 'place-holder';

            if ($feature == OnedrivePreviewTemplate::templateName) {
                $previewUri = $this->config->generateUrl($file);
                $fullscreenUri = $this->config->generateUrl($file);

                return "[${feature} uuid=${uuid} preview_uri=${previewUri} fullscreen_uri=${fullscreenUri}]";
            } elseif ($feature == OnedriveVideoTemplate::templateName) {
                $previewUri = $this->config->generateUrl($file);

                return "[${feature} uuid=${uuid} preview_uri=${previewUri} fullscreen_uri=${fullscreenUri} base_name=${filename}]";
            } elseif ($feature == OnedriveAudioTemplate::templateName) {
                $previewUri = $this->config->generateUrl($file);

                return "[${feature} uuid=${uuid} preview_uri=${previewUri} fullscreen_uri=${fullscreenUri} base_name=${filename}]";
            } elseif ($feature == OnedriveDownloadTemplate::templateName) {
                $size = $file->humanSize;

                return "[$feature uuid={$file->uuid} name=${filename} size={$size}]";
            } elseif ($feature == OnedrivePdfTemplate::templateName) {
                $previewUri = $this->config->generateUrl($file);

                return "[${feature} uuid=${uuid} preview_uri=${previewUri} fullscreen_uri=${fullscreenUri} base_name=${filename}]";
            } else {
                return '';
            }
        }, $content);
    }
}
