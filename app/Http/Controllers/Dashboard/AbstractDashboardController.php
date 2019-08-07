<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use NovaVoip\Interfaces\iPaginationGenerator;
use NovaVoip\Traits\SearchablePaginate;

abstract class AbstractDashboardController extends Controller
{
    use SearchablePaginate;

    /**
     * @var string
     */
    protected $collectionName = 'collection';

    /**
     * @var callable|null
     */
    protected $customizeQuery = null;

    /**
     * @var string
     */
    protected $dashboardPrefix = 'dashboard';

    /**
     * @var string
     */
    protected $defaultSortOrder = iPaginationGenerator::SORT_ASC;

    /**
     * @var string
     */
    protected $orderByField = 'order_by';

    /**
     * @var int
     */
    protected $recordsPerPage = 15;

    /**
     * @var array
     */
    protected $searchableFields = [];

    /**
     * @var array
     */
    protected $sortableFields = [];

    /**
     * @var string
     */
    protected $sortDirectionField = 'sort_order';

    /**
     * @var string|null
     */
    protected $viewBasePath;
    /**
     * @var string|null
     */
    protected $viewPath;

    /**
     * @return Builder
     */
    abstract protected function getBuilder(): Builder;

    protected function getCastingClass(): ?string
    {
        return null;
    }

    /**
     * @return array
     */
    protected function getIndexPageData(): array
    {
        return [];
    }

    protected function getResourceRouteParameters(): array
    {
        return [$this->getResourceName()];
    }

    /**
     * @param Request $request
     * @return iPaginationGenerator
     */
    protected function getPaginator(Request $request): iPaginationGenerator
    {
        return $this->paginate($request, $this->customizeQuery ? call_user_func($this->customizeQuery, $this->getBuilder()) : $this->getBuilder(), $this->recordsPerPage);
    }

    /**
     * @return array
     */
    protected function getSearchableFields(): array
    {
        return $this->searchableFields;
    }

    /**
     * @return array
     */
    protected function getSortableFields(): array
    {
        return $this->sortableFields;
    }

    /**
     * @return string
     * @throws \ErrorException
     */
    protected function getViewBasePath(): string
    {
        if (!isset($this->viewBasePath)) {
            throw new \ErrorException('viewBasePath is missing');
        }
        return $this->viewBasePath;
    }

    /**
     * @param iPaginationGenerator $paginationGenerator
     * @return iPaginationGenerator
     */
    protected function prePaginationRender(iPaginationGenerator $paginationGenerator): iPaginationGenerator
    {
        return $paginationGenerator;
    }

    /**
     * @param string $view
     * @param array $data
     * @return \Illuminate\Http\Response
     */
    protected function renderForm(string $view, array $data = [])
    {
        return view($view, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     * @throws \ErrorException
     */
    final public function index(Request $request)
    {
        $paginationGenerator = $this->getPaginator($request)
            ->view(sprintf($this->viewPath ?? '%s.%s.index', $this->dashboardPrefix, $this->getViewBasePath()))
            ->setCollectionName($this->collectionName)
            ->setSearchableFields($this->getSearchableFields())
            ->setOrder($this->getSortableFields(), $this->orderByField, $this->sortDirectionField)
            ->cast($this->getCastingClass());
        return $this->prePaginationRender($paginationGenerator)->render($this->getIndexPageData());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \ErrorException
     */
    public function create()
    {
        return $this->renderForm(sprintf($this->viewPath ?? '%s.%s.create', $this->dashboardPrefix, $this->getViewBasePath()));
    }
}
