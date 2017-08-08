<?php

namespace JumpGate\Admin\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;

abstract class AdminBaseController extends BaseController
{
    public $paginate = 15;

    public function index()
    {
        $results = $this->search()->get()->sortBy(request('orderBy'));
        $results = $this->paginate($this->transformer->transformAll($results)->toArray(), $this->paginate);

        $filtered = [];
        if (! empty(array_filter(request()->except('_token')))) {
            $filtered = request()->except('_token');
        }
        // dump($filtered);

        $filters = $this->filters();
        $routes  = $this->getRoutes();
        $columns = $this->columns();
        $title   = $this->getTitle();

        $this->setViewData(compact(
            'results',
            'filtered',
            'filters',
            'routes',
            'columns',
            'title'
        ));

        return $this->view('admin::index');
    }

    public function show($id)
    {
        $resource = $this->model->find($id);
        $routes   = $this->getRoutes();

        $this->setViewData(compact('resource', 'routes'));

        return $this->view();
    }

    public function store()
    {
        dd(request()->all());
        $this->model->create(request()->all());

        return redirect(route($this->routes()['index']))
            ->with('message', $this->getArea() . ' created.');
    }

    public function update($id)
    {
        $resource = $this->model->find($id);

        $resource->update(request()->all());

        return redirect(route($this->routes()['index']))
            ->with('message', $this->getArea() . ' updated.');
    }

    public function destroy($id)
    {
        $resource = $this->model->find($id);

        $resource->delete();

        return redirect(route($this->routes()['index']))
            ->with('message', $this->getArea() . ' removed.');
    }

    /**
     * Get all available filters for the area.
     *
     * @return array
     */
    abstract public function filters();

    /**
     * Get all available table header and result property values for the area.
     *
     * @return array
     */
    abstract public function columns();

    /**
     * Get all available routes for the area.
     *
     * @return array
     */
    public function routes()
    {
        $area = $this->getArea(true);

        return [
            'index'  => 'admin.' . $area . '.index',
            'show'   => 'admin.' . $area . '.show',
            'create' => 'admin.' . $area . '.create',
            'edit'   => 'admin.' . $area . '.edit',
            'delete' => 'admin.' . $area . '.destroy',
        ];
    }

    /**
     * Make sure all needed routes have been supplied.
     *
     * @return array
     */
    protected function getRoutes()
    {
        $routes   = $this->routes();
        $required = collect(['index', 'show', 'create', 'edit', 'delete']);

        $missing = $required->diff(collect($routes)->keys());

        if (count($missing) > 0) {
            throw new \InvalidArgumentException('You must supply all routes.  You are missing: ' . humanReadableImplode($missing->toArray()));
        }

        return $routes;
    }

    /**
     * Search for models using that models search provider.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function search()
    {
        return $this->model
            ->search(request()->all());
    }

    /**
     * Get a default area title from the called class.
     *
     * @return string
     */
    protected function getTitle()
    {
        return Str::title(
            supportCollector()
                ->explode('\\', get_called_class())
                ->last()
        );
    }

    protected function getArea($lowercase = false)
    {
        $singular = Str::singular(
            $this->getTitle()
        );

        if ($lowercase) {
            return Str::lower($singular);
        }

        return $singular;
    }

    protected function paginate($items, $perPage)
    {
        $pageStart           = \Request::get('page', 1);
        $offSet              = ($pageStart * $perPage) - $perPage;
        $itemsForCurrentPage = array_slice($items, $offSet, $perPage, true);

        return new LengthAwarePaginator(
            $itemsForCurrentPage, count($items), $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );
    }
}
