<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

abstract class CoreController extends Controller
{
    /**
     * The field to display in the title.
     *
     * @var string
     */
    protected $titleField;

    /**
     * The prefix of the route.
     *
     * @var string
     */
    protected $routePrefix;

    /**
     * The title of the page.
     *
     * @var string
     */
    protected $title;

    /**
     * The title of the page.
     *
     * @var string
     */
    protected $pageTitle;

    /**
     * The prefix of the pages.
     *
     * @var string
     */
    protected $pagesPrefix;

    /**
     * The model of the page.
     *
     * @var string
     */
    protected $model;

    /**
     * The transformer class of the page.
     *
     * @var string
     */
    protected $transformerClass;

    /**
     * The display fields of the page.
     *
     * @var array
     */
    protected $displayFields = [];

    /**
     * The schema of the page.
     *
     * @var array
     */
    protected $schema = [];

    /**
     * The table columns of the page.
     *
     * @var array
     */
    protected $tableColumns = [];

    /**
     * The validation rules of the page.
     *
     * @var array
     */
    protected $storeRules = [];

    /**
     * The update rules of the page.
     *
     * @var array
     */
    protected $updateRules = [];

    public function index()
    {
        $teams = $this->model::all();

        return Inertia::render($this->pagesPrefix . '/Index', [
            'pageTitle' => $this->pageTitle ?? $this->title,
            'title' => $this->title ?? $this->pageTitle,

            'titleField' => $this->titleField,
            'routePrefix' => $this->routePrefix,

            'columns' => $this->tableColumns,

            'displayFields' => $this->displayFields,
            'schema' => $this->schema,

            'resource' => $this->transformerClass::collection($teams),
        ]);
    }

    public function show($id)
    {
        $item = $this->model::find($id);

        return Inertia::render($this->pagesPrefix . '/Show', [
            'pageTitle' => 'Team Details (' . $item->name . ')',
            'titleField' => $this->titleField,
            'routePrefix' => $this->routePrefix,

            'resource' => new $this->transformerClass($item),
            'fields' => $this->displayFields,
        ]);
    }

    public function create()
    {
        return Inertia::render($this->pagesPrefix . '/Create', [
            'title' => 'Create Team',
            'pageTitle' => 'Create Team',
            'routePrefix' => $this->routePrefix,
            'schema' => $this->schema,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->storeRules);

        $item = $this->model::create($validated);

        return redirect()->route($this->routePrefix . '.index');
    }

    public function edit($id)
    {
        $item = $this->model::find($id);

        return Inertia::render($this->pagesPrefix . '/Edit', [
            'pageTitle' => 'Edit ' . $item->name,
            'routePrefix' => $this->routePrefix,
            'schema' => $this->schema,

            'resource' => new $this->transformerClass($item),
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate($this->updateRules);

        $item = $this->model::find($id);

        $item->update($validated);

        if( $request->routeIs($this->routePrefix . '.edit') ) {
            return redirect()->route($this->routePrefix . '.edit', [$id]);
        }

        return redirect()->route($this->routePrefix . '.index');
    }

    public function destroy(Request $request, $id)
    {
        $item = $this->model::find($id);

        $item->delete();

        return redirect()
            ->route($this->routePrefix . '.index')
            ->with('message', 'Item deleted successfully');
    }
}