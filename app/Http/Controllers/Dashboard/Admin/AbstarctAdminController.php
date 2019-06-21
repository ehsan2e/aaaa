<?php

namespace App\Http\Controllers\Dashboard\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use NovaVoip\Interfaces\iPaginationGenerator;
use NovaVoip\Traits\SearchablePaginate;

abstract class AbstarctAdminController extends Controller
{
    use SearchablePaginate;

    /**
     * @var string
     */
    protected $collectionName = 'collection';

    /**
     * @var array
     */
    protected $searchableFields = [];

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
        return $this->paginate($request, $this->getBuilder());
    }

    /**
     * @return array
     */
    protected function getSearchableFields(): array
    {
        return $this->searchableFields;
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
            ->view(sprintf($this->viewPath ?? 'dashboard.admin.%s.index', $this->getViewBasePath()))
            ->setCollectionName($this->collectionName)
            ->setSearchableFields($this->getSearchableFields());
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
        return $this->renderForm(sprintf($this->viewPath ?? 'dashboard.admin.%s.create', $this->getViewBasePath()));
    }
}