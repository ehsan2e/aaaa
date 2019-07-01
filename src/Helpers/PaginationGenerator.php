<?php

namespace NovaVoip\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use NovaVoip\Interfaces\iPaginationGenerator;

class PaginationGenerator implements iPaginationGenerator
{
    /**
     * @var string
     */
    protected
        $collectionName = 'collection';
    /**
     * @var array
     */
    protected
        $requestData;
    /**
     * @var Builder
     */
    protected
        $queryBuilder;
    /**
     * @var string
     */
    protected
        $queryParamName = 'q';
    /**
     * @var array
     */
    protected $queryParamFilters = [];
    /**
     * @var bool
     */
    protected
        $rawQueryAllowed = false;
    /**
     * @var array
     */
    protected
        $searchableFields = [];
    /**
     * @var string
     */
    protected
        $viewName;

    /**
     *  constructor.
     * @param array $requestData
     * @param Builder $queryBuilder
     */
    public function __construct(array $requestData, Builder $queryBuilder)
    {
        $this->requestData = $requestData;
        $this->queryBuilder = $queryBuilder;
    }

    public function bindQueryParamFilter(string $paramName, $handler): iPaginationGenerator
    {
        $this->queryParamFilters[$paramName] = $handler;
        return $this;
    }

    /**
     * @return string
     */
    public function getCollectionName(): string
    {
        return $this->collectionName;
    }

    /**
     * @return string
     */
    public function getQueryParamName(): string
    {
        return $this->queryParamName;
    }

    /**
     * @return array
     */
    public function getSearchableFields(): array
    {
        return $this->searchableFields;
    }

    /**
     * @param bool $allowed
     * @return iPaginationGenerator
     */
    public function rawQuery(bool $allowed): iPaginationGenerator
    {
        $this->rawQueryAllowed = $allowed;
        return $this;
    }

    /**
     * @param array $data
     * @return View
     */
    public function render(array $data = []): View
    {
        if (!isset($this->viewName)) {
            throw new \RuntimeException('View is not set');
        }

        $q = $this->requestData[$this->getQueryParamName()] ?? null;
        $queryBuilder = $this->queryBuilder;
        if (isset($q)) {
            if ($this->rawQueryAllowed && strpos($q, ':::>') === 0) {
                $queryBuilder->whereRaw(substr($q, 4));
            } else {
                $fields = $this->getSearchableFields();
                if (count($fields) > 0) {
                    $queryBuilder = $queryBuilder->where(function (Builder $query) use ($q, $fields) {
                        $fn = 'where';
                        foreach ($fields as $key => $value) {
                            if (is_callable($value)) {
                                $query->{$fn}(function ($query2) use ($value, $q) {
                                    $value($query2, $q);
                                });
                            } elseif (is_numeric($key)) {
                                $query->{$fn}($value, 'LIKE', "%{$q}%");
                            } else {
                                $query->{$fn}($key, $value, $value === 'LIKE' ? "%{$q}%" : $q);
                            }
                            $fn = 'orWhere';
                        }
                    });
                }
            }
        }
        if(count($this->queryParamFilters) > 0){
            foreach ($this->queryParamFilters as $queryParam => $filter){
                if(!isset($this->requestData[$queryParam])){
                    continue;
                }
                if(is_scalar($filter)){
                    $queryBuilder->where($filter, $this->requestData[$queryParam]);
                }
            }
        }
        $queryError = null;
        try {
            $collection = $queryBuilder->paginate();
            if (isset($q)) {
                $collection->appends([$this->getQueryParamName() => $q]);
            }
        } catch (QueryException $queryException) {
            $queryError = $queryException->getMessage();
            $collection = new LengthAwarePaginator([], 0, 15);
        }
        return view($this->viewName, [
                $this->getCollectionName() => $collection,
                'canRunRawQuery' => $this->rawQueryAllowed,
                'queryError' => $queryError,
                'queryParamName' => $this->getQueryParamName(),
            ] + $data);
    }

    /**
     * @param string $collectionName
     * @return iPaginationGenerator
     */
    public function setCollectionName(string $collectionName): iPaginationGenerator
    {
        $this->collectionName = $collectionName;
        return $this;
    }

    /**
     * @param string $queryParamName
     * @return iPaginationGenerator
     */
    public function setQueryParamName(string $queryParamName): iPaginationGenerator
    {
        $this->queryParamName = $queryParamName;
        return $this;
    }

    /**
     * @param $searchableFields
     * @return iPaginationGenerator
     */
    public function setSearchableFields($searchableFields): iPaginationGenerator
    {
        $this->searchableFields = $searchableFields;
        return $this;
    }

    /**
     * @param string $view
     * @return iPaginationGenerator
     */
    public function view(string $view): iPaginationGenerator
    {
        $this->viewName = $view;
        return $this;
    }
}