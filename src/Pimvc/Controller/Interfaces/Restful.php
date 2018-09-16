<?php

/**
 * Description of Pimvc\Controller\Interfaces\Restful
 *
 * @author pierrefromager
 */

namespace Pimvc\Controller\Interfaces;

interface Restful
{

    /**
     * index
     *
     */
    public function index();

    /**
     * create
     *
     */
    public function create();

    /**
     * store
     *
     */
    public function store();

    /**
     * show
     *
     */
    public function show();

    /**
     * edit
     *
     */
    public function edit();

    /**
     * update
     *
     */
    public function update();

    /**
     * destroy
     *
     */
    public function destroy();
}
