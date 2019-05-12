<?php

namespace kirksfletcher\pagespeed;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use kirksfletcher\pagespeed\filters\RemoveComments;
use kirksfletcher\pagespeed\filters\RemoveWhiteSpace;

class Pagespeed
{
    protected $trimWhiteSpace = false;
    protected $removeComments = false;
    protected $allowDynamic = false;

    /**
     * Render view, apply enabled filters, and cache output for lightning fast performance
     *
     * @param $view
     * @param array $data
     * @param string $slug
     * @return mixed
     */
    public function view($view, $data = [], $slug = '')
    {
        try {
            if (!Auth::check()) {

                if($this->allowDynamic) {
                    $cacheRef = md5(strtolower($view.$slug) . serialize($data));
                }else{
                    $cacheRef = ($slug == '') ? md5(strtolower($view)) : md5(strtolower($slug));
                }

                $view = Cache::rememberForever($cacheRef, function () use ($view, $data) {
                    return $this->renderView($view, $data);
                });
            } else {
                $view = $this->renderView($view, $data);
            }

            return $view;
        } catch (\Exception $e) {
            return response($e->getMessage(), 420);
        }
    }

    /**
     * Remove cache for slug or view
     *
     * @param $slug
     */
    public function killCacheView($slug)
    {
        $slug = md5(strtolower($slug));
        Cache::forget($slug);
    }

    /**
     * Enable / Disable plugins
     *
     * @param $plugin
     * @param bool $enable
     */
    public function plugin($plugin, $enable = true)
    {
        $plugin = strtolower($plugin);
        switch ($plugin) {
            case 'trimwhitespace':
                $this->trimWhiteSpace = $enable;
                break;
            case 'removecomments':
                $this->removeComments = $enable;
                break;
        }
    }

    /**
     * Allow cache ref to be based on md5 of the page data + view + slug
     * creates more cache files but allows dynamic content
     *
     * @param bool $state
     */
    public function allowDynamicContent($state = true)
    {
        $this->allowDynamic = $state;
    }


    /**
     * @param $view
     * @param $data
     * @return null|string|string[]
     */
    private function renderView($view, $data)
    {
        $cacheView = View::make($view, $data)->render();

        if ($this->trimWhiteSpace) {
            $cacheView = RemoveWhiteSpace::render($cacheView);
        }

        if ($this->removeComments) {
            $cacheView = RemoveComments::render($cacheView);
        }

        return $cacheView;
    }
}
