### Prerequisites
You'll need to have `composer` on your machine

### Steps
1. run `composer install`
2. `cat .env.example > .env`
3. `./vendor/bin/sail up -d --build`
- if you have next error
```
    [laravel.test  7/12] RUN groupadd --force -g www-data sail:
    0.105 groupadd: invalid group ID 'www-data'
```
try to change `WWWGROUP` in `.env`
4. `./vendor/bin/sail artisan key:generate`
5. `./vendor/bin/sail artisan storage:link`
6. `./vendor/bin/sail artisan migrate`
7. `./vendor/bin/sail artisan db:seed PermissionSeeder`
8. `./vendor/bin/sail artisan db:seed LocalSeeder`


### Unit tests
1. create a db `pwa_test` manually next to `pwa`
2. run migration `./vendor/bin/sail artisan migrate --database=test`
3. run `./vendor/bin/sail artisan test --coverage-html coverage-report`
   This will create `coverage-report` which available by [this link](http://localhost:63342/pwa-app/coverage-report/index.html)
   (or, if you use phpstorm, open `./coverage-report/index.html` and click on icon of your browser in upper right corner)
4. to run separate test use `./vendor/bin/sail artisan test --filter=ExampleTest`


### Permissions rules
#### General rule
```<accessLevel><EntitySingular><Action>```:
 - `<accessLevel>` - `common`, `client`, `manage`
 - `<EntitySingular>` - name of an entity in singular form
 - `<Action>` - `Create`, `Read`, `Update`, `Delete`, `Clone`

Example: `clientCompanyRead`, `clientTeamCreate`

If you have exclusive `action`, like `Invite`, `SendMessage` or so, feel free to use it instead of suggested,
but don't forget to mention it in `PermissionTest.php` file in `$exceptionalSuffixes` array.
Otherwise, you will not pass codereview stage

#### Depending on routes' names rules
If you need more than one endpoint for any of `CRUD` operation, like `create` and `store`, or `edit` and `update` etc.,
use mentioned previously actions for any option.
If you need to add exception to this rule, just edit `$exceptionsForRoute` array in `ApiRouteTest.php` file.

Example:
```GET|PUT /edit|update``` - action = `update`
```GET|DELETE /delete|destroy``` - action = `delete`
