<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'due_date',
        'points',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    // Helper methods
    public function isOverdue()
    {
        return $this->due_date < now();
    }

    public function isUpcoming()
    {
        return $this->due_date > now();
    }

    public function getSubmissionCount()
    {
        return $this->submissions()->count();
    }

    public function getGradedSubmissionCount()
    {
        return $this->submissions()->whereNotNull('grade')->count();
    }

    public function getAverageScore()
    {
        return $this->submissions()->whereNotNull('grade')->avg('grade');
    }

    // Scopes
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('due_date', '>', now());
    }

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }
}
