<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('l5-swagger.documentations.' . $documentation . '.api.title') }}</title>

    {{-- ✅ Assets sécurisés --}}
    <link rel="stylesheet" href="{{ secure_asset('docs/asset/swagger-ui.css') }}">
    <link rel="icon" type="image/png" href="{{ secure_asset('docs/asset/favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ secure_asset('docs/asset/favicon-16x16.png') }}" sizes="16x16"/>

    <style>
        html { box-sizing: border-box; overflow-y: scroll; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin: 0; background: #fafafa; }
    </style>

    @if(config('l5-swagger.defaults.ui.display.dark_mode'))
        {{-- ✅ Styles dark mode conservés --}}
        <style>
            body#dark-mode { background: #1b1b1b; }
            /* ... (tes styles dark mode ici, inchangés) ... */
        </style>
    @endif
</head>

<body @if(config('l5-swagger.defaults.ui.display.dark_mode')) id="dark-mode" @endif>
<div id="swagger-ui"></div>

{{-- ✅ Scripts sécurisés --}}
<script src="{{ secure_asset('docs/asset/swagger-ui-bundle.js') }}"></script>
<script src="{{ secure_asset('docs/asset/swagger-ui-standalone-preset.js') }}"></script>

<script>
    window.onload = function() {
        const ui = SwaggerUIBundle({
            dom_id: '#swagger-ui',
            url: "{!! $urlToDocs !!}",
            operationsSorter: {!! isset($operationsSorter) ? '"' . $operationsSorter . '"' : 'null' !!},
            configUrl: {!! isset($configUrl) ? '"' . $configUrl . '"' : 'null' !!},
            validatorUrl: {!! isset($validatorUrl) ? '"' . $validatorUrl . '"' : 'null' !!},
            oauth2RedirectUrl: "{{ route('l5-swagger.' . $documentation . '.oauth2_callback', [], $useAbsolutePath) }}",

            requestInterceptor: function(request) {
                request.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
                return request;
            },

            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],

            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],

            layout: "StandaloneLayout",
            docExpansion: "{!! config('l5-swagger.defaults.ui.display.doc_expansion', 'none') !!}",
            deepLinking: true,
            filter: {!! config('l5-swagger.defaults.ui.display.filter') ? 'true' : 'false' !!},
            persistAuthorization: "{!! config('l5-swagger.defaults.ui.authorization.persist_authorization') ? 'true' : 'false' !!}",
        });

        window.ui = ui;

        @if(in_array('oauth2', array_column(config('l5-swagger.defaults.securityDefinitions.securitySchemes'), 'type')))
        ui.initOAuth({
            usePkceWithAuthorizationCodeGrant: "{!! (bool)config('l5-swagger.defaults.ui.authorization.oauth2.use_pkce_with_authorization_code_grant') !!}"
        });
        @endif
    };
</script>
</body>
</html>
