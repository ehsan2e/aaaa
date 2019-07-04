<?php

namespace NovaVoip\Interfaces;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

interface iPaginationGenerator
{
    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';

    /**
     *  constructor.
     * @param array $requestData
     * @param Builder $queryBuilder
     * @param int|null $perPage
     */
    public function __construct(array $requestData, Builder $queryBuilder, int $perPage = null);

    /**
     * @param string $paramName
     * @param null $handler
     * @param string|null $field
     * @return iPaginationGenerator
     */
    public function bindQueryParamFilter(string $paramName, $handler = null, string $field=null): iPaginationGenerator;

    /**
     * @param string $class
     * @return iPaginationGenerator
     */
    public function cast(?string $class): iPaginationGenerator;

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
     * @param array $sortConfig
     * @param string $orderByParam
     * @param string $sortDirectionParam
     * @param string $sortConfigVariable
     * @return iPaginationGenerator
     */
    public function setOrder(array $sortConfig, string $orderByParam, string $sortDirectionParam, string $sortConfigVariable): iPaginationGenerator;

    /**
     * @param int $number
     * @return iPaginationGenerator
     */
    public function setRecordsPerPage(int $number): iPaginationGenerator;

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