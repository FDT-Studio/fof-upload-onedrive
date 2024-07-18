import app from 'flarum/app';

app.initializers.add('fdt-studio-fof-upload-onedrive', function (app) {

    const setting = (s) => app.translator.trans(`fdt-studio-fof-upload-onedrive.onedriveConfig.${s}`)
    const label = (s) => app.translator.trans(`fdt-studio-fof-upload-onedrive.admin.labels.onedriveLabel.${s}`)
    const help = (s) => app.translator.trans(`fdt-studio-fof-upload-onedrive.admin.labels.onedriveHelp.${s}`)

    app.extensionData
        .for('fdt-studio-fof-upload-onedrive')
        .registerSetting(
          {
            setting: setting('email'),
            label: label('email'),
            help: help('email'),
            type: 'text',
          },
          1010
        )
        .registerSetting(
            {
                setting: setting('clientId'),
                label: label('clientId'),
                help: help('clientId'),
                type: 'text',
            },
            1000
        )
        .registerSetting(
            {
                setting: setting('clientKey'),
                label: label('clientKey'),
                help: help('clientKey'),
                type: 'text',
            },
            990
        )
        .registerSetting(
          {
            setting: setting('tenantId'),
            label: label('tenantId'),
            help: help('tenantId'),
            type: 'text',
          },
          980
        )

        .registerSetting(
            {
                setting: setting('region'),
                label: label('region'),
                help: help('region'),
                type: 'select',
                options: {
                    'global': label('regionOption.global'),
                    'cn': label('regionOption.cn'),
                    'us': label('regionOption.us'),
                    'de': label('regionOption.de'),
                },
                default: 'global',
            },
            550
        )

        .registerSetting(
            {
                setting: setting('rootPath'),
                label: label('rootPath'),
                help: help('rootPath'),
                type: 'text',
            },
            530
        )
});
