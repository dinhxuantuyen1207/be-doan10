<?php

namespace App\Http\Controllers;

use App\Models\News;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_title' => ['required'],
                'last_title' => ['required'],
                'description' => ['required'],
                'image' => ['required'],
                'news' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'Info Error !']);
            }
            return DB::transaction(function () use ($request) {
                $files = $request->file('image');
                if ($request->hasFile('image')) {
                    $name = time() . rand(1, 100) . "." . $files->getClientOriginalExtension();
                    $new = News::create([
                        'tieu_de_1' => $request->first_title,
                        'tieu_de_2' => $request->last_title,
                        'mo_ta' => $request->description,
                        'anh_tin_tuc' => $name,
                        'tin_tuc' => $request->news
                    ]);
                    if (isset($new)) {
                        $files->move('upload', $name);
                        return response()->json(['status' => true]);
                    } else {
                        return response()->json(['status' => false, 'message' => 'Info Error !']);
                    }
                } else {
                    return response()->json(['status' => false, 'message' => 'No file uploaded']);
                }
            }, 5);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function list()
    {
        $data = News::select('id', 'tieu_de_1', 'tieu_de_2', 'anh_tin_tuc')->get();
        if ($data) {
            return response()->json(['status' => true, 'data' => $data]);
        } else {
            return response()->json(['status' => false, 'message' => "Data Empty !"]);
        }
    }

    /**
     * Display the specified resource.
     */

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            if (!isset($id)) {
                return response()->json(['status' => false, 'message' => 'Info Error !']);
            }
            $data = News::find($id);
            if ($data) {
                return response()->json(['status' => true, 'data' => $data]);
            } else {
                return response()->json(['status' => false, 'message' => 'Not Found !']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function view($id)
    {
        try {
            if (!isset($id)) {
                return response()->json(['status' => false, 'message' => 'Info Error !']);
            }
            $data = News::find($id);
            if ($data) {
                return response()->json(['status' => true, 'data' => $data]);
            } else {
                return response()->json(['status' => false, 'message' => 'Not Found !']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function update(Request $request)
    {
        try {
            $filename = '';
            $validator = Validator::make($request->all(), [
                'id' => ['required'],
                'first_title' => ['required'],
                'last_title' => ['required'],
                'description' => ['required'],
                'news' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->fails()]);
            }
            $data = News::find($request->id);
            if (isset($data)) {
                $data->tieu_de_1 = $request->first_title;
                $data->tieu_de_2 = $request->last_title;
                $data->mo_ta = $request->description;
                $data->tin_tuc = $request->news;
                if ($request->hasFile('image')) {
                    $files = $request->file('image');
                    if ($request->hasFile('image')) {
                        $filename = 'upload/' . $data->anh_tin_tuc;
                        $name = time() . rand(1, 100) . "." . $files->getClientOriginalExtension();
                        $data->anh_tin_tuc = $name;
                        $files->move('upload', $name);
                    }
                }
                $data->save();
                if ($request->hasFile('image')) {
                    if (file_exists(public_path($filename))) {
                        unlink(public_path($filename));
                    }
                }
                return response()->json(['status' => true]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            if (isset($request->id)) {
                $news = News::find($request->id);
                if (isset($news)) {
                    $news->delete();
                    $data = News::select('id', 'tieu_de_1', 'tieu_de_2', 'anh_tin_tuc')->get();
                    if ($data) {
                        return response()->json(['status' => true, 'data' => $data]);
                    } else {
                        return response()->json(['status' => true]);
                    }
                }
                return response()->json(['status' => false, 'message' => 'Not Found !']);
            }
            return response()->json(['status' => false, 'message' => 'Not Found !']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
