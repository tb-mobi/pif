<?php

namespace mobi2\Http\Controllers\Products;

use Illuminate\Http\Request;

use mobi2\Http\Requests;
use mobi2\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    protected $data=array(
      'id'=>'0002'
      ,'name'=>'зПИФ'
    );
    public function index()
    {
        return view('pif');
    }
    public function getIndex(){
       return view('pif');
    }
    public function getRegister(Request $request){
      //TODO register new lead
       return redirect('client/pinset');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
