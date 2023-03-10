<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $data['pagename'] =  __('dash.sections');
        $data['sections']  = Section::withCount('children')->latest()->get();
        return view('sections.index',$data);
    }


    public function create()
    {
        $data['pagename'] =  __('dash.add').' '.__('dash.sections');
        $data['parents']   = Section::get();
        return view('sections.create',$data);
    }

    public function store(Request $request)
    {
        $data =$request->validate([
            'ar_name' => 'required',
            'en_name' => 'required',
            'parent_id' => 'int',
            'status' => 'required',
            'image'   => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image');
            $imageName = time() . '-' . $request->name . '.' . $request->file("image")->extension();
            $path = $request->file('image')
                ->move(public_path("images" . DIRECTORY_SEPARATOR . "sctions"), $imageName);
            $request->image = $imageName;
        }

        $section = new Section();
        $section->ar_name = $request->ar_name;
        $section->en_name = $request->en_name;
        $section->image = $request->image;
        
        if($request->parent_id){
         $section->parent_id = $request->parent_id;
        }
        $section->status = $request->status;
        $section->save();
        toast(__('dash.store-success'),'success');
        return redirect()->route('section.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['section'] = Section::findOrfail($id);
        $data['pagename'] = __('dash.edit').' '.__('dash.sections');
        $data['parents']   = Section::where('id','!=',$data['section']->id)->get();
        return view('sections.edit',$data);
    }


    public function update(Request $request,$id)
    {
        $section = Section::findOrFail($id);
        $image = public_path('images' . DIRECTORY_SEPARATOR . 'sctions' . DIRECTORY_SEPARATOR . $section->image);

        if ($request->hasFile('image')) {

            if (File::exists($image)) {
                File::delete($image);
            }

            $imagePath = $request->file('image');
            $imageName = time() . '-' . $request->name . '.' . $request->file("image")->extension();
            $path = $request->file('image')
                ->move(public_path("images/sctions"), $imageName);
            $request->image = $imageName;
            $section->image = $imageName;
        }

        if($request->parent_id){
         $section->parent_id = $request->parent_id;
        } else {
            $section->parent_id = null;
        }

        $section->ar_name = $request->ar_name;
        $section->en_name = $request->en_name;
       // $section->parent_id = $request->parent_id;
        $section->status = $request->status;

        $section->save();

        toast(__('dash.edit-success'),'success');;
        return redirect()->route('section.index');
    }

    public function destroy($id)
    {
        $type = Section::findOrFail($id);
        $image = public_path('images' . DIRECTORY_SEPARATOR . 'sctions' . DIRECTORY_SEPARATOR . $type->image);

        if (File::exists($image)) {
            File::delete($image);
        }
        $type->delete();

        toast(__('dash.delete-success'),'success');
        return redirect()->route('section.index');
    }
}
