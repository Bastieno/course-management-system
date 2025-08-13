<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CourseMaterial extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'type',
        'file_path',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the course that owns the material
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the file URL for web access
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return Storage::disk('public')->url($this->file_path);
        }
        return null;
    }

    /**
     * Get the file size in human readable format
     */
    public function getFileSizeAttribute()
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            $bytes = Storage::disk('public')->size($this->file_path);
            return $this->formatBytes($bytes);
        }
        return null;
    }

    /**
     * Check if file exists
     */
    public function fileExists()
    {
        return $this->file_path && Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Delete the associated file
     */
    public function deleteFile()
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->delete($this->file_path);
        }
        return true;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Get file type icon class
     */
    public function getFileIconAttribute()
    {
        switch ($this->type) {
            case 'pdf':
                return 'fas fa-file-pdf text-red-500';
            case 'video':
                return 'fas fa-file-video text-blue-500';
            case 'document':
                return 'fas fa-file-word text-blue-600';
            case 'link':
                return 'fas fa-link text-green-500';
            default:
                return 'fas fa-file text-gray-500';
        }
    }
}
