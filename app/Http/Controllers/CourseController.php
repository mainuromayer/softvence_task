<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreCourseRequest;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::withCount('modules')->latest()->paginate(10);
        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest  $request)
    {
        $validated = $request->validated();
    
        DB::beginTransaction();
    
        try {
            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('public/course-thumbnails');
                $validated['thumbnail'] = str_replace('public/', '', $path);
            }
    
            // Create course
            $course = Course::create($validated);
    
            // Create modules and contents
            if ($request->has('modules')) {
                foreach ($request->modules as $moduleIndex => $moduleData) {
                    $module = $course->modules()->create([
                        'title' => $moduleData['title'],
                        'order' => $moduleIndex + 1 // Use array index for proper ordering
                    ]);
    
                    if (isset($moduleData['contents'])) {
                        foreach ($moduleData['contents'] as $contentIndex => $contentData) {
                            $content = [
                                'title' => $contentData['title'],
                                'type' => $contentData['type'],
                                'description' => $contentData['description'] ?? null,
                                'order' => $contentIndex + 1 // Use array index for proper ordering
                            ];
    
                            // Handle file uploads for media types
                            if (in_array($contentData['type'], ['image', 'file', 'video'])) {
                                $fileFieldName = "modules.{$moduleIndex}.contents.{$contentIndex}.content";
                                
                                if ($request->hasFile($fileFieldName)) {
                                    $file = $request->file($fileFieldName);
                                    
                                    if ($contentData['type'] === 'image') {
                                        $file->validate(['image' => 'mimes:jpeg,png,jpg,gif']);
                                    } elseif ($contentData['type'] === 'video') {
                                        $file->validate(['video' => 'mimes:mp4,mov,avi']);
                                    }
                                    
                                    $path = $file->store("public/module-contents");
                                    $content['content'] = str_replace('public/', '', $path);
                                } else {
                                    throw new \Exception("File missing for {$contentData['type']} content");
                                }
                            } else {
                                $content['content'] = $contentData['content'];
                            }
    
                            $module->contents()->create($content);
                        }
                    }
                }
            }
    
            DB::commit();
    
            return redirect()->route('courses.index', $course)
                ->with('success', 'Course created successfully!');
    
        } catch (Exception $e) {
            DB::rollBack();
            

            if (isset($course) && $course->thumbnail) {
                Storage::delete('public/'.$course->thumbnail);
            }
            
            return back()->withInput()
                ->with('error', 'Error creating course: '.$e->getMessage())
                ->withErrors(['exception' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course->load('modules.contents');
        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }
}
