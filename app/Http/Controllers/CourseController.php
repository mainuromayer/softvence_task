<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Course;
use App\Models\Module;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreCourseRequest;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::withCount(['modules', 'contentsThroughModules'])
                    ->latest()
                    ->paginate(10);
                    
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
    public function store(StoreCourseRequest $request)
    {
        DB::beginTransaction();
        
        try {
            // Handle course thumbnail
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
            }
            
            // Create course
            $course = Course::create([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'thumbnail' => $thumbnailPath,
            ]);
            
            // Handle modules and contents
            if ($request->has('modules')) {
                foreach ($request->modules as $moduleData) {
                    $module = $course->modules()->create([
                        'title' => $moduleData['title'],
                        'order' => $course->modules()->count() + 1,
                    ]);
                    
                    if (isset($moduleData['contents'])) {
                        foreach ($moduleData['contents'] as $contentData) {
                            $content = [
                                'title' => $contentData['title'],
                                'type' => $contentData['type'],
                                'description' => $contentData['description'] ?? null,
                                'order' => $module->contents()->count() + 1,
                            ];
                            
                            // Handle content based on type
                            if (in_array($contentData['type'], ['image', 'video', 'file'])) {
                                if (isset($contentData['content_file'])) {
                                    $file = $contentData['content_file'];
                                    $path = $file->store("module-contents/{$module->id}", 'public');
                                    $content['content'] = $path;
                                }
                            } else {
                                $content['content'] = $contentData['content'] ?? null;
                            }
                            
                            $module->contents()->create($content);
                        }
                    }
                }
            }
            
            DB::commit();
            return redirect()->route('courses.index')->with('success', 'Course created successfully!');
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Course creation failed: ' . $e->getMessage());
            
            // Clean up uploaded files if any
            if (isset($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            
            return back()->withInput()->with('error', 'Failed to create course. Please try again.');
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course->load([
            'modules' => function($query) {
                $query->withCount('contents');
            },
            'modules.contents'
        ]);
    
        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCourseRequest $request, Course $course)
    {
        DB::beginTransaction();
        
        try {
            // Handle course thumbnail
            $thumbnailPath = $course->thumbnail;
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail if exists
                if ($course->thumbnail) {
                    Storage::disk('public')->delete($course->thumbnail);
                }
                $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
            }
            
            // Update course
            $course->update([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'thumbnail' => $thumbnailPath,
            ]);
            
            // Get existing module IDs to track what to keep
            $existingModuleIds = [];
            $existingContentIds = [];
            
            // Handle modules and contents
            if ($request->has('modules')) {
                foreach ($request->modules as $moduleData) {
                    // Update or create module
                    if (isset($moduleData['id'])) {
                        $module = $course->modules()->findOrFail($moduleData['id']);
                        $module->update(['title' => $moduleData['title']]);
                    } else {
                        $module = $course->modules()->create([
                            'title' => $moduleData['title'],
                            'order' => $course->modules()->count() + 1,
                        ]);
                    }
                    $existingModuleIds[] = $module->id;
                    
                    // Handle contents
                    if (isset($moduleData['contents'])) {
                        foreach ($moduleData['contents'] as $contentData) {
                            $content = [
                                'title' => $contentData['title'],
                                'type' => $contentData['type'],
                                'description' => $contentData['description'] ?? null,
                            ];
                            
                            // Handle content based on type
                            if (in_array($contentData['type'], ['image', 'video', 'file'])) {
                                if (isset($contentData['content_file'])) {
                                    // Delete old file if exists
                                    if (isset($contentData['existing_file'])) {
                                        Storage::disk('public')->delete($contentData['existing_file']);
                                    }
                                    // Store new file
                                    $file = $contentData['content_file'];
                                    $path = $file->store("module-contents/{$module->id}", 'public');
                                    $content['content'] = $path;
                                } elseif (isset($contentData['existing_file'])) {
                                    $content['content'] = $contentData['existing_file'];
                                }
                            } else {
                                $content['content'] = $contentData['content'] ?? null;
                            }
                            
                            // Update or create content
                            if (isset($contentData['id'])) {
                                $contentModel = $module->contents()->findOrFail($contentData['id']);
                                $contentModel->update($content);
                            } else {
                                $content['order'] = $module->contents()->count() + 1;
                                $module->contents()->create($content);
                            }
                            $existingContentIds[] = $contentModel->id ?? null;
                        }
                    }
                }
            }
            
            // Delete modules not present in the request
            $course->modules()->whereNotIn('id', $existingModuleIds)->delete();
            
            DB::commit();
            return redirect()->route('courses.index')->with('success', 'Course updated successfully!');
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Course update failed: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'Failed to update course. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        try {
            if ($course->thumbnail) {
                Storage::delete('public/'.$course->thumbnail);
            }
            $course->delete();
            return redirect()->route('courses.index')->with('success', 'Course deleted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Error deleting course: '.$e->getMessage());
        }
    }


    public function storeModule(Request $request, Course $course)
{
    $request->validate(['title' => 'required|string|max:255']);
    
    $course->modules()->create($request->only('title'));
    return back()->with('success', 'Module added successfully');
}

public function destroyModule(Course $course, Module $module)
{
    $module->delete();
    return back()->with('success', 'Module deleted successfully');
}

public function storeContent(Request $request, Module $module)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'type' => 'required|in:text,image,video,file,link',
        'content' => 'required_if:type,text,link',
        'content_file' => 'required_if:type,image,video,file|file'
    ]);

    $contentData = $request->only('title', 'type', 'description');
    
    if (in_array($request->type, ['image', 'file', 'video'])) {
        $path = $request->file('content_file')->store('public/module-contents');
        $contentData['content'] = str_replace('public/', '', $path);
    } else {
        $contentData['content'] = $request->content;
    }

    $module->contents()->create($contentData);
    return back()->with('success', 'Content added successfully');
}

public function destroyContent(Course $course, Content $content)
{
    if (in_array($content->type, ['image', 'file', 'video'])) {
        Storage::delete('public/'.$content->content);
    }
    $content->delete();
    return back()->with('success', 'Content deleted successfully');
}


}
