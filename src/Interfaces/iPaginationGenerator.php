<?php

namespace NovaVoip\Interfaces;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

interface iPaginationGenerator
{
    /**
     *  constructor.
     * @param array $requestData
     * @param Builder $queryBuilder
     */
    public function __construct(array $requestData, Builder $queryBuilder);

    /**
     * @return string
     */
    public function getCollectionName(): string;

    /**
     * @return string
     */
    public function getQueryParamName(): string;

    /**
     * @return array
     */
    public function getSearchableFields(): array;

    /**
     * @param bool $allowed
     * @return iPaginationGenerator
     */
    public function rawQuery(bool $allowed): iPaginationGenerator;

    /**
     * @param array $data
     * @return View
     */
    public function render(array $data = []): View;

    /**
     * @param string $collectionName
     * @return iPaginationGenerator
     */
    public function setCollectionName(string $collectionName): iPaginationGenerator;

    /**
     * @param string $queryParamName
     * @return iPaginationGenerator
     */
    public function setQueryParamName(string $queryParamName): iPaginationGenerator;

    /**
     * @param $searchableFields
     * @return iPaginationGenerator
     */
    public function setSearchableFields($searchableFields): iPaginationGenerator;

    /**
     * @param string $view
     * @return iPaginationGenerator
     */
    public function view(string $view): iPaginationGenerator;
}