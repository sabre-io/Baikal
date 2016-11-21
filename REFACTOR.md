# Refactoring Baikal to the Silex framework

- Original webroot is now at 'html.old'
- New webroot remains the same 'html' for easier upgrades
- Composer autoloader switch to psr-4 and support both locations
- Emulate hardcoded Dashboard page for now, replace with dynamic values

## Differences between new and old

You can run two PHP server instances to compare behaviour

Running the new Silex-based code
`$ php -S 0.0.0.0:8000 -t html`

Running the original code
`$ php -S 0.0.0.0:8001 -t html.old`


