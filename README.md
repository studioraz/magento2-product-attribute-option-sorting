# SR_ProductAttributeOptionSorting

Magento 2 module that adds attribute option sorting behavior for product attributes in Admin.

## Purpose

This module extends product attribute option handling so option sorting mode can be managed in Admin and applied consistently after attribute save.

## Features

- Adds admin integration for product attribute option sorting behavior
- Persists sorting mode on attribute save
- Applies sorting logic via service/observer flow

## Requirements

- Magento 2 (Adobe Commerce / Magento Open Source)
- PHP version compatible with your Magento installation

## Installation

### Composer (recommended)

```bash
composer require studioraz/magento2-product-attribute-option-sorting:^1.0
bin/magento module:enable SR_ProductAttributeOptionSorting
bin/magento setup:upgrade
bin/magento cache:flush
```

### Manual (from source)

1. Place module files under:
   `app/code/SR/ProductAttributeOptionSorting`
2. Run:

```bash
bin/magento module:enable SR_ProductAttributeOptionSorting
bin/magento setup:upgrade
bin/magento cache:flush
```

## Repository layout

- Module source code lives under `src/`

## Module name

- `SR_ProductAttributeOptionSorting`

## Releases

Releases follow the [shared Release Please guide](https://github.com/studioraz/ci-templates/blob/master/docs/release-please.md).
Use Conventional Commit PR titles (`feat: add support`, `fix: correct behavior`).
Squash merges use the PR title and body; single-commit PRs must have a matching commit title.

Pushes to `master`, the Monday 06:00 UTC schedule and manual runs maintain the release PR and synchronize shared configuration.
Merging a release PR creates a GitHub Release and an unprefixed SemVer tag, and updates the root `CHANGELOG.md`.
Enrollment preserves existing release history (verified baseline `1.0.3`).

The optional `RELEASE_TOKEN` is forwarded to the shared workflow. Without it, `GITHUB_TOKEN` is used and bot-created PRs do not trigger PR-title CI. Supply a PAT or GitHub App token when those checks are required.
