<?php

return [
    'audits' => [
        [
            'title' => 'Tests',
            'command' => './vendor/bin/phpunit -d memory_limit=-1 --no-progress --configuration phpunit.xml;',
            'failure_hint' => 'Run tests using "php artisan test --stop-on-error" to help discover code issues in isolation.',
        ],
        [
            'title' => 'Stan',
            'command' => './vendor/bin/phpstan analyze -v --memory-limit=-1',
            'failure_hint' => 'Address the code issues found by "./vendor/bin/phpstan analyze -v --memory-limit=-1" (or adjust your phpstan.neon to allow for them).',
        ],
        [
            'title' => 'Pint (all files)',
            'command' => './vendor/bin/pint --test',
            'failure_hint' => 'Run "./vendor/bin/pint" to have Pint fix these code style issues.',
        ],
        [
            'title' => 'Pint (dirty files)',
            'command' => './vendor/bin/pint --dirty --test',
            'failure_hint' => 'Run "./vendor/bin/pint --dirty" to have Pint fix these code style issues while remaining scoped to uncommited changes only.',
        ],
        [
            'title' => 'Prettier',
            'command' => 'npx prettier --config .prettierrc -u -l $(git diff --name-only --diff-filter=d HEAD  | xargs)',
            'failure_hint' => 'Run "npx prettier --config .prettierrc -u -w $(git diff --name-only --diff-filter=d HEAD  | xargs)" to have prettier fix these code style issues.',
        ],
        [
            'title' => 'Composer Audit',
            'command' => 'composer audit',
        ],
        [
            'title' => 'NPM Audit',
            'command' => 'npm audit',
        ],
    ],
];
