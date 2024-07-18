<?php

namespace FDTStudio\UploadExtOnedrive\Templates;

use FoF\Upload\File;
use FoF\Upload\Templates\AbstractTextFormatterTemplate;
use Illuminate\Contracts\View\View;

class OnedriveAudioTemplate extends AbstractTextFormatterTemplate
{
    public const templateName = 'upl-onedrive-audio';

    /**
     * @var string
     */
    protected $tag = 'onedrive-audio';

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return $this->trans('fdt-studio-fof-upload-onedrive.admin.template.audio.name');
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return $this->trans('fdt-studio-fof-upload-onedrive.admin.template.audio.description');
    }

    /**
     * The xsl template to use with this tag.
     *
     * @return View
     */
    public function template(): View
    {
        return $this->getView('fdt-studio-fof-upload-onedrive.templates::onedrive-audio');
    }

    /**
     * The bbcode to be parsed.
     *
     * @return string
     */
    public function bbcode(): string
    {
        return '[upl-onedrive-audio uuid={IDENTIFIER}]';
    }

    public function preview(File $file): string
    {
        return "[upl-onedrive-audio uuid=$file->uuid]";
    }
}
