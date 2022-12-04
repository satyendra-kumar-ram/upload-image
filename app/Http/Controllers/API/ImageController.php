<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\ImageResource;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ImageController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $images = Image::all();
        return $this->sendResponse(ImageResource::collection($images), 'Images retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'image' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        if($request->hasfile('image')){
            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            $input['url'] = '/storage/' . $filePath;
        }

        //  $image= new Image();
        //  $image->name= $input['name'];
        //  $image->url = $file_path;
        //  $image->save();
        $image = Image::create($input);
        return $this->sendResponse(new ImageResource($image), 'Image Uploaded successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $image = Image::find($id);
        if (is_null($image)) {
            return $this->sendError('Image not found.');

        }
        return $this->sendResponse(new ImageResource($image), 'Image retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:id,'.$id.',name,detail',
            'image' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        if($request->hasfile('image')){
            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
           return  $imagePath = public_path('storage/'.$fileName);
            if(File::exists($imagePath)){
                unlink($imagePath);
            }
            $input['url'] = '/storage/' . $filePath;
        }

        $input = $request->all();
        $image = Image::find($id);
        $image->name = $input['name'];
        $image->url = $input['url'];
        $image->save();
        return $this->sendResponse(new ImageResource($image), 'Image updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $image = Image::find($id);
        $image->delete();
        return $this->sendResponse([], 'Image deleted successfully.');
    }
}
