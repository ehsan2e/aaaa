<?php

namespace NovaVoip\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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
    protected $castClass;
    /**
     * @var string
     */
    protected $collectionName = 'collection';
    /**
     * @var int
     */
    protected $perPage = 15;
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
    protected $requestData;
    /**
     * @var array
     */
    protected $searchableFields = [];
    /**
     * @var array
     */
    protected $sorting = [
        'orderByParam' => 'order_by',
        'sortConfig' => [],
        'sortDirectionParam' => 'sort_direction',
        'sortConfigVariable' => 'sortConfig',
    ];
    /**
     * @var string
     */
    protected $viewName;

    /**
     *  constructor.
     * @param array $requestData
     * @param Builder $queryBuilder
     * @param int|null $perPage
     */
    public function __construct(array $requestData, Builder $queryBuilder, int $perPage = null)
    {
        $this->requestData = $requestData;
        $this->queryBuilder = $queryBuilder;
        if (isset($perPage)) {
            $this->perPage = $perPage;
        }
    }

    /**
     * @param string $paramName
     * @param null $handler
     * @param string|null $field
     * @return iPaginationGenerator
     */
    public function bindQueryParamFilter(string $paramName, $handler = null, string $field=null): iPaginationGenerator
    {
        $this->queryParamFilters[$paramName] = $handler ?? $paramName;
        return $this;
    }

    /**
     * @param string $class
     * @return iPaginationGenerator
     */
    public function cast(?string $class): iPaginationGenerator
    {
        $this->castClass = $class;
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
                        if (in_array(null, $value, true)) {
                            $queryBuilder->where(function ($query) use ($filter, $value) {
                                $query->whereNull($filter)->orWhereIn($filter, array_unique(array_filter($value, function ($v) {
                                    return isset($v);
                                })));
                            });
                        } else {
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

        // Set query order if applicable
        $orderBy = $this->resolveOrderBy();
        $sortDirection = $this->resolveSortDirection();
        if ($orderBy !== -1) {
            $this->requestData[$this->sorting['orderByParam']] = $orderBy;
            $this->requestData[$this->sorting['sortDirectionParam']] = $sortDirection;
            list($dummy, $sortHandle) = $this->sorting['sortConfig'][$orderBy];
            if (is_callable($sortHandle)) {
                $sortHandle($queryBuilder, $sortDirection);
            } else {
                $queryBuilder->orderBy($sortHandle, $sortDirection);
            }
        }


        $queryError = null;
        try {
            /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $collection */
            $collection = $queryBuilder->paginate($this->perPage);
            if (isset($this->castClass)) {
                $newCollection = new Collection();
                /** @var ProductTypeTaxGroup $originalModel */
                foreach ($collection as $originalModel) {
                    /** @var Model $castedModel */
                    $castedModel = new $this->castClass();
                    $attributes = $originalModel->getAttributes();
                    array_walk($attributes, function ($value, $key) use ($castedModel) {
                        $castedModel->{$key} = $value;
                    });
                    $castedModel->syncOriginal();
                    $newCollection->add($castedModel);
                }
                $collection = new LengthAwarePaginator($newCollection, $collection->total(), $collection->perPage(), $collection->currentPage(), $collection->getOptions());
            }
            $appendedData = [];
            if (isset($q)) {
                $appendedData[$this->getQueryParamName()] = $q;
            }
            if ($orderBy !== -1) {
                $appendedData[$this->sorting['orderByParam']] = $orderBy;
                $appendedData[$this->sorting['sortDirectionParam']] = $sortDirection;
            }
            $collection->appends($appendedData);
        } catch (QueryException $queryException) {
            $queryError = $queryException->getMessage();
            $collection = new LengthAwarePaginator([], 0, 15);
        }
        return view($this->viewName, [
                $this->getCollectionName() => $collection,
                'canRunRawQuery' => $this->rawQueryAllowed,
                'queryError' => $queryError,
                'queryParamName' => $this->getQueryParamName(),
                $this->sorting['sortConfigVariable'] => [
                    'options' => array_map(function ($item) {
                        return $item[0];
                    }, $this->sorting['sortConfig']),
                    'orderBy' => $orderBy,
                    'orderByParam' => $this->sorting['orderByParam'],
                    'sortDirection' => $sortDirection,
                    'sortDirectionParam' => $this->sorting['sortDirectionParam'],
                ]
            ] + $data);
    }

    /**
     * @return int
     */
    protected function resolveOrderBy(): int
    {
        $requestedOrder = (int)($this->requestData[$this->sorting['orderByParam']] ?? '0');
        return isset($this->sorting['sortConfig'][$requestedOrder]) ? $requestedOrder : ((count($this->sorting['sortConfig']) === 0) ? -1 : 0);
    }

    /**
     * @return string
     */
    protected function resolveSortDirection(): string
    {
        $requestedSortDirection = $this->requestData[$this->sorting['sortDirectionParam']] ?? iPaginationGenerator::SORT_ASC;
        return in_array($requestedSortDirection, [iPaginationGenerator::SORT_ASC, iPaginationGenerator::SORT_DESC]) ? $requestedSortDirection : iPaginationGenerator::SORT_ASC;
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
     * @param array $sortConfig
     * @param string $orderByParam
     * @param string $sortDirectionParam
     * @param string $sortConfigVariable
     * @return iPaginationGenerator
     */
    public function setOrder(array $sortConfig, string $orderByParam='order_by', string $sortDirectionParam = 'sort_direction', string $sortConfigVariable = 'sortConfig'): iPaginationGenerator
    {
        $this->sorting = compact('sortConfig', 'orderByParam', 'sortDirectionParam', 'sortConfigVariable');
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
     * @param int $number
     * @return iPaginationGenerator
     */
    public function setRecordsPerPage(int $number): iPaginationGenerator
    {
        $this->perPage = $number;
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
                return function ($v) {
                    if (is_array($v)) {
                        return array_map(self::getCast(self::CAST_BOOLEAN), $v);
                    }
                    return (bool)$v;
                };
            case self::CAST_NULLABLE_BOOLEAN:
                return function ($v) {
                    if (is_array($v)) {
                        return array_map(self::getCast(self::CAST_NULLABLE_BOOLEAN), $v);
                    }
                    return is_null($v) ? $v : ((bool)$v);
                };
            default:
                return function ($v) {
                    return $v;
                };
        }
    }
}