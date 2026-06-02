<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdvertisementController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'link_url'    => 'nullable|url|max:255',
            'sort_order'  => 'nullable|integer|min:0|max:255',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'is_active'   => 'nullable|boolean',
            'image'       => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ], [
            'title.required'  => 'عنوان الإعلان مطلوب',
            'image.mimes'     => 'الصورة يجب أن تكون JPG/PNG/WEBP/GIF',
            'image.max'       => 'حجم الصورة يجب ألا يتجاوز 5MB',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            try {
                $file      = $request->file('image');
                $ext       = strtolower($file->getClientOriginalExtension());
                $filename  = 'ads/' . Str::random(36) . '.' . $ext;
                $disk      = config('filesystems.default', 'public');
                
                if (!Storage::disk($disk)->put($filename, $file->get())) {
                    throw new \Exception('فشل في حفظ الملف على القرص');
                }
                
                $imagePath = $filename;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'حدث خطأ أثناء رفع الصورة: ' . $e->getMessage());
            }
        }

        Advertisement::create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'image_path'  => $imagePath,
            'link_url'    => !empty($data['link_url']) ? $data['link_url'] : null,
            'sort_order'  => $data['sort_order'] ?? 0,
            'start_date'  => !empty($data['start_date']) ? $data['start_date'] : null,
            'end_date'    => !empty($data['end_date']) ? $data['end_date'] : null,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.ads.index')
            ->with('success', 'تم إضافة الإعلان بنجاح');
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'link_url'    => 'nullable|url|max:255',
            'sort_order'  => 'nullable|integer|min:0|max:255',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'is_active'   => 'nullable|boolean',
            'image'       => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            try {
                // حذف الصورة القديمة إن وُجدت
                if ($advertisement->image_path) {
                    $disk = config('filesystems.default', 'public');
                    Storage::disk($disk)->delete($advertisement->image_path);
                }

                $file      = $request->file('image');
                $ext       = strtolower($file->getClientOriginalExtension());
                $filename  = 'ads/' . Str::random(36) . '.' . $ext;
                $disk      = config('filesystems.default', 'public');
                
                if (!Storage::disk($disk)->put($filename, $file->get())) {
                    throw new \Exception('فشل في حفظ الملف الجديد');
                }
                
                $data['image_path'] = $filename;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'حدث خطأ أثناء تحديث الصورة: ' . $e->getMessage());
            }
        }

        unset($data['image']);
        $data['link_url']   = !empty($data['link_url']) ? $data['link_url'] : null;
        $data['start_date'] = !empty($data['start_date']) ? $data['start_date'] : null;
        $data['end_date']   = !empty($data['end_date']) ? $data['end_date'] : null;
        $data['is_active']  = $request->boolean('is_active');

        $advertisement->update($data);

        return redirect()->route('admin.ads.index')
            ->with('success', 'تم تحديث الإعلان بنجاح');
    }
}
