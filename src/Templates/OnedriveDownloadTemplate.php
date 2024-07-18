<?php

namespace FDTStudio\UploadExtOnedrive\Templates;

use FoF\Upload\File;
use FoF\Upload\Templates\AbstractTextFormatterTemplate;
use Illuminate\Contracts\View\View;

class OnedriveDownloadTemplate extends AbstractTextFormatterTemplate
{
    public const templateName = 'upl-onedrive-download';

    /**
     * @var string
     */
    protected $tag = 'onedrive-download';

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return $this->trans('fdt-studio-fof-upload-onedrive.admin.template.download.name');
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return $this->trans('fdt-studio-fof-upload-onedrive.admin.template.download.description');
    }

    /**
     * {@inheritdoc}
     */
    public function template(): View
    {
        return $this->getView('fdt-studio-fof-upload-onedrive.templates::onedrive-download');
    }

    /**
     * {@inheritdoc}
     */
    public function bbcode(): string
    {
        return '[upl-onedrive-download uuid={IDENTIFIER} name={SIMPLETEXT} size={SIMPLETEXT2}]';
    }

    public function preview(File $file): string
    {
        return "[upl-onedrive-download uuid=$file->uuid name=$file->base_name size=$file->humanSize]";
    }
}
