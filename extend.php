<?php

/*
 * This file is part of fdt-studio/fof-upload-onedrive.
 *
 * Copyright (c) 2024 FDTStudio.
 *
 */

namespace FDTStudio\UploadExtOnedrive;

use Flarum\Api\Serializer\PostSerializer;
use Flarum\Extend;
use FoF\Upload\Events\Adapter\Collecting;
use FoF\Upload\Events\Adapter\Instantiate;
use FDTStudio\UploadExtOnedrive\Formatters\OnedriveAudioFormatter;
use FDTStudio\UploadExtOnedrive\Formatters\OnedriveDownloadFormatter;
use FDTStudio\UploadExtOnedrive\Formatters\OnedrivePdfFormatter;
use FDTStudio\UploadExtOnedrive\Formatters\OnedrivePreviewFormatter;
use FDTStudio\UploadExtOnedrive\Formatters\OnedriveVideoFormatter;
use FDTStudio\UploadExtOnedrive\Listeners\AdapterInstantiateListener;
use FDTStudio\UploadExtOnedrive\Listeners\AdapterRegisterListener;
use FDTStudio\UploadExtOnedrive\Providers\OnedriveProvider;

return [
    (new Extend\Routes('api'))
        ->get('/fof-upload-onedrive/download/{uuid}/{post}/{csrf}', 'fof-upload-onedrive.download', Api\Controllers\DownloadController::class),

    (new Extend\Event())
        ->listen(Collecting::class, AdapterRegisterListener::class)
        ->listen(Instantiate::class, AdapterInstantiateListener::class),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),

    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\View())
        ->namespace('fdt-studio-fof-upload-onedrive.templates', __DIR__.'/resources/templates'),

    /*(new Extend\ApiSerializer(PostSerializer::class))
        ->attributes(Extenders\AddCurrentPostAttributes::class),*/

    (new Extend\ServiceProvider())
        ->register(OnedriveProvider::class),

    (new Extend\Formatter())
        ->render(OnedriveVideoFormatter::class)
        //->render(OnedriveAudioFormatter::class)
        ->render(OnedriveDownloadFormatter::class)
        ->render(OnedrivePreviewFormatter::class)
        //->render(OnedrivePdfFormatter::class),
];
