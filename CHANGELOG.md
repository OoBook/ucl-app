# Changelog

All notable changes to `ucl-app` will be documented in this file

## v1.0.1 - 2025-04-06

### :rocket: Features

- add success messages to item creation and update redirects by @OoBook in https://github.com/OoBook/ucl-app/commit/8b8ef03258dcb50cd22dac60ed514bae9f45aa84
- add flash messaging support for success and error notifications in the layout by @OoBook in https://github.com/OoBook/ucl-app/commit/93b1d32f9e1c088cf87acf81e5241ca21486e2bf
- implement custom error responses with Inertia for specific status codes and add Error.vue component for rendering error pages by @OoBook in https://github.com/OoBook/ucl-app/commit/abd856e4999ba5b40ba67edd996f093f18d28686
- create a new Table component with dynamic rendering for data and actions by @OoBook in https://github.com/OoBook/ucl-app/commit/4951bcbfb658436c7c92e0814284326b98e84969
- integrate new Table component for item display and add item removal functionality by @OoBook in https://github.com/OoBook/ucl-app/commit/a0e26b763e085db0edd0f006a0cdba9d99500c80
- create Fixture model, migration, and factory for match scheduling by @OoBook in https://github.com/OoBook/ucl-app/commit/fac87901805fd15d0c9f2e3946610b566c4ea8dc
- add home and away fixtures relationships to Team model by @OoBook in https://github.com/OoBook/ucl-app/commit/edb66bd0c62bf135e2be7ab2be6fdef45b18ae03
- create Fixtures index page with generation and clearing functionality by @OoBook in https://github.com/OoBook/ucl-app/commit/363720edc444fa56f1a8f0bfa5f323dc55d585fc
- add FixtureSeeder and TeamSeeder to database seeding process by @OoBook in https://github.com/OoBook/ucl-app/commit/a58b6111ebe63fe85aeb2c495d5ab6f2be1b18a3
- implement fixture management with index, generation, and clearing functionalities by @OoBook in https://github.com/OoBook/ucl-app/commit/dcc576029b50cfa8dfbf7f2a165104e2abdae4e7

### :recycle: Refactors

- streamline item deletion response to include success message by @OoBook in https://github.com/OoBook/ucl-app/commit/006a0c8828f245be75b022ef6ab73b2b6fe78edf
- remove Inertia error response handling from app.php by @OoBook in https://github.com/OoBook/ucl-app/commit/4af264177c71db79af56ad5a72f90c9c37253ee6

### :white_check_mark: Testing

- add unit tests for Fixture model attributes and relationships by @OoBook in https://github.com/OoBook/ucl-app/commit/56a2528d678a9f60bc81f2f19f1610737103791f
- add feature tests for fixture management including index display, generation, error handling, and clearing by @OoBook in https://github.com/OoBook/ucl-app/commit/adcfda91b988c168a74495aebeeb8fe42b34c992

### :package: Build

- update asset filenames and remove obsolete files for improved build process by @OoBook in https://github.com/OoBook/ucl-app/commit/3ab9fa69447000af55e5d4252f8bb926638720c8

### :beers: Other Stuff

- enable SQLite in PHPUnit configuration for testing by @OoBook in https://github.com/OoBook/ucl-app/commit/06852eaec1fcc77b909ad3bd59e1f7616f455559

## v1.0.0 - 2025-04-05

### :rocket: Features

- implement CoreController with CRUD operations and Inertia.js integration by @OoBook in https://github.com/OoBook/ucl-app/commit/f61b382df5ee5e97453de5af7f83c2b33ae1bb71
- add Team model, factory, and migration for teams table by @OoBook in https://github.com/OoBook/ucl-app/commit/7e0a72a2feabe268ce2a7cc82df2933f00466b77
- add TeamResource and TeamSeeder for team data management by @OoBook in https://github.com/OoBook/ucl-app/commit/8e33da3ba8302bb37fd368be322b660ff10893ee
- create Vue components for team management including Create, Edit, Index, and Show pages by @OoBook in https://github.com/OoBook/ucl-app/commit/361c97e18a124539756fd4aa1051100bcd341f5c
- implement TeamController for managing team data with validation rules and display configurations by @OoBook in https://github.com/OoBook/ucl-app/commit/37f3829ca585aac55003599cc0c79d32bb9f6df8
- add resource routes for TeamController to manage team operations by @OoBook in https://github.com/OoBook/ucl-app/commit/ad434240f387432fe80afd7baf44f5624e9a6fd8
- add Teams navigation link to AuthenticatedLayout for team management access by @OoBook in https://github.com/OoBook/ucl-app/commit/e1d120063a65ee4f4d25102027cbb5d7746f8c22

### :white_check_mark: Testing

- add feature tests for TeamController and unit tests for Team model attributes by @OoBook in https://github.com/OoBook/ucl-app/commit/d05baf68ac467e9e671575e1da3276648cd3f488

## v0.0.0 - 2025-04-05

- Initial Tag
