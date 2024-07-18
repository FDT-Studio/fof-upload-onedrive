<?php

namespace FDTStudio\UploadExtOnedrive\Templates;

use FoF\Upload\File;
use FoF\Upload\Templates\AbstractTextFormatterTemplate;
use Illuminate\Contracts\View\View;

class OnedrivePreviewTemplate extends AbstractTextFormatterTemplate
{
    public const templateName = 'upl-onedrive-preview';

    /**
     * @var string
     */
    protected $tag = 'onedrive-preview';

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return $this->trans('fdt-studio-fof-upload-onedrive.admin.template.image-preview.name');
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return $this->trans('fdt-studio-fof-upload-onedrive.admin.template.image-preview.description');
    }

    /**
     * {@inheritdoc}
     */
    public function template(): View
    {
        return $this->getView('fdt-studio-fof-upload-onedrive.templates::onedrive-preview');
    }

    /**
     * {@inheritdoc}
     */
    public function bbcode(): string
    {
        return '[upl-onedrive-preview uuid={IDENTIFIER} preview_uri={URL} fullscreen_uri={URL}]';
    }

    public function preview(File $file): string
    {
        return "[upl-onedrive-preview uuid=$file->uuid preview_uri={URL} fullscreen_uri={URL}]";
    }
}
