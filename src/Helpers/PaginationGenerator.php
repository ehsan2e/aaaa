<?php

namespace NovaVoip\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use NovaVoip\Interfaces\iPaginationGenerator;

class PaginationGenerator implements iPaginationGenerator
{
    const CAST_BOOLEAN = 1;
    const CAST_NULLABLE_BOOLEAN = 2;
    /**
     * @var string
     */
    protected $collectionName = 'collection';
    /**
     * @var array
     */
    protected $requestData;
    /**
     * @var Builder
     */
    protected $queryBuilder;
    /**
     * @var string
     */
    protected $queryParamName = 'q';
    /**
     * @var array
     */
    protected $queryParamFilters = [];
    /**
     * @var bool
     */
    protected $rawQueryAllowed = false;
    /**
     * @var array
     */
    protected $searchableFields = [];
    /**
     * @var string
     */
    protected $viewName;

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

    /**
     * @param string $paramName
     * @param null $handler
     * @return iPaginationGenerator
     */
    public function bindQueryParamFilter(string $paramName, $handler = null): iPaginationGenerator
    {
        $this->queryParamFilters[$paramName] = $handler ?? $paramName;
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
        if (count($this->queryParamFilters) > 0) {
            foreach ($this->queryParamFilters as $queryParam => $filter) {
                if (is_array($filter)) {
                    $filterConfig = $filter;
                    $filter = $filterConfig['filter'] ?? $queryParam;
                    $nullable = $filterConfig['nullable'] ?? false;
                    $check = $filterConfig['check'] ?? '=';
                    $format = $filterConfig['format'] ?? ($check === 'like' ? '%[[v]]%' : '[[v]]');
                    $cast = $filterConfig['cast'] ?? null;
                } else {
                    $nullable = false;
                    $check = '=';
                    $format = '[[v]]';
                    $cast = null;
                }
                if ((!array_key_exists($queryParam, $this->requestData)) || (is_null($this->requestData[$queryParam]) && (!$nullable))) {
                    continue;
                }
                $value = $this->requestData[$queryParam];
                if (is_callable($cast)) {
                    $value = $cast($value);
                }
                if (is_scalar($filter)) {
                    if (is_null($value)) {
                        $queryBuilder->whereNull($filter);
                    } else if (is_scalar($value)) {
                        $queryBuilder->where($filter, $check, str_replace('[[v]]', $value, $format));
                    } elseif (is_array($value)) {
                        if(in_array(null, $value, true)){
                            $queryBuilder->where(function($query) use ($filter, $value){
                                $query->whereNull($filter)->orWhereIn($filter, array_unique(array_filter($value, function($v){return isset($v);})));
                            });
                        }else{
                            $queryBuilder->whereIn($filter, array_unique($value));
                        }
                    }

                } elseif (is_callable($filter)) {
                    $queryBuilder->where(function (Builder $query) use ($queryParam, $filter) {
                        $filter($query, $this->requestData[$queryParam]);
                    });
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


    /**
     * @param int $castType
     * @return callable
     */
    public static function getCast(int $castType): callable
    {
        switch ($castType) {
            case self::CAST_BOOLEAN:
                return function($v){
                    if(is_array($v)){
                        return array_map(self::getCast(self::CAST_BOOLEAN), $v);
                    }
                    return (bool) $v;
                };
            case self::CAST_NULLABLE_BOOLEAN:
                return function($v){
                    if(is_array($v)){
                        return array_map(self::getCast(self::CAST_NULLABLE_BOOLEAN), $v);
                    }
                    return is_null($v) ? $v : ((bool) $v);
                };
            default:
                return function ($v) {
                    return $v;
                };
        }
    }
}