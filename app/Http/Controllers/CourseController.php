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
                $thumbnail = $request->file('thumbnail');
                $thumbnailPath = $thumbnail->storeAs(
                    'course-thumbnails/' . date('Y/m'),
                    $thumbnail->getClientOriginalName(),
                    'public'
                );
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
                    ]);
                    
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
                                    $file = $contentData['content_file'];
                                    $path = $file->storeAs(
                                        'module-contents/' . $module->id . '/' . date('Y/m'),
                                        $file->getClientOriginalName(),
                                        'public'
                                    );
                                    
                                    $content['content'] = $path;
                                    $content['original_filename'] = $file->getClientOriginalName(); // Store original name
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
        // Validate the request
        $validated = $request->validated();
        
        // Handle course thumbnail
        $thumbnailPath = $course->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store(
                'course-thumbnails/' . date('Y/m'),
                'public'
            );
        }
        
        // Update course
        $course->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'thumbnail' => $thumbnailPath,
        ]);
        
        // Track existing modules and contents
        $existingModuleIds = [];
        $existingContentIds = [];
        
        foreach ($validated['modules'] as $moduleData) {
            // Validate module has contents
            if (empty($moduleData['contents'])) {
                throw new \Exception('Each module must have at least one content item.');
            }
            
            // Update or create module
            $module = isset($moduleData['id']) 
                ? $course->modules()->findOrFail($moduleData['id'])
                : $course->modules()->create(['title' => $moduleData['title']]);
            
            $module->update(['title' => $moduleData['title']]);
            $existingModuleIds[] = $module->id;
            
            // Process contents
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
                        $path = $contentData['content_file']->store(
                            'module-contents/' . $module->id . '/' . date('Y/m'),
                            'public'
                        );
                        $content['content'] = $path;
                        $content['original_filename'] = $contentData['content_file']->getClientOriginalName();
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
                    $existingContentIds[] = $contentModel->id;
                } else {
                    $newContent = $module->contents()->create($content);
                    $existingContentIds[] = $newContent->id;
                }
            }
            
            // Delete contents not present in the request
            $module->contents()->whereNotIn('id', $existingContentIds)->delete();
        }
        
        // Delete modules not present in the request
        $course->modules()->whereNotIn('id', $existingModuleIds)->delete();
        
        DB::commit();
        return redirect()->route('courses.index')->with('success', 'Course updated successfully!');
        
    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Course update failed: ' . $e->getMessage());
        return back()->withInput()->with('error', $e->getMessage());
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


}
