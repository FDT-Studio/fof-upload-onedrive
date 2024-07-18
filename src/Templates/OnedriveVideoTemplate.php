<?php

namespace FDTStudio\UploadExtOnedrive\Templates;

use FoF\Upload\File;
use FoF\Upload\Templates\AbstractTextFormatterTemplate;
use Illuminate\Contracts\View\View;

class OnedriveVideoTemplate extends AbstractTextFormatterTemplate
{
    public const templateName = 'upl-onedrive-video';

    /**
     * @var string
     */
    protected $tag = 'onedrive-video';

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return $this->trans('fdt-studio-fof-upload-onedrive.admin.template.video-preview.name');
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return $this->trans('fdt-studio-fof-upload-onedrive.admin.template.video-preview.description');
    }

    /**
     * The xsl template to use with this tag.
     *
     * @return string
     */
    public function template(): View
    {
        return $this->getView('fdt-studio-fof-upload-onedrive.templates::onedrive-video');
    }

    /**
     * The bbcode to be parsed.
     *
     * @return string
     */
    public function bbcode(): string
    {
        return '[upl-onedrive-video uuid={IDENTIFIER} preview_uri={URL} fullscreen_uri={URL}]';
    }

    public function preview(File $file): string
    {
        return "[upl-onedrive-video uuid=$file->uuid preview_uri={URL} fullscreen_uri={URL}]";
    }
}
