<?php

namespace Importer\Contracts;

interface DataSourceAdapterInterface
{
    public function getSourceName(): string;

    public function discover(): array;

    public function download(array $resource): string;

    public function parse(string $filePath): iterable;

    public function normalize(array $row): array;

    public function validate(array $row): array;
}

