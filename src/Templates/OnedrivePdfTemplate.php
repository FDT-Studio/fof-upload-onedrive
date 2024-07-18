<?php

namespace FDTStudio\UploadExtOnedrive\Templates;

use FoF\Upload\File;
use FoF\Upload\Templates\AbstractTextFormatterTemplate;
use Illuminate\Contracts\View\View;

class OnedrivePdfTemplate extends AbstractTextFormatterTemplate
{
    public const templateName = 'upl-onedrive-pdf';

    /**
     * @var string
     */
    protected $tag = 'onedrive-pdf';

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return $this->trans('fdt-studio-fof-upload-onedrive.admin.template.pdf.name');
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return $this->trans('fdt-studio-fof-upload-onedrive.admin.template.pdf.description');
    }

    /**
     * {@inheritdoc}
     */
    public function template(): View
    {
        return $this->getView('fdt-studio-fof-upload-onedrive.templates::onedrive-pdf');
    }

    /**
     * {@inheritdoc}
     */
    public function bbcode(): string
    {
        return '[upl-onedrive-pdf uuid={IDENTIFIER} preview_uri={URL} fullscreen_uri={URL}]';
    }

    public function preview(File $file): string
    {
        return "[upl-onedrive-pdf uuid=$file->uuid preview_uri={URL} fullscreen_uri={URL}]";
    }
}
